<?php 
$this->load->helper("url"); 
$base_url = base_url();

$jsonPromoterData      = json_encode($promoterData);
?>

<html>
                                                                   
<head>                                                                 
<!-- CSS FILES -->
<link rel="stylesheet" type="text/css" href="<?php echo $base_url?>css/slideshow.css">
<link rel="stylesheet" type="text/css" href="<?php echo $base_url?>css/results.css">

<!--link href="<?php echo $base_url?>css/page.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="<?php echo $base_url?>css/jquery.ad-gallery.css">
<link rel="stylesheet" type="text/css" href="<?php echo $base_url?>css/slideshow.css"-->
<style type="text/css"></style>
  
<!-- JAVASCRIPT FILES -->
<script type="text/javascript" src="<?php echo $base_url?>javascript/wz_jsgraphics.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ad-gallery.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/dimensions.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.galleriffic.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.opacityrollover.js"></script>

<script type="text/javascript"> 
	////////////////////////////////////////////////////////////////////
	// Script Wide Constants
	////////////////////////////////////////////////////////////////////
	// function Constants
	////// BORDER //////
	// The pixels between the colonies and the edge of the image
	//////
	var BORDER_TOP    = 7; // was 7
	var BORDER_BOTTOM = 45; 
	var BORDER_LEFT   = 7; 
	var BORDER_RIGHT  = 63;
	
	////// BOX PADDING //////
	// How large the rectangle highlight box is in pixels
	//////
	var BOX_W = 14; 
	var BOX_H = 14;
	
	////// ELEMENT SPACING //////
	// How far away the colonies are from each other in pixels
	//////
	var ELESPACING_W = 12.25; // values typically 12.1 to 12.3
	var ELESPACING_H = 12.15;	

	////// POSITION //////
	// The location of the images top left hand corner
	//////
	var POS_X = 0; 
	var POS_Y = 0;

	////// DIMENSIONS //////
	// the width and height the image
	//////
	var WIDTH  = 600; 
	var HEIGHT = 400;
	
	////// HEADER HEIGHT /////
	// The height of the header
	//////
	var HEADER_HEIGHT = 0;
	
	////// TEXT SPACING //////
	// How far vertically apart text is from each other, should be near 
	// text size
	//////
	var TEXT_SPACING = 15;
	
	////// DIRECTION INTENSITY //////
	// How far the text has to go in order to be to the other side of the box on the highlight
	//////
	var DIRECTION_INTENSITY_X = 250;
	var DIRECTION_INTENSITY_Y = 125;
	
	////// MOUSE OVER COLOR  //////
	////// MOUSE CLICK COLOR //////
	// the color for mouseover and mouseclicks
	//////
	var MOUSE_OVER_COLOR  = "rgb(30,	144,	255)";
	var MOUSE_CLICK_COLOR = "rgb(77,	77,		77)";
	
	////// X ELEMENTS //////
	////// Y ELEMENTS //////
	// the number of X or Y elements on the plate
	//////
	var X_ELEMENTS = 48;
	var Y_ELEMENTS = 32;

	////// SHADOW DISTANCE //////
	// Keeps track of how many pixels away the shadow is from the text
	//////
	var SHADOW_DISTANCE = 1;
	
	////////////////////////////////////////////////////////////////////
	//
	////////////////////////////////////////////////////////////////////
	// Variables
	////////////////////////////////////////////////////////////////////
	
	////// IS THE SLIDESHOW LOADED //////
	// has the slideshow been loaded?
	//////
	var slideshowLoaded = false;
	
	////// CURRENT INDEX //////
	// used to interact with the data, keeps track of the current Index
	//////
	var currentIndex = 0;

	////// DATA //////
	// contains the data for the plates
	//////
	var promoterData      = eval(<?php echo $jsonPromoterData?>);
	
	
	////// DIMENSIONS //////
	// contains the dimensions of the plate to be highlighted
	//////
	var dims = new dimensions();
	
	////// CURRENT CLICK //////
	// the table element that is currently selected
	//////
	var current_click = undefined;
	
	///// LIST TAGS /////
	// Converts plate numbers to a tag for the images usually
	/////
	var list_tags = ["_1-4", "_5-8", "_9-12"]
	
	////////////////////////////////////////////////////////////////////
	//
	////////////////////////////////////////////////////////////////////
	// Functions
	///////////////////////////////////////// function Functions ///////
	
	////////////////////////////////////////////////////////////////////
	// Ajax Functions
	///////////////////////////////////////// function AjaxFunctions ///

	
	
	
	
	////// LOAD INTENSITY VALUES
	// This fn loads in the intensity values for a given index
	// the data is loaded into data[index] and then the interactionTable 
	// is converted to the new indexes data values.
	//////
	function loadIntensityValues(index){
		$.ajax({
			url : '<?php echo $base_url?>index.php/data/getIntensityData',
			type : 'post',
			data : {
				promData   : promoterData[index],
				index      : index,
				project_id : promoterData[index].project_id,
				user_id    : promoterData[index].user_id
			},
			success : function(answer){
				//////
				// load in the new data from ajax into the matrix
				//////
				promoterData[index].matrix = eval( "(" + answer + ")" );
				//////
				// Convert tables based on new information
				//////
				convertInteractionTable(window.promoterData[index].matrix, index);
				convertDescriptions(index);
			}
		});
	}
  
	var quadNumber = 0;
	////// UPDATE CALL //////
	// ajax fn to update the call.
	//////
	function updateCall(plateName, plateNumber, x_val, y_val, newVal, index, quadCall){
		$.ajax({
			url : '<?php echo $base_url?>index.php/data/updateModifiedCall',
			type : 'post',
			data : {
				plate_name: plateName,
				plate_number: plateNumber,
				x_coord: x_val,
				y_coord: y_val,
				new_val: newVal,
				project_id : promoterData[index].project_id,
				user_id    : promoterData[index].user_id
			},
			success : function(answer){
				
				//////
				// sets the InfoTable up with the new value. At the moment it will look like it works even if it doesn't.
				//////
				window.promoterData[index].matrix[y_val][x_val].modified_call = newVal;
				//SELF NOTE: LOOK INTO MOVING THIS ELSEWHERE LATER FOR BETTER HANDLING
				if(window.promoterData[index] == window.promoterData[currentIndex] && window.current_click != undefined){
					window.convertInfoTable(y_val, x_val);
				}
				//////
				// Quad Call handling else just display success message
				//////
				if(quadCall) {
					quadNumber++
					if(quadNumber == 4){
						alert(answer + "\n\nYou updated the entire QUAD! Congratulations.");
					}
				} else {
					alert(answer);
				}
			}
		});
	}
	
	function callPlateEntire(){
		var plateName = promoterData[currentIndex].bait_id;
		var plateNumber = promoterData[currentIndex].transcriptionData[0].position.plate_num;
		var call_val = document.getElementById("human-call").value;
		var index = currentIndex;
		$.ajax({
			url : '<?php echo $base_url?>index.php/data/updateModifiedCallEntirePlate',
			type : 'post',
			data : {
				plate_name: plateName,
				plate_number: plateNumber,
				new_val: call_val,
				project_id : promoterData[index].project_id,
				user_id    : promoterData[index].user_id
			},
			success : function(answer){
				for(var y_coord = 0; y_coord < Y_ELEMENTS; y_coord++){
					for(var x_coord = 0; x_coord < X_ELEMENTS; x_coord++){
						window.promoterData[index].matrix[y_coord][x_coord].modified_call = call_val;
					}
				}
				alert(answer);
			}
		});
	}
	
	////////////////////////////////////////////////////////////////////
	// Drawing Functions
	/////////////////////////////////////// function DrawingFunctions //
	
	////// DRAW PERMLIGHT //////
	// Permlight is drawn to permanently highlight the searched colonies
	//////
	function drawPermlight(index){
		clearPermlight();
		if(promoterData[index].transcriptionData[0].position.x != undefined){
			for(var i in promoterData[index].transcriptionData){
				jPermGraphics.drawRect(Math.round(dims.borderLeft + dims.pX + dims.elementSpacingWidth  * (( promoterData[index].transcriptionData[i].position.x - 1) * 2 )), 
				                       Math.round(dims.borderTop  + dims.pY + dims.elementSpacingHeight * (( promoterData[index].transcriptionData[i].position.y - 1) * 2 )),
				                       2 * dims.boxWidth  - 2,
				                       2 * dims.boxHeight - 2);
			}
			//////
			// paint permanent highlights
			//////	
			jPermGraphics.paint();
		}
	}
	
	////// CLEAR PERMLIGHT //////
	//
	//////
	function clearPermlight(){jPermGraphics.clear();}
	
	
	////// DRAW POSITIVELIGHT //////
	// Draws positivelights on the plate
	// Positivelights are drawn over all positives on the plate.
	////
	function drawPositivelight(index){		
		clearPositivelight();
		
		for(var j = 0; j < Y_ELEMENTS; j = j + 2 ){
			for(var i = 0; i < X_ELEMENTS; i = i + 2 ){	
				//////
				// Determine how many positives there are on the quad
				//////
				var numberPositive = 0;
				for(var k = 0; k < 2; k++){
					for(var l = 0; l < 2; l++){
						if(promoterData[index].matrix[j+k][i+l].modified_call == "Positive") {
								numberPositive++;
						} else if (promoterData[index].matrix[j+k][i+l].modified_call == "") {
							if(promoterData[index].matrix[j+k][i+l].call_type == "Positive"){
								numberPositive++;
							}
						}
						
					}
				}
				//////
				// If there are 2 or more colonies that are positive, Draw a highlight
				//////
				if(numberPositive > 1){
					jPositiveGraphics.drawRect(Math.round(this.dims.borderLeft + this.dims.pX + this.dims.elementSpacingWidth  * Math.round((i - 1) / 2) * 2), 
					       Math.round(this.dims.borderTop  + this.dims.pY + this.dims.elementSpacingHeight * Math.round((j - 1) / 2) * 2),
					       (this.dims.boxWidth  - 1) * 2,
					       (this.dims.boxHeight - 1) * 2);
				}
			}
		}
		//////
		// paint!
		//////
		jPositiveGraphics.paint();
	}
	
	////// 
	// 
	////
	function drawBleedlight(index){		
		clearBleedlight();
		
		for(var j = 0; j < Y_ELEMENTS; j = j + 2 ){
			for(var i = 0; i < X_ELEMENTS; i = i + 2 ){	
				//////
				// Determine how many positives there are on the quad
				//////
				var numberPositive = 0;
				for(var k = 0; k < 2; k++){
					for(var l = 0; l < 2; l++){
						if(promoterData[index].matrix[j+k][i+l].bleed_over == "BO") {
								numberPositive++;
						} 
					}
				}
				//////
				// If there are 2 or more colonies that are positive, Draw a highlight
				//////
				if(numberPositive > 0){
					jBleedGraphics.drawRect(Math.round(this.dims.borderLeft + this.dims.pX + this.dims.elementSpacingWidth  * Math.round((i - 1) / 2) * 2), 
					       Math.round(this.dims.borderTop  + this.dims.pY + this.dims.elementSpacingHeight * Math.round((j - 1) / 2) * 2),
					       (this.dims.boxWidth  - 1) * 2,
					       (this.dims.boxHeight - 1) * 2);
				}
			}
		}
		//////
		// paint!
		//////
		jBleedGraphics.paint();
	}
	
	////// 
	// 
	////
	function drawDuplicatelight(index){		
		clearDuplicatelight();
		
		for(var j = 0; j < Y_ELEMENTS; j = j + 2 ){
			for(var i = 0; i < X_ELEMENTS; i = i + 2 ){	
				//////
				// If the colony is a duplicate, rather if the top left of the quad is labelled as duplicate draw the box
				//////
				if(promoterData[index].matrix[j][i] != undefined){
					if(promoterData[index].matrix[j][i].duplicate == "TRUE"){
						jDuplicateGraphics.drawRect(Math.round(this.dims.borderLeft + this.dims.pX + this.dims.elementSpacingWidth  * Math.round((i - 1) / 2) * 2), 
							   Math.round(this.dims.borderTop  + this.dims.pY + this.dims.elementSpacingHeight * Math.round((j - 1) / 2) * 2),
							   (this.dims.boxWidth  - 1) * 2,
							   (this.dims.boxHeight - 1) * 2);
					}
				}
			}
		}
		//////
		// paint!
		//////
		jDuplicateGraphics.paint();
	}
	
	
	////// 
	//
	//////
	function clearPositivelight(){jPositiveGraphics.clear();}
	
	////// 
	// 
	//////
	function clearBleedlight(){jBleedGraphics.clear();}
	
	////// 
	// 
	//////
	function clearDuplicatelight(){jDuplicateGraphics.clear();}
	
	////// DRAW SELECTLIGHT //////
	// Draws Highlights on the plate
	// Selectlights are drawn when a element in the matrix is selected
	////
	function drawSelectlight(coordY, coordX){		
		clearSelectlight();
		jSelectGraphics.drawRect(Math.round(this.dims.borderLeft + this.dims.pX + this.dims.elementSpacingWidth  * coordX), 
								 Math.round(this.dims.borderTop  + this.dims.pY + this.dims.elementSpacingHeight * coordY),
								 this.dims.boxWidth,
								 this.dims.boxHeight);
								
		//////
		// paint!
		//////
		jSelectGraphics.paint();

	}
	////// CLEAR SELECTLIGHT ON PLATE //////
	// Clears the selectlights on the plate
	//////
	function clearSelectlight(){jSelectGraphics.clear();}

	////// DRAW HIGHLIGHT //////
	// Draws Highlights on the plate
	// Highlights are drawn when you mouse over the image or when you mouse over the matrix
	////
	function drawHighlight(coordY, coordX){
		//////
		// These functions draw a box at specific coordinates.
		//////
		jGraphics.setColor('gold');
		jGraphics.drawRect(Math.round(this.dims.borderLeft + this.dims.pX + this.dims.elementSpacingWidth  * Math.round((coordX - 1) / 2) * 2), 
					       Math.round(this.dims.borderTop  + this.dims.pY + this.dims.elementSpacingHeight * Math.round((coordY - 1) / 2) * 2),
					       (this.dims.boxWidth  - 1) * 2,
					       (this.dims.boxHeight - 1) * 2);			
		jGraphics.setColor('pink');			
		jGraphics.drawRect(Math.round(this.dims.borderLeft + this.dims.pX + this.dims.elementSpacingWidth * coordX), 
					       Math.round(this.dims.borderTop + this.dims.pY + this.dims.elementSpacingHeight * coordY),
					       this.dims.boxWidth,
					       this.dims.boxHeight);

		//////
		// These functions draw text at specific coordinates
		//////
		
		if(promoterData[currentIndex].matrix != undefined){
			/////
			// direction mod determines if the text is left or right of the highlight box
			// -1 means its left, 0 means it's right
			/////
			(coordX > X_ELEMENTS / 2) ? directionModX = -1:directionModX = 0;
			(coordY > Y_ELEMENTS / 2) ? directionModY = -1:directionModY = 0;
			
			
			jGraphics.setFont("arial","15px",Font.BOLD); 
			//if(document.getElementById("shadowBox").checked){drawHighlightShadowing(coordY, coordX);	}	// Draw Shadowing if option is checked
			jGraphics.setColor('#D5C9C8'); // EARL GRAY
			
			trans_src = promoterData[currentIndex].matrix[coordY][coordX];
			highlightText(coordY, coordX, promoterData[currentIndex].matrix[coordY][coordX], 0);
		}
		
		//////
		// PAINT!
		//////
		jGraphics.paint();
	} 
	
	function drawHighlightShadowing(coordY, coordX){
		jGraphics.setColor('black'); 
		highlightText(coordY, coordX, promoterData[currentIndex].matrix[coordY][coordX], 1);
	}
	
	function highlightText(coordY, coordX, trans_src, isShadowing){
		////// BASE LOCATIONS //////
		// the base location is the top left hand corner for the text box.
		//////
		var baseLocationX = this.dims.borderLeft + this.dims.pX  + this.dims.boxWidth + 5 
		                    + this.dims.elementSpacingWidth  * coordX 
		                    + SHADOW_DISTANCE * isShadowing
			                + directionModX * DIRECTION_INTENSITY_X;
		var baseLocationY = this.dims.borderTop  + this.dims.pY 
		                    + this.dims.elementSpacingHeight * coordY 
		                    + SHADOW_DISTANCE * isShadowing 
		                    + directionModY * DIRECTION_INTENSITY_Y;
			jGraphics.drawString("Alternate Prey Name: " 	+ trans_src.orf_name, 		Math.round(baseLocationX), Math.round(baseLocationY + 1 * TEXT_SPACING));
			jGraphics.drawString("Prey Gene Name: " 		+ trans_src.common_name, 	Math.round(baseLocationX), Math.round(baseLocationY + 2 * TEXT_SPACING));
			jGraphics.drawString("Prey Family: " 			+ trans_src.info, 			Math.round(baseLocationX), Math.round(baseLocationY + 3 * TEXT_SPACING));
			jGraphics.drawString("Call by Spot-On: " 		+ trans_src.call_type, 		Math.round(baseLocationX), Math.round(baseLocationY + 4 * TEXT_SPACING));
			jGraphics.drawString("Prey Array Coordinate: " 	+ trans_src.array_coord, 	Math.round(baseLocationX), Math.round(baseLocationY + 5 * TEXT_SPACING));
			jGraphics.drawString("MODIFIED Call: " 			+ trans_src.modified_call, 	Math.round(baseLocationX), Math.round(baseLocationY + 6 * TEXT_SPACING));
			jGraphics.drawString("Colony Z-Prime: " 		+ trans_src.z_prime, 		Math.round(baseLocationX), Math.round(baseLocationY + 7 * TEXT_SPACING));
			if(!isShadowing){
				jGraphics.setColor('#7B131E'); 
			}
			jGraphics.drawString("Notes: " 					+ trans_src.note, 			Math.round(baseLocationX), Math.round(baseLocationY + 8 * TEXT_SPACING));
			if(!isShadowing){
				jGraphics.setColor('#D5C9C8'); // EARL GRAY
			}
		/*
		if(document.getElementById("detailBox").checked){
			jGraphics.drawString("WB Name: " + trans_src.wb_name, Math.round(baseLocationX), Math.round(baseLocationY + 8 * TEXT_SPACING));
			jGraphics.drawString("Alt Names: " + trans_src.alt_name, Math.round(baseLocationX), Math.round(baseLocationY + 9 * TEXT_SPACING));
			jGraphics.drawString("Ptp Intensity: " + trans_src.ptp_intensity, Math.round(baseLocationX), Math.round(baseLocationY + 10 * TEXT_SPACING));
			jGraphics.drawString("Manual Call: " + trans_src.human_call, Math.round(baseLocationX), Math.round(baseLocationY + 11 * TEXT_SPACING));
			jGraphics.drawString("Bleed Over: " + trans_src.bleed_over, Math.round(baseLocationX), Math.round(baseLocationY + 12 * TEXT_SPACING));
			jGraphics.drawString("Duplicate: " + trans_src.duplicate, Math.round(baseLocationX), Math.round(baseLocationY + 13 * TEXT_SPACING));
		}*/
	}
	////// CLEAR HIGHLIGHT ON PLATE //////
	// Clears the highlights on the plate
	//////
	function clearHighlight(){jGraphics.clear();}
	
	////////////////////////////////////////////////////////////////////
	//
	////////////////////////////////////////////////////////////////////
	
	
	
	////// SET DIMENSIONS //////
	// Sets the default dimensions, should be relatively accurate.
	// It's better to automatically set the dimensions however
	//////
	function setDimensions(){
		dims.setPosition       (POS_X, POS_Y)                                        ;
		dims.setDimensions     (WIDTH, HEIGHT)                                       ;
		dims.setBorder         (BORDER_TOP, BORDER_BOTTOM, BORDER_LEFT, BORDER_RIGHT);
		dims.setElementSpacing (ELESPACING_W, ELESPACING_H)                          ;
		dims.setBoxDimensions  (BOX_W, BOX_H)                                        ;
	}
	
	////// CONVERT INTERACTION TABLE //////
	// converts a interaction matrix passed to it into innerHTML.
	// This fn will set the matrix values to the new interaction
	// matrix and then reset the mouseover and click functions for the
	// table.
	//////
	// This also gets and sets the heatmap for the interaction
	// table as part of setting up the table
	//////
	function convertInteractionTable(intermatrix, index){
		if(index == currentIndex){	


			var str = "<tbody>";
			for (var j = 0; j < Y_ELEMENTS; j++){
				str = str + "<tr>";
				for (var i = 0; i < X_ELEMENTS; i++){
					if(intermatrix[j] != undefined) {
						if(intermatrix[j][i] != undefined) {
							value = parseInt(intermatrix[j][i].original_intensity);
							
						} else {value = 0;}
					} else {value = 0;}
						
						
					heatMapVal = getHeatMap(value);
					bgColor = "rgb(" + heatMapVal[0] + ", " + heatMapVal[1] + ", " + heatMapVal[2] + ")";
					textColor = "black";
					str = str + "<td class=iatelement style='background-color: " + bgColor + "; color: " + textColor + "'>" + value + "</td>";
					
				} // end var j
				str = str + "</tr>";
			}  // end var i
			
			str = str + "</tbody>";
			
			var matrixTable = document.getElementById('matrixtable');
			matrixTable.innerHTML = str;
			
			
			
		    ////// MOUSE OVER EVENT HANDLER //////
		    // This fn handles the mouseover for the interaction table
		    //////
		    $('.iatelement').mouseover(function(){
					drawHighlight(this.parentNode.rowIndex, this.cellIndex);
					if(current_click == this) return;
					this.style.backgroundColor = MOUSE_OVER_COLOR;
			}); // end mouseover
		    
		    ////// MOUSE OUT EVENT HANDLER //////
		    // This fn handles the mouseout for the interaction table
		    //////
		    $('.iatelement').mouseout(function(){
					clearHighlight();
					if(current_click == this) return;
					heatMapVal = getHeatMap(parseInt(this.innerHTML));
					this.style.backgroundColor= "rgb(" + heatMapVal[0] + ", " + heatMapVal[1] + ", " + heatMapVal[2] + ")";
			}); // end mouseout
			
			////// MOUSE CLICK EVENT HANDLER //////
			// This fn handles the mouseclick for the Interaction table
			//////
			$('.iatelement').click(function(){
					if(current_click != undefined){
						heatMapVal = getHeatMap(parseInt(current_click.innerHTML));
						current_click.style.backgroundColor= "rgb(" + heatMapVal[0] + ", " + heatMapVal[1] + ", " + heatMapVal[2] + ")";
					}
					current_click = this;
					this.style.backgroundColor = MOUSE_CLICK_COLOR;
					convertInfoTable(this.parentNode.rowIndex, this.cellIndex);
			}); // end mouseClick
			
			
			interactionIndex = index
		} // end if(index == currentIndex)
	}
	
	
	
	

	////// CONVERT INFO TABLE //////
	// takes the XY coordinate in the matrix and output the relevant 
	// information onto the matrix information table.
	//////
	function convertInfoTable(coordY, coordX){
		if(promoterData[currentIndex].matrix != undefined){
			info_src = promoterData[currentIndex].matrix[coordY][coordX];
		    
			str =       "<td>" + info_src.common_name + "</td>" ; 
			str = str + "<td>" + info_src.orf_name + "</td>" ;
			str = str + "<td>" + info_src.array_coord + "</td>" ;
			str = str + "<td>" + info_src.z_prime + "</td>" ;
			str = str + "<td>" + info_src.call_type + "</td>" ; 
			str = str + "<td>" + info_src.modified_call + "</td>" ; 
			
			document.getElementById('info-table').innerHTML = str;
			
			//////
			// Also while we are at it, highlight the selections
			//////
			drawSelectlight(coordY, coordX);
		} // end if data
	}; // end convertInfoTable
	
	////// CLEAR INFO TABLE //////
	// clears the info table.
	//////
	function clearInfoTable(){
		    //////////////////////////////////
			str =       "<td>&nbsp;</td>" ; //
			str = str + "<td></td>"       ; //
			str = str + "<td></td>"       ; //
			str = str + "<td></td>"       ; //
			str = str + "<td></td>"       ; //
			str = str + "<td></td>"       ; //
			//////////////////////////////////
		
			document.getElementById('info-table').innerHTML = str;
			current_click = null;
			clearSelectlight();
	}
	function convertPositiveTable(index){
		if(index != currentIndex) {return 0};
		//Table Headers
		str =       "";
		str = str + "<tr>";
		str = str + "<th>Prey Gene Name</th>";
		str = str + "<th>Alternate Prey Name</th>";
		str = str + "<th>Prey Array Coordinate</th>";
		str = str + "<th>Number of Positive Colonies</th>";
		str = str + "<th>Average Raw Intensity</th>";
		str = str + "<th>Average BTB Intensity</th>";
		str = str + "<th>Average Z-Prime Score</th>";
		str = str + "</tr>";
		for(var j = 0; j < Y_ELEMENTS; j=j+2){
			for(var i = 0; i < X_ELEMENTS; i=i+2){
			
				x_val = Math.round((i - 1) / 2) * 2;
				y_val = Math.round((j - 1) / 2) * 2;
				
				total_positive = 0;
				total_original_intensity = 0;
				total_rc_intensity = 0;
				total_ptp_intensity = 0;
				total_z_prime = 0;
				
				
				for(var k = 0; k < 2; k++){
					for(var l = 0; l < 2; l++){
						info_src = promoterData[index].matrix[y_val+k][x_val+l];

						if(info_src.modified_call == "Positive"){
								total_positive++;
								total_original_intensity = total_original_intensity + parseFloat(info_src.original_intensity) ;
								total_rc_intensity       = total_rc_intensity + parseFloat(info_src.rc_intensity);
								total_ptp_intensity      = total_ptp_intensity + parseFloat(info_src.ptp_intensity);
								total_z_prime            = total_z_prime + parseFloat(info_src.z_prime);
						} else if (info_src.modified_call == ""){
							if(info_src.call_type == "Positive"){
								total_positive++;
								total_original_intensity = total_original_intensity + parseFloat(info_src.original_intensity) ;
								total_rc_intensity       = total_rc_intensity + parseFloat(info_src.rc_intensity);
								total_ptp_intensity      = total_ptp_intensity + parseFloat(info_src.ptp_intensity);
								total_z_prime            = total_z_prime + parseFloat(info_src.z_prime);
							}
						}	
					}
				}
				
				if(total_positive > 1){
					ave_original_intensity = total_original_intensity / total_positive;
					ave_rc_intensity = total_rc_intensity / total_positive;
					ave_ptp_intensity = total_ptp_intensity / total_positive;
					ave_z_prime	= total_z_prime / total_positive;
				
					str = str + "<tr>";
					str = str + "<td>" + info_src.common_name + "</td>" ;
					str = str + "<td>" + info_src.orf_name + "</td>" ;
					str = str + "<td>" + info_src.array_coord + "</td>" ;
					str = str + "<td>" + total_positive + "</td>" ;
					str = str + "<td>" + ave_original_intensity + "</td>" ;
					str = str + "<td>" + ave_ptp_intensity + "</td>" ;
					str = str + "<td>" + ave_z_prime + "</td>" ;
					str = str + "</tr>";
				}
				
				
				
			}
		}
		document.getElementById('positive-info-table').innerHTML = str;
	}
	function clearPositiveTable(){
		//Table Headers
		str =       "";
		str = str + "<tr>";
		str = str + "<th>Prey Gene Name</th>";
		str = str + "<th>Alternate Prey Name</th>";
		str = str + "<th>Prey Array Coordinate</th>";
		str = str + "<th>Number of Positive Colonies</th>";
		str = str + "<th>Average Raw Intensity</th>";
		str = str + "<th>Average BTB Intensity</th>";
		str = str + "<th>Average Z-Prime Score</th>";
		str = str + "</tr>";
		
		
		document.getElementById('positive-info-table').innerHTML = str;
		
		}
	
	
	function convertDescriptions(index){
		convertPromoterDescriptions(index);
		convertTranscriptionDescriptions(index);
	}
	
	////// CONVERT DESCRIPTIONS //////
	// Takes a data object passed to it and displays all of the
	// information it can in the description box.
	//////
	function convertPromoterDescriptions(index){
		if(index != currentIndex) return;
		var data = promoterData[index];
		var plateNumber = data.transcriptionData[0].position.plate_num;
											var str = "<p>";
		str = str + "<span class='large-plus-text'>";
		if(data.bait_id 			!= undefined) str = str + "<b>Bait ID:</b> "     + data.bait_id + " ";
		if(data.bait_name 			!= undefined) str = str + "<b>Bait Gene Name:</b> "   + data.bait_name + " ";
		if(data.bait_name2 			!= undefined) str = str + "<b>Alternate Bait Name:</b> " + data.bait_name2 + " ";
		if(data.bait_name3 			!= undefined) str = str + "<b>Bait Family:</b> " + data.bait_name3 + " ";
		str = str + "</span>";
		if(data.background_score	!= undefined) str = str + "<br><b>Background Score:</b> " + data.background_score + " ";
		if(data.list 				!= undefined) str = str + "<b>Prey Array Version:</b> " + data.list + " ";
											str = str + "<b>User:</b> " + data.user_id + " ";
											str = str + "<b>Project:</b> " + data.project_id + " ";
											str = str + "</p>";
		document.getElementById('promoterDescriptions').innerHTML = str;
	}
	function convertTranscriptionDescriptions(index){
		if(index != currentIndex) return;
		var data = promoterData[currentIndex].transcriptionData;
		
		var plate_number_text = ["1-4", "5-8", "9-12"];
		
		var str = "";
		for (i in data){

			                                            str = str + "<p>";
			if(data[i].coordinate != undefined) str = str + "<b>coordinate</b>: " + data[i].coordinate + " ";
			if(data[i].orf_name != undefined) str = str + "<b>orf name</b>: " + data[i].orf_name + " ";
			if(data[i].orf_name2 != undefined) str = str + "<b>orf name 2</b>: " + data[i].orf_name2 + " ";
			if(data[i].wb_gene != undefined) str = str + "<b>wb gene</b>: " + data[i].wb_gene + " ";
			if(data[i].common_name != undefined) str = str + "<b>common name</b>: "  + data[i].common_name + " ";
			if(data[i].info != undefined) str = str + "<b>info</b>: " + data[i].info + " ";
			if(data[i].coordinate2 != undefined) str = str + "<b>coordinate2</b>: "  + data[i].coordinate2 + " ";
			if(data[i].position.plate_num != undefined) str = str + "<b>plate number</b>: " + plate_number_text[data[i].position.plate_num] + " ";
			if(data[i].position.x != undefined) str = str + "<b>x</b>: " + data[i].position.x + " ";
			if(data[i].position.y != undefined) str = str + "<b>y</b>: " + data[i].position.y + " ";
			                                            str = str + "</p>";
		}
		document.getElementById('transcriptionDescriptions').innerHTML = str;
	}
	
	////// GET HEATMAP VALUE //////
	// based on a value returns a color in the format rrggbb in hex
	//////
	function getHeatMap(val){
		HEAT_CONTRAST   = 2    ;
		HEAT_MIN        = 80   ;
		HEAT_COL_MIN    = 0    ;
		HEAT_COL_MAX    = 200  ;
		heatMapValue = (val - HEAT_MIN)*HEAT_CONTRAST;
		heatMapValue = Math.max(HEAT_COL_MIN, Math.min(HEAT_COL_MAX, heatMapValue));
		
		return [255 - heatMapValue, 255 - heatMapValue, 255];

	}


	////// ON LOAD EVENT HANDLER //////
	// function Onload
	//////
	var jGraphics;
	var jh;
	window.onload=function(){
		//////	//////	//////	//////	//////	//////	//////	//////	
		// Any Regular onload functions should go here
		//////
		 
		////// 
		// loads the first interaction matrix
		// also displays the first interaction matrix
		//////
		loadIntensityValues(0);
		 
		//////
		// Initialize Galleriffic Gallery
		//////
		
		//////
		// Initialize Opacity Changes in Thumbnails
		//////
		var onMouseOutOpacity = 0.67;
		$('#thumbs ul.thumbs li').opacityrollover({
			mouseOutOpacity:   onMouseOutOpacity,
			mouseOverOpacity:  1.0,
			fadeSpeed:         'fast',
			exemptionSelector: '.selected'
		});

		//////
		// Initialize Advanced Galleriffic Gallery
		//////
		var gallery = $('#thumbs').galleriffic({
			delay:                     3500,
			numThumbs:                 7,
			preloadAhead:              5,
			enableTopPager:            false,
			enableBottomPager:         true,
			maxPagesToShow:            7,
			imageContainerSel:         '#slideshow',
			controlsContainerSel:      '#controls',
			captionContainerSel:       '',
			loadingContainerSel:       '#loading',
			renderSSControls:          true,
			renderNavControls:         true,
			playLinkText:              'Play',
			pauseLinkText:             'Pause',
			prevLinkText:              '&lsaquo;&lsaquo; Prev',
			nextLinkText:              'Next &rsaquo;&rsaquo;',
			nextPageLinkText:          'Next &rsaquo;',
			prevPageLinkText:          '&lsaquo; Prev',
			enableHistory:             false,
			autoStart:                 false,
			syncTransitions:           true,
			defaultTransitionDuration: 900,
			onSlideChange:             function(prevIndex, nextIndex) {
				// function onSlideChange
				//////
				// The slide is changing, Set the current index to the 
				// index of the next slide. 
				//////	
				if(currentIndex != nextIndex){
					currentIndex = nextIndex;
					//////
					// If the matrix data is not loaded, use a  
					// JqueryAjax call to get the matrix data. Then show
					// the new Interaction Table
					//////
					if(promoterData[currentIndex].matrix == undefined){
						loadIntensityValues(currentIndex);
					} else {
					//////
					// If the matrix data is already loaded, just show the 
					// new interaction table
					//////
						convertInteractionTable(window.promoterData[currentIndex].matrix, currentIndex);
						convertDescriptions(currentIndex);
					}
					//This has changed the position of the slideshow so everything needs to be redrawn
					dispImage = $('span.current a').find('img');
					dims.autoSetDimensions(dispImage[0]);
					
					//////
					// Reset values that need to be reset
					//////
					$("#positiveBox").val("Highlight Positive Interactions");
					$("#bleedBox").val("Enable Bleed Over Highlights");
					$("#duplicateBox").val("Enable Duplicate Highlights");
					drawPermlight(currentIndex);
					//////
					// Clear Data that needs to be cleared.
					//////
					clearInfoTable();
					//////
					// Clear Highlights
					//////
					clearPositivelight();
					clearBleedlight();
					clearDuplicatelight();
					//////
					// Clear the positive table if it has been populated
					//////
					clearPositiveTable();
				}
				//////
				// 'this' refers to the gallery, which is an extension of $('#thumbs')
				//////
				this.find('ul.thumbs').children()
					.eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
					.eq(nextIndex).fadeTo('fast', 1.0);
			},
			onPageTransitionOut:       function(callback) {
				this.fadeTo('fast', 0.0, callback);
			},
			onPageTransitionIn:        function() {
				this.fadeTo('fast', 1.0);
			},
			onImageLoad:			   function() {
				//////
				// function SlidesshowOnLoads
				//////
				
				if(!slideshowLoaded){
					slideshowLoaded = true;
					
					/////
					// Set up transcription description
					/////
					convertDescriptions(currentIndex);
					
					//////
					// Sets up some default dimensions so that if the below code fails
					// the website will still function, maybe
					//////
					setDimensions();
					
					//////
					// autoSetDimensions is called to accurately set dimensions
					//////

					dispImage = $('span.current a').find('img');
					dims.autoSetDimensions(dispImage[0]);
					dims.pY -= HEADER_HEIGHT; // adjust dims for the header element height
					
					//////
					// All permanent drawings should go onto jPermGraphics, 
					//////
					drawPermlight(0);
					
					
				}
				
				//////
				// Set up events for image
				////// function ImageEvents
				
				var oldMouseCoordX = -1;
				var oldMouseCoordY = -1;
				
				//////
				// Detect when the window is resized and take action to reset the dimensionality
				//////
				$(window).resize(function() {
					dispImage = $('span.current a').find('img');
					dims.autoSetDimensions(dispImage[0]);
				});
				
				//////
				// Detect when the mouse is over the image
				//////
				$(".current a img").mouseenter(function() {
					//////
					// Start searching coordinates
					//////
					$(window).unbind('mousemove');
					$(window).mousemove(function(e){
						//////
						// Find the mouse location
						//////
						var mouseLocationX = e.pageX - dims.pX;
						var mouseLocationY = e.pageY - dims.pY;
						
						//////
						// Detect when the mouse leaves the image
						//////
						// imageBuffer makes the right and the bottom edge of the slideshow more responsive for mouseover highlights
						var imageBuffer = 6;
						if(mouseLocationX < 0    || mouseLocationX >= dims.width + imageBuffer
						   || mouseLocationY < 0 || mouseLocationY >= dims.height + imageBuffer) {
							   /////
								// Reset the Old Cells background color
								//////
							
								$oldCell = $("#matrixtable").find("tr").eq(oldMouseCoordY).find("td").eq(oldMouseCoordX);
								value = $oldCell.html();
								heatMapVal = getHeatMap( parseInt($oldCell.html()) );
								bgColor = "rgb(" + heatMapVal[0] + ", " + heatMapVal[1] + ", " + heatMapVal[2] + ")";
								$oldCell.css("background-color",  bgColor  );
								
								/////
								// Reset variables, clear the highlight and remove mouse move
								/////
								oldMouseCoordX = -1;
								oldMouseCoordY = -1;
								clearHighlight();
								
								
	
								$(window).unbind('mousemove');
								return 0;
						   }
						
						//////
						// Find which coordinate on the plate the mouse location corresponds to
						//////
						var mouseCoordX = Math.floor((mouseLocationX - dims.borderLeft) / dims.elementSpacingWidth);
						var mouseCoordY = Math.floor((mouseLocationY -HEADER_HEIGHT - dims.borderTop)  / dims.elementSpacingHeight);
					   
						//////
						// Sanity check the mouse coordinate
						/////
						if(mouseCoordX <  0)          {mouseCoordX = 0;}
						if(mouseCoordX >= X_ELEMENTS - 1) {mouseCoordX = X_ELEMENTS - 1;}
						if(mouseCoordY <  0)          {mouseCoordY = 0;}
						if(mouseCoordY >= Y_ELEMENTS - 1) {mouseCoordY = Y_ELEMENTS - 1;}
						
						//////
						// If the mouse coord is different than previous mouse coord
						// clear the highlight and make a new coordinate
						//////
						if(mouseCoordX != oldMouseCoordX || mouseCoordY != oldMouseCoordY){
							clearHighlight();
							drawHighlight(mouseCoordY, mouseCoordX);

							$("#matrixtable").find("tr").eq(mouseCoordY).find("td").eq(mouseCoordX).css('background-color', "#1E90FF");
							
							/////
							// Reset the Old Cells background color
							//////
							if(oldMouseCoordX != -1 && oldMouseCoordY != -1){
								$oldCell = $("#matrixtable").find("tr").eq(oldMouseCoordY).find("td").eq(oldMouseCoordX);
								value = $oldCell.html();
								heatMapVal = getHeatMap( parseInt($oldCell.html()) );
								bgColor = "rgb(" + heatMapVal[0] + ", " + heatMapVal[1] + ", " + heatMapVal[2] + ")";
								$oldCell.css("background-color",  bgColor  );
							}
							
							///////
							// Set up the coordinate we just made as the current coordinate
							//////
							oldMouseCoordX = mouseCoordX;
							oldMouseCoordY = mouseCoordY;
						}
						
					});

				});
				
			}
			
		});

		////////////////////////////////////////////////////////////////
		var color;

		
		
		//////
		// Initialize Drawing Canvases here
		//////
		jPermGraphics   = new jsGraphics('permlight')  ;
		jGraphics       = new jsGraphics('highlight')  ; 
		jSelectGraphics = new jsGraphics('selectlight');
		jPositiveGraphics = new jsGraphics('positivelight');
		jBleedGraphics = new jsGraphics('bleedlight');
		jDuplicateGraphics = new jsGraphics('duplicatelight');
		
		//////
		// Any drawings which have standardized settings should have those settings go here
		//////
			/* jGraphics */
		jGraphics.setColor('pink');
		jGraphics.setStroke(2);
			/* jPermGraphics */
		jPermGraphics.setColor('blue');
		jPermGraphics.setStroke(2);
			/* jSelectGraphics */
		jSelectGraphics.setColor('purple');
		jSelectGraphics.setStroke(2);
		    /* jPositiveGraphics */
		jPositiveGraphics.setColor('cyan');
		jPositiveGraphics.setStroke(2);
			/* jBleedGraphics */
		jBleedGraphics.setColor('yellow');
		jBleedGraphics.setStroke(2);
			/* jDuplicateGraphics */
		jDuplicateGraphics.setColor('red');
		jDuplicateGraphics.setStroke(2);
		
		//////
		// Set up Jquery handles
		////// function Jquery
	
		$("#positiveBox").click(function() {
			if(interactionIndex == currentIndex){
				/////
				// If enable
				/////
				if( $("#positiveBox").val() == "Highlight Positive Interactions" ){
					$("#positiveBox").val("Hide Positive Interactions");
					drawPositivelight(currentIndex);
				//////
				// If Disable
				//////
				} else if ($("#positiveBox").val() == "Hide Positive Interactions" ) {
					$("#positiveBox").val("Highlight Positive Interactions");
					clearPositivelight();
				}
			} else {
				alert("Please wait for the interaction table to load before trying to enable positive highlight");
			}
		});
		$("#bleedBox").click(function() {
			if(interactionIndex == currentIndex){
				/////
				// If enable
				/////
				if( $("#bleedBox").val() == "Enable Bleed Over Highlights" ){
					$("#bleedBox").val("Disable Bleed Over Highlights");
					drawBleedlight(currentIndex);
				//////
				// If Disable
				//////
				} else if ($("#bleedBox").val() == "Disable Bleed Over Highlights" ) {
					$("#bleedBox").val("Enable Bleed Over Highlights");
					clearBleedlight();
				}
			} else {
				alert("Please wait for the interaction table to load before trying to enable bleed highlight");
			}
		});
		$("#duplicateBox").click(function() {
			if(interactionIndex == currentIndex){
				/////
				// If enable
				/////
				if( $("#duplicateBox").val() == "Enable Duplicate Highlights" ){
					$("#duplicateBox").val("Disable Duplicate Highlights");
					drawDuplicatelight(currentIndex);
				//////
				// If Disable
				//////
				} else if ($("#duplicateBox").val() == "Disable Duplicate Highlights" ) {
					$("#duplicateBox").val("Enable Duplicate Highlights");
					clearDuplicatelight();
				}
			} else {
				alert("Please wait for the interaction table to load before trying to enable duplicate highlight");
			}
		});
	}
	
	////// handleCallSwitch //////
	// handles the call switch button
	//////
	
	function handleCallSwitch(quadCall){	
		if(current_click == null) {
			alert("No transcription factor selected, Click on a cell in the table in order to select that cell before trying to change the call");
			return;
		}
		
		//////
		// value collecting
		// call_val is gotten by reading the value in the input box
		// y_val and x_val are gotten through the currently clicked on element
		// plateName is gotten through the idbait and the position
		//////
		
		var call_val = document.getElementById("human-call").value;
		
		var y_val = current_click.parentNode.rowIndex;
		var x_val = current_click.cellIndex;
		
		var plateNumber = promoterData[currentIndex].transcriptionData[0].position.plate_num;
		var plateName = promoterData[currentIndex].bait_id;

		//////
		// Checks to see if the call was a quad call option and calls based on that
		//////
		if(quadCall){
			x_val = Math.round((x_val - 1) / 2) * 2;
			y_val = Math.round((y_val - 1) / 2) * 2;
			
			////// updateCallQuad //////
			// updates the call as long as everything is OK
			//////
			quadNumber = 0;
			updateCall(plateName, plateNumber, x_val    , y_val    , call_val, currentIndex, quadCall);
			updateCall(plateName, plateNumber, x_val + 1, y_val    , call_val, currentIndex, quadCall);
			updateCall(plateName, plateNumber, x_val    , y_val + 1, call_val, currentIndex, quadCall);
			updateCall(plateName, plateNumber, x_val + 1, y_val + 1, call_val, currentIndex, quadCall);
		} else {
			////// updateCall //////
			// updates the call as long as everything is OK
			//////
			updateCall(plateName, plateNumber, x_val, y_val, call_val, currentIndex);
		}
		
		
	}
	
	function handlePositiveTable(){
		convertPositiveTable(currentIndex);
	}

	//////
	// File Download Functions
	//////
	/*
	function downloadFile(){
		var data = getPositiveTable();
		var promoter = promoterData[currentIndex];
		 $("#downloadButton").empty().html('<img src="<?php echo $base_url?>loader.gif"/>');
		 
		 // Make Ajax call
		 $("#fileDownload").load('<?php echo $base_url?>index.php/downloads/downloadFile', { data : data, promoter : promoter}, function(answer){
			 		
				if(answer == ""){
					alert("file does not exist");
				} else if(answer == "No data"){
					alert("There is no data on that plate!");
				} else{
					var url = eval(answer);
					window.location.href = url;
				}
				$("#fileDownload").empty().html("Click here to download the data file in .csv format <input id='btnLoad' type='button' name='filedownload' value='download' onclick='downloadFile();'></input>");
			 }); 
	}*/
	
	function downloadSequenceFile(){
		var bait = promoterData[currentIndex].bait_id;
		var bait_genename = promoterData[currentIndex].bait_name;
		var bait_altname = promoterData[currentIndex].bait_name2;
		var userId    = $('#user_id').val();
		var projectId = $('#project_id').val();
		//alert(projectId);
		if(projectId == "701"){
			 $("#loader").empty().html('<img src="<?php echo $base_url?>loader.gif"/>');
			 
			 // Make Ajax call
			 $("#loader").load('<?php echo $base_url?>index.php/downloads/downloadSequenceFile', { bait : bait, bait_genename: bait_genename, bait_altname: bait_altname}, function(answer){
					if(answer == ""){
						alert("file does not exist");
					} else if(answer == "No data"){
						alert("There is no data for that plate!");
					} else{
						var url = eval(answer);
						window.location.href = url;
					}
					$("#loader").empty();
				 });
		} else {
			alert("This feature only works for the 701 project");
		}
	}
	//
	/*
	$.ajax({
		url : '<?php echo $base_url?>index.php/data/getIntensityData',
		type : 'post',
		data : {
			promData   : promoterData[index],
			index      : index,
			project_id : promoterData[index].project_id,
			user_id    : promoterData[index].user_id
		},
		success : function(answer){
			//////
			// load in the new data from ajax into the matrix
			//////
			promoterData[index].matrix = eval( "(" + answer + ")" );
			//////
			// Convert tables based on new information
			//////
			convertInteractionTable(window.promoterData[index].matrix, index);
			convertDescriptions(index);
		}
	});
	*/
	
	
	function getPositiveTable(){
		
			var index = currentIndex;
			
			data = new Array();
			
			
			data_list = new Array();
			tf_list = new Array();
			
			for(var i = 0; i < X_ELEMENTS; i=i+2){
				for(var j = 0; j < Y_ELEMENTS; j=j+2){
					
					var temp = new Array();
					
					x_val = Math.round((i - 1) / 2) * 2;
					y_val = Math.round((j - 1) / 2) * 2;
					
					total_positive = 0;
					total_original_intensity = 0;
					total_rc_intensity = 0;
					total_ptp_intensity = 0;
					total_z_score = 0;
					total_z_prime = 0;
					
					
					for(var k = 0; k < 2; k++){
						for(var l = 0; l < 2; l++){
							info_src = promoterData[index].matrix[y_val+k][x_val+l];
	
							if(info_src.modified_call == "Positive"){
									total_positive++   
									tf_list.push(info_src.orf_name); 
									total_original_intensity = total_original_intensity + parseFloat(info_src.original_intensity) ;
									total_rc_intensity = total_rc_intensity + parseFloat(info_src.rc_intensity);
									total_ptp_intensity = total_ptp_intensity + parseFloat(info_src.ptp_intensity);
									total_z_score = total_z_score + parseFloat(info_src.z_score);
									total_z_prime = total_z_prime + parseFloat(info_src.z_prime);
							} else if (info_src.modified_call == ""){
								if(info_src.call_type == "Positive"){
									tf_list.push(info_src.orf_name);  
									total_positive++;
									total_original_intensity = total_original_intensity + parseFloat(info_src.original_intensity);
									total_rc_intensity = total_rc_intensity + parseFloat(info_src.rc_intensity);
									total_ptp_intensity = total_ptp_intensity + parseFloat(info_src.ptp_intensity);
									total_z_score = total_z_score + parseFloat(info_src.z_score);
									total_z_prime = total_z_prime + parseFloat(info_src.z_prime);
								}
							}	
						}
					}
					
					if(total_positive > 1){
						ave_original_intensity = total_original_intensity / total_positive;
						ave_rc_intensity = total_rc_intensity / total_positive;
						ave_ptp_intensity = total_ptp_intensity / total_positive;
						ave_z_score = total_z_score / total_positive;
						ave_z_prime = total_z_prime / total_positive;
			
						temp[0] = info_src.array_coord;
						temp[1] = info_src.common_name;
						temp[2] = info_src.orf_name;
						temp[3] = info_src.wb_name;
			
						//temp[4] = ave_original_intensity;
						//temp[5] = ave_rc_intensity;
						temp[4] = ave_ptp_intensity;
						//temp[7] = ave_z_score;
						temp[5] = ave_z_prime;
						
						data.push(temp);
					}
					
					
					
				}
			}
			//alert(tf_list);
			//return data_list;
			return data;
	}

	//////
	// function EndScript
	//////
