<?php



/**
 * Icas class
 *
 * Entry point for frontend interface
 *
 * @author alex
 *
 */
class Icas{

	public function __construct(){
		add_action('init', array( $this, 'includes' ) );
		// load scripts & css for all pages
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		
		// load scripts & css for specific pages
		add_action( 'template_redirect', array( $this, 'load_pages_scripts' ) );
	}



	public function includes(){

	}


	/**
	 *  Load CSS for administration interface
	 */
	public function load_scripts(){
		wp_enqueue_style( 'icas_style', ICAS_PLUGIN_URL.'/css/icas.css', false, "1.0", "all" );
		
		wp_enqueue_style( 'chartist_style', ICAS_PLUGIN_URL.'/css/chartist.css', false, "1.0", "all" );
		

		
		// JS
		
		wp_register_script( 'icas-js', ICAS_PLUGIN_URL.'/js/form-script.js', array( 'jquery' ),'1.0', 'all' );
		
		wp_register_script( 'chartist', ICAS_PLUGIN_URL.'/js/chartist.js', array(), '1.0', 'all' );
		
		
		
		wp_register_script( 'chartist-accessibility', ICAS_PLUGIN_URL.'/js/chartist-plugin-accessibility.js', array( 'chartist' ),'1.0', 'all' );
		
		wp_register_script( 'chartist-axistitle', ICAS_PLUGIN_URL.'/js/chartist-plugin-axistitle.js', array( 'chartist' ),'1.0', 'all' );
		
		
		$protocol = isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://';		
		$params = array( 
				'ajaxurl' => admin_url( 'admin-ajax.php', $protocol) ,
				'select_txt' => __("Selecteaza", "icas")
		);		

		
		wp_localize_script( 'icas-js', 'icas' , $params );
		
		wp_enqueue_script( 'icas-js' );
		
		wp_enqueue_script( 'chartist' );
		
		wp_enqueue_script( 'chartist-accessibility' );
		
		wp_enqueue_script( 'chartist-axistitle');
		
		wp_enqueue_script( 'jquery-ui-tooltip' );
		
		
		wp_enqueue_style( 'icas-ui-css',
		'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css',
		false,
		ICAS_PLUGIN_VERSION,
		false );
	}
	
	
	public function load_pages_scripts(){
		global $post;

		if( is_single() && 'construction' == get_post_type() ){
			wp_enqueue_style('fancybox', ICAS_PLUGIN_URL.'/fancyBox/source/jquery.fancybox.css', false, "1.0", "all");
			wp_register_script( 'fancybox-js', ICAS_PLUGIN_URL.'/fancyBox/source/jquery.fancybox.pack.js', array( 'jquery' ),'1.0', 'all' );
			wp_register_script( 'single-construction-js', ICAS_PLUGIN_URL.'/js/single-construction.js', array( 'jquery', 'fancybox-js' ),'1.0', 'all' );
			wp_enqueue_script( 'fancybox-js' );
			wp_enqueue_script( 'single-construction-js' );
		}
	}

}