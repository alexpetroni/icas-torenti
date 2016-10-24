<?php




function ap_icas_get_include_content( $filename ){
	if( is_file( $filename) ){
		ob_start();
		include $filename;
		return ob_get_clean();
	}
	return false;
}


function ap_icas_remove_percent($s){
	$last_char = substr($s, -1);
	
	if($last_char == "%"){
		$s = substr($s, 0, -1);
	}
	
	return $s;
}





function ap_icas_add_longitudinals(){
	
	if( get_option( 'icas-activated' ) == 'j'){
		delete_option('icas-activated');
	}else{
		return;
	}
	
	$tranv_file_path =  ICAS_PLUGIN_DIR.'assets/longitudinale.csv';
	
	include ICAS_PLUGIN_DIR.'assets/map-longitudinale.php';
	
	if( file_exists( $tranv_file_path ) ){
		$f = fopen($tranv_file_path, 'r');
		
		
		
		if( $f !== false ){
			
						
	
		
			
			$mapping_number = count( $long_mapping );
			
			$sector = $pinten = -1; // $sector is the sector index, $pinten is the spur index
			
			
			$postarr = array(
					'post_title' => 'My New Post',
					'post_content' => '',
					'post_status' => 'publish',
					'post_date' => date('Y-m-d H:i:s'),
					'post_type' => 'construction',
			);
			
			while( $data = fgetcsv( $f ) ){				
				//error_log('data[0] '. print_r($data[0], 1));
				// insert only if it is a new construction record and reset the sector and spur index
				if( $sector >= 0 && ! empty( $data[0] ) ){
					wp_insert_post( $postarr );
					//error_log('Insert now'. print_r($_POST, 1));
					$sector = $pinten = -1;
					

					$_POST = array();
				}				
				
				
				$data = array_map("ap_icas_remove_percent", $data);
				
				// for safety, make it same length with the $long_mapping array
				$data = array_slice($data, 0, $mapping_number);
				
				// $long_mapping defined in map-transversale.php
				$mapped_data = array_combine( $long_mapping,  $data );
				
				
				/*
				error_log('$long_mapping '. print_r( count($long_mapping ), 1));
				error_log('$data '. print_r( count($data ), 1)); ap_icas_trans_apron_erosion_height
				error_log( print_r($mapped_data, 1) );
				*/
				
				//error_log( 'mapped data '. print_r($mapped_data, 1) );
				
				if( $sector == -1 ){					
					create_construction_from_data( $mapped_data );
				}
				

				
				if(! empty( $mapped_data['ap_icas_long_cons_sector'] ) ){
					$sector++;
					create_sector_from_data( $sector, $mapped_data );
					
				}
				

				if(! empty( $mapped_data['ap_icas_long_spur_spur_nr'] ) ){
					$pinten++;
					create_spur_for_sector_from_data( $pinten, $sector, $mapped_data );					
				}
				
				
				//error_log(print_r($_POST, 1));
			}
		}
		// after the last line is readed, insert it
		wp_insert_post( $postarr );
		
		fclose($f);
	}else{
		error_log("Nu fisier");
	}

}



function create_construction_from_data( $mapped_data ){
	$fields = array(
		'ap_icas_construction_review_date',
		'ap_icas_basin_name',
		'ap_icas_construction_code_0',
		'ap_icas_construction_code_1',

		'ap_icas_construction_city',
		'ap_icas_construction_owner', //20
		
		'ap_icas_construction_longitude_deg',
		'ap_icas_construction_longitude_min',
		'ap_icas_construction_longitude_sec',
		
		'ap_icas_construction_latitude_deg',
		'ap_icas_construction_latitude_min',
		'ap_icas_construction_latitude_sec',
		
		'ap_icas_construction_date',
		

			

		'ap_icas_construction_ys'
			
	);
	
	

	// prepare for taxonomies that are loaded by name, to provide the taxonomy id
	// area
	$areas_tax_arr = ap_icas_get_area_terms_as_options( 0, false, 'name', 'term_id', false );
		
	// location
	$location_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'icas_location', false, 'slug', 'term_id', false );
		
	// trans_constr_type
	$trans_constr_type_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'trans_constr_type', false, 'slug', 'term_id', false );
	
	
		
	$_POST['ap_icas_construction_type'] = 'long';
		
	$_POST['ap_icas_construction_latitude_hemis'] = 'N';
		
	$_POST['ap_icas_construction_longitude_hemis'] = 'E';
		
	$_POST['ap_icas_construction_protected_area'] = '';
		
	$_POST['ap_icas_construction_code'] = $mapped_data['ap_icas_construction_code_0'].'-'.$mapped_data['ap_icas_construction_code_1'];
		
	// convert first area to term_id
	$_POST['ap_icas_cod_bazin'][] = $areas_tax_arr[$mapped_data['ap_icas_cod_bazin_0']];
		
	for( $i = 1; $i <7; $i++ ){
		$_POST['ap_icas_cod_bazin'][$i] = $mapped_data['ap_icas_cod_bazin_'.$i];
	}
		
	// location
	$_POST['ap_icas_construction_county'] = $location_tax_arr[strtolower($mapped_data['ap_icas_construction_county'])];
		
	
	foreach ( $fields as $k => $val ){
		$_POST[$val] = $mapped_data[$val];
	}
}


