<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Finance_process_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->finance_process_setting();
    }

    protected $mem_type_id = array();
    protected $data = null;
    protected $member_ignore = array();
    private $days = null;

    private function set_mem_type_id($id){
        $this->mem_type_id[] = $id;
    }

    public function get_mem_type_id(){
        return $this->mem_type_id;
    }

    public function get_data($mem_type_id){
        return $this->data[$mem_type_id];
    }

    public function get_member_by_id($member_id = ""){
        return $this->db->where('member_id', str_pad($member_id, 6, "0", STR_PAD_LEFT))->get('coop_mem_apply')->row();
    }

    public function get_day($mem_type_id, $date_of_process, $member_id = "", $ignore = false){
        return date("Y-m-d", strtotime($date_of_process));
    }

    public function get_day_of_process($mem_type_id, $date_of_process){
        return date("Y-m-t", strtotime($date_of_process));
    }

    public function get_day_of_work($date, $active_week_end = '0'){
        return self::find_day_of_month($date, $active_week_end);
    }

    private function find_day_of_month( $date, $week_end = '0', $flow = 'up', $max_date = ""){

        if($week_end == '1'){
            $chk = date('w', strtotime($date));
            $last_days = date('Y-m-d', strtotime($date));
            if($max_date == "") $max_date = $last_days;
            $flag = $flow == 'down' ? " -1 day" : " +1 day";
            $flow = $date > $max_date ? 'down' : $flow;
            if($_GET['debug'] == 'on') {
                echo $date." ::: ".$chk." ::: ".$max_date." ::: ".(($date > $max_date) ? 'true' : 'false')." ::: ".($this->check_holiday($date) ? 'true' : 'false')."<br><br>";
            }
            if($chk == '0' || $chk == '6' || $date > $max_date || $this->check_holiday($date)) {
                $date = date('Y-m-d', strtotime($date. $flag));
                return $this->find_day_of_month($date, $week_end, $flow, $max_date);
            }else{
                return $date;
            }
        }else{
            return $date;
        }
    }

    public function finance_process_setting(){
       $res = $this->finance_process_setting_val();
       if(sizeof($res)){
           $result = array();
           foreach ($res as $item => $val){
               $tmp = $val['mem_type_id'];
               if(!empty($val['member_id'])){
                   self::set_member_ignore($val['member_id']);
                   continue;
               }
                   self::set_mem_type_id($tmp);
               $result[$tmp] = (object) $val;
           }
           ksort($result);
           $this->data = $result;
           return $result;
       }else{
           echo "Places check table 'coop_finance_process_profile_setting' or 'coop_finance_process_detail_setting' and try again later.";
           exit;
       }
    }

    private function set_member_ignore($list_member = ""){
        if($list_member == ""){
            return;
        }
        $member_list = explode(',', $list_member);
        foreach ($member_list as $value) {
            if(!in_array($value, $this->member_ignore)) {
                array_push($this->member_ignore, $value);
            }
        }
    }

    private function finance_process_setting_val(){
        $this->db->select(array("t1.process_group_id",
            "t1.process_group_name",
            "t1.day_before_end_month",
            "t1.active_weekend",
            "t1.process_status",
            "t2.process_type",
            "t2.mem_type_id",
            "t2.member_id"
        ));
        $this->db->from("coop_finance_process_profile_setting t1");
        $this->db->join("coop_finance_process_detail_setting t2", "t1.process_group_id=t2.process_group_id","inner");
        $this->db->where(array("active_status" => "1"));
        return $this->db->get()->result_array();
    }

    public function find_last_cal_interest_atm($loan_atm_id,  $date = "", $counter = 0, $round = 0){

        if($date == ""){
            $date = date('Y-m-d');
        }else{
            $date = date("Y-m-01", strtotime($date));
        }

        $res = date('ym', strtotime($date.'-1 month  +543 year'));
            $trans = $this->db->select("transaction_datetime")->from("coop_loan_atm_transaction")->where(array('loan_atm_id' => $loan_atm_id, 'receipt_id like' => $res . '%'))->order_by('loan_atm_transaction_id', "desc")->limit(1)->get()->row();

        $result = "";
        if(!empty($trans->transaction_datetime)){
            $result = explode(" ", $trans->transaction_datetime)[0];
            if(isset($_GET['debug'])){
                echo "statemt 1 = ".$result." <br>";
            }
        }

        if(empty($result)){
            $date = date('Y-m-d', strtotime($date.' -1 month'));
            $result = $this->get_day_of_process_by_mem_type_id($loan_atm_id, $date);
            if(isset($_GET['debug'])){
                echo "statemt 2 = ".$result." <br>";
            }
        }

        if(empty($result)){
            $this->db->select('date_last_interest')->from('coop_loan_atm')->where('loan_atm_id', $loan_atm_id);
            $result = $this->db->get()->row()->date_last_interest;
            if(isset($_GET['debug'])){
                echo "statemt 3 = ".$result." <br>";
            }
        }

        return $result;
    }

    public function find_last_cal_interest($loan_id,  $date = "", $counter = 0, $round = 0){

        if($date == ""){
            $date = date('Y-m-d');
        }else{
            $date = date("Y-m-01", strtotime($date));
        }

        $res = date('ym', strtotime($date.'-1 month  +543 year'));
        $trans = $this->db->select("transaction_datetime")->from("coop_loan_transaction")->where(array('loan_id' => $loan_id, 'receipt_id like' => '%'.$res . '%'))->order_by('loan_transaction_id', "desc")->limit(1)->get()->row();

        $result = "";
        if(!empty($trans->transaction_datetime)){
            $result = explode(" ", $trans->transaction_datetime)[0];
            if(isset($_GET['debug'])){
                echo "statemt 1 = ".$result." <br>";
            }
        }

        if(empty($result)){
            $date = date('Y-m-d', strtotime($date.' -1 month'));
            $result = $this->get_day_of_process_by_mem_type_id($loan_id, $date, "LOAN");
            if(isset($_GET['debug'])){
                echo "statemt 2 = ".$result." <br>";
            }
        }

        if(empty($result)){
            $this->db->select('date_last_interest')->from('coop_loan')->where('id', $loan_id);
            $result = $this->db->get()->row()->date_last_interest;
            if(isset($_GET['debug'])){
                echo "statemt 3 = ".$result." <br>";
            }
        }

        return $result;
    }


    /**
     * @param string $loan_atm_id
     * @return array|mixed
     */
    private function find_member_by_loan_atm_id($loan_atm_id = ""){
        $data = array();
        if($loan_atm_id == ""){
            return $data;
        }
        $data = $this->db->select("t2.*")->from("coop_loan_atm t1")->join("coop_mem_apply t2", "t2.member_id=t1.member_id")
        ->where("t1.loan_atm_id", $loan_atm_id)->get()->row();
        return $data;
    }

    private function find_member_by_loan_id($loan_id = ""){
        $data = array();
        if($loan_id == ""){
            return $data;
        }
        $data = $this->db->select("t2.*")->from("coop_loan t1")->join("coop_mem_apply t2", "t2.member_id=t1.member_id")
            ->where("t1.id", $loan_id)->get()->row();
        return $data;
    }

    private function get_day_of_process_by_mem_type_id($id, $date, $type = "ATM"){
        if($type == 'ATM') {
            $member = $this->find_member_by_loan_atm_id($id);
        }else{
            $member = $this->find_member_by_loan_id($id);
        }

        $res = $this->get_day($member->mem_type_id, $date, $member);

        return $res == 'N/A' ? "" : $res;
    }

    public function calculate_atm_tistrsaving($principal_balance, $interest, $rate){
        $result['total'] = $total = ROUND($principal_balance * ($rate / 100), 0);
        $result['principal'] = $total;
        return (object) $result;
    }

    private function check_holiday($date = ''){

        if($date == ''){
            $date = date('Y-m-d');
            $year = date('Y', strtotime($date));
        }else{
            $year = date("Y", strtotime($date));
        }

        if($_GET['debug'] == "on"){
            echo "date check holiday: ".$date."<br>";
        }

        $num = $this->db->select("*")
        ->from("coop_calendar_holiday")
        ->where(array('work_year' => $year, 'holiday_date' => $date))
        ->get()->result_array();


        if($_GET['debug'] == "on"){
            echo "<br>";
            echo $this->db->last_query();
            echo "<br>";
        }

        return sizeof($num) ? true : false;
    }

    private function data_clear_atm_detail($member_id = ''){
        if($member_id <> ""){
            $this->db->where(array('member_id' => $member_id));
        }
        return $this->db->select("*")->from('coop_loan_atm')->where(array('loan_atm_status' => '1', 'total_amount_approve !=' => 'total_amount_balance'))->get()->result_array();
    }

    private function data_find_record($arr = array()){
        $data = array();
        foreach ($arr as $item => $value){
            $data[] = $value['member_id'];
        }
        return $data;
    }

    private function data_atm_detail($arr = array()){
         $data = $this->db->select("*")->from('coop_loan_atm_detail')->where_in('member_id', $arr)->order_by('loan_id', 'DESC')->get()->result_array();
         $rec = array();
         foreach ($data as $item => $val){
             if(!empty($val)) {
                 $rec[$val['member_id']][] = $val;
             }
         }
         return $rec;
    }

    public function data_atm_rec(){
        ini_set('precision', 12);// precision

        $atm_main = $this->data_clear_atm_detail();
        $atm_member_id = $this->data_find_record($atm_main);
        $src = $this->data_atm_detail($atm_member_id);
        $data = array();
        foreach ($atm_main as $members => $member){
            $principle = ROUND($member['total_amount_approve'] - $member['total_amount_balance'], 2);
            if($principle == 0){
                continue;
            }
            //$data[$member['member_id']]['total_amount_approve'] = $member['total_amount_approve'] ;
            //$data[$member['member_id']]['total_amount_balance'] = $member['total_amount_balance'];
            //$data[$member['member_id']]['principle'] = $principle;
            if($principle == 0){
                continue;
            }else {
                $flag = true;
                if(!empty($src[$member['member_id']])) {
                    foreach ($src[$member['member_id']] as $item => $value) {
                        $value['loan_status'] = $principle > 0 ? 0 : 1;
                        $tmp = $principle;
                        $principle -= $value['loan_amount'];
                        if ($tmp > $principle && $principle < 0 && $flag) {
                            $flag = false;
                            $value['loan_amount_balance'] = $value['loan_amount'] + $principle;
                        } else {
                            $value['loan_amount_balance'] = $value['loan_amount'];
                        }
                        if ($tmp < 0) {
                            $value['loan_amount_balance'] = 0;
                        }
                        //$value['principle'] = $principle;

                        $data[$member['member_id']][] = $value;
                    }
                }
            }
        }
        return $data;
    }

    private function convert_format_update($data = array()){
        $num = 0; $result = array();
        foreach ($data as $key => $val){
            foreach ($val as $item => $obj) {
                $result[$num] = $obj;
                $num++;
            }
        }
        return $result;
    }

    public function update_loan_atm_detail(){
        $res = $this->data_atm_rec();
        $item = $this->convert_format_update($res);
        $this->db->update_batch('coop_loan_atm_detail', $item, 'loan_id');
        return $res;
    }

    public function round_process_month($member, $date = null){

        if($date === null || $date === ""){
            return date('Y-m-d');
        }

        $date_round = date("Y-m-24", strtotime($date));
        $current = date("Y-m-d", strtotime($date));
        $check = $this->check_round_process($date_round);
        return $date_round;
    }

    public function check_round_process($date){
        $check = date('w', strtotime($date));
        if($check == "6" || $check == "0" || $this->check_holiday($date)){
            $date_process = date("Y-m-d", strtotime($date." +1 day"));
            return $this->check_round_process($date_process);
        }else{
            return $date;
        }
    }


}
