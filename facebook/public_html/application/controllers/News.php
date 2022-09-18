<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends CI_Controller {
	function __construct()
	{
		parent::__construct();

	}
	public function index()
	{
		//echo "<pre>";print_r($_SESSION);echo"</pre>";
		$arr_data = array();
		$this->libraries->template('news',$arr_data);
	}
}
