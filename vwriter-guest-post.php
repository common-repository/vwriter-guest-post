<?php

/*
  Plugin Name: vWriter Guest Post
  Plugin URI: http://www.vwriter.com/guestpostplugin.php
  Description:  Automate your guest posting with this powerful plugin from vWriter.com. Visitors to your blog can request a 100% unique, professionally-written version of your blog post they can then publish as a Guest Post on their blog with link(s) back to your site. Essential for any business owner wanting to build online traffic and visibility for the long-term, this must-have plugin gives you high quality, targeted traffic and SEO benefits, develops new business opportunities, and builds powerful relationships with other businesses and bloggers. All Guest Post requests are subject to your approval - you maintain full control over which sites are going to be posting your content and linking back to you. 
  Version: 1.0
  Author: takanomi
  Author URI: http://vwriter.com
  Text Domain: vWriter-Guest-Post-Plugin
 */


/*
  Copyright (c) 2013  vWriter.com  (email : supportteam@vwriter.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

define( 'VWRITER_GUEST_POST_OPTIONS_NAME', 'vwriter_guest_post' );
define( 'VWRITER_GUEST_POST_VERSION', '1.0' );
define( 'VWRITER_GUEST_POST_MIN_WP_VERSION', '3.0' );

// Option default values
define( 'VWRITER_GUEST_POST_TEXT_DEFAULT', 'Grab a unique, professionally-written version of this post for your own blog.' );
define( 'VWRITER_GUEST_POST_ANCHOR_TEXT_DEFAULT', 'More info...' );
define( 'VWRITER_GUEST_POST_CLIENT_ID_DEFAULT', 0 );
define( 'VWRITER_GUEST_POST_ADD_TO_ALL_DEFAULT', 0 );

if( !class_exists( "VwriterGuestPost" ) ){

    class VwriterGuestPost {

        var $options;
        var $options_name = "vwriter_guest_post";
        var $plugin_dir = "";
        var $css_dir = "";
        var $js_dir = "";
        var $images_dir = "";
        var $css_url = "";
        var $js_url = "";
        var $images_url = "";
        var $admin_css = array( "post-footer-admin.css" );
        var $admin_js = array( "post-footer.js" );
        var $frontend_css = array( );
        var $frontend_js = array( );

        function VwriterGuestPost(){
            // Full path and plugin basename of the main plugin file
            $this->plugin_file = dirname( dirname( __FILE__ ) ) . '/vwriter-guest-post.php';
            $this->plugin_basename = plugin_basename( $this->plugin_file );

            // Plugin directory names
            $this->plugin_path = dirname( __FILE__ );
            $this->css_dir = $this->plugin_path . '/css/';
            $this->js_dir = $this->plugin_path . '/js/';
            $this->images_dir = $this->plugin_path . '/images/';

            // Plugin URLs
            $this->css_url = plugins_url( 'css', __FILE__ );
            $this->js_url = plugins_url( 'js', __FILE__ );
            $this->images_url = plugins_url( 'images', __FILE__ );

            // Load localizations if available
            load_plugin_textdomain( 'vwriterguestpost', false, 'vwriter-guest-post/localization' );

            // Make sure our options are setup in the db
            $this->setup_options();
            $this->options = get_option( VWRITER_GUEST_POST_OPTIONS_NAME );
        }

        /**
         * init
         * Actions that need to occur each time the plugin is started should go here
         */
        function init(){

            // Instantiate the VwriterGuestPostFrontend or VwriterGuestPostAdmin Class
            // Deactivate and die if files can not be included
            if( is_admin() ){
                // Load the admin page code
                if( @include ( dirname( __FILE__ ) . '/inc/admin.php' ) ){
                    $VwriterGuestPostAdmin = new VwriterGuestPostAdmin();
                }
                else{
                    VwriterGuestPost::deactivate_and_die( dirname( __FILE__ ) . '/inc/admin.php' );
                }
            }
            else{
                // Load the frontend code
                if( @include ( dirname( __FILE__ ) . '/inc/frontend.php' ) ){
                    $VwriterGuestPostFrontend = new VwriterGuestPostFrontend();
                }
                else{
                    VwriterGuestPost::deactivate_and_die( dirname( __FILE__ ) . '/inc/frontend.php' );
                }
            }
        }

        /**
         * Callback for the register_activation_hook
         * Actions that need to occur when the plugin is activated should go here
         */
        function plugin_activation(){
            
        }

        /*         * *
         * Callback for register_deactivation_hook
         * Actions that need to occur when the plugin is deactivated should go here
         */

        function plugin_deactivation(){
            
        }

        /*         * *
         * Callback for register_uninstall_hook
         * Clean up the db when the plugin is uninstalled
         */

        function plugin_uninstall(){
            delete_option( VWRITER_GUEST_POST_OPTIONS_NAME );
        }

        /**
         * Return the default option values
         */
        function default_options(){
            $defaults = array(
                'post_text' => VWRITER_GUEST_POST_TEXT_DEFAULT,
                'post_anchor_text' => VWRITER_GUEST_POST_ANCHOR_TEXT_DEFAULT,
                'vwriter_client_id' => VWRITER_GUEST_POST_CLIENT_ID_DEFAULT,
                'add_to_all' => VWRITER_GUEST_POST_ADD_TO_ALL_DEFAULT // 0 = don't add to all posts, 1 = add to all posts
            );
            return $defaults;
        }

        /**
         * Setup shared functionality for Admin and Front End
         */
        // If any of the necessary files are not found we come here to deactivate the plugin and show an error message.
        function deactivate_and_die(){
            load_plugin_textdomain( 'vwriter-guest-post', false, 'vwriter-guest-post/localization' );
            $message = sprintf( __( "vWriter Guest Post Plugin has been automatically deactivated because the file <strong>%s</strong> is missing. Please reinstall the plugin and reactivate." ), $file );
            if( !function_exists( 'deactivate_plugins' ) )
                include ( ABSPATH . 'wp-admin/includes/plugin.php' );
            deactivate_plugins( __FILE__ );
            wp_die( $message );
        }

        // Set default options if they don't already exisit
        function setup_options(){
            if( !get_option( VWRITER_GUEST_POST_OPTIONS_NAME ) ){
                $this->options = $this->default_options();
                add_option( VWRITER_GUEST_POST_OPTIONS_NAME, $this->options );
            }
        }

        /**
         * Get specific option from the options array
         */
        function get_option( $option ){
            if( isset( $this->options[$option] ) ){
                return $this->options[$option];
            }
            else{
                return false;
            }
        }

        /**
         * Set specific option from the options array
         */
        function set_option( $option, $value ){
            $this->options[$option] = $value;
            update_option( VWRITER_GUEST_POST_OPTIONS_NAME, $this->options );
        }

        /**
         * Get the full URL to the plugin
         */
        function plugin_url(){
            $plugin_url = plugins_url( plugin_basename( dirname( __FILE__ ) ) );
            return $plugin_url;
        }

    }

    // End VwriterGuestPostAdmin class
} // End if VwriterGuestPostAdmin

