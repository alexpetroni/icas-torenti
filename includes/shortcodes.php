<?php
add_action( 'init', 'ap_icas_register_shortcodes' );

function ap_icas_register_shortcodes(){
	include 'shortcodes/search_form.php';
	include 'shortcodes/search_result.php';
	include 'shortcodes/articles.php';
}