<?php
// Longitudinal construction data metabox

class Icas_Construction_Transversal_Data_Metabox{
	
	
	public static function get_fields(){
			
		$fields= array(
				'ap_icas_trans_dim_ye',
				'ap_icas_trans_dim_h',
				'ap_icas_trans_dim_a',
				'ap_icas_trans_dim_b',				
				'ap_icas_trans_dim_lr',
				'ap_icas_trans_dim_br',
				'ap_icas_trans_dim_lc',
				'ap_icas_trans_dim_bc',
				'ap_icas_trans_apron_teeth_total',
				
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
				'ap_icas_trans_damage_erosion_percent',

				'ap_icas_trans_apron_crack_nr',
				'ap_icas_trans_apron_crack_percent',
				'ap_icas_trans_apron_af_height',
				'ap_icas_trans_apron_af_percent',
				'ap_icas_trans_apron_detach',
				'ap_icas_trans_apron_teeth_detach',
				'ap_icas_trans_apron_detach_counter_dam',
				'ap_icas_trans_apron_erosion_percent',
				'ap_icas_trans_apron_erosion_height',
				
				'ap_icas_trans_sidewall_left_horiz_craks_nr',
				'ap_icas_trans_sidewall_left_horiz_length',
				'ap_icas_trans_sidewall_left_vert_craks_nr',
				'ap_icas_trans_sidewall_left_vert_length',
				'ap_icas_trans_sidewall_left_displaced',
				'ap_icas_trans_sidewall_left_abrasion_deep',
				'ap_icas_trans_sidewall_left_abrasion_percent',
				
				'ap_icas_trans_sidewall_right_horiz_craks_nr',
				'ap_icas_trans_sidewall_right_horiz_length',
				'ap_icas_trans_sidewall_right_vert_craks_nr',
				'ap_icas_trans_sidewall_right_vert_length',
				'ap_icas_trans_sidewall_right_displaced',
				'ap_icas_trans_sidewall_right_abrasion_deep',
				'ap_icas_trans_sidewall_right_abrasion_percent',
				
				
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
				'ap_icas_trans_final_spur_erosion_percent',
				
				
				'ap_icas_trans_disf_colmat_deversor',
				'ap_icas_trans_disf_colmat_apron_su',
				'ap_icas_trans_disf_colmat_apron_srad',
				'ap_icas_trans_disf_hat',
			//	'ap_icas_trans_disf_gal',
				'ap_icas_trans_disf_veget_amonte',
				'ap_icas_trans_disf_veget_aval',
				'ap_icas_trans_disf_section_dim'
		);
			
		return $fields;
	}
	
	
	public static function get_material_term_fields(){
		return array(				
				'mat_main_body',
				'mat_wings',
				'mat_apron',
				'mat_counter_dam',
				'mat_side_walls',
				'mat_final_spur'				
		);
	}
	
	
	public static function get_dimensional_term_fields(){
		return array(
				'trans_disip_type',
				'trans_constr_type'
		);
	}
	
