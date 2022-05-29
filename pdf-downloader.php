<?php
/**
 * Plugin Name: Kodesmeden PDF Downloader
 * Version: 1.0.0
 * Description: This plugin is created specifically for searching and downloading PDF files from the uploads folder.
 * Author: Kodesmeden
 * Author URI: https://kodesmeden.dk/
 * Requires at least: 5.0
 * Tested up to: 6.0
 *
 * Text Domain: kodesmeden
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Daniel S. Nielsen
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Setup constants
define( 'KS_PDF_DL_FILE', __FILE__ );
define( 'KS_PDF_DL_VERSION', '1.0.0' );

// Load language early
load_plugin_textdomain( 'kodesmeden', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

// Initialize Plugin
require_once( __DIR__ . '/includes/init.php' );

// Add plugin action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ks_pdf_dl_action_links' );
function ks_pdf_dl_action_links( $links ) {
	$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=ks-pdf-dl' ) ) . '">' . __( 'Search', 'kodesmeden' ) . '</a>';
	
	return $links;
}

// Add plugin meta links
add_filter( 'plugin_row_meta' , 'ks_pdf_dl_meta_links', 10, 4 );
function ks_pdf_dl_meta_links( $plugin_meta, $plugin_file, $plugin_data, $status ) {
	if ( $plugin_file === plugin_basename( KS_PDF_DL_FILE ) ) {
		$button_style_0 = 'padding: 2px 10px 4px;border-radius: 3px;';
		$button_style_1 = 'background: #175D55;color: #fff;' . $button_style_0;
		$button_style_2 = 'background: #f8a717;color: #444;' . $button_style_0;
		$button_style_3 = 'background: #203863;color: #fff;' . $button_style_0;
		
		$plugin_meta = [
			'<a href="https://kodesmeden.dk/?utm_source=' . parse_url( home_url(), PHP_URL_HOST ) . '&utm_medium=referral" target="_blank" style="' . $button_style_1 . '"><span class="dashicons dashicons-visibility"></span> ' . __( 'Visit Kodesmeden', 'kodesmeden' ) . '</a>
			 <a href="https://kodesmeden.dk/kontakt/?utm_source=' . parse_url( home_url(), PHP_URL_HOST ) . '&utm_medium=referral" target="_blank" style="' . $button_style_2 . '"><span class="dashicons dashicons-email"></span> ' . __( 'Contact Kodesmeden', 'kodesmeden' ) . '</a>
			 <a href="https://www.dnsinfo.dk/?utm_source=' . parse_url( home_url(), PHP_URL_HOST ) . '&utm_medium=referral" target="_blank" style="' . $button_style_3 . '"><span class="dashicons dashicons-search"></span> ' . __( 'DNS Info', 'kodesmeden' ) . '</a>',
		];
	}
     
    return $plugin_meta;
}

// Init admin actions
new KS_PDF_DL_Admin();