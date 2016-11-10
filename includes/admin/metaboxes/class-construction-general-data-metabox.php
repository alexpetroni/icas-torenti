<?php
// Construction general data metabox

class Icas_General_Data_Metabox{
	
	
	public static function get_fields(){
		$fields = array(
				'_original_Ys',
				'ap_icas_construction_latitude_deg',
				'ap_icas_construction_latitude_min',
				'ap_icas_construction_latitude_sec' ,
				'ap_icas_construction_latitude_hemis',
				'ap_icas_construction_longitude_deg',
				'ap_icas_construction_longitude_min',
				'ap_icas_construction_longitude_sec',
				'ap_icas_construction_longitude_hemis',
				'ap_icas_construction_ys',
				'ap_icas_construction_protected_area', 			
				'ap_icas_construction_owner',
				'ap_icas_construction_date',
				'ap_icas_construction_review_date' 
				) ;
			
		return $fields;
	}
	
	
	public static function get_location_term_fields(){
		return array(
				'ap_icas_construction_county',
				'ap_icas_construction_city'
		);
	}
	
	public static function output( $post ){
		
		
		$metafields_arr = self::get_fields();
	
		$meta_arr = ap_icas_get_post_meta( $post->ID, $metafields_arr	);
		
		if( empty( $meta_arr['ap_icas_construction_longitude'] ) ){
			$meta_arr['ap_icas_construction_latitude_hemis'] = 'N';
		}
		
		if( empty( $meta_arr['ap_icas_construction_longitude'] ) ){
			$meta_arr['ap_icas_construction_longitude_hemis'] = 'E';
		}
		
		extract($meta_arr);	
		
		// get location terms 		
		$locations_terms = wp_get_object_terms( $post->ID, 'icas_location' );
		
		echo '<p>';
		
		// ==============================
		// Coordinates
		// ==============================
		// LATITUDE
		
		$size = 2;
		echo '<div class="icas-field">';
		$latitude_deg = array(
				'size' =>	$size,
				'before' => '',
				'after' => '',
				'name'	=>	'ap_icas_construction_latitude_deg',
				'value'	=>	$ap_icas_construction_latitude_deg,
				'label'	=>	__("Latitudine" , 'icas') .": "
		);
		
		ap_icas_get_form_element( $latitude_deg );
		
		echo '&deg;';
		
		$latitude_min = array(
				'size' =>	$size,
				'before' => '',
				'after' => '',
				'name'	=>	'ap_icas_construction_latitude_min',
				'value'	=>	$ap_icas_construction_latitude_min,
				'label'	=>	""
		);
		
		
		ap_icas_get_form_element( $latitude_min );
		echo "'";
		
		$latitude_sec = array(
				'size' =>	$size,
				'before' => '',
				'after' => '',
				'name'	=>	'ap_icas_construction_latitude_sec',
				'value'	=>	$ap_icas_construction_latitude_sec,
				'label'	=>	""
		);
		
		ap_icas_get_form_element( $latitude_sec );
		echo '"';
		
		$latitude = array(
				'type'	=>	'hidden',
				'before' => '',
				'after' => '',
				'name'	=>	'ap_icas_construction_latitude_hemis',
				'value'	=>	$ap_icas_construction_latitude_hemis,
				'label'	=>	""
		);
		
		ap_icas_get_form_element( $latitude );
		echo '<b>'.$ap_icas_construction_latitude_hemis . '</b>';
		echo '</div>';
		
		// LONGITUDE
		echo '<div class="icas-field">';
		$longitude_deg = array(
				'size' =>	$size,
				'before' => '',
				'after' => '',
				'name'	=>	'ap_icas_construction_longitude_deg',
				'value'	=>	$ap_icas_construction_longitude_deg,
				'label'	=>	__("Longitudine" , 'icas') .": "
		);
		
		ap_icas_get_form_element( $longitude_deg );
		echo '&deg;';
		
		$longitude_min = array(
				'size' =>	$size,
				'before' => '',
				'after' => '',
				'name'	=>	'ap_icas_construction_longitude_min',
				'value'	=>	$ap_icas_construction_longitude_min,
				'label'	=>	""
		);
		
		ap_icas_get_form_element( $longitude_min );
		echo "'";
		
		$longitude_sec = array(
				'size' =>	$size,
				'before' => '',
				'after' => '',
				'name'	=>	'ap_icas_construction_longitude_sec',
				'value'	=>	$ap_icas_construction_longitude_sec,
				'label'	=>	""
		);
		
		ap_icas_get_form_element( $longitude_sec );
		echo '"';
		
		$longitude = array(
				'type' =>	'hidden',
				'before' => '',
				'after' => '',
				'name'	=>	'ap_icas_construction_longitude_hemis',
				'value'	=>	$ap_icas_construction_longitude_hemis,
				'label'	=>	""
		);
		
		ap_icas_get_form_element( $longitude );
		echo '<b>'.$ap_icas_construction_longitude_hemis .'</b>';
		
		
		echo '</div>' ; // end .icas-admin-field 
		
		$ys = array(
				'size' =>	2,
				'name'	=>	'ap_icas_construction_ys',
				'value'	=>	$ap_icas_construction_ys,
				'label'	=>	"<b>Ys</b>");
		ap_icas_get_form_element( $ys );
		
// ==> DE STERS		
		if( $_original_Ys ){
			echo 'anterior: '. $_original_Ys;
		}
		echo '</p>';
		

		
		echo '<p>';
		
		// Construction date
		$years_arr = array('' => __("Selecteaza", 'icas'));
		$current_year = (int) date("Y");
		for( $i = 0; $i < 100; $i++ ){
			$y = $current_year - $i;
			$years_arr[$y] = $y;
		}
		
		$construction_date_args = array(
				'type'	=>	'select',
				'name'	=>	'ap_icas_construction_date',
				'options'	=>	$years_arr,
				'value'	=>	$ap_icas_construction_date,
				'label'	=>	__("An executie" , 'icas') .": "
		
		);
		ap_icas_get_form_element( $construction_date_args );
		
		
		// Review date
		$years_arr = array('' => __("Selecteaza", 'icas'));
		$current_year = (int) date("Y");
		for( $i = 0; $i < 100; $i++ ){
			$y = $current_year - $i;
			$years_arr[$y] = $y;
		}
		
		$construction_date_args = array(
				'type'	=>	'select',
				'name'	=>	'ap_icas_construction_review_date',
				'options'	=>	$years_arr,
				'value'	=>	$ap_icas_construction_review_date,
				'label'	=>	__("An inventariere" , 'icas') .": "
		
		);
		ap_icas_get_form_element( $construction_date_args );
		
		// Protected area exist
		$existence_args = array(
				'type'	=>	'select',
				'name'	=>	'ap_icas_construction_protected_area',
				'options'	=>	array(
						'' => __("Selecteaza", 'icas'),
						'y' => __("Da", "icas"),
						'n' => __("Nu", "icas"),
				),
				'value'	=>	$ap_icas_construction_protected_area,
				'label'	=>	__("Arie naturala protejata" , 'icas') .": "
				
			);
		
		ap_icas_get_form_element( $existence_args );
		
		echo '</p><p>';
		
		
		// Construction county	
		$county_args = array(
				'type'	=>	'select',
				'name'	=>	'ap_icas_construction_county',
				'options'	=> ap_icas_get_county_list(),
				'value'	=>	isset( $locations_terms[0]->term_id ) ? $locations_terms[0]->term_id : '',
				'label'	=>	__("Judet" , 'icas') .": "
		);
		
		ap_icas_get_form_element( $county_args );
		
		
		// Construction city
		$city_args = array(
				'name'	=>	'ap_icas_construction_city',
				'value'	=>	isset( $locations_terms[1]->name ) ? esc_attr ( $locations_terms[1]->name ) : '',
				'label'	=>	__("Localitate" , 'icas') .": "
		);
		
		ap_icas_get_form_element( $city_args );
		
		
		// Construction owner
		$owner_args = array(
				'name'	=>	'ap_icas_construction_owner',
				'value'	=>	$ap_icas_construction_owner,
				'label'	=>	__("Proprietar" , 'icas') .": "
		);
		
		ap_icas_get_form_element( $owner_args );
		echo '</p>';
		
	}
	
	
	