function create_sector_from_data($sector_id, $data){
	
	// mat_sect_apron
	$mat_sect_apron_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'mat_sect_apron', false, 'slug', 'term_id', false );
		
		
	// mat_sect_walls
	$mat_sect_walls_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'mat_sect_walls', false, 'slug', 'term_id', false );
	
		
	// mat_sect_spur
	$mat_sect_spur_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'mat_sect_spur', false, 'slug', 'term_id', false );
		
	
	$sector_fields = array(
			'ap_icas_long_cons_sector',
			'ap_icas_long_cons_stairs',
			'ap_icas_long_cons_length',
			'ap_icas_long_cons_deep',
			'ap_icas_long_cons_width_apron', // latime radier
			'ap_icas_long_cons_fruit_guard_wall',
			'mat_sect_apron',
			'mat_sect_walls',
			'mat_sect_spur',
			
			
			'ap_icas_long_apron_craks_nr',
			'ap_icas_long_apron_damage_percent',
			'ap_icas_long_apron_displaced',
			'ap_icas_long_apron_abrasion_deep',
			'ap_icas_long_apron_abrasion_percent',
			
			
			'ap_icas_long_sidewall_left_horiz_craks_nr',
			'ap_icas_long_sidewall_left_horiz_length',
			'ap_icas_long_sidewall_left_vert_craks_nr',
			'ap_icas_long_sidewall_left_vert_length',
			'ap_icas_long_sidewall_right_horiz_craks_nr',
			'ap_icas_long_sidewall_right_horiz_length',
			'ap_icas_long_sidewall_right_vert_craks_nr',
			'ap_icas_long_sidewall_right_vert_length',
			'ap_icas_long_sidewall_left_displaced',
			'ap_icas_long_sidewall_right_displaced',
			'ap_icas_long_sidewall_left_abrasion_deep',
			'ap_icas_long_sidewall_left_abrasion_percent',
			'ap_icas_long_sidewall_right_abrasion_deep',
			'ap_icas_long_sidewall_right_abrasion_percent',
			
			'ap_icas_long_disfunctio_su',
			'ap_icas_long_disfunctio_srad',
			'ap_icas_long_disfunctio_sect_aval'
	);
	
	$_POST['sector_id'][] = 0;
	
	foreach ( $sector_fields as $k => $val ){
		$_POST[$val][$sector_id] = $data[$val];
		//error_log( print_r('sector $_POST['.$val.']['.$sector_id.']  '. $data[$val] , 1));
	}
	
	
	// mat_sect_apron
	$_POST['mat_sect_apron'][$sector_id] = isset( $mat_sect_apron_tax_arr[strtolower($data['mat_sect_apron'])] ) ?  $mat_sect_apron_tax_arr[strtolower($data['mat_sect_apron'])] : '';
	
	// mat_sect_walls
	$_POST['mat_sect_walls'][$sector_id] = isset( $mat_sect_walls_tax_arr[strtolower($data['mat_sect_walls'])] ) ?  $mat_sect_walls_tax_arr[strtolower($data['mat_sect_walls'])] : '';
	
	
	// mat_sect_spur
	$_POST['mat_sect_spur'][$sector_id] = isset( $mat_sect_spur_tax_arr[strtolower($data['mat_sect_spur'])] ) ?  $mat_sect_spur_tax_arr[strtolower($data['mat_sect_spur'])] : '';
	
}


function create_spur_for_sector_from_data($pinten, $sector, $data){
	
	$fields = array(		
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
	
	
	
	foreach ( $fields as $k => $val ){	
		$_POST[$val][$sector][] = $data[$val];
	}
	
}