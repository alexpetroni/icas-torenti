<?php

function ap_log( $x = null , $message = '' ){
	
	error_log("\n".__FILE__ .( $message ? "\n\n # ".$message : '') ."\n\n". print_r( $x, 1)  ."\n\n\n\n");
}

/**
 * For a hierachical taxonomy which top level parent id is given (default 0) , sort the $tax_arr 
 * so that $sorted_tax_arr[0] is highest level, $sorted_tax_arr[1] is next level down etc
 * 
 * @param array $tax_arr the array given by wp_get_post_terms
 * @param int $top_parent_id 
 * @return array $sorted_tax_arr 
 */
function ap_icas_sort_taxonomy_hierarchy( $tax_arr, $top_parent_id = 0 ){
	
	$parent_term_id = $top_parent_id;
	
	$sorted_tax_arr = array();
	
	$parent_find = true;
	
	while( $parent_find ){
		$parent_find = false;
		for( $i = 0; $i < count( $tax_arr ); $i++ ){		
			if( $tax_arr[$i]->parent == $parent_term_id ){
				$parent_term_id = $tax_arr[$i]->term_id;
				$sorted_tax_arr[] = $tax_arr[$i];
				$parent_find = true;
				break;
			}
		}
	}	
	
	return $sorted_tax_arr;
}


/**
 * Format a fieldset title for icas frontend
 *
 * @param string $title
 * @param string $echo
 * @return string
 */
function ap_icas_fieldset_title( $title, $echo = true ){

	$title = '<div class="fieldset-title">'.$title.'</div>';

	if( ! $echo ){
		return  $title;
	}

	echo $title;
}


/**
 * Format a fieldset title for icas admin
 * 
 * @param string $title
 * @param string $echo
 * @return string
 */
function ap_icas_admin_fieldset_title( $title, $echo = true ){
	
	$title = '<div class="admin-fieldset-title">'.$title.'</div>';
	
	if( ! $echo ){
		return  $title;
	}
	
	echo $title;
}




/**
 * Get the name for the first term from the provided taxonomy for the given post.
 * If no post_id provided, for the current post
 * Get empty string if no term is provided or inexistent taxonomy
 * 
 * @param unknown $taxonomy
 * @param int $post_id
 * @return string
 */
function ap_icas_get_term_name( $taxonomy, $post_id = null ){
	global $post;
	
	if( $post_id == NULL ){
		$post_id = $post->ID;
	}

	$terms = wp_get_post_terms( $post_id, $taxonomy );
	
	$term_name = '&nbsp;';
	
	if( ! is_wp_error( $terms ) && $terms ){
		$term_name = $terms[0]->name;
	}
	
	return $term_name;
}


/**
 * Get the name for the first term from the provided taxonomy for the given post.
 * If no post_id provided, for the current post
 * Get empty string if no term is provided or inexistent taxonomy
 *
 * @param unknown $taxonomy
 * @param int $post_id
 * @return string
 */
function ap_icas_get_construction_areas( $post_id = null ){
	global $post;
	
	$taxonomy = 'area';
	
	$term_name = '&nbsp;';

	if( $post_id == NULL ){
		$post_id = $post->ID;
	}

	$terms = wp_get_post_terms( $post_id, $taxonomy );


	$parent_id = 0;
	$ordered_terms = array();
	
	if( ! is_wp_error( $terms ) && $terms ){
		for( $i = 0; $i < count( $terms ); $i++ ){
			foreach ( $terms as $t ){			
				if( $t->parent == $parent_id ){
					$ordered_terms[] = $t->name;
					$parent_id = $t->term_id;
				}
			}
		}
		
		
		$term_name = implode('-', $ordered_terms );
	}

	return $term_name;
}

/**
 * Format a result data for front-side
 * 
 * @param string $title
 * @param string $echo
 * @return string
 */
function ap_icas_data_field( $label, $val, $echo = true ){
	
	$v = is_array( $val ) ? $val[0] : $val;
	
	if( empty( $v ) ){
		$v = '&nbsp;';
	}
	
	$t	 = '<div class="icas-data-wrapper">';
	
	$t	.= '<span class="icas-data-label">'.$label.'</span>';
	$t	.= '<span class="icas-data-val">'. $v .'</span>';
	
	$t 	.= '</div>';

	if( ! $echo ){
		return  $t;
	}

	echo $t;
}

/**
 * For a given area taxonomies NAMES array, hierarchical ordered, will return an array with all the terms obj
 * If a term name is not present, it will create it
 * 
 * 
 * @param array $area_names_arr
 * @param int $parent_tax_id 
 * @return array
 */
function ap_icas_get_area_terms( $area_names_arr, $parent_tax_id = 0 ){
	$terms_arr = array();
	
	$terms_ids_arr = array();
		
	for( $i = 0; $i < ICAS_AREA_TAX_DEEP ; $i++ ){
		// if empty, quit
		if( ! isset( $area_names_arr[ $i ] ) || ! $area_names_arr[ $i ] ){
			break;
		}
		
		$tax_name = $area_names_arr[ $i ];
		
		if( ! is_int( $tax_name ) ){
			$tax_name = trim( $area_names_arr[ $i ] );
		}
	
		$tax_term_id = false;
		
		// if term does not exists, create it
		$term_exists = term_exists( $tax_name, 'area', $parent_tax_id );
	
		if( $term_exists !== 0 && $term_exists !== null ){
			$tax_term_id =  (int) $term_exists['term_id'];
		}else{
			$new_term_args = array( 'parent' => $parent_tax_id );
			$new_tax = wp_insert_term( $tax_name, 'area', $new_term_args );
			if( ! is_wp_error( $new_tax ) ){
				$tax_term_id = (int) $new_tax['term_id'];
			}
		}
	
		if( $tax_term_id ){
			$terms_ids_arr[] = $tax_term_id;
			$parent_tax_id = $tax_term_id;
		}else{
			break;
		}
	}
	
	
	if( $terms_ids_arr ){
		foreach ( $terms_ids_arr as $term_id ){
			$t = get_term( $term_id, 'area' );
			if( ! is_wp_error($t) && $t ){
				$terms_arr[] = $t;
			}
		}
	}
	
	return $terms_arr;
}



/**
 * @param number $parent_id
 * @param string $hide_empty
 * @param string $key
 * @param string $value
 * @param string $add_select
 * @return number|WP_Error|WP_Error|NULL[]
 */
function ap_icas_get_area_terms_as_options( $parent_id = 0, $hide_empty = true, $key = 'term_id', $value = 'name', $add_select = true ){

	$area_arr = get_terms('area' , array( 'hide_empty' => $hide_empty, 'parent' => (int) $parent_id ) );
	
	if( empty ( $area_arr ) ) return $area_arr;
	
	$options_arr = array();
	
	if( $add_select ){
		$options_arr[""] = __("Selecteaza", "icas");
	}
	
	foreach ( $area_arr as $a ){
		if( isset( $a->$key ) &&  isset( $a->$value ) ){
			$options_arr[$a->$key] = $a->$value;
		}else{
			return  new WP_Error(__("Non-existent term properties", "icas") );
		}
	}

	return $options_arr;
}



