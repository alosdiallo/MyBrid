<?php 
$base_url = base_url();
?>

<html>
	<head>    
		<title>MyBrid</title>
		<!-- CSS FILES -->
		<link href="<?php echo $base_url?>css/universal.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $base_url?>css/jquery.ui.all.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $base_url?>css/slideshow.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $base_url?>css/results.css" rel="stylesheet" type="text/css">

		<!-- JAVASCRIPT FILES -->
		<script>
			var base_url = "<?php echo $base_url?>";
			var userMem = "<?php echo $this->session->userdata('user_mem')?>";
			var projectMem = "<?php echo $this->session->userdata('project_mem')?>";
		</script>
		<script type="text/javascript" src="https://getfirebug.com/firebug-lite.js"></script>

		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery-1.6.2.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.core.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.widget.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.position.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.autocomplete.js"></script>
		<!--script type="text/javascript" src="<?php echo $base_url?>javascript/array_unique.js"></script-->
		<script type="text/javascript" src="<?php echo $base_url?>javascript/project_control.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/wz_jsgraphics.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/overlay_highlights.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ad-gallery.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/dimensions.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.galleriffic.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.opacityrollover.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/download_browse_interactions.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/slideshow.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/update_call.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/autocomplete_promoter_transcription_factor.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/convertDescriptions.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/tables_browse_interactions.js"></script>
		
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
			//var HEADER_HEIGHT = 0;
			// How far vertically apart text is from each other, should be near text size
			var TEXT_SPACING = 15;
			// How far the text has to go in order to be to the other side of the box on the highlight
			var DIRECTION_INTENSITY_X = 250;
			var DIRECTION_INTENSITY_Y = 125;
			// the color for mouseover and mouseclicks
			var MOUSE_OVER_COLOR  = "rgb(30,	144,	255)";
			var MOUSE_CLICK_COLOR = "rgb(77,	77,		77)";
			// the number of X or Y elements on the plate
			var X_ELEMENTS = 48;
			var Y_ELEMENTS = 32;
			// Keeps track of how many pixels away the shadow is from the text
			var SHADOW_DISTANCE = 1;		
			// contains the data for the plates
			var promoterData      = eval([{"bait_id":"NULL","bait_name":"NULL","bait_name2":"NULL","bait_name3":"NULL","background_score":null,"user_id":"NULL","project_id":"NULL","plate_number":"NULL","transcriptionData":[{"coordinate":"NULL","orf_name":"NULL","orf_name2":"NULL","wb_gene":"NULL","common_name":"NULL","alt_name":null,"info":"NULL","info2":null,"note":null,"coordinate2":"NULL","duplicate":"NULL","x_coord":"0","y_coord":"0","plate_number":"0","list":"NULL","position":{"plate_num":0,"x":0,"y":0}}],"list":"NULL","image":"EA_A01_1-4_5mM_Xgal_7d_W.cropped.resized.grey.png"}]);
	
			////// CURRENT CLICK //////
			// the table element that is currently selected
			var current_click = undefined;
			///// LIST TAGS /////
			// Converts plate numbers to a tag for the images usually
			//var list_tags = ["_1-4", "_5-8", "_9-12"]	
		</script>
		<script>
			var jh;
			onload=function(){
				//hideSlideshow();
				//alert("onload");
				loadIntensityValues(0);
				setupHighlightOverlays();
				setupHighlightsFromButtons();
				var color;
				slideshow_InitializeGallery();
				catchPost();
				//alert("reached catch point");
			}
			
			function catchPost(){
				if("<?php echo (isset($_POST['hasPost'])?$_POST['hasPost']:"")?>" == "true"){
					//$("#user_id").val("<?php echo(isset($_POST['user_id'])?$_POST['user_id']:"");?>");
					//$("#project_id").val("<?php echo(isset($_POST['project_id'])?$_POST['project_id']:"");?>");
					$("#promoter").val("<?php echo(isset($_POST['promoter'])?$_POST['promoter']:"");?>");
					$("#transcriptionFactor").val("<?php echo(isset($_POST['transcriptor'])?$_POST['transcriptor']:"");?>");
					search("<?php echo(isset($_POST['user_id'])?$_POST['user_id']:"");?>", "<?php echo(isset($_POST['project_id'])?$_POST['project_id']:"");?>");
					showAdvanced();
				}
			}
		</script>
		<script>
			function hideSlideshow(){
				$("#slideshowHider").hide(); 
			}
			function showSlideshow(){
				$("#slideshowHider").show();
				slideshow_AutoSetDimensions();
				slideshow_ResetChangables();
			}
		</script>
		<script>
			var andSearch = "FALSE";
			function showAdvanced(){
				$("#combinedHider").hide();
				$("#advancedHider").show();
				
				// Reset values that aren't going to shown in advanced search.
				$("#combined").val("");
				andSearch = "TRUE";
				//$("#andSearch").val("TRUE");

				
				$("#advancedhiderbuttondiv").html('<button onClick="hideAdvanced()">Hide Advanced Options</button>');
				slideshow_AutoSetDimensions();
				slideshow_ResetChangables();
			}
			function hideAdvanced(){
				$("#advancedHider").hide();
				$("#combinedHider").show();
				
				// Reset advanced search values
				$("#promoter").val("");
				$("#transcriptionFactor").val("");
				andSearch = "FALSE";
				
				//$("#andSearch").val("FALSE");
				
				$("#advancedhiderbuttondiv").html('<button onClick="showAdvanced()">Show Advanced Options</button>');
				slideshow_AutoSetDimensions();
				slideshow_ResetChangables();
			}
		</script>
		<script>
			function setModifyCall(user){
				if(<?php echo $this->session->userdata('admin')?> || (<?php echo '"'.strtoupper($this->session->userdata['username']).'"';?> == user.toUpperCase())){
					$("#call-switch").show();
				} else {
					$("#call-switch").hide();
				}
			}
			
			function search(user, project){
				// Optional arguments
				user = (typeof user == "undefined")?$("#user_id").val():user;
				project = (typeof project == "undefined")?$("#project_id").val():project;
				//var user = $("#user_id").val();
				//var project = $("#project_id").val();
				
				
				//var andSearch = $("#andSearch").val();
				
				var combined = $("#combined").val();
				var promoter = $("#promoter").val();
				var transcriptionFactor = $("#transcriptionFactor").val();
				
				var positive = $("#positive").prop("checked"); // is the Checkbox marked?
				var bleedover = $("#bleedover").prop("checked"); // is the Checkbox marked?
				
				$("#errorMessage").html("");
				
				$.ajax({
					url : '<?php echo $base_url?>index.php/data/browseInteractionsSearch_2',
					type : 'post',
					data : {
						user_id: user,
						project_id: project,
						combined: combined,
						promoter: promoter,
						transcriptor: transcriptionFactor,
						positiveSearch: positive,
						bleedoverSearch: bleedover,
						andSearch: andSearch
					},
					success : function(answer){
						if(answer == "NOTLOGGEDIN"){
							// The user is not logged in
							hideSlideshow();
							$("#message").show();
						} else if (answer == "QUERYFAILED"){
							// The query has returned no results
							$("#errorMessage").html("<span class='huge-text red-text'>The query you entered has returned no results </span><br><br>");
							hideSlideshow();
							$("#message").show();
						} else if (answer == "NOSEARCH"){
							// The user inputted nothing into the search boxes.
							$("#errorMessage").html("");
							hideSlideshow();
							//$("#message").show();
						} else {
							$("#message").hide();
							
							// The search was probably successful
							promoterData = eval(answer);
							showSlideshow();
							slideshow_LoadNewData();
							slideshow_InitializeGallery();
							loadIntensityValues(0);
							setModifyCall(user);
							
							
							slideshow_AutoSetDimensions();
							slideshow_ResetChangables();
						}
					}
				});

			}
		</script>
		
	</head>
	<body>
		<div id="head" class="medium-padding">
			<div id="session_controls" class="alignright">
				<span id="user_session_controls">
					<FORM METHOD="LINK" ACTION="<?php echo $base_url?>index.php/login/logout/" class="alignright">
						<INPUT TYPE="submit" VALUE="Logout">
					</FORM>
					<FORM METHOD="LINK" ACTION="<?php echo $base_url?>" class="alignright">
						<INPUT TYPE="submit" VALUE="Back to Homepage">
					</FORM>
				</span>
			</div>
			<!--input type="submit" value="Search" /-->
			<!--z-score &gt; <input type="text" id="zscore" name="zscore" size="3" autocomplete=off />-->
			<!--z-prime &gt; <input type="text" id="zprime" name="zprime" size="3" autocomplete=off />-->
			
			<!--<input type="checkbox" id="bleedover" name="bleedoverSearch" value="True" /> Bleed Over-->
			<span id="user_project_controls">
				
				<span id="project_controls" >
					<select id="project_id" name="project_id" class="alignright"></select>
				</span>
				<div class="alignright"><b>Project: </b></div>
				<span id="user_controls" >
					<select id="user_id" name="user_id" class="alignright"></select>
				</span>
				<div class="alignright"><b>User: </b></div>
			</span>
			
			<span id="combinedHider">
				<input type="text" id="combined" size="58" autocomplete=off style="border-width: 0px;"/>
				<!--select id="andSearch" name="andSearch" style="border-width: 0px; margin-left: -5px;">
					<option value="FALSE">Bait OR Prey</option>
					<option value="TRUE">Bait AND Prey</option>
				</select-->
			</span>
			<span id="advancedHider" style="display: none;">
				Bait: <input type="text" id="promoter" name="promoter" size="30" autocomplete=off />
				Prey: <input type="text" id="transcriptionFactor" name="transcriptor" size='30' autocomplete=off />
			</span>
			
			<button type="button" onClick="search()">Search</button><br>
			<input type="checkbox" id="positive" name="positiveSearch" value="True" /> <span class='normal-text'> Search Only Positive Interactions </span>
			<input type="checkbox" id="bleedover" name="positiveSearch" value="True" /> <span class='normal-text'> Search for Bleed Overs </span>

			<br>
			 
			<div id="advancedhiderbuttondiv">
				<button onClick="showAdvanced()">Show Advanced Options</button>
			</div>
			
		</div>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/locateFavoriteGene.js"></script>
		<div id="message" class="medium-padding">
			<div id="errorMessage"></div>
			Please choose the user + project you would like to view, then you can enter a bait or prey as well as a comma delimited series of either.
			<br><br>
			In Y1H a bait is a fragment of regulatory DNA (eg. Promoter, enhancer).  To search Y1H baits you may enter the bait identifier (if you know it) or the gene to which this regulatory DNA is linked.
			<br><br>
			In Y2H a bait is a protein.  To search Y2H baits you may enter the bait identifier or the gene that encodes the protein.  
			<br><br>
			A prey in either Y1H or Y2H is a protein that has the potential to physically interact with a bait.  You may search  for preys using either the array position or the protein name.
			<hr>
			<ul><p><hl>FAVORITE GENE SEARCH
			<p>Please enter YOUR FAVORITE GENE to get a table explaining whether YOUR FAVORITE GENE is a BAIT or a PREY and which projects it belongs to.</p>
			<p><input type="text" id="favoriteGene" /></input><button type="button" onClick="locateFavoriteGene()">Locate!</button></p>
			<p><div id='favoriteGeneTable'></div></p>
			</h1></p></ul>
		</div>  
		<div id="slideshowHider" style="display: none;">
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
						<div id="counts" class="small-padding small-margin"></div>
					</div>
					<!-- Start Thumbnail Implementation -->
					<div id="thumbs" class="navigation">
						<ul class="thumbs noscript">
							<li>
								<a class='thumb' href='http://franklin-umh.cs.umn.edu/UMassProject/images/EA_A01_1-4_5mM_Xgal_7d_W.cropped.resized.grey.png' title='EA_A01_1-4'>
									<img src='http://franklin-umh.cs.umn.edu/UMassProject/thumbs/EA_A01_1-4_5mM_Xgal_7d_W.cropped.resized.grey.png._thumb.png' alt='EA_A01_1-4' />
								</a>
							</li>
						</ul>
					</div>
					<!-- End Thumbnail Implementation -->
					<!-- End Minimal Gallery Html Containers -->
					<div id="slideshow-controls" class="center small-padding">
						<!--<p>
							<INPUT type="checkbox" id="shadowBox" /> Enable Shadowing
							<INPUT type="checkbox" id="detailBox" /> Enable Details
						</p>-->
						<p><br>
							<INPUT type="button" id="positiveBox" value="Highlight Positive Interactions">
							<INPUT type="button" id="permanentBox" value="Hide Transcription Factor Boxes">
							<INPUT type="button" id="bleedBox" value="Enable Bleed Over Highlights">
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
						<table class='small-text standardTable' border='1' name='matrixtable' id='matrixtable' style='width: 1100px;'>
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
								<th>DBD Info</th>
							</tr>
							<tr name='info-table' id='info-table'>
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
						<button type="button" id="callSwitchButton" onClick="callPlateEntire()" >Change All Calls on the Plate</button>
						<br>
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
		</div>
	</body>                                                                
</html>
