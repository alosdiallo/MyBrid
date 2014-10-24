<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Datatype{
	public $image;
	public $thumbnail;
	public $info;
	public $matrix;
	
	public function Datatype($image, $thumb, $info, $matrix){
			$this->image = $image;
			$this->thumbnail = $thumb;
			$this->info = $info;
			$this->matrix = $matrix;
	}
}

?>
