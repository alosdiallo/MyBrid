<html>  
<?php 
$this->load->helper("url"); 
$base_url = base_url();
?>
<head>
<script src="http://code.jquery.com/jquery-latest.js"></script>
	<link href="<?php echo $base_url?>css/universal.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $base_url?>css/jquery.ui.all.css" rel="stylesheet" type="text/css">

	<!-- JAVASCRIPT FILES -->
<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.core.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.position.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.autocomplete.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/project_control.js"></script>

<style type="text/css">
</style>
	<style>
		.ui-autocomplete {
			max-height: 100px;
			overflow-y: auto;
			/* prevent horizontal scrollbar */
			overflow-x: hidden;
			/* add padding to account for vertical scrollbar */
			padding-right: 20px;
		}
		/* IE 6 doesn't support max-height
		 * we use height instead, but this forces the menu to always be this tall
		 */
		* html .ui-autocomplete {
			height: 100px;
		}
	</style>	

<script type="text/javascript">
	//////
	// Define base url for project control
	//////
	var base_url = "<?php echo $base_url?>"; 
	var userMem = "<?php echo $this->session->userdata('user_mem')?>";
	var projectMem = "<?php echo $this->session->userdata('project_mem')?>";
	
	function getTable(){
		
	
		var transcriptor     = $("input[id=Transcriptor]").val();
		var promoter         = $("input[id=Promoter]").val();
		var arrayCoord       = $("input[id=ArrayCoord]").val();
		
		var zScoreMin        = $("input[id=Z_score_min]").val();
		var zScoreMax        = $("input[id=Z_score_max]").val();
		
		var zPrimeMin        = $("input[id=Z_prime_min]").val();
		var zPrimeMax        = $("input[id=Z_prime_max]").val();
		
		var origIntensityMin = $("input[id=Orig_intensity_value_min]").val();
		var origIntensityMax = $("input[id=Orig_intensity_value_max]").val();
		
		var rcIntensityMin   = $("input[id=Rc_intensity_value_min]").val();
		var rcIntensityMax   = $("input[id=Rc_intensity_value_max]").val();
		
		var ptpIntensityMin  = $("input[id=Ptp_intensity_value_min]").val();
		var ptpIntensityMax  = $("input[id=Ptp_intensity_value_max]").val();
		
		var positive         = $('input[id=Positive]').is(':checked');
		var bleedover        = $('input[id=Bleedover]').is(':checked');
		
		var userId           = $('#user_id').val();
		var projectId        = $('#project_id').val();
		
		$('#gradient-style').html('<img src="http://franklin-umh.cs.umn.edu/UMassProject/images/loader.gif"/>');
		$.ajax({
				
				url : '<?php echo $base_url?>index.php/tableview/getTable/',
				type : 'post',
				data : {
					transcriptor       : transcriptor,
					promoter           : promoter,
					array_coord        : arrayCoord,
					z_score_min        : zScoreMin,
					z_score_max        : zScoreMax,
					z_prime_min        : zPrimeMin,
					z_prime_max        : zPrimeMax,
					orig_intensity_min : origIntensityMin,
					orig_intensity_max : origIntensityMax,
					rc_intensity_min   : rcIntensityMin,
					rc_intensity_max   : rcIntensityMax,
					ptp_intensity_min  : ptpIntensityMin,
					ptp_intensity_max  : ptpIntensityMax,
					positive           : positive,
					bleedover          : bleedover,
					user_id            : userId,
					project_id         : projectId
				},
				success : function(answer){
					
					if(typeof(answer) == "string"){
						
						var hash = eval('(' + answer + ')');
						
						generateTableElement(hash);
					}
				}
			});
	}
	
	function generateTableElement(elementData){
		currentHTML = $('#gradient-style').html();
		str =       '<thead>';
	    str = str + 	'<tr>';
		str = str +			'<th scope="col">Plate Name</th>';
	    str = str + 		'<th scope="col">Transcriptor Factor</th>';
		str = str +			'<th scope="col">Orf Name</th>';
		str = str +			'<th scope="col">Y</th>';
		str = str +			'<th scope="col">X</th>';
		str = str +			'<th scope="col">Plate Medium</th>';
		str = str +			'<th scope="col">Call Type</th>';
		str = str +			'<th scope="col">Bleed Over</th>';
		str = str +			'<th scope="col">Human Call</th>';
		str = str + 		'<th scope="col">Modified Call</th>';
	    str = str + 		'<th scope="col">Z-Score</th>';
		 str = str + 		'<th scope="col">Z-Prime</th>';
	    str = str + 		'<th scope="col">Array Coord</th>';
	    str = str + 		'<th scope="col">Original Intensity Value</th>';
	    str = str + 		'<th scope="col">RC Intensity Value</th>';
	    str = str + 		'<th scope="col">PTP Intensity Value</th>';
	    str = str + 	'</tr>';
	    str = str + '</thead>';
	    str = str + '<tfoot>';
	    str = str +		'<tr>';
	    str = str + 		'<td colspan="4"></td>';
	    str = str + 	'</tr>';
	    str = str + '</tfoot>';
	    str = str + '<tbody>';
		for(element in elementData){
			eleData = elementData[element];
			str = str + "<tr>" ;
			str = str + 	"<td>"+eleData.plate_name+"</td>";
			str = str + 	"<td>"+eleData.transcriptor_factor+"</td>";
			str = str + 	"<td>"+eleData.orf_name+"</td>";
			str = str + 	"<td>"+eleData.y_coord+"</td>";
			str = str + 	"<td>"+eleData.x_coord+"</td>";
			str = str + 	"<td>"+eleData.plate_median+"</td>";
			str = str + 	"<td>"+eleData.call_type+"</td>";
			str = str + 	"<td>"+eleData.bleed_over+"</td>";
			str = str + 	"<td>"+eleData.human_call+"</td>";
			str = str + 	"<td>"+eleData.modified_call+"</td>";
			str = str + 	"<td>"+parseFloat(eleData.z_score).toPrecision(5)+"</td>";
			str = str + 	"<td>"+parseFloat(eleData.z_prime).toPrecision(5)+"</td>";
			str = str + 	"<td>"+eleData.array_coord+"</td>";
			str = str + 	"<td>"+parseFloat(eleData.orig_intensity_value).toPrecision(5)+"</td>";
			str = str + 	"<td>"+parseFloat(eleData.rc_intensity_value).toPrecision(5)+"</td>";
			str = str + 	"<td>"+parseFloat(eleData.ptp_intensity_value).toPrecision(5)+"</td>";
			str = str + "</tr>";
		}
		str = str + '</tbody>';
		$('#gradient-style').html(str);
	}
	
	function getList(data){
		
		var list = new Array();
		
		for(var i = 0; i < data.length; i++){
			list.push(data[i].transcriptor_factor);
			list.push(data[i].orf_name);
		}
		
		return list;
	}
	

