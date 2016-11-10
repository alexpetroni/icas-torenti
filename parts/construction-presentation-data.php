<?php

/**
 * Display the construction data
 * 
 * @param int $id
 */
function ap_icas_get_construction_data( $id ){
	$construction_type = wp_get_post_terms( $id, 'construction_type' );
	
	if( $construction_type ){
		
		ap_icas_get_construction_general_data_table( $id );
		
		$constr_term = $construction_type[0];
		
		if( 'long' == $constr_term->slug ){
			ap_icas_get_construction_longitudinal_data_table( $id );
		}else{
			ap_icas_get_construction_transversal_data_table( $id );
		}
	} else {
		echo "<small class='error'> Lipseste declararea tipului lucrarii (longitudinal/transversal) </small>";
	}
}



/**
 * Responsable for display general construction data
 * 
 * @param int $id
 */
function ap_icas_get_construction_general_data_table( $id ){
	global $post;
	
	$meta_arr = get_post_meta( $id );

	echo '<div class="row">';
	echo '<div class="small-12 medium-6 columns">';
	// =================================================================================
	// Identification
	
	ap_icas_data_field( __( 'Ys',	'icas'), $meta_arr['ap_icas_construction_ys'] );	
	ap_icas_data_field( __( 'Tip construcție',	'icas'), ap_icas_get_term_name( 'construction_type' ) );
	ap_icas_data_field( __( 'Denumire bazin',	'icas'), $meta_arr['ap_icas_basin_name'] );
	ap_icas_data_field( __( 'Cod lucrare',	'icas'), $meta_arr['ap_icas_construction_code']);
	ap_icas_data_field( __( 'Cod cadastral',	'icas'), ap_icas_get_construction_areas() );
	
	
	ap_icas_data_field( __( 'Latitudine',	'icas' ), ap_icas_get_latitude_from_meta( $meta_arr, false ) );
	ap_icas_data_field( __( 'Longitude',	'icas' ), ap_icas_get_longitude_from_meta( $meta_arr, false ) );
	// print the script for latitude/longitude
	ap_icas_get_lat_long_script( $meta_arr );
	
	ap_icas_data_field( __( 'An construcție',	'icas' ), $meta_arr['ap_icas_construction_date'] );
	ap_icas_data_field( __( 'An inventariere',	'icas' ), $meta_arr['ap_icas_construction_review_date'] );
	
	
	$protected_area = '';
	if( ! empty( $meta_arr['ap_icas_construction_protected_area'] ) && $meta_arr['ap_icas_construction_protected_area'][0] == 'y' ){
		$protected_area = __("Da", "icas"); 
	}
	
	if( ! empty( $meta_arr['ap_icas_construction_protected_area'] ) && $meta_arr['ap_icas_construction_protected_area'][0] == 'n' ){
		$protected_area = __("Nu", "icas");
	}
	
	ap_icas_data_field( __( 'Arie protejată',	'icas' ), $protected_area );
	
	
	
	$location_terms = wp_get_post_terms( $id, 'icas_location');
	$county = $city = '&nbsp;';
	foreach ( $location_terms  as $loc ){
		if( $loc->parent == 0  ){
			$county = $loc->name;
		}else{
			$city = $loc->name;
		}
	}
	
	ap_icas_data_field( __( 'Județ',	'icas' ), $county );
	ap_icas_data_field( __( 'Localitate',	'icas' ), $city );
	
	ap_icas_data_field( __( 'Proprietar',	'icas' ), $meta_arr['ap_icas_construction_owner'] );
	
	echo '</div>';
	
	echo '<div class="small-12 medium-6 columns">';
		ap_icas_get_construction_map();
	echo '</div>';
}



/**
 * Responsable for display general construction images
 * 
 * @param int $id
 */
