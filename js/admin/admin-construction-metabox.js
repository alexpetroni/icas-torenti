/**
 * 
 */


( function( $ ){
	
	// disable title insert
	$(".post-type-construction #title").attr("disabled", "disabled");
	
	
	// show / hide longitudinal and transversal data based on selected construction type
	$("#ap_icas_construction_type" ).change(function(){
		var selected = $("#ap_icas_construction_type option:selected" ).val();
		if ( 'trans' == selected ){
			$('#construction_longitudinal_data').hide();
			$('#construction_transversal_data').show();
		}else if( 'long' == selected ){
			$('#construction_longitudinal_data').show();
			$('#construction_transversal_data').hide();
		}else{
			$('#construction_longitudinal_data').hide();
			$('#construction_transversal_data').hide();
		}
	});
	
	$("#ap_icas_construction_type" ).change();
	
	
	
	// Remove spur section 
	function spur_remove_parent_table(){
		$(this).closest("tr").remove();
	}
	
	
	// Add remove section button functionalities
	$(".remove_sector_btn_container input").click(sector_remove_itself);
	
	// Add remove spur button functionalities
	$("td.remove_spur_btn_container input").click(spur_remove_itself);
	
	// Add  spur section 
	$("input.button[name=add_long_spur_section_btn]").click(function(){
		var tbody = $(this).closest('.ap_icas_long_spur_section_header').next(".ap_icas_long_spur_section").children("table.icas-admin-table").children("tbody");
		
		var tr_clone = tbody.children("tr").first().clone( true );
		tr_clone.find("td.remove_spur_btn_container").append('<input type="button" class="button" value=" - " name="remove_spur_table_btn">').click(spur_remove_itself);
		tr_clone.find('input:text').each(function(){
				$(this).attr('value', '');
				$(this).val('');
			});
		
		
		tr_clone.appendTo( tbody );
	});
	
	
	// Cloning sectors
	$("#add_long_sector_btn").click(function(){
		
		var i = 0;
		var sect_nr	= parseInt($("#long_sector_collection_wrapper").attr('data-sector-numbers'));

		$("#long_sector_collection_wrapper").attr('data-sector-numbers', 1 + sect_nr );
		
		var sect_clone = $("#long_sector_collection_wrapper .postbox").first().clone( true );
		
		// remove if are more than one spur row		
		var spur_first_row = sect_clone.find(".ap_icas_long_spur_section tbody tr").first().clone( true );
		sect_clone.find(".ap_icas_long_spur_section tbody tr").each(function(){
			$(this).remove();
		});
		
		sect_clone.find(".ap_icas_long_spur_section tbody").append( spur_first_row );
		
		sect_clone.find("h3 span").text( $("#long_sector_collection_wrapper").attr('data-new-sector-title') );
		
		// TO DO
		sect_clone.removeClass("closed");
		
		sect_clone.find(".remove_sector_btn_container").append('<input type="button" class="button button-primary button-large" value=" - " name="remove_sector_btn">').click(sector_remove_itself);
		sect_clone.find(':text').each(function(){	
			$(this).attr('value', '');
			$(this).val('');
			
			var name  = $(this).attr('name');
			name =  name.replace(/\[(.+?)\]/g, '['+sect_nr+']');
			$(this).attr('name', name );
		});
		
		
		sect_clone.find('select').each(function(){	
			$(this).attr('value', '');
			$(this).val('');
			
			var name  = $(this).attr('name');
			name =  name.replace(/\[(.+?)\]/g, '['+sect_nr+']');
			$(this).attr('name', name );
		});
		
		sect_clone.find(":hidden[name^='sector_id']").each(function(){	
			$(this).attr('value', '');
			$(this).val('');
			
			var name  = $(this).attr('name');
			name =  name.replace(/\[(.+?)\]/g, '['+sect_nr+']');
			$(this).attr('name', name );
		});
		
		sect_clone.appendTo(".long_sector_collection_wrapper");
		
		$('html, body').animate({
	        scrollTop: $(sect_clone).offset().top
	    }, 800);
	});
	
	
	// Remove cloned sector
	function sector_remove_itself(){
		var c = confirm("Doresti sa stergi sectorul?");
		if( ! c ){
			return;
		}
		
		$(this).closest(".postbox").remove();
		var sect_nr	= $("#long_sector_collection_wrapper").attr('data-sector-numbers');
		$("#long_sector_collection_wrapper").attr('data-sector-numbers', sect_nr--);
	}
	
	
	// Remove spur
	function spur_remove_itself(){
		$(this).closest("tr").remove();
	}
	
	
	
	// If trans_disip_type is "placa disipatoare", enable "Numar total dinti" si "dinti desprinsi"
	$("#trans_disip_type").change(function(){
		var sel = $("#trans_disip_type option:selected").val();
		var disip_type = $("#disip_board").val();
		if( sel == disip_type ){
			$("#ap_icas_trans_apron_teeth_total").prop('disabled', false);
			$("#ap_icas_trans_apron_teeth_detach").prop('disabled', false);
		}else{
			$("#ap_icas_trans_apron_teeth_total").prop('disabled', true);
			$("#ap_icas_trans_apron_teeth_detach").prop('disabled', true);
		}

	});
	
	
	$("#trans_disip_type").change();
	
})( jQuery );