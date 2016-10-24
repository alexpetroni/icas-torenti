<?php
include_once ICAS_PLUGIN_DIR.'includes/queries/class-constructions-wp-query.php';

add_shortcode( 'icas-search-result', 'ap_icas_search_result' );

function ap_icas_search_result(){
	
	if(! isset( $_GET['ap_icas_main_search']) ){
		return;
	}
	echo '<div class="search-result-container">';	
	
	// Because longitudinal constructions are composite 'construction_sector' post_types, they have special treatment
	// Query on longitudinals should returne results containing constructions that have at least one sector that meet the selection criterias
	
	$longitudinal_query	= ! empty( $_GET['ap_icas_construction_type'] ) && $_GET['ap_icas_construction_type'] == 'long';
	$transversal_query	= ! empty( $_GET['ap_icas_construction_type'] ) && $_GET['ap_icas_construction_type'] == 'trans';
	
	// if area is specified, show some specific tabs
	$area_id = ap_icas_get_selected_area_id_from_query_str( $_SERVER['QUERY_STRING'] );

	
	
	$q_args = ap_icas_get_constructions_query_args_from_str( $_SERVER['QUERY_STRING'] );
	
	$args	= $q_args['general'];
	$long_sect_args	= $q_args['longitudinal'];
	
	$search_query = new Constructions_WP_Query( $args, $long_sect_args );
	
	//error_log('query args '. print_r( $args, 1 ) );
	
	//error_log('Constructions_WP_Query $search_query->request '. print_r( $search_query->request , 1 ) );
	
	//error_log('Constructions_WP_Query $search_query '. print_r( $search_query , 1 ) );
	
	// echo json_encode( $args ) . '<br />';	
	
	$const_type_query = ! $longitudinal_query && ! $transversal_query ? 'mixed' : $transversal_query ? 'trans' : 'long';
	
	echo "\n<script>\n";
	echo 'var query_args = '.json_encode( $args )."; \n";
	echo 'var sectors_query_args = '.json_encode( $long_sect_args )."; \n";
	echo 'var query_type = "'.$const_type_query.'" ;'."\n";
	echo "</script>\n";
	
	
	// error_log('$search_query '. print_r( $search_query, 1 ) );
	
	if( $search_query->have_posts() ){
		?>
				
		<div class="row collapse">
		
			<div class="medium-3 columns">
				<ul class="tabs vertical" id="example-vert-tabs" data-tabs>
				<li class="tabs-title is-active"><a href="#constructions_list" aria-selected="true">Lista lucrari</a></li>
				<li class="tabs-title"><a href="#ys_segment_distribution">Distributie lucrari hidrotehnice pe categorie de stare</a></li>
			<?php if( $area_id ) :?>
				<li class="tabs-title"><a href="#ys_area_distribution">Distributie medie Ys pe bazine</a></li> 
			<?php endif;?>
				<li class="tabs-title"><a href="#ys_years_distribution">Distributie medie Ys functie varsta</a></li>
				<li class="tabs-title"><a href="#ys_decade_distribution">Distributie medie Ys pe decade</a></li>
		<?php if( $transversal_query ) : ?>
				<li class="tabs-title"><a href="#ys_ye_distribution">Distributie medie Ys functie de Ye</a></li>
				<li class="tabs-title"><a href="#ys_trans_material_construction_distribution">Distributie medie Ys functie de materiale de constructie corp transversale</a></li>
				
				<!--   <li class="tabs-title"><a href="#granulometry_distribution">Granulometrie</a></li> -->
		<?php endif; ?>
		
		<?php if(false &&  $transversal_query ) : ?>
				<li class="tabs-title"><a href="#ys_long_material_construction_distribution">Distributie medie Ys functie de materiale de constructie corp transversale</a></li>
		
				<li class="tabs-title"><a href="#ys_map">Harta lucrari</a></li>
				<li class="tabs-title"><a href="#panel4v">Tab 6</a></li>
		<?php endif;?>
				</ul>
			</div>
		   <div class="medium-9 columns">
		    <div class="tabs-content vertical" data-tabs-content="example-vert-tabs" id="results-tabs-content-vertical">
		    <div id="autosave-img"><img src="<?php echo ICAS_PLUGIN_URL.'/images/ajax-loader.gif';?>"></div>	
		       <div class="tabs-panel is-active" id="constructions_list">      
     
		<?php 
		if( $transversal_query || $longitudinal_query ){
			parse_str( $_SERVER['QUERY_STRING'], $qs );
			$qs['download'] = 'download_list';
			$download_link = http_build_query( $qs );
			
			//echo $download_link;
			//echo '<div class="download-list-container"><a target="_blank" href="/?'.$download_link.'">'.__("Download lista", "icas").'</a></div>';
		}
		 	echo "<h2>".__("Lucrări", "icas") .": ". $search_query->found_posts."</h2>"; // num pages:" . $search_query->max_num_pages ."
			
		 	
		 	echo '<table><thead><tr>';
			echo '<td>Cod cadastral</td>';
			echo '<td>Lucrare</td>';
			echo '<td>Curs de apa</td>';
			echo '<td>Ys</td>';
			echo '</tr></thead><tbody>';
		
		while( $search_query->have_posts()){
			$search_query->the_post();
			
			$cod_cadastral =  get_the_title();
			
			$cod_cadastral = substr(get_the_title(), 0, strpos(get_the_title(), '#'));
			
			$cod_lucrare = substr(get_the_title(), strpos(get_the_title(), '#'));
			
			echo '<tr>';
			echo '<td><a href="'.get_permalink().'">'. $cod_cadastral . '</a></td>';
			echo '<td><a href="'.get_permalink().'">'. $cod_lucrare . '</a></td>';
			echo '<td>'. $search_query->post->basin .'</td>';
			echo '<td>'. $search_query->post->ys .'</td>';
			echo '</tr>';
		}
 
		echo '</tbody></table>';
		
		echo '<div class="row"><div class="small-6 columns">';
		next_posts_link(__("Urmatoarele rezultate", "icas"), $search_query->max_num_pages);
		echo '</div><div class="small-6 columns">';
		previous_posts_link(__("Rezultatele anterioare", "icas")); 
		echo '</div></div>'; // row
		
		echo '</div>'; // #panel1v
		?>

		
		<div class="tabs-panel" id="ys_segment_distribution">
			<!--  <div class="ct-bar-ys-segment-distribution ct-major-tenth"></div>		 -->
			<div class="row">
				<div class="small-12 medium-4 columns">
					<div class="ct-pie-ys-segment-distribution"></div>
				</div>
				<div class="small-12 medium-8 columns">
				<div class="table-ys-segment-distribution"></div>
					<div class="ct-pie-ys-segment-distribution-legend graphic-legend">
					</div>
				</div>
			</div>
			<div class="ct-pie-ys-segment-distribution"></div>
			
			
		</div>
		
		<?php if($area_id): ?>
		
		<div class="tabs-panel" id="ys_area_distribution">
			<div class="table-ys-area-distribution"></div>
		</div>
		<?php endif; ?>
		
		<div class="tabs-panel" id="ys_years_distribution">
			<div class="ct-ys-years-distribution ct-major-tenth"></div>
			<div class="table-ys-years-distribution under-graph-table"></div>
		</div>
		
		<div class="tabs-panel" id="ys_decade_distribution">
			<div class="ct-ys-decade-distribution ct-major-tenth"></div>
			<div class="table-ys-decade-distribution under-graph-table"></div>
		</div>
		
		<div class="tabs-panel" id="ys_ye_distribution">
			<div class="ct-ys-ye-distribution ct-major-tenth"></div>
			<div class="table-ys-ye-distribution under-graph-table"></div>
		</div>
		
		<div class="tabs-panel" id="ys_trans_material_construction_distribution">
			<div class="ct-ys-trans-material-construction-distribution ct-major-tenth"></div>
			<div class="table-ys-trans-material-construction-distribution under-graph-table"></div>
		</div>

		<div class="tabs-panel" id="granulometry_distribution">
			<div class="ct-granulometry-distribution  ct-major-tenth"></div>
		</div>
		
		<?php // ================================ LONGITUDINALS ================= ?>
		<div class="tabs-panel" id="ys_long_material_construction_distribution">
			<div class="ct-ys-long-material-construction-distribution  ct-major-tenth"></div>
			<div class="table-ys-long-material-construction-distribution"></div>
		</div>
		
		
		
		<div class="tabs-panel" id="ys_map">
				
		</div>
		
		<?php 
		echo '</div></div></div> ';
		
		
		
		
	}else{
		echo '<h1>'.__("Nu s-au găsit rezultate", "icas").'</h1>';
	}
	
	
	echo '</div>'; // .search-result-container
	wp_reset_postdata();
}