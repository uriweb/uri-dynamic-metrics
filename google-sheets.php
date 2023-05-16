<?php
/**
 *  @wordpress-plugin
 *  Plugin Name: URI Dynamic Metrics
 *  Plugin URI:  https://github.com/uriweb/uri-dynamic-metrics
 *  Description: A plugin that adds blocks to pull data from google sheets.
 *  Version:     2.0.1
 *  Author:      Alexandra Gauss
 *  Author:      Nathan Lannon
 *  Author URI:  https://nathanlannon.work/
 *  License:     GPL2+
 *  License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

function uri_dynamic_metrics_create_block_google_sheets_block_init() {
	register_block_type( __DIR__ . '/build', array(
        'render_callback' => 'uri_dynamic_metrics_create_block_google_sheets_block_render',
        'supports' => array(
            'html' => false,
            'color' => array(
                'gradients' => true,
                'link' => true,
                '__experimentalDefaultControls' => array(
                    'background' => true,
                    'text' => false,
                    'link' => false
                )
            )    
        )
	) );
}
add_action( 'init', 'uri_dynamic_metrics_create_block_google_sheets_block_init' );

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_script( 'jquery' );

    wp_enqueue_script(
        'nathanlannon-animated-counter',
        plugin_dir_url( __FILE__ ) . '/assets/animated-counter.js',
        array('jquery', 'jquery-easing'),
        '',
        true
    );

    wp_enqueue_script(
        'jquery-easing',
        plugin_dir_url( __FILE__ ) . '/assets/jquery.easing.js',
        array('jquery'),
        '',
        true
    );
});

include 'assets/render.php';