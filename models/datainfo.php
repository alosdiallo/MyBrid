<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class datainfo{
	public $image;
	public $thumbnail;
	public $info;
	public $matrix;
	public $position;
	
	public function datainfo($image = null, $thumb = null, $info = null, $matrix = null, $position = null){
			$this->image = $image;
			$this->thumbnail = $thumb;
			$this->info = $info;
			$this->matrix = $matrix;
			$this->position = $position;
	}
}

?>