/**
 * Compose the area code for a given area term id
 * 
 * The result is something like II-3-a-c
 * 
 * @param int $term_id
 * @return string
 */
function ap_icas_get_area_code_for_leaf( $term_id ){
	$area_code = '';
	
	$area_codes_arr = array();
	
	$term = get_term_by('id', $term_id, 'area' );
	
	if( ! $term ){
		return $area_code;
	}
	
	array_push( $area_codes_arr, $term->name );
	
	$ancestors_arr = get_ancestors( $term_id, 'area' );

	if( is_array( $ancestors_arr ) && ! empty( $ancestors_arr ) ){		
		foreach ( $ancestors_arr as $parent_id ){
			$parent_term = get_term_by( 'id', $parent_id, 'area' );
			if( $parent_term ){
				array_unshift($area_codes_arr, $parent_term->name );
			}
		}
	}
	
	$area_code = implode('-', $area_codes_arr);
	
	return $area_code;
}

/**
 * Get the terms for for provided taxonomy
 * 
 * @param string $taxonomy
 * @param string $hide_empty
 * @return Array
 */
function ap_icas_get_taxonomy_terms( $taxonomy, $hide_empty = false ){
	
	$args = array( 'hide_empty' => $hide_empty, 'orderby' => 'id');
	
	$mat_arr = get_terms($taxonomy, $args );
	
	return $mat_arr;
}




/**
 * Get an array with terms specified properties as keys and values for specified parent slug 
 * Used for populating options for a select element in a form with terms for the provided taxonomy
 * 
 * @param string $taxonomy
 * @param string $hide_empty
 * @param boolean $key
 * @param string $val
 * @return Ambigous <multitype:, unknown>|WP_Error|multitype:NULL 
 */
function ap_icas_get_taxonomy_terms_as_options( $taxonomy, $hide_empty = false, $key = 'term_id', $value = 'name', $add_select = true ){

	$mat_arr = ap_icas_get_taxonomy_terms($taxonomy , $hide_empty);
	
	if( empty ( $mat_arr ) ) return $mat_arr;
	
	$options_arr = array();
	
	if( $add_select ){
		$options_arr[""] = __("Selecteaza", "icas");
	}
	
	foreach ( $mat_arr as $mat ){
		if( isset( $mat->$key ) &&  isset( $mat->$value ) ){
			$options_arr[$mat->$key] = $mat->$value;
		}else{
			return  new WP_Error(__("Non-existent term properties", "icas") );
		}
	}

	return $options_arr;
}


/**
 * Return the transversal construction materials taxonomies as $term => $taxonomies_names
 *
 * @return array
 */
function ap_icas_get_transversal_material_taxonomies(){
	return array(
			'mat_main_body'	=> __('Corp lucrare', 'icas'),
			'mat_wings'	=> __('Aripi lucrare', 'icas'),
			'mat_apron'	=> __('Radier', 'icas'),
			'mat_counter_dam'	=> __('Contrabaraj', 'icas'),
			'mat_side_walls'	=> __('Ziduri de conducere', 'icas'),
			'mat_final_spur'	=> __('Pinten terminal', 'icas')
	);
}

/**
 * Return the transversal construction materials taxonomies as $term => $taxonomies_names
 *
 * @return array
 */
function ap_icas_get_longitudinal_material_taxonomies(){
	return array(
			'mat_sect_apron'	=> __('Radier sector', 'icas'),
			'mat_sect_walls'	=> __('Ziduri garda', 'icas'),
			'mat_sect_spur'	=> __('Pinteni', 'icas')
	);
}


/**
 * Return the construction materials taxonomies as $term => $taxonomies_names
 * 
 * @return array
 */
function ap_icas_get_material_taxonomies(){
	return array_merge( ap_icas_get_transversal_material_taxonomies() , ap_icas_get_longitudinal_material_taxonomies() );
}


/**
 * Get the explanation for the abbreviations in material terms
 * 
 * @return multitype:string Ambigous <string, mixed> 
 */
function ap_icas_get_material_terms_code(){
	
	$terms_explanations = array(
			'B' => __("Beton, Beton ciclopian, Beton armat", "icas"),
			'M' => __("Zidarie de piatra cu mortar de ciment (beton placat cu zidarie)", "icas"),
			'GA' => __("Gabion (zidarie uscata in plasa de sarma)", "icas"),
			'CBG' => __("Contraforti din beton si grinzi din beton (armat)", "icas"),
			'CBGM' => __("Contraforti din beton si grinzi metalice", "icas"),
			'CBPB' => __("Contraforti din beton si placi din beton (armat)", "icas"),
			'CMG' => __("Contraforti din zidarie si grinzi din beton (armat)", "icas"),
			'CMPB' => __("Contraforti din zidarie si placi din beton (armat)", "icas"),
			'CMPM' => __("Contraforti din zidarie si placi din zidarie", "icas"),
			'CL' => __("Casoaie din lemn", "icas"),
			'L' => __("Lemn", "icas"),
			'ZU' => __("Zidarie uscata", "icas"),
			'ME' => __("Elemente metalice", "icas"),
			'PB' => __("Blocuri, casete din beton prefabricate", "icas"),
			'PP' => __("Placi prefabricate, placi tip L, fasii cu goluri", "icas"),
			'PT' => __("Tuburi prefabricate din beton", "icas"),
			'PG' => __("Grinzi din beton armat, grinzi tip I", "icas"),
			'PM' => __("Pamant", "icas"),
			'XX' => __("Alte materiale", "icas"),
			'NA' => __("Nu exista", "icas")
	);
	
	return $terms_explanations ;
}





/**
 * Get the explanation for the abbreviations in transversal constructions types
 * 
 * @return multitype:string 
 */
function ap_icas_get_transversaly_contruction_type_terms_code(){
	$type_explanation = array(
			 'AR' =>	'Baraje in arc',
			 'FI' =>	'Baraj filtrant',
			 'GFE' =>	'Baraj de greutate cu fundatie evazata',
			 'GR' =>	'Baraj de greutate clasice, gabioane, zidarie uscata, casoaie din lemn',
			 'GS' =>	'Baraje de greutate subdimensionate',
			 'P' =>		'Baraj din elemente prefabricate fara contraforti (blocuri, grinzi, hexapozi, grebla sine CF, cablu funicular, anvelope, redane etc)', 
			 'PC' =>	'Baraj din elemente prefabricate pe contraforti (grinzi, placi, contraforti si arce, in consola cu contraforti, fundatie evazata si placi in consola etc)',
			 'T' =>		'Baraj din tuburi'
	);
	
	return $type_explanation;
}

/**
 * Get a list with the disipatory types
 *
 * @param string $hide_empty
 * @param string $key
 * @param string $value
 * @param string $add_select
 * @return array
 */
