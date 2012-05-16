<?php
/*
Plugin Name: Path Access
Plugin URI: http://wordpress.org/extend/plugins/path-access/
Description: This plugin gives you the ability to call the 404.php template file or your own custom template file for specific paths where a user is not logged in.
Version: 1.0.1
Tags: members, access, path
Author URI: http://kevindees.cc

/--------------------------------------------------------------------\
|                                                                    |
| License: GPL                                                       |
|                                                                    |
| Path Access.           |
| Copyright (C) 2011, Kevin Dees,                                    |
| http://kevindees.cc                                               |
| All rights reserved.                                               |
|                                                                    |
| This program is free software; you can redistribute it and/or      |
| modify it under the terms of the GNU General Public License        |
| as published by the Free Software Foundation; either version 2     |
| of the License, or (at your option) any later version.             |
|                                                                    |
| This program is distributed in the hope that it will be useful,    |
| but WITHOUT ANY WARRANTY; without even the implied warranty of     |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      |
| GNU General Public License for more details.                       |
|                                                                    |
| You should have received a copy of the GNU General Public License  |
| along with this program; if not, write to the                      |
| Free Software Foundation, Inc.                                     |
| 51 Franklin Street, Fifth Floor                                    |
| Boston, MA  02110-1301, USA                                        |   
|                                                                    |
\--------------------------------------------------------------------/
*/

// protect yourself
if ( !function_exists( 'add_action') ) {
	echo "Hi there! Nice try. Come again.";
	exit;
}

class pathAccess {
	// when object is created
	function __construct() {
		add_action('admin_menu', array($this, 'menu')); // add item to menu
	}

	// make menu
	function menu() {
		add_submenu_page('options-general.php', 'Path Access', 'Path Access', 'manage_options', __FILE__,array($this, 'settings_page'), '', '');
	}

	// create page for output and input
	function settings_page() {
		?>
	<div class="icon32" id="icon-options-general"><br></div>
	<div id="path-access-wp-page" class="wrap">

		<h2>Path Access</h2>

		<?php
		// selected
		$selected = array();

		// updating
		if($_POST['submit'] && check_admin_referer('path_access_action','path_access_ref') ) :
			update_option('path_access', trim(addslashes($_POST['path_access'])));
			update_option( 'path_access_roles', $_POST['path_access_roles'] );
			$editable_roles = get_editable_roles();

			$checked = array();
			if($selected = $_POST['path_access_roles']) :
				foreach( $selected as $picked => $name ) :
					$checked = array_merge( array( "$name" => true ), $checked);
				endforeach;
			endif;

			foreach ( $editable_roles as $role => $details ) :
				$role = esc_attr($role);
				$thisRole = get_role($role);
				if ( $checked[$role] ) // preselect specified role
					$thisRole->add_cap('path_access');
				else
					$thisRole->remove_cap('path_access');
			endforeach;

			echo '<div id="message" class="updated below-h2"><p>Path Access is updated.</p></div>';
		endif; //end updating
		?>

		<form method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>">
			<?php wp_nonce_field('path_access_action','path_access_ref'); ?>

			<table class="form-table">
				<tbody>
				<tr>
					<td>
						<h3 style="font-weight: bold;">Restrict Paths</h3>
						<p>
							Separate paths with a return and include beginning and ending slashes "/". Add * to the end of a path to restrict all child pages. You must have a <a href="http://codex.wordpress.org/Creating_an_Error_404_Page" target="_blank">404.php template</a> file or you can create your own template file and name it access.php. Some paths are blocked such as /wp-admin and /wp-login.php for your safety. <a href="http://en.wikipedia.org/wiki/URI_scheme" target="_blank">What is a path?</a>
						</p>
						<textarea style="width: 98%" id="path_access" name="path_access" rows="15" cols="70"><?php echo get_option('path_access'); ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						<h3 style="font-weight: bold;">Allow Access to Selected Roles</h3>
						<p>The checked roles will have access to the above paths. Every other role and logged out user will not.</p>
						<?php $this->wp_select_roles( get_option('path_access_roles') ); ?>
						<p class="submit">
							<input type="submit" name="submit" class="button-primary" value="Save Changes" />
						</p>
					</td>
				</tr>
				</tbody>
			</table>
		</form>

	</div>

	<?php }

	static function path_access() {
		// setup paths and current url
		$paths = get_option('path_access');
		$paths = explode(PHP_EOL, $paths);
		$url = parse_url($_SERVER['REQUEST_URI']);

		// check for logged in user and login page
		$loggedIn = is_user_logged_in();
		$isAdminPath = preg_match('/^\/wp-login.php|\/wp-admin\/(.*)/', $url['path']);

		// check files
		$file404 = is_file( TEMPLATEPATH . '/' . '404.php');
		$fileAccess = is_file( TEMPLATEPATH . '/' . 'access.php');

		$isAccessRole = current_user_can( 'path_access' );

		// path access logic
		foreach($paths as $key => $path ) :
			$pattern = '/^' . preg_replace(array('/\//','/\*/'), array('\/', '(.*)'), trim($path)) . '$/';
			$match = preg_match($pattern, $url['path']);

			if($match  && !$isAdminPath && !$loggedIn || !$isAccessRole && !$isAdminPath && $match) :
				if($fileAccess) :
					header('HTTP/1.1 403 Forbidden');
					load_template( TEMPLATEPATH . '/' . 'access.php');
					die();
				elseif($file404) :
					header('HTTP/1.1 404 Not Found');
					load_template( TEMPLATEPATH . '/' . '404.php');
					die();
				else :
					echo '<h1>Please add a 404.php file to your theme.</h1><p><a href="http://codex.wordpress.org/Creating_an_Error_404_Page">How to make a 404.php for your theme.</a></p>';
					die();
				endif;
			endif;
		endforeach;
	}

	// list roles as checkbox
	function wp_select_roles( $selected ) {
		$output = '';
		$checked = array();

		$editable_roles = get_editable_roles();

		if($selected) :
			foreach( $selected as $picked => $name ) :
				$checked = array_merge( array( "$name" => true ), $checked);
			endforeach;
		endif;

		foreach ( $editable_roles as $role => $details ) :
			$name = translate_user_role($details['name'] );
			$role = esc_attr($role);
			if ( $checked[$role] ) // preselect specified role
				$output .= "<p><label><input type=\"checkbox\" checked=\"checked\" value=\"$role\" name=\"path_access_roles[]\"'> $name</label></p>";
			else
				$output .= "<p><label><input type=\"checkbox\" value=\"$role\" name=\"path_access_roles[]\"'> $name</label></p>";
		endforeach;
		echo $output;
	}
}

add_action('init','pathAccess::path_access');

new pathAccess();