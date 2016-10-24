<?php

include 'class-construction-images-metabox.php';
include 'class-construction-general-data-metabox.php';
include 'class-construction-identification-metabox.php';
include 'class-construction-longitudinal-data-metabox.php';
include 'class-construction-transversal-data-metabox.php';

class Icas_Construction_Metaboxes{
	
	public function __construct(){
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );		
		add_action( 'admin_menu' , array( $this, 'remove_area_meta' ) );
		
		add_filter( 'wp_insert_post_data' , array( $this, 'modify_post_title' ), '99', 2 );
		
		add_action( 'save_post_construction', array( $this, 'save_construction_meta' ), 10, 3 );
		
		add_action( 'delete_post', array( $this, 'delete_longitudinal_sectors') );
	}
	
	
	// Create metaboxes
	public function add_metaboxes(){		
		add_meta_box('construction_identification',	__('Identificare bazin', 'icas'), 'Icas_Construction_Identification_Metabox::output', 'construction', 'normal', 'high');
		add_meta_box('construction_general_data',	__('Date generale', 'icas'), 'Icas_General_Data_Metabox::output', 'construction', 'normal', 'high');
		add_meta_box('construction_longitudinal_data',	__('Elemente dimensionale longitudinale', 'icas'), 'Icas_Construction_Longitudinal_Data_Metabox::output', 'construction', 'normal', 'high');
		add_meta_box('construction_transversal_data',	__('Elemente dimensionale transversale', 'icas'), 'Icas_Construction_Transversal_Data_Metabox::output', 'construction', 'normal', 'high');
		
		add_meta_box('construction_images',	__('Imagini', 'icas'), 'Icas_Construction_Images_Metabox::output', 'construction', 'side', 'default');
	}
	

	// remove area taxonomy from the right side for construction
	public function remove_area_meta() {
		//remove_meta_box( 'areadiv', 'construction', 'side' );
		remove_meta_box( 'tagsdiv-construction_type', 'construction', 'side' );
		remove_meta_box( 'pageparentdiv', 'construction', 'side' );
		remove_meta_box( 'icas_locationdiv', 'construction', 'side' );
		remove_meta_box( 'tagsdiv-trans_disip_type', 'construction', 'side' );
		remove_meta_box( 'tagsdiv-trans_constr_type', 'construction', 'side' );
		remove_meta_box( 'tagsdiv-trans_gal_type', 'construction', 'side' );
	
		// construction material taxonomies 
		$const_materials_taxonomies = ap_icas_get_material_taxonomies();
		foreach ( $const_materials_taxonomies as $k => $v ){
			remove_meta_box( 'tagsdiv-'.$k, 'construction', 'side' );
		}

	}
	
	
	
	
	
	/**
	 * Modify the title for the construction using cod_bazin and cod_lucrare
	 * 
	 * @param array $data
	 * @param array $postarr
	 * @return array
	 */
	public function modify_post_title( $data, $postarr ){		
		// don't save if construction type is not selected
		if( $data['post_type'] != 'construction' || empty( $_POST[ 'ap_icas_construction_type' ] ) ){
			return $data;
		}
		
		$area_delimiter = '-';
		$code_delimiter = '#';
		$data['post_title'] = '';
	
		if( isset( $_POST['ap_icas_cod_bazin'] ) && $_POST['ap_icas_cod_bazin'] ){			
			$area_tax_arr = $_POST['ap_icas_cod_bazin'];
			
			// first come directly as term_id, but as string. This check is needed because on loading data from files come as term name
			if( is_numeric( $area_tax_arr[0] ) ){
				$area_tax_arr[0] = (int) $area_tax_arr[0]; 
			}
	
	
			$area_terms_arr = ap_icas_get_area_terms( $area_tax_arr );
	
			// remove until the first empty element from the array
			$title_terms_ids_arr = array();
	
			foreach ( $area_terms_arr as $t ){
				$title_terms_ids_arr[] = $t->name;
			}
	
			$data['post_title'] .= implode($area_delimiter, $title_terms_ids_arr );
	
			if( isset( $_POST['ap_icas_construction_code'] ) &&  $_POST['ap_icas_construction_code'] ){
				$data['post_title'] .= $code_delimiter.$_POST['ap_icas_construction_code'];
			}
			
			$data['post_name'] = sanitize_title( $data['post_title'] );
	
		}		
	
		return $data; 
	}

	
	/**
	 * Save construction metadata
	 * 
	 * @param unknown $post_id
	 * @param unknown $post
	 * @param unknown $update
	 */
	public function save_construction_meta( $post_id, $post, $update ){
		
		// error_log('$update  save_construction_meta '. print_r($update , 1) );
		
		// Checks save status		
		//if ( ! $update ) return;
		// if ( ! isset( $_POST[ 'icas_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'icas_nonce' ], 'sery-icas-nonce' ) )  return;
		
		if(  empty( $_POST[ 'ap_icas_construction_type' ] ) ){
			return;
		}
		
		Icas_Construction_Identification_Metabox::save($post_id, $post, $update);
		Icas_General_Data_Metabox::save($post_id, $post, $update);
		Icas_Construction_Images_Metabox::save($post_id, $post, $update);
		Icas_Construction_Longitudinal_Data_Metabox::save($post_id, $post, $update);
		Icas_Construction_Transversal_Data_Metabox::save($post_id, $post, $update);
	}
	
	
	
	
	public function delete_longitudinal_sectors( $post_id ){
		$post = get_post( $post_id );

		if( ! $post ){ return; }
		
		if ( $post->post_type == 'construction' ){

				$children = get_children( array('post_parent' => $post_id,  'post_type' => 'construction_sector' ) );
				
				if( $children ){
					foreach ( $children as $c ){
						wp_delete_post( $c->ID );
					}
				}
			
		}
		
	}
}


new Icas_Construction_Metaboxes();