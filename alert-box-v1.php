<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              elfiiik.cz
 * @since             1.0
 * @package           Ab
 *
 * @wordpress-plugin
 * Plugin Name:       Alert box for comments
 * Plugin URI:        
 
 * Description:       This plugin adds button, that opens small box. It shows 3 latest responds on your comments.
 * Version:           1.0
 * Author:            Jakub Puna
 * Author URI:        elfiiik.cz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ab
 * Domain Path:       /languages
 */


add_action('admin_menu', 'abfc_create_menu');

function abfc_create_menu() {
	add_menu_page('Alert box settings', 'Alert Box settings', 'administrator', __FILE__, 'abfc_settings_page' , plugins_url('assets/bell-icon1.jpg', __FILE__) );
	add_action( 'admin_init', 'abfc_register_settings' );
}


function abfc_register_settings() {
	register_setting( 'abfc_settings_group', 'abfc_top' );
	register_setting( 'abfc_settings_group', 'abfc_left' );
	register_setting( 'abfc_settings_group', 'abfc_top2' );
	register_setting( 'abfc_settings_group', 'abfc_left2' );
}

function abfc_settings_page() {
?>
	<div class="wrap">
	<h1>Alert box position</h1>

	<form method="post" action="options.php">
	    <?php settings_fields( 'abfc_settings_group' ); ?>
	    <?php do_settings_sections( 'abfc_settings_group' ); ?>
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">Top</th>
	        <td><input type="text" name="ab_top" value="<?php echo esc_attr( get_option('abfc_top') ); ?>" /></td>
	        </tr>
	         
	        <tr valign="top">
	        <th scope="row">Left</th>
	        <td><input type="text" name="ab_left" value="<?php echo esc_attr( get_option('abfc_left') ); ?>" /></td>
	        </tr>

	        <tr valign="top">
	        <th scope="row">Top 2</th>
	        <td><input type="text" name="ab_top2" value="<?php echo esc_attr( get_option('abfc_top2') ); ?>" /></td>
	        </tr>
	         
	        <tr valign="top">
	        <th scope="row">Left 2</th>
	        <td><input type="text" name="ab_left2" value="<?php echo esc_attr( get_option('abfc_left2') ); ?>" /></td>
	        </tr>
	        
	    </table>
	    
	    <?php submit_button(); ?>

	</form>
	</div>
<?php } 



function abfc_install_table () {
   global $wpdb;

   $table_name = $wpdb->prefix . "answers";
   $charset_collate = $wpdb->get_charset_collate();

   $sql = "CREATE TABLE $table_name (
	  	id mediumint(9) NOT NULL AUTO_INCREMENT,
		id_comment int NOT NULL,
		id_post int NOT NULL,
		email text NOT NULL,
		username text NOT NULL,
		to_author text NOT NULL,
		comment_date text NOT NULL,
		PRIMARY KEY  (id)
		) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

register_activation_hook( __FILE__, 'abfc_install_table' );






add_action( 'comment_post', 'abfc_message_post', 10, 2 );
function abfc_message_post( $comment_ID, $comment_approved ) {
		global $wpdb;
		$datum = date("j.n.Y H:i");
		$table_name = $wpdb->prefix . "answers";
		$table_name_comments = $wpdb->prefix . "comments";

		if (is_user_logged_in()) {
			$current_user = wp_get_current_user();
			$current_user_email =  $current_user->user_email;
			$current_user_name = $current_user->user_login;
		} else {
			$current_user_email = $wpdb->get_var( "SELECT comment_author_email FROM $table_name_comments WHERE comment_ID = '$comment_ID'");
			$current_user_name = $wpdb->get_var("SELECT comment_author FROM $table_name_comments WHERE comment_ID = '$comment_ID'");
		}

		

		$tmp = $wpdb->get_var( "SELECT comment_author FROM $table_name_comments WHERE comment_ID = '$comment_ID'");
		$tmp_parent = $wpdb->get_var( "SELECT comment_parent FROM $table_name_comments WHERE comment_ID = '$comment_ID'");
		$tmp_post_id = $wpdb->get_var( "SELECT comment_post_ID FROM $table_name_comments WHERE comment_ID = '$comment_ID'");


		$sel = 	$wpdb->get_results("SELECT * FROM  $table_name_comments");
		if ($tmp_parent!=0) {
			$ofi_author = $wpdb->get_var("SELECT comment_author_email FROM $table_name_comments WHERE comment_ID = '$tmp_parent'"); // Vyhledá autora, kterému se ukáže poté, že mu někdo odpověděl
			if ($current_user_email != $ofi_author) {
				foreach ($sel as $key ) {
					if ($tmp_parent == $key->comment_ID) {
						$wpdb->insert($table_name, array('id_comment' => $comment_ID, 'id_post' => $tmp_post_id, 'email' => $current_user_email, 'username' => $current_user_name, 'to_author' => $ofi_author ,'comment_date' => $datum ));
						$pocet = $wpdb->get_var("SELECT count(email) FROM $table_name WHERE to_author='$ofi_author'");

						//pokud jsou na jeden email v databázi více než 3 výsledky, staré se smažou
						if ($pocet > 3) {
							$limit = $pocet - 3;
							for ($i=0; $i < $limit ; $i++) { 
								$smazat= $wpdb->get_var("SELECT min(id) FROM $table_name WHERE to_author='$ofi_author'");
								$wpdb->delete($table_name, array('id' => $smazat ));
							}
						}
					}
				}
			}
		}

}



