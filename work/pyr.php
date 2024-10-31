<?php
/** 
 * The primary class file for our plugin
 * 
 * We're actually going to use this to pull everything together
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Protect Your REST
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// make sure the class does not already exist
if( ! class_exists( 'PYR' ) ) {

    /** 
     * Class PYR
     * 
     * Class for running the entire plugin
     * 
     * @since 7.4
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Protect Your REST
     * 
    */
    class PYR {

        // fire us up
        public function __construct( ) {

            // hold our actions
            $_actions = $this -> actions_needed( );

            // we already know they exist so just loop this
            foreach( $_actions as $_action => $_to_take ) {

                // hook into the wordpress action as specified
                add_action( $_action, function( ) use ( $_to_take ) {

                    // fire up the class
                    $_c = new $_to_take -> class( );

                    // fire off the method
                    $_c -> { $_to_take -> method }( );

                    // clean it up
                    unset( $_c );

                }, $_to_take -> priority );

            }

        }

        /** 
         * actions_needed
         * 
         * this method just populates an array of objects to map wordpress actions
         * to our internal methodologies
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return array Returns an array of actionable objects
         * 
        */
        private function actions_needed( ) : array {

            // just return the array
            return array(

                // the settings admin menu
                'admin_menu' => ( object ) array(
                    'class' => 'PYR_Settings',
                    'method' => 'menu_item',
                    'priority' => 1
                ),

                // the settings
                'admin_init' => ( object ) array(
                    'class' => 'PYR_Settings',
                    'method' => 'create_settings',
                    'priority' => 1
                ),

                // front-end denials and removals
                'plugins_loaded' => ( object ) array(
                    'class' => 'PYR_FrontEnd',
                    'method' => 'process_kills',
                    'priority' => 1
                ),

                // front-end requestss
                'init' => ( object ) array(
                    'class' => 'PYR_FrontEnd',
                    'method' => 'process_requests',
                    'priority' => PHP_INT_MAX
                ),

                // front-end requestss
                'template_redirect' => ( object ) array(
                    'class' => 'PYR_FrontEnd',
                    'method' => 'process_requests',
                    'priority' => PHP_INT_MAX
                ),

            );

        }

    }

}
