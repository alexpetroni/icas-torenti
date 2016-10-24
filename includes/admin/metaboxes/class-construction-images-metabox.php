<?php
// Construction images data metabox

class Icas_Construction_Images_Metabox{

	protected static $ap_icas_img_field_name = 'ap_icas_constr_img';
	
	public function __construct(){
		add_action( 'save_post', array($this,  'save'), 30, 3 );
	}
	
	public static function output( $post ){
		$metafield = self::$ap_icas_img_field_name;
		
		$ap_icas_images = get_post_meta( $post->ID, $metafield );
		

		
		
		echo '<p><div id="featured-footer-image-container">
				<input type="hidden" name="ap_icas_img_field_name" id="ap_icas_img_field_name" value="'.$metafield.'">';
		
		if( $ap_icas_images && ! empty ( $ap_icas_images[0] ) ){
						
			foreach ( $ap_icas_images[0] as $img_id ){
				$i = wp_get_attachment_image_src( $img_id, 'thumbnail');
				echo "<div class='icas_admin_thumbs'><a class='delete_thumb'><i class='fa fa-trash fa-2x'></i></a><img src='$i[0]' width='75' height='75' /><input type='hidden' name='ap_icas_constr_img[]' value='".$img_id."'></div>";
			}
		}

		echo '</div></p>';
		
		echo '<p class="hide-if-no-js">';
		echo '<input type="button" id="ap_icas_load_img_btn" name="ap_icas_load_img_btn" value="'.__("Adaugă imagini", 'icas').'" class="button-secondary">';
		
		echo '</p>';
		
	}



	// Save general data
	public static function save( $post_id, $post, $update ){
		$metafield = self::$ap_icas_img_field_name;
		if( $_POST[ $metafield ] ){
			update_post_meta( $post_id, $metafield, $_POST[ $metafield ] );
		}else{
			delete_post_meta( $post_id, $metafield );
		}
	}

}