function ap_icas_get_disip_type_list( $hide_empty = false, $key = 'term_id', $value = 'name', $add_select = true ){
	$taxonomy = "trans_disip_type";
	return ap_icas_get_type_list($taxonomy, $hide_empty, $key, $value, $add_select);
}



/**
 * Get a list with the transversal construction types
 *
 * @param string $hide_empty
 * @param string $key
 * @param string $value
 * @param string $add_select
 * @return array
 */
function ap_icas_get_trans_constr_type_list( $hide_empty = false, $key = 'term_id', $value = 'name', $add_select = true ){
	$taxonomy = "trans_constr_type";
	return ap_icas_get_type_list($taxonomy, $hide_empty, $key, $value, $add_select);
}


/**
 * Get a list with the granulometry deposits types
 *
 * @param string $hide_empty
 * @param string $key
 * @param string $value
 * @param string $add_select
 * @return array
 */
function ap_icas_get_trans_gal_type_list( $hide_empty = false, $key = 'term_id', $value = 'name', $add_select = true ){
	$taxonomy = "trans_gal_type";
	return ap_icas_get_type_list($taxonomy, $hide_empty, $key, $value, $add_select);
}

/**
 * Get a list with the terms for a specific taxonomy types
 *
 * @param string $hide_empty
 * @param string $key
 * @param string $value
 * @param string $add_select
 * @return array
 */
function ap_icas_get_type_list( $taxonomy,  $hide_empty = false, $key = 'term_id', $value = 'name', $add_select = true, $parent = 0, $orderby =  'id' ){

	$args = array( 'hide_empty' => $hide_empty, 'orderby' => $orderby, 'parent' => $parent );

	if( $add_select ){
		$type_arr[""] = __( "Selecteaza", "icas" );
	}

	$terms_arr = get_terms( $taxonomy, $args );

	foreach ( $terms_arr as $t ){
		$type_arr[$t->term_id] = $t->name;
	}

	return $type_arr;
}


/**
 * Get a list with the locations counties
 * 
 * @param string $hide_empty
 * @param string $key
 * @param string $value
 * @param string $add_select
 * @return array
 */
function ap_icas_get_county_list( $hide_empty = false, $key = 'term_id', $value = 'name', $add_select = true ){
	$taxonomy = "icas_location";
	return ap_icas_get_type_list($taxonomy, $hide_empty, $key, $value, $add_select);
}




/**
 * Get the array used in WP_Query for meta interogations if fields are set, otherwise false
 * By conventions the min and max values are the meta_name suffixed with _min and _max
 * 
 * $arr is the $_POST or $_GET array where this values are set
 * 
 * @param string $meta_name
 * @param array $arr
 * @param string $comparation_type
 * @return boolean|array 
 * 
 */
function ap_icas_get_meta_query_min_max_args($meta_name, $arr, $comparation_type = "numeric" ){
	$meta_args = array(
			'key'	=> $meta_name,
			'type'	=> $comparation_type
	);
	
	$min_field_name = $meta_name.'_min';
	$max_field_name = $meta_name.'_max';
	
	$min = isset( $arr[$min_field_name] ) ? trim( $arr[$min_field_name] ) : "";
	$max = isset( $arr[$max_field_name] ) ? trim( $arr[$max_field_name] ) : "";
	
	if( "" == $min  && "" == $max ) {
		return false;
	}
	
	if( "" !=  $min && "" != $max ){
	
		if( $min == $max ){
			$meta_args['value'] = (int) $max;
		}else{
			$range = array( (int) $min, (int) $max );
			asort( $range );
			$meta_args['value'] = $range;
			$meta_args['compare'] = 'BETWEEN';
		}
	
	}else{
		// if only min are set
		if( "" !=  $min ){
			$meta_args['value'] = (int) $min;
			$meta_args['compare'] = '>=';
		}
	
		// if only max are set
		if( "" !=  $max ){
			$meta_args['value'] = (int) $max;
			$meta_args['compare'] = '<=';
		}
	}
	
	return $meta_args;
}





/**
 * Get the array used in WP_Query for taxonomy interogations it fields are checked, otherwise false
 *
 * @param string $meta_name
 * @param array  $arr the $_POST or $_GET values frome which this 
 * @param string $min_field_name
 * @param string $max_field_name
 */
function ap_icas_get_tax_query_args( $tax_name, $arr, $form_field_name = "", $field = 'term_id' ){
	
	if( "" == $form_field_name ){
		$form_field_name = $tax_name;
	}
	
	$tax_args = array(
			'taxonomy' => $tax_name,
			'field'    => $field
	);

	if( empty( $arr[$form_field_name] ) ){
		return false;
	}


	$tax_args['terms'] = $arr[$form_field_name];
	
	return $tax_args;
}




/**
 * Compose the latitude from the meta_arr obtained from get_post_meta
 *
 * @param array $meta_arr
 * @param bool $echo
 * @return string
 */
function ap_icas_get_lat_long_script( $meta_arr, $print = true ){
	$script = '<script>';
	$latitude = $longitude = '';
	
	if( ! empty( $meta_arr['ap_icas_construction_latitude_deg'][0] ) ){
		//$latitude = $meta_arr['ap_icas_construction_latitude_deg'][0].'°'. $meta_arr['ap_icas_construction_latitude_min'][0]."'". $meta_arr['ap_icas_construction_latitude_sec'][0].'\"'. $meta_arr['ap_icas_construction_latitude_hemis'][0];
		$latitude = (int)$meta_arr['ap_icas_construction_latitude_deg'][0] + ( (int)$meta_arr['ap_icas_construction_latitude_min'][0] / 60 ) + ((float) $meta_arr['ap_icas_construction_latitude_sec'][0] / 3600) * ( strtoupper( $meta_arr['ap_icas_construction_latitude_hemis'][0] ) == 'N' ? 1 : -1 ) ;
	}
	if( ! empty( $meta_arr['ap_icas_construction_longitude_deg'][0] ) ){
		//$longitude = $meta_arr['ap_icas_construction_longitude_deg'][0].'°'. $meta_arr['ap_icas_construction_longitude_min'][0]."'". $meta_arr['ap_icas_construction_longitude_sec'][0].'\"'. $meta_arr['ap_icas_construction_longitude_hemis'][0];
		$longitude = (int)$meta_arr['ap_icas_construction_longitude_deg'][0] + ( (int)$meta_arr['ap_icas_construction_longitude_min'][0] / 60 ) + ((float) $meta_arr['ap_icas_construction_longitude_sec'][0] / 3600) * ( strtoupper( $meta_arr['ap_icas_construction_longitude_hemis'][0] ) == 'E' ? 1 : -1 ) ;
	}
	
	$script .= ' var cons_lat ='.$latitude.' ; ';
	$script .= ' var cons_long ='.$longitude.' ; ';
	
	//$script .= 'alert(' .$latitude." + ' ' +". $longitude.');';
	
	$script .= ' var cons_basin_name = "'.$meta_arr['ap_icas_basin_name'][0].'" ;';
	
	$script .= '</script>';
	
	if( ! $print ){
		return $script;
	}
	echo $script;
}