	public static function get_disfunctional_term_fields(){
		return array(
				'trans_gal_type'
		);
	}
	
	
	public static function output( $post ){
	
		if( !isset( $post ) || !isset( $post->ID ) ) return;
		
		
		// get metadata
		$metafields_arr = self::get_fields();		
		
		$meta_arr = ap_icas_get_post_meta( $post->ID, $metafields_arr );
		
		
		// get construction materials
		$material_taxonomies_arr = self::get_material_term_fields();
		
		// fill it first with default values
		$terms_arr = array_fill_keys( $material_taxonomies_arr, '');
		
		// request construction material terms
		$mat_terms_arr = wp_get_post_terms( $post->ID, $material_taxonomies_arr );		
		
		foreach ( $mat_terms_arr as $tax ){
			if( isset( $terms_arr[$tax->taxonomy] ) ){
				$terms_arr[$tax->taxonomy] = $tax->term_id;
			}
		}
		
		// get disip type value
		$trans_disip_type = '';
		
		$disip_terms_arr = wp_get_post_terms( $post->ID, 'trans_disip_type' );		
		
		if( ! is_wp_error($disip_terms_arr) && $disip_terms_arr ){
			$trans_disip_type = $disip_terms_arr[0]->term_id;
		}
		
		
		
		// get construction type value
		$trans_constr_type = '';
		
		$constr_type_terms_arr = wp_get_post_terms( $post->ID, 'trans_constr_type' );
		
		if( ! is_wp_error( $constr_type_terms_arr ) && $constr_type_terms_arr ){
			$trans_constr_type = $constr_type_terms_arr[0]->term_id;
		}
		
		
		// get disip type value
		$trans_gal_type = '';
		
		$gal_terms_arr = wp_get_post_terms( $post->ID, 'trans_gal_type' );
		
		if( ! is_wp_error( $gal_terms_arr ) && $gal_terms_arr ){
			$trans_gal_type = $gal_terms_arr[0]->term_id;
		}
		
		
		// ==================================================
		// Transversal construction dimensions
		// ==================================================
			
		echo '<p>';
		ap_icas_admin_fieldset_title( __('Elemente dimensionale', 'icas') );
		
		echo '<table class="icas-admin-table"><thead>
				<tr><td colspan="5">'.__('Lucrare propriu-zisă', 'icas').'</td>
				<td colspan="4">'.__('Radier', 'icas').'</td>
				<td  colspan="2">'.__('Confuzor', 'icas').'</td></tr>
				</thead>
					<tbody>
					<tr>
						<td>'.__("Ye", "icas").' (m)</td>
						<td>'.__("H", "icas").' (m)</td>
						<td>'.__("a", "icas").' (m)</td>
						<td>'.__("B", "icas").' (m)</td>
						<td>'.__("tip lucrare", "icas").'</td>
						<td>'.__("Lr", "icas").' (m)</td>
						<td>'.__("Br", "icas").'  (m)</td>
						<td>'.__("tip disip.", "icas").'</td>
						<td>'.__("Nr. total de dinti", "icas").'</td>	
						<td>'.__("Lc", "icas").'  (m)</td>
						<td>'.__("Bc", "icas").'  (m)</td>
					</tr>';
		
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
		
		foreach ( $trans_dim_arr as $dim ){
			$d = array(
					'name'	=>	$dim,
					'id'	=>	$dim,
					'size'	=> 3,
					'label'	=>	""
			);
			
			// except disip_type, which is a taxonomy and presented as a select 
			if( 'trans_disip_type' == $dim ) {
				$d['type'] = "select";
				$d['size'] = "1";
				$d['id'] = "trans_disip_type";
				$d['options'] = ap_icas_get_disip_type_list();
				$d['value'] = $trans_disip_type;
			}elseif ('trans_constr_type' == $dim ){
				$d['type'] = "select";
				$d['size'] = "1";
				$d['options'] = ap_icas_get_trans_constr_type_list();
				$d['value'] = $trans_constr_type;
			}else{
				$d['value']	=	$meta_arr[$dim];
			}
			
			echo '<td>';
			ap_icas_get_form_element( $d );
			echo '</td>';
		}
		
		echo '</tr>';
		echo '</tbody></table>';
		
		
		// Add marker for "Placa disipatoare" 
		
		$disip_board = get_term_by( 'name', __("Placa disipatoare", "icas"), 'trans_disip_type');
		$disip_board_id = 0;
		if(! is_wp_error( $disip_board ) ){
			$disip_board_id = $disip_board->term_id;			
		}
		
		$disip_board_hidden = array(
				'type'	=> 'hidden',
				'value'	=> $disip_board_id,
				'name'	=> 'disip_board',
				'id' 	=> 'disip_board'
		);
		
		ap_icas_get_form_element( $disip_board_hidden );
		
		echo '</p>';
		
		
		
		// ==================================================
		// Construction materials
		// ==================================================
			
		echo '<p>';
		ap_icas_admin_fieldset_title( __('Materiale de constructii', 'icas') );
		
		echo '<table class="icas-admin-table"><thead>
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
		
		
		
		foreach ( $material_taxonomies_arr as $mat ){
			echo '<td>';
			$d = array(
					'type'	=>  'select',
					'name'	=>	$mat,
					'value'	=>	$terms_arr[ $mat ],
					'options' => ap_icas_get_taxonomy_terms_as_options( $mat ),
					'label'	=>	""
			);
			ap_icas_get_form_element( $d );
			echo '</td>';
		}
		
		echo '</tr>';
		echo '</tbody></table>';
		echo '</p>';
		
		
		// ==================================================
		// Main construction damages
		// ==================================================
		
		echo '<p>';
		ap_icas_admin_fieldset_title( __('Avarii lucrare propriu zisă', 'icas') );
		
		echo '<table class="icas-admin-table"><thead>
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
				</thead>
					<tbody>
					<tr>
						<td>'.__("stanga", "icas").' (m)</td>
						<td>'.__("dreapta", "icas").' (m)</td>
								
						<td>'.__("H", "icas").' (m)</td>
						<td>'.__("%", "icas").'</td>
								
						<td>'.__("nr", "icas").'</td>
						<td>'.__("L", "icas").' (m)</td>
								
						<td>'.__("nr", "icas").'</td>
						<td>'.__("L", "icas").' (m)</td>
								
						<td>'.__("nr", "icas").'</td>
						<td>'.__("L", "icas").' (m)</td>
								
						<td>'.__("nr", "icas").'</td>
						<td>'.__("L", "icas").' (m)</td>
								
						<td>'.__("%", "icas").'</td>
								
						<td>'.__("% stanga", "icas").'</td>
						<td>'.__("% dreapta", "icas").'</td>								
								
						<td>'.__("h", "icas").'  (cm)</td>
						<td>'.__("%", "icas").'</td>
					</tr>';
		
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
			$d = array(
					'name'	=>	$dam,
					'value'	=>	$meta_arr[$dam],
					'size'	=> 1,
					'label'	=>	""
			);
			echo '<td>';
			ap_icas_get_form_element( $d );
			echo '</td>';
		}
		
		
		echo '</tr>';
		echo '</tbody></table>';
		echo '</p>';
		
		
		
