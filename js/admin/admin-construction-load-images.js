/**
 * Used by load construction images metabox
 */

/**
 *  http://code.tutsplus.com/tutorials/getting-started-with-the-wordpress-media-uploader--cms-22011
 * Callback function for the 'click' event of the 'Upload image'
 * anchor in its meta box.
 *
 * Displays the media uploader for selecting an image.
 *
 * @since 0.1.0
 */
function renderMediaUploader( $ ) {
    'use strict';
 
    var file_frame, image_data;
 
    /**
     * If an instance of file_frame already exists, then we can open it
     * rather than creating a new instance.
     */
    if ( undefined !== file_frame ) {
 
        file_frame.open();
        return;
 
    }
 
    /**
     * If we're this far, then an instance does not exist, so we need to
     * create our own.
     *
     * Here, use the wp.media library to define the settings of the Media
     * Uploader. We're opting to use the 'post' frame which is a template
     * defined in WordPress core and are initializing the file frame
     * with the 'insert' state.
     *
     * We're also allowing the user to select more than one image.
     */
    file_frame = wp.media.frames.file_frame = wp.media({
        frame:    'post',
        state:    'insert',
        multiple: true
    });
 
    /**
     * Setup an event handler for what to do when an image has been
     * selected.
     *
     * Since we're using the 'view' state when initializing
     * the file_frame, we need to make sure that the handler is attached
     * to the insert event.
     */
    file_frame.on( 'insert', function() {
 
    	// Read the JSON data returned from the Media Uploader
        var json = file_frame.state().get( 'selection' ).toJSON();

        $.each(json, function( i, obj ){
        	if( $.trim( obj.url ).length > 0 ){
        		var thumb_url = obj.sizes.thumbnail.url;
        		var img_id	= obj.id;
        		 $( '#featured-footer-image-container' ).append("<div class='icas_admin_thumbs'><img src='"+thumb_url+"' width='75' height='75' /><input type='hidden' name='ap_icas_constr_img[]' value='"+img_id+"'></div>");
        	}
        	console.log(obj);
        });
    });
 
    
    // Now display the actual file_frame
    file_frame.open();
 
}


(function( $ ) {
    'use strict';
 
    $(function() {
        $( '#ap_icas_load_img_btn' ).on( 'click', function( evt ) {
 
            // Stop the anchor's default behavior
            evt.preventDefault();
 
            // Display the media uploader
            renderMediaUploader( $ );
 
        });
 
    });
    
    // delete the thumbnail
    $(function() {
        $( 'a.delete_thumb' ).on( 'click', function( evt ) {
    		var c = confirm("Doresti sa stergi imaginea?");
    		if( ! c ){
    			return;
    		}
        	$(this).closest(".icas_admin_thumbs").remove();
        });
    });
 
})( jQuery );
