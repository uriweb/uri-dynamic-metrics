<?php
/**
 *  Plugin Name: URI Dynamic Metrics
 *  Plugin URI:  https://github.com/uriweb/uri-dynamic-metrics
 *  Description: A plugin that adds blocks to pull data from google sheets.
 *  Version:     2.0.1
 *  Author: URI Web Communications
 *  Author URI: https://today.uri.edu/
 *  License:     GPL2+
 *  License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * 
 *  @author Alexandra Gauss <alexandra_gauss@uri.edu>
 *  @author Brandon Fuller <bjcfuller@uri.edu>
 *  @author Nathan Lannon <nathanlannon27@gmail.com>
 *  @package uri-dynamic-metrics
 */

function uri_dynamic_metrics_register_block() {

    // Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	register_block_type( __DIR__ . '/build', array(
        'render_callback' => 'uri_dynamic_metrics_render_block',
        'supports' => array(
            'html' => false
        )
	) );
}
add_action( 'init', 'uri_dynamic_metrics_register_block' );

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_script( 'jquery' );

    wp_enqueue_script(
        'uri-dynamic-metrics-animated-counter',
        plugin_dir_url( __FILE__ ) . '/assets/animated-counter.js',
        array('jquery', 'jquery-easing'),
        '',
        true
    );

    wp_enqueue_script(
        'uri-dynamic-metrics-jquery-easing',
        plugin_dir_url( __FILE__ ) . '/assets/jquery.easing.js',
        array('jquery'),
        '',
        true
    );
});

include 'assets/render.php';