<?php
/** 
 * Settings for the plugin
 * 
 * Setup the settings for the plugin
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Protect Your REST
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// make sure the class does not already exist
if( ! class_exists( 'PYR_Settings' ) ) {

    /** 
     * Class PYR_Settings
     * 
     * Class for building out the settings for the plugin
     * 
     * @since 7.4
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Protect Your REST
     * 
     * @property $_opts The options for internal use only
     * 
    */
    class PYR_Settings {

        // hold the options
        private $_opts;

        // fire us up
        public function __construct( ) {

            // populate the internal options
            $this -> _opts = get_option( 'kppyr-options' );

        }

        /** 
         * create_settings
         * 
         * this creates the settings that are used for the plugin
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Protect Your REST
         * 
         * @return void This method returns nothing
         * 
        */
        public function create_settings( ) : void {

            // register the settings
            register_setting( 'kppyr', // group
                'kppyr-options', // name
                array(
                    'type' => 'object',
                    'show_in_rest' => false,
                    'sanitize_callback' => function( $_input ) { return $this -> sanitize_options( $_input ); },
                ) );

            // build out the settings section
            add_settings_section(
                'pyr_setting_section', // id
                null, // title
                null, // callback
                'pyr-settings' // page
            );
    
            // add the settings field
            $this -> the_settings_fields( );

        }

        /** 
         * menu_item
         * 
         * this creates the menu item for the settings page for the plugin
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Protect Your REST
         * 
         * @return void This method returns nothing
         * 
        */
        public function menu_item( ) : void {

            // add an admin menu page
            add_menu_page( 
                __( 'Protect Your REmote', 'kp-pyr' ), // page title
                __( 'Protect Your REmote', 'kp-pyr' ), // menu title
                'manage_options', // minimum user capabilities
                'pyr-settings', // the menu slug
                function( ) {

                    // fire off the local method
                    $this -> the_menu_page( );

                }, 
                'dashicons-share', // the menu icon, 
                20 // the menu position    
            );

        }

        /** 
         * the_menu_page
         * 
         * this will render the settings page
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Protect Your REST
         * 
         * @return void This method returns nothing
         * 
        */
        private function the_menu_page( ) {

            // check user capabilities
            if ( ! current_user_can( 'manage_options' ) ) {

                // shouldn't be here, just return
                return;
            }

            ?>
            <div class="wrap">
                <h1><?php _e( 'Protect Your REmote Settings', 'kp-pyr' ); ?></h1>
                <p></p>
                <?php 
                
                    // check if the user saved the settings
                    if ( isset( $_GET['settings-updated'] ) ) {

                        // add settings saved message with the class of "updated"
                        add_settings_error( 'kppyr_messages', 'kppyr_message', __( 'The settings have been saved.', 'kp-pyr' ), 'updated' );
                    
                    }

                    // show the messages
                    settings_errors( 'kppyr_messages' );
                
                ?>
                <form method="post" action="options.php">
                    <?php

                        // render out the settings fields
                        settings_fields( 'kppyr' );

                        // with the proper sections
                        do_settings_sections( 'pyr-settings' );

                        // now render the submit button
                        submit_button( __( 'Save Your Settings', 'kp-pyr' ) );

                    ?>
                </form>
            </div>
            <?php

        }

        /** 
         * the_settings_fields
         * 
         * this will render the settings fields
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Protect Your REST
         * 
         * @return void This method returns nothing
         * 
        */
        private function the_settings_fields( ) : void {

            // add: primary field to turn off all REST API Endpoints
            add_settings_field(
                'turn_off_rest', // id
                'Turn Off the REST API', // title
                function( ) { 
                    
                    // render the checked
                    $_checked = checked( $this -> _opts['turn_off_rest'], 1, false );

                    // render the field
                    ?>
                    <input <?php _e( esc_attr( $_checked ) ); ?> type="checkbox" name="kppyr-options[turn_off_rest]" id="turn_off_rest" value="1" />
                    <?php
                }, // callback
                'pyr-settings', // page
                'pyr_setting_section' // section
            );

            // add: primary field to turn off all XML RPC Endpoionts
            add_settings_field(
                'turn_off_xmlrpc', // id
                'Turn Off the XML RPC', // title
                function( ) { 
                    
                    // render the checked
                    $_checked = checked( $this -> _opts['turn_off_xmlrpc'], 1, false );

                    // render the field
                    ?>
                    <input <?php _e( esc_attr( $_checked ) ); ?> type="checkbox" name="kppyr-options[turn_off_xmlrpc]" id="turn_off_xmlrpc" value="1" />
                    <?php
                }, // callback
                'pyr-settings', // page
                'pyr_setting_section' // section
            );

            // add: primary field to turn off all RSS Feeds
            add_settings_field(
                'turn_off_feeds', // id
                'Turn Off the RSS Feeds', // title
                function( ) { 
                    
                    // render the checked
                    $_checked = checked( $this -> _opts['turn_off_feeds'], 1, false );

                    // render the field
                    ?>
                    <input <?php _e( esc_attr( $_checked ) ); ?> type="checkbox" name="kppyr-options[turn_off_feeds]" id="turn_off_feeds" value="1" />
                    <?php
                }, // callback
                'pyr-settings', // page
                'pyr_setting_section' // section
            );

        }

        /** 
         * sanitize_options
         * 
         * This method sanitizes the options input fields
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Protect Your REST
         * 
         * @param array $_input The form input fields to be sanitized
         * 
         * @return array Returns the sanitized fields
         * 
        */
        private function sanitize_options( $_input ) : array {

            // hold the fields
            $_fields = array(
                'turn_off_rest' => ( isset( $_input['turn_off_rest'] ) ) ? intval( $_input['turn_off_rest'] ) : 0,
                'turn_off_xmlrpc' => ( isset( $_input['turn_off_xmlrpc'] ) ) ? intval( $_input['turn_off_xmlrpc'] ) : 0,
                'turn_off_feeds' => ( isset( $_input['turn_off_feeds'] ) ) ? intval( $_input['turn_off_feeds'] ) : 0,
            );

            // return the fields array
            return ( $_input ) ?? $_fields;

        }

    }

}
