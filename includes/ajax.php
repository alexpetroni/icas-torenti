<?php

include_once 'queries/class-ys-histo-wp-query.php';
include_once 'queries/class-ys-years-histo-wp-query.php';
include_once 'queries/class-ys-decade-histo-wp-query.php';
include_once 'queries/class-ys-ye-wp-query.php';
include_once 'queries/class-ys-area-wp-query.php';
include_once 'queries/class-ys-long-mat-constr-wp-query.php';
include_once 'queries/class-ys-trans-mat-constr-wp-query.php';
include_once 'queries/class-granulometries-wp-query.php';

// Get cities for a given county

add_action( 'wp_ajax_nopriv_ap_icas_get_county_cities', 'ap_icas_ajax_get_county_cities');
add_action( 'wp_ajax_ap_icas_get_county_cities', 'ap_icas_ajax_get_county_cities');

function ap_icas_ajax_get_county_cities(){
	$county_id = (int) $_REQUEST['county_id'];
	
	$cities_terms = get_terms( 'icas_location', array('parent' => $county_id ) );
	
	$cities = array();
	
	if( $cities_terms ){
		foreach ( $cities_terms as $c ){
			$cities[] = array( 'id' => $c->term_id, 'name' => $c->name );
		}
	}
	
	wp_send_json($cities);
	die();
}


// Get children areas for a provided parent area_id
add_action( 'wp_ajax_nopriv_ap_icas_get_area_children', 'ap_icas_ajax_get_area_children');
add_action( 'wp_ajax_ap_icas_get_area_children', 'ap_icas_ajax_get_area_children');


function ap_icas_ajax_get_area_children(){
	$parent_area_id = (int) $_POST['parent_id'];
	
	$area_terms = get_terms('area', array( 'parent' => $parent_area_id, 'hide_empty' => true ) );
	
	$areas = array();
	
	if( $area_terms ){
		foreach ( $area_terms as $a ){
			$areas[] = array( 'id' => $a->term_id, 'name' => $a->name );
		}
	}
	
	wp_send_json($areas);
	die();
}



// ==================================================================================================
//									Graphics
// ==================================================================================================

add_action( 'wp_ajax_nopriv_ap_icas_get_graphics_data', 'ap_icas_get_graphics_data');
add_action( 'wp_ajax_ap_icas_get_graphics_data', 'ap_icas_get_graphics_data');


/**
 * Main entrance for graphics data
 */
function ap_icas_get_graphics_data(){
	
	
	// error_log('selection_query_args'. print_r($_POST['selection_query_args'], 1));
	$tab = $_POST['tab'];
	
	switch( $tab ){
		case 'ys_segment_distribution':
			ap_icas_ys_distribution();
			break;
		
		case 'ys_years_distribution':
			ap_icas_ys_years_distribution();
			break;
		
			
		case 'ys_decade_distribution' :
			ap_icas_ys_decade_distribution ();
			break;
			
		case 'ys_area_distribution':
			ap_icas_ys_area_distribution();
			break;
			
		case 'ys_ye_distribution' :
			ap_icas_ys_ye_distribution ();
			break;
			
		case 'ys_trans_material_construction_distribution' :
			ys_trans_material_construction_distribution ();
			break;
			
		case 'ys_long_material_construction_distribution' :
			ys_long_material_construction_distribution ();
			break;
				
		case 'granulometry_distribution':
			ap_icas_granulometry_distribution();
			break;
	}	
}




/**
 * Return ys grouped by 20-40-60-80-100 interval for the specific selection
 */
