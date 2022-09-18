<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PHP_XLSXWriter/xlsxwriter.class.php" ; 
class Myxmlxwriter extends XLSXWriter {
	function __construct()
	{
		parent::__construct();
		$CI =& get_instance();
	}
}
?>