function abfc_button() {
	$response = "";
	$number = 0;
	$table_name = $wpdb->prefix . "answers";

	global $wpdb;

	if (is_user_logged_in()) {
		$current_user = wp_get_current_user();
		$current_user_email =  $current_user->user_email;
		$table_name = $wpdb->prefix . "answers";
		$table_name_posts = $wpdb->prefix . "posts";


		$search = $wpdb->get_results( "SELECT * FROM $table_name WHERE to_author = '$current_user_email' ORDER BY id desc");
		

		foreach ($search as $key => $value){
			$post = $wpdb->get_var( "SELECT guid FROM $table_name_posts WHERE ID = '$value->id_post'");
			$response .= "<div class=\"ab_inside_response\">";
			$response .= "<form action=\"".$post."/#comment-".$value->id_comment."\" method=\"post\">";
			$response .= "<input type=\"hidden\" name=\"respond_id\" value=".$value->id.">";
			$response .= "<span class=\"ab_name\">".$value->username . "</span> <span class=\"ab_text\"> has responded on your </span> <input class=\"ab_comment\" type=\"submit\" value=\"comment\">";
			$response .= "</form>";
			$response .= "<p class=\"ab_date\">" . $value->comment_date . "</p>";
			$response .= "</div>";
			$number++;
		}
	} else {
		$road = get_site_url() . "/wp-login.php";
		$response .= "<p class=\"ab_login\"> Please login <a href=\"$road\">here</a></p>";
	}
  
		

	$ab_div .= "<div id=\"ab\" onclick=\"openAb()\" style=\"top: ".get_option('abfc_top')."; left: ".get_option('abfc_left').";\">";
	if ($number > 0) {
		$ab_div .= "<div id=\"ab_number\">" .$number. "</div>"; 
	}
	$ab_div .= '<div id="ab2">';
	$ab_div .= "<div id=\"ab3\">";

	$ab_div .='</div>';
	$ab_div .='</div>';
	$ab_div .='</div>';

	$ab_div .="<div id=\"ab_inside\" style=\"top: ".get_option('abfc_top2')."; left: ".get_option('abfc_left2').";\">";
	$ab_div .= $response;
	$ab_div .='</div>';

	echo $ab_div;
}

function abfc_delete() {
	global $wpdb;
	if (isset($_POST["respond_id"])) {
		$respond_id = $_POST["respond_id"];
		if (is_numeric($respond_id)) {
			$table_name = $wpdb->prefix . "answers";
			$wpdb->delete($table_name, array('id' => $respond_id ));
		}	
	}
}

function abfc_style() {
	wp_register_style('abfc_style', plugins_url('style.css',__FILE__ ));
	wp_register_script('abfc_script', plugins_url('scripts.js',__FILE__ ));
    wp_enqueue_style('abfc_style');
    wp_enqueue_script('abfc_script');
}

add_action('wp_footer','abfc_style');
add_action('wp_footer', 'abfc_delete');
add_action('wp_footer', 'abfc_button');


