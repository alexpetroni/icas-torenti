<?php

ap_icas_register_icas_taxonomies();
ap_icas_register_construction_post_type();

// insert construction_type

if( ! term_exists( __("Transversal", "icas"), 'construction_type' ) ){
	$ins = wp_insert_term( __("Transversal", "icas"), 'construction_type', array( 'slug' => 'trans' ) );
}

if( ! term_exists(  __("Longitudinal", "icas"), 'construction_type' ) ){
	wp_insert_term( __("Longitudinal", "icas"), 'construction_type', array( 'slug' => 'long' ) );
}



// insert main areas
if( ! term_exists( __("I", "icas"), 'area' ) ){
	$base_area = array(
			'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII', 'XIII', 'XIV'
	);
	
	foreach ( $base_area as $a ){
		wp_insert_term( $a, 'area');
	}
}


// insert construction materials
if( ! term_exists( __("B", "icas"), 'mat_main_body' ) ){
	
	$mat_tax_arr = ap_icas_get_material_taxonomies();	
	
	
	$mat_arr = ap_icas_get_material_terms_code();
	
	$cons_materials = array(
			'mat_main_body' => array( 
					'B',
					'M',
					'GA',
					'CBG',
					'CBGM',
					'CBPB',
					'CMG',
					'CMPB',
					'CMPM',
					'CL',
					'L',
					'ZU',
					'ME',
					'PB',
					'PP',
					'PT',
					'PG',
					'PM',
					'XX'
					),
			'mat_wings' => array(
					'B',
					'M',
					'GA',
					'CBG',
					'CBGM',
					'CBPB',
					'CMG',
					'CMPB',
					'CMPM',
					'CL',
					'L',
					'ZU',
					'ME',
					'PB',
					'PP',
					'PT',
					'PG',
					'PM',
					'XX'
			),
			'mat_apron' => array(
					'B',
					'M',
					'GA',					
					'ZU',
					'PP',
					'PG',
					'XX',
					'NA'
			),
			'mat_counter_dam' => array(
					'B',
					'M',
					'GA',					
					'XX',
					'NA'
			),
			'mat_side_walls' => array(
					'B',
					'M',
					'GA',
					'ZU',
					'PB',
					'PP',
					'PT',
					'PG',
					'PM',
					'XX',
					'NA'
			),
			'mat_final_spur' => array(
					'B',
					'M',
					'GA',
					'PP',				
					'XX',
					'NA'
			),
			'mat_sect_apron' => array(
					'B',
					'M',
					'ZU',
					'PP',
					'PM',
					'XX'
			),
			'mat_sect_walls' => array(
					'B',
					'M',
					'ZU',
					'PP',
					'XX'
			),
			'mat_sect_spur' => array(
					'B',
					'M',
					'ZU',
					'PP',
					'XX',
					'NA'
			),
			
	);
	
	foreach ( $mat_tax_arr as $tax_slug => $tax_name ){
		if( isset( $cons_materials[ $tax_slug ] ) ){
			$terms_arr = $cons_materials[ $tax_slug ];
		}else{
			break;
		}
		
		foreach ( $terms_arr as  $term_name ){		
			wp_insert_term( $term_name, $tax_slug, array( 'description' => $mat_arr[$term_name] ) );
		}
		
	}
}


// insert disip types
if( ! term_exists( __("Fara", "icas"), 'trans_disip_type' ) ){
	$disip_types = array(
		'n' =>	__("Fara", "icas"),
		'd' =>	__("Placa disipatoare", "icas"),
		's' =>	__("Bazin disipator", "icas"),
		'na' =>	__("N/A", "icas")
	);

	foreach ( $disip_types as $k => $a ){
		wp_insert_term( $a, 'trans_disip_type', array( 'slug' => $k ) );
	}
}


// insert trans construction type
if( ! term_exists( __("AR", "icas"), 'trans_constr_type' ) ){
	$trans_cons_types = array(
			__("AR", "icas"),
			__("FI", "icas"),
			__("GFE", "icas"),
			__("GR", "icas"),
			__("GS", "icas"),
			__("P", "icas"),
			__("PC", "icas"),
			__("T", "icas"),
			__("XX", "icas")
	);

	foreach ( $trans_cons_types as $a ){
		wp_insert_term( $a, 'trans_constr_type');
	}
}

// insert locations
if( ! term_exists( __("Alba", "icas"), 'icas_location' ) ){
		
	include ICAS_PLUGIN_DIR.'assets/judete.php';
			
	foreach ( $judete as  $code => $name ){
		wp_insert_term( $name, 'icas_location', array( 'slug' => $code ) );
	}		
}

// insert gal_type (Gal)
if( ! term_exists( __("Fine", "icas"), 'trans_gal_type' ) ){

	$gal_arr = array(
			'f' => __("Fine", "icas"),
			'm' => __("Medii", "icas"),
			'g' => __("Grosiere", "icas"),
			'na' => __("N/A", "icas"),
	);
		
	foreach ( $gal_arr as  $code => $name ){
		wp_insert_term( $name, 'trans_gal_type', array( 'slug' => $code ) );
	}
}


