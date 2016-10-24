<?php


// Add 'area' taxonomy to Construction post type 
add_action( 'init', 'ap_icas_register_icas_taxonomies' ); 

function ap_icas_register_icas_taxonomies(){
	
	// ==================================================
	// Area
	// ==================================================
	$area_args = array(
			'hierarchical' => true,
			'query_var' => 'area',
			'show_tagcloud' => true,
			'show_in_menu' => false,
			'rewrite' => array(
					'slug' => 'area',
					'with_front' => false
			),
			'labels' => array(
					'name' => __('Areas', 'icas' ),
					'singular_name' => __('Area', 'icas' ),
					'edit_item' => __('Edit Area', 'icas' ),
					'update_item' => __('Update Area', 'icas' ),
					'add_new_item' => __('Add New Area', 'icas' ),
					'new_item_name' => __('New Area Name', 'icas' ),
					'all_items' => __('All Areas', 'icas' ),
					'search_items' => __('Search Areas', 'icas' ),
					'parent_item' => __('Parent Area', 'icas' ),
			'parent_item_colon' => __('Parent Area:', 'icas' ),
			),
	);
	
	register_taxonomy('area', array('construction'), $area_args );
	
	
	
	// ==================================================
	// Construction type
	// ==================================================
	$type_args = array(
			'hierarchical' => false,
			'query_var' => 'construction_type',
			'show_tagcloud' => false,
			'show_in_menu' => false,
			'rewrite' => array(
					'slug' => 'construction_type',
					'with_front' => false
			),
			'labels' => array(
					'name' => __('Construction Types', 'icas' ),
					'singular_name' => __('Construction Type', 'icas' ),
					'edit_item' => __('Edit Construction Type', 'icas' ),
					'update_item' => __('Update Construction Type', 'icas' ),
					'add_new_item' => __('Add New Construction Type', 'icas' ),
					'new_item_name' => __('New Construction Type Name', 'icas' ),
					'all_items' => __('All Construction Types', 'icas' ),
					'search_items' => __('Search Construction Types', 'icas' ),
					'parent_item' => __('Parent Construction Type', 'icas' ),
					'parent_item_colon' => __('Parent Construction Type:', 'icas' ),
			),
	);
	
	register_taxonomy('construction_type', array('construction'), $type_args );
	
	
	
	
	// ==================================================
	// Construction materials
	// ==================================================
	
	$mat_terms = ap_icas_get_material_taxonomies();
	
	foreach ( $mat_terms as $mat_tax => $mat_name ){
	
		$cons_material_args = array(
				'hierarchical' => false,
				'public' => true,
				'show_in_menu' => false,
				'label' => $mat_name
		);
	
		register_taxonomy( $mat_tax, array('construction', 'construction_sector'), $cons_material_args );
	}
	

	
	// ==================================================
	// Transversal constructions, dimensional disipatory types
	// ==================================================
	
	$disip_args = array(
			'hierarchical' => false,
			'query_var' => 'disipatory_type',
			'show_in_menu' => false,
			'show_tagcloud' => false,
			'labels' => array(
					'name' => __('Disipatory Types', 'icas' ),
					'singular_name' => __('Disipatory Type', 'icas' ),
					'edit_item' => __('Edit Disipatory Type', 'icas' ),
					'update_item' => __('Update Disipatory Type', 'icas' ),
					'add_new_item' => __('Add New Construction Type', 'icas' ),
					'new_item_name' => __('New Disipatory Type Name', 'icas' ),
					'all_items' => __('All Disipatory Types', 'icas' ),
					'search_items' => __('Search Disipatory Types', 'icas' ),
					'parent_item' => __('Parent Disipatory Type', 'icas' ),
					'parent_item_colon' => __('Parent Disipatory Type:', 'icas' ),
			),
	);
	
	register_taxonomy( 'trans_disip_type', array('construction'), $disip_args );
	
	
	// ==================================================
	// Transversal constructions type
	// ==================================================
	
	$trans_constr_type_args = array(
			'hierarchical' => false,
			'query_var' => 'trans_constr_type',
			'show_tagcloud' => false,
			'show_in_menu' => false,
			'labels' => array(
					'name' => __('Transversal Construction Types', 'icas' ),
					'singular_name' => __('Transversal Construction  Type', 'icas' ),
					'edit_item' => __('Edit Transversal Construction  Type', 'icas' ),
					'update_item' => __('Update Transversal Construction  Type', 'icas' ),
					'add_new_item' => __('Add New Transversal Construction  Type', 'icas' ),
					'new_item_name' => __('New Transversal Construction  Type Name', 'icas' ),
					'all_items' => __('All Transversal Constructions  Types', 'icas' ),
					'search_items' => __('Search Transversal Construction Types', 'icas' ),
					'parent_item' => __('Parent Transversal Construction Type', 'icas' ),
					'parent_item_colon' => __('Parent Transversal Construction Type:', 'icas' ),
			),
	);
	
	register_taxonomy( 'trans_constr_type', array('construction'), $trans_constr_type_args );

	
	// ==================================================
	// Location
	// ==================================================
	
	$type_args = array(
			'hierarchical' => true,
			'query_var' => 'icas_location',
			'sort' =>	true,
			'show_tagcloud' => false,
			'show_in_menu' => false,
			'labels' => array(
					'name' => __('Location Types', 'icas' ),
					'singular_name' => __('Location Type', 'icas' ),
					'edit_item' => __('Edit Location Type', 'icas' ),
					'update_item' => __('Update Location Type', 'icas' ),
					'add_new_item' => __('Add New Location Type', 'icas' ),
					'new_item_name' => __('New Location Type Name', 'icas' ),
					'all_items' => __('All Locations Types', 'icas' ),
					'search_items' => __('Search Location Types', 'icas' ),
					'parent_item' => __('Parent Location Type', 'icas' ),
					'parent_item_colon' => __('Parent Location Type:', 'icas' ),
			),
	);
	
	register_taxonomy('icas_location', array('construction'), $type_args );
	
	
	
	// ==================================================
	// Gal (granulometrie aluviuni)
	// ==================================================
	
	$gal_args = array(
			'hierarchical' => false,
			'public' => true,
			'show_in_menu' => false,
			'label' => __('Granulometrie aluviuni', 'icas' ),
	);
	
	register_taxonomy( 'trans_gal_type', array('construction'), $gal_args );
	
	
	
	// ==================================================
	// Authors tags
	// ==================================================
	$auth_args = array(
			'hierarchical' => false,
			'query_var' => 'abht_authors',
			'show_tagcloud' => false,
			'show_in_menu' => true,
			'sort' => true,
			'rewrite' => array(
					'slug' => 'abht_authors',
					'with_front' => false
			),
			'labels' => array(
					'name' => __('Autori', 'icas' ),
			),
	);
	
	register_taxonomy('abht_authors', array('post'), $auth_args );
	
}
