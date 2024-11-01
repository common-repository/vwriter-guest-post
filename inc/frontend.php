<?php

/* * *************************
 *
 */

class VwriterGuestPostFrontend extends VwriterGuestPost {

    /**
     * Setup backend functionality in WordPress
     */
    function VwriterGuestPostFrontend(){
        VwriterGuestPost::VwriterGuestPost();

        // Add the custom css and Javascript files for the admin page that are 
        // listed in $this->frontend_css and $this->frontend_js vars.
        add_action( 'wp_enqueue_scripts', array( &$this, 'load_frontend_custom' ) );

        // Setup the filter to add the footer text to the post content
        add_filter( "the_content", array( &$this, "vwriter_guest_post_append" ) );

        // Setup the [vwriter_guest_post] shortcode
        add_shortcode( "vwriter_guest_post", array( &$this, "vwriter_guest_post_shortcode" ) );
    }

    /**
     * Add the post_text to the end of the post if it is displaying on a single page
     * and the add_to_all option is checked
     */
    function vwriter_guest_post_append( $content ){
        if( is_single() && $this->get_option( 'post_text' ) ){
            $href = "http://www.vwriter.com/guestpost.php?id=" . $this->get_option( 'vwriter_client_id' ) . "&post=" . get_permalink() . "&title=" . urlencode( get_the_title() ) . "";
            $anchorTag = "<a href=" . $href . "> " . $this->get_option( 'post_anchor_text' ) . "</a>";
            
            $postMetaDatas = get_post_meta( get_the_ID() );
            
            $showContentRewriteLink = 0;

            foreach( $postMetaDatas as $key => $value ){
                if( $key == '_show_vwriter_rewrite_meta_value_key' ){
                    $showContentRewriteLink = $value[0];
                }
            }
            if( ($this->get_option( 'add_to_all' ) == 1) || ($showContentRewriteLink == 1) ){
                $content = $content . "<p>" . $this->get_option( 'post_text' ) . " " . $anchorTag . "</p>";
            }
        }
        return $content;
    }

    /**
     * Add the post_text into a post if the shortcode [vwriter_guest_post] is present and
     * the add_to_all checkbox is not checked
     */
    function vwriter_guest_post_shortcode(){
        if( is_single() && !$this->get_option( 'add_to_all' ) ){
            return $this->get_option( "post_text" );
        }
    }

    /**
     * Add Javascript and stylesheet files
     */
    function load_frontend_custom(){
        if( !empty( $this->frontend_css ) ){
            $this->load_admin_css();
        }

        if( !empty( $this->frontend_js ) ){
            $this->load_admin_scripts();
        }
    }

    /**
     * Load CSS files listed in the $this->admin_css var
     */
    function load_frontend_css(){
        foreach( $this->frontend_css as $css ){
            if( file_exists( $this->css_dir . $css ) ){
                wp_register_style( $css, $this->css_url . "/" . $css );
                wp_enqueue_style( $css );
            }
        }
    }

    /**
     * Load scripts listed in the $this->admin_js var
     */
    function load_frontend_scripts(){
        foreach( $this->frontend_js as $js ){
            if( file_exists( $this->js_dir . $js ) ){
                wp_deregister_script( $js );
                wp_register_script( $js, $this->js_url . "/" . $js );
                wp_enqueue_script( $js );
            }
        }
    }

}

// End VwriterGuestPostFrontend class