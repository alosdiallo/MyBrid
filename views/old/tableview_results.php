<?php 
$this->load->helper("url"); 
$base_url = base_url();
?>
<html>
                                                                   
<head>                                                                 
<!-- CSS FILES -->
<link href="<?=$base_url?>css/page.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="<?=$base_url?>css/jquery.ad-gallery.css">
<link rel="stylesheet" type="text/css" href="<?=$base_url?>css/slideshow.css">

<!-- JAVASCRIPT FILES -->
<script type="text/javascript" src="<?=$base_url?>javascript/wz_jsgraphics.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/jquery.ad-gallery.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/dimensions.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/jquery.galleriffic.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/jquery.opacityrollover.js"></script>
                                                  
</head>                                                      
<body>                                                                 
	<div id='tableview'>
		<?php
			
			$str = "<table>";
			$str .= "<table border='1'>";
			$str .= "<th> Bait </th><th> TF_ARRAY_NAME </th><th> TF_ORF_NAME </th><th> TF_GENE_NAME </th><th> RAW_INTENSITY </th><th> RC_INTENSITY_VALUE </th><th> PTP_INTENSITY_VALUE </th>
			<th> __Z_SCORE__  </th><th> PLATE_MEDIAN </th><th> CALL_TYPE </th><th> BLEED_OVER </th>";
			
			foreach($data as $line){
	
				$str .= "<tr>";
				$str .= "<td>" . $line->bait ."</td>";
				$str .= "<td>" . $line->tf_array_name . "</td>";
				$str .= "<td>" . $line->tf_orf_name . "</td>";
				$str .= "<td>" . $line->tf_gene_name . "</td>";
				$str .= "<td>" . $line->raw_intensity . "</td>";
				$str .= "<td>" . $line->rc_intensity_value . "</td>";
				$str .= "<td>" . $line->ptp_intensity_value . "</td>";
				$str .= "<td>" . $line->z_score . "</td>";
				$str .= "<td>" . $line->plate_median . "</td>";
				$str .= "<td>" . $line->call_type . "</td>";
				
				if($line->bleed_over == ''){
					$str .= "<td> - </td>";
				}
				else{
					$str .= "<td>" . $line->bleed_over . "</td>";
				}
				$str .= "</tr>";
			}
		
			$str .= "</table>";
			
			
			echo $str;
		?>
	</div>
                               
 </body>                                                                
 </html>
