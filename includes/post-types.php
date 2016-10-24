<?php

// Construction post type
add_action( 'init', 'ap_icas_register_construction_post_type' );

function ap_icas_register_construction_post_type(){
	

	
	$construction_args = array(
			'public' => true,
			'query_var' => 'construction',	
			'hierarchical' => false,
			'supports' => array(
					'title', 
					'author',
					'editor',
					'custom-fields',
					// 'page-attributes',
					'gallery',
					'thumbnail'
				),
			'taxonomies' => array('area'),
			'menu_position' => 3,
			'labels' => array(
					'name' => __('Lucrari hidrotehnice', 'icas' ),
					'singular_name' =>__('Lucrare hidrotehnica', 'icas' ),
					'add_new' => __('Adauga lucrare hidrotehnica', 'icas' ),
					'add_new_item' => __('Adauga lucrare hidrotehnica', 'icas' ),
					'edit_item' => __('Editeaza lucrare', 'icas' ),
					'new_item' => __('Lucrare noua', 'icas' ),
					'view_item' => __('Vizualizeaza lucrare', 'icas' ),
					'search_items' => __('Cauta lucrari', 'icas' ),
					'not_found' => __('Nu exista lucrare', 'icas' ),
					'not_found_in_trash' => __('No Constructions Found In Trash', 'icas' )
			),
	);
	
	
	$sector_args = array(
			'public' => true,
			'query_var' => 'construction_sector',
			'hierarchical' => true,
			'labels' => array(
					'name' => __('Sectoare hidrotehnice', 'icas' )
					)
	);
	
	register_post_type( 'construction', $construction_args );
	register_post_type( 'construction_sector', $sector_args );
}	