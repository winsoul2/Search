<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require('FPDI-1.6.2/fpdi.php');
class Myfpdi extends FPDI {
	function __construct()
	{
		parent::__construct();
		$CI =& get_instance();
	}
}
?>