<?php
// Construction identification metabox
class Icas_Construction_Identification_Metabox{	
	
	
	public static function get_fields(){
		 
		$fields= array(
				//'ap_icas_construction_id',
				'ap_icas_construction_code',
				'ap_icas_basin_name'
		);
		 
		 return $fields;
	}
	
	
	/**
	 * Create the metabox for construction location identification
	 */
	public static function output( $post ){
		
		$metafields_arr = self::get_fields();
	
	
		$meta_arr = ap_icas_get_post_meta( $post->ID, $metafields_arr );
			
		
		extract( $meta_arr );
	
		// get construction type
		$construction_type_tax_arr = wp_get_post_terms( $post->ID, 'construction_type' );
		
		
		
		// if not set yet, default 'trans'
		$ap_icas_construction_type = $construction_type_tax_arr ? $construction_type_tax_arr[0]->slug : '';
		
		
		// get construction type taxonomies
		$taxonomies_const_type_arr = get_terms( 'construction_type', array( 'hide_empty' => false , 'order' => 'ASC') );
		
		$options_tax = array();
		
		$options_tax[""] = __( "Selecteaza", "icas" );
		
		foreach ( $taxonomies_const_type_arr as $tax ){
			$options_tax[$tax->slug] = $tax->name;
		}
		
		
		
		// Construction type
		$construction_type_args = array(
				'type'	=>	'select',
				'id'	=> 'ap_icas_construction_type',
				'name'	=>	'ap_icas_construction_type',
				'value'	=>	$ap_icas_construction_type,
				'options'	=>	$options_tax,
				'label'	=>	__("Tip constructie" , 'icas') .": "
		);
		
		ap_icas_get_form_element( $construction_type_args );
		
		// Witness for initial type (long or trans). If this change, on save clean the data (metadata and terms) for the other type
		$construction_type_args = array(
				'type'	=>	'hidden',
				'id'	=> 'original_construction_type',
				'name'	=>	'original_construction_type',
				'value'	=>	$ap_icas_construction_type
		);
		
		ap_icas_get_form_element( $construction_type_args );
		
		
		
		// get area taxonomy for this construction
		$cod_bazin_tax = wp_get_post_terms( $post->ID, 'area' );
		// sorting the taxonomy
		if( $cod_bazin_tax ){
			$cod_bazin_tax = ap_icas_sort_taxonomy_hierarchy( $cod_bazin_tax );
		}
		
		$cod_bazin = array_fill( 0, ICAS_AREA_TAX_DEEP, '' );
		
		// level 0 is provided as term_id value, next levels as taxonomy name
		if( isset( $cod_bazin_tax[0] )  && isset( $cod_bazin_tax[0]->term_id ) ){
			$cod_bazin[0] = $cod_bazin_tax[0]->term_id;
		
			// if is set the 0 level, update the rest with the values
			for( $i = 1; $i < ICAS_AREA_TAX_DEEP; $i++ ){
		
				if( isset( $cod_bazin_tax[$i] )  && isset( $cod_bazin_tax[$i]->name )){
					$cod_bazin[$i] = $cod_bazin_tax[$i]->name;
				}else{
					$cod_bazin[$i] = '';
				}
			}
		}
		
		
		
		

	
		// Construction basin name
		$basin_name_args = array(
				'name'	=>	'ap_icas_basin_name',
				'value'	=>	$ap_icas_basin_name,
				'label'	=>	__("Denumire bazin" , 'icas') .": "
		);
		
		ap_icas_get_form_element( $basin_name_args );
		
		
		// Construction code
		$construction_code_args = array(
				'name'	=>	'ap_icas_construction_code',
				'value'	=>	$ap_icas_construction_code,
				'size'	=> 7,
				'label'	=>	__("Cod lucrare" , 'icas') .": "
		);
		
		ap_icas_get_form_element( $construction_code_args );
		
		
		
		// Cadastral code
		$area_args = array( 
				'parent' => 0, 
				'hide_empty' => false 			
		);
		$area_tax = get_terms( 'area', $area_args );
		wp_nonce_field( basename( __FILE__ ), 'icas_nonce' );
		echo "<p>".__("Cod cadastral", "icas");
		echo ": <select name='ap_icas_cod_bazin[]'>";
		echo "<option ".selected( $cod_bazin[0], "", false) . " value=''>".__('Selecteaza bazin', 'icas' ). '</option>';
		foreach ( $area_tax as $tax ){
			echo "<option value='$tax->term_id' ".selected( $cod_bazin[0], $tax->term_id, false) .">".esc_attr( $tax->name ) ."</option>";
		}
		echo "</select>";
		
		for( $i = 1; $i < ICAS_AREA_TAX_DEEP; $i++ ){
			echo ' <input name="ap_icas_cod_bazin[]" value="'.$cod_bazin[$i].'" size="2"> '; 
		}
		
		echo "</p>";
		
		
		
	
	
		
	
	}
	
	
	// Saving identification metabox values
	public static function save( $post_id, $post, $update ){
		
		// ====================================================
		//		Save construction_type
		// ====================================================
		
		if( isset( $_POST[ 'ap_icas_construction_type' ] ) ) {
			$construction_type = $_POST[ 'ap_icas_construction_type' ];
			wp_set_object_terms( $post_id, $construction_type , 'construction_type');
		}
		
		
		// ====================================================
		//		Save cod_bazin
		// ====================================================
		
		
		// Checks for input and sanitizes/saves if needed
		if( isset( $_POST[ 'ap_icas_cod_bazin' ] ) ) {
			// remove previous taxonomies 
			wp_set_object_terms( $post_id, null , 'area');
			
			// first taxonomy come from drop-down, as taxonomy term_id, but as string
			$area_terms_names = $_POST[ 'ap_icas_cod_bazin' ];
			$area_terms_names[0] = (int) $area_terms_names[0];
	
			
			$construction_taxonomies_arr = ap_icas_get_area_terms( $area_terms_names );
			
			$construction_tax_ids_arr = array();
			
			if( $construction_taxonomies_arr ){
				foreach ( $construction_taxonomies_arr as $t ){
					$construction_tax_ids_arr[] = $t->term_id;
				}
				wp_set_object_terms( $post_id, $construction_tax_ids_arr , 'area');
			}
				
		}
			
			// Metafields to update arr
			$metafields_arr = self::get_fields();
			
			foreach ( $metafields_arr as $metafield ){
				update_post_meta( $post_id, $metafield, strip_tags( $_POST[ $metafield ] ) );
			}
	}
}
