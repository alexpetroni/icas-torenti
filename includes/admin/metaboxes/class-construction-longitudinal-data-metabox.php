<?php
// Longitudinal construction data metabox error_log
// Radier == Apron
// Pinten == Spur

class Icas_Construction_Longitudinal_Data_Metabox{
	
	
	
	public static function output( $post ){
		
		if( !isset( $post ) || !isset( $post->ID ) ) return;
		
		// get post children of type 'construction_sector'
		$child_sectors_args = array(
				'post_parent'	=> $post->ID,
				'post_type'		=> 'construction_sector',
				'order' => 'ASC'
		);
		
		$total_length = '';
		
		$children_arr = get_children( $child_sectors_args );

		
		$sectors_arr = array();
		
		if( ! empty( $children_arr )){
			foreach ( $children_arr as $c ){
				$sectors_arr[] = new Icas_Construction_Sector( $c->ID );
			}
			
			$tot_l = get_post_meta( $post->ID, 'ap_icas_long_total_length', true );
			
		}else{
			$sectors_arr[] = new Icas_Construction_Sector();
		}
		
		if( ! empty( $tot_l ) ){
			$total_length = '<span class="total_lenght">'.  __("Lungime totala", "icas"). ' '.$tot_l. 'm</span>' ;
		}
		
		
		
		// displayed title for new sector
		$new_sector_title = __("Sector nou", "icas");
		
		echo '<div class="add_sector_btn_wrapper">'.$total_length.'<input type="button" class="button button-primary button-large" value="Adauga sector" id="add_long_sector_btn" name="add_long_sector_btn"></div>';
		
		echo '<div id="long_sector_collection_wrapper" class="long_sector_collection_wrapper" data-sector-numbers="'.count($sectors_arr).'"  data-new-sector-title="'.$new_sector_title.'">';
		
		$existent_sectors = array();
		
		// construct sectors
		for ( $i = 0; $i < count( $sectors_arr ); $i++ ){
			
			$s = $sectors_arr[ $i ];
			// grab already existent sectors
			$existent_sectors[] = $s->id;
			
			$sector_title = $s->id != 0 ? sprintf(__("Sector %d", 'icas') ,$s->get_field('ap_icas_long_cons_sector')) : $new_sector_title;
			
			// if is a new post, show the new sector open, otherwise show them closed
			$postobox_closed_class = $s->id != 0 ? ' closed ' : '' ;
			
			
			echo '<div class="postbox '. $postobox_closed_class .'">';
			
			

			
			// handle div
			echo '<div class="handlediv" title="click"></div>';
			
			// remove btn
			$remove_sector_btn = $i > 0 ? '<input type="button" class="button button-primary button-large" value=" - " name="remove_sector_btn">' : '';
			echo '<div class="remove_sector_btn_container">'.$remove_sector_btn.'</div>';
			
			echo '<h3 class="hndle ui-sortable-handle"><span>'. $sector_title .'</span></h3><div class="inside">';

			
			
			// sector content;
			echo '<div class="long_sector">';
			
			
			// construction_sector post id
			echo "<input type='hidden' value='$s->id' name='sector_id[$i]'>";
			
					
			// Construction sector
			$sector_args = array(
					'name'	=>	"ap_icas_long_cons_sector[$i]",
					'value'	=>	$s->get_field('ap_icas_long_cons_sector'),
					'size'	=> 1,
					'label'	=>	__("Sector" , 'icas') .": "
			);
			
			
			ap_icas_get_form_element( $sector_args );
			
			
			// Construction stairs
			$stairs_args = array(
					'name'	=>	"ap_icas_long_cons_stairs[$i]",
					'value'	=>	$s->get_field('ap_icas_long_cons_stairs'),
					'size'	=> 1,
					'label'	=>	__("Nr. trepte" , 'icas') .": "
			);
			
			ap_icas_get_form_element( $stairs_args );
			
			
			// Construction length
			$constr_length_args = array(
					'name'	=>	"ap_icas_long_cons_length[$i]",
					'value'	=>	$s->get_field('ap_icas_long_cons_length'),
					'size'	=> 3,
					'label'	=>	__("Lungime" , 'icas') .": "
			);
			
			ap_icas_get_form_element( $constr_length_args );
			
			
			// Construction deep
			$constr_deep_args = array(
					'name'	=>	"ap_icas_long_cons_deep[$i]",
					'value'	=>	$s->get_field('ap_icas_long_cons_deep'),
					'size'	=> 3,
					'label'	=>	__("Adancime" , 'icas') .": "
			);
			
			ap_icas_get_form_element( $constr_deep_args );
			
			
			// Construction radier width
			$constr_width_apron_args = array(
					'name'	=>	"ap_icas_long_cons_width_apron[$i]",
					'value'	=>	$s->get_field('ap_icas_long_cons_width_apron'),
					'size'	=> 3,
					'label'	=>	__("Latime radier" , 'icas') .": "
			);
			
			ap_icas_get_form_element( $constr_width_apron_args );
			
			
			// Construction radier width
			$constr_fruit_guard_wall_args = array(
					'name'	=>	"ap_icas_long_cons_fruit_guard_wall[$i]",
					'value'	=>	$s->get_field('ap_icas_long_cons_fruit_guard_wall'),
					'size'	=> 3,
					'label'	=>	__("Fruct zid garda" , 'icas') .": "
			);
			
			ap_icas_get_form_element( $constr_fruit_guard_wall_args );
			
			
			
			echo "<p>";
			ap_icas_admin_fieldset_title( __( 'Materiale de constructie', 'icas' ) );
			
			// Construction material radier
			$construction_material_apron_args = array(
					'type'	=>	'select',
					'name'	=>	"mat_sect_apron[$i]",
					'options'	=>	ap_icas_get_taxonomy_terms_as_options('mat_sect_apron'),
					'value'	=>	$s->get_material_term_value('mat_sect_apron'),
					'label'	=>	__("Radier" , 'icas') .": "
				
			);
			
			ap_icas_get_form_element( $construction_material_apron_args );
			
			
			// Construction material guard walls
			$construction_material_guard_wall_args = array(
					'type'	=>	'select',
					'name'	=>	"mat_sect_walls[$i]",
					'options'	=>	ap_icas_get_taxonomy_terms_as_options('mat_sect_walls'),
					'value'	=>	$s->get_material_term_value('mat_sect_walls'),
					'label'	=>	__("Ziduri garda" , 'icas') .": "
			
			);
			
			ap_icas_get_form_element( $construction_material_guard_wall_args );
			
			
			// Construction material spur (pinteni)
			$construction_material_spur_args = array(
					'type'	=>	'select',
					'name'	=>	"mat_sect_spur[$i]",
					'options'	=>	ap_icas_get_taxonomy_terms_as_options('mat_sect_spur'),
					'value'	=>	$s->get_material_term_value('mat_sect_spur'),
					'label'	=>	__("Pinteni" , 'icas') .": "
			
			);
			
			ap_icas_get_form_element( $construction_material_spur_args );
			
			echo '</p>';
			
		
			
			
			// ==================================================
			// Construction apron damages
			// ==================================================
			
 			echo '<p>';
			ap_icas_admin_fieldset_title( __('Avarii radier', 'icas') );
			
			echo '<table class="icas-admin-table"><thead><tr><td colspan="2">'.__("Fisuri", "icas").'</td><td>'.__("Desprinderi", "icas").'</td><td  colspan="2">'.__("Eroziuni", "icas").'</td></tr></thead>
					<tbody>
					<tr>
						<td>'.__("nr", "icas").'</td>
						<td>'.__("% afectat", "icas").'</td>
						<td>'.__("% desprins", "icas").'</td>
						<td>'.__("adancime (cm)", "icas").'</td>
						<td>'.__("% afectat", "icas").'</td>
					</tr>';
			
			
			echo '<tr>';
			
			$apron_damage_fields = array(
					'ap_icas_long_apron_craks_nr',
					'ap_icas_long_apron_damage_percent',
					'ap_icas_long_apron_displaced',
					'ap_icas_long_apron_abrasion_deep',
					'ap_icas_long_apron_abrasion_percent',
			);
			
			foreach ( $apron_damage_fields as $damage ){
				$d = array(
						'name'	=>	$damage."[$i]",
						'value'	=>	$s->get_field( $damage ),
						'size'	=> 3,
						'label'	=>	""
				);
			
				echo '<td>';
				ap_icas_get_form_element( $d );
				echo '</td>';
			}
			
			echo '</tr>';
			echo ' </tbody>
					</table>';
			
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
			
			$field_prefix = 'ap_icas_long_sidewall_';
			
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
							'name'	=>	$f_name."[$i]",
							'value'	=>	$s->get_field($f_name),
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
			// Construction spur damages
			// ==================================================
			
			echo '<p><div class="ap_icas_long_spur_section_header">';
			
	

		
			ap_icas_admin_fieldset_title( __('Avarii pinten', 'icas') );
			
			$spur_add_btn = array(
					'type'	=> 'button',
					'name'	=> 'add_long_spur_section_btn',
					'before'=> '',
					'after' => '',
					'class' => 'button',
					'value'	=> __('Adauga pinten', 'icas')
			);
			ap_icas_get_form_element( $spur_add_btn );
		
			echo '</div>';
			
			echo '<div class="ap_icas_long_spur_section">';		
			
			
			
			echo '<table class="icas-admin-table">
					<thead>
					<tr class="icas-secondary-thead">
						<td>'.__("Nr. pinten", "icas").'</td>
						<td colspan="2">'.__("Decastrare", "icas").'</td>
						<td colspan="2">'.__("Afuieri", "icas").'</td>
						<td colspan="2">'.__("Fisuri orizontale", "icas").'</td>
						<td colspan="2">'.__("Fisuri verticale", "icas").'</td>	
						<td colspan="3">'.__("Desprinderi", "icas").'</td>	
						<td colspan="2">'.__("Eroziuni", "icas").'</td>
						<td ></td>
					</tr>
			
					<tr  class="icas-secondary-thead">
						<td></td>
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
						<td></td>		
					</tr>
					</thead>
					<tbody>';
			echo '<tr>';
			
			$spur_damage_fields = Icas_Construction_Sector::get_spur_fields_names();

			$spur_number = $s->get_spur_numbers() > 0 ? $s->get_spur_numbers() : 1 ;
			
			for ( $j = 0; $j < $spur_number; $j++ ){
			
				foreach ( $spur_damage_fields as $damage ){
					$d = array(
							'name'	=>	$damage."[$i][]",
							'value'	=>	$s->get_spur_field( $damage, $j) ,
							'size'	=> 3,
							'label'	=>	""
					);
					
					echo '<td>';
					ap_icas_get_form_element( $d );
					echo '</td>';
				}
				// first table row doesn't have remove btn
				$remove_btn = $j == 0 ? '' : '<input type="button" class="button" value=" - " name="remove_spur_table_btn">';
				echo '<td class="remove_spur_btn_container">'.$remove_btn.'</td>';
				echo '</tr>';
			}
			echo ' </tbody>
					</table>';
			
			echo '</div></p>';
			
			
			
			// ==================================================
			// Construction disfunctionalities
			// ==================================================
			
			echo '<p>';
			ap_icas_admin_fieldset_title( __('Disfunctionalitati', 'icas') );
			
			echo '<table class="icas-admin-table">
					<thead>
					<tr class="icas-secondary-thead">
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
			
			$disf_damage_fields = array(
					'ap_icas_long_disfunctio_su',
					'ap_icas_long_disfunctio_srad',
					'ap_icas_long_disfunctio_sect_aval'
			);
			
			
			foreach ( $disf_damage_fields as $damage ){
				$d = array(
						'name'	=>	$damage.'['.$i.']',
						'value'	=>	$s->get_field( $damage ),
						'size'	=> 3,
						'label'	=>	""
				);
			
				echo '<td>';
				ap_icas_get_form_element( $d );
				echo '</td>';
			}
			
			echo '</tr>';
			echo ' </tbody>
					</table>';
			
			echo '</p>';
			
			
			echo '</div>'; // .long_sector
			
			echo '</div>'; // <div class="inside">
			echo '</div>'; // <div class="postbox">
		}
		
		echo '<input type="hidden" name="last_saved_sectors" value="'.implode(',', $existent_sectors).'">';
		
		echo '</div>'; // .long_sector_collection_wrapper 

	}
	
	
	
