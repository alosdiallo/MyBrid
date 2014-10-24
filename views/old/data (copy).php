<?php 
$base_url = base_url();
?>
                                                                 
<head>    
	<title>Spot-On</title>
	<!-- CSS FILES -->
	<link href="<?=$base_url?>css/page.css" rel="stylesheet" type="text/css">
	<link href="<?=$base_url?>css/style.css" rel="stylesheet" type="text/css">
	<link href="<?=$base_url?>css/jquery.ui.all.css" rel="stylesheet" type="text/css">
	
	<!-- JAVASCRIPT FILES -->
	<script type="text/javascript" src="<?=$base_url?>javascript/jquery-1.6.2.js"></script>
	<script type="text/javascript" src="<?=$base_url?>javascript/jquery.ui.core.js"></script>
	<script type="text/javascript" src="<?=$base_url?>javascript/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="<?=$base_url?>javascript/jquery.ui.position.js"></script>
	<script type="text/javascript" src="<?=$base_url?>javascript/jquery.ui.autocomplete.js"></script>
	
	<style type="text/css">
	.alignleft {
		float: left;
	}
	.alignright {
		float: right;
	}
	</style>
	<script>
	function monkeyPatchAutocomplete() {

          // Don't really need to save the old fn, 
          // but I could chain if I wanted to
          var oldFn = $.ui.autocomplete.prototype._renderItem;

          $.ui.autocomplete.prototype._renderItem = function( ul, item) {
              var re = new RegExp("^" + this.term, "i") ;
              var t = item.label.replace(re,"<span style='font-weight:bold;color:Blue;'>" + this.term + "</span>");
              return $( "<li></li>" )
                  .data( "item.autocomplete", item )
                  .append( "<a>" + t + "</a>" )
                  .appendTo( ul );
          };
      }


	$(function() {
		monkeyPatchAutocomplete();
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
		<div id="head" class="ui-widget">
			<div class="user_session_controls">
				<p class='alignright'>
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
					<FORM METHOD="LINK" ACTION="<?=$base_url?>index.php/register/" class='alignright'>
						<INPUT TYPE="submit" VALUE="Register">
					</FORM>
					<FORM METHOD="LINK" ACTION="<?=$base_url?>" class='alignright'>
						<INPUT TYPE="submit" VALUE="Back to Homepage">
					</FORM>
				</p>
			</div>
			<form action="<?=$base_url?>index.php/data/" method="post">
			   	  Transcription Factor: <input type="text" id="transcriptor" name="transcriptor" size='30' autocomplete=off 
			   	  onblur="if(this.value == ''){this.value='enter your keywords here';}" value="enter your keywords here" 
			   	  onfocus="if(this.value == this.defaultValue){this.value = '';}" />
			   	  Promoter : <input type="text" id="promoter" name="promoter" size="30" autocomplete=off
			   	  onblur="if(this.value == ''){this.value='enter your keywords here';}" value="enter your keywords here" 
			   	  onfocus="if(this.value == this.defaultValue){this.value = '';}"/>
				  <input type="submit" value="Search" />
				  z-score &gt; <input type="text" id="zscore" name="zscore" size="3" autocomplete=off value="0" />
				  <input type="checkbox" id="positive" name="positiveSearch" value="True" /> Force positive search
			</form>
		</div>
	</div>	 
	<?=$notLoggedIn ?"You must be logged in to use this feature."               . "\n":""?>      
	<?=$queryFail   ?"The query that you have entered has returned no results." . "\n":""?>
</body>
</html>