/**
 * Compose the latitude from the meta_arr obtained from get_post_meta
 * 
 * @param array $meta_arr
 * @param bool $echo
 * @return string
 */
function ap_icas_get_latitude_from_meta( $meta_arr, $print = true ){
	$latitude = $meta_arr['ap_icas_construction_latitude_deg'][0].'&deg;'. $meta_arr['ap_icas_construction_latitude_min'][0]."'". $meta_arr['ap_icas_construction_latitude_sec'][0].'" '. $meta_arr['ap_icas_construction_latitude_hemis'][0];
	if( ! $print ){
		return $latitude;
	}
	echo $latitude;
}


/**
 * Compose the longitude from the meta_arr obtained from get_post_meta
 *
 * @param array $meta_arr
 * @param bool $echo
 * @return string
 */
function ap_icas_get_longitude_from_meta( $meta_arr, $print = true ){
	$longitude = $meta_arr['ap_icas_construction_longitude_deg'][0].'&deg;'. $meta_arr['ap_icas_construction_longitude_min'][0]."'". $meta_arr['ap_icas_construction_longitude_sec'][0].'" '. $meta_arr['ap_icas_construction_longitude_hemis'][0];
	if( ! $print ){
		return $longitude;
	}
	echo $longitude;
}




/**
 * Get the SQL query for parents costructions ids for sectors that qualify for args selection
 * 
 * @param array $args
 * @return string
 */
function ap_icas_get_sectors_parents_ids_query( $args ){
	
	$query = '';

		$args['cache_results'] = false;
		$args['update_post_term_cache'] = false;
		$args['update_post_meta_cache'] = false;
		$args['posts_per_page'] = 1;
		$args['no_found_rows'] = true;
	
	add_filter( 'posts_fields',	'ap_icas_get_parent_id_fields' );
	$q = new WP_Query( $args );
	remove_filter( 'posts_fields',	'ap_icas_get_parent_id_fields');
	
	
	$query = $q->request;
	// remove any limitations
	if( strpos($query, 'ORDER BY') ){
		$query = substr( $query, 0, strpos($query, 'ORDER BY') );
	}
	
	if( strpos($query, 'LIMIT') ){
		$query = substr( $query, 0, strpos($query, 'LIMIT') );
	}
	
	wp_reset_postdata();
	
	return $query;
}


function ap_icas_get_parent_id_fields( $sql ){
	global $wpdb;
	return $sql = " DISTINCT $wpdb->posts.post_parent as parent_id ";
}




/**
 * Parse the $_SERVER['QUERY_STRING'] for construction selections and return an array with 
 * global => the_array_args_for_general_selection
 * longitudinal => the_array_args_for_longitudinal_sectors_selection
 * 
 * @param string $qs the $_SERVER['QUERY_STRING']
 * @param array	$query_args args to overwrite the defaults query args
 * 
 * @return array
 */
function ap_icas_get_constructions_query_args_from_str( $qs, $query_args = array() ){
	global $wp_query;
	
	$result = array(
			'general' => array(),
			'longitudinal' => array()
	);
	
	if( ! $qs ){
		return $result;
	}
	
	
	parse_str( $qs, $q );	
	
	$default_args =  array(
			'posts_per_page'	=> 50,
			'post_type'		=> 'construction',
			'post_status'	=> 'publish',
			'meta_query'	=> array()
	);
	
	$args = wp_parse_args( $query_args, $default_args );
	
	
	// Because longitudinal constructions are composite 'construction_sector' post_types, they have special treatment
	// On query on longitudinals should be returned results containing constructions that have at least one sector that meet the selection criterias
	$longitudinal_query = $transversal_query = false;
	
	// flag for area selection
	$area_id = null;
	
	// if is a longitudinal request, longitudinal sectors are stored separatly for creating sub-queries
	$long_sect_args = array();
	
	// pagination
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	
	if ( get_query_var( 'paged' ) ) { $paged = get_query_var( 'paged' ); }
	elseif ( get_query_var( 'page' ) ) { $paged = get_query_var( 'page' ); }
	else { $paged = 1; }
	
	$args['paged'] = $paged;
	
	// taxonomy relations and $long_tax_relation is for taxonomies specified for longitudinals construction
	$tax_relation = $long_tax_relation = 0;
	// meta relations and $long_meta_relation is for metafields specified for longitudinals construction
	$meta_relation = $long_meta_relation = 0;
	
	// construction type
	if( isset( $q['ap_icas_construction_type'] )  && ! empty( $q['ap_icas_construction_type'] )){
		$args['tax_query'][] = array(
				'taxonomy' => 'construction_type',
				'field'    => 'slug',
				'terms'    => $q['ap_icas_construction_type'],
		);
		$tax_relation++;
	}
	
	// county and city
	// if city not set, try the county
	if( isset( $q['ap_icas_construction_city'] )  && ! empty( $q['ap_icas_construction_city'] ) ){
		$args['tax_query'][] = array(
				'taxonomy' => 'icas_location',
				'field'    => 'term_id',
				'terms'    => $q['ap_icas_construction_city'],
		);
		$tax_relation++;
	}elseif ( isset( $q['ap_icas_construction_county'] )  && ! empty( $q['ap_icas_construction_county'] ) ){
		$args['tax_query'][] = array(
				'taxonomy' => 'icas_location',
				'field'    => 'term_id',
				'terms'    => $q['ap_icas_construction_county'],
		);
		$tax_relation++;
	}
	
	
	// Area
	if( isset( $q['ap_icas_cod_bazin'] ) ){
		$areas_arr = $q['ap_icas_cod_bazin'];
	
		// find the most specific area taxonomy, the last term non-empty
		for(  $i = 0; $i < count( $areas_arr ); $i++ ){
			if( $areas_arr[$i] ){
				$area_id = (int) $areas_arr[$i];
			}else{
				break;
			}
		}
	
		if( $area_id ){
			$args['tax_query'][] = array(
					'taxonomy' => 'area',
					'field'    => 'term_id',
					'terms'    => (int) $area_id,
			);
			$tax_relation++;
		}
	}
	
	
	
	// Selection using construction date & Ys  - indicele de stare metafields
	
	$general_meta_fields = array(
			'ap_icas_construction_date',
			'ap_icas_construction_ys'
	);
	
	// add meta query for Ys and construction_date
	foreach ( $general_meta_fields as $field ){
		$filter = ap_icas_get_meta_query_min_max_args( $field, $q );
		if( $filter ){
			$args['meta_query'][] = $filter;
			$meta_relation++;
		}
	}
	
	
	// =============================================================
	// 			transversals selection types
	// =============================================================
	
	if( $q['ap_icas_construction_type'] == 'trans' ){
	
		$trans_meta_fields = array(
				'ap_icas_trans_dim_ye',
				'ap_icas_trans_dim_h',
				'ap_icas_trans_dim_lr'
		);
	
		// add meta selecton query for Ye, H, Lr
		foreach ( $trans_meta_fields as $field ){
			$filter = ap_icas_get_meta_query_min_max_args( $field, $q );
				
			if( $filter ){
				$args['meta_query'][] = $filter;
				$meta_relation++;
			}
		}
	
	
	
		// add taxonomy selecton query for
		// construction type,  disip type, Gal type && transversal construction materials
	
		$trans_tax_fields = array(
				'trans_constr_type',
				'trans_disip_type',
				'trans_gal_type',
		);
		// adding transversal construction materials
		$trans_tax_fields = array_merge( $trans_tax_fields, array_keys( ap_icas_get_transversal_material_taxonomies() ) );
	
		foreach ( $trans_tax_fields as $trans_tax ){
			$filter = ap_icas_get_tax_query_args( $trans_tax, $q );
				
			if( $filter ){
				$args['tax_query'][] = $filter;
				$tax_relation++;
			}
		}
	
	
	}
	
	// =============================================================
	// 			longitudinal selection types
	// =============================================================
	if( $q['ap_icas_construction_type'] == 'long' ){
	
		$long_sect_args = array(
				'post_type'		=> 'construction_sector',
				'post_status'	=> 'publish',
				'no_found_rows'	=> true,
				'meta_query' => array(),
				'tax_query' => array()
		);
	
		$trans_meta_fields = array(
				'ap_icas_long_cons_length',
				'ap_icas_long_cons_deep',
				'ap_icas_long_dim_bs'
		);
	
		// add meta selection query for Ye, H, Lr
		foreach ( $trans_meta_fields as $field ){
			$filter = ap_icas_get_meta_query_min_max_args( $field, $q );
	
			if( $filter ){
				$long_sect_args['meta_query'][] = $filter;
				$long_meta_relation++;
			}
		}
	
		// add taxonomies selection for
		$long_tax_fields = array(
				'mat_sect_apron',
				'mat_sect_walls',
				'mat_sect_spur'
		);
	
		foreach ( $long_tax_fields as $long_tax ){
			$filter = ap_icas_get_tax_query_args( $long_tax, $q );
	
			if( $filter ){
				$long_sect_args['tax_query'][] = $filter;
				$long_tax_relation++;
			}
		}
	}
	
	
	// die(print_r($args, 1));
	
	// combine multiple taxonomies and metas for constructions
	if( $tax_relation > 1 ){
		$args['tax_query']['relation'] = 'AND';
	}
	
	if( $meta_relation > 1 ){
		$args['meta_query']['relation'] = 'AND';
	}
	
	// combine multiple taxonomies and metas for sectors from longitudinal constructions
	if( $long_tax_relation > 1 ){
		$long_sect_args['tax_query']['relation'] = 'AND';
	}
	
	if( $long_meta_relation > 1 ){
		$long_sect_args['meta_query']['relation'] = 'AND';
	}
	
	
	$result['general'] = $args;
	$result['longitudinal'] = $long_sect_args;
	
	return $result;
}