function ap_icas_get_construction_images( $id = null){
	global $post;
	
	if( ! $id ){
		$id = $post->ID;
	}

	$ap_icas_images = get_post_meta( $id,  'ap_icas_constr_img' );
	
		if( $ap_icas_images && ! empty ( $ap_icas_images[0] ) ){
			
			echo '<div class="row"><div class="small-12 columns">';
			echo '<ul class="single-constr-gallery">';
			foreach ( $ap_icas_images[0] as $img_id ){
				$large = wp_get_attachment_image_src( $img_id, 'large');
				$i = wp_get_attachment_image_src( $img_id, 'thumbnail');
				echo '<li class="single-constr-thumbnail"><a class="fancybox" rel="icas-group" href="'.$large[0].'">'."<img src='$i[0]' /></a></li>";
			}
			echo '</ul>';
			echo '</div></div>';
		}

}



/**
 * Responsable for display the transversal data table
 * 
 * @param int $id
 */
function ap_icas_get_construction_transversal_data_table( $id ){

	$meta_arr = get_post_meta( $id );

	//error_log('meta arr '. print_r($meta_arr, 1));

	echo '<div class="row">';
	echo '<div class="small-12 columns">';

	// ==================================================
	// Transversal construction dimensions
	// ==================================================

	ap_icas_fieldset_title( __('Elemente dimensionale', 'icas') );
	echo '
<table class="icas-table"><thead>
					<tr><td colspan="5">'.__('Lucrare propriu-zisă', 'icas').'</td>
					<td colspan="4">'.__('Radier', 'icas').'</td>
					<td  colspan="2">'.__('Confuzor', 'icas').'</td></tr>
					
						<tr>
							<td>'.__("Ye", "icas").'</td>
							<td>'.__("H", "icas").'</td>
							<td>'.__("a", "icas").'</td>
							<td>'.__("B", "icas").'</td>
							<td>'.__("tip lucrare", "icas").'</td>
							<td>'.__("Lr", "icas").'</td>
							<td>'.__("Br", "icas").'</td>
							<td>'.__("tip disip.", "icas").'</td>
							<td>'.__("Nr. total de dinti", "icas").'</td>
							<td>'.__("Lc", "icas").'</td>
							<td>'.__("Bc", "icas").'</td>
						</tr></thead>
						<tbody>';

	$trans_dim_arr = array(
			'ap_icas_trans_dim_ye',
			'ap_icas_trans_dim_h',
			'ap_icas_trans_dim_a',
			'ap_icas_trans_dim_b',
			'trans_constr_type',
			'ap_icas_trans_dim_lr',
			'ap_icas_trans_dim_br',
			'trans_disip_type',
			'ap_icas_trans_apron_teeth_total',
			'ap_icas_trans_dim_lc',
			'ap_icas_trans_dim_bc',
	);

	echo '<tr>';

	foreach ( $trans_dim_arr as $d ){
		if( $d != 'trans_constr_type' && $d != 'trans_disip_type' ){
			$val = ! empty( $meta_arr[$d][0] ) ? $meta_arr[$d][0]: '&nbsp;';
		}else{
			$val = ap_icas_get_term_name($d);
		}

		echo "<td>$val</td>";
	}
	echo '</tr>';


	echo '</tbody></table>';



	// ==================================================
	// Construction materials
	// ==================================================


	ap_icas_fieldset_title( __('Materiale de constructii', 'icas') );

	echo '<table class="icas-table"><thead>
					<tr class="icas-secondary-thead">
							<td>'.__("Corp lucrare", "icas").'</td>
							<td>'.__("Aripi lucrare", "icas").'</td>
							<td>'.__("Radier", "icas").'</td>
							<td>'.__("Contrabaraj", "icas").'</td>
							<td>'.__("Ziduri de conducere", "icas").'</td>
							<td>'.__("Pinten terminal", "icas").'</td>
					</tr>

					</thead>
						<tbody>';

	$material_taxonomies_arr = array(
			'mat_main_body',
			'mat_wings',
			'mat_apron',
			'mat_counter_dam',
			'mat_side_walls',
			'mat_final_spur'
	);

	echo '<tr>';
	foreach ( $material_taxonomies_arr as $mat ){
		echo '<td>';
		echo ap_icas_get_term_name( $mat );
		echo '</td>';
	}

	echo '</tr>';
	echo '</tbody></table>';

	// ==================================================
	// Main construction damages
	// ==================================================


	ap_icas_fieldset_title( __('Avarii lucrare propriu zisă', 'icas') );

	echo '<table class="icas-table"><thead>
					<tr>
						<td colspan="2">'.__('Decastrare', 'icas').'</td>
						<td colspan="2">'.__('Afuieri', 'icas').'</td>
						<td colspan="2">'.__('Fis, oriz, z, deversată', 'icas').'</td>
						<td colspan="2">'.__('Fis, vert, z, deversată', 'icas').'</td>
						<td colspan="2">'.__('Fis, oriz, z, nedeversată', 'icas').'</td>
						<td colspan="2">'.__('Fis, vert, z, nedeversată', 'icas').'</td>
						<td			   >'.__('Desprinderi zonă deversată', 'icas').'</td>
						<td colspan="2">'.__('Desprinderi z, nedeversată', 'icas').'</td>
						<td colspan="2">'.__('Eroziuni', 'icas').'</td>
			
					</tr>

						<tr>
							<td>'.__("stanga", "icas").'</td>
							<td>'.__("dreapta", "icas").'</td>

							<td>'.__("H", "icas").'</td>
							<td>'.__("%", "icas").'</td>

							<td>'.__("nr", "icas").'</td>
							<td>'.__("L", "icas").'</td>

							<td>'.__("nr", "icas").'</td>
							<td>'.__("L", "icas").'</td>

							<td>'.__("nr", "icas").'</td>
							<td>'.__("L", "icas").'</td>

							<td>'.__("nr", "icas").'</td>
							<td>'.__("L", "icas").'</td>

							<td>'.__("%", "icas").'</td>

							<td>'.__("stanga", "icas").'</td>
							<td>'.__("dreapta", "icas").'</td>

							<td>'.__("H", "icas").'</td>
							<td>'.__("%", "icas").'</td>
						</tr>					
						</thead>
						<tbody>';

	echo '<tr>';

	$damages_arr = array(
			'ap_icas_trans_damage_dec_left',
			'ap_icas_trans_damage_dec_right',
			'ap_icas_trans_damage_af_height',
			'ap_icas_trans_damage_af_percent',
			'ap_icas_trans_damage_h_crak_dev_nr',
			'ap_icas_trans_damage_h_crak_dev_l',
			'ap_icas_trans_damage_v_crak_dev_nr',
			'ap_icas_trans_damage_v_crak_dev_l',
			'ap_icas_trans_damage_h_crak_undev_nr',
			'ap_icas_trans_damage_h_crak_undev_l',
			'ap_icas_trans_damage_v_crak_undev_nr',
			'ap_icas_trans_damage_v_crak_undev_l',
			'ap_icas_trans_damage_detach_dev',
			'ap_icas_trans_damage_detach_undev_left',
			'ap_icas_trans_damage_detach_undev_right',
			'ap_icas_trans_damage_erosion_height',
			'ap_icas_trans_damage_erosion_percent'
	);

	foreach ( $damages_arr as $dam ){
		echo '<td>';
		$val = isset( $meta_arr[$dam][0] ) ? $meta_arr[$dam][0]: '&nbsp;';
		echo $val;
		echo '</td>';
	}


	echo '</tr>';
	echo '</tbody></table>';

	// ==================================================
	// Apron damages
	// ==================================================


	ap_icas_fieldset_title( __('Avarii radier', 'icas') );

	echo '<table class="icas-table"><thead>
					<tr>
						<td colspan="2">'.__('Fisuri', 'icas').'</td>
						<td colspan="2">'.__('Afuieri', 'icas').'</td>
						<td rowspan="2">'.__('Desprindere<br />radier', 'icas').'</td>
						<td rowspan="2">'.__('Dinţi<br />desprinsi', 'icas').'</td>
						<td rowspan="2">'.__("Desprindere<br />contrabaraj", "icas").'</td>
						<td colspan="2">'.__('Eroziuni', 'icas').'</td>
					</tr>

						<tr>
							<td>'.__("nr", "icas").'</td>
							<td>'.__("%", "icas").'</td>

							<td>'.__("H", "icas").'</td>
							<td>'.__("%", "icas").'</td>


							<td>'.__("h", "icas").'</td>
							<td>'.__("%", "icas").'</td>
						</tr>
						</thead>
						<tbody>';

	echo '<tr>';

	$apron_damages_arr = array(
			'ap_icas_trans_apron_crack_nr',
			'ap_icas_trans_apron_crack_percent',
			'ap_icas_trans_apron_af_height',
			'ap_icas_trans_apron_af_percent',
			'ap_icas_trans_apron_detach',
			'ap_icas_trans_apron_teeth_detach',
			'ap_icas_trans_apron_detach_counter_dam',
			'ap_icas_trans_apron_erosion_height',
			'ap_icas_trans_apron_erosion_percent',

	);

	foreach ( $apron_damages_arr as $dam ){
		echo '<td>';
		$val = isset( $meta_arr[$dam][0] ) ? $meta_arr[$dam][0]: '&nbsp;';
		echo $val;
		echo '</td>';
	}


	echo '</tr>';
	echo '</tbody></table>';




	// ==================================================
	// Construction sidewalls damages
	// ==================================================


	ap_icas_admin_fieldset_title( __('Avarii ziduri de conducere', 'icas') );




	echo '<table class="icas-table">
					  <thead>';


	echo ' <tr>
							<td></td>
							<td colspan="2">'.__("Fisuri orizontale", "icas").'</td>
							<td colspan="2">'.__("Fisuri verticale", "icas").'</td>
							<td>'.__("Desprinderi", "icas").'</td>
							<td colspan="2">'.__("Eroziuni", "icas").'</td>
						</tr>';

	echo '<tr>
							<td></td>
							<td>'.__("nr", "icas").'</td>
							<td>'.__("lungime(m)", "icas").'</td>
							<td>'.__("nr", "icas").'</td>
							<td>'.__("lungime(m)", "icas").'</td>
							<td>%</td>
							<td>'.__("adancime(cm)", "icas").'</td>
							<td>%</td>
						</tr>';
	echo '</thead>';

	echo '<tbody>';


	$ziduri_arr = array(
			'left' =>	__("Zid stanga", "icas"),
			'right'=>	__("Zid dreapta", "icas")
	);

	$field_prefix = 'ap_icas_trans_sidewall_';

	$sidewall_damage_fields_postfix = array(
			'horiz_craks_nr',
			'horiz_length',
			'vert_craks_nr',
			'vert_length',
			'displaced',
			'abrasion_deep',
			'abrasion_percent'
	);




	foreach ( $ziduri_arr as $key => $value ){
		echo '<tr>';
		echo '<td>'. $value .'</td>';
		foreach ( $sidewall_damage_fields_postfix as $damage ){
			$f_name = $field_prefix.$key.'_'.$damage;
			echo '<td>';
			$val = isset( $meta_arr[$f_name][0] ) ? $meta_arr[$f_name][0]: '&nbsp;';
			echo $val;
			echo '</td>';
		}

		echo '</tr>';
	}

	echo ' </tbody></table>';




	// ==================================================
	// Final spur damages
	// ==================================================

	ap_icas_fieldset_title( __('Avarii pinten terminal', 'icas') );

	echo '<table class="icas-table"><thead>
					<tr>
						<td colspan="2">'.__('Decastrare', 'icas').'</td>
						<td colspan="2">'.__('Fisuri orizontale', 'icas').'</td>
						<td colspan="2">'.__('Fisuri verticale', 'icas').'</td>
						<td colspan="3">'.__('Desprinderi', 'icas').'</td>
						<td colspan="2">'.__('Eroziuni', 'icas').'</td>
					</tr>
						<tr>
							<td>'.__("stanga", "icas").'</td>
							<td>'.__("dreapta", "icas").'</td>


							<td>'.__("nr", "icas").'</td>
							<td>'.__("lungime", "icas").'</td>

							<td>'.__("nr", "icas").'</td>
							<td>'.__("lungime", "icas").'</td>

							<td>'.__("stanga", "icas").'</td>
							<td>'.__("dreapta", "icas").'</td>
							<td>'.__("central", "icas").'</td>

							<td>'.__("h", "icas").'</td>
							<td>'.__("%", "icas").'</td>
						</tr>
						</thead>
						<tbody>';

	echo '<tr>';

	$apron_damages_arr = array(
			'ap_icas_trans_final_spur_decastr_left',
			'ap_icas_trans_final_spur_decastr_right',
			'ap_icas_trans_final_spur_horiz_crack_nr',
			'ap_icas_trans_final_spur_horiz_crack_length',
			'ap_icas_trans_final_spur_vert_crack_nr',
			'ap_icas_trans_final_spur_vert_crack_length',
			'ap_icas_trans_final_spur_detach_left',
			'ap_icas_trans_final_spur_detach_right',
			'ap_icas_trans_final_spur_detach_center',
			'ap_icas_trans_final_spur_erosion_height',
			'ap_icas_trans_final_spur_erosion_percent'
	);

	foreach ( $apron_damages_arr as $dam ){
		echo '<td>';
		$val = isset( $meta_arr[$dam][0] ) ? $meta_arr[$dam][0]: '&nbsp;';
		echo $val;
		echo '</td>';
	}


	echo '</tr>';
	echo '</tbody></table>';


	// ==================================================
	// Disfunctionalities
	// ==================================================

	ap_icas_fieldset_title( __('Disfuncţionalităţi', 'icas') );

	echo '<table class="icas-table"><thead>
					<tr>
						<td>'.__('Colmatare deversor', 'icas').'</td>
						<td colspan="2">'.__('Colmatare radier', 'icas').'</td>
						<td>'.__('Inaltime aterisament', 'icas').'</td>
						<td>'.__('Granulometrie aluviuni', 'icas').'</td>
						<td colspan="2">'.__('Vegetatie lemnoasa nedorita', 'icas').'</td>
						<td>'.__('Reducere sectiune', 'icas').'</td>
					</tr>
						<tr>
							<td>'.__("%SU", "icas").'</td>

							<td>'.__("%SU", "icas").'</td>
							<td>'.__("%Srad", "icas").'</td>

							<td>'.__('Hat (m)', 'icas').'</td>

							<td>'.__('Gal', 'icas').'</td>

							<td>'.__("amonte", "icas").'</td>

							<td>'.__("aval", "icas").'</td>

							<td>'.__("aval", "icas").'</td>
						</tr>
						</thead>
						<tbody>';

	echo '<tr>';

	$apron_damages_arr = array(
			'ap_icas_trans_disf_colmat_deversor',
			'ap_icas_trans_disf_colmat_apron_su',
			'ap_icas_trans_disf_colmat_apron_srad',
			'ap_icas_trans_disf_hat',
			'trans_gal_type',
			'ap_icas_trans_disf_veget_amonte',
			'ap_icas_trans_disf_veget_aval',
			'ap_icas_trans_disf_section_dim'
	);

	foreach ( $apron_damages_arr as $dam ){
			
		if( $dam == 'trans_gal_type' ){
			$val = ap_icas_get_term_name( $dam );;
		}else{
			$val = isset( $meta_arr[$dam][0] ) ? $meta_arr[$dam][0]: '&nbsp;';
		}
		echo '<td>';
		echo $val;
		echo '</td>';
	}


	echo '</tr>';
	echo '</tbody></table>';

	echo '</div>';
	echo '</div>';
}





