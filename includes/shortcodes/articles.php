<?php
add_shortcode( 'icas_articles', 'ap_icas_articles' );

function ap_icas_articles( $attr ){
	
	$defaults = array(
			'posts_per_page' => 5,
			'post_type' => 'post',
			'category'	=> ''
	);
	
	$args = shortcode_atts( $defaults, $attr );
	
	$art_query = new WP_Query( $args );
	
	$text = '';
	
	if( $art_query->have_posts() ){
		
		while ( $art_query->have_posts() ){
			$art_query->the_post();
			$text.= "<div class='row fp-articles'>";
			if( has_post_thumbnail() ){
				$text .= '<div class="small-3  columns"><div class="fp-articles">'.get_the_post_thumbnail($art_query->post->ID, 'thumbnail').'</div></div>';
			}
			
			$text .= '<div class="small-9  columns">';
			$text .= '<a class="article-title" href="'.get_permalink().'">'.get_the_title().'</a>';
			$text .= '<div class="article-time">'.get_the_date('Y/m/d').'</div>';
			$text .= '<div class="article-excerpt">'.get_the_excerpt().'</div>';
			$text .= '</div>';
			
			$text .= '</div>';
		}
		
	}
	
	wp_reset_query();
	return $text;
}



// =======================================================================================================

//											Recover data


add_shortcode( 'icas_recover', 'ap_icas_recover' );

function ap_icas_recover( ){
	
	$server_data_file = ICAS_PLUGIN_DIR.'debug/lista-lucrari-server.csv';
	
	$serban_data_file = ICAS_PLUGIN_DIR.'debug/tabel-serban.csv';
	
	
	$constr_arr = [];
	
	$test = false;
	
	// SERVER
	
	if(file_exists( $server_data_file ) ){
		$f = fopen($server_data_file, 'r');
		$i = 0;

		
		if( $f !== false ){
			while ( $data = fgetcsv( $f )  ){
				
				$cod = $data[2].'#'.$data[3];
				
				$constr_arr[$cod] = array(						
						'an' => $data[1],
						'ch' => $data[4],
						'cp' => $data[5],
						'rh' => $data[6],
						'rp' => $data[7],
						'ys' => $data[8],
						'id' => $data[0]
				);
				
				if($test){
					$i++;
					if($i > 20 ) break;
				}
			}
			
			
		}
		
		fclose($f);
			
	}else{
		error_log('========= NU exista fisier server: ');
	}
	
	
	
	// SERBAN
	
	$serban_arr = [];
	
	if( file_exists( $serban_data_file ) ){
		$s = fopen($serban_data_file, 'r');
		$i = 0;
	
	
		if( $s !== false ){
			while ( $data = fgetcsv( $s )  ){
	
				$cod = implode('-', array_filter( array_slice($data, 1, 7) ) ).'#'. implode('-', array_filter( array_slice($data, 8, 2) ) );
	
				
				$serban_arr[$cod] = array(
							'an' => $data[0],
							'ch' => $data[10],
							'cp' => $data[11],
							'rh' => $data[12],
							'rp' => $data[13],
							'ys' => $data[14]
					);

	
	
			if($test){
					$i++;
					if($i > 20 ) break;
				}
			}
				
			
		}
	
		fclose($s);
			
	}else{
		error_log('========= NU exista fisier server: ');
	}
	
	
	$comune_arr = array_intersect_key($serban_arr, $constr_arr);
	//error_log("Serban  ". count($serban_arr));
	//error_log("comune ". count($comune_arr));
	//error_log( print_r($comune_arr, 1));
	
	$debug_file= fopen(ICAS_PLUGIN_DIR.'debug/debug.csv', 'w'); 
	
	foreach ($comune_arr as $k => $v){
		fputcsv($debug_file, array($constr_arr[$k]['id'], $v['ch'], $v['cp'], $v['rh'], $v['rp'], $v['ys']));
	}	
	
	
	fclose($debug_file);
	
	
	
	/*
	$lucrari_inexistente = array_keys( array_diff_key($serban_arr, $constr_arr) );
	
	$lucrari_inplus_pe_server = array_diff_key($constr_arr,  $serban_arr);
	
	
	error_log("Lucrari existente in fisierul refacut de Serban dar care nu sunt prezente pe server: " . print_r($lucrari_inexistente, 1));
	

	error_log("Lucrari existente pe server care nu exista in fisierul de la Serban (introduse manual): " );
	$j = 0;
	$raport_arr = [];
	foreach($lucrari_inplus_pe_server as $k => $val ){
		$raport_arr[$k] =  ' http://abht.ro/construction/?p='.$val['id'];		
		
	}
	error_log( print_r($raport_arr, 1) );
	*/

}
// =======================================================================================================

