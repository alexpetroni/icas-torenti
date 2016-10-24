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