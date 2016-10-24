<?php
/**
 * General construction query which take in account the sectors subqueries if needed
 * 
 * @author alex
 *
 */
class Download_Longitudinal_Constructions_WP_Query extends WP_Query{
	
	
	public $long_sect_query = '';
	
	
	
	public function __construct( $query_args = '', $long_sect_query_args = array() ){		
		
		// if a children filter is imposed, find the children request query
		// add a limit 1 per response, and remove it later in use, because we are interensted only in generated query
		if( $long_sect_query_args ){			
			$this->long_sect_query = ap_icas_get_sectors_parents_ids_query( $long_sect_query_args );
			// error_log( '$this->children '. print_r($this->long_sect_query, 1));
		}
		
		add_filter( 'posts_fields',	array( $this, 'posts_fields' ), 10, 2 );
		add_filter( 'posts_search',	array( $this, 'posts_where' ), 10, 2 );	
		add_filter( 'posts_orderby', array( $this, 'posts_orderby') , 10, 2 );
		add_filter( 'posts_join',	array( $this, 'posts_join' ), 10, 2 );
		
		parent::__construct( $query_args );
		
		remove_filter( 'posts_fields',	array( $this, 'posts_fields' ), 10, 2 );
		remove_filter( 'posts_search',	array( $this, 'posts_where' ), 10, 2 );
		remove_filter( 'posts_orderby', array( $this, 'posts_orderby') , 10, 2 );
		remove_filter( 'posts_join',	array( $this, 'posts_join' ), 10, 2 );
	}	
	
	
	public function posts_fields( $sql, $q ){
		global $wpdb;
	
		return $sql." , meta_ys.meta_value as ys, meta_basin.meta_value as basin ";
	}
	
	public function posts_where( $sql, $q ){
		global $wpdb;
		
		$sql .= " AND meta_ys.meta_key = 'ap_icas_construction_ys'  ";
		$sql .= " AND meta_basin.meta_key = 'ap_icas_basin_name' ";
		//$sql .= " AND meta_basin.meta_key = 'ap_icas_construction_code' ";
		
		if( $this->long_sect_query ){
			$sql .= " AND $wpdb->posts.ID IN ( $this->long_sect_query ) ";
		}
		return $sql;
	}
	
	public function posts_join( $sql, $q){
		global $wpdb;
	
		$sql .= " INNER JOIN $wpdb->postmeta meta_ys ON ( $wpdb->posts.ID = meta_ys.post_id )   ";
		$sql .= " INNER JOIN $wpdb->postmeta meta_basin ON ( $wpdb->posts.ID = meta_basin.post_id )   ";
		return $sql;
	}
	
	
	public function posts_orderby( $sql, $q){
		global $wpdb;
	
		$sql = " post_title ASC  ";
		return $sql;
	}
}