/**
 * Retrun the most specific area_id term taxonomy from construction selection or false if area not specified
 * 
 * @param strint $qs
 * @return boolean|number
 */
function ap_icas_get_selected_area_id_from_query_str( $qs ){
	if( ! $qs ){
		return false;
	}
	
	$area_id = false;
	
	parse_str( $qs, $q );
	
	if( empty( $q['ap_icas_cod_bazin'] ) ){
		return false;
	}
	
	$areas_arr = $q['ap_icas_cod_bazin'];
	
	// find the most specific area taxonomy, the last term non-empty
	for(  $i = 0; $i < count( $areas_arr ); $i++ ){
		if( $areas_arr[$i] ){
			$area_id = (int) $areas_arr[$i];
		}else{
			break;
		}
	}
	
	return $area_id;
}



/**
 * Get the SELECT fields names as alias for specified fields that are used in an INNER JOIN with meta_table specified as meta_field_name alias
 * For an array of metafields return a string in forma meta_table.field_name as field_name, ....
 * 
 * @param unknown $fields
 */
function ap_icas_sql_select_meta_fields_from_table( $fields ){
	$general_q = array();
	foreach ( $fields as $f ){
		$general_q[] = " meta_".$f.".meta_value as ".$f;
	}
	
	return join(', ', $general_q);
}

/**
 * Get the WHERE for specified fields that are used in an INNER JOIN with meta_table specified as meta_field_name alias
 * For an array of metafields return a string in forma meta_table.field_name as field_name, ....
 *
 * @param unknown $fields
 */
function ap_icas_sql_where_meta_fields_from_table( $fields ){
	$general_q = array();
	foreach ( $fields as $f ){
		$general_q[] = " AND  meta_".$f.".meta_key = '".$f."' ";
	}

	return join(' ', $general_q);
}


/**
 * Get the JOIN for specified fields that are used in an INNER JOIN with meta_table specified as meta_field_name alias
 * For an array of metafields return a string in forma meta_table.field_name as field_name, ....
 *
 * @param unknown $fields
 */
function ap_icas_sql_join_meta_fields_from_table( $fields ){
	$general_q = array();
	foreach ( $fields as $f ){
		$meta_table = "meta_".$f;
		$general_q[] = " INNER JOIN  wp_postmeta $meta_table ON ( wp_posts.ID = $meta_table.post_id ) ";
	}

	return join(' ', $general_q);
}



/**
 * Wrapper for ys calculation
 * 
 * @param array $args
 * @return number
 */
function ap_icas_calculate_ys( $args = array() ){
	if( empty($args) || ! in_array( $args['ap_icas_construction_type'] , array('long', 'trans') )   ){
		return -1;
	}
	
	if( 'trans' == $args['ap_icas_construction_type'] ){ // transversals
		
		$mat_term_na = get_term_by('slug', $args['mat_apron'],  'na' );		
		
		if( empty( $args['ap_icas_trans_dim_lr'] ) || ( $mat_term_na && $mat_term_na->id == $args['mat_apron'] ) ){
			return ap_icas_calculate_ys_for_trans_without_apron( $args ); // without apron
		}else{
			return ap_icas_calculate_ys_for_trans_with_apron( $args ); // with apron
		}
	}else{
		return ap_icas_calculate_ys_for_long( $args ); // longitudinals
	}
}


