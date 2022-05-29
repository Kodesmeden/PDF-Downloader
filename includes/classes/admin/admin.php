<?php

class KS_PDF_DL_Admin {
	
	public function __construct() {
		$this->init();
	}
	
	public function init() {
		add_action( 'init', [ $this, 'download' ] );
		add_action( 'admin_menu', [ $this, 'add_ks_menu' ] );
		
		$page = htmlspecialchars( filter_input( INPUT_GET, 'page' ) );
		if ( $page === 'ks-pdf-dl' ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		}
	}
	
	public function enqueue_scripts() {
		$kodesmeden_assets = plugin_dir_url( KS_PDF_DL_FILE ) . 'assets';
		
		wp_enqueue_style( 'kodesmeden', $kodesmeden_assets . '/css/ks_pdf_dl.css', [], KS_PDF_DL_VERSION );
		wp_enqueue_script( 'kodesmeden', $kodesmeden_assets . '/js/ks_pdf_dl.js', [ 'jquery' ], KS_PDF_DL_VERSION, true );
	}
	
	public function add_ks_menu() {
		add_menu_page(
			__( 'PDF Downloader', 'kodesmeden' ), // Page Title
			__( 'PDF Downloader', 'kodesmeden' ), // Menu Title
			'manage_options', 
			'ks-pdf-dl',
			[ $this, 'page_content' ],
			'dashicons-pdf',
			90,
		);
	}
	
	public function page_content() {
		$files = [];
		
		$page = htmlspecialchars( filter_input( INPUT_GET, 'page' ) );
		$search = htmlspecialchars( filter_input( INPUT_GET, 's' ) );
		if ( ! empty( $search ) ) {
			$directory = WP_CONTENT_DIR . '/uploads';
			
			$files = $this->search_pdf_files( $directory, $search );
		}
		
		
		echo '<div class="wrap">
			<h1>' . __( 'Kodesmeden PDF Downloader', 'kodesmeden' ) . '</h1>
			<form method="get" action="" class="pdf-search-form">
				<input type="hidden" name="page" value="' . $page . '">
				<input type="search" name="s" value="' . $search . '" required>
				<input type="submit" value="' . __( 'Search', 'kodesmeden' ) . '">
			</form>';
		
		if ( isset( $_GET['s'] ) ) {
			if ( ! empty( $files ) ) {
				echo '<p>' . sprintf( __( 'Your search for "%s" yielded these results:', 'kodesmeden' ), $search ) . '</p>';
				echo '<table class="pdf-dl-table" cellspacing="0">';
				
				foreach ( $files as $file ) {
					$filename = basename( $file );
					
					echo '<tr>
						<td>' . $filename . '</td>
						<td><a href="?page=' . $page . '&download=' . $filename . '" data-file="' . $file . '" title="' . sprintf( __( 'Download %s', 'kodesmeden' ), $filename ) . '" class="pdf-download"><span class="dashicons dashicons-download"></span></a></td>
					</tr>';
				}
				
				echo '</table>';
			} else {
				echo '<p>' . sprintf( __( 'Your search for "%s" yielded no results...', 'kodesmeden' ), $search ) . '</p>';
			}
		}
		
		echo '</div>';
	}
	
	public function download() {
		$page = htmlspecialchars( filter_input( INPUT_GET, 'page' ) );
		$file = htmlspecialchars( filter_input( INPUT_GET, 'download' ) );
		
		if ( ! is_admin() ) {
			return;
		}
		
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		if ( $page !== 'ks-pdf-dl' ) {
			return;
		}
		
		if ( empty( $file ) ) {
			return;
		}
		
		return $this->download_pdf( $file );
	}
	
	private function search_pdf_files( $directory, $search, &$return = [] ) {
		foreach ( scandir( $directory ) as $instance ) {
			if ( $instance[0] == '.' ) {
				continue;
			}
			
			$path = $directory . '/' . $instance;
			
			if ( is_dir( $path ) ) {
				$this->search_pdf_files( $path, $search, $return );
			} else {
				if ( ! strrpos( $instance, '.pdf' ) ) {
					continue;
				}
				
				if ( ! stripos( $instance, $search ) ) {
					continue;
				}
				
				$return[] = $path;
			}
		}
		
		return $return;
	}
	
	private function download_pdf( $path ) {
		$referrer = htmlspecialchars( filter_input( INPUT_SERVER, 'HTTP_REFERER' ) );
		$file = $path;
		
		if ( file_exists( $file ) ) {
			$filename = basename( $file );
			$filesize = filesize( $file );
			
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate' );
			header( 'Pragma: public' );
			header( 'Content-Length: ' . $filesize );
			
			readfile( $file );
		} else {
			wp_redirect( $referrer );
		}
		
		exit;
	}
	
}