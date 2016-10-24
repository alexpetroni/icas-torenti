<?php
/**
 * Class which query the Ys values for a selection
 * 
 * The response is the post ID-s and the Ys for each construction
 * 
 * @author alex
 *
 */
class Ys_Histo_WP_Query extends WP_Query{
	
	
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
		
		$ys_sel  = " CASE ";
		$ys_sel .= " WHEN meta.meta_value > 0  AND meta.meta_value <= 20 THEN 20 ";
		$ys_sel .= " WHEN meta.meta_value > 20 AND meta.meta_value <= 40 THEN 40 ";
		$ys_sel .= " WHEN meta.meta_value > 40 AND meta.meta_value <= 60 THEN 60 ";
		$ys_sel .= " WHEN meta.meta_value > 60 AND meta.meta_value <= 80 THEN 80 ";
		$ys_sel .= " WHEN meta.meta_value > 80 AND meta.meta_value <= 100 THEN 100 ";
		$ys_sel .= " ELSE -10 ";
		$ys_sel .= " END as ys_segment, ";
		$ys_sel .= " COUNT(*) n, ";
		$ys_sel .= " TRUNCATE ( AVG(meta.meta_value), 2 ) avg_ys ";
		
		return $ys_sel;
	}
	
	public function posts_where( $sql, $q ){
		global $wpdb;
		
		$sql .= " AND meta.meta_key = 'ap_icas_construction_ys' ";
		
		if( $this->long_sect_query ){
			$sql .= " AND $wpdb->posts.ID IN ( $this->long_sect_query ) ";
		}
		
		return $sql;
	}
	
	
	public function posts_groupby( $sql, $q ){
		global $wpdb;

		return ' ys_segment ';
	}
	
	
	public function posts_orderby( $sql, $q ){
		global $wpdb;
		return ' ys_segment ASC ';
	}
	
	
	public function posts_join( $sql, $q){
		global $wpdb;
		
		$sql .= " INNER JOIN $wpdb->postmeta meta ON ( $wpdb->posts.ID = meta.post_id )   ";
		return $sql;
	}
	
}