// ====================================================================
//				LUCRARI TRANSVERSALE FARA RADIER
// ====================================================================
function ap_icas_calculate_ys_for_trans_with_apron( $args ){
	
	$sum = 0;
	// ================================================================
	//			Lucrare propriu zisa
	// ================================================================
	
	// decastrare
	$ye = (int) $args['ap_icas_trans_dim_ye'];	
	$h = (int) $args['ap_icas_trans_dim_h'];
	
	if( $ye + $h == 0 ) return -1 ;
	
	$decas_ilim = 1;
	$decas_Ii = ( (int) $args['ap_icas_trans_damage_dec_left'] + (int) $args['ap_icas_trans_damage_dec_right'] ) / ( $ye + $h );

	
	$sum += .92 * min( $decas_Ii / $decas_ilim , 1 );
	
	// afuiere
	$afuiere_ilim = 2;
	$afuiere_Ii = (int) $args['ap_icas_trans_damage_af_height'] * (int) $args['ap_icas_trans_damage_af_percent'] / 100 ;

	$sum +=  2.52 * min( $afuiere_Ii / $afuiere_ilim , 1 );
	
	
	// fisurare	
	$lo = (int) $args['ap_icas_trans_damage_h_crak_dev_l'] + (int) $args['ap_icas_trans_damage_h_crak_undev_l'];
	$lv = (int) $args['ap_icas_trans_damage_v_crak_dev_l'] + (int) $args['ap_icas_trans_damage_v_crak_undev_l'];
	
	$b = (int) $args['ap_icas_trans_dim_b'];
	
	if( $b == 0 ){
		return -1;
	}
	
	$fisurare_ilim = 10;
	$fisurare_Ii = $lo/$b + $lv/ ($ye + $h );
	

	
	$sum += .74 * min( $fisurare_Ii / $fisurare_ilim , 1);	
	
	
	// desprindere zona deversata
	$desrp_zona_dev_ilim = 1;
	$desrp_zona_dev_Ii = (int) $args['ap_icas_trans_damage_detach_dev'] / 100;
	$sum += 1.90 * min( $desrp_zona_dev_Ii / $desrp_zona_dev_ilim , 1 );
	
	
	// desprindere aripi 
	$desrp_aripi_ilim = 1;
	$desrp_aripi_Ii = ( (int) $args['ap_icas_trans_damage_detach_undev_left'] + (int) $args['ap_icas_trans_damage_detach_undev_right'] ) / ( 2 * 100 ) ; // medium between the two
	$sum += 3.27 * min( $desrp_aripi_Ii / $desrp_aripi_ilim , 1 );
	
	
	// Eroziune
	$eroziune_ilim = 50;
	$eroziune_Ii = (int) $args['ap_icas_trans_damage_erosion_height'] * (int) $args['ap_icas_trans_damage_erosion_percent'] / 100 ;
	$sum += 0.82 * min( $eroziune_Ii/$eroziune_ilim, 1 );
	
	
	// ================================================================
	//			Radier
	// ================================================================
	
	// Fisurare
	$rad_fisurare_ilim = 5;
	$rad_fisurare_Ii = (int) $args['ap_icas_trans_apron_crack_nr'] * (int) $args['ap_icas_trans_apron_crack_percent'] / 100;
	$sum += 0.66 * min( $rad_fisurare_Ii / $rad_fisurare_ilim, 1 );
	
	
	// Desprindere
	$rad_despr_ilim = 1;
	$rad_despr_Ii = (int) $args['ap_icas_trans_apron_detach'];
	$sum += 1.33 * min( $rad_despr_Ii / $rad_despr_ilim , 1 );
	
	
	// Afuiere
	$rad_afuiere_ilim = 1;
	$rad_afuire_Ii = (int) $args['ap_icas_trans_apron_af_height'] * (int) $args['ap_icas_trans_apron_af_percent'] / 100;
	$sum += 0.24 * min( $rad_afuire_Ii / $rad_afuiere_ilim , 1 );

	
	// Eroziune
	$rad_eroziune_ilim = 50;
	$rad_eroziune_Ii = (int) $args['ap_icas_trans_damage_erosion_height'] * (int) $args['ap_icas_trans_apron_erosion_percent'] / 100 ;
	$sum += 0.52 * min( $rad_eroziune_Ii / $rad_eroziune_ilim , 1 );
	
	// ================================================================
	//			Sistem disipator
	// ================================================================
	
	// Desprindere dinti
	if( ! empty ( $args['ap_icas_trans_apron_teeth_total']) ){ // if has teeth
		$disip_ilim = 1;
		$disip_Ii = (int) $args['ap_icas_trans_apron_teeth_detach'] / (int) $args['ap_icas_trans_apron_teeth_total'];
		$sum += 0.03 * min( $disip_Ii / $disip_ilim , 1 );
	}
	// Desprindere contrabaraj
	$despr_contrabaraj_ilim = 1;
	$despr_contrabaraj_Ii = (int) $args['ap_icas_trans_apron_detach_counter_dam'] / 100;
	$sum += 0.10 * min( $despr_contrabaraj_Ii / $despr_contrabaraj_ilim , 1 );
	
	
	// ================================================================
	//			Ziduri de conducere
	// ================================================================
	
	// Fisurare	
	$Lr =  (int) $args['ap_icas_trans_dim_lr'];
	$zid_cond_fis_ilim = 5;
	$zid_cond_fis_Ii = ( (int) $args['ap_icas_trans_sidewall_left_horiz_length'] + (int) $args['ap_icas_trans_sidewall_right_horiz_length'] + (int) $args['ap_icas_trans_sidewall_left_vert_length'] + (int) $args['ap_icas_trans_sidewall_right_vert_length'] ) /(2 * $Lr) ;
	$sum += 0.31 * min( $zid_cond_fis_Ii / $zid_cond_fis_ilim, 1 );
	
	// Desprindere
	$zid_cond_despr_ilim = 1;
	$zid_cond_despr_Ii = ( (int) $args['ap_icas_trans_sidewall_right_displaced'] + (int) $args['ap_icas_trans_sidewall_left_displaced'] ) / ( 2 *100 );
	$sum += 1.0 * min( $zid_cond_despr_Ii / $zid_cond_despr_ilim , 1 );
	
	
	// Eroziune
	$zid_cond_eroz_ilim = 50;
	$zid_cond_eroz_Ii = ( (int) $args['ap_icas_trans_sidewall_left_abrasion_deep'] * (int) $args['ap_icas_trans_sidewall_left_abrasion_percent'] + (int) $args['ap_icas_trans_sidewall_right_abrasion_deep'] * (int) $args['ap_icas_trans_sidewall_right_abrasion_percent'] ) / ( 2 * 100 );
	$sum += 0.18 * min( $zid_cond_eroz_Ii / $zid_cond_eroz_ilim , 1 );
	
	
	// ================================================================
	//			Pinten terminal
	// ================================================================
	

	// Decastrare
	$Hz = max( 1, (int) $args['ap_icas_trans_dim_h'] );
	$pinten_decastr_ilim = 1;
	$pinten_decastr_Ii = ( (int) $args['ap_icas_trans_final_spur_decastr_left'] + (int) $args['ap_icas_trans_final_spur_decastr_right'] ) / $Hz ;
	$sum += 0.59 * min( $pinten_decastr_Ii / $pinten_decastr_ilim, 1 );
	

	// Fisurare
	$Bp = (int) $args['ap_icas_trans_dim_br'] + 4 * (int) $args['ap_icas_trans_dim_h'] ;
	$pinten_fis_ilim = 5;
	$pinten_fis_Ii = ( (int) $args['ap_icas_trans_final_spur_horiz_crack_length']  + (int) $args['ap_icas_trans_final_spur_vert_crack_length'] ) / $Bp;
	$sum += 0.45 * min( $pinten_fis_Ii / $pinten_fis_ilim, 1 );
	
	

	// Desprindere
	$pinten_despr_ilim = 1;
	$pinten_despr_Ii =  ( (int) $args['ap_icas_trans_final_spur_detach_left'] + (int) $args['ap_icas_trans_final_spur_detach_right']  + (int) $args['ap_icas_trans_final_spur_detach_center'] ) / ( 3 * 100 );
	
	$sum += 1.18 * min( $pinten_despr_Ii / $pinten_despr_ilim , 1 );
	
	// Eroziune
	$pinten_eroz_ilim = 50;
	$pinten_eroz_Ii = (int) $args['ap_icas_trans_final_spur_erosion_height'] * (int) $args['ap_icas_trans_final_spur_erosion_percent']  / 100 ;
	$sum += 0.38 * min( $pinten_eroz_Ii / $pinten_eroz_ilim , 1 );
	
	
	$YaREF = 31.32;
	
	error_log('$sum '. $sum);
	error_log('sqrt( $sum ) '.sqrt( $sum ));
	error_log('( 1000 / $YaREF ) * sqrt( $sum ) '. (( 1000 / $YaREF ) * sqrt( $sum )));
	
	$ys = 100 - ( 1000 / $YaREF ) * sqrt( $sum );
	
	return 'cu radier : '. $ys;
}

