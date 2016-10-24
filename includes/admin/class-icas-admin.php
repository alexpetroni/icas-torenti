<?php



/**
 * Icas_Admin class 
 * 
 * Entry point for administration interface
 * 
 * @author alex
 *
 */
class Icas_Admin{
	
	public function __construct(){
		add_action('init', array( $this, 'includes' ) );
		
		// Load admin CSS files
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_css' ) );
		
		// Include metaboxes js files
		add_action('admin_print_scripts-post.php', array( $this, 'image_admin_scripts' ) );
		add_action('admin_print_scripts-post-new.php', array( $this, 'image_admin_scripts' ) );
		
		// to avoid the expensive query which generate Custom Fields meta box (wp-admin/includes/templates.php function meta_form(){}
		add_filter( 'postmeta_form_keys', array($this, 'limit_postmeta'), 10, 3  );
	}
	

	
	
	
	public function includes(){
		include ICAS_PLUGIN_DIR.'includes/form-elements.php';
		include 'metaboxes/class-icas-construction-metaboxes.php';
	}
	
	
	/**
	 *  Load CSS for administration interface
	 */
	public function load_admin_css(){	
		wp_enqueue_style('icas_admin_style', ICAS_PLUGIN_URL.'/css/admin.css',false, "1.0", "all");
		
		wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
	}
	
	
	
	/**
	 *  Load js scripts for 'construction' post_type
	 */
	public function image_admin_scripts() {	
		global $post;
		
		if( 'construction' == get_post_type( $post ) ){
			
			wp_enqueue_media();
		
			wp_enqueue_script(
			'ap-icas-image-upload',
			plugins_url() . '/icas-torenti/js/admin/admin-construction-load-images.js',
			array( 'jquery' ),
			'1.0',
			'all'
					);
		
			wp_enqueue_script(
			'ap-icas-metabox-scripts',
			plugins_url() . '/icas-torenti/js/admin/admin-construction-metabox.js',
			array( 'jquery' ),
			'1.0',
			'all'
					);
		}	
	}

	 
	 /*
	  * Returning a non-null value will effectively short-circuit and avoid a
	  *  expensive query against postmeta.
	  */
	public function limit_postmeta( $string, $post ) {
		return array(null);
	}
	
}