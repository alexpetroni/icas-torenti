<?php
include_once ICAS_PLUGIN_DIR.'includes/queries/class-constructions-wp-query.php';
include_once ICAS_PLUGIN_DIR.'includes/queries/class-download-transversal-constructions-wp-query.php';

include_once ICAS_PLUGIN_DIR.'includes/class-icas-construction.php';
include_once ICAS_PLUGIN_DIR.'includes/class-icas-construction-sector.php';

add_action('template_redirect', 'ap_icas_template_redirect');

function ap_icas_template_redirect(){
	global $wp_query, $wpdb;
	
	if( isset( $_GET['download'] ) && 'download_list' == $_GET['download'] ){
		
			$q_args = ap_icas_get_constructions_query_args_from_str( $_SERVER['QUERY_STRING'] );;
			error_log ('start Constructions_WP_Query ' );
		$c = new Download_Transversal_Constructions_WP_Query( $q_args['general'] );
		
		error_log ('Constructions_WP_Query '.print_r($c, 1) );
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=lista-lucrari.csv');
		
		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');
		
		$query = $c->request;
		// remove the limit clause 
		$limit_pos = stripos( $query,  'LIMIT');
		if( false !== $limit_pos ){
			$query = substr( $query , 0, $limit_pos );
		}
		error_log ('$query NO limits '.print_r($query, 1) );
		$results = $wpdb->get_results( $query, ARRAY_A );
		
		error_log ('$results '.print_r($results, 1) );
		
		if( empty ($results ) ){
			fputcsv($output, array('No result found'));
		}else{
			// output the column headings
			fputcsv($output, array('Column 1', 'Column 2', 'Column 3'));
			
			foreach ( $results as $id ){
				$c = new Icas_Construction( $id );
			}
			
		}

		
		


		fclose($output);
		die;

		
		// loop over the rows, outputting them
		while ($row = mysql_fetch_assoc($rows)) fputcsv($output, $row);
		die(print_r($_GET));
	}
}