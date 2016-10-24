/**
 * foundation.tabs.js
 */

$( function(){
	
	
	// ==============================================
	//				INTEROGATION
	// ==============================================
	
	// ==============================================
	//				County
	// ==============================================
	
	// On county selection change, load availables cities
	if( $("#ap_icas_construction_county option:selected").val() == "" ){
		$('#ap_icas_construction_city').attr("disabled", "disabled");
	}	
	
	$("#ap_icas_construction_county").change(function(){
		var selected = $("#ap_icas_construction_county option:selected").val();
		if(! selected ){
			$('#ap_icas_construction_city option').remove();
			$('#ap_icas_construction_city').attr("disabled", "disabled");
			return;
		}
		
		$.ajax({
			type: "POST",
			url: icas.ajaxurl,
			dataType: 'json',
			data : {
				action: "ap_icas_get_county_cities", 
				county_id: selected
					},
			success: function(response) {	
				$('#ap_icas_construction_city option').remove();

				if(! $.isEmptyObject(response) ){
					$('#ap_icas_construction_city').append( $('<option></option>').text(icas.select_txt).val("") );
					$('#ap_icas_construction_city').removeAttr("disabled");
					
					$.each( response, function(){
						$('#ap_icas_construction_city').append( $('<option></option>').text(this.name).val(this.id) );
					} );
				}
				
			  }			
		});		
		
	});
	
	
	
	
	// ==============================================
	//				Area
	// ==============================================
	
	// on select change clear all children 
	$('.area_select').change(function(){
		// make no sense if it is last child ( select element is wrapped in a  .icas-field div )
		if( $(this).parent().is(":last-child") ){
			return;
		}
		
		
		$(this).parent().nextAll().find("select option").remove().end().children("select").attr("disabled", "disabled");
		
		var selected = $("option:selected", this).val();
		
		// if is a value selected, call ajax to receive the area children
		if( selected ){
			
			$.ajax({
				type: "POST",
				url: icas.ajaxurl,
				context: this,
				dataType: 'json',
				data: {
					action: 'ap_icas_get_area_children',
					parent_id: selected
				},
				success: function( response ){
					if( ! $.isEmptyObject( response ) ){
					var nextSibling = $(this).parent().next().children("select");
					if( nextSibling ){
						nextSibling.removeAttr("disabled");
						nextSibling.append( $('<option></option>').text(icas.select_txt).val("") );
						
						$.each( response, function(){
							nextSibling.append( $('<option></option>').text(this.name).val(this.id) );
						} );
					}
				}
					
				}
			});			
		}
	});
	
	
	$("#ap_icas_construction_type").change(function(){
		var selected = $("#ap_icas_construction_type option:selected").val();
		if( selected == "long" ){
			$("#transversals").hide();
			$("#transversals :input").attr("disabled", true);
			$("#longitudinals").show();
			$("#longitudinals :input").attr("disabled", false);
		}else if( selected == "trans"  ){
			$("#transversals").show();
			$("#transversals :input").attr("disabled", false);
			$("#longitudinals").hide();
			$("#longitudinals :input").attr("disabled", true);
		}else{
			$("#transversals").hide();
			$("#transversals :input").attr("disabled", true);
			$("#longitudinals").hide();
			$("#longitudinals :input").attr("disabled", true);
		}
	});
	
	$("#ap_icas_construction_type").change();
	
	$( document ).tooltip();
	
	
	

	// ==============================================
	//				RESULTS
	// ==============================================	
	
	// array with the already loaded graphics, don't ask them second time
	var graphics_loaded = [];
	
	// map the tabs ids with the response functions
	var responses_map = {
			ys_segment_distribution : ys_segment_distribution_response,
			ys_years_distribution	: ys_years_distribution_response,
			ys_area_distribution	: ys_area_distribution_response,
			ys_decade_distribution	: ys_decade_distribution_response,
			ys_ye_distribution	: ys_ye_distribution_response,
			ys_trans_material_construction_distribution	: ys_trans_material_construction_distribution_response,
			ys_long_material_construction_distribution : ys_long_material_construction_distribution_response,
			granulometry_distribution	: granulometry_distribution_response,
			ys_map	: ys_map_response
			
	};
	
	// vertical tabs
	$("#example-vert-tabs").on("change.zf.tabs",  function(event){
		
		// the tab
		var t = $(this).find('.tabs-title.is-active a').attr('aria-controls');
		
		if( -1 != $.inArray( t, graphics_loaded ) || 'constructions_list' == t ){
			return;
		}
		
		
		if( 'ys_map' == t ){
			alert('harta');
			prepare_map( t );
		}
		
		// show image
		$("#autosave-img").show();
		
		
		$.ajax({
			type: "POST",
			url: icas.ajaxurl,
			context: {tab: t}, 
			cache: false,
			dataType: 'json',
			data: {
				action: "ap_icas_get_graphics_data", 
				selection_query_args: query_args,	
				sectors_selection_query_args: sectors_query_args,
				tab: t
				  },
			
			success: function(response){
				graphics_loaded.push(this.tab);
				responses_map[this.tab]( response );
				
				// show image
				$("#autosave-img").hide();
			},
			
			error: function(jqXHR, textStatus, errorThrown) {
                console.log('error');
                console.log(errorThrown);
                console.log(jqXHR);
            }
			
		});
	});
	
	
	
	// Ys distribution on 0-20-40-60-80-100 intervals
	function ys_segment_distribution_response( response ){		
		
		var data = {
				labels: [],
				series: []
		};
		
		var pie_series = [];
		
		var total_no_costruction = 0;
		
		var legend = {
			ys_20: {
					range: " Ys <= 20 ",
					description: "Stare foarte proasta"
					},
			ys_40 : {
				range : "20 < Ys <= 40 ",
				description : "Stare proasta"
			},
			ys_60 : {
				range : "40 < Ys <= 60 ",
				description : "Stare medie"
			},
			ys_80 : {
				range : "60 < Ys <= 80 ",
				description : "Stare buna"
			},
			ys_100 : {
				range : "80 < Ys <= 100 ",
				description : "Stare foarte buna"
			}
		}
		
		//console.log(response);
		var i = 0;
		$.each( response, function(){
			var s = [0,0,0,0,0];
			s[i] = parseInt( this.n);
			data.series[i] = s;
			data.labels.push( parseInt( this.ys_segment) );
			i++;
			
			pie_series.push( parseInt( this.n) );	
			
			total_no_costruction += parseInt( this.n );
		});
		
		
		var table_content = [];
		
		$.each( response, function(){
			var percent = total_no_costruction? Math.round(  parseInt( this.n ) / total_no_costruction *100*100 ) / 100 + '%' : "";			
			var state_txt = '<div class="color-legend legend-ys-'+ this.ys_segment + '"></div>' + legend["ys_" + this.ys_segment].description + '<br/><span class="table_small">'+legend["ys_" + this.ys_segment].range + '</span>';
			table_content.push([state_txt, this.avg_ys, this.n, percent]);
		});
		
		var options={
				seriesBarDistance: 10,
				plugins: [				   
				          Chartist.plugins.ctAccessibility({
				            caption: 'Distributie Ys',
				            seriesHeader: 'Intervale Ys',
				            summary: 'Numarul de lucrari distribuite pe intervale de cate 20',
				            valueTransform: function(value) {
				              return value ;
				            },
				            // ONLY USE THIS IF YOU WANT TO MAKE YOUR ACCESSIBILITY TABLE ALSO VISIBLE!
				           // visuallyHiddenStyles: 'position: relative; top: 100%; width: 100%; font-size: 11px; overflow-x: auto; background-color: rgba(0, 0, 0, 0.1); padding: 10px'
				          })
				        ]
		};
		// abandon for now
		//new Chartist.Bar('.ct-bar-ys-segment-distribution', data, options);
		
		
		var sum = function(a, b) { return a + b };

		var d1 = {series: pie_series };
		
		new Chartist.Pie('.ct-pie-ys-segment-distribution', d1 , {
			 width: 270 ,
			  height: 270,
		  labelInterpolationFnc: function(value) {
		    return Math.round(value / d1.series.reduce(sum) * 100 *100) / 100+ '%';
		  }
		});
		
		
		var table = '<table>';
		table += get_table_header(["Categorie de stare", "Medie Ys", "Numar constructii", "%"]);
		table += get_table_content( table_content );
		table += '</table>';
		
		$(".table-ys-segment-distribution").append(table);
		
	}
	
	
	function ys_years_distribution_response( response ){		
		
		var data = { labels: [], series: [] };
		data.series[0] = [];
		
		var total_no_costruction = 0;
		
		$.each( response, function(){
			data.labels.push( this.year );
			data.series[0].push( this.avg_ys );
			
			total_no_costruction += this.n != null  ? parseInt( this.n ) :  0 ;

			
		});

		
		var table_content = [];
		
		$.each( response, function(){
			var y = this.year;
			if( this.n != null ){ // for now hide the years without constructions
				var avg_ys =  this.avg_ys != null ? this.avg_ys : 0;
				var n = this.n != null ? this.n : 0 ;			
				
				var percent = total_no_costruction? Math.round(  parseInt( n ) / total_no_costruction *100*100 ) / 100 + '%' : "";			
				table_content.push([this.year, avg_ys, n, percent]);
			}

		});
		
		table_content.reverse();
		
		var labels_interval = get_years_labels_interval( data.labels.length );
		
		var options = {
				fillHoles: true,
				showLine: false,
				low: 0,
				hight: 100,
				  //showPoint: false,
				  // Disable line smoothing
				  lineSmooth: false,
				  axisX: {
					    labelInterpolationFnc: function(value, index) {
					   // return 	data.labels[index];
					     return parseInt( data.labels[index] ) % labels_interval === 0 ? data.labels[index] : null;
					    }
					  },
		  axisY: {
			    labelInterpolationFnc: function(value, index) {
			   // return 	data.labels[index];
			     return value % 5 === 0 ? value : null;
			    }
			  }
		};
		
		
		new Chartist.Line('.ct-ys-years-distribution', data, options);
		
		var table = '<table>';
		table += get_table_header(["An", "Medie Ys", "Numar constructii", "%"]);
		table += get_table_content( table_content );
		table += '</table>';
		
		$(".table-ys-years-distribution").append(table);
	}
	
	
	
	function ys_decade_distribution_response( response ){		
		
		var data = { labels: [], series: [] };
		data.series[0] = [];
		var total_no_costruction = 0;
		
		console.log( response );
		
		$.each( response, function(){
			data.labels.push( this.year );
			data.series[0].push( parseInt( this.avg_ys ) );
				
			total_no_costruction += this.n != null  ? parseInt( this.n ) :  0 ;
		});
		
		var table_content = [];
		
		$.each( response, function(){
			var y = this.year;
			if( this.n != null ){
				var avg_ys =  this.avg_ys != null ? this.avg_ys : 0;
				var n = this.n != null ? this.n : 0 ;			
				
				var percent = total_no_costruction? Math.round(  parseInt( n ) / total_no_costruction *100*100 ) / 100 + '%' : "";		
				
				var decade_end = parseInt( this.year ) + 9;
				
				table_content.push([this.year +'-'+decade_end, avg_ys, n, percent]);
			}
		});
		
		table_content.reverse();
		
		var options = {
				fillHoles: true,
				showLine: false,
				low: 0,
				hight: 100,
				  //showPoint: false,
				  // Disable line smoothing
				  lineSmooth: false
		};
		
		
		new Chartist.Line('.ct-ys-decade-distribution', data, options);
		
		var table = '<table>';
		table += get_table_header(["Decada", "Medie Ys", "Numar constructii", "%"]);
		table += get_table_content( table_content );
		table += '</table>';
		
		$(".table-ys-decade-distribution").append(table);
	}
	
	
	
	
	function ys_area_distribution_response( response ){
		
		var table_content = [];
		
		var total_no_costruction = 0;
		
		$.each( response, function(){
			if( this.n != null ){}
				total_no_costruction = Math.max(total_no_costruction, parseInt( this.n ) );
		});
		
		$.each( response, function(){
			var area_code = this.area_code;
			if( this.n != null ){
				var avg_ys =  this.avg_ys != null ? this.avg_ys : 0;
				var n = this.n != null ? this.n : 0 ;	
				
				var n_trans = this.n_trans != null ? this.n_trans : 0;
				
				var n_long = n - n_trans;
				
				var percent = total_no_costruction? Math.round(  parseInt( n ) / total_no_costruction *100*100 ) / 100 + '%' : "";		
				
				
				table_content.push([this.area_code, avg_ys, n, n_trans +' / ' + n_long, percent]);
			}
		});
		
		
		var table = '<table>';
		table += get_table_header(["Cod bazin", "Medie Ys", "Nr. total constructii", "Transversale / Longitudinale", "%"]);
		table += get_table_content( table_content );
		table += '</table>';
		$(".table-ys-area-distribution").append(table);
	}
	
	
	
	
	
	function ys_ye_distribution_response( response ){		
		
		var data = { labels: [], series: [] };
		
		var total_no_costruction = 0;
		
		$.each( response, function(){		
			
			data.labels.push( this.ye );
			data.series.push( parseInt( this.n ) );

			total_no_costruction += this.n != null  ? parseInt( this.n ) :  0 ;
		});
		
		
		var table_content = [];
		
		$.each( response, function(){
			var tip = this.ye == 0 ? 'Traverse<br><span class="table_small">(Ye = 0)</span>' : this.ye == 1 ? 'Praguri<br><span class="table_small">(0 < Ye <= 2)</span>' : 'Baraje<br><span class="table_small">(Ye > 2)</span>';
			var avg_ys =  this.avg_ys != null ? this.avg_ys : 0;
			var n = this.n != null ? this.n : 0 ;			
			
			var percent = total_no_costruction? Math.round(  parseInt( n ) / total_no_costruction *100*100 ) / 100 + '%' : "";			
			table_content.push([tip, avg_ys, n, percent]);

		});
	
		
		
		var options ={
				 width: 270 ,
				  height: 270,
			  labelInterpolationFnc: function(value, index ) {
				  return  value == 0 ? 'Traverse '+ data.series[index]  : value == 1 ? 'Praguri ' + data.series[index] : 'Baraje '+ data.series[index];
			    return Math.round(value / total_no_costruction * 100 *100) / 100+ '%';
			  }
		  }
		
		
		new Chartist.Pie('.ct-ys-ye-distribution', data, options );
		
		
		var table = '<table>';
		table += get_table_header(["Tip", "Medie Ys", "Numar constructii", "%"]);
		table += get_table_content( table_content );
		table += '</table>';
		
		$(".table-ys-ye-distribution").append(table);
	}
	
	
	function granulometry_distribution_response( response ){
		
		var data = { labels: [], series: [] };
		data.series[0] = [];
		
		$.each( response, function(){
			data.labels.push( this.tax_name );
			data.series[0].push( parseInt( this.n ) );
		});
		
		var options={
				seriesBarDistance: 10,
				plugins: [
				          Chartist.plugins.ctAccessibility({
				            caption: 'Distributie Granulometrie',
				            seriesHeader: 'Tipuri granulometrie',
				            summary: 'Numarul de lucrari distribuite in functie de granulometrie',
				            valueTransform: function(value) {
				              return value ;
				            },
				            // ONLY USE THIS IF YOU WANT TO MAKE YOUR ACCESSIBILITY TABLE ALSO VISIBLE!
				           // visuallyHiddenStyles: 'position: relative; top: 100%; width: 100%; font-size: 11px; overflow-x: auto; background-color: rgba(0, 0, 0, 0.1); padding: 10px'
				          })
				        ]
		};
		new Chartist.Bar('.ct-granulometry-distribution', data, options );
	}
	

	
	function ys_trans_material_construction_distribution_response( response ){		
		
		var data = { labels: [], series: [] };
		data.series[0] = [];
		
		var total_no_costruction = 0;
		
		$.each( response, function(){
			data.labels.push( this.name );
			data.series[0].push( parseInt( this.avg_ys ) );
			
			total_no_costruction += this.n != null  ? parseInt( this.n ) :  0 ;
		});
		
		
		var table_content = [];
		
		$.each( response, function(){

			var avg_ys =  this.avg_ys != null ? this.avg_ys : 0;
			var n = this.n != null ? this.n : 0 ;			
			
			var percent = total_no_costruction? Math.round(  parseInt( n ) / total_no_costruction *100*100 ) / 100 + '%' : "";			
			table_content.push([this.name, this.description, avg_ys, n, percent]);
		});
		
		var options = {
				fillHoles: true,
				showLine: false,
				low: 0,
				hight: 100,
				  //showPoint: false,
				  // Disable line smoothing
				  lineSmooth: false,
				  axisX: {
					    labelInterpolationFnc: function(value, index) {
					    	return value;
					   // return 	data.labels[index];
					     return  value == 0 ? 'Traverse<br>(Ye = 0)' : value == 1 ? 'Praguri<br>(0 < Ye <= 2)' : 'Baraje<br>(Ye > 2)';
					    }
					  }
		};
		
		
		new Chartist.Bar('.ct-ys-trans-material-construction-distribution', data, options );
		
		
		
		new Chartist.Bar('.ct-ys-ye-distribution', data, options );
		
		
		var table = '<table>';
		table += get_table_header(["Cod", "Descriere", "Medie Ys", "Numar constructii", "%"]);
		table += get_table_content( table_content );
		table += '</table>';
		
		$(".table-ys-trans-material-construction-distribution").append(table);
	}
	
	
	
	
	
	function ys_long_material_construction_distribution_response( response ){		
		
		var data = { labels: [], series: [] };
		data.series[0] = [];
		
		var total_no_costruction = 0;
		
		$.each( response, function(){
			data.labels.push( this.name );
			data.series[0].push( parseInt( this.avg_ys ) );
			
			total_no_costruction += this.n != null  ? parseInt( this.n ) :  0 ;
		});
		
		
		var table_content = [];
		
		$.each( response, function(){

			var avg_ys =  this.avg_ys != null ? this.avg_ys : 0;
			var n = this.n != null ? this.n : 0 ;			
			
			var percent = total_no_costruction? Math.round(  parseInt( n ) / total_no_costruction *100*100 ) / 100 + '%' : "";			
			table_content.push([this.name, this.description, avg_ys, n, percent]);
		});
		
		var options = {
				fillHoles: true,
				showLine: false,
				low: 0,
				hight: 100,
				  //showPoint: false,
				  // Disable line smoothing
				  lineSmooth: false,
				  axisX: {
					    labelInterpolationFnc: function(value, index) {
					    	return value;
					   // return 	data.labels[index];
					     return  value == 0 ? 'Traverse<br>(Ye = 0)' : value == 1 ? 'Praguri<br>(0 < Ye <= 2)' : 'Baraje<br>(Ye > 2)';
					    }
					  }
		};
		
		
		new Chartist.Bar('.ct-ys-trans-material-construction-distribution', data, options );
		
		
		
		new Chartist.Bar('.ct-ys-ye-distribution', data, options );
		
		
		var table = '<table>';
		table += get_table_header(["Nume", "Descriere", "Medie Ys", "Numar constructii", "%"]);
		table += get_table_content( table_content );
		table += '</table>';
		
		$(".table-ys-trans-material-construction-distribution").append(table);
	}	
	
	

	function ys_map_response( response ){	
	}
	
	function get_years_labels_interval( years_range ){
		return years_range < 12 ? 1: years_range >12 && years_range < 25 ? 2 : 5;
	}
	
	
	
	
	function get_table_header( titles ){
		var r = "";
		if( $.isArray( titles ) ){
			r = "<thead><tr>";
			for( var i = 0; i < titles.length ; i++){
				r += "<td>"+ titles[i] + '</td>';
			}
			r += "</tr></thead>"
		}
		
		return r;
	}
	
	function get_table_content( rows ){
		var r = "";
		if( rows && $.isArray( rows ) ){
			r = "<tbody>";
			for( var i = 0; i < rows.length ; i++){
				r += '<tr>';
				for( var j = 0; j < rows[i].length; j++ ){
					r += "<td>"+ rows[i][j] + '</td>';
				}
				r +='</tr>';
			}
			r += "</tbody>"
		}
		
		return r;
	}
	
	
	// ===============================================================
	//		Constructions List Map Script
	// ===============================================================
	function prepare_map( t ){
		var script=document.createElement('script');
		script.type='text/javascript';
		script.src= "//maps.googleapis.com/maps/api/js?key=AIzaSyA0Gh987mNFsoIuc6XAX6-HVdG3Wl6j3cA&callback=initConstructionsListMap";

		$("body").append(script);
	}
	

	
});



function initConstructionsListMap(){
	alert('initMap');
}