function ap_icas_ys_distribution(){
	global $wpdb;
	
	$sectors_selection_query_args = array();
	
	$selection_query_args = ap_icas_parse_query_args( $_POST['selection_query_args'], false );
	$selection_query_args['posts_per_page'] = -1;
	
	if( ! empty( $_POST['sectors_selection_query_args'] ) ){
		$sectors_selection_query_args =  $_POST['sectors_selection_query_args'] ;
	}
	
	$q = new Ys_Histo_WP_Query( $selection_query_args,  $sectors_selection_query_args );
	
	// error_log('Ys_Histo_WP_Query '. print_r($q, 1));
	
	$results = array();
	
	if( $q->have_posts() ){
		while( $q->have_posts() ){
			$q->the_post();
			if( -10 != $q->post->ys_segment ){ // exclude the constructions with obvious errors in Ys calculations, the negative ones
				$results[$q->post->ys_segment] = array(
						'ys_segment' => $q->post->ys_segment,	
						'avg_ys'	=> $q->post->avg_ys,
						'n'		=> $q->post->n					
				);
			}
		}
	}
	
	// autocomplete the results with missing ys ranges, to preserve colors and labels
	if( ! empty( $results ) && count( $results ) < 5 ){
		$seg = array("20", "40", "60", "80", "100");
		foreach ( $seg as $s ){
			if( !isset( $results[$s] ) ){
				$results[$s] = array(
						'ys_segment' => $s,
						'avg_ys'	=> 0,
						'n'		=> 0
				);
			}
		}
		
		ksort( $results );
		
		$results = array_values( $results );
	}
	
	
	
	wp_reset_postdata();
	wp_send_json( $results );
	die();
}



/**
 * Return the Ys distribution by years
 * 
 */
function ap_icas_ys_years_distribution(){	
	global $wpdb;
	
	$sectors_selection_query_args = array();
	
	$selection_query_args = ap_icas_parse_query_args( $_POST['selection_query_args'], false );
	$selection_query_args['posts_per_page'] = -1;
	
	if( ! empty( $_POST['sectors_selection_query_args'] ) ){
		$sectors_selection_query_args = $_POST['sectors_selection_query_args'];
	}	
	
	$q = new Ys_Years_Histo_WP_Query( $selection_query_args,  $sectors_selection_query_args );
	
	$results = array();
	
	if( $q->have_posts() ){
		
		// find the first year and last year, to have a continuous years range
		$first_year	= (int) $q->posts[0]->year;		
		$last_year	= (int) $q->posts[$q->found_posts - 1]->year;
		
		
		$constructions_sparse_arry = array();
		
		while( $q->have_posts() ){
			$q->the_post();
			$constructions_sparse_arry[(int) $q->post->year] = array('year' =>  $q->post->year, 'n' => $q->post->n, 'avg_ys' =>  $q->post->avg_ys);
		}
		// fill the gaps
		for( $i = $first_year; $i < $last_year; $i++ ){
			if( ! isset( $constructions_sparse_arry [ $i ] ) ){
				$constructions_sparse_arry[ $i ] = array( 'year' => $i, 'n' => null, 'avg_ys' => null );
			}
		}
		
		sort($constructions_sparse_arry);
		
	}
	
	$results = $constructions_sparse_arry;
	
	wp_reset_postdata();
	
	wp_send_json( $results );
	die();
}



/**
 * Return the Ys distribution by decades
 *
 */
function ap_icas_ys_decade_distribution(){
	global $wpdb;

	$sectors_selection_query_args = array();

	$selection_query_args = ap_icas_parse_query_args( $_POST['selection_query_args'], false );
	$selection_query_args['posts_per_page'] = -1;

	if( ! empty( $_POST['sectors_selection_query_args'] ) ){
		$sectors_selection_query_args = $_POST['sectors_selection_query_args'];
	}

	$q = new Ys_Decade_Histo_WP_Query( $selection_query_args,  $sectors_selection_query_args );
	
	//error_log('Ys_Decade_Histo_WP_Query '. print_r($q, 1));

	$results = array();

	if( $q->have_posts() ){

		// find the first year and last year, to have a continuous years range
		$first_year	= (int) $q->posts[0]->year;
		$last_year	= (int) $q->posts[$q->found_posts - 1]->year;

		$constructions_sparse_arry = array();

		while( $q->have_posts() ){
			$q->the_post();
			if( $q->post->year ){ // hide responses without year
				$constructions_sparse_arry[(int) $q->post->year] = array('year' =>  $q->post->year, 'n' => $q->post->n, 'avg_ys' =>  $q->post->avg_ys);
			}
		}

		sort($constructions_sparse_arry);

	}

	$results = $constructions_sparse_arry;

	wp_reset_postdata();

	wp_send_json( $results );
	die();
}