/**
 * Setup initial hooks and actions for VWRITER_GUEST_POST plugin
 * 
 */
register_deactivation_hook( __FILE__, array( 'VwriterGuestPost', 'plugin_deactivate' ) );
register_uninstall_hook( __FILE__, array( 'VwriterGuestPost', 'plugin_uninstall' ) );

add_action( 'init', array( 'VwriterGuestPost', 'init' ) );


/**
 * Adds a checkbox on the Post and Page edit screens (Admin).
 */

$VwriterGuestPost = new VwriterGuestPost();
// if user has checked display all on setting page, then there is no need to display check option for each indidual post.
if( $VwriterGuestPost->get_option( 'add_to_all' ) != 1 ){
    add_action( 'add_meta_boxes', 'vwriter_guest_post_add_custom_checkbox' );
}

function vwriter_guest_post_add_custom_checkbox(){

    $screens = array( 'post', 'page' );

    foreach( $screens as $screen ){

        add_meta_box(
                'vwriter_guest_post_sectionid', __( 'vWriter Guest Post Plugin by vWriter.com', 'vwriter_guest_post_textdomain' ), 'vwriter_guest_post_inner_custom_checkbox', $screen
        );
    }
}



/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function vwriter_guest_post_inner_custom_checkbox( $post ){

    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'vwriter_guest_post_inner_custom_checkbox', 'vwriter_guest_post_inner_custom_checkbox_nonce' );

    /*
     * Use get_post_meta() to retrieve an existing value
     * from the database and use the value for the form.
     */
    $value = get_post_meta( $post->ID, '_show_vwriter_rewrite_meta_value_key', true );

    
    if( isset( $value ) && $value == 1 )
        $checkboxPart = 'checked';
    else
        $checkboxPart = '';
    echo '<input id="vwriter_guest_post_rewrite_link" name="vwriter_guest_post_rewrite_link" class="checkbox" type="checkbox" value="1" ' . $checkboxPart . ' />';
    echo ' <label for="vwriter_guest_post_rewrite_link">';
    _e( "Display guest post invitation", 'vwriter_guest_post_textdomain' );
    echo '</label> ';
    echo '<a href="/wp-admin/options-general.php?page=vwriter-guest-post/inc/admin.php">How this works  </a>';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function vwriter_guest_post_save_postdata( $post_id ){

    /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */

    // Check if our nonce is set.
    if( !isset( $_POST['vwriter_guest_post_inner_custom_checkbox_nonce'] ) )
        return $post_id;

    $nonce = $_POST['vwriter_guest_post_inner_custom_checkbox_nonce'];

    // Verify that the nonce is valid.
    if( !wp_verify_nonce( $nonce, 'vwriter_guest_post_inner_custom_checkbox' ) )
        return $post_id;

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    // Check the user's permissions.
    if( 'page' == $_POST['post_type'] ){

        if( !current_user_can( 'edit_page', $post_id ) )
            return $post_id;
    } else{

        if( !current_user_can( 'edit_post', $post_id ) )
            return $post_id;
    }

    /* OK, its safe for us to save the data now. */
    if( isset( $_POST['vwriter_guest_post_rewrite_link'] ) ){
        // Sanitize user input.
        $mydata = sanitize_text_field( $_POST['vwriter_guest_post_rewrite_link'] );
    }
    else{
        $mydata = "0";
    }
    // Update the meta field in the database.
    update_post_meta( $post_id, '_show_vwriter_rewrite_meta_value_key', $mydata );
}

add_action( 'save_post', 'vwriter_guest_post_save_postdata' );