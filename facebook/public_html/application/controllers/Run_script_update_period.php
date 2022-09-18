<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Run_script_update_period extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$sql = "SELECT a.ID,a.CT,a.CN,a.SUMOFPERIOD,b.*
FROM (
SELECT t1.id, t2.loan_group_id ,t1.contract_number, t1.member_id, period_now
FROM srusct.coop_loan as t1 
INNER JOIN srusct.coop_loan_name as t2 ON t1.loan_type = t2.loan_name_id
WHERE t2.loan_group_id = '$_GET[type]' AND t1.loan_status=1) as  b
LEFT JOIN sotr.contract as a ON a.CN = b.contract_number AND a.ID = b.member_id
WHERE a.CT = '0000$_GET[type]' AND b.id not in ('125183',
'128728',
'132080',
'142703',
'147941',
'148329',
'148331',
'148910',
'148948',
'149357',
'149385',
'149407',
'149422',
'149450',
'150591',
'152134',
'152394',
'152729',
'153085',
'153129',
'153185',
'153249',
'153656',
'153674',
'153743',
'153864',
'153896',
'154000',
'154051',
'154166',
'154186',
'154209',
'154225',
'154312',
'154623',
'154725',
'154825',
'154845',
'155619',
'155620',
'155621',
'155623',
'155624',
'155625',
'155626',
'155627',
'155628',
'155630',
'155631',
'155632',
'155633',
'155634',
'155635',
'155637',
'155638',
'155639',
'155640',
'155641',
'155642',
'155643',
'155644',
'155645',
'155646',
'155647',
'155648',
'155649',
'155650',
'155651',
'155652',
'155654',
'155655',
'155656',
'155657',
'155658',
'155659',
'155660',
'155661',
'155662',
'155663',
'155664',
'155665',
'155666',
'155667',
'155668',
'155669',
'155670',
'155671',
'155673',
'155678',
'155683')
";
		$rs = $this->db->query($sql);
		$row_loanentry = $rs->result_array();
//echo $this->db->last_query();exit;

		//echo '<pre>';print_r($row_loanentry);exit;

		foreach ($row_loanentry as $key => $value) {
			$SUMOFPERIOD = number_format($value['SUMOFPERIOD'],-2);
			$period =$value['SUMOFPERIOD']-$value['period_now'];
			if($period!=0) {
				$data_insert = array();
				$data_insert['period_now'] = $SUMOFPERIOD;
//			$this->db->where("id = '".$value['id']."' AND contract_number = '".$value['contract_number']."' AND member_id = '".$value['member_id']."'");
//			$this->db->update('coop_loan', $data_insert);
				echo "UPDATE coop_loan SET period_now = $SUMOFPERIOD WHERE member_id = '$value[member_id]' AND contract_number ='$value[contract_number]';";
				echo "<br>";
			}
//			else{
//				echo "ok <br>";
//			}

			}
		exit;
	}

}