	// Save general data
	public static function save( $post_id, $post, $update ){
		
		// Metafields to update arr
		$metafields_arr = self::get_fields();
		
		foreach ( $metafields_arr as $metafield ){
			update_post_meta( $post_id, $metafield, strip_tags( $_POST[ $metafield ] ) );
		}
		
		
		if( $_POST['ap_icas_construction_county'] ){		
			
			$terms_arr = array();
			
			$county_term_id = (int)  $_POST['ap_icas_construction_county'];
			
			$terms_arr[] = $county_term_id;
			
			if( $_POST['ap_icas_construction_city'] ){
			
				// check if city is registered as taxonomy 			
				$city_term = term_exists( $_POST['ap_icas_construction_city'], 'icas_location', $county_term_id );
				// if not exists, create it
				if( empty( $city_term ) ){
					$new_term = wp_insert_term($_POST['ap_icas_construction_city'], 'icas_location', array('parent' => $county_term_id ) );
					
					if( is_wp_error( $new_term ) ){
					//	error_log('error insert new city term '. print_r($new_term, true) );
						return;
					}
					
					$city_term_id = $new_term['term_id'];
				}else{
					$city_term_id = $city_term['term_id'];
				}
				
				$terms_arr[] = (int) $city_term_id;
			}
			
			
			wp_set_object_terms( $post_id, $terms_arr, 'icas_location' );
		}
		
		
		
		$ys = ap_icas_calculate_ys( $_POST );
		
		update_post_meta( $post_id, 'ap_icas_construction_ys', $ys );
	}


}