// ====================================================================
//				LUCRARI TRANSVERSALE FARA RADIER
// ====================================================================
function ap_icas_calculate_ys_for_trans_without_apron( $args ){	
	
	$sum = 0;
	// ================================================================
	//			Lucrare propriu zisa
	// ================================================================
	
	// decastrare
	$ye = (int) $args['ap_icas_trans_dim_ye'];	
	$h = (int) $args['ap_icas_trans_dim_h'];
	
	if( $ye + $h == 0 ) return -1 ;
	
	$decas_ilim = 1;
	$decas_Ii = ( (int) $args['ap_icas_trans_damage_dec_left'] + (int) $args['ap_icas_trans_damage_dec_right'] ) / ( $ye + $h );
	
	
	$sum += 2.41 * min( $decas_Ii / $decas_ilim , 1 );
	
	// afuiere
	$afuiere_ilim = 2;
	$afuiere_Ii = (int) $args['ap_icas_trans_damage_af_height'] * (int) $args['ap_icas_trans_damage_af_percent'] / 100 ;	
	$sum +=  1.79 * min( $afuiere_Ii / $afuiere_ilim , 1 );
	
	
	// fisurare	
	$lo = (int) $args['ap_icas_trans_damage_h_crak_dev_l'] + (int) $args['ap_icas_trans_damage_h_crak_undev_l'];
	$lv = (int) $args['ap_icas_trans_damage_v_crak_dev_l'] + (int) $args['ap_icas_trans_damage_v_crak_undev_l'];
	
	$b = (int) $args['ap_icas_trans_dim_b'];
	
	if( $b == 0 ){
		return -1;
	}
	
	$fisurare_ilim = 5;
	$fisurare_Ii = ($lo / $b) + $lv / ($ye + $h );
	
	$sum += 2.15 * min( $fisurare_Ii / $fisurare_ilim , 1);	
	
	
	// desprindere zona deversata
	$desrp_zona_dev_ilim = 1;
	$desrp_zona_dev_Ii = (int) $args['ap_icas_trans_damage_detach_dev'] / 100;
	$sum += 5.23 * min( $desrp_zona_dev_Ii / $desrp_zona_dev_ilim , 1 );
	
	
	// desprindere aripi 
	$desrp_aripi_ilim = 1;
	$desrp_aripi_Ii = ( (int) $args['ap_icas_trans_damage_detach_undev_left'] + (int) $args['ap_icas_trans_damage_detach_undev_right'] ) / ( 2 * 100 ) ; // medium between the two
	$sum += 9.66 * min( $desrp_aripi_Ii / $desrp_aripi_ilim , 1 );
	
	
	// Eroziune
	$eroziune_ilim = 50;
	$eroziune_Ii = (int) $args['ap_icas_trans_damage_erosion_height'] * ((int) $args['ap_icas_trans_damage_erosion_percent'] /100 );
	$sum += 1.92 *  min( $eroziune_Ii / $eroziune_ilim, 1 );
/* 	error_log('$eroziune_Ii ' . $eroziune_Ii);
	
	error_log('$eroziune_Ii / $eroziune_ilim ' . $eroziune_Ii / $eroziune_ilim);
	error_log('min( $eroziune_Ii / $eroziune_ilim, 1 ) ' . min( $eroziune_Ii / $eroziune_ilim, 1) ); */
	
	$YaREF = 38.59;
	
	$ys = 100 - ( 1000 / $YaREF ) * sqrt( $sum );
	
	return 'fara radier : '. $ys;
	
}


