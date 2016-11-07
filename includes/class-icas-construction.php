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
	
	
	
	public function get_meta_as_string( $key ){
		if( ! isset( $this->meta ) ){
			$this->meta = get_post_meta( $this->id );
		}
	
		if( isset( $this->meta[$key] ) ){
			return implode(',' , $this->meta[$key] );
		}
	
		return '';
	}
	
	
	public function get_cadastral_code(){
	
		// get area taxonomy for this construction
		$cod_bazin_tax = wp_get_post_terms( $this->id, 'area' );
		// sorting the taxonomy
		if( $cod_bazin_tax ){
			$cod_bazin_tax = ap_icas_sort_taxonomy_hierarchy( $cod_bazin_tax );
		}
		
		$cod_bazin = array_fill( 0, ICAS_AREA_TAX_DEEP, '' );
		
		// level 0 is provided as term_id value, next levels as taxonomy name
		if( isset( $cod_bazin_tax[0] )  && isset( $cod_bazin_tax[0]->term_id ) ){
			$cod_bazin[0] = $cod_bazin_tax[0]->name;
		
			// if is set the 0 level, update the rest with the values
			for( $i = 1; $i < ICAS_AREA_TAX_DEEP; $i++ ){
		
				if( isset( $cod_bazin_tax[$i] )  && isset( $cod_bazin_tax[$i]->name )){
					$cod_bazin[$i] = $cod_bazin_tax[$i]->name;
				}else{
					$cod_bazin[$i] = '';
				}
			}
		}
		
		
		return implode('-', array_filter($cod_bazin) );
		
	}
	
	
	public function get_term($term, $index = 0, $term_field = 'name'){
		$terms_arr = wp_get_object_terms( $this->id, $term );
		if($terms_arr && $terms_arr[$index]){
			return $terms_arr[$index]->$term_field;
		}
		
		return '';
	}

	
}