		// ==================================================
		// Apron damages
		// ==================================================
		
		echo '<p>';
		ap_icas_admin_fieldset_title( __('Avarii radier', 'icas') );
		
		echo '<table class="icas-admin-table"><thead>
				<tr>
					<td colspan="2">'.__('Fisuri', 'icas').'</td>
					<td colspan="2">'.__('Afuieri', 'icas').'</td>
					<td>'.__('Desprindere<br> radier', 'icas').'</td>
					<td>'.__('Dinţi<br> desprinși', 'icas').'</td>
					<td>'.__("Desprindere<br> contrabaraj", "icas").'</td>
					<td colspan="2">'.__('Eroziuni', 'icas').'</td>				
				</tr>
				</thead>
					<tbody>
					<tr>
						<td>'.__("nr", "icas").'</td>
						<td>'.__("%", "icas").'</td>
								
						<td>'.__("H", "icas").' (m)</td>
						<td>'.__("%", "icas").'</td>
								
						<td>%</td>
		
						<td>'.__("nr", "icas").'</td>
									
						<td>%</td>
		
						<td>'.__("h", "icas").' (cm)</td>
						<td>'.__("%", "icas").'</td>
					</tr>';
		
		echo '<tr>';
		
		$apron_damages_arr = array(
				'ap_icas_trans_apron_crack_nr',
				'ap_icas_trans_apron_crack_percent',
				'ap_icas_trans_apron_af_height',
				'ap_icas_trans_apron_af_percent',
				'ap_icas_trans_apron_detach',
				'ap_icas_trans_apron_teeth_detach',
				'ap_icas_trans_apron_detach_counter_dam',
				'ap_icas_trans_apron_erosion_percent',
				'ap_icas_trans_damage_erosion_height'
		);
		
		foreach ( $apron_damages_arr as $dam ){
			$d = array(
					'name'	=>	$dam,
					'id'	=>	$dam,
					'value'	=>	$meta_arr[$dam],
					'size'	=> 3,
					'label'	=>	""
			);
			echo '<td>';
			ap_icas_get_form_element( $d );
			echo '</td>';
		}
		
		
		echo '</tr>';
		echo '</tbody></table>';
		echo '</p>';
		
		
		
		
		// ==================================================
		// Construction sidewalls damages
		// ==================================================
			