	// Save longitudinal data
	public static function save( $post_id, $post, $update ){
		
		// if it is a trans type, do nothing, except if is an update which has changed the construction type
		if( $_POST['ap_icas_construction_type'] == 'trans'){
			
			//if it is a longitudinal construction update which before was a transversal one, delete all transversal meta if were inserted
			if( $update && ! empty($_POST['original_construction_type']) && $_POST['ap_icas_construction_type'] != $_POST['original_construction_type'] ){
				// get post children of type 'construction_sector'
				$child_sectors_args = array(
						'post_parent'	=> $post->ID,
						'post_type'		=> 'construction_sector'
				);
				
				$children_arr = get_children( $child_sectors_args );
				
				foreach ( $children_arr as $c ){
					wp_delete_post( $c->ID );
				}
			}
			return;
		}
		
		// error_log('Icas_Construction_Longitudinal_Data_Metabox:: save '. $post_id.' '. print_r($_POST, 1));

		if( ! isset( $_POST['sector_id'][0]) ){
			return;
		}
		
		$sectors = $_POST['sector_id'];
		
		// delete sectors that are not present anymore
		$sectors_to_delete = array_diff( explode(',', $_POST['last_saved_sectors'] ) ,  $sectors );
		
		if( $sectors_to_delete ){
			foreach ( $sectors_to_delete as $sect_id ){
				wp_delete_post( $sect_id, true );
			}		
		}
		
		// update or create new 'construction_sector' posts		
		$sector_fields_arr = Icas_Construction_Sector::get_sector_fields_names();
		
		$spur_fields_arr = Icas_Construction_Sector::get_spur_fields_names();
		
		foreach ( $_POST['sector_id'] as $key => $val ){
			
			
			
			$sect_id = $val;
			
			// delete an existent sector if sector number was deleted
			if( $sect_id && empty( $_POST['ap_icas_long_cons_sector'][ $key ] ) ){
				wp_delete_post( $sect_id, true );
				break;
			}
			
			// do not attempt to save a sector without a number
			if( empty( $_POST['ap_icas_long_cons_sector'][ $key ] ) ){
				break;
			}
			
			
			// if it is a new sector ($sect_id = 0 or "") and have a sector number create a post
			if( empty( $sect_id ) ){
				$sect_args = array(
						'post_parent'	=> $post_id,
						'post_type'		=> 'construction_sector',
						'post_status'	=> 'publish',
						'post_title'	=> $_POST['ap_icas_long_cons_sector'][ $key ],
						'post_content'	=> $_POST['ap_icas_long_cons_sector'][ $key ]
				);
				$sect_id = wp_insert_post( $sect_args );
			}
			
			
			// save sector data
			foreach ( $sector_fields_arr as $field ){
				if( isset( $_POST[ $field ][ $key ] ) ){
					update_post_meta( $sect_id, $field,  $_POST[ $field ][ $key ] );
				}			
			}
			
			// save spur data
			foreach ( $spur_fields_arr as $spur ){
				if( isset( $_POST[ $spur ][ $key ] ) ){
					update_post_meta( $sect_id, $spur,  $_POST[ $spur ][ $key ] );
				}
			}
			
			// save sector construction materials taxonomies
			$terms_arr = Icas_Construction_Sector::get_material_term_fields();

			foreach ( $terms_arr as $tax ){
				if( isset( $_POST[ $tax ][ $key ] ) ){
					wp_set_object_terms( $sect_id, (int) $_POST[ $tax ][ $key ], $tax);
				}
			}
			
			
			$total_length = 0; 
			// total lenght
			foreach ( $_POST['ap_icas_long_cons_length'] as $k => $v ){
				$total_length += (float)$v;
			}
			
			update_post_meta( $post_id, 'ap_icas_long_total_length',  $total_length );
			
		}
	}
	
	
}