/**
 * Return the Ys distribution by area
 *
 */
function ap_icas_ys_area_distribution(){
	global $wpdb;
	
	$sectors_selection_query_args = array();
	
	$results = array();
	
	$selection_query_args = ap_icas_parse_query_args( $_POST['selection_query_args'], true );
	
	if( ! empty( $_POST['sectors_selection_query_args'] ) ){
		$sectors_selection_query_args = $_POST['sectors_selection_query_args'];
	}
	
	if( ! empty( $_POST['selection_query_args']['tax_query'] ) ){
		
		$area_id = null;
		//error_log('$_POST[selection_query_args][tax_query] '. print_r($_POST['selection_query_args']['tax_query'], 1)  );
		foreach ( $_POST['selection_query_args']['tax_query'] as $tax ){
			if( isset( $tax['taxonomy'] ) && 'area' == $tax['taxonomy'] ){
				$area_id = $tax['terms'];
				
			}
		}
		
		ap_log( $area_id, '$area_id  ' );
		
		$q = new Ys_Area_WP_Query( $selection_query_args,  $sectors_selection_query_args );
		
		ap_log( $q->request, 'Ys_Area_WP_Query  ' );
		
		$subquery = $q->request;
		// eliminate "LIMIT" clause
		$q_limit_position = stripos( $q->request, 'LIMIT');
		if( false !== $q_limit_position ){
			$subquery = substr( $q->request, 0, $q_limit_position );
		}
		
		
		$area_query = "SELECT area_id, TRUNCATE ( AVG(ys), 2) as avg_ys, COUNT(*) as constr_no, SUM( CASE WHEN constr_type = 'trans' THEN 1 ELSE 0 END ) as transversals  ";
		$area_query .= " FROM ( $subquery ) as t GROUP BY area_id ORDER BY area_id ASC ";
		
		$area_results = $wpdb->get_results( $area_query );
		
		ap_log( $area_results, '$area_results  ' );
		
		if( $area_results ){
			foreach ( $area_results as $r ){
				
				$area_code = ap_icas_get_area_code_for_leaf( $r->area_id );
				
				
				$results[ $r->area_id] = array(
						'area_code' => $area_code,
						'area_id' =>  $r->area_id,
						'n' => $r->constr_no,
						'n_trans' => $r->transversals,
						'avg_ys' => $r->avg_ys
				);
			}
		}
		
		sort( $results );
		
		$results = array_values( $results );
		
		//ap_log( $results, 'Results Area' );
	}
	


	wp_send_json( $results );
	die();
}


/**
 * Distribution on Ye
 * 
 * 0 = traverses
 * 0-2.0 = sills
 * > 2.0 = dams
 */
function ap_icas_ys_ye_distribution(){
	global $wpdb;

	$selection_query_args = ap_icas_parse_query_args( $_POST['selection_query_args'], false );
	$selection_query_args['posts_per_page'] = -1;


	$q = new Ys_Ye_WP_Query( $selection_query_args );

	//error_log('Ys_Ye_WP_Query '. print_r($q, 1));

	$results = array();

	if( $q->have_posts() ){

		while( $q->have_posts() ){
			$q->the_post();
			$results[(int) $q->post->ye] = array('ye' =>  $q->post->ye, 'n' => $q->post->n, 'avg_ys' =>  $q->post->avg_ys);
		}
	}
	
	wp_reset_postdata();

	wp_send_json( $results );
	die();
}

