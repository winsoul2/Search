<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transfer_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function addHistory($data = array())
	{
		if (sizeof($data)) {
			$this->db->insert("coop_loan_transfer_history", $data);
		}
	}
}
