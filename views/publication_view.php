<?php 
	$base_url = base_url();
?>
<html>                                                               
	<head>    
		<title>MyBrid</title>
		<!-- CSS FILES -->
		<!--link href="<?php echo $base_url?>css/jquery.ui.all.css" rel="stylesheet" type="text/css"-->
		<link href="<?php echo $base_url?>css/universal.css" rel="stylesheet" type="text/css">
		
		<!-- JAVASCRIPT FILES -->
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery-1.6.2.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.core.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.widget.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.position.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.autocomplete.js"></script>
		
		<style type="text/css">
			#head {
			    height: 40px;
			}
		</style>
		<script>
			//////
			// Define base url for project control
			//////
			var base_url = "<?php echo $base_url?>"; 
		</script>
	</head>
	<body>
		<div id="head">
			
			<div class="user_session_controls">
				<?php
					if($this->session->userdata('is_logged_in') == false){
						echo '
						<FORM METHOD="LINK" ACTION="'.$base_url.'index.php/login/" class="alignright">
						<INPUT TYPE="submit" VALUE="Login">
						</FORM>
						';
					} else {
						echo '
						<FORM METHOD="LINK" ACTION="'.$base_url.'index.php/login/logout/" class="alignright">
						<INPUT TYPE="submit" VALUE="Logout">
						</FORM>
						';
					}
				?>
				<FORM METHOD="LINK" ACTION="<?php echo $base_url?>" class='alignright'>
					<INPUT TYPE="submit" VALUE="Back to Homepage">
				</FORM>
				<span class='medium-padding huge-text white-text'><b>Walhout Lab Publications</b></span>
			</div>
		</div>
		<div class="background large-padding">
			<?php
				foreach($project as $proj){
					if($proj->title){
						//echo "User ID: ". $proj->user_id." Project ID: ".$proj->project_id."<br>";
						if($proj->title   ){echo "<b>".$proj->title."</b><br>";   }
						if($proj->authors ){echo $proj->authors."<br>"; }
						if($proj->abstract){echo "<br>".$proj->abstract."<br>";}
						if($proj->paper   ){echo "<a href='http://franklin-umh.cs.umn.edu/UMassProject/publication/".$proj->paper."'>Link to Paper</a>   ";}
						if($proj->data    ){echo "<a href='http://franklin-umh.cs.umn.edu/UMassProject/publication/".$proj->data."'>Link to Data</a>";}
						echo "<hr>";
					}
				}
			?>
		</div>	 
	</body>
</html>
