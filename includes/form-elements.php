<?php

// create a form element for metabox use
/**
 * @param array $args
 * @return string 
 */
function ap_icas_get_form_element( $args, $print = true ){
	$defaults = array(
		'type'	=> 'input',
		'name'	=> '',
		'id'	=> '',
		'label'	=>	'',
		'label_tooltip' => '',
		'value'	=>	'',
	    'attr'	=>	'',
		'options'	=> array(),
		'before'	=>	'<div class="icas-field">',
		'after'	=>	'</div>',
		'size'	=>	'',
		'class'	=> ''
			
	);
	
	if( ! is_array( $args ) || ! $args || ! isset($args['name']) ){
		return new WP_Error('icas_error', 'Empty metabox arguments');
	}
	
	$args = wp_parse_args( $args, $defaults );
	
	extract( $args );
	
	// allowable form types
	$allow_types = array( 'input', 'select', 'hidden', 'button', 'checkbox' );
	
	if (! in_array( $type, $allow_types) ){
		return new WP_Error('icas_error', 'Unsupported metabox form element type');
	}
	
	if( $size ){
		$size = " size='".$size."' ";
	}
	
	if( $id ){
		$id = " id='".$id."' ";	
	}
	
	if( $class ){
		$class = " class='".$class."' ";
	}
	
	if( $attr ){
		if( is_array( $attr ) && $attr ){
			$atr_list = '';
			
			foreach ( $attr as $k => $v ){
				$atr_list .= ' '.$k.'="'.esc_attr($v).'" ';
			}
			
			$attr =  $atr_list;
		}else{
			$attr = ' '. $attr. ' ';
		}
	}
	
	if( $label_tooltip ){
		$label_tooltip = ' title="'.esc_attr( $label_tooltip ).'" ';
	}
	
	$fe = '';
	
	// ====================================================
	//		INPUT
	// ====================================================
	if( $type == 'input' ){
		$fe .= $before . '<span class="metabox_label"  '.$label_tooltip.'>'.$label."</span><input $attr $size name='$name' $id $class value='".esc_attr( $value )."' $size>". $after;
	}
	
	// ====================================================
	//		SELECT
	// ====================================================
	if( $type == 'select' ){
		$fe .= $before . '<span class="metabox_label" '.$label_tooltip.'>'.$label."</span><select $attr name='$name' $id $class $size>";
		
		if( $options ){
			foreach ( $options as $key => $option_val ){
				$fe .= "<option value='$key' ".selected( $value, $key, false).">$option_val</option>";
			}
		}
		
		$fe .= '</select>';
		
		$fe .= $after;
	}
	
	
	// ====================================================
	//		BUTTON
	// ====================================================
	if( $type == 'button' ){
		$fe .= $before . "<input $attr type='button' $id name='$name' value='".esc_attr( $value )."' $class >". $after;
	}
	
	
	// ====================================================
	//		CHECKBOX
	// ====================================================
	if( $type == 'checkbox' ){
		$fe .= $before ."<input $attr type='checkbox' $id name='$name' value='".esc_attr( $value )."' $class >".' <span class="metabox_label"  '.$label_tooltip.'>'.$label."</span>". $after;
	}
	
	// ====================================================
	//		HIDDEN
	// ====================================================
	if( $type == 'hidden' ){
		$fe .= $before . "<span class='metabox_label' >".$label."</span><input $id $attr type='hidden' $class name='$name' value='".esc_attr( $value )."'>". $after;
	}
	
	if ( $print){
		echo $fe ;
		return;
	}
	
	return $fe;
}


/**
 * Get first meta values for a post as array (meta_key => meta_val), empty string if value not found
 * 
 * @param int $post_id
 * @param array $meta_fields
 * @return array 
 */
function ap_icas_get_post_meta( $post_id, $meta_fields ){
	
	$post_meta = get_post_meta( $post_id );
	
	$meta_arr = array();
	
	foreach ( $meta_fields as $val ){
		if( isset( $post_meta[$val][0] ) ){
			$meta_arr[$val] = $post_meta[$val][0];
		}else{
			$meta_arr[$val] = '';
		}
	}
	
	return $meta_arr;
}


// 
