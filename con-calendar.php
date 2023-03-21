<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://sudhanshu.wisdmlabs.net/
 * @since             1.0.0
 * @package           Con_Calendar
 *
 * @wordpress-plugin
 * Plugin Name:       Content Calendar
 * Plugin URI:        https://https://sudhanshu.wisdmlabs.net/
 * Description:       First Plugin Assignment
 * Version:           1.0.0
 * Author:            Sudhanshu Rai
 * Author URI:        https://https://sudhanshu.wisdmlabs.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       con-calendar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CON_CALENDAR_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-con-calendar-activator.php
 */
function activate_con_calendar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-con-calendar-activator.php';
	Con_Calendar_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-con-calendar-deactivator.php
 */
function deactivate_con_calendar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-con-calendar-deactivator.php';
	Con_Calendar_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_con_calendar' );
register_deactivation_hook( __FILE__, 'deactivate_con_calendar' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-con-calendar.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_con_calendar() {

	$plugin = new Con_Calendar();
	$plugin->run();

}
run_con_calendar();


// Add menu item
function content_calendar_plugin_menu() {
    add_menu_page( 'Content Calendar', 'Content Calendar', 'manage_options', 'content-calendar', 'content_calendar_plugin_page' );
}
add_action( 'admin_menu', 'content_calendar_plugin_menu' );


// Create database table
function content_calendar_plugin_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'content_calendar';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        day date NOT NULL,
        occasion varchar(255) NOT NULL,
        post_title varchar(255) NOT NULL,
        author int(11) NOT NULL,
        reviewer int(11) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'content_calendar_plugin_create_table' );


// Create page content
function content_calendar_plugin_page() {
    ?>
    <div class="wrap">
        <h1>Content Calendar</h1>
        <form method="post" >
            <?php
            // Add nonce field for security
            wp_nonce_field( 'content_calendar_plugin_save', 'content_calendar_plugin_nonce' );
            ?>
            <table class="form-table">
                <tr>
                    <th><label for="day">Day</label></th>
                    <td><input type="date" name="day" id="day" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="occasion">Occasion</label></th>
                    <td><input type="text" name="occasion" id="occasion" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="post_title">Post Title</label></th>
                    <td><input type="text" name="post_title" id="post_title" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="author">Author</label></th>
                    <td>
                        <select name="author" id="author">
                            <?php
                            // Get all WordPress users
                            $users = get_users();
                            foreach ( $users as $user ) {
                                echo '<option value="' . $user->ID . '">' . $user->user_nicename . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="reviewer">Reviewer</label></th>
                    <td>
                        <select name="reviewer" id="reviewer">
                            <?php
                            // Get all WordPress users other than the author
                            foreach ( $users as $user ) {
                                if ( $user->ID != get_current_user_id() ) {
                                    echo '<option value="' . $user->ID . '">' . $user->user_nicename . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button( 'Add Content', 'primary', 'add_content' ); ?>
        </form>
    </div>
    <?php

    

    // Verify nonce
    if ( !isset( $_POST['content_calendar_plugin_nonce'] ) || !wp_verify_nonce( $_POST['content_calendar_plugin_nonce'], 'content_calendar_plugin_save' ) || ! isset($_POST['add_content']) ) {
        return;
    }

    // Save form data to database
    global $wpdb;
    $table_name = $wpdb->prefix . 'content_calendar';
    $data = array(
        'day' => sanitize_text_field( $_POST['day'] ),
        'occasion' => sanitize_text_field( $_POST['occasion'] ),
        'post_title' => sanitize_text_field( $_POST['post_title'] ),
        'author' => sanitize_text_field( $_POST['author'] ),
        'reviewer' => sanitize_text_field( $_POST['reviewer'] )
	);
	$wpdb->insert( $table_name, $data );

    ?>

    <h3>Content Calendar</h3>
    <table id="content-calendar-table">
        <tr>
            <th>Day</th>
            <th>Occasion</th>
            <th>Post Title</th>
            <th>Author</th>
            <th>Reviewer</th>
        </tr>

    <?php

	// Display the table
    $results = $wpdb->get_results("SELECT * FROM $table_name");


    if($results) {
        ?>
            <?php foreach ($results as $row) { ?>
                <tr>
                    <td><?php echo $row->day; ?></td>
                    <td><?php echo $row->occasion; ?></td>
                    <td><?php echo $row->post_title; ?></td>
                    <td><?php echo get_user_by('ID', $row->author)->display_name; ?></td>
                    <td><?php echo get_user_by('ID', $row->reviewer)->display_name; ?></td>
                </tr>
            <?php } ?>
        </table>
        <?php 
    }
}