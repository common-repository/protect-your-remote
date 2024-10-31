<?php
/** 
 * Front End Processing
 * 
 * This class handles all front-end processing and requests for the 
 * site's REST API
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Protect Your REST
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// make sure the class does not already exist
if( ! class_exists( 'PYR_FrontEnd' ) ) {

    /** 
     * Class PYR_FrontEnd
     * 
     * Class for processing all the front-end stuff necessary for the REST API
     * 
     * @since 7.4
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Protect Your REST
     * 
     * @property object $_opts The options for internal use only
     * 
    */
    class PYR_FrontEnd {

        /** 
         * process_kills
         * 
         * this method controls the front end processing for allowing
         * or effectivly "killing" access to the Wordrpess REST API
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Protect Your REST
         * 
         * @return void This method returns nothing
         * 
        */
        public function process_kills( ) : void {

            // process the wordpress actions controlling remote access to this site
            $this -> process_actions( );

            // now process the filters controlling remote access to this site
            $this -> process_filters( );

            // process the actuall requests
            $this -> process_requests( );
            
        }

        /** 
         * process_requests
         * 
         * this method attempts to interupt all requests to the site
         * to look for REST requests.  If found, it will throw a 403
         * unauthorized error page
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Protect Your REST
         * 
         * @return void This method returns nothing
         * 
        */
        public function process_requests( ) : void {

            // get the options
            $_opts = ( get_option( 'kppyr-options' ) ) ? ( object ) get_option( 'kppyr-options' ) : new stdClass( );

            // first see if we're going to be turning off the REST
            if( isset( $_opts -> turn_off_rest ) && boolval( $_opts -> turn_off_rest ) ) {

                // now check if it is indeed a REST request
                $_is_rest = $this -> is_rest_request( );

                // if it is
                if( $_is_rest ) {

                    // render the error message
                    $this -> error_out_the_request( 'rest' );

                }

            }

            // first see if we're going to be turning off the feeds
            if( isset( $_opts -> turn_off_feeds ) && boolval( $_opts -> turn_off_feeds ) ) {

                // if it is
                if( is_feed( ) ) {

                    // render the error message
                    $this -> error_out_the_request( 'feed' );

                }

            }

            // first see if we're going to be turning off the RPC
            if( isset( $_opts -> turn_off_xmlrpc ) && boolval( $_opts -> turn_off_xmlrpc ) ) {

                // is this a RPC or RSD request?
                $_is_rpc = ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) || isset( $_GET['rsd'] );

                // if it is
                if( $_is_rpc ) {

                    // render the error message
                    $this -> error_out_the_request( 'rpc' );

                }

            }

        }

        /** 
         * process_actions
         * 
         * this method attempts to process the actions controlling the REST API
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Protect Your REST
         * 
         * @return void This method returns nothing
         * 
        */
        private function process_actions( ) : void {

            // get the options
            $_opts = ( get_option( 'kppyr-options' ) ) ? ( object ) get_option( 'kppyr-options' ) : new stdClass( );

            // process the REST API
            if( isset( $_opt -> turn_off_rest ) && boolval( $_opt -> turn_off_rest ) ) {

                // remove the REST info from the headers
                remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
                remove_action( 'template_redirect', 'rest_output_link_header', 11 );

                // remove the rest link from the <head>
                remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );

                // remove the REST defaults
                remove_action( 'init', 'rest_api_init' );
                remove_action( 'rest_api_init', 'rest_api_default_filters', 10 );
                remove_action( 'rest_api_init', 'register_initial_settings', 10 );
                remove_action( 'rest_api_init', 'create_initial_rest_routes', 99 );
                remove_action( 'parse_request', 'rest_api_loaded' );

            }

            // process the XML RPC
            if( isset( $_opt -> turn_off_xmlrpc ) && boolval( $_opt -> turn_off_xmlrpc ) ) {

                // remove the realy simple discovery link from the <head>
                remove_action( 'wp_head', 'rsd_link', 10 );

                // remove the manifest link from the <head>
                remove_action( 'wp_head', 'wlwmanifest_link', 10 );

            }

            // process the Feeds
            if( isset( $_opt -> turn_off_feeds ) && boolval( $_opt -> turn_off_feeds ) ) {

                // remove the oembed discovery links from the <head>
                remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

                // remove the feed links from the <head>
                remove_action( 'wp_head', 'feed_links', 2 );
                remove_action( 'wp_head', 'feed_links_extra', 3 );

                // remove the feeds themselves
                remove_action( 'do_feed_rdf', 'do_feed_rdf', 10 );
                remove_action( 'do_feed_rss', 'do_feed_rss', 10 );
                remove_action( 'do_feed_rss2', 'do_feed_rss2', 10 );
                remove_action( 'do_feed_atom', 'do_feed_atom', 10 );
                remove_action( 'do_pings', 'do_all_pings', 10 );

            }
            
        }

        /** 
         * process_filters
         * 
         * this method attempts to process the filters controlling the remote access
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Protect Your REST
         * 
         * @return void This method returns nothing
         * 
        */
        private function process_filters( ) : void {

            // get the options
            $_opts = ( get_option( 'kppyr-options' ) ) ? ( object ) get_option( 'kppyr-options' ) : new stdClass( );

            // process the REST API
            if( isset( $_opt -> turn_off_rest ) && boolval( $_opt -> turn_off_rest ) ) {

                // try to disable the REST API
                add_filter( 'rest_jsonp_enabled', '__return_false' );
                add_filter( 'json_enabled', '__return_false' );

                // try to force the authentication to fail
                add_filter( 'rest_authentication_errors', function( $_access ) {

                    // return a wordpress error
                    new WP_Error( 'rest_cannot_access', __( 'REST has been disabled.', 'kp-pyr' ) );

                }, 20 );

                // hook into the rest api endpoints
                add_filter( 'rest_endpoints', function( $_eps ) : array {

                    // loop over the existing endpoints
                    foreach( $_eps as $_ep => $_det ) {

                        // remove it
                        unset( $_eps[$_ep] );

                    }

                    // return the endpoints array
                    return $_eps;

                }, PHP_INT_MAX );

                // try to kill application passwords
                add_filter( 'wp_is_application_passwords_available', '__return_false' );

            }

            // process the XML RPC
            if( isset( $_opt -> turn_off_xmlrpc ) && boolval( $_opt -> turn_off_xmlrpc ) ) {

                // remove the RPC
                add_filter( 'xmlrpc_enabled', '__return_false' );
                
            }

            // process the Feeds
            if( isset( $_opt -> turn_off_feeds ) && boolval( $_opt -> turn_off_feeds ) ) {

                // remove the ping backs
                add_filter( 'pings_open', '__return_false' );

            }

        }

        /** 
         * is_rest_request
         * 
         * this method attempts to determine if the current request is for the REST API or not
         * based on a fiew factors
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Protect Your REST
         * 
         * @return bool This method returns a boolean value
         * 
        */
        private function is_rest_request( ) : bool {

            // hold our return, default as false
            $_ret = false;

            // our full requested URI
            $_uri = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            // check if the REST_REQUEST constant is defined, and see if there is a rest_route attached to it
            if( defined( 'REST_REQUEST' ) && REST_REQUEST || isset( $_GET['rest_route'] ) && strpos( $_GET['rest_route'] , '/', 0 ) === 0 ) {

                // there is, return true
                $ret = true;

            }
				
            // check the wp_rewrite global
            global $wp_rewrite;

            // if it's not currently set
            if( ! $wp_rewrite ) {
                
                // set it
                $wp_rewrite = new WP_Rewrite( );
                
            }

            // parse the rest URL, if it exists
            $_rest_url = wp_parse_url( trailingslashit( rest_url( ) ) );

            // parse the current URL, if it exists
            $_current_url = wp_parse_url( $_uri );

            // check if the rest url matches the current url
            if( in_array( $_current_url['path'], $_rest_url ) ) {

                // it does not, return true
                $_ret = true;

            }
            
            // get the rest prefix, if it exists
            $_rest_prefix = rest_get_url_prefix( );

            // set the return if the prefix matches
            $_ret = false !== strpos( $_uri, $_rest_prefix );

            // return our return
            return $_ret;

        }

        /** 
         * error_out_the_request
         * 
         * render a 403 header, and dump an error message
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Protect Your REST
         * 
         * @param string $_type The type of request presented: used for formatting the proper message
         * 
         * @return void This method returns nothing
         * 
        */
        private function error_out_the_request( string $_type = 'rest' ) : void {

            // set the 403
            header( 'HTTP/1.1 403 Forbidden' );

            // hold the content type string
            $_ct = '';

            // hold the message string
            $_msg = '';

            // switch to generate the message and proper content type
            switch( $_type ) {

                // feed
                case 'feed':

                    // the content type should be:
                    $_ct = 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' );

                    $_msg = '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>
                    <rss version="2.0">
                        <channel>
                            <timestamp>' . date( "c", time( ) ) . '</timestamp>
                            <status>403</status>
                            <error>' . __( 'Access Forbidden', 'kp-pyr' ) . '</error>
                            <message>' . __( 'Your access to the RSS Feeds have been forbidden by the site admins.', 'kp-pyr' ) . '</message>
                        </channel>
                    </rss>';

                    break;

                // RPC / RSD
                case 'rpc':

                    // the content type should be:
                    $_ct = $_ct = sprintf( 'Content-Type: text/xml; charset=%s', get_option( 'blog_charset' ) );

                    $_msg = '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>
                    <rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">
                        <service>
                            <timestamp>' . date( "c", time( ) ) . '</timestamp>
                            <status>403</status>
                            <error>' . __( 'Access Forbidden', 'kp-pyr' ) . '</error>
                            <message>' . __( 'Your access to the RPC has been forbidden by the site admins.', 'kp-pyr' ) . '</message>
                        </service>
                    </rsd>';

                    break;

                // REST or default
                case 'rest':
                    default:
                        
                        // the content type should be:
                        $_ct = sprintf( 'Content-Type: application/json; charset=%s', get_option( 'blog_charset' ) );
    
                        // the message
                        $_msg = json_encode( array(
                            'timestamp' => date( "c", time( ) ),
                            'status' => 403,
                            'error' => __( 'Access Forbidden', 'kp-pyr' ),
                            'message' => __( 'Your access to the REST API has been forbidden by the site admins.', 'kp-pyr' ),
                        ) );
                    break;
                    
            }

            // set the content type
            header( $_ct );

            // write a error response
            _e( $_msg );

            // now die
            die( );

        }

    }

}