/**
 * Distribution of Ys on transversal construction body materials
 *
 */
function ys_trans_material_construction_distribution(){
	global $wpdb;

	$selection_query_args = ap_icas_parse_query_args( $_POST['selection_query_args'], false );
	$selection_query_args['posts_per_page'] = -1;


	$q = new Ys_Trans_Mat_Constr_Wp_Query( $selection_query_args );

	// error_log('Ys_Trans_Mat_Constr_Wp_Query '. print_r($q, 1));

	$results = array();

	if( $q->have_posts() ){

		while( $q->have_posts() ){
			$q->the_post();
			$results[] = array(
					'term_id' =>  $q->post->term_id, 
					'n' => $q->post->n, 
					'avg_ys' =>  $q->post->avg_ys,
					'name'	=> $q->post->tax_name,
					'description' =>  $q->post->term_description
			);
		}
	}

	wp_reset_postdata();

	wp_send_json( $results );
	die();
}



/**
 * Distribution of Ys on longitudinal construction body materials
 *
 */
function ys_long_material_construction_distribution(){
	global $wpdb;

	$selection_query_args = ap_icas_parse_query_args( $_POST['selection_query_args'], false );
	$selection_query_args['posts_per_page'] = -1;


	$q = new Ys_Trans_Mat_Constr_Wp_Query( $selection_query_args );

	//error_log('Ys_Trans_Mat_Constr_Wp_Query '. print_r($q, 1));

	$results = array();

	if( $q->have_posts() ){

		while( $q->have_posts() ){
			$q->the_post();
			$results[] = array(
					'term_id' =>  $q->post->term_id,
					'n' => $q->post->n,
					'avg_ys' =>  $q->post->avg_ys,
					'name'	=> $q->post->tax_name,
					'description' =>  $q->post->term_description
			);
		}
	}

	wp_reset_postdata();

	wp_send_json( $results );
	die();
}


/**
 * Return the granulometry distribution for the specific selection
 */
function ap_icas_granulometry_distribution(){
	global $wpdb, $post;

	$selection_query_args = ap_icas_parse_query_args( $_POST['selection_query_args'] );
	
	$q = new Granulometries_WP_Query( $selection_query_args );
	
	$req = $q->request;
	
	//error_log('Granulometries_WP_Query req '. print_r($req, 1));
	
	//error_log('Granulometries_WP_Query '. print_r($q, 1));
	
	$req = substr( $req, 0, strpos($req, 'LIMIT' ) );

	$req = substr( $req, 0, strpos($req, 'ORDER BY' ) );
	
	$results = array();
	
	if( $q->have_posts() ) {
		while ( $q->have_posts() ){
			$q->the_post();
			$results[] = array(
					'n' => $post->n,
					'avg_ys' => $post->avg_ys,
					'tax_name' => $post->tax_name,
					'term_id'	=> $post->term_id
			);
			
		}
	}
	
	//error_log( 'results '. print_r($results, 1));
	
	wp_send_json( $results );
	die();
}


/**
 * Helper function, preparing for post selection query avoidint caching and in some cases unused results
 * 
 * Can be used to obtain a selection query, usually used in a subquery, so no cache needed and limit it to a single result
 * 
 * @param array $post_arr the $_POST selection args
 * @param bool $single_result if limit result to a single result
 * @return array
 */
function ap_icas_parse_query_args( $post_arr, $single_result = true ){
	
	if( ! $post_arr ) return array();
	
	$selection_query_args = $post_arr;

	$selection_query_args['cache_results'] = false;
	$selection_query_args['update_post_term_cache'] = false;
	$selection_query_args['update_post_meta_cache'] = false;
	$selection_query_args['no_found_rows'] = false;
	
	if( $single_result ){
		$selection_query_args['posts_per_page'] = 1;
		$selection_query_args['page'] = 1;
		$selection_query_args['no_found_rows'] = true;
	}

	return $selection_query_args;
}
