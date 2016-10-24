<?php
/**
 * Group constructions by Ye
 * 0 => traverses
 * 0 - 2 => sills
 * > 2 => dams
 * 
 * @author alex
 *
 */
class Ys_Ye_WP_Query extends WP_Query{
	
	public function __construct( $query = '' ){
		
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
		
		return " CASE WHEN meta_ye.meta_value = '' OR meta_ye.meta_value = 0 THEN 0 WHEN  meta_ye.meta_value > 0 AND meta_ye.meta_value <= 2 THEN 1 WHEN meta_ye.meta_value > 2 THEN 2 END as ye,   COUNT(*) n, TRUNCATE ( AVG(meta_ys.meta_value), 2 ) avg_ys";
	}
	
	public function posts_where( $sql, $q ){
		global $wpdb;
		
		$sql .= " AND meta_ys.meta_key = 'ap_icas_construction_ys' ";
		$sql .= " AND meta_ye.meta_key = 'ap_icas_trans_dim_ye' ";
		
		return $sql;
	}
	
	
	public function posts_groupby( $sql, $q ){
		global $wpdb;

		return ' ye ';
	}
	
	public function posts_orderby( $sql, $q ){
		global $wpdb;
	
		return ' ye ';
	}
	
	public function posts_join( $sql, $q){
		global $wpdb;
		
		$sql .= " INNER JOIN $wpdb->postmeta meta_ys ON ( $wpdb->posts.ID = meta_ys.post_id )   ";
		$sql .= " INNER JOIN $wpdb->postmeta meta_ye ON ( $wpdb->posts.ID = meta_ye.post_id )   ";
		return $sql;
	}
	
}