		echo '<p>';
		ap_icas_admin_fieldset_title( __('Avarii ziduri de conducere', 'icas') );
			
		
			
			
		echo '<table class="icas-admin-table">
				  <thead>';
			
		
		echo ' <tr>
						<td></td>
						<td colspan="2">'.__("Fisuri orizontale", "icas").'</td>
						<td colspan="2">'.__("Fisuri verticale", "icas").'</td>
						<td>'.__("Desprinderi", "icas").'</td>
						<td colspan="2">'.__("Eroziuni", "icas").'</td>
					</tr>';
		
		echo '<tr  class="icas-secondary-thead">
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
			echo '<td style="font-weight:bold">'. $value .'</td>';
			foreach ( $sidewall_damage_fields_postfix as $damage ){
				$f_name = $field_prefix.$key.'_'.$damage;
				$d = array(
						'name'	=>	$f_name,
						'value'	=>	$meta_arr[$f_name],
						'size'	=> 3,
						'label'	=>	""
				);
				echo '<td>';
				ap_icas_get_form_element( $d );
				echo '</td>';
			}
		
			echo '</tr>';
		}
			
		echo ' </tbody>
					</table>';
			
		echo '</p>';
		
		
		
		// ==================================================
		// Final spur damages
		// ==================================================
		
		echo '<p>';
		ap_icas_admin_fieldset_title( __('Avarii pinten terminal', 'icas') );
		
		echo '<table class="icas-admin-table"><thead>
				<tr>
					<td colspan="2">'.__('Decastrare', 'icas').'</td>
					<td colspan="2">'.__('Fisuri orizontale', 'icas').'</td>
					<td colspan="2">'.__('Fisuri verticale', 'icas').'</td>
					<td colspan="3">'.__('Desprinderi', 'icas').'</td>
					<td colspan="2">'.__('Eroziuni', 'icas').'</td>
				</tr>
				</thead>
					<tbody>
					<tr>
						<td>'.__("stânga", "icas").' (m)</td>
						<td>'.__("dreapta", "icas").' (m)</td>
								
								
						<td>'.__("nr", "icas").'</td>
						<td>'.__("lungime", "icas").' (m)</td>
								
						<td>'.__("nr", "icas").'</td>
						<td>'.__("lungime", "icas").' (m)</td>
		
						<td>'.__("stânga", "icas").' %</td>
						<td>'.__("dreapta", "icas").' %</td>
						<td>'.__("central", "icas").' %</td>
		
						<td>'.__("h", "icas").' (cm)</td>
						<td>'.__("%", "icas").'</td>
					</tr>';
		
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
			$d = array(
					'name'	=>	$dam,
					'value'	=>	$meta_arr[$dam],
					'size'	=> 3,
					'label'	=>	""
			);
			echo '<td>';
			ap_icas_get_form_element( $d );
			echo '</td>';
		}
		
		
		echo '</tr>';
		echo '</tbody></table>';
		echo '</p>';
		
		
		
		// ==================================================
		// Disfunctionalities
		// ==================================================
		
		echo '<p>';
		ap_icas_admin_fieldset_title( __('Disfuncţionalităţi', 'icas') );
		
		echo '<table class="icas-admin-table"><thead>
				<tr>
					<td>'.__('Colmatare deversor', 'icas').'</td>
					<td colspan="2">'.__('Colmatare radier', 'icas').'</td>
					<td>'.__('Înaltime aterisament', 'icas').'</td>
					<td>'.__('Granulometrie aluviuni', 'icas').'</td>
					<td colspan="2">'.__('Vegetație lemnoasă nedorită', 'icas').'</td>
					<td>'.__('Reducere secțiune', 'icas').'</td>
				</tr>
				</thead>
					<tbody>
					<tr>
						<td>'.__("%SU", "icas").'</td>		
		
						<td>'.__("%SU", "icas").'</td>
						<td>'.__("%Srad", "icas").'</td>
		
						<td>'.__('Hat (m)', 'icas').'</td>
								
						<td>'.__('Gal', 'icas').'</td>
		
						<td>'.__("amonte (1-5)", "icas").'</td>
								
						<td>'.__("aval (1-5)", "icas").'</td>
								
						<td>'.__("aval", "icas").' %</td>
					</tr>';
		
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
			
			if( $dam != 'trans_gal_type' ){
				$d = array(
						'name'	=>	$dam,
						'value'	=>	$meta_arr[$dam],
						'size'	=> 3,
						'label'	=>	""
				);
			}else{
				$d = array(
						'type' 	=>  "select",
						'name'	=>	$dam,
						'value'	=>	$trans_gal_type,
						'options' => ap_icas_get_taxonomy_terms_as_options( 'trans_gal_type' ),
						'label'	=>	""
				);
			}
			echo '<td>';
			ap_icas_get_form_element( $d );
			echo '</td>';
		}
		
		
		echo '</tr>';
		echo '</tbody></table>';
		echo '</p>';
			
	}
	
	
	
	// Save transversal data
	public static function save( $post_id, $post, $update ){
		
		// Metafields to update arr
		$metafields_arr = self::get_fields();
		
		// if it is a long type, do nothing, except if is an update which has changed the construction type
		if( $_POST['ap_icas_construction_type'] == 'long' ){
			
			//if it is a longitudinal construction update which before was a transversal one, delete all transversal meta if were inserted
			if( $update && ! empty($_POST['original_construction_type']) && $_POST['ap_icas_construction_type'] != $_POST['original_construction_type'] ){
				// get post children of type 'construction_sector'
				foreach ( $metafields_arr as $meta ){
					delete_post_meta( $post_id, $meta );
				}	

				// delete material terms
				$material_terms_fields_arr = self::get_material_term_fields();
				
				foreach ( $material_terms_fields_arr as $mat ){
					$ids_arr = wp_get_post_terms( $post_id, $mat , array( 'fields' => 'ids') );
					if( ! is_wp_error( $ids_arr ) ){
						wp_remove_object_terms($post_id , $ids_arr , $mat );
					}
				}
				
			}
				
			return;
		}
		

		// update the metafields
		foreach ( $metafields_arr as $metafield ){
			update_post_meta( $post_id, $metafield, strip_tags( $_POST[ $metafield ] ) );
		}
		
		// Materials terms which have the same names as the terms they store
		$material_terms_fields_arr = self::get_material_term_fields();
		
		foreach ( $material_terms_fields_arr as $mat ){
			
			if( isset( $_POST[$mat] ) && ! empty( $_POST[$mat] ) ){							
				wp_set_object_terms( $post_id, (int) $_POST[$mat] , $mat );				
			}else{
				$ids_arr = wp_get_post_terms( $post_id, $mat , array( 'fields' => 'ids') );
				
				if( ! is_wp_error( $ids_arr ) ){
					wp_remove_object_terms($post_id , $ids_arr , $mat );
				}
			}
		}
		
		
		// Disip type 
		$disip_terms_fields_arr = self::get_dimensional_term_fields();
		
		foreach ( $disip_terms_fields_arr as $mat ){
			
			if( isset( $_POST[$mat] ) && ! empty( $_POST[$mat] ) ){						
				wp_set_object_terms( $post_id, (int) $_POST[$mat] , $mat );				
			}else{
				$ids_arr = wp_get_post_terms( $post_id, $mat , array( 'fields' => 'ids') );
				
				if( ! is_wp_error( $ids_arr ) ){
					wp_remove_object_terms($post_id , $ids_arr , $mat );
				}
			}
		}
		
		
		// Gal type
		$disfunctional_terms_fields_arr = self::get_disfunctional_term_fields();
		
		foreach ( $disfunctional_terms_fields_arr as $disf ){
				
			if( isset( $_POST[$disf] ) && ! empty( $_POST[$disf] ) ){
				wp_set_object_terms( $post_id, (int) $_POST[$disf] , $disf );
			}else{
				$ids_arr = wp_get_post_terms( $post_id, $disf , array( 'fields' => 'ids') );
		
				if( ! is_wp_error( $ids_arr ) ){
					wp_remove_object_terms($post_id , $ids_arr , $disf );
				}
			}
		}
		
	}
	
}