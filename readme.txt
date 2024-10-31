=== Protect Your REmote ===
Contributors: kevp75
Donate link: https://paypal.me/kevinpirnie
Tags: rest, api, rest api, security, rest security, remote security, remote access, rss, rds, rpc
Requires at least: 5.5
Tested up to: 5.9.2
Requires PHP: 7.4
Stable tag: 0.1.27
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Simple plugin to manage RPC, RSD, RSS, and REST access.

== Description ==

This plugin attempts to protect your site's remote services by checking a box.

It not only disables the remote data, it also denies access to them.

Please beware, turning off the REST API can have negative admin impacts due to Block needs.  We are working on setting up the settings to allow you to choose to turn it back on for the admin.

== Installation ==

1. Download the plugin, unzip it, and upload to your sites `/wp-content/plugins/` directory
    1. You can also upload it directly to your Plugins admin
	2. Or install it from the Wordpress Plugin Repository
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Why would I need this plugin? =

I needed a way to quickly turn access to the remote channels of my site on and off, and rather than digging through another plugins settings, I wrote this to simply find and do what I needed it to do.

== Screenshots ==

1. Settings 1

== Changelog ==

= 0.1.27 =
* First public release

== In The Works ==

* Turn REST back on for Blocks
* Add JWT methodology for access
* Set access levels for different remote methods, routes, and endpoints