</script>
		<script>
			var promoterTags;
			var transcriptionTags;
			
			function projectChange(){
				// Grab lists for autocomplete when projectChanges
				$.ajax({
					url : '<?php echo $base_url?>index.php/autocomplete/getPromoterData',
					type : 'post',
					data : {
						user   : userMem,
						project : projectMem
					},
					success : function(answer){
						promoterTags = eval(answer);
					}
				});
				$.ajax({
					url : '<?php echo $base_url?>index.php/autocomplete/getTranscriptionData',
					type : 'post',
					data : {
						user   : userMem,
						project : projectMem
					},
					success : function(answer){
						transcriptionTags = eval(answer);
					}
				});
			}
			
			$(function() {
				/// Set up lists for autocomplete
				promoterTags = ["blank"];
				transcriptionTags = ["blank"];
				projectChange();
				
				/// Set up functions for autocomplete
				function split( val ) {
					return val.split( /,\s*/ );
				}
				function extractLast( term ) {
					return split( term ).pop();
				}
			// Promoter
				$( "#Promoter" )
					// don't navigate away from the field on tab when selecting an item
					.bind( "keydown", function( event ) {
						if ( event.keyCode === $.ui.keyCode.TAB &&
								$( this ).data( "autocomplete" ).menu.active ) {
							event.preventDefault();
						}
					})
					.autocomplete({
						minLength: 0,
						source: function( request, response ) {
							var matches = $.map( promoterTags, function(tag) {
								if ( tag.toUpperCase().indexOf(request.term.toUpperCase()) === 0 ) {
									return tag;
								}
							});
							response(matches);
						},
						focus: function() {
							// prevent value inserted on focus
							return false;
						},
						select: function( event, ui ) {
							var terms = split( this.value );
							// remove the current input
							terms.pop();
							// add the selected item
							terms.push( ui.item.value );
							// add placeholder to get the comma-and-space at the end
							terms.push( "" );
							this.value = terms.join( ", " );
							return false;
						}
					});
				// No need for end on this level
			// Transcriptor
				$( "#Transcriptor" )
					// don't navigate away from the field on tab when selecting an item
					.bind( "keydown", function( event ) {
						if ( event.keyCode === $.ui.keyCode.TAB &&
								$( this ).data( "autocomplete" ).menu.active ) {
							event.preventDefault();
						}
					})
					.autocomplete({
						minLength: 0,
						source: function( request, response ) {
							var matches = $.map( transcriptionTags, function(tag) {
								if ( tag.toUpperCase().indexOf(request.term.toUpperCase()) === 0 ) {
									return tag;
								}
							});
							response(matches);
						},
						focus: function() {
							// prevent value inserted on focus
							return false;
						},
						select: function( event, ui ) {
							var terms = split( this.value );
							// remove the current input
							terms.pop();
							// add the selected item
							terms.push( ui.item.value );
							// add placeholder to get the comma-and-space at the end
							terms.push( "" );
							this.value = terms.join( ", " );
							return false;
						}
					});
				// No need for end on this level
			});
		</script>     
		<script>
		function downloadFile(duplicate){
			var transcriptor     = $("input[id=Transcriptor]").val();
			var promoter         = $("input[id=Promoter]").val();
			var arrayCoord       = $("input[id=ArrayCoord]").val();
			
			var zScoreMin        = $("input[id=Z_score_min]").val();
			var zScoreMax        = $("input[id=Z_score_max]").val();
			
			var zPrimeMin        = $("input[id=Z_prime_min]").val();
			var zPrimeMax        = $("input[id=Z_prime_max]").val();
			
			var origIntensityMin = $("input[id=Orig_intensity_value_min]").val();
			var origIntensityMax = $("input[id=Orig_intensity_value_max]").val();
			
			var rcIntensityMin   = $("input[id=Rc_intensity_value_min]").val();
			var rcIntensityMax   = $("input[id=Rc_intensity_value_max]").val();
			
			var ptpIntensityMin  = $("input[id=Ptp_intensity_value_min]").val();
			var ptpIntensityMax  = $("input[id=Ptp_intensity_value_max]").val();
			
			var positive         = $('input[id=Positive]').is(':checked');
			var bleedover        = $('input[id=Bleedover]').is(':checked');
			
			var userId           = $('#user_id').val();
			var projectId        = $('#project_id').val();

			$("#loader").html('<img src="http://franklin-umh.cs.umn.edu/UMassProject/images/loader.gif"/>');
			 
			$.ajax({
				url : '<?php echo $base_url?>index.php/tableview/downloadTable',
				type : 'post',
				data : {
					transcriptor       : transcriptor,
					promoter           : promoter,
					array_coord        : arrayCoord,
					z_score_min        : zScoreMin,
					z_score_max        : zScoreMax,
					z_prime_min        : zPrimeMin,
					z_prime_max        : zPrimeMax,
					orig_intensity_min : origIntensityMin,
					orig_intensity_max : origIntensityMax,
					rc_intensity_min   : rcIntensityMin,
					rc_intensity_max   : rcIntensityMax,
					ptp_intensity_min  : ptpIntensityMin,
					ptp_intensity_max  : ptpIntensityMax,
					positive           : positive,
					bleedover          : bleedover,
					user_id            : userId,
					project_id         : projectId
				},
				success : function(answer){	
					if(answer == "error"){
						alert("file does not exist");
					} else{
						var url = eval(answer);
						//alert(url);
						window.location.href = url;
					}
					$("#loader").empty();
				}
			});
		}
		</script>
