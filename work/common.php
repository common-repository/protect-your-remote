<?php
/** 
 * Common Functionality
 * 
 * Setup the common functionality for the plugin
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Protect Your REST
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// set the plugins path
$_pi_path = KPPYR_PATH . '/protect-your-rest.php';

// Plugin Activation
register_activation_hook( $_pi_path, function( $_network ) : void {
        
    // check the PHP version, and deny if lower than 7.4
    if ( version_compare( PHP_VERSION, '7.4', '<=' ) ) {

        // it is, so throw and error message and exit
        wp_die( __( '<h1>PHP To Low</h1><p>Due to the nature of this plugin, it cannot be run on lower versions of PHP.</p><p>Please contact your hosting provider to upgrade your site to at least version 7.4.</p>', 'kp-pyr' ), 
            __( 'Cannot Activate: PHP To Low', 'kp-pyr' ),
            array(
                'back_link' => true,
            ) );

    }

    // check if we tried to network activate this plugin
    if( is_multisite( ) && $_network ) {

        // we did, so... throw an error message and exit
        wp_die( 
            __( '<h1>Cannot Network Activate</h1><p>Due to the nature of this plugin, it cannot be network activated.</p><p>Please go back, and activate inside your subsites.</p>', 'kp-pyr' ), 
            __( 'Cannot Network Activate', 'kp-pyr' ),
            array(
                'back_link' => true,
            ) 
        );
    }

} );

// Plugin De-Activation
register_deactivation_hook( $_pi_path, function( ) : void {

    // nothing to do here because we want to be able to keep settings on deactivate

} );

// let's make sure the plugin is activated
if( in_array( KPPYR_DIRNAME . '/' . KPPYR_FILENAME, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    // setup our autoload
    spl_autoload_register( function( $_cls )  : void {

        // as long as we're not looking for PYR
        if( $_cls != 'PYR' ) {

            // reformat the class name to match the file name for inclusion
            $_class = strtolower( str_ireplace( '_', '-', $_cls ) );

            // pull in our classes based on the file path
            $_path = KPPYR_PATH . "/work/class/$_class.php";

            // if the file exists
            if( @is_readable( $_path ) ) {

                // include it once
                include $_path;
            }

        }

    } );

    // include the primary class file
    include KPPYR_PATH . '/work/pyr.php';

    // setup a class alias
    class_alias( 'PYR_Static', 'PYRS' );

    // and fire us up
    new PYR( );

}
