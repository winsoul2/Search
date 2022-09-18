<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PHPExcel/Classes/PHPExcel/Writer/Excel2007.php" ;
class Myexcel2007 extends PHPExcel_Writer_Excel2007 {
	function __construct()
	{
		parent::__construct();
		$CI =& get_instance();
	}
}
?>