<?php
include_once ICAS_PLUGIN_DIR.'includes/queries/class-constructions-wp-query.php';
include_once ICAS_PLUGIN_DIR.'includes/queries/class-download-transversal-constructions-wp-query.php';

include_once ICAS_PLUGIN_DIR.'includes/class-icas-construction.php';
include_once ICAS_PLUGIN_DIR.'includes/class-icas-construction-sector.php';

add_action('template_redirect', 'ap_icas_template_redirect');

function ap_icas_template_redirect(){
	global $wp_query, $wpdb;
	
	if( isset( $_GET['download'] ) && 'download_list' == $_GET['download'] ){
		
			$q_args = ap_icas_get_constructions_query_args_from_str( $_SERVER['QUERY_STRING'] );;
			//error_log ('start Constructions_WP_Query ' );
		$c = new Download_Transversal_Constructions_WP_Query( $q_args['general'] );
		
		//error_log ('Constructions_WP_Query '.print_r($c, 1) );
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=lista-lucrari.csv');
		
		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');
		
		$query = $c->request;
		// remove the limit clause 		
		$limit_pos = stripos( $query,  'LIMIT');
		if( false !== $limit_pos ){
			$query = substr( $query , 0, $limit_pos );
		}
		//error_log ('$query NO limits '.print_r($query, 1) );
		$results = $wpdb->get_results( $query, ARRAY_A );
		
		//error_log ('$results '.print_r($results, 1) );
		
		if( empty ($results ) ){
			fputcsv($output, array('No result found'));
		}else{
			
			$top_header = array_merge(
					array('Id'),
					array('Date generale') , array_fill(0, 7, " "),  
					array('Elemente dimensionale') , array_fill(0, 12, " "), 
					array('Materiale de constructii') , array_fill(0, 5, " "),
					array('Avarii lucrare propriu zisă') , array_fill(0, 16, " "),
					array('Avarii radier') , array_fill(0, 8, " "),
					array('Avarii ziduri de conducere') , array_fill(0, 13, " "),
					array('Avarii pinten terminal') , array_fill(0, 10, " "),
					array('Disfuncţionalităţi') , array_fill(0, 7, " "),
					array('Ys')
					);
			//$top_header += array('Elemente dimensionale') + array_fill(0, 11, " ");
			
			
			// output the column headings
			fputcsv($output, $top_header);			
			
			/*
			 * array(
					'Date generale', implode( ', ', array_fill(0, 7, " ")), str_repeat('" ", ', 7),
					'Elemente dimensionale', str_repeat(', ', 11),
					'Materiale de constructii', str_repeat(' ', 5),
					'Avarii lucrare propriu zisă', str_repeat(' ', 16),
					'Avarii radier', str_repeat(' ', 8),
					'Avarii ziduri de conducere', str_repeat(' ', 13),
					'Avarii pinten terminal', str_repeat(' ', 10),
					'Disfuncţionalităţi', str_repeat(' ', 7)
			)
			 */
			
			// output the column headings
			fputcsv($output, array(
					' ',
					'An inventariere', 
					'Cod cadastral', 
					'Denumire bazin',
					'Cod lucrare',
					
					'Judeţ',
					'Localitate',
					'Proprietar',					
					'An executie',
					
					// Elemente dimensionale
					'Ye (m)',
					'H (m)',
					'a (m)',
					'B (m)',
					'tip lucrare',
					'Lr (m)',
					'Br (m)',
					'Tip disipator',
					'Hz (m)',
					'Nr. total de dinti',
					'Lc (m)',
					'Bc (m)',
					'Bp (m) ' ,
					
					//Materiale de constructii
					'Corp lucrare ',
					'Aripi lucrare',
					'Radier',
					'Contrabaraj',
					'Ziduri de conducere',
					'Pinten terminal',
					

					// Avarii lucrare propriu zisă
					'Decastrare stanga (m)',
					'Decastrare dreapta (m)',
					'Afuieri H (m)',
					'Afuieri %',
					'Fis, oriz, z, deversată nr',
					'Fis, oriz, z, deversată L(m)',
					'Fis, oriz, z, nedeversată nr',
					'Fis, oriz, z, nedeversată L(m)',						
					'Fis, vert, z, deversată nr',
					'Fis, vert, z, deversată L(m)',
					'Fis, vert, z, nedeversată nr',
					'Fis, vert, z, nedeversată L(m)',					
					'Desprinderi zonă deversată (%)',					
					'Desprinderi z, nedeversată stanga(%)',
					'Desprinderi z, nedeversată dreapta(%)',
					'Eroziuni h (cm)',
					'Eroziuni %',
					
					// Avarii radier
					'Fisuri nr',
					'Fisuri %',
					'Afuieri H(m)',
					'Afuieri %',
					'Desprindere radier %',
					'Dinţi desprinși nr',
					'Desprindere contrabaraj %',
					'Eroziuni h (cm)',
					'Eroziuni %',
					
					// Avarii ziduri de conducere
					'Fisuri orizontale nr',
					'Fisuri orizontale l(m)',
					'Fisuri verticale nr',
					'Fisuri verticale l(m)',
					'Desprinderi %',
					'Eroziuni (cm)',
					'Eroziuni (%)',
					
					'Fisuri orizontale nr',
					'Fisuri orizontale l(m)',
					'Fisuri verticale nr',
					'Fisuri verticale l(m)',
					'Desprinderi %',
					'Eroziuni (cm)',
					'Eroziuni (%)',
					
					// Avarii pinten terminal 					
					'Decastrare stânga (m)',
					'Decastrare dreapta (m)',
					'Fisuri orizontale nr',
					'Fisuri orizontale lungime (m) 	',
					'Fisuri verticale nr',
					'Fisuri verticale lungime (m)',
					'Desprinderi stânga %',
					'Desprinderi dreapta %',
					'Desprinderi centru %',
					'Eroziuni h (cm)',
					'Eroziuni %',
					
					
					
					// Disfuncţionalităţi
					
					'Colmatare deversor',
					'Colmatare radier %SU',
					'Colmatare radier %Srad',
					'Înaltime aterisament',
					'Granulometrie aluviuni',
					'Vegetație lemnoasă nedorită amonte%',
					'Vegetație lemnoasă nedorită aval%',
					'Reducere secțiune aval%'
					)
				);
			
			foreach ( $results as $id ){
				//error_log($id['ID'].' memory: '. memory_get_usage() );
				$c = new Icas_Construction( $id['ID'] );
				

				fputcsv($output, array(
						$id['ID'],
						$c->get_meta_as_string('ap_icas_construction_review_date'),
						$c->get_cadastral_code(),
						
						$c->get_meta_as_string('ap_icas_basin_name'), // denumire bazin
						$c->get_meta_as_string('ap_icas_construction_code'), // cod lucrare

						$c->get_term('icas_location', 0),
						$c->get_term('icas_location', 1),						
						$c->get_meta_as_string('ap_icas_construction_owner'),
						
						$c->get_meta_as_string('ap_icas_construction_date'), // an executie
						
						// Elemente dimensionale						
						$c->get_meta_as_string('ap_icas_trans_dim_ye'),
						$c->get_meta_as_string('ap_icas_trans_dim_h'),
						$c->get_meta_as_string('ap_icas_trans_dim_a'),
						$c->get_meta_as_string('ap_icas_trans_dim_b'),						
						$c->get_term('trans_constr_type'),						
						$c->get_meta_as_string('ap_icas_trans_dim_lr'),
						$c->get_meta_as_string('ap_icas_trans_dim_br'),
						$c->get_term('trans_disip_type'),
						$c->get_meta_as_string('ap_icas_trans_dim_hz'),
						$c->get_meta_as_string('ap_icas_trans_apron_teeth_total'),
						$c->get_meta_as_string('ap_icas_trans_dim_lc'),
						$c->get_meta_as_string('ap_icas_trans_dim_bc'),
						$c->get_meta_as_string('ap_icas_trans_dim_bp'),
						
						// Materiale de constructii
						$c->get_term('mat_main_body'),
						$c->get_term('mat_wings'),
						$c->get_term('mat_apron'),
						$c->get_term('mat_counter_dam'),
						$c->get_term('mat_side_walls'),
						$c->get_term('mat_final_spur'),
						
						// Avarii lucrare propriu zisă
						$c->get_meta_as_string('ap_icas_trans_damage_dec_left'),
						$c->get_meta_as_string('ap_icas_trans_damage_dec_right'),
						$c->get_meta_as_string('ap_icas_trans_damage_af_height'),
						$c->get_meta_as_string('ap_icas_trans_damage_af_percent'),
						$c->get_meta_as_string('ap_icas_trans_damage_h_crak_dev_nr'),
						$c->get_meta_as_string('ap_icas_trans_damage_h_crak_dev_l'),
						$c->get_meta_as_string('ap_icas_trans_damage_v_crak_dev_nr'),
						$c->get_meta_as_string('ap_icas_trans_damage_v_crak_dev_l'),
						$c->get_meta_as_string('ap_icas_trans_damage_h_crak_undev_nr'),
						$c->get_meta_as_string('ap_icas_trans_damage_h_crak_undev_l'),
						$c->get_meta_as_string('ap_icas_trans_damage_v_crak_undev_nr'),
						$c->get_meta_as_string('ap_icas_trans_damage_v_crak_undev_l'),
						$c->get_meta_as_string('ap_icas_trans_damage_detach_dev'),
						$c->get_meta_as_string('ap_icas_trans_damage_detach_undev_left'),
						$c->get_meta_as_string('ap_icas_trans_damage_detach_undev_right'),
						$c->get_meta_as_string('ap_icas_trans_damage_erosion_height'),
						$c->get_meta_as_string('ap_icas_trans_damage_erosion_percent'),						
						
						
						// Avarii radier 
						$c->get_meta_as_string('ap_icas_trans_apron_crack_nr'),
						$c->get_meta_as_string('ap_icas_trans_apron_crack_percent'),
						$c->get_meta_as_string('ap_icas_trans_apron_af_height'),
						$c->get_meta_as_string('ap_icas_trans_apron_af_percent'),						
						$c->get_meta_as_string('ap_icas_trans_apron_detach'),						
						$c->get_meta_as_string('ap_icas_trans_apron_teeth_detach'),
						$c->get_meta_as_string('ap_icas_trans_apron_detach_counter_dam'),
						$c->get_meta_as_string('ap_icas_trans_apron_erosion_height'),
						$c->get_meta_as_string('ap_icas_trans_apron_erosion_percent'),
						
						
						// Avarii ziduri de conducere
						
						$c->get_meta_as_string('ap_icas_trans_sidewall_left_horiz_craks_nr'),
						$c->get_meta_as_string('ap_icas_trans_sidewall_left_horiz_length'),
						$c->get_meta_as_string('ap_icas_trans_sidewall_left_vert_craks_nr'),
						$c->get_meta_as_string('ap_icas_trans_sidewall_left_vert_length'),
						$c->get_meta_as_string('ap_icas_trans_sidewall_left_displaced'),
						$c->get_meta_as_string('ap_icas_trans_sidewall_left_abrasion_deep'),
						$c->get_meta_as_string('ap_icas_trans_sidewall_left_abrasion_percent'),
						
						$c->get_meta_as_string('ap_icas_trans_sidewall_right_horiz_craks_nr'),
						$c->get_meta_as_string('ap_icas_trans_sidewall_right_horiz_length'),
						$c->get_meta_as_string('ap_icas_trans_sidewall_right_vert_craks_nr'),
						$c->get_meta_as_string('ap_icas_trans_sidewall_right_vert_length'),
						$c->get_meta_as_string('ap_icas_trans_sidewall_right_displaced'),
						$c->get_meta_as_string('ap_icas_trans_sidewall_right_abrasion_deep'),
						$c->get_meta_as_string('ap_icas_trans_sidewall_right_abrasion_percent'),

						
						
						// Avarii pinten terminal
						$c->get_meta_as_string('ap_icas_trans_final_spur_decastr_left'),
						$c->get_meta_as_string('ap_icas_trans_final_spur_decastr_right'),
						$c->get_meta_as_string('ap_icas_trans_final_spur_horiz_crack_nr'),
						$c->get_meta_as_string('ap_icas_trans_final_spur_horiz_crack_length'),
						$c->get_meta_as_string('ap_icas_trans_final_spur_vert_crack_nr'),
						$c->get_meta_as_string('ap_icas_trans_final_spur_vert_crack_length'),
						$c->get_meta_as_string('ap_icas_trans_final_spur_detach_left'),
						$c->get_meta_as_string('ap_icas_trans_final_spur_detach_right'),
						$c->get_meta_as_string('ap_icas_trans_final_spur_detach_center'),
						$c->get_meta_as_string('ap_icas_trans_final_spur_erosion_height'),
						$c->get_meta_as_string('ap_icas_trans_final_spur_erosion_percent'),
						
						
						// Disfuncţionalităţi
						$c->get_meta_as_string('ap_icas_trans_disf_colmat_deversor'),
						$c->get_meta_as_string('ap_icas_trans_disf_colmat_apron_su'),
						$c->get_meta_as_string('ap_icas_trans_disf_colmat_apron_srad'),
						$c->get_meta_as_string('ap_icas_trans_disf_hat'),
						$c->get_term('trans_gal_type'),
						$c->get_meta_as_string('ap_icas_trans_disf_veget_amonte'),
						$c->get_meta_as_string('ap_icas_trans_disf_veget_aval'),
						$c->get_meta_as_string('ap_icas_trans_disf_section_dim'),
						
						
						// Ys
						$c->get_meta_as_string('ap_icas_construction_ys'), 
						));
			}
			
		}

		
		


		fclose($output);
		die;

		
		// loop over the rows, outputting them
		while ($row = mysql_fetch_assoc($rows)) fputcsv($output, $row);
		die(print_r($_GET));
	}
}