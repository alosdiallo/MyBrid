<?php 
$base_url = base_url();


?>

<html>
                                                                   
<head>                                                                  
<!-- CSS FILES -->
<link href="<?=$base_url?>css/style.css" rel="stylesheet" type="text/css">
<link href="<?=$base_url?>css/jquery.ui.all.css" rel="stylesheet" type="text/css">
<!-- JAVASCRIPT FILES -->
<script type="text/javascript" src="<?=$base_url?>javascript/jquery-1.5.1.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/jquery.ui.core.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/jquery.ui.position.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/jquery.ui.autocomplete.js"></script>
<script>

	$(function() {
		/*
		*** transTags
		* Contains the transcriptor tags gotten from controller
		**/
		var transTags = <?=$transcriptorTags?>
		
		/*
		*** Autocomplete: Transcriptor
		* Uses the transcriptor tags above to create an autocomplete query
		**/
		$( "#transcriptor" ).autocomplete({
			source: function(req, responseFn) {
		        var re = $.ui.autocomplete.escapeRegex(req.term);
		        var matcher = new RegExp( "^" + re, "i" );
		        var a = $.grep( transTags, function(item,index){
		            return matcher.test(item);
		        });
		        /*
		        ***
		        * responseFn is passed an array with the values that should be shown to the user
		        * if no match is found, display nothing otherwise a contains a list of transcriptors
		        * to display as an autocomplete
		        **/
		        if(a.length == 0)
		        {
					responseFn( ["No match found."] );
				} else {
					responseFn( a );
				}
		    }

		});
		
		/*
		*** promTags
		* Contains the transcriptor tags gotten from controller
		**/
		var promTags = <?=$promotorTags?>
		
		/*
		*** Autocomplete: promotors
		* Uses the promotor tags above to create an autocomplete query
		**/
		$( "#promoter" ).autocomplete({
			source: function(req, responseFn) {
		        var re = $.ui.autocomplete.escapeRegex(req.term);
		        var matcher = new RegExp( "^" + re, "i" );
		        var a = $.grep( promTags, function(item,index){
		            return matcher.test(item);
		        });
		        /*
		        ***
		        * responseFn is passed an array with the values that should be shown to the user
		        * if no match is found, display nothing otherwise a contains a list of promotors
		        * to display as an autocomplete
		        **/
		        if(a.length == 0)
		        {
					responseFn( ["No match found."] );
				} else {
					responseFn( a );
				}
		    }

		});
	});
</script>

                                                              
</head>                                                                  
<body>                                                                 
   <div class="head">
	  	 <div id="head"> 
		   	<form action="http://csbio.cs.umn.edu/UMassProject/dev/ci/index.php/browse_interactions/" method="post">
		   	  Transcription Factor: <input type="text" id="transcriptor" name="transcriptor" size='50' 
		   	  onblur="if(this.value == ''){this.value='enter your keywords here';}" value="enter your keywords here" 
		   	  onfocus="if(this.value == this.defaultValue){this.value = '';}" />
		   	  Promoter : <input type="text" id="promoter" name="promoter" size="50" 
		   	  onblur="if(this.value == ''){this.value='enter your keywords here';}" value="enter your keywords here" 
		   	  onfocus="if(this.value == this.defaultValue){this.value = '';}"/>
			  <input type="submit" value="Search" />
			  <!--
			  <p> To browse interactions, you have to choose either a promoter_(i.e EA_A02) or a transcriptor factor(dmd-4) and search.</p>
			  <p> By entering any data on the input, the autocomplete will help you to find what you need.</p>
			  -->
			</form>
		</div>
	</div>	                      
 </body>                                                                 
 </html>