</script>
<script>
			function downloadFile(){
				var user = $('#user_id').val();
				var project = $('#project_id').val();
				var promoters = promoterData[currentIndex].bait_id;
				//$("#promoter").val();
				//var tfs = $("#transcriptor").val();
				
				var tfs = '';
				
				
				
				if(promoters != ""){
					promoters = promoters.replace(/ /g, '');
					promoters = '"'+promoters.replace(/,/g, '","')+'"';
				}
				if(tfs != ""){
					tfs = tfs.replace(/ /g, '');
					tfs = '"'+tfs.replace(/,/g, '","')+'"';
				}

				$("#loader").html('<img src="http://franklin-umh.cs.umn.edu/UMassProject/images/loader.gif"/>');
				 
				$.ajax({
					url : '<?php echo $base_url?>index.php/network_view/downloadNetwork',
					type : 'post',
					data : {
						user : user,
						project : project,
						promoters: promoters,
						tfs: tfs
					},
					success : function(answer){	
						if(answer == "error"){
							alert("file does not exist");
						} else if (answer == "data"){
							alert("There are no interactions for your input");
						} else {
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
	<!--Drawing Divs-->
	<div id="duplicatelight"></div>
	<div id="bleedlight"></div>
	<div id="positivelight"></div>
	<div id="permlight"></div>
	<div id="highlight"></div>
	<div id="selectlight"></div>  
	<!-- END DRAWING-->  
	<div id="promoterDescriptions" class="large-text small-padding small-margin"><p></p></div>
	<div id="results-container">
		
		<!-- we will add our HTML content here -->
		<div class="background medium-margin medium-padding half-width" id="container">
			
			<!-- Start Minimal Gallery Html Containers -->
			<div id="gallery" class="content">
				<div id="controls" class="controls"></div>
				<div class="slideshow-container">
					<div id="loading" class="loader"></div>
					<div id="slideshow" class="slideshow"></div>
				</div>
			</div>
			<!-- Start Thumbnail Implementation -->
			<div id="thumbs" class="navigation">
				<ul class="thumbs noscript">
					<?php
					$list_tags      = array(0=>"_1-4", 1=>"_5-8", 2=>"_9-12");
					$imagepath      = "http://franklin-umh.cs.umn.edu/UMassProject/"."images/";
					$thumbpath      = "http://franklin-umh.cs.umn.edu/UMassProject/"."thumbs/";
					$errorImage     = "imageError.png";
					$imageSuffix    = "_5mM_Xgal_7d_W.cropped.resized.grey.png";
					$thumbSuffix    = "._thumb.png";
					$deadImageCount = 0;
					
					$i = 0;
					foreach($promoterData as $id){
						if(isset($id->image))
						{
							$imagefile = $id->image;
						} else {
							$deadImageCount++;
							$imagefile = $errorImage;
						}
						
						$image = $imagepath . $imagefile;
						$thumb = $thumbpath . $imagefile . "._thumb.png";
						/*
						$plateNumber = $transcriptionData[0]->position['plate_num'];
						if($plateNumber == -1){
							$plateNumber = $i;
						}
						*/
						$title = $id->bait_id . $list_tags[ $id->transcriptionData[0]->position['plate_num'] ];
					
						//$title = $idbait;
						////////////////////////////////////////////////
						$output  = "<li>\n";
						$output .= "	<a class='thumb' href='$image' title='$title'>\n";
						$output .= "		<img src='$thumb' alt='$title' />\n";
						$output .= "	</a>\n";
						$output .= "</li>\n";
						
						echo $output;
						////////////////////////////////////////////////
						$i++;
					} // end foreach*/
					?>
				</ul>
			</div>
			<!-- End Thumbnail Implementation -->
			<!-- End Minimal Gallery Html Containers -->
			<div id="slideshow-controls" class="center small-padding">
				<!--<p>
					<INPUT type="checkbox" id="shadowBox" /> Enable Shadowing
					<INPUT type="checkbox" id="detailBox" /> Enable Details
				</p>-->
				<p>
					<INPUT type="button" id="positiveBox" value="Highlight Positive Interactions">
					<!--<INPUT type="button" id="bleedBox" value="Enable Bleed Over Highlights">-->
					<!--<INPUT type="button" id="duplicateBox" value="Enable Duplicate Highlights">-->
				</p>
			</div>
			<div style="clear: both;"></div>
			
			<div id="transcriptionDescriptions" class="medium-text small-padding small-margin"><p></p></div>
			<div id='fileDownload'>
				Click here to download interactions for the current bait
				<input id='btnLoad' type='button' name='filedownload' value='download' onclick='downloadFile();'></input>
			</div>
			<div id="sequenceDownload">
				Click here to download sequence and genome coordinates for this DNA bait in csv format
				<input type='button' name='seqdownload' value='download' onclick='downloadSequenceFile()'></input>
			</div>
			<div id="loader"></div>
		</div>
		<!-- Interaction Table -->
		<div id="interactions" class="background medium-margin medium-padding half-width">
		<div id="interaction-table">
			<p> 
				<table class='small-text standardTable' border='1' id='matrixtable' style='width: 1100px;'>
					<tr>
						<td class='iatelement'>The table is loading</td>
						<td class='iatelement'>The table is loading</td>
					</tr>
					<tr>
						<td class='iatelement'>The table is loading</td>
						<td class='iatelement'>The table is loading</td>
					</tr>
				</table>
			</p>
		</div>
		<div id='matrix-info'>
			<p>
				<table id='matrix-info-table' class="medium-text standardTable" style='width: 1100 px;'>
					<tr>
						<th>Prey Gene Name</th>
						<!--<th>Original Intensity</th>-->
						<!--<th>RC Intensity</th>-->
						<th>Alternate Prey Name</th>
						<!--<th>Z Score</th>-->
						<th>Prey Array Coordinate</th>
						<th>Z-Prime Score</th>
						<th>Called by Spot-On</th>
						<th>Manual Call</th>
					</tr>
					<tr id='info-table'>
						<td>&nbsp;</td>
						<td></td> 
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>
			</p>
		</div>
		<div id='call-switch'>
			<p>
			Change Interaction Call to: 
			<select id="human-call" name="human-call">
				<option value="Positive">Positive</option>
				<option value="Negative">Negative</option>
			</select> 
			<button type="button" class="callSwitchButton" id="callSwitchButton" onClick="handleCallSwitch(0)" >Change Selected Colony</button>
			<button type="button" class="quadSwitchButton" id="quadSwitchButton" onClick="handleCallSwitch(1)" >Change Entire Quad</button>
			<button type="button" id="callSwitchButton" onClick="callPlateEntire()" >Change All Calls on the Plate</button><br />
			</p>
		</div>
		<div id='positive-info' class='matrix-info'>
			<p>
				<b>Positive Call Table</b>
				<table id='positive-info-table' class='medium-text standardTable' style='width: 1100 px;'>
					<tr>
						<th>Prey Gene Name</th>
						<th>Alternate Prey Name</th>
						<th>Prey Array Coordinate</th>
						<th>Number of Positive Colonies</th>
						<th>Average Raw Intensity</th>
						<th>Average BTB Intensity</th>
						<th>Average Z-Prime Score</th>
					</tr>
				</table>
			</p>
			<button type="button" id="positiveTableButton" onClick="handlePositiveTable()" >Populate Positive Table</button>
		</div>
		
		<!--div class="footer" id="footer">
		<ul class="copyright"><li>&copy; 2009-2011 Regents of the University of Minnesota. All rights reserved.</li>
		<li>The University of Minnesota is an equal opportunity educator and employer</li>
		<li>Last modified on July 1, 2011</li></ul>
		</div-->
	</div>         
</body>                                                                
</html>