/**
 * Responsable for display 
 * 
 * @param int $id the post id
 */
function ap_icas_get_construction_longitudinal_data_table( $id ){
	
	$children_arr = get_children( array( 'post_parent' => $id, 'post_type' => 'construction_sector', 'order' => 'ASC') );
	
	//error_log(' $children '. print_r($children_arr, 1));
	
	$sectors_arr = array();
	
	if( $children_arr ){
		foreach ( $children_arr as $c ){
			$sectors_arr[] = new Icas_Construction_Sector( $c->ID );
		}
	}
	
	$total_l = get_post_meta( $id, 'ap_icas_long_total_length', true );
	
	if( '' == $total_l ){
		$total_l = ' - ';
	}else{
		$total_l .= ' m';
	}
	echo '<div class="icas-data-wrapper">
		  <span class="icas-data-label">'.__("Lungime totală").'</span>
		  <span class="icas-data-val">'.$total_l.'</span>
	</div>';
	echo '<div class="icas-data-wrapper">
		  <span class="icas-data-label">'.__("Număr sectoare").'</span>
		  <span class="icas-data-val">'.count( $sectors_arr ).'</span>
	</div>';
	
	
	$dimension_table_fields =  array(
			'ap_icas_long_cons_stairs'	=> __("Nr. trepte" , 'icas'),
			'ap_icas_long_cons_length'	=> __("Lungime" , 'icas'),
			'ap_icas_long_cons_deep'	=> __("Adancime" , 'icas'),
			'ap_icas_long_cons_width_apron'	=> __("Latime radier" , 'icas'),
			'ap_icas_long_cons_fruit_guard_wall'	=> __("Fruct zid garda" , 'icas')
	);
	
	
	$constr_materials_fields = array(
			'mat_sect_apron'	=> __("Radier" , 'icas'),
			'mat_sect_walls'	=> __("Ziduri garda" , 'icas'),
			'mat_sect_spur'		=> __("Pinteni" , 'icas')
	);
	
	
	$apron_damage_fields = array(
					'ap_icas_long_apron_craks_nr',
					'ap_icas_long_apron_damage_percent',
					'ap_icas_long_apron_displaced',
					'ap_icas_long_apron_abrasion_deep',
					'ap_icas_long_apron_abrasion_percent',
	);
	
	
	$ziduri_arr = array(
			'left' =>	__("Zid stanga", "icas"),
			'right'=>	__("Zid dreapta", "icas")
	);
	
	$sidewall_damage_fields_postfix = array(
			'horiz_craks_nr',
			'horiz_length',
			'vert_craks_nr',
			'vert_length',
			'displaced',
			'abrasion_deep',
			'abrasion_percent'
	);
	
	
	$disf_damage_fields = array(
			'ap_icas_long_disfunctio_su',
			'ap_icas_long_disfunctio_srad',
			'ap_icas_long_disfunctio_sect_aval'
	);
	
	
	$spur_damage_fields = Icas_Construction_Sector::get_spur_fields_names();
	
	
	
	for( $i = 0; $i < count( $sectors_arr ); $i++ ){
		
		$s = $sectors_arr[$i];
		
		echo '<div class="row">';
		
			echo '<div class="small-12 columns">';		
				echo '<div class="sector_title"><span> '. sprintf(__("Sector %s", 'icas'), $s->get_field('ap_icas_long_cons_sector') ).' </span></div>';
				
				// Tabel Elemente dimensionale
				echo '<div class="fieldset-title">'.__("Elemente dimensionale", "icas").'</div>';
				echo '<table class="icas-table">';
					echo '<thead><tr>';
						foreach ( $dimension_table_fields as $k => $v ){
							echo '<td>'. $v . '</td>';
						}					
					echo '</tr></thead>';
					
					echo '<tbody><tr>';
						foreach ( $dimension_table_fields as $k => $v ){
							echo '<td>'.$s->get_field( $k ) . '</td>';
						}
					echo '</tr></tbody>';
					
					
				echo '</table>';
				
				
				// Tabel materiale de constructie
				echo '<div class="fieldset-title">'.__( 'Materiale de constructie', "icas").'</div>';
				echo '<table class="icas-table">';
				echo '<thead><tr>';
				foreach ( $constr_materials_fields as $k => $v ){
					echo '<td>'. $v . '</td>';
				}
				echo '</tr></thead>';
					
				echo '<tbody><tr>';
				foreach ( $constr_materials_fields as $k => $v ){
					echo '<td>'.ap_icas_get_term_name( $k, $s->id ) . '</td>';
				}
				echo '</tr></tbody>';
					
					
				echo '</table>';
				
				// Tabel avarii radier
				echo '<div class="fieldset-title">'.__( 'Avarii radier', "icas").'</div>';
				echo '<table class="icas-table"><thead><tr><td colspan="2">'.__("Fisuri", "icas").'</td><td>'.__("Desprinderi", "icas").'</td><td  colspan="2">'.__("Eroziuni", "icas").'</td></tr>
					
					<tr>
						<td>'.__("nr", "icas").'</td>
						<td>'.__("% afectat", "icas").'</td>
						<td>'.__("% desprins", "icas").'</td>
						<td>'.__("adancime (cm)", "icas").'</td>
						<td>'.__("% afectat", "icas").'</td>
					</tr></thead>';
					
				echo '<tbody><tr>';
				foreach ( $apron_damage_fields as $v ){
					echo '<td>'.$s->get_field( $v ) . '</td>';
				}
				echo '</tr></tbody>';
					
					
				echo '</table>';
				
				// Tabel avarii ziduri de conducere
				echo '<div class="fieldset-title">'.__('Avarii ziduri de conducere', 'icas').'</div>';
				echo '<table class="icas-table">
				  <thead>';
					
				
				echo ' <tr>
						<td rowspan="2"></td>
						<td colspan="2">'.__("Fisuri orizontale", "icas").'</td>
						<td colspan="2">'.__("Fisuri verticale", "icas").'</td>
						<td>'.__("Desprinderi", "icas").'</td>
						<td colspan="2">'.__("Eroziuni", "icas").'</td>
					</tr>';
				
				echo '<tr  class="icas-secondary-thead">
						<td>'.__("nr", "icas").'</td>
						<td>'.__("lungime(m)", "icas").'</td>
						<td>'.__("nr", "icas").'</td>
						<td>'.__("lungime(m)", "icas").'</td>
						<td>%</td>
						<td>'.__("adancime(cm)", "icas").'</td>
						<td>%</td>
					</tr>';
				echo '</thead>';
				
				echo '<tbody>';
				
					
				$field_prefix = 'ap_icas_long_sidewall_';			
					
					
				foreach ( $ziduri_arr as $key => $value ){
					echo '<tr>';
					echo '<td>'. $value .'</td>';
					foreach ( $sidewall_damage_fields_postfix as $damage ){
						$f_name = $field_prefix.$key.'_'.$damage;
						echo '<td>'. $s->get_field($f_name) .'</td>';
					}
				
					echo '</tr>';
				}
					
				echo ' </tbody>
					</table>';
				
				
				
				// Tabel avarii pinten
				echo '<div class="fieldset-title">'.__('Avarii pinten', 'icas').'</div>';
				echo '<table class="icas-table">
					<thead>
					<tr>
						<td rowspan="2">'.__("Nr. pinten", "icas").'</td>
						<td colspan="2">'.__("Decastrare", "icas").'</td>
						<td colspan="2">'.__("Afuieri", "icas").'</td>
						<td colspan="2">'.__("Fisuri orizontale", "icas").'</td>
						<td colspan="2">'.__("Fisuri verticale", "icas").'</td>
						<td colspan="3">'.__("Desprinderi", "icas").'</td>
						<td colspan="2">'.__("Eroziuni", "icas").'</td>
					</tr>
		
					<tr>
						
						<td>'.__("stanga", "icas").'</td>
						<td>'.__("dreapta", "icas").'</td>
						<td>'.__("H", "icas").'</td>
						<td>'.__("%", "icas").'</td>
						<td>'.__("nr", "icas").'</td>
						<td>'.__("lungime(m)", "icas").'</td>
						<td>'.__("nr", "icas").'</td>
						<td>'.__("lungime(m)", "icas").'</td>
						<td>'.__("stanga", "icas").'</td>
						<td>'.__("dreapta", "icas").'</td>
						<td>'.__("centru", "icas").'</td>
						<td>'.__("H", "icas").'</td>
						<td>%</td>
					</tr>
					</thead>
					<tbody>';
				echo '<tr>';
					

				
				$spur_number = $s->get_spur_numbers() > 0 ? $s->get_spur_numbers() : 1 ;
					
				for ( $j = 0; $j < $spur_number; $j++ ){
						
					foreach ( $spur_damage_fields as $damage ){
						echo '<td>';
						echo $s->get_spur_field( $damage, $j);
						echo '</td>';
					}
					// first table row doesn't have remove btn
					echo '</td>';
					echo '</tr>';
				}
				echo ' </tbody>
					</table>';

				
				
			echo '</div>';	
		echo '</div>'; // .row
	}
	
	



	// ==================================================
	// Disfunctionalities
	// ==================================================

	ap_icas_fieldset_title( __('Disfuncţionalităţi', 'icas') );

	echo '<table class="icas-table">
			<thead>
					<tr>
						<td colspan="2">'.__("Colmatare radier", "icas").'</td>
						<td>'.__("Reducere sect. aval", "icas").'</td>
					</tr>

					<tr  class="icas-secondary-thead">
						<td>'.__("%SU", "icas").'</td>
						<td>'.__("%Srad", "icas").'</td>
						<td>%</td>
					</tr>
					</thead>
					<tbody>';	

		echo '<tr>';



			foreach ( $disf_damage_fields as $damage ){			
				echo '<td>';
				echo $s->get_field( $damage );
				echo '</td>';
			}


		echo '</tr>';
	echo '</tbody></table>';
}



/**
 * Display the map on single construction page
 * It use cons_lat && cons_long js variables defined already in code
 */
function ap_icas_get_construction_map(){
	?>
	<div id="single-construction-map-container">
	<div id="single-construction-map"></div>
	</div>
	<script>
	var map;
	var have_marker = false;
	var construction_pos =  {lat: 45.75, lng: 24.40}
	var map_zoom = 6;
	 
	// if are set longitude and latitude
	if( cons_lat && cons_long ){
		construction_pos = {lat: cons_lat, lng: cons_long};
		have_marker = true;
		map_zoom = 9;
	}
	
	function initMap() {
		map = new google.maps.Map(document.getElementById('single-construction-map'), {
			center: construction_pos,
			mapTypeId: google.maps.MapTypeId.HYBRID,
			fullscreenControl: true,
			zoom: map_zoom
		});
	
			if( have_marker ){
				var marker = new google.maps.Marker({
					position: construction_pos,
					map: map,
					title: cons_basin_name
				});
			}
	}
	
	
	
	</script>
	<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyA0Gh987mNFsoIuc6XAX6-HVdG3Wl6j3cA&callback=initMap"
			async defer></script>
<?php 
}
