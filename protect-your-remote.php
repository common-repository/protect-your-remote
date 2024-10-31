<?php

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

/*
Plugin Name:    Protect Your REmote
Plugin URI:     https://kevinpirnie.com
Description:    Plugin attempts to protect your remote endpoints
Version:        1.0.27
Author:         Kevin C Pirnie
Text Domain:    kp-pyr
License:        GPLv3
License URI:    https://www.gnu.org/licenses/gpl-3.0.html
*/

// setup the full page to this plugin
define( 'KPPYR_PATH', dirname( __FILE__ ) );

// setup the directory name
define( 'KPPYR_DIRNAME', basename( dirname( __FILE__ ) ) );

// setup the primary plugin file name
define( 'KPPYR_FILENAME', basename( __FILE__ ) );

// Include our "work"
require KPPYR_PATH . '/work/common.php';
