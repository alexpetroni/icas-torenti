<?php
class Ys_Years_Histo_WP_Query extends WP_Query{
	
	
	public $long_sect_query = '';
	
	public function __construct( $query = '', $long_sect_query_args = array() ){
		
		if( $long_sect_query_args ){
			$this->long_sect_query = ap_icas_get_sectors_parents_ids_query( $long_sect_query_args );
		}
		
		add_filter( 'posts_fields',	array( $this, 'posts_fields' ), 10, 2 );
		add_filter( 'posts_search',	array( $this, 'posts_where' ), 10, 2 );		
		add_filter( 'posts_groupby', array( $this, 'posts_groupby' ), 10, 2 );
		add_filter( 'posts_orderby', array( $this, 'posts_orderby') , 10, 2 );
		add_filter( 'posts_join',	array( $this, 'posts_join' ), 10, 2 );		
		
		parent::__construct( $query );
		
		remove_filter('posts_fields', array( $this, 'posts_fields' ), 10  );
		remove_filter( 'posts_search',	array( $this, 'posts_where' ), 10, 2 );
		remove_filter( 'posts_groupby', array( $this, 'posts_groupby' ), 10, 2 );
		remove_filter( 'posts_orderby', array( $this, 'posts_orderby') , 10, 2 );
		remove_filter( 'posts_join',	array( $this, 'posts_join' ), 10, 2 );
	}
	
	public function posts_fields( $sql, $q ){
		global $wpdb;
		
		return "meta_year.meta_value as year,   COUNT(*) n, TRUNCATE ( AVG(meta_ys.meta_value), 2 ) avg_ys";
	}
	
	public function posts_where( $sql, $q ){
		global $wpdb;
		
		$sql .= " AND meta_ys.meta_key = 'ap_icas_construction_ys' ";
		$sql .= " AND meta_year.meta_key = 'ap_icas_construction_date' ";
		$sql .= " AND meta_year.meta_value > 1900 ";
		
		if( $this->long_sect_query ){
			$sql .= " AND $wpdb->posts.ID IN ( $this->long_sect_query ) ";
		}
		
		return $sql;
	}
	
	
	public function posts_groupby( $sql, $q ){
		global $wpdb;

		return ' meta_year.meta_value ';
	}
	
	public function posts_orderby( $sql, $q ){
		global $wpdb;
	
		return ' meta_year.meta_value ';
	}
	
	public function posts_join( $sql, $q){
		global $wpdb;
		
		$sql .= " INNER JOIN $wpdb->postmeta meta_ys ON ( $wpdb->posts.ID = meta_ys.post_id )   ";
		$sql .= " INNER JOIN $wpdb->postmeta meta_year ON ( $wpdb->posts.ID = meta_year.post_id )   ";
		return $sql;
	}
	
}