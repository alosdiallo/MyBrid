<?php 
$base_url = base_url();

$arrdata = object_to_array($data);
$jsondata = json_encode($arrdata);
$jsontransMatrix = json_encode($transMatrix);

/*
***
* Converts a php object into a php array
**/
function object_to_array($data) 
{
  if(is_array($data) || is_object($data))
  {
    $result = array(); 
    foreach($data as $key => $value)
    { 
      $result[$key] = object_to_array($value); 
    }
    return $result;
  }
  return $data;
}
?>

<html>
<head>
<!-- CSS FILES -->

<!-- JAVASCRIPT FILES -->
<script type="text/javascript" src="<?=$base_url?>javascript/jquery-1.5.1.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/jquery.ui.core.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/jquery.ui.position.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/jquery.ui.autocomplete.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/dimensions.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/wz_jsgraphics.js"></script>
	<title> Browsing Interactions </title>   
	<style type="text/css">
		.items
		{
			width: 350px; 
			height: 500px; 
			overflow: auto; 
			padding: 1px;
			border: medium outset black;
		}
		.boxed #display
		{
		  
		  height: 600;
		  left:400px;
		  top:100px;
		  position: absolute;
		}
		.sel
		{
			margin-top: 0px;
			margin-bottom: 0px;
		}
		.iaele
		{
			padding-top: 4px;
			padding-bottom: 4px;
			border: 3px outset black;
		}
	</style>
	<script type='text/javascript'>
		var ACTIVATED = '#EE64FF';
		var MOUSE_OVER = '#64EEFF';
		var INACTIVATED = '#CCCCCC';
		$(function() {
			
			 $('.iaele').click(function(){
					this.style.backgroundColor = ACTIVATED;
					changePlateSrc(this.id);
					
					document.getElementById(curID).style.backgroundColor = INACTIVATED;
					curID = this.id;
				}
			);
			/*
		     *** Mouseover
		     * This function handles the mouseover for the image
		     **/
		    $('.iaele').mouseover(function(){
					this.style.backgroundColor = MOUSE_OVER;
				}
			);
		     /*
		     *** Mouseout
		     * This function handles the mouseout for the image
		     **/
		    $('.iaele').mouseout(function(){
					if(document.getElementById(curID) == this)
					{
						this.style.backgroundColor = ACTIVATED;
					} else {
						this.style.backgroundColor = INACTIVATED;
					}
				}
			);			
		});
		
		/*
		*** var data
		* Contains all of the matrix interaction data, the pictures
		* and the other miscellaneous data from the database.
		*** var transMatrix
		* Contains all of the genes on the plate
		**/
		var data = eval(<?=$jsondata?>);
		var transMatrix = eval(<?=$jsontransMatrix?>);
		var curID;
		
		function changePlateSrc(index)
		{
			var arrIndex = index.split(",");
			var currImage = "<?=$base_url?>images/" + data[parseInt(arrIndex[0])].pictures[parseInt(arrIndex[1])];
			document.getElementById('display').src = currImage;
			curPlate = arrIndex[0];
		}
		
		
		
		
		
		
		
		/*
		*** var dims
		* contains the dimensions of the plate to be highlighted
		**/
		//var jg = new jsGraphics('highlight');
	
	
	
					
		var dims = new dimensions(	new Array(55,55,95,106),
									new Array(15.5,16),
									new Array(14.75,14.92));
	
		var jg;
		var jp;
		window.onload=function(){
			/*
			 * Any Regular onload functions should go here
			 */
			 
			var element = document.getElementById('permlight').children[0];
			curID = element.id;
			element.style.backgroundColor = ACTIVATED;
			//THERE IS NONE!
			
			
			/*
			 * Initialize Drawing Canvases here
			 */
			jp = new jsGraphics('permlight');
			
			/*
			 * Any drawings which have standardized settings should have those settings go here
			 */
				/* jp */
			jp.setColor('blue');
			jp.setStroke(2);
			
			/*
			 * setsrc is called to get the dimensions of the image that is going to be drawn on
			 */
			this.dims.setsrc(document.getElementById('display'));
			
			/*
			 * All permanent drawings should go onto jp, 
			 */
			if(<?=($position['x']-1)*2+2?>)
			{
			jp.drawRect(Math.round(this.dims.BORDER_LEFT + this.dims.pX + this.dims.ELESPACING_W * <?=($position['x']-1)*2?>), 
						Math.round(this.dims.BORDER_TOP + this.dims.pY + this.dims.ELESPACING_H * <?=($position['y']-1)*2?>),
						2 * this.dims.RECT_PAD_W - 2,
						2 * this.dims.RECT_PAD_H - 2);
			}
			
			/*
			 * paint permanent highlights
			 */	
			jp.paint();
		}
	</script>
</head>

<body>
	<p>
	<div class="items" id="permlight" style="background-color:#666666">
		<?php
			$i = 0;
			foreach($data as $id){
				if(isset($position['plate_num']))
				{
					$pos = $position["plate_num"];
					echo "
	<div class='iaele' id='$i,$pos' style='background-color:#CCCCCC'>
		<span class='image'>
			<p class='sel'> Tsf: " . $id->prey_gene . ", Promotor: " . $id->bait_gene . "</p>
		</span>
	</div>\n";
				} else {
					for ($j = 0; $j <= 2; $j++) 
					{
						echo "
	<div class='iaele' id='$i,$j' style='background-color:#CCCCCC'>
		<span class='image'>
			<p class='sel'> Plate Number: " . $j . ", Promotor: " . $id->bait_gene . "</p>
		</span>
	</div>\n";
					}
				}
				$i++;
			}
		?>
   </div>
   <br/>
   <div class="boxed">
	   <img id='display' src='http://csbio.cs.umn.edu/UMassProject/dev/ci/images/<?=$data[0]->pictures[(isset($position["plate_num"])?$position["plate_num"]:'0')]?>'></img>
   </div>
   </p>
   
   
   
</body>
</html>