function ap_icas_calculate_ys_for_long( $args ){
		
	$sum = 0;
	
	$total_length = 0;
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		$total_length += (int) $args['ap_icas_long_cons_length'];
	}
	
	// if we cannot calculate total length, return -1
	if( ! $total_length ){
		return -1;
	}
	
	// ================================================================
	//			Radier
	// ================================================================
	
	// fisurare
	
	$ponder_sum = 0;
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		$ponder_sum += (int) $val * (int) $args['ap_icas_long_apron_craks_nr'][$key] * (int) $args['ap_icas_long_apron_damage_percent'][$key] / 100;
	}
	
	$rad_fis_ilim = 5;
	$rad_fis_Ii = $ponder_sum / $total_length;
	
	$sum += .93 * min( $rad_fis_Ii/ $rad_fis_ilim , 1 );
	
	
	// desprindere
	
	$ponder_sum = 0;
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		$ponder_sum += (int) $val * (int) $args['ap_icas_long_apron_displaced'][$key] / 100;
	}
	
	$rad_despr_ilim = 1;
	$rad_despr_Ii = $ponder_sum / $total_length;
	
	$sum += 3.67 * min( $rad_despr_Ii/ $rad_despr_ilim , 1 );
	
	// eroziune
	
	$ponder_sum = 0;
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		$ponder_sum += (int) $val * (int) $args['ap_icas_long_apron_displaced'][$key] / 100;
	}
	
	$rad_despr_ilim = 1;
	$rad_despr_Ii = $ponder_sum / $total_length;
	
	$sum += 3.67 * min( $rad_despr_Ii/ $rad_despr_ilim , 1 );
	
	// ================================================================
	//			Ziduri de conducere	
	// ================================================================
	
	// fisurare
	
	$lo = 0;
	$lv = 0;
	$lr = 0;
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		$lo += (int) $args['ap_icas_long_sidewall_left_horiz_length'][$key] + (int) $args['ap_icas_long_sidewall_right_horiz_length'][$key] ;
		$lv += (int) $args['ap_icas_long_sidewall_left_vert_length'][$key] + (int) $args['ap_icas_long_sidewall_right_vert_length'][$key] ;
		$lr += (int) $args['ap_icas_long_cons_length'][$key];
	}
	
	$zid_fis_ilim = 5;
	$zid_fis_Ii = $lo + $lv / $lr;
	
	$sum += .25 * min( $zid_fis_Ii / $zid_fis_ilim , 1 );
	
	
	
	// desprindere
	
	$ponder_sum = 0;
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		$ponder_sum += (int) $val * ( (int) $args['ap_icas_long_sidewall_left_displaced'][$key] + (int) $args['ap_icas_long_sidewall_right_displaced'][$key] ) / ( 2 * 100 );
	}
	
	$rad_despr_ilim = 1;
	$rad_despr_Ii = $ponder_sum / $total_length;
	
	$sum += 1.66 * min( $rad_despr_Ii/ $rad_despr_ilim , 1 );
	
	
	
	// eroziune

	$ponder_sum = 0;
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		$ponder_sum += (int) $val * ( (int) $args['ap_icas_long_sidewall_left_abrasion_deep'][$key] * (int) $args['ap_icas_long_sidewall_left_abrasion_percent'][$key] + (int) $args['ap_icas_long_sidewall_right_abrasion_deep'][$key] * (int) $args['ap_icas_long_sidewall_right_abrasion_percent'][$key] ) / ( 2 * 100 );
	}
	
	$rad_despr_ilim = 50;
	$rad_despr_Ii = $ponder_sum / $total_length;
	
	$sum += .81 * min( $rad_despr_Ii/ $rad_despr_ilim , 1 );
	
	// ================================================================
	//			Pinteni
	// ================================================================
	
	// find Hz, the max deep from all sectors
	$Hz_arr = array();
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		$Hz_arr[] = (int) $args['ap_icas_long_cons_deep'][$key];
	}
	
	$Hz = max( $Hz_arr );
	
	// decastrare
	
	$Ast = $Adr = 0;
	$total_pinteni = 0;
	
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		foreach ( $args['ap_icas_long_spur_spur_nr'][$key] as $spur_key => $spur_val ) { 
			
			if( $spur_val ) { // do not count unmarked spurs
				$Ast += (int) $args['ap_icas_long_spur_decastr_left'][$key][$spur_key] ;
				$Adr += (int) $args['ap_icas_long_spur_decastr_right'][$key][$spur_key] ;
				$total_pinteni++ ;
			}
		}
	}
	
	if( $total_pinteni ){
		$pinteni_decastr_ilim = 1;
		$pinteni_despr_Ii = ( $Ast + $Adr ) / ( $total_pinteni * $Hz );
		
		$sum += .14 * min( $pinteni_despr_Ii / $pinteni_decastr_ilim , 1 );
	}
	

	// fisurare
	$Lo = $Lv = 0;
	$pinteni_despr_Ii = $total_pinteni = 0;
	
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		foreach ( $args['ap_icas_long_spur_spur_nr'][$key] as $spur_key => $spur_val ) {
				
			if( $spur_val ) { // do not count unmarked spurs
				$Lo += (int) $args['ap_icas_long_spur_horiz_lenght'][$key][$spur_key] ;
				$Lv += (int) $args['ap_icas_long_spur_vert_lenght'][$key][$spur_key] ;
				$total_pinteni++ ;
			}
		}
	}
	
	$pinteni_decastr_ilim = 5;
	if( $total_pinteni ){
		$pinteni_despr_Ii = ( $Lo + $Lv ) / ( $total_pinteni * $Hz );
	}
	
	$sum += .45 * min( $pinteni_despr_Ii / $pinteni_decastr_ilim , 1 );
	
	
	
	// afuiere
	
	$afuiere = 0;
	$pinteni_afuiere_Ii = $total_pinteni = 0;
	
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		foreach ( $args['ap_icas_long_spur_spur_nr'][$key] as $spur_key => $spur_val ) {
	
			if( $spur_val ) { // do not count unmarked spurs
				$afuiere += (int) $args['ap_icas_long_spur_afuieri_height'][$key][$spur_key] * (int) $args['ap_icas_long_spur_afuieri_percent'][$key][$spur_key] / 100;
				$total_pinteni ++ ;
			}
		}
	}
	
	$pinteni_afuiere_ilim = 1;
	if( $total_pinteni ){
		$pinteni_afuiere_Ii = $afuiere / $total_pinteni ;
	}
	
	$sum += .35 * min( $pinteni_afuiere_Ii / $pinteni_afuiere_ilim , 1 );
	
	
	
	// desprindere aripi
	
	$pinteni_desprindere_aripi_Ii = 0;
	
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		foreach ( $args['ap_icas_long_spur_spur_nr'][$key] as $spur_key => $spur_val ) {
	
			if( $spur_val ) { // do not count unmarked spurs
				$pinteni_desprindere_aripi_Ii += ( (int) $args['ap_icas_long_spur_displaced_left'][$key][$spur_key] * (int) $args['ap_icas_long_spur_displaced_right'][$key][$spur_key] ) / ( 2 * 100 );
			}
		}
	}
	
	$pinteni_desprindere_aripi_ilim = 1;
	
	$sum += .69 * min( $pinteni_desprindere_aripi_Ii / $pinteni_desprindere_aripi_ilim , 1 );
	
	
	// desprindere zona centrala
	
	$pinteni_desprindere_centrala_Ii = 0;
	
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		foreach ( $args['ap_icas_long_spur_spur_nr'][$key] as $spur_key => $spur_val ) {
	
			if( $spur_val ) { // do not count unmarked spurs
				$pinteni_desprindere_centrala_Ii +=  (int) $args['ap_icas_long_spur_displaced_center'][$key][$spur_key] / 100 ;
			}
		}
	}
	
	$pinteni_desprindere_centrala_ilim = 1;
	
	$sum += 1.02 * min( $pinteni_desprindere_centrala_Ii / $pinteni_desprindere_centrala_ilim , 1 );
	
	// eroziune
	
	$pinteni_eroziune_Ii = $pinteni_sum_eroz = 0;
	$pinteni = 0;
	
	foreach ( $args['ap_icas_long_cons_length'] as $key => $val ){
		foreach ( $args['ap_icas_long_spur_spur_nr'][$key] as $spur_key => $spur_val ) {
	
			if( $spur_val ) { // do not count unmarked spurs
				$pinteni_sum_eroz +=  (int) $args['ap_icas_long_spur_abrasion_deep'][$key][$spur_key] * (int) $args['ap_icas_long_spur_abrasion_percent'][$key][$spur_key] / 100 ;
				$pinteni++;
			}
		}
	}
	
	if( $pinteni ){
		$pinteni_eroziune_Ii = $pinteni_sum_eroz / $pinteni;
	}
	
	$pinteni_eroziune_ilim = 50;
	
	$sum += .55 * min( $pinteni_eroziune_Ii  / $pinteni_eroziune_ilim , 1 );
	
	
	
	
	$YaREF = 27.62;
	
	$ys = 100 - ( 1000 / $YaREF ) * sqrt( $sum );
	
	return 'longitudinal : '. $ys;
	
}

