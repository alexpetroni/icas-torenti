<?php 
// If uninstall not called from WordPress exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
exit ();

function ap_icas_delete_taxonomies(){
	
	$constr_types_arr = array( 
			'trans' ,
			'long'
	);
	
	$taxonomy = 'construction_type';
	
	
	foreach ( $constr_types_arr as $type ){
		$term = get_term_by( 'slug', $type , $taxonomy );
		
		if( $term ){
			wp_delete_term( (int) $term->term_id, $taxonomy );
		}
	}
}

ap_icas_delete_taxonomies();