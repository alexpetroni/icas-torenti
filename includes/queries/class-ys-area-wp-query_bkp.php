<?php
/**
 * Class which query the Ys values for a area and the subareas children 
 * 
 * The response is the post ID-s and the Ys for each construction
 * 
 * @author alex
 *
 */
class Ys_Area_WP_Query extends WP_Query{
	
	
	public $long_sect_query = '';
	
	public function __construct( $query = '', $long_sect_query_args = array() ){		
		
		if( $long_sect_query_args ){
			$this->long_sect_query = ap_icas_get_sectors_parents_ids_query( $long_sect_query_args );
		}
		
		add_filter( 'posts_fields',	array( $this, 'posts_fields' ), 10, 2 );
		add_filter( 'posts_search',	array( $this, 'posts_where' ), 10, 2 );		
		add_filter( 'posts_groupby', array( $this, 'posts_groupby' ), 10, 2 );
		add_filter( 'posts_orderby', array( $this, 'posts_orderby') , 10, 2 );
		add_filter( 'post_limits', array( $this, 'post_limits' ), 10, 2 );
		add_filter( 'posts_join',	array( $this, 'posts_join' ), 10, 2 );
		
		parent::__construct( $query );
		
		remove_filter('posts_fields', array( $this, 'posts_fields' ), 10  );
		remove_filter( 'posts_search',	array( $this, 'posts_where' ), 10, 2 );
		remove_filter( 'posts_groupby', array( $this, 'posts_groupby' ), 10, 2 );
		remove_filter( 'posts_orderby', array( $this, 'posts_orderby') , 10, 2 );
		remove_filter( 'post_limits', array( $this, 'post_limits' ), 10, 2 );
		remove_filter( 'posts_join',	array( $this, 'posts_join' ), 10, 2 );
		
	}
	
	public function posts_fields( $sql, $q ){
		global $wpdb;
		
		$ys_sel = " DISTINCT icas_term_tax.term_id as area_id, ";
		$ys_sel .= " COUNT( DISTINCT $wpdb->posts.ID) as n, ";
		$ys_sel .= " COUNT( CASE WHEN icas_terms_tab.slug = 'trans' THEN $wpdb->posts.ID END ) as n_trans, ";
		$ys_sel .= " TRUNCATE ( AVG( meta.meta_value ), 2 ) as avg_ys, ";
		$ys_sel .= " COUNT( meta.meta_key ) as test ";
		
		return $ys_sel;
	}
	
	public function posts_where( $sql, $q ){
		global $wpdb;
		
		$sql .= " AND meta.meta_key = 'ap_icas_construction_ys' ";
		$sql .= " AND icas_term_tax.taxonomy = 'area' ";
		$sql .= " AND icas_constr_type_tax.taxonomy = 'construction_type' ";
		
		if( $this->long_sect_query ){
			$sql .= " AND $wpdb->posts.ID IN ( $this->long_sect_query ) ";
		}
		
		return $sql;
	}
	
	
	public function posts_groupby( $sql, $q ){
		global $wpdb;

		return ' area_id ';
	}
	
	
	public function posts_orderby( $sql, $q ){
		global $wpdb;
		return ' area_id ASC ';
	}
	
	
	public function posts_join( $sql, $q){
		global $wpdb;
		
		$sql .= " INNER JOIN $wpdb->postmeta meta ON ( $wpdb->posts.ID = meta.post_id )   ";
		$sql .= " INNER JOIN $wpdb->term_relationships icas_area_term_rel ON ( wp_posts.ID = icas_area_term_rel.object_id )   ";
		$sql .= " INNER JOIN $wpdb->term_taxonomy icas_term_tax ON ( icas_term_tax.term_taxonomy_id = icas_area_term_rel.term_taxonomy_id )   ";
		
		$sql .= " INNER JOIN $wpdb->term_relationships icas_constr_type_term_rel ON ( wp_posts.ID = icas_constr_type_term_rel.object_id )   ";
		$sql .= " INNER JOIN $wpdb->term_taxonomy icas_constr_type_tax ON ( icas_constr_type_tax.term_taxonomy_id = icas_constr_type_term_rel.term_taxonomy_id )   ";
		$sql .= " INNER JOIN $wpdb->terms icas_terms_tab ON ( icas_constr_type_tax.term_id = icas_terms_tab.term_id )   ";
		
		return $sql;
	}
	
	public function post_limits( $sql, $q){
		return " ";
	}
	
}