<?php
/**
 * General construction query which take in account the sectors subqueries if needed
 * 
 * @author alex
 *
 */

include_once ICAS_PLUGIN_DIR.'includes/admin/metaboxes/class-construction-general-data-metabox.php';
include_once ICAS_PLUGIN_DIR.'includes/admin/metaboxes/class-construction-transversal-data-metabox.php';



class Download_Transversal_Constructions_WP_Query extends WP_Query{
	
	
	
	public function __construct( $query_args ){		
		$query_args['posts_per_page'] = 1;
		
		
		add_filter( 'posts_fields',	array( $this, 'posts_fields' ), 10, 2 );
		/*
		add_filter( 'posts_search',	array( $this, 'posts_where' ), 10, 2 );	
		add_filter( 'posts_orderby', array( $this, 'posts_orderby') , 10, 2 );
		add_filter( 'posts_join',	array( $this, 'posts_join' ), 10, 2 );
		
		//add_filter( 'post_limits', array( $this, 'post_limits' ), 10, 2 );*/
		
		parent::__construct( $query_args );
		
		remove_filter( 'posts_fields',	array( $this, 'posts_fields' ), 10, 2 );
/* 		remove_filter( 'posts_search',	array( $this, 'posts_where' ), 10, 2 );
		remove_filter( 'posts_orderby', array( $this, 'posts_orderby') , 10, 2 );
		remove_filter( 'posts_join',	array( $this, 'posts_join' ), 10, 2 ); */
	}	
	
	
	
	
	public function posts_fields( $sql, $q ){
		global $wpdb;
		
		// general fields
		//$fields_general = ap_icas_sql_select_meta_fields_from_table( Icas_General_Data_Metabox::get_fields() );			
		
		return $sql = ' wp_posts.ID as ID ' ;
	}
	
	
	function post_limits( $sql, $q ){
		return ' s';
	}
	
	
	public function posts_where( $sql, $q ){
		global $wpdb;
		
		//$sql .= " AND meta_ys.meta_key = 'ap_icas_construction_ys'  ";
		// $sql .= " AND meta_basin.meta_key = 'ap_icas_basin_name' ";
		
		
		//$sql .= " AND meta_basin.meta_key = 'ap_icas_construction_code' ";
		
		// general
		$fields_general = ap_icas_sql_where_meta_fields_from_table( Icas_General_Data_Metabox::get_fields() );
		
		$sql .= $fields_general;
		
		return $sql;
	}
	
	public function posts_join( $sql, $q){
		global $wpdb;
	
		//$sql .= " INNER JOIN $wpdb->postmeta meta_ys ON ( $wpdb->posts.ID = meta_ys.post_id )   ";
		//$sql .= " INNER JOIN $wpdb->postmeta meta_basin ON ( $wpdb->posts.ID = meta_basin.post_id )   ";
		
		$sql .= ap_icas_sql_join_meta_fields_from_table( Icas_General_Data_Metabox::get_fields() );
		return $sql;
	}
	
	
	public function posts_orderby( $sql, $q){
		global $wpdb;
	
		$sql = " post_title ASC  ";
		
		return $sql;
	}
}