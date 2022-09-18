<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PHPExcel/Classes/PHPExcel.php" ; 
class Myexcel extends PHPExcel {
	function __construct()
	{
		parent::__construct();
		$CI =& get_instance();
	}
}
?>