</head>
<body>
	<div id="head" class="medium-padding">
		<div id="session_controls" class="alignright">
			<!--Session Control Divs-->
			<span id="user_session_controls">
				<?php
					if($this->session->userdata('is_logged_in') == false){
							echo '
							<FORM METHOD="LINK" ACTION="'.$base_url.'index.php/login/" class="alignright">
								<INPUT TYPE="submit" VALUE="Login">
							</FORM>
							';
					  }
					  else{
							echo '
							<FORM METHOD="LINK" ACTION="'.$base_url.'index.php/login/logout/" class="alignright">
								<INPUT TYPE="submit" VALUE="Logout">
							</FORM>
							';
					  }
				?>
				<FORM METHOD="LINK" ACTION="<?php echo $base_url?>" class="alignright">
					<INPUT TYPE="submit" VALUE="Back to Homepage">
				</FORM>
			</span>
			<!--Project Control Divs-->
			<span id="user_project_controls">
				<span id="user_controls" >
					<select id="user_id" name="user_id" class="alignright"></select>
				</span>
				<span id="project_controls" >
					<select id="project_id" name="project_id" class="alignright"></select>
				</span>
			</span>
		</div>
		
		Bait: <input type="text" id="Promoter" size="20" value="" /><br>
		Prey: <input type="text" id="Transcriptor" size='20' value="" >   <br>	  	  
		<!--
		Array Coordinate: <input type='text' id='ArrayCoord' size='20' value="" onkeydown="autocomplete(this);" /><br>
		-->
		<table border="0" class='centered'>
			<tr>
				<td><input type='text' id='Z_score_min' size='2' value=""/></td>
				<td>&lt;</td>
				<td>Z-Score</td>
				<td>&lt;</td>
				<td><input type='text' id='Z_score_max' size='2' value=""/></td>
			</tr>
			<tr>
				<td><input type='text' id='Z_prime_min' size='2' value=""/></td>
				<td>&lt;</td>
				<td>Z-Prime</td>
				<td>&lt;</td>
				<td><input type='text' id='Z_prime_max' size='2' value=""/></td>
			</tr>
			<tr>
				<td><input type='text' id='Orig_intensity_value_min' size='2' value=""/></td>
				<td>&lt;</td>
				<td>Original Intensity Value</td>
				<td>&lt;</td>
				<td><input type='text' id='Orig_intensity_value_max' size='2' value=""/></td>
			</tr>
			<tr>
				<td><input type='text' id='Rc_intensity_value_min' size='2' value=""/></td>
				<td>&lt;</td>
				<td>RC Intensity Value</td>
				<td>&lt;</td>
				<td><input type='text' id='Rc_intensity_value_max' size='2' value=""/></td>
			</tr>
			<tr>
				<td><input type='text' id='Ptp_intensity_value_min' size='2' value=""/></td>
				<td>&lt;</td>
				<td>PTP Intensity Value</td>
				<td>&lt;</td>
				<td><input type='text' id='Ptp_intensity_value_max' size='2' value=""/></td>
			</tr>
		</table>
		<input type="checkbox" id="Positive" name="Positive" value="true" /> Positive values			  
		<input type="checkbox" id="Bleedover" name="Bleedover" value="true" /> Bleed Over <br/>
				  
		<input type="button" id='search' value="Search" onclick='getTable();'/>
	</div>
	<input id='btnLoad' type='button' value='Download Table' onclick='downloadFile();'></input>
	<div id='loader'></div>
	<div id='table'>
	<table id="gradient-style" class='standardTable'>
    <thead>
    	<tr>
      </tr>
    </thead>
    <tfoot>
    </tfoot>
    <tbody>
    </tbody>
</table>
	</div>	 
</body>
</html>
