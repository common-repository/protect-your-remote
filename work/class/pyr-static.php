<?php
/** 
 * Publicly static class
 * 
 * This class will contain publicly static methods that I feel
 * could be good additions to the wordpress core functionality
 * but mainly so ehy are easily accessible in this plugin ;)
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Protect Your REST
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// make sure the class does not already exist
if( ! class_exists( 'PYR_Static' ) ) {

    /** 
     * Class PYR_Static
     * 
     * Public class holding our static methods
     * 
     * @since 7.4
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Protect Your REST
     * 
    */
    class PYR_Static {

        /** 
         * decrypt
         * 
         * Static method for decryption a string utilizing openssl libraries
         * if openssl is not found, will simply base64_decode the string
         * 
         * @since 7.3
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @param string $_val The string to be encrypted
         * @param string $_key String used as the key for the encryption methods
         * @param int $_secret String used as the initialization vector for the encryption methods
         * 
         * @return string Returns the decrypted or decoded string
         * 
        */
        public static function decrypt( string $_val, string $_key = '', string $_secret = '' ) : string {

            // hold our return
            $_ret = '';
            
            // setup the key
            if( ! $_key ) {

                // default to wordpress's AUTH_KEY constant, or an empty string
                $_key = ( defined( 'AUTH_KEY' ) ) ? AUTH_KEY : '';

            }

            // setup the secret
            if( ! $_secret ) {

                // default to wordpress's AUTH_SALT constant, or an empty string
                $_secret = ( defined( 'AUTH_SALT' ) ) ? AUTH_SALT : '';

            }

            // make sure the openssl library exists
            if( ! function_exists( 'openssl_decrypt' ) ) {

                // it does not, so all we can really do is base64decode the string
                $_ret = base64_decode( $_val );

            // otherwise
            } else {

                // the encryption method
                $_enc_method = "AES-256-CBC";

                // generate a key based on the _key
                $_the_key = hash( 'sha256', $_key );

                // generate an initialization vector based on the _secret
                $_iv = substr( hash( 'sha256', $_secret ), 0, 16 );

                // return the decrypted string
                $_ret = openssl_decrypt( base64_decode( $_val ), $_enc_method, $_the_key, 0, $_iv );

            }

            // return our string
            return gzuncompress( $_ret );

        }

        /** 
         * encrypt
         * 
         * Static method for encrypting a string utilizing openssl libraries
         * if openssl is not found, will simply base64_encode the string
         * 
         * @since 7.3
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @param string $_val The string to be encrypted
         * @param string $_key String used as the key for the encryption methods; defaults to WP AUTH_KEY unless not set, then empty string
         * @param int $_secret String used as the initialization vector for the encryption methods; defaults to WP AUTH_SALT unless not set, then empty string
         * 
         * @return string Returns the encrypted or encoded string
         * 
        */
        public static function encrypt( string $_val, string $_key = '', string $_secret = '' ) : string {

            // hold our return
            $_ret = '';

            // setup the key
            if( ! $_key ) {

                // default to wordpress's AUTH_KEY constant, or an empty string
                $_key = ( defined( 'AUTH_KEY' ) ) ? AUTH_KEY : '';

            }

            // setup the secret
            if( ! $_secret ) {

                // default to wordpress's AUTH_SALT constant, or an empty string
                $_secret = ( defined( 'AUTH_SALT' ) ) ? AUTH_SALT : '';

            }

            // compress our value
            $_val = gzcompress( $_val );

            // make sure the openssl library exists
            if( ! function_exists( 'openssl_encrypt' ) ) {

                // it does not, so all we can really do is base64encode the string
                $_ret = base64_encode( $_val );

            // otherwise
            } else {

                // the encryption method
                $_enc_method = "AES-256-CBC";

                // generate a key based on the _key
                $_the_key = hash( 'sha256', $_key );

                // generate an initialization vector based on the _secret
                $_iv = substr( hash( 'sha256', $_secret ), 0, 16 );

                // return the base64 encoded version of our encrypted string
                $_ret = base64_encode( openssl_encrypt( $_val, $_enc_method, $_the_key, 0, $_iv ) );

            }

            // return our string
            return $_ret;

        }



    }

}
