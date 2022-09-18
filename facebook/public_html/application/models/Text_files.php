<?php
/**
 * Created by PhpStorm.
 * User: macmini2
 * Date: 2019-11-06
 * Time: 21:24
 */

class Text_files extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        define('TXT_SHARE_DEDUCT_CODE', "403203");
        define('TXT_LOAN_DEDUCT_CODE', "425600");
        define('TXT_SAV_DEDUCT_CODE', "403204");
    }

    public function share_layout_file($data)
    {
        if(is_array($data)){
            $data = (object) $data;
        }
        $empNo = $this->format($data->employee_id, 7);
        $start = $this->format($data->date, 10);
        $amt = $this->format($data->pay_amount,10, STR_PAD_RIGHT);
        $deduct = $this->format($data->deduct_code, 6);
        return sprintf("%s%s%s%s\r\n", $empNo, $start, $amt, $deduct);
    }

    public function loan_layout_file($data)
    {
        if(is_array($data)){
            $data = (object) $data;
        }
        $empNo = $this->format($data->employee_id, 7);
        $begin = $this->format($data->begin_date, 10);
        $end = $this->format($data->end_date, 10);
        $deduct = $this->format($data->deduct_code);
        $amt = $this->format($data->amt, 10, STR_PAD_RIGHT);
        $principle = $this->format($data->principle, 10, STR_PAD_RIGHT);
        return sprintf("%s%s%s%s%s%s\r\n", $empNo, $begin, $end,  $deduct, $amt, $principle);
    }

    public function saving_layout_file($data){
        if(is_array($data)){
            $data = (object) $data;
        }
        $empNo = $this->format($data->employee_id, 7);
        $start = $this->format($data->date, 10);
        $amt = $this->format($data->pay_amount,10, STR_PAD_RIGHT);
        $deduct = $this->format($data->deduct_code, 6);
        return sprintf("%s%s%s%s\r\n", $empNo, $start, $amt, $deduct);
    }

    public function format($txt, $length = 0, $str_pad = 0){
        switch ($str_pad){
            case STR_PAD_LEFT:
                return sprintf('%s%s', $txt, self::add_space($length, mb_strlen($txt, 'utf-8')));
            case STR_PAD_RIGHT:
                return sprintf('%s%s', self::add_space($length, mb_strlen($txt, 'utf-8')), $txt);
            default:
                return sprintf('%s%s', $txt, self::add_space($length, mb_strlen($txt, 'utf-8')));
        }

    }

    /**
     * @param int $number
     * @param int $start
     * @return string
     */
    private function add_space($number = 0, $start = 0, $character = " ")
    {
        $txt = "";
        for ($i = $start; $i < $number; $i++) {
            $txt .= $character;
        }
        return $txt;
    }

    public function share_text_file($data)
    {
        $item = array();
        $item['employee_id'] = $data['employee_id'];
        $item['date'] = self::convert_date($data['start_date']);
        $item['pay_amount'] = number_format($data['pay_amount'],2, '.', '');
        $item['deduct_code'] = TXT_SHARE_DEDUCT_CODE;
        return $this->share_layout_file($item);
    }

    public function loan_text_file($data)
    {


        $item = array();
        $item['employee_id'] = $data['employee_id'];
        $item['begin_date'] = self::convert_date($data['start_date']);
        $item['end_date'] = self::convert_date($data['end_date']);
        $item['deduct_code'] = TXT_SHARE_DEDUCT_CODE;
        $item['amt'] = number_format($data['pay_amount'],2,'.', '');
        $item['principle'] = number_format($data['loan_amount'],2, '.', '');
        return $this->loan_layout_file($item);
    }

    public function deposit_text_file($data)
    {
        $item = array();
        $item['employee_id'] = $data['employee_id'];
        $item['date'] = self::convert_date($data['date']);
        $item['pay_amount'] = number_format($data['pay_amount'],2, '.', '');
        $item['deduct_code'] = TXT_SAV_DEDUCT_CODE;
        return $this->saving_layout_file($item);
    }

    public function get_deposit_month($data){
        $fields = array(
            't3.employee_id',
            'LAST_DAY(CURRENT_DATE ()) AS `date`',
            'SUM(`t2`.`pay_amount`) as pay_amount'
        );

        $where = array(
            't1.profile_month' => $data['month'],
            't1.profile_year' => $data['year'],
            't2.deduct_code' => "DEPOSIT",
            't3.mem_type_id' => $data['mem_type_id']
        );

        $result = $this->db->select($fields)
            ->from("coop_finance_month_profile as t1")
            ->join("coop_finance_month_detail as t2", "t1.profile_id=t2.profile_id", "inner")
            ->join("coop_mem_apply as t3", "t2.member_id=t3.member_id", "inner")
            ->where($where)
            ->group_by('t2.member_id')
            ->order_by("CAST(t3.faction AS UNSIGNED) ASC, t3.employee_id ASC")
            ->get()->result_array();

        if($_GET['display'] === "show") {
            echo "<pre>";
            echo $this->db->last_query();
            echo "</pre>";
            echo "<br><br>";
        }

        return $result;
    }

    public function get_share_month($data){

        $fields = array(
            't3.employee_id',
            'LAST_DAY(CURRENT_DATE ()) AS `start_date`',
            'SUM(`t2`.`pay_amount`) as pay_amount'
        );

        $where = array(
            't1.profile_month' => $data['month'],
            't1.profile_year' => $data['year'],
            't2.deduct_code' => "SHARE",
            't3.mem_type_id' => $data['mem_type_id']
        );

        $result = $this->db->select($fields)
            ->from("coop_finance_month_profile as t1")
            ->join("coop_finance_month_detail as t2", "t1.profile_id=t2.profile_id", "inner")
            ->join("coop_mem_apply as t3", "t2.member_id=t3.member_id", "inner")
            ->where($where)
            ->group_by('t2.member_id')
            ->order_by("CAST(t3.faction AS UNSIGNED) ASC, t3.employee_id ASC")
            ->get()->result_array();

        if($_GET['display'] === "show") {
            echo "<pre>";
            echo $this->db->last_query();
            echo "</pre>";
            echo "<br><br>";
        }

        return $result;
    }

    public function get_loan_contract($data){

        $fields = array(
            't4.employee_id',
            'LAST_DAY(CURRENT_DATE ()) AS `start_date`',
            'ADDDATE(ADDDATE(`t3`.`date_start_period`,INTERVAL `t3`.`period_amount`-1 MONTH),INTERVAL 1 DAY) AS end_date',
			'SUM(`t2`.`pay_amount`) as pay_amount',
            '`t3`.`loan_amount` as loan_amount',

        );

        $where = array(
            't1.profile_month' => $data['month'],
            't1.profile_year' => $data['year'],
            't2.deduct_code' => 'LOAN',
            't4.mem_type_id' => $data['mem_type_id']
        );

        $result = $this->db->select($fields)
            ->from("coop_finance_month_profile as t1")
            ->join("coop_finance_month_detail as t2", "t1.profile_id=t2.profile_id", "inner")
            ->join("coop_loan as t3", "t2.loan_id=t3.id", "inner")
            ->join("coop_mem_apply as t4", "t4.member_id=t3.member_id", "inner")
            ->where($where)
            ->group_by('t2.loan_id')
            ->order_by("CAST(t4.faction AS UNSIGNED) ASC, t4.employee_id ASC")
            ->get()->result_array();

        if($_GET['display'] === "show") {
            echo "<pre>";
            echo $this->db->last_query();
            echo "</pre>";
            echo "<br><br>";
        }

        return $result;
    }

    public function get_rows($data){
        if($data['type'] == "LOAN"){
            $var = 'DISTINCT t2.loan_id';
        }else{
            $var = 'DISTINCT t2.member_id';
        }

        $where = array(
            't1.profile_month' => $data['month'],
            't1.profile_year' => $data['year'],
            't2.deduct_code' => $data['type'],
            't3.mem_type_id' => $data['mem_type_id']
        );
        return $this->db->select("COUNT({$var}) as amt")
            ->from("coop_finance_month_profile as t1")
            ->join("coop_finance_month_detail as t2", "t1.profile_id=t2.profile_id", "inner")
            ->join("coop_mem_apply as t3", "t2.member_id=t3.member_id", "inner")

            ->where($where)
            ->get()->row_array()['amt'];
    }

    public function convert_date($date){
        $date = date("d/m/Y", strtotime($date));
        $ex_date = explode("/",$date);
        $ex_date[2] += 543;
        return join("/",$ex_date);
    }

    public function create_file($file_name = "TEST", $text = ""){
        ini_set("precision", 12);
        $path_file = './assets/document/' . $file_name . ".DAT";
        $file = fopen($path_file, "w") or die("Unable to open file");
        fwrite($file, $text);
        fclose($file);

        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="' . $file_name . '.txt"');
        readfile($path_file);
    }

}
