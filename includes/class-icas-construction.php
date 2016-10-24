<?php



/**
 * ICAS Construction 
 * 
 * @author alex
 *
 */
class Icas_Construction{
	
	/**
	 * The product (post) ID.
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * $post Stores post data
	 *
	 * @var $post WP_Post
	 */	
	public $post = null;
	
	protected $type = null;
	
	/**
	 * Sectors (post_type construction_sectors) for a construction if 
	 * Empty array if it is a transversal construction
	 * 
	 * @var array
	 */
	public $long_sectors = array();
	
	public $transversals;
	
	
	/**
	 * meta values for 'construction' post type
	 * @var array
	 */
	protected $meta;
	
	
	public function __construct( $construction ){
		if( is_numeric( $construction ) ){
			$this->id = absint( $construction );
			$this->post = get_post( $this->id );
		} elseif( $construction instanceof  Icas_Construction ){
			$this->id	= $construction->id;
			$this->post	= $construction->post;
		} elseif ( isset( $construction->ID ))	{
			$this->id	= $construction->id;
			$this->post	= $construction->post;
		}
	}
	
	
	/**
	 * Get a construction meta 
	 * 
	 * @param string $key
	 * @return mixed Array|String Array if key exists, empty string if was not found
	 */
	public function get_meta( $key ){
		if( ! isset( $this->meta ) ){
			$this->meta = get_post_meta( $this->id );
		}
		
		if( isset( $this->meta[$key] ) ){
			return $this->meta[$key];
		}
		
		return '';
	}
	
	
	/**
	 * Give the construction type 'long' or 'trans'
	 */
	public function get_construction_type(){
		return $this->type;
	}
	
	
	
	public function is_longitudinal(){
		return ! empty( $this->long_sectors );
	}
	
	
	
	public function is_transversal(){
		return empty( $this->long_sectors );
	}
	
}