// 		DEBUG



add_shortcode( 'icas_debug', 'ap_icas_debug' );

function ap_icas_debug( ){
	

	
	$data_file = ICAS_PLUGIN_DIR.'debug/debug.csv';
	
	if( file_exists( $data_file ) ){
		$f = fopen($data_file, 'r');
		
		if( $f !== false ){
			
			while( $data = fgetcsv( $f ) ){
				$postId = (int) $data[0];
				$post = get_post( $postId );
				
				$ap_icas_trans_damage_erosion_height = $data[1];
				$ap_icas_trans_damage_erosion_percent = $data[2];
				
				$ap_icas_trans_apron_erosion_height = $data[3];
				$ap_icas_trans_apron_erosion_percent = $data[4];
				
				$original_ys = $data[5];
				
				

			
				
				if('construction' !=  $post->post_type ){
					continue;
				}
				
				$post_meta = [];
				
				error_log('$post ' . $post->post_type );
				
				error_log('========= Terms: ');
				
				$term = wp_get_post_terms( $post->ID, 'construction_type');				
				error_log( print_r($term[0]->slug, 1));
				$post_meta['ap_icas_construction_type'] = $term[0]->slug;
				
				$mat_apron = wp_get_post_terms($post->ID, 'mat_apron', array('fields' => 'ids') );
				error_log( print_r($mat_apron, 1));
				$post_meta['mat_apron'] = $mat_apron[0];
				
				
				error_log('========= Metas: ');
				
				$post_meta_original = get_post_meta( $postId );
				
				foreach ($post_meta_original as $k => $v ){
					$post_meta[$k] = $v[0];
					error_log($k . ' => '. $post_meta[$k]);
				}
				
				

				$post_meta['ap_icas_trans_damage_erosion_height'] = $ap_icas_trans_damage_erosion_height;
				$post_meta['ap_icas_trans_damage_erosion_percent'] = $ap_icas_trans_damage_erosion_percent;
				
				$post_meta['ap_icas_trans_apron_erosion_height'] = $ap_icas_trans_apron_erosion_height;
				$post_meta['ap_icas_trans_apron_erosion_percent'] = $ap_icas_trans_apron_erosion_percent;
				
				
				$ys = ap_icas_calculate_ys($post_meta);
				
				error_log( 'Ys = '.$ys);
				
				
				update_post_meta( $postId, 'ap_icas_construction_ys', $ys );
				
				update_post_meta( $postId, 'ap_icas_trans_damage_erosion_height', $ap_icas_trans_damage_erosion_height );
				update_post_meta( $postId, 'ap_icas_trans_damage_erosion_percent', $ap_icas_trans_damage_erosion_percent );
				
				update_post_meta( $postId, 'ap_icas_trans_apron_erosion_height', $ap_icas_trans_apron_erosion_height );
				update_post_meta( $postId, 'ap_icas_trans_apron_erosion_percent', $ap_icas_trans_apron_erosion_percent );
				
				update_post_meta( $postId, '_original_Ys', strip_tags( $original_ys ) );
				
				delete_post_meta($postId, 'test_ys');
			}
			
			
			fclose($f);
			
			error_log('Terminat update');
		}
		
	} else {
		error_log('Nu exista fisierul');
	}
	

	$text = 'Terminat update';

	return $text;
}