<?php
class Ys_Long_Mat_Constr_Wp_Query extends WP_Query{
	
	public $long_sect_query = '';
	
	public function __construct( $query = '' ){
		
		if( $long_sect_query_args ){
			$this->long_sect_query = ap_icas_get_sectors_parents_ids_query( $long_sect_query_args );
		}
		
		add_filter( 'posts_fields',	array( $this, 'posts_fields' ), 10, 2 );
		add_filter( 'posts_search',	array( $this, 'posts_where' ), 10, 2 );		
		add_filter( 'posts_groupby', array( $this, 'posts_groupby' ), 10, 2 );
		add_filter( 'posts_join',	array( $this, 'posts_join' ), 10, 2 );			
		add_filter( 'post_limits', array( $this, 'post_limits' ), 10, 2 );	
		add_filter( 'posts_orderby', array( $this, 'posts_orderby' ), 10, 2 );
		
		parent::__construct( $query );
		
		remove_filter('posts_fields', array( $this, 'posts_fields' ), 10  );
		remove_filter( 'posts_search',	array( $this, 'posts_where' ), 10, 2 );
		remove_filter( 'posts_groupby', array( $this, 'posts_groupby' ), 10, 2 );
		remove_filter( 'posts_join',	array( $this, 'posts_join' ), 10, 2 );
		remove_filter( 'post_limits', array( $this, 'post_limits' ), 10, 2 );
		remove_filter( 'posts_orderby', array( $this, 'posts_orderby' ), 10, 2 );
	}
	
	public function posts_fields( $sql, $q ){
		global $wpdb;
		return " COUNT(*) as n, TRUNCATE( AVG( meta.meta_value ), 2 ) avg_ys, icas_terms.name as tax_name, icas_terms.term_id as term_id, icas_term_tax.description as term_description ";
	}
	
	public function posts_where( $sql, $q ){
		global $wpdb;
		
		$sql .= " AND meta.meta_key = 'ap_icas_construction_ys' ";
		$sql .= " AND icas_term_tax.taxonomy = 'mat_sect_apron' ";
		
		if( $this->long_sect_query ){
			$sql .= " AND $wpdb->posts.ID IN ( $this->long_sect_query ) ";
		}
		
		return $sql;
	}
	
	
	public function posts_groupby( $sql, $q ){
		global $wpdb;
		return "  term_id ";
		return $sql;
	}
	
	public function posts_join( $sql, $q){
		global $wpdb;
		
		$sql .= " INNER JOIN $wpdb->postmeta meta ON ( wp_posts.ID = meta.post_id )   ";
		$sql .= " INNER JOIN $wpdb->term_relationships icas_term_rel ON ( wp_posts.ID = icas_term_rel.object_id )   ";
		$sql .= " INNER JOIN $wpdb->term_taxonomy icas_term_tax ON ( icas_term_tax.term_taxonomy_id = icas_term_rel.term_taxonomy_id )   ";
		$sql .= " INNER JOIN $wpdb->terms icas_terms ON ( icas_terms.term_id = icas_term_tax.term_id )   ";
		
		return $sql;
	}
	
	
	public function post_limits( $sql, $q){
		return " ";
	}
	
	
	
	public function posts_orderby( $sql, $q){
		return " term_id ASC ";
	}
}