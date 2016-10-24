<?php
add_shortcode( 'icas-search', 'ap_icas_search_form' );

function ap_icas_search_form(){
	echo '<form action="/rezultate-selectie" method="GET">';
	
	extract( $_GET );
	
	
	// =====================================
	// Construction type
	// =====================================
	echo '<div class="area_group row">';
	
	echo '<div class="icas-field small-12 large-4 columns">';
	
	$construction_type_tax_arr = get_terms( 'construction_type' );
	
	$options_tax = array("" => __( "Selecteaza", "icas" ) );
	
	foreach ( $construction_type_tax_arr as $c ){
		$options_tax[ $c->slug ] = $c->name;
	}
	
	$construction_type_args = array(
			'type'	=>	'select',
			'id'	=> 'ap_icas_construction_type',
			'name'	=>	'ap_icas_construction_type',
			'value'	=>	isset( $ap_icas_construction_type ) ? $ap_icas_construction_type : '',
			'options'	=>	$options_tax,			
			'label'	=>	__("Tip constructie" , 'icas') .": "
	);
	
	ap_icas_get_form_element( $construction_type_args );
	
	echo '</div>';
	
	// =====================================
	// Execution Year
	// =====================================
	
	echo '<div class="icas-field small-12 large-8 columns">';
	echo '<div class="">'.__("An executie", "icas").':</div>';
	echo '<div class="row">';
	

	
	$years_arr = array();
	$current_year = (int) date("Y");
	for( $i = 0; $i < 100; $i++ ){
		$y = $current_year - $i;
		$years_arr[$y] = $y;
	}
	
	echo '<div class="icas-field small-12 large-6 columns">';
	
	$construction_date_start_args = array(
			'type'	=>	'select',
			'name'	=>	'ap_icas_construction_date_min',
			'options'	=>	 (array("" => __("Intre anul" , 'icas')) + $years_arr),
			'value'	=>	isset( $ap_icas_construction_date_min ) ? 	$ap_icas_construction_date_min : ''
	);
	ap_icas_get_form_element( $construction_date_start_args );
	
	echo '</div>';
	
	echo '<div class="icas-field small-12 large-6 columns">';
	
	$construction_date_end_args = array(
			'type'	=>	'select',
			'name'	=>	'ap_icas_construction_date_max',
			'options'	=>	(array("" => __("Si anul" , 'icas')) + $years_arr) ,
			'value'	=>	isset( $ap_icas_construction_date_max ) ? $ap_icas_construction_date_max : ''
	
	);
	ap_icas_get_form_element( $construction_date_end_args );
	echo '</div>';
	
	echo '</div></div></div>';
	
	
	echo '<div class="area_group row">';
	// =====================================
	// Location
	// ===================================== 
	// Construction county
	$county_args = array(
			'type'	=>	'select',
			'name'	=>	'ap_icas_construction_county',
			'id'	=>	'ap_icas_construction_county',
			'options'	=> ap_icas_get_county_list( true ),
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'value'	=>	isset( $ap_icas_construction_county ) ? $ap_icas_construction_county : '',
			'label'	=>	__("Judet" , 'icas') .": "
	);
	
	ap_icas_get_form_element( $county_args );
	
	$cities_options = array();
	
	if( isset( $ap_icas_construction_county ) && $ap_icas_construction_county ){
		$cities_terms = get_terms( "icas_location", array( 'parent' => (int) $ap_icas_construction_county ) );
		
		if( ! is_wp_error( $cities_terms ) && $cities_terms ){
			$cities_options[""] = __( "Selecteaza", "icas" );
		}
		
		foreach ( $cities_terms as $c ){
			$cities_options[ $c->term_id ] = $c->name;
		}
	}
	
	// Construction city
	$city_args = array(
			'type' 	=>  'select',
			'name'	=>	'ap_icas_construction_city',
			'id'	=>	'ap_icas_construction_city',
			'value'	=>	isset( $ap_icas_construction_city ) ? esc_attr ( $ap_icas_construction_city ) : '',
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'options'	=> isset( $cities_options ) ? $cities_options : '', 
			'label'	=>	__("Localitate" , 'icas') .": "
	);
	
	ap_icas_get_form_element( $city_args );
	echo '</div>'; // .row
	
	// =====================================
	// Area
	// =====================================	
	
	
	echo '<div class="area_group row">';
	echo '<div class="small-12 columns"><div class="">'.__("Cod cadastral", "icas").':</div></div>';
	
	$parent_area = 0;
	
	for( $i = 0; $i < ICAS_AREA_TAX_DEEP - 1 ; $i++ ){
		
		$area_options =  isset( $parent_area ) ? ap_icas_get_area_terms_as_options( $parent_area ) : array() ;
		
		$selected_val = isset( $ap_icas_cod_bazin[$i] ) ? esc_attr ( $ap_icas_cod_bazin[$i] ) : '';
		
		$select_args = array(
			'type' 	=>  'select',
			'class' =>	'area_select',
			'size'	=> 	1,
			'before'	=>	'<div class="icas-field small-12 large-2 medium-2 columns">',
			'name'	=>	'ap_icas_cod_bazin[]',
			'value'	=>	isset( $selected_val ) ? $selected_val : '',
			'options'	=> $area_options
			);

		ap_icas_get_form_element( $select_args );

		
		$parent_area = $selected_val ? $selected_val : null;
	}
	
	echo '</div>';
	

	// ======================================================
	//					Ys 
	// ======================================================
	
	echo '<div class="area_group row">';
	echo '<div class="small-12 columns"><div class="">'.__("Indicele de stare (Ys)", "icas").':</div></div>';
	
	
	
	$ys_min_args = array(
			'name'	=>	'ap_icas_construction_ys_min',
			'before'	=>	'<div class="icas-field small-12 large-3 medium-3 columns">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_construction_ys_min ) ? $ap_icas_construction_ys_min : '',
			'label'	=>	__("Min" , 'icas')
	
	);
	ap_icas_get_form_element( $ys_min_args );
	
	$ys_max_args = array(
			'name'	=>	'ap_icas_construction_ys_max',
			'before'	=>	'<div class="icas-field small-12 large-3 medium-3 columns end">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_construction_ys_max ) ? $ap_icas_construction_ys_max : '',
			'label'	=>	__("Max" , 'icas')
	
	);
	ap_icas_get_form_element( $ys_max_args );
	
	
	echo '</div>';
	
	// ======================================================
	//					TRANSVERSALS
	// ======================================================
 
	echo '<div id="transversals">';
	
	echo '<div class="area_group row" id="transv_constr_dimensions">';
	
	echo '<div class="small-12 large-4 columns">';
	
	// Ye	
	echo '<div class="fields_title">'.__("Înălțime elevație - Ye (m) ", "icas").'</div>';
	
	echo '<div class="row">';	
	
	
	$ye_min_args = array(
			'name'	=>	'ap_icas_trans_dim_ye_min',
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_trans_dim_ye_min ) ? $ap_icas_trans_dim_ye_min : '',
			'label'	=>	__("Min" , 'icas')
	
	);
	ap_icas_get_form_element( $ye_min_args );
	
	$ye_max_args = array(
			'name'	=>	'ap_icas_trans_dim_ye_max',
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_trans_dim_ye_max ) ? $ap_icas_trans_dim_ye_max : '',
			'label'	=>	__("Max" , 'icas')
	
	);
	ap_icas_get_form_element( $ye_max_args );
	
	echo '</div>';
	
	echo '</div>'; // large-4
	
	echo '<div class="small-12 large-4 columns">';
	
	// H		
	echo '<div class="fields_title">'.__("Sarcină în deversor - H (m)", "icas").'</div>';
	
	echo '<div class="row">';
	
	
	$h_min_args = array(
			'name'	=>	'ap_icas_trans_dim_h_min',
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_trans_dim_h_min ) ? $ap_icas_trans_dim_h_min : '' ,
			'label'	=>	__("Min" , 'icas')
	
	);
	ap_icas_get_form_element( $h_min_args );
	
	$h_max_args = array(
			'name'	=>	'ap_icas_trans_dim_h_max',
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_trans_dim_h_max ) ? $ap_icas_trans_dim_h_max: '',
			'label'	=>	__("Max" , 'icas')
	
	);
	ap_icas_get_form_element( $h_max_args );
	
	echo '</div>';
	
	echo '</div>'; // large-4
	
	echo '<div class="small-12 large-4 columns">';
	
	// Lr		
	echo '<div class="fields_title">'.__("Lungime radier - Lr (m)", "icas").'</div>';
	
	echo '<div class="row">';
	
	
	$lr_min_args = array(
			'name'	=>	'ap_icas_trans_dim_lr_min',
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_trans_dim_lr_min ) ? $ap_icas_trans_dim_lr_min : '',
			'label'	=>	__("Min" , 'icas')
	
	);
	ap_icas_get_form_element( $lr_min_args );
	
	$lr_max_args = array(
			'name'	=>	'ap_icas_trans_dim_lr_max',
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_trans_dim_lr_max ) ? $ap_icas_trans_dim_lr_max : '',
			'label'	=>	__("Max" , 'icas')
	
	);
	ap_icas_get_form_element( $lr_max_args );
	
	echo '</div>';
	
	echo '</div>'; // large-4
	
	echo '</div>'; // row
	
	
	// -----------------------------------------
	//			Accordion
	// -----------------------------------------
	
	echo '<div class="area_group row">';
	
	echo '<div class="small-12 columns">';
	
	echo '<ul class="accordion" data-accordion>';
	echo '<li class="accordion-item is-active" data-accordion-item>';
	echo '<a href="#" class="accordion-title">'.__("Tip lucrare", "icas").'</a>';
	echo ' <div class="accordion-content" data-tab-content>';
	// construction type
	echo '<div class="area_group row">';
	
	// echo '<div class="small-12 columns"><div class="fields_title">'.__("Tip lucrare", "icas").':</div></div>';
	
	$constructions_codes = ap_icas_get_transversaly_contruction_type_terms_code();
	
	$constr_type_tax_arr = ap_icas_get_trans_constr_type_list(true, $key = 'term_id', $value = 'name', $add_select = false );
	
	foreach ( $constr_type_tax_arr as $key => $val ){
		$c_type_ck_args = array(
				'type'	=> 'checkbox',
				'name'	=>	'trans_constr_type[]',
				'before'	=>	'<div class="icas-field small-4 large-2 end columns">',
				'value'	=>	$key,
				'label_tooltip' => isset( $constructions_codes[$val] ) ? $constructions_codes[$val] : '',
				'label'	=>	esc_attr($val)
		
		);
		
		if( ! empty( $_GET['trans_constr_type'] ) && in_array( $key, $_GET['trans_constr_type'] ) ){
			$c_type_ck_args['attr'] = ' checked="checked" ';
		} 
		ap_icas_get_form_element( $c_type_ck_args );
	}
	
	echo '</div>';
	
	echo '</div>';
	echo '</li>';
	


	
	// disip type
	echo '<li class="accordion-item" data-accordion-item>';
	echo '<a href="#" class="accordion-title">'.__("Tip disipator", "icas").'</a>';
	echo '<div class="accordion-content" data-tab-content>';
	
	echo '<div class="area_group row">';
	
	$constr_type_tax_arr = ap_icas_get_disip_type_list(true, $key = 'term_id', $value = 'name', $add_select = false );
	
	foreach ( $constr_type_tax_arr as $key => $val ){
		$d_type_ck_args = array(
				'type'	=> 'checkbox',
				'name'	=>	'trans_disip_type[]',
				'before'	=>	'<div class="icas-field small-4 large-3 medium-2 end columns">',
				'value'	=>	$key,
				'label'	=>	esc_attr($val)
	
		);
		
		if( ! empty( $_GET['trans_disip_type'] ) && in_array( $key, $_GET['trans_disip_type'] ) ){
			$d_type_ck_args['attr'] = ' checked="checked" ';
		}
		
		ap_icas_get_form_element( $d_type_ck_args );
	}
	
	echo '</div>';
	echo '</div>';
	echo '</li>';
	
	
	//=================================================
	//			Construction materials
	//=================================================
	echo '<li class="accordion-item" data-accordion-item>';
	echo '<a href="#" class="accordion-title">'.__("Materiale de constructie", "icas").'</a>';
	echo '<div class="accordion-content" data-tab-content>';
	
	$trans_materials_tax_arr = ap_icas_get_transversal_material_taxonomies();
	
	$materials_codes = ap_icas_get_material_terms_code();
	
	foreach ( $trans_materials_tax_arr as $key => $val ){
		echo '<div class="fields_title">'.$val.':</div>';
		echo '<div class="area_group row">';
		$terms_list = ap_icas_get_taxonomy_terms_as_options( $key, true, 'term_id', 'name', false );
		
		
		foreach ( $terms_list as $k => $v ){
			$m_type_ck_args = array(
					'type'	=> 'checkbox',
					'name'	=>	$key.'[]',
					'before'	=>	'<div class="icas-field small-4 large-2 end columns">',
					'value'	=>	$k,
					'label_tooltip' => isset( $materials_codes[$v] ) ? $materials_codes[$v] : '',
					'label'	=>	esc_attr($v)
		
			);
		
			if( ! empty( $_GET[$key] ) && in_array( $k, $_GET[$key] ) ){
				$m_type_ck_args['attr'] = ' checked="checked" ';
			}
		
			ap_icas_get_form_element( $m_type_ck_args );
		}
		
		
		echo '</div>';
	}
	echo '</div>';
	echo '</li>';
	
	
	// Gal type

	echo '<li class="accordion-item" data-accordion-item>';
	echo '<a href="#" class="accordion-title">'.__("Granulometrie aluviuni", "icas").'</a>';
	echo '<div class="accordion-content" data-tab-content>';
	
	echo '<div class="area_group row">';
	
	$gal_type_tax_arr = ap_icas_get_trans_gal_type_list(true, $key = 'term_id', $value = 'name', $add_select = false );
	
	foreach ( $gal_type_tax_arr as $key => $val ){
		$d_type_ck_args = array(
				'type'	=> 'checkbox',
				'name'	=>	'trans_gal_type[]',
				'before'	=>	'<div class="icas-field small-4 large-3 medium-2 end columns">',
				'value'	=>	$key,
				'label'	=>	esc_attr($val)
	
		);
	
		if( ! empty( $_GET['trans_gal_type'] ) && in_array( $key, $_GET['trans_gal_type'] ) ){
			$d_type_ck_args['attr'] = ' checked="checked" ';
		}
	
		ap_icas_get_form_element( $d_type_ck_args );
	}
	
	echo '</div>';
	echo '</div>';
	echo '</li>';

	
	echo '</ul>';
	
	echo '</div>'; // small-12
	
	echo '</div>'; // row
	
	
	echo '</div>'; // #transversals
	

	
	
	
	
	
	// ======================================================
	//					LONGITUDINALS
	// ======================================================
	
	echo '<div id="longitudinals">';
	
	
	
	echo '<div class="area_group row" id="long_constr_dimensions">';
	
	echo '<div class="small-12 large-4 columns">';
	
	// Ye
	echo '<div class="fields_title">'.__("Lungime - Ls (m) ", "icas").'</div>';
	
	echo '<div class="row">';
	
	
	$ls_min_args = array(
			'name'	=>	'ap_icas_long_cons_length_min',
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_long_cons_length_min ) ? $ap_icas_long_cons_length_min : '',
			'label'	=>	__("Min" , 'icas')
	
	);
	ap_icas_get_form_element( $ls_min_args );
	
	$ls_max_args = array(
			'name'	=>	'ap_icas_long_cons_length_max',
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_long_cons_length_max ) ? $ap_icas_long_cons_length_max : '',
			'label'	=>	__("Max" , 'icas')
	
	);
	ap_icas_get_form_element( $ls_max_args );
	
	echo '</div>';
	
	echo '</div>'; // large-4
	
	echo '<div class="small-12 large-4 columns">';
	
	// Hs
	echo '<div class="fields_title">'.__("Adâncime - Hs (m)", "icas").'</div>';
	
	echo '<div class="row">';
	
	
	$hs_min_args = array(
			'name'	=>	'ap_icas_long_cons_deep_min',
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_long_cons_deep_min ) ? $ap_icas_long_cons_deep_min : '',
			'label'	=>	__("Min" , 'icas')
	
	);
	ap_icas_get_form_element( $hs_min_args );
	
	$hs_max_args = array(
			'name'	=>	'ap_icas_long_cons_deep_max',
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_long_cons_deep_max ) ? $ap_icas_long_cons_deep_max : '',
			'label'	=>	__("Max" , 'icas')
	
	);
	ap_icas_get_form_element( $hs_max_args );
	
	echo '</div>';
	
	echo '</div>'; // large-4
	
	echo '<div class="small-12 large-4 columns">';
	
	// Lr
	echo '<div class="fields_title">'.__("Lăţime radier - bs (m)", "icas").'</div>';
	
	echo '<div class="row">';
	
	
	$bs_min_args = array(
			'name'	=>	'ap_icas_long_cons_width_apron_min',
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_long_cons_width_apron_min ) ? $ap_icas_long_cons_width_apron_min : '',
			'label'	=>	__("Min" , 'icas')
	
	);
	ap_icas_get_form_element( $bs_min_args );
	
	$bs_max_args = array(
			'name'	=>	'ap_icas_long_cons_width_apron_max',
			'before'	=>	'<div class="icas-field small-12 large-6 medium-6 columns">',
			'size'	=> 3,
			'value'	=>	isset( $ap_icas_long_cons_width_apron_max ) ? $ap_icas_long_cons_width_apron_min : '',
			'label'	=>	__("Max" , 'icas')
	
	);
	ap_icas_get_form_element( $bs_max_args );
	
	echo '</div>';
	
	echo '</div>'; // large-4
	
	echo '</div>'; // row
	
	

	//=================================================
	//			Construction materials
	//=================================================
	
	// -----------------------------------------
	//			Accordion
	// -----------------------------------------
	echo '<div class="area_group row">';
	
	echo '<div class="small-12 columns">';
	
	echo '<ul class="accordion" data-accordion>'; 
	echo '<li class="accordion-item" data-accordion-item>';
	echo '<a href="#" class="accordion-title">'.__("Materiale de constructie", "icas").'</a>';
	echo '<div class="accordion-content" data-tab-content>';
	
	$long_materials_tax_arr = ap_icas_get_longitudinal_material_taxonomies();
	
	$materials_codes = ap_icas_get_material_terms_code();
	
	foreach ( $long_materials_tax_arr as $key => $val ){
		echo '<div class="fields_title">'.$val.':</div>';
		echo '<div class="area_group row">';
		$terms_list = ap_icas_get_taxonomy_terms_as_options( $key, true, 'term_id', 'name', false );
	
	
		foreach ( $terms_list as $k => $v ){
			$m_type_ck_args = array(
					'type'	=> 'checkbox',
					'name'	=>	$key.'[]',
					'before'	=>	'<div class="icas-field small-4 large-2 end columns">',
					'value'	=>	$k,
					'label_tooltip' => isset( $materials_codes[$v] ) ? $materials_codes[$v] : '',
					'label'	=>	esc_attr($v)
	
			);
	
			if( ! empty( $_GET[$key] ) && in_array( $k, $_GET[$key] ) ){
				$m_type_ck_args['attr'] = ' checked="checked" ';
			}
	
			ap_icas_get_form_element( $m_type_ck_args );
		}
	
	
		echo '</div>';
	}
	echo '</div>';
	echo '</li>';
	
	echo '</ul>'; // end accordion
	
	echo '</div>'; // small-12
	echo '</div>'; //  row
	echo '</div>';
	
	echo '<div class="submit_btn_container">';
	echo '<input type="submit" name="ap_icas_main_search" id="ap_icas_main_search" value="Submit" class="button">';
	echo '</div>';

	echo '</form>';
}