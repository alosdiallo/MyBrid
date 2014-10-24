<?php

define("FILEPATH", "/project/csbio/web/UMassProject/dataFiles/");
define("FILEEND", ".cropped.resized.grey.png.red.median.colony.txt");
define("ROW", 32);
define("COLUMN", 48); 

class Interaction{
	
	private $file;
	private $matrix;
	
	/*
	 * constructor for the class Promoter
	 * takes two arguments: $time, $promoter
	 * e.g time: 1-4, 5-9, 9-12
	 * e.g promoter: EA_A02
	 */
	public function Interaction($image){
		$this->setFile($image);
		$this->matrix = array();
	}
	/*
	 * sets a promoter 
	 * e.g EA_A02
	 * 
	 * 	$this->file = FILEPATH . $this->image . FILEEND;
	 */
	public function setFile($image){
		$arr = preg_split("/.JPG/", $image);
		$this->file = FILEPATH . $arr[0] . FILEEND;
	}
	/*
	 * Y1H_307_N_9-12_5mM_Xgal_7d_W.cropped.resized.grey.png.red.median.colony.txt
	 * EA_C09_N_1-4_5mM_Xgal_7d_W.JPG
	 * EA_C09_N_1-4_5mM_Xgal_7d_W.cropped.resized.grey.png.red.median.colony.txt
	 */
	
	/*
	 * returns the file
	 */
	public function getFile(){
		return $this->file;
	}
	
	/*
	 * returns the image
	 */
	public function getImage(){
		return $this->image;
	}
	
	public function getMatrix(){
		
		$lines = file($this->file);
		
		$row = count($lines);
		
		if($row == ROW){
			
			for($i = 0; $i < $row; $i++){
				$arr = split("\t", $lines[$i]);
				
				$column = count($arr);
				
				if($column == COLUMN){
					array_push($this->matrix, $arr);
					
				}
				
			}
		}
		
		return $this->matrix;
		

	}
}
?>
