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





function ap_icas_add_transversals(){
	
	if( get_option( 'icas-activated' ) == 'j'){
		delete_option('icas-activated');
	}else{
		return;
	}
	
	$tranv_file_path =  ICAS_PLUGIN_DIR.'assets/transversale.csv';
	
	include ICAS_PLUGIN_DIR.'assets/map-transversale.php';
	
	if( file_exists( $tranv_file_path ) ){
		$f = fopen($tranv_file_path, 'r');
		
		
		
		if( $f !== false ){
			
			
			// prepare for taxonomies that are loaded by name, to provide the taxonomy id
			// area
			$areas_tax_arr = ap_icas_get_area_terms_as_options( 0, false, 'name', 'term_id', false );
			
			// location
			$location_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'icas_location', false, 'slug', 'term_id', false );
			
			// trans_constr_type
			$trans_constr_type_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'trans_constr_type', false, 'slug', 'term_id', false );
			
			//trans_disip_type
			$trans_disip_type_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'trans_disip_type', false, 'slug', 'term_id', false );
			
			//trans_gal_type
			$trans_gal_type_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'trans_gal_type', false, 'slug', 'term_id', false );
							
			// mat_main_body
			$mat_main_body_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'mat_main_body', false, 'slug', 'term_id', false );
			
			
			// mat_wings
			$mat_wings_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'mat_wings', false, 'slug', 'term_id', false );
				
			
			// mat_apron
			$mat_apron_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'mat_apron', false, 'slug', 'term_id', false );
				
			
			// mat_counter_dam
			$mat_counter_dam_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'mat_counter_dam', false, 'slug', 'term_id', false );
				
			
			// mat_side_walls
			$mat_side_walls_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'mat_side_walls', false, 'slug', 'term_id', false );
				
			
			// mat_final_spur
			$mat_final_spur_tax_arr = ap_icas_get_taxonomy_terms_as_options( 'mat_final_spur', false, 'slug', 'term_id', false );
			
			
			$mapping_number = count( $trans_mapping );
			
			while( $data = fgetcsv( $f ) ){				
				
				$data = array_map("ap_icas_remove_percent", $data);
				
				// for safety, make it same length with the $trans_mapping array
				$data = array_slice($data, 0, $mapping_number);
				// $trans_mapping defined in map-transversale.php
				$mapped_data = array_combine( $trans_mapping,  $data );
				/*
				error_log('$trans_mapping '. print_r( count($trans_mapping ), 1));
				error_log('$data '. print_r( count($data ), 1)); ap_icas_trans_apron_erosion_height
				error_log( print_r($mapped_data, 1) );
				*/
				
				$postarr = array(
						'post_title' => 'My New Post',
						'post_content' => '',
						'post_status' => 'publish',
						'post_date' => date('Y-m-d H:i:s'),
						'post_type' => 'construction',
				);
				
				$_POST = $mapped_data;
				
				$_POST['ap_icas_construction_type'] = 'trans';
				
				$_POST['ap_icas_construction_latitude_hemis'] = 'N';
				
				$_POST['ap_icas_construction_longitude_hemis'] = 'E';
				
				$_POST['ap_icas_construction_protected_area'] = '';
				
				$_POST['ap_icas_construction_code'] = $_POST['ap_icas_construction_code_0'].'-'.$_POST['ap_icas_construction_code_1'];
				
				// convert first area to term_id
				$_POST['ap_icas_cod_bazin'][] = $areas_tax_arr[$_POST['ap_icas_cod_bazin_0']];
				
				for( $i = 1; $i <7; $i++ ){
					$_POST['ap_icas_cod_bazin'][$i] = $_POST['ap_icas_cod_bazin_'.$i];
				}
				
				// location 				
				$_POST['ap_icas_construction_county'] = $location_tax_arr[strtolower($_POST['ap_icas_construction_county'])];
				
				// trans_constr_type
				$_POST['trans_constr_type'] = $trans_constr_type_tax_arr[strtolower($_POST['trans_constr_type'])];
				
				// trans_disip_type
				$_POST['trans_disip_type'] = isset ( $trans_disip_type_tax_arr[strtolower($_POST['trans_disip_type'])] ) ? $trans_disip_type_tax_arr[strtolower($_POST['trans_disip_type'])]: $trans_disip_type_tax_arr['na'];
				
				// trans_gal_type
				$_POST['trans_gal_type'] = isset( $trans_gal_type_tax_arr[strtolower($_POST['trans_gal_type'])] ) ? $trans_gal_type_tax_arr[strtolower($_POST['trans_gal_type'])]: $trans_gal_type_tax_arr['na'];
				
				// mat_main_body
				$_POST['mat_main_body'] = isset( $mat_main_body_tax_arr[strtolower($_POST['mat_main_body'])] ) ?  $mat_main_body_tax_arr[strtolower($_POST['mat_main_body'])] : '';
				
				// mat_wings
				$_POST['mat_wings'] = isset( $mat_wings_tax_arr[strtolower($_POST['mat_wings'])] ) ? $mat_wings_tax_arr[strtolower($_POST['mat_wings'])] : '';

				//mat_apron
				$_POST['mat_apron'] = isset( $mat_apron_tax_arr[strtolower($_POST['mat_apron'])] ) ? $mat_apron_tax_arr[strtolower($_POST['mat_apron'])] : '' ;
				
				// mat_counter_dam
				$_POST['mat_counter_dam'] = isset( $mat_counter_dam_tax_arr[strtolower($_POST['mat_counter_dam'])] ) ? $mat_counter_dam_tax_arr[strtolower($_POST['mat_counter_dam'])]: $mat_counter_dam_tax_arr['na'];
				
				// mat_side_walls
				$_POST['mat_side_walls'] = isset( $mat_side_walls_tax_arr[strtolower($_POST['mat_side_walls'])] ) ? $mat_side_walls_tax_arr[strtolower($_POST['mat_side_walls'])] : '' ;
				
				//mat_final_spur
				$_POST['mat_final_spur'] = isset( $mat_final_spur_tax_arr[strtolower($_POST['mat_final_spur'])] ) ? $mat_final_spur_tax_arr[strtolower($_POST['mat_final_spur'])] : '' ;
				
				
				
				wp_insert_post($postarr);
				
				//error_log(print_r($_POST, 1));
			}
		}
		
		fclose($f);
	}else{
		error_log("Nu fisier");
	}

}