<?php


class Icas_Construction_Sector{
	
	public $id;
	
	protected static $sector_fields_arr = array(
				'ap_icas_long_cons_sector',
				'ap_icas_long_cons_stairs',
				'ap_icas_long_cons_length',
				'ap_icas_long_cons_deep',
				'ap_icas_long_cons_width_apron',
				'ap_icas_long_cons_fruit_guard_wall',
				
				'ap_icas_long_apron_craks_nr',
				'ap_icas_long_apron_damage_percent',
				'ap_icas_long_apron_displaced',
				'ap_icas_long_apron_abrasion_deep',
				'ap_icas_long_apron_abrasion_percent',
				
				'ap_icas_long_sidewall_left_horiz_craks_nr',
				'ap_icas_long_sidewall_left_horiz_length',
				'ap_icas_long_sidewall_left_vert_craks_nr',
				'ap_icas_long_sidewall_left_vert_length',
				'ap_icas_long_sidewall_left_displaced',
				'ap_icas_long_sidewall_left_abrasion_deep',
				'ap_icas_long_sidewall_left_abrasion_percent',
				
				'ap_icas_long_sidewall_right_horiz_craks_nr',
				'ap_icas_long_sidewall_right_horiz_length',
				'ap_icas_long_sidewall_right_vert_craks_nr',
				'ap_icas_long_sidewall_right_vert_length',
				'ap_icas_long_sidewall_right_displaced',
				'ap_icas_long_sidewall_right_abrasion_deep',
				'ap_icas_long_sidewall_right_abrasion_percent',
				
				
				'ap_icas_long_disfunctio_su',
				'ap_icas_long_disfunctio_srad',
				'ap_icas_long_disfunctio_sect_aval'
				
		) ;
	
	
	protected static $spur_fields_arr = array(
			'ap_icas_long_spur_spur_nr',
			'ap_icas_long_spur_decastr_left',
			'ap_icas_long_spur_decastr_right',
			'ap_icas_long_spur_afuieri_height',
			'ap_icas_long_spur_afuieri_percent',
			'ap_icas_long_spur_horiz_craks_nr',
			'ap_icas_long_spur_horiz_lenght',
			'ap_icas_long_spur_vert_craks_nr',
			'ap_icas_long_spur_vert_lenght',
			'ap_icas_long_spur_displaced_left',
			'ap_icas_long_spur_displaced_right',
			'ap_icas_long_spur_displaced_center',
			'ap_icas_long_spur_abrasion_deep',
			'ap_icas_long_spur_abrasion_percent'
	);
	
	
	protected static $material_term_fields_arr = array(
			'mat_sect_apron',
			'mat_sect_walls',
			'mat_sect_spur'
	);
	
	
	/**
	 * Array with values for each key from self::sector_fields_arr
	 * @var array
	 */
	protected $sector_arr;
	/**
	 * Array containing an array for each spur field
	 * @var array
	 */
	protected $spurs_arr;
	
	
	/**
	 * Array with the construction material taxonomies terms 
	 * @var array
	 */
	protected $material_terms_arr;

	
	/**
	 * @param int $id
	 */
	public function __construct( $id = 0 ){		
		$this->id = $id;
		
		$this->sector_arr	= array();
		$this->spurs_arr	= array();
		$this->material_terms_arr = array();
		
		if( $this->id ){
			$meta_arr = get_post_meta( $this->id );
			
			if( $meta_arr ){
				foreach ( self::$sector_fields_arr as $key ){
					$this->sector_arr[$key] = isset( $meta_arr[$key][0] ) ? $meta_arr[$key][0] : '';
				}
				
				
				foreach ( self::$spur_fields_arr as $key ){
					$this->spurs_arr[$key] = isset( $meta_arr[$key][0] ) ? unserialize ( $meta_arr[$key][0] ) : array('');
				}
			}
			
			
			$terms = wp_get_post_terms( $this->id, self::$material_term_fields_arr );
			if( ! is_wp_error( $terms ) ){
				foreach ( $terms as $t ){
					$this->material_terms_arr[$t->taxonomy] = $t;
				}
			}			
		}
		
	}
	
	
	/**
	 * Get the sector value for a given key, empty string if not found
	 * 
	 * @param string $f
	 * @return Ambigous <string, multitype:string >
	 */
	public function get_field( $f ){		
		return isset( $this->sector_arr[$f] ) ? $this->sector_arr[$f] : '';
	}
	
	
	/**
	 * Get the spur value for a given field and key, empty string if not found
	 * 
	 * @param string $f spur field
	 * @param string $index array index
	 * @return Ambigous <string, multitype:string >
	 */
	public function get_spur_field( $f, $index = 0 ){		
		return isset( $this->spurs_arr[$f][$index] ) ? $this->spurs_arr[$f][$index] : '';
	}
	
	
	public function get_spur_numbers(){
		if(isset( $this->spurs_arr['ap_icas_long_spur_spur_nr'] ) && is_array( $this->spurs_arr['ap_icas_long_spur_spur_nr'] ) ){
			return count( $this->spurs_arr['ap_icas_long_spur_spur_nr'] );
		}
		
		return 0;
	}
	
	
	/**
	 * Give the term_id for specified material taxonomy
	 * Empty string if not exists
	 * 
	 * @param unknown $taxonomy
	 * @return string
	 */
	public function get_material_term_value( $taxonomy ){
		if( isset( $this->material_terms_arr[$taxonomy]->term_id ) ){
			return $this->material_terms_arr[$taxonomy]->term_id;
		}
		return '';
	}
	
	
	public static function get_sector_fields_names(){
		return self::$sector_fields_arr;
	}
	
	
	public static function get_spur_fields_names(){
		return self::$spur_fields_arr;
	}
	
	
	/**
	 * Get the material term fields, which correspond with sector  construction materials taxonomies
	 * 
	 * @return multitype:string 
	 */
	public static function get_material_term_fields(){
		return self::$material_term_fields_arr;
	}
	
	/**
	 * Get the material term fields, which correspond with sector  construction materials taxonomies
	 * The same as get_material_term_fields()
	 * 
	 * @return multitype:string 
	 */
	public static function get_material_taxonomies(){
		return self::$material_term_fields_arr;
	}
	
}