<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', '-1');
ini_set('memory_limit', '-1');

class Average_dividend extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function average_dividend_test(){
        // AND COLUMN_NAME in ('SEQ')
        $stmt = "SELECT DISTINCT TABLE_NAME, COLUMN_NAME 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA='sotr2' AND TABLE_NAME not in ('reportsupply1', 'reportsupply4')";
        $all_column = $this->db->query($stmt)->result_array();

//        echo $this->db->last_query();

        $table_name = array();
        foreach ($all_column as $key => $item) {
            $table_name[$item['TABLE_NAME']][] = $item['COLUMN_NAME'];
        }
        
//        echo '<pre>'; print_r($all_column);exit;


        foreach ($table_name as $key => $items) {
            $count = $this->db->select('count(*) as count')
                ->from("sotr2.".$key)
                ->get()->row_array();

            if($count['count'] > 0) {
                $this->db->select('*');
                $this->db->from("sotr2." . $key);
                foreach ($items as $item) {
//                    $this->db->where($item . " LIKE '%รับที่สหกรณ์%' OR ". $item . " LIKE '%โอนเข้าบัญชีธนาคาร%' OR ". $item . " LIKE '%รับที่หน่วยงาน%'");
                    $this->db->where($item . " LIKE '2640%'");
                }
                $this->db->limit("2");
                $chack_data = $this->db->get()->result_array();
                $sql = $this->db->last_query();
                $sql = str_replace("AND", "OR", $sql);
                $rs = $this->db->query($sql);
                $chack_data = $rs->result_array();
                if (!empty($chack_data)) {
                    echo $sql . ';<hr>';
                    echo '<pre>';
                    print_r($chack_data);
                    echo '</pre>';
                    echo '<hr>';
                }
            }
        }

//        foreach ($table_name as $key => $items) {
//            $sql = "ALTER TABLE ".$key." CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;";
//            echo $sql;
//            echo '<br>';
//            $sql = "UPDATE ".$key." SET";
//            foreach ($items as $key => $value) {
//                if($key > 0){
//                    $sql .= ", `".$value."` = CONVERT(BINARY(CONVERT(`".$value."` USING latin1)) USING TIS620) <br>";
//                }else{
//                    $sql .= "`".$value."` = CONVERT(BINARY(CONVERT(`".$value."` USING latin1)) USING TIS620) <br>";
//                }
//            }
//            $sql .= ";";
//            echo $sql;
//            echo '<br>';
//            echo '<br>';
//        }
    }

    public function average_dividend_update(){
        $diff_average = $_GET['diff_average'];
        $member_id = $_GET['member_id'];
        $member_id_sotr = '00000'.$member_id;
        $sql = "SELECT 
                t1.member_id,
                t1.interest AS `return_amount`,
                t1.payment_date AS `date` ,
                t1.receipt_id,
                t3.contract_number
                FROM coop_finance_transaction as t1 
                LEFT JOIN coop_receipt as t2 ON t1.receipt_id = t2.receipt_id 
                LEFT JOIN coop_loan as t3 ON t1.loan_id = t3.id 
                WHERE t1.payment_date BETWEEN '2020-01-01' AND '2020-12-31' AND t1.interest<> 0 
                AND (t2.receipt_status != '2' OR t2.receipt_status is null) AND t1.member_id = '".$member_id."'";

        $rs = $this->db->query($sql);
        $row_transaction = $rs->result_array();
        $row_transaction_new = array();
        foreach ($row_transaction as $items) {
            $row_transaction_new[$items['member_id']][$items['contract_number']][$items['date']][$items['receipt_id']][] = $items['return_amount'];
        }

        $sql = "SELECT ID, CN, ED, CALLINTEREST FROM `sotr`.`loanentry` WHERE `ID` = '".$member_id_sotr."' AND `ED` >= '25630000' AND `ED` < '25640000' AND PT NOT IN ('2', '5') ";
        $rs = $this->db->query($sql);
        $row_loanentry = $rs->result_array();

        echo $sql;

        $row_loanentry_new = array();
        foreach ($row_loanentry as $items) {
            $member_id = substr($items['ID'],-5);
//            $row_loanentry_new[$member_id]['receipt_id'] = $items['CN'];
            $date_1 = substr($items['ED'],0,4);
            $date_2 = substr($items['ED'],4,2);
            $date_3 = substr($items['ED'],6,2);
            $date = ($date_1-543).'-'.$date_2.'-'.$date_3;
            $row_loanentry_new[$member_id][$items['CN']][$date][] = $items['CALLINTEREST'];
        }
//        echo '<pre>';
//        print_r($row_transaction_new);
        echo '<hr>';
        echo round( $diff_average*100/10.25, 2);
        echo '<hr>';
//        print_r($row_loanentry_new);
//        echo '</pre>';

        foreach ($row_transaction_new as $member_id => $contract_number_arr) {
            foreach ($contract_number_arr as $contract_number => $date_arr) {
                foreach ($date_arr as $date => $receipt_arr) {
                    foreach ($receipt_arr as $receipt_id => $return_amount_arr) {
                        foreach ($return_amount_arr as $return_amount_key => $return_amount) {
                            echo number_format($return_amount, 2);
                            if(!empty($row_loanentry_new[$member_id][$contract_number][$date])) {
                                foreach ($row_loanentry_new[$member_id][$contract_number][$date] as $loanentry_key => $loanentry_value) {
                                    if ($return_amount == $loanentry_value) {
                                        echo ' | ' . $loanentry_value;
                                        unset($row_transaction_new[$member_id][$contract_number][$date][$receipt_id][$return_amount_key]);
                                        unset($row_loanentry_new[$member_id][$contract_number][$date][$loanentry_key]);
                                        break;
                                    }
                                }
                            }
                            echo '<br>';
                        }
                    }
                }
            }
        }

        foreach ($row_transaction_new as $member_id => $contract_number_arr) {
            foreach ($contract_number_arr as $contract_number => $date_arr) {
                foreach ($date_arr as $date => $receipt_arr) {
                    foreach ($receipt_arr as $receipt_id => $return_amount_arr) {
                        if(empty($row_transaction_new[$member_id][$contract_number][$date][$receipt_id])){
                            unset($row_transaction_new[$member_id][$contract_number][$date][$receipt_id]);
                        }
                        if(!empty($row_loanentry_new[$member_id][$contract_number][$date])) {
                            foreach ($row_loanentry_new[$member_id][$contract_number][$date] as $loanentry_key => $loanentry_value) {
                                if (empty($row_transaction_new[$member_id][$contract_number][$date][$loanentry_key])) {
                                    unset($row_transaction_new[$member_id][$contract_number][$date][$loanentry_key]);
                                }
                            }
                        }
                    }
                    if(empty($row_transaction_new[$member_id][$contract_number][$date])){
                        unset($row_transaction_new[$member_id][$contract_number][$date]);
                    }
                    if(empty($row_loanentry_new[$member_id][$contract_number][$date])){
                        unset($row_loanentry_new[$member_id][$contract_number][$date]);
                    }
                }
                if(empty($row_transaction_new[$member_id][$contract_number])){
                    unset($row_transaction_new[$member_id][$contract_number]);
                }
                if(empty($row_loanentry_new[$member_id][$contract_number])){
                    unset($row_loanentry_new[$member_id][$contract_number]);
                }
            }
            if(empty($row_transaction_new[$member_id])){
                unset($row_transaction_new[$member_id]);
            }
            if(empty($row_loanentry_new[$member_id])){
                unset($row_loanentry_new[$member_id]);
            }
        }

        foreach ($row_loanentry_new as $member_id => $contract_number_arr) {
            foreach ($contract_number_arr as $contract_number => $date_arr) {
                foreach ($date_arr as $date => $number) {
                    foreach ($number as $key => $value) {
                        if($value == '0.00'){
                            unset($row_loanentry_new[$member_id][$contract_number][$date][$key]);
                        }
                    }
                    if(empty($row_loanentry_new[$member_id][$contract_number][$date])){
                        unset($row_loanentry_new[$member_id][$contract_number][$date]);
                    }
                }
                if(empty($row_loanentry_new[$member_id][$contract_number])){
                    unset($row_loanentry_new[$member_id][$contract_number]);
                }
            }
            if(empty($row_loanentry_new[$member_id])){
                unset($row_loanentry_new[$member_id]);
            }
        }

        echo '<pre>';
        echo '----- ลบออก -----<br>';
        print_r($row_transaction_new);
        echo '----- เพิ่ม -----<br>';
        print_r($row_loanentry_new);
        echo '</pre>';

    }

    public function import_data_average_dividend(){
        $this->db->select('id, member_id');
        $this->db->from('coop_mem_apply');
        $coop_mem_apply_arr = $this->db->get()->result_array();
        $coop_mem_apply = array();

        $this->db->select('ID, HOWTORECV');
        $this->db->from('sotr3.member');
        $member_arr = $this->db->get()->result_array();
        $member = array();

        $this->db->select('info, name');
        $this->db->from('coop_dividend_average_receive');
        $receive_arr = $this->db->get()->result_array();
        $receive = array();

        foreach ($receive_arr as $item) {
            $receive[$item['info']] = $item['name'];
        }

        foreach ($member_arr as $item) {
            $strops = strpos($item['ID'],'S');
            if($strops === FALSE){
                $item['ID'] = (int)$item['ID'];
                $item['ID'] = sprintf("%05d",$item['ID']);
            }
            $member[$item['ID']] = $item['HOWTORECV'];
        }

        foreach ($coop_mem_apply_arr as $item) {
            $coop_mem_apply[$item['member_id']]['id'] = $item['id'];
            $coop_mem_apply[$item['member_id']]['info'] = $member[$item['member_id']];
            $coop_mem_apply[$item['member_id']]['name'] = $receive[$member[$item['member_id']]];
        }

//        echo '<pre>'; print_r($receive); echo '</pre>';
//        echo '<pre>'; print_r($member); echo '</pre>';
//        echo '<pre>'; print_r($coop_mem_apply); echo '</pre>';

        foreach ($coop_mem_apply as $key => $value) {
            $sql = "UPDATE `coop_mem_apply` SET `average_receive` = '".$value['info']."' WHERE `id` = ".$value['id'].";";
            echo $sql.'<br>';
        }
    }

    public function index()
    {
        $arr_data = array();

        $this->db->select(array(
            'id',
            'year',
            'dividend_percent',
            'average_percent',
            'dividend_value',
            'average_return_value',
            'gift_varchar',
            'status',
            'approve_date'
        ));


        $this->db->from('coop_dividend_average_master');
        $this->db->order_by('id DESC');
        $row = $this->db->get()->result_array();


        #check report insure
        $this->db->select('*')->from('coop_dividend_deduct');
        $this->db->where("transfer_date IS NOT NULL AND deduct_id in (2,3,4)");
        $this->db->group_by("master_id");
        $res = $this->db->get()->result_array();

        $report_insure = [];
        foreach ($res as $index => $value){
            $report_insure[$value['master_id']] = true;
        }
        $arr_data['report_insure'] = $report_insure;


        #check receipt
        $field = ['master_id', ' count(member_id) as `counts`'];
        $this->db->select()
        ->from('coop_dividend_average_receipt')
        ->group_by("master_id");

        $res = $this->db->get()->result_array();
        $chk_receipt = [];
        foreach ($res as $key => $val){
            $chk_receipt[$val['master_id']] = $val['counts'] !== 0;
        }
        $arr_data['chk_receipt'] = $chk_receipt;

        $arr_data['month_arr'] = array('1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม');
        $arr_data['month_short_arr'] = array('1' => 'ม.ค.', '2' => 'ก.พ.', '3' => 'มี.ค.', '4' => 'เม.ย.', '5' => 'พ.ค.', '6' => 'มิ.ย.', '7' => 'ก.ค.', '8' => 'ส.ค.', '9' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.');

        $arr_data['data'] = $row;
        //print_r($this->db);
        $this->libraries->template('average_dividend/index', $arr_data);
    }

    public function management(){

        $master_id      = $_GET['id'];
        $limit          = 1000;
        $page           = isset($_GET['page']) ? ($_GET['page'] - 1 ) : 0 ;
        $member_id      = isset($_GET['member_id']) && !empty($_GET['member_id']) ? str_pad($_GET['member_id'], 6, '0', STR_PAD_LEFT) : "";

        $stmt = "SELECT m.member_id, (m.dividend_value+m.average_return_value) AS `return`,cast(IF (`deduct`.amount IS NULL,0,`deduct`.amount) AS DECIMAL (18,2)) AS deduct,`m`.`gift_varchar`,a.account_id,a.account_status FROM coop_dividend_average m LEFT JOIN (SELECT `member_id`,`master_id`,ROUND(SUM(amount),2) AS `amount` FROM coop_dividend_deduct GROUP BY member_id) `deduct` ON `m`.`master_id`=`deduct`.`master_id` AND `m`.`member_id`=`deduct`.`member_id` LEFT JOIN coop_maco_account a ON m.member_id=a.mem_id AND a.type_id=2 WHERE m.master_id='{$master_id}' GROUP BY member_id ORDER BY m.member_id ";
        $count = $this->db->query($stmt)->num_rows();

        $list = [];
        for($number = 1; $number <= ($count/$limit) + 1; $number++){

            $list[($number - 1)]['page'] = $number;
            if($number > 1){
                $txt    = ($number-1) * $limit + 1;
            }else{
                $txt    = $number;
            }

            $stmt_loop = "SELECT min(member_id) as `min`, max(member_id) as `max` FROM (".$stmt." LIMIT 1000 OFFSET ".(($number-1) * $limit)." ) T ";
            $p = $this->db->query($stmt_loop)->row();

            if(($p->max - $p->min) < 1000){
                $min_limit = $txt + ($p->max - $p->min);
            }else{
                $min_limit = $number * $limit;
            }

            $list[($number - 1)]['text'] = $txt." - ".($min_limit)." (รหัส ".$p->min." - ".$p->max.") ";

        }
        $arr_data['page'] = $list;

//        $str        = array_map(function($v){ return sprintf( "'%s'", $v); }, explode(" - ",$list[($page)]['text']));
//        $_limit     = " AND `m`.`member_id` between ".join(" AND ", $str);
        $_limit = "";
        if(isset($page)) {
            $_limit = "LIMIT " . $limit . " OFFSET " . ($page * $limit);
        }

        $cond = "";
        if(!empty($member_id)){
            $cond = "AND m.member_id = '{$member_id}' ";
        }

        $arr_data['member_id'] = $member_id;
        $arr_data['master_id'] = $master_id;

        $stmt = "SELECT m.member_id,concat(p.prename_full, c.firstname_th, ' ', c.lastname_th) as fullname, (m.dividend_value+m.average_return_value) AS `return`,cast(IF (`deduct`.amount IS NULL,0,`deduct`.amount) AS DECIMAL (18,2)) AS deduct,`m`.`gift_varchar`,a.account_id,a.account_status, `t`.`ignore_return`, `t`.`ignore_gift` FROM coop_dividend_average m INNER JOIN coop_mem_apply c ON m.member_id=c.member_id LEFT JOIN (SELECT `member_id`,`master_id`,ROUND(SUM(amount),2) AS `amount` FROM coop_dividend_deduct GROUP BY member_id) `deduct` ON `m`.`master_id`=`deduct`.`master_id` AND `m`.`member_id`=`deduct`.`member_id` LEFT JOIN coop_maco_account a ON m.member_id=a.mem_id AND a.type_id=2 LEFT JOIN coop_dividend_ignore_transfer t ON m.master_id = t.master_id AND m.member_id = t.member_id LEFT JOIN coop_prename p ON c.prename_id = p.prename_id WHERE m.master_id='{$master_id}' {$cond} GROUP BY member_id ORDER BY m.member_id ".$_limit;
        $rs = $this->db->query($stmt)->result_array();
        $arr_data['list'] = $rs;

        $this->libraries->template('average_dividend/management', $arr_data);
    }

    public function ignore_transfer(){

        if(@$_POST){

            $stmt = "INSERT INTO coop_dividend_ignore_transfer (master_id, member_id, ignore_return, ignore_gift, date_create) VALUES ( ?, ?, ?, ?, ? ) ON DUPLICATE KEY UPDATE master_id = VALUES(master_id), member_id = VALUES(member_id), ignore_return = VALUES(ignore_return), ignore_gift = VALUES(ignore_gift)";
            $this->db->query($stmt, array(
                @$_POST['master_id'],
                @$_POST['member_id'],
                @$_POST['ignore_return'],
                @$_POST['ignore_gift'],
                date('Y-m-d H:i:s')
            ));

            //$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

            header('Content-Type: application/json');
            echo json_encode(array('status' => true));
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode(array('status' => false));
        exit;
    }

    function save_data_test()
    {

        $data_insert            = array();
        $data_insert['year']    = (date('Y') + 543);
        $data_insert['status']  = '0';
        $this->db->insert('coop_dividend_average_master', $data_insert);
        $last_id = $this->db->insert_id();

        $average_percent        = ($_POST['average_percent2'] / 100);
        $dividend_percent       = ($_POST['dividend_percent1'] / 100);
        $gift_varchar           = ($_POST['money_gift']);

        $sum_average_return     = 0;
        $sum_dividend_return    = 0;

        $data_arr = array();
        $this->db->select(array('SUM(t1.interest) as sum_interest', 't1.member_id'));
        $this->db->from('coop_finance_transaction as t1');
        $this->db->where("t1.interest > 0 AND t1.payment_date BETWEEN '" . date('Y') . "-01-01' AND '" . date('Y') . "-12-31'");
        $this->db->group_by("t1.member_id");
        $rs = $this->db->get()->result_array();
        //echo $this->db->last_query(); exit;

        foreach ($rs as $key => $row) {
            $data_arr[$row['member_id']]['sum_interest']             = $row['sum_interest'];
            $average_return                                          = $row['sum_interest'] * $average_percent;
            @$data_arr[$row['member_id']]['sum_average_return_now'] += $average_return;
        }

        #หารายการที่มีหุ้นคงเหลือเป็น 0
        $stmt = "SELECT member_id, SUM(share_early_value) AS share_early_value FROM (
SELECT `member_id`,IF (`share_type`='SRP',`share_early_value`*-1,`share_early_value`) AS `share_early_value`,`share_date`,`share_type`,`share_status` FROM `coop_mem_share` WHERE `share_type` IN ('SPA','SPM','SRP') AND `share_date` BETWEEN '2017-11-08' AND NOW() AND `share_early_value`<> 0 AND `share_status` IN (1,5) ORDER BY `share_date` ASC) t1 GROUP BY member_id HAVING share_early_value=0";
        $unlike = $this->db->query($stmt)->result_array();

        $resolve = [];
        foreach ($unlike as $key => $val){
            $resolve[$val['member_id']] = $val['share_early_value'];
        }
        unset($unlike);


        $this->db->select(array(
            't1.member_id',
            't1.share_collect_value',
            't1.share_date'
        ));

        $this->db->from('coop_mem_share as t1');
        $this->db->where("
				share_status != '2'
				AND t1.share_date <= '" . (date('Y') - 1) . "-12-31'
			");
        $this->db->order_by("t1.share_date ASC");
        //สถานะหุ้น array('0'=>'รอชำระเงิน', '1'=>'ชำระเงินแล้ว', '2'=>'รออนุมัติยกเลิกใบเสร็จ', '3'=>'ยกเลิกใบเสร็จ','4'=>'คืนเงิน','5'=>'ถอนหุ้น','6'=>'ส่วนต่างเก็บรายเดือนไม่ครบ')
        $rs = $this->db->get()->result_array();

        //echo $this->db->last_query(); exit;
        $data_share = array();
        foreach ($rs as $key => $row) {
            $data_share[$row['member_id']]['member_id'] = $row['member_id'];
            $data_share[$row['member_id']]['share_collect_value'] = $row['share_collect_value'];
        }

        foreach ($data_share as $key => $row) {
            $share_collect_value = $row['share_collect_value'];
            $dividend_return = $share_collect_value * $dividend_percent * (12 / 12);
            @$data_arr[$row['member_id']]['sum_dividend_return_now'] += $dividend_return;
        }

        $this->db->select(array(
            'member_id', 'share_early_value', 'share_date', 'share_type'
        ));
        $this->db->from('coop_mem_share');
        $this->db->where("share_type in ('SPA','SPM', 'SRP')  and share_date BETWEEN '" . (date('Y') - 1) . "-11-08' AND NOW()  AND share_early_value <> 0 AND share_status IN (1, 5) ");
        $this->db->order_by("member_id,share_date asc");
        $res = $this->db->get()->result_array();

        //echo"<pre>";print_r($res);exit;

        //----------------- ค่าคงที่เวลาการคำนวนหุ้น--------------
        $list_month_cal = [];
        $a = 11;
        $b = 1;
        $arr = [];
        $data_insert = [];
        for ($i = 12; $i >= 0; $i--) {
            if ($i > 10) {
                $list_month_cal[(date('Y') - 1)][$a] = $i / 12;
                $a++;
            } else {
                $list_month_cal[(date('Y'))][sprintf('%02d', $b)] = $i / 12;
                $b++;
            }

        }


        #หาเงินปันผลในรอบปี
        $data1 = array();
        foreach ($res as $index => $key) {

            $listd  = explode("-", $res[$index]['share_date']);
            $dmz    = intval(substr($listd[2], 0, 2));

            if ($dmz < 8) {

                $mo = str_pad(intval($listd[1]) - 1 , 2, "0", STR_PAD_LEFT);

                if($mo == "00"){
                    $mo = "12";
                    $listd[0] -= 1;
                }

            } else {
                $mo = str_pad(intval($listd[1]), 2, "0", STR_PAD_LEFT);

            }
            if($key['share_type'] == 'SRP') {
                $data1[$res[$index]['member_id']] -= $res[$index]['share_early_value'] * $dividend_percent * $list_month_cal[$listd[0]][$mo];
            }else{
                $data1[$res[$index]['member_id']] += $res[$index]['share_early_value'] * $dividend_percent * $list_month_cal[$listd[0]][$mo];
            }

        }
        unset($res);
        //echo"<pre>";print_r($data1);exit;

        #หาดอกเบี้ยสะสม
        $sql = "SELECT member_id,SUM(return_amount) AS return_amount FROM (SELECT member_id,interest AS `return_amount`,payment_date AS `date` FROM coop_finance_transaction WHERE payment_date BETWEEN '" . (date('Y') - 1) . "-12-01' AND '" . (date('Y')) . "-11-30' AND account_list_id IN ('15','31') AND interest<> 0 UNION ALL SELECT member_id,(return_amount*-1) AS return_amount,return_time AS `date` FROM coop_process_return WHERE return_time BETWEEN '" . (date('Y') - 1) . "-12-01' AND '" . (date('Y')) . "-11-30') T GROUP BY member_id";
        $rs = $this->db->query($sql);
        //echo $this->db->last_query(); exit;
        $row_transaction = $rs->result_array();
        $key_transaction = array();
        foreach ($row_transaction as $value) {
            $key_transaction[$value['member_id']] = $value['return_amount'];
        }
        unset($row_transaction);

        //echo"<pre>";print_r($key_transaction);exit;
        /*foreach ($res as $index => $val) {


            $listd = explode("-", $res[$index]['share_date']);
            $data1[$res[$index]['member_id']] += $res[$index]['share_early_value'] * $average_percent * $list_month_cal[$listd[0]][$listd[1]];


            //$listd = explode("-",$res[$index]['share_date']);

            //$arr[$res[$index]['member_id']][$listd[0]][$listd[1]] = $res[$index]['share_early_value']*$average_percent*$list_month_cal[$listd[0]][$listd[1]];

            $data_insert['member_id'] = $res[$index]['member_id'];
            $data_insert['month'] = $listd[1];
            $data_insert['year'] = $listd[0];
            $data_insert['dividend_percent'] = $_POST['dividend_percent'];
            $data_insert['average_percent'] = $_POST['average_percent'];
            $data_insert['dividend_value'] = number_format(@$res[$index]['share_early_value']*$average_percent*$list_month_cal[$listd[0]][$listd[1]],2,'.','');
            $data_insert['average_return_value'] = '';
            $data_insert['master_id'] = $last_id;
            $data_insert['date_create'] = date('Y-m-d H:i:s');
            $this->db->insert('coop_dividend_average', $data_insert);
            $sum_divide += $res[$index]['share_early_value']*$average_percent*$list_month_cal[$listd[0]][$listd[1]];

        }*/


        #หารายชื่อคน
        $this->db->select(array("member_id", "apply_type_id"));
        $this->db->from('coop_mem_apply');
        $this->db->where(array('mem_type' => 1));
        $res_mem = $this->db->get()->result_array();

        $member_apply_type = array();
        foreach ($res_mem as $key => $value){
            $member_apply_type[$value['member_id']] = $value['apply_type_id'];
        }
        unset($res_mem);

        $number = 0;
        $arr_list = [];
        foreach ($data1 as $indexx => $vall) {
            if(isset($member_apply_type[$indexx])) {
                $arr_list[$number]['member_id'] = $indexx;
                $arr_list[$number]['master_id'] = $last_id;
                $arr_list[$number]['date_create'] = date('Y-m-d H:i:s');
                $arr_list[$number]['year'] = date('Y');
                $arr_list[$number]['dividend_percent'] = $_POST['dividend_percent1'];
                $arr_list[$number]['average_percent'] = $_POST['average_percent2'];
                $arr_list[$number]['dividend_value'] = $vall;
                $arr_list[$number]['average_return_value'] = $key_transaction[$indexx] * $average_percent;
                $arr_list[$number]['gift_varchar'] = $member_apply_type[$indexx] == '1' ? $gift_varchar : 0;
                $number++;
            }
        }
        unset($data1);

        $this->db->insert_batch('coop_dividend_average', $arr_list);

        //echo"<pre>";print_r($data1);exit;
        /*
                    for($i=1; $i<=11; $i++){

                        $this->db->select(array(
                            't1.member_id',
                            't1.share_early_value',
                            't1.share_date'
                        ));
                        $this->db->from('coop_mem_share as t1');
                        $this->db->where("
                            share_status == '1'
                            AND t1.share_date BETWEEN '".date('Y')."-".sprintf('%02d',$i)."-08' AND ''
                            AND t1.share_type = 'SPM'
                        ");
                        //สถานะหุ้น array('0'=>'รอชำระเงิน', '1'=>'ชำระเงินแล้ว', '2'=>'รออนุมัติยกเลิกใบเสร็จ', '3'=>'ยกเลิกใบเสร็จ','4'=>'คืนเงิน','5'=>'ถอนหุ้น','6'=>'ส่วนต่างเก็บรายเดือนไม่ครบ')

                        $this->db->order_by("t1.share_date ASC");
                        $rs = $this->db->get()->result_array();
                        echo $this->db->last_query(); exit;
                        $data_share = array();
                        foreach($rs as $key => $row){
                            $data_share[$row['member_id']]['member_id'] = $row['member_id'];
                            $data_share[$row['member_id']]['share_collect_value'] = $row['share_collect_value'];
                        }
                        foreach($data_share as $key => $row){
                            $dividend_return = $row['share_collect_value']*$dividend_percent*((12-$i)/12);
                            @$data_arr[$row['member_id']]['sum_dividend_return_now'] += $dividend_return;
                        }


                        $prev_date = date('Y-m-d',strtotime('-1 month' ,strtotime(date('Y')."-".sprintf('%02d',$i)."-07")));
                            $now_date = date('Y')."-".sprintf('%02d',$i)."-07";

                            $this->db->select(array(
                                't1.member_id',
                                't1.share_collect_value',
                                't1.share_date'
                            ));
                            $this->db->from('coop_mem_share as t1');
                            $this->db->where("
                                share_status != '2'
                                AND t1.share_date BETWEEN '".$prev_date."' AND '".$now_date."'
                                AND t1.share_type = 'SPA'
                            ");
                            $this->db->order_by("t1.share_date ASC");


                            $rs = $this->db->get()->result_array();
                            echo $this->db->last_query(); exit;
                            $data_share = array();
                            foreach($rs as $key => $row){
                                $data_share[$row['member_id']]['share_collect_value'] = $row['share_collect_value'];
                            }
                            foreach($data_share as $key => $row){
                                $dividend_return = $row['share_collect_value']*$dividend_percent*((12-$i)/12);
                                $sum_dividend_return += $dividend_return;
                            }

                    }

                    //echo"<pre>";print_r($data_arr);exit;
                foreach($data_arr as $key => $value){
                        $data_insert = array();

                        $data_insert['member_id'] = $key;
                        $data_insert['year'] = (date('Y')+543);
                        $data_insert['dividend_percent'] = $_POST['dividend_percent'];
                        $data_insert['average_percent'] = $_POST['average_percent'];
                        $data_insert['dividend_value'] = number_format(@$value['sum_dividend_return_now'],2,'.','');
                        $data_insert['average_return_value'] = number_format(@$value['sum_average_return_now'],2,'.','');
                        $data_insert['master_id'] = $last_id;
                        $data_insert['date_create'] = date('Y-m-d H:i:s');
                        $this->db->insert('coop_dividend_average', $data_insert);

                        //echo $sql_insert."<br>";
                        $sum_dividend_return += number_format(@$value['sum_dividend_return_now'],2,'.','');
                        $sum_average_return += number_format(@$value['sum_average_return_now'],2,'.','');
                    }
                    //exit;
                $data_insert = array();
                $data_insert['dividend_percent'] = $_POST['dividend_percent'];
                $data_insert['average_percent'] = $_POST['average_percent'];
                $data_insert['dividend_value'] = number_format($sum_dividend_return,2,'.','');
                $data_insert['average_return_value'] = number_format($sum_average_return,2,'.','');
                $data_insert['date_create'] = date('Y-m-d H:i:s');
                $this->db->where('id', $last_id);
                $this->db->update('coop_dividend_average_master', $data_insert);
                */

        $sum_divide         = array_sum(array_map(function($val){ return $val['dividend_value'];}, $arr_list));
        $sum_average_return = array_sum(array_map(function($val){ return $val['average_return_value'];}, $arr_list));

        $data_insert = array();

        $data_insert['dividend_percent']        = $_POST['dividend_percent1'];
        $data_insert['average_percent']         = $_POST['average_percent2'];
        $data_insert['gift_varchar']            = number_format($gift_varchar, 2, '.', '');
        $data_insert['dividend_value']          = number_format($sum_divide, 2, '.', '');
        $data_insert['average_return_value']    = number_format($sum_average_return, 2, ".", "");
        $data_insert['date_create']             = date('Y-m-d H:i:s');
        $this->db->where('id', $last_id);
        $this->db->update('coop_dividend_average_master', $data_insert);

        $this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
        //exit;
        echo "<script>document.location.href='" . base_url(PROJECTPATH . '/average_dividend') . "'</script>";
    }

    function average_dividend_expect()
    {
        $data_arr = array();
        $arr_data = array();
        for ($j = 1; $j <= 3; $j++) {
            if ($_POST['average_percent'][$j] == '' || $_POST['dividend_percent'][$j] == '') {
                continue;
            }
            $average_percent = ($_POST['average_percent'][$j] / 100);
            $dividend_percent = ($_POST['dividend_percent'][$j] / 100);

            $sum_average_return = 0;
            $sum_dividend_return = 0;

            $this->db->select(array('SUM(t1.interest) as sum_interest'));
            $this->db->from('coop_finance_transaction as t1');
            $this->db->where("
					t1.interest > 0
					AND t1.payment_date BETWEEN '" . date('Y') . "-01-01' AND '" . date('Y') . "-12-31'
				");
            $row = $this->db->get()->result_array();

            $sum_interest = $row[0]['sum_interest'];

            $average_return = $sum_interest * $average_percent;
            $sum_average_return += $average_return;

            $this->db->select(array(
                't1.member_id',
                't1.share_collect_value',
                't1.share_date'
            ));
            $this->db->from('coop_mem_share as t1');
            $this->db->where("t1.share_date <= '" . (date('Y') - 1) . "-12-31'");
            $this->db->order_by("t1.share_date ASC");
            $rs = $this->db->get()->result_array();
            $data_share = array();
            foreach ($rs as $key => $row) {
                $data_share[$row['member_id']]['share_collect_value'] = $row['share_collect_value'];
            }
            foreach ($data_share as $key => $row) {
                $share_collect_value = $row['share_collect_value'];
                $dividend_return = $share_collect_value * $dividend_percent * (12 / 12);
                $sum_dividend_return += $dividend_return;
            }

            for ($i = 1; $i <= 11; $i++) {
                $this->db->select(array(
                    't1.member_id',
                    't1.share_collect_value',
                    't1.share_date'
                ));
                $this->db->from('coop_mem_share as t1');
                $this->db->where("
						share_status != '2'
						AND t1.share_type = 'SPM'
						AND t1.share_date LIKE '" . date('Y') . "-" . sprintf('%02d', $i) . "%'
					");
                $this->db->order_by("t1.share_date ASC");
                $rs = $this->db->get()->result_array();
                $data_share = array();
                foreach ($rs as $key => $row) {
                    $data_share[$row['member_id']]['share_collect_value'] = $row['share_collect_value'];
                }
                foreach ($data_share as $key => $row) {
                    $dividend_return = $row['share_collect_value'] * $dividend_percent * ((12 - $i) / 12);
                    $sum_dividend_return += $dividend_return;
                }

                $prev_date = date('Y-m-d', strtotime('-1 month', strtotime(date('Y') . "-" . sprintf('%02d', $i) . "-07")));
                $now_date = date('Y') . "-" . sprintf('%02d', $i) . "-07";

                $this->db->select(array(
                    't1.member_id',
                    't1.share_collect_value',
                    't1.share_date'
                ));
                $this->db->from('coop_mem_share as t1');
                $this->db->where("
						share_status != '2'
						AND t1.share_date BETWEEN '" . $prev_date . "' AND '" . $now_date . "'
						AND t1.share_type = 'SPA'
					");
                $this->db->order_by("t1.share_date ASC");
                $rs = $this->db->get()->result_array();
                $data_share = array();
                foreach ($rs as $key => $row) {
                    $data_share[$row['member_id']]['share_collect_value'] = $row['share_collect_value'];
                }
                foreach ($data_share as $key => $row) {
                    $dividend_return = $row['share_collect_value'] * $dividend_percent * ((12 - $i) / 12);
                    $sum_dividend_return += $dividend_return;
                }

            }
            //}
            $data_arr[$j]['average_percent'] = $_POST['average_percent'][$j];
            $data_arr[$j]['average_return'] = $sum_average_return;
            $data_arr[$j]['dividend_percent'] = $_POST['dividend_percent'][$j];
            $data_arr[$j]['dividend_return'] = $sum_dividend_return;


            $arr_data['data_arr'] = $data_arr;
        }

        $this->db->select(array(
            'coop_name_th',
            'address1',
            'address2',
            'coop_img'
        ));
        $this->db->from('coop_profile');
        $this->db->limit(1);
        $row_profile = $this->db->get()->result_array();
        $arr_data['row_profile'] = $row_profile[0];

        $this->load->view('average_dividend/average_dividend_expect', $arr_data);
    }

    function save_data(){

        $data_insert            = array();
        $data_insert['year']    = (date('Y') + 543);
        $data_insert['status']  = '0';
        $this->db->insert('coop_dividend_average_master', $data_insert);
        $last_id = $this->db->insert_id();

        $average_percent = ($_POST['average_percent2'] / 100);
        $dividend_percent = ($_POST['dividend_percent1'] / 100);
        $gift_varchar = $_POST['money_gift'];

        $this->db->select('accm_month_ini');
        $account_period_setting = $this->db->get('coop_account_period_setting')->row();
        $setting_period =  $account_period_setting->accm_month_ini;
        if(empty($setting_period)){
            $setting_period = 11;
        }
        if($setting_period == '1'){
            $Minus_year = 1;
        }else{
            $Minus_year = 0;
        }
        $Minus_year = 0;
        $period_start = date( 'Y-m-t',strtotime((date('Y')-1-$Minus_year)."-".($setting_period-1)));
        $period_first_start = (date('Y')-1-$Minus_year)."-".$setting_period."-01";
        $period_second_start = (date("Y")-1-$Minus_year)."-".$setting_period."-01";
        $period_last_month = (date("Y")-$Minus_year)."-".($setting_period)."-30";
        $period_end = date('Y-m-t', strtotime((date('Y')-$Minus_year)."-".($setting_period-1)));
        $period_end_month = (date("Y")-$Minus_year)."-".($setting_period)."-30";

        $average_start =date('Y-m-d', strtotime((date('Y') - 1-$Minus_year)."-".$setting_period."-01"));
        $average_stop =date('Y-m-t', strtotime((date('Y')-$Minus_year)."-".($setting_period-1)));

        $datas = [];
        $this->db->select(array(
            't1.member_id', "t1.share_early_value", 't1.share_date', 't1.share_type'
        ));
        $this->db->from('coop_mem_share as t1');
        $this->db->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner");
        $this->db->where("t1.share_type in ('SPA','SPM', 'SRP','SB', 'MS', 'RX', 'RM','SDP')  and t1.share_date BETWEEN '" .$period_second_start."' AND '".$period_last_month."'  AND t1.share_early_value <> 0 AND t1.share_status in (1,5) AND t2.member_status != '2'");
        $this->db->order_by("member_id asc,share_date asc");
        $res = $this->db->get()->result_array();

        if($_GET['debug'] == 'test'){
            echo $this->db->last_query();
        }

        $stmt  = "SELECT a.member_id,m.share_collect_value AS `share_collect_value`,a.share_date FROM (
SELECT max(x.share_collect_value) AS share_collect_value,x.share_date,x.member_id FROM (
SELECT member_id,MAX(share_date) AS share_date FROM coop_mem_share WHERE share_date<='".$period_first_start."' AND share_status != '3' GROUP BY member_id) v INNER JOIN coop_mem_share x ON v.member_id=x.member_id AND v.share_date=x.share_date GROUP BY x.member_id) a INNER JOIN coop_mem_share m ON a.member_id=m.member_id AND a.share_date=m.share_date AND a.share_collect_value=m.share_collect_value INNER JOIN coop_mem_apply t3 ON a.member_id=t3.member_id AND t3.member_status != '2' HAVING share_collect_value> 0 ORDER BY member_id ASC";
        $result = $this->db->query($stmt)->result_array();
        $share_collect_value = [];
        foreach ($result as $key => $value){
            $share_collect_value[$value['member_id']] = $value['share_collect_value'];
        }
        unset($result);


        #หารายการที่มีหุ้นคงเหลือเป็น 0
        $stmt = "SELECT member_id, SUM(share_early_value) AS share_early_value FROM (
SELECT `member_id`,IF (`share_type`='SRP',`share_early_value`*-1,`share_early_value`) AS `share_early_value`,`share_date`,`share_type`,`share_status` FROM `coop_mem_share` WHERE `share_type` IN ('SPA','SPM', 'SRP','SB', 'MS', 'RX', 'RM','SDP') AND `share_date` BETWEEN '" . $period_start."' AND '" .$period_end."' AND `share_early_value`<> 0 AND `share_status` IN (1,5) ORDER BY `share_date` ASC) t1 GROUP BY member_id HAVING share_early_value <= 0";
        $unlike = $this->db->query($stmt)->result_array();

        $resolve = [];
        foreach ($unlike as $key => $val){
            $resolve[$val['member_id']] = $val['share_early_value'];
        }
        unset($unlike);


        if($_GET['debug'] == 'test'){
            echo $this->db->last_query();
        }

        $this->db->select('cal_day, switch_cal_dividend, switch_cal_average');
        $setting_dividend_average = $this->db->get('coop_setting_dividend_average')->row();
        $cal_day =  $setting_dividend_average->cal_day;
        $switch_cal_dividend =  $setting_dividend_average->switch_cal_dividend;
        if(empty($setting_period)){
            $cal_day = 365;
        }
        if(empty($setting_period)){
            $switch_cal_dividend = 2;
        }
        $switch_cal_dividend = 2;

        $list_month_cal = [];
        $a = $setting_period;
        $b = 1;
        $arr = [];
        $data_insert = [];
        $first_time = 1;
        for ($i = 12; $i >= 0; $i--) {
            if ($i >= $setting_period) {
                $list_month_cal[(date('Y') - 1)][sprintf('%02d', $a)] = ($i-1) / 12;
                $a++;
            } else {
                if($first_time == 1){
                    $first_time = 0;
                    $cut = 1;
                    if($setting_period == 1){
                        $cut++;
                        $i2 = 13;
                    }else{
                        $i2 = $setting_period;
                    }
                    $list_month_cal[(date('Y')-$cut)][sprintf('%02d', $i2-1)] = 1;
                }
                $list_month_cal[(date('Y') )][sprintf('%02d', $b)] = ($i-1) / 12;
                if($list_month_cal[(date('Y') )][sprintf('%02d', $b)] < 0){
                    $list_month_cal[(date('Y') )][sprintf('%02d', $b)] = 0;
                }
                $b++;
            }

        }

        #หาเงินปันผลในรอบปี
        $data1 = array(); $chk = [];

        $no = 1;
        foreach($share_collect_value as $index => $val ){
            $data1[$index] += $val * $dividend_percent * 1;
            if(isset($_GET['debug']) && isset($_GET['member_id']) && $index == $_GET['member_id']){
                echo $no. " :: ". ($val * $dividend_percent * 1) ." :: ". $val." * ".$dividend_percent." * 1 <br>" ;
            }
        }
        unset($share_collect_value);

        foreach ($res as $index => $val) {

            $listd = explode("-", $val['share_date']);
            $dmz = intval($listd[2]);

            if ($dmz < 8) {

                $mo = str_pad(intval($listd[1]) - 1 , 2, "0", STR_PAD_LEFT);
                if($mo == "00"){
                    $mo = "12";
                    $listd[0] -= 1;
                }
            } else {
                $mo = str_pad(intval($listd[1]), 2, "0", STR_PAD_LEFT);
                if($mo == $setting_period){
//                    $mo -= 1;

                }
            }


//            if($list_month_cal[$listd[0]][$mo] == 1){
//                $val['share_early_value'] = $share_collect_value[$val['member_id']];
//            }
            $new_share_date = date("Y-m-d", strtotime($val['share_date']));
            $diff_day = $this->center_function->diff_day($period_first_start, $new_share_date);
            $cal_day = $day_of_year-$diff_day;

            if ($val['share_type'] == "SRP" || $val['share_type'] == "RX" || $val['share_type'] == "RM") {
                if($switch_cal_dividend == '2'){
                    // สำนักงบ
                    $cal_dividend_month = $val['share_early_value'] * $dividend_percent * $list_month_cal[$listd[0]][$mo];
                    $cal_dividend_month = round($cal_dividend_month, 2);
                    $data1[$val['member_id']] -= $cal_dividend_month;
                }else{
                    // ตำรวจ
                    if(($cal_day>0)) {
                        $cal_dividend_month = $val['share_early_value'] * $dividend_percent * $cal_day / $day_of_year;
                        $cal_dividend_month = round($cal_dividend_month, 2);
                        $data1[$val['member_id']] -= $cal_dividend_month;
                    }
                }
            } else {
                if($switch_cal_dividend == '2') {
                    $cal_dividend_month = $val['share_early_value'] * $dividend_percent * $list_month_cal[$listd[0]][$mo];
                    $cal_dividend_month = round($cal_dividend_month, 2);
                    $data1[$val['member_id']] += $cal_dividend_month;
                }else{
                    if(($cal_day>0)) {
                        $cal_dividend_month = $val['share_early_value'] * $dividend_percent * $cal_day / $day_of_year;
                        $cal_dividend_month = round($cal_dividend_month, 2);
                        $data1[$val['member_id']] += $cal_dividend_month;
                    }
                }
            }

            if(isset($_GET['debug']) && isset($_GET['member_id']) && $val['member_id'] == $_GET['member_id']){
                echo ++$no. " :: ".$val['share_early_value'] * @$dividend_percent * @$list_month_cal[$listd[0]][$mo]." :: ".$val['share_early_value']." ".$dividend_percent." ".$list_month_cal[$listd[0]][$mo]." ".$listd[0]." ".$mo."<br>" ;
            }
        }
        unset($res);

        #หาดอกเบี้ยสะสม
        $z = 0;
        $sql = "SELECT member_id,SUM(return_amount) AS return_amount 
        FROM (
            SELECT t1.member_id,t1.interest AS `return_amount`,t1.payment_date AS `date` 
            FROM coop_finance_transaction as t1
            LEFT JOIN coop_receipt as t2 ON t1.receipt_id = t2.receipt_id
            LEFT JOIN coop_loan as t3 ON t1.loan_id = t3.id 
            WHERE t1.payment_date BETWEEN '" . $average_start. "' AND '" . $average_stop . "' AND t1.interest<> 0 AND (t2.receipt_status != '2' OR t2.receipt_status is null)
            AND t3.contract_number NOT LIKE '%/%'
            UNION ALL SELECT member_id,(return_principal-return_amount) AS return_amount,return_time AS `date` FROM coop_process_return WHERE return_time BETWEEN '" . $average_start. "' AND '" . $average_stop . "'
        ) T GROUP BY member_id";
        $rs = $this->db->query($sql);

        //echo $this->db->last_query(); exit;
        $row_transaction = $rs->result_array();
        $key_transaction = array();
        foreach ($row_transaction as $value) {
            if(empty($key_transaction[$value['member_id']])) {
                $key_transaction[$value['member_id']] = $value['return_amount'];
            }else{
                $key_transaction[$value['member_id']] += $value['return_amount'];
            }
        }
        unset($row_transaction);

        #หารายชื่อคน
        $this->db->select(
            array(
                "m.member_id AS member_id",
                "m.apply_type_id AS apply_type_id",
                "m.mem_type AS mem_type",
                "p.prename_full AS prename_full",
                "m.firstname_th AS firstname_th",
                "m.lastname_th AS lastname_th",
                "mg.mem_group_name AS mem_group_name",
                "mg.mem_group_id AS mem_group_id",
                "m.level",
                "re.info as receive_id",
                "re.name as receive_name"
            )
        );
        $this->db->from('coop_mem_apply m');
        $this->db->join('coop_mem_group mg', 'm.level = mg.id');
        $this->db->join('coop_prename p', 'm.prename_id = p.prename_id', 'left');
        $this->db->join('coop_mem_req_resign r', 'm.member_id = r.member_id', 'left');
        $this->db->join('coop_dividend_average_receive re', "m.average_receive = re.info AND re.group = 'P05'", 'left');
        $this->db->where(" mem_type=1 OR (mem_type=2 AND `r`.`resign_date` BETWEEN '".(date('Y').'-'.$setting_period.'-01')."' AND '".$period_last_month."')");

        $rs_mem = $this->db->get()->result_array();

        $res_men_key = array();
        foreach ($rs_mem as $value) {
            $res_men_key[$value['member_id']] = $value;
        }
        unset($rs_mem);

        //echo "<pre>";print_r($res_men_key);exit;

        $number = 0;
        $arr_list = [];
        foreach ($data1 as $indexx => $vall) {

            $return = $key_transaction[$indexx] * $average_percent;
            $gift = $gift_varchar;
            $vall = ROUND($vall);
            $return = ROUND($return);
            $sum_val = $vall + $return + $gift;

            if (!empty($res_men_key[$indexx]['prename_full']) && !isset($resolve[$indexx]) && $sum_val) {

                $arr_list[$number]['member_id'] = $indexx;
                $arr_list[$number]['master_id'] = $last_id;
                $arr_list[$number]['date_create'] = date('Y-m-d H:i:s');
                $arr_list[$number]['year'] = date('Y');
                $arr_list[$number]['dividend_percent'] = $_POST['dividend_percent1'];
                $arr_list[$number]['average_percent'] = $_POST['average_percent2'];
                $arr_list[$number]['dividend_value'] = $vall;
                $arr_list[$number]['average_return_value'] = round($return, 2);
                $arr_list[$number]['gift_varchar'] = $gift;
                $number++;
            }
        }
        unset($key_transaction, $key_row, $data1);

        $this->db->insert_batch('coop_dividend_average', $arr_list);


        $sum_divide         = array_sum(array_map(function($val){ return $val['dividend_value'];}, $arr_list));
        $sum_average_return = array_sum(array_map(function($val){ return $val['average_return_value'];}, $arr_list));

        $data_insert = array();

        $data_insert['dividend_percent']        = $_POST['dividend_percent1'];
        $data_insert['average_percent']         = $_POST['average_percent2'];
        $data_insert['gift_varchar']            = number_format($gift_varchar, 2, '.', '');
        $data_insert['dividend_value']          = number_format($sum_divide, 2, '.', '');
        $data_insert['average_return_value']    = number_format($sum_average_return, 2, ".", "");
        $data_insert['date_create']             = date('Y-m-d H:i:s');
        $this->db->where('id', $last_id);
        $this->db->update('coop_dividend_average_master', $data_insert);

        $this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
        //exit;
//        echo '<pre>';print_r($arr_list);exit;
        echo "<script>document.location.href='" . base_url(PROJECTPATH . '/average_dividend') . "'</script>";

    }

    function calculate_data()
    {

        $average_percent = ($_GET['average_percent'] / 100);
        $dividend_percent = ($_GET['dividend_percent'] / 100);
        $gift_varchar = $_GET['money_gift'];

        $member = str_pad($_GET['member_id'], 6,'0', STR_PAD_LEFT);

        $this->db->select('accm_month_ini');
        $account_period_setting = $this->db->get('coop_account_period_setting')->row();
        $setting_period =  $account_period_setting->accm_month_ini;
        if(empty($setting_period)){
            $setting_period = '11';
        }
        if($setting_period == '1'){
            $Minus_year = 1;
        }else{
            $Minus_year = 0;
        }
        $Minus_year = 0;
        $period_start = date( 'Y-m-t',strtotime((date('Y')-1-$Minus_year)."-".($setting_period-1)));
        $period_first_start = (date('Y')-1-$Minus_year)."-".$setting_period."-01";
        $period_second_start = (date("Y")-1-$Minus_year)."-".$setting_period."-01";
        $period_last_month = (date("Y")-$Minus_year)."-".($setting_period)."-30";
        $period_end = date('Y-m-t', strtotime((date('Y')-$Minus_year)."-".($setting_period-1)));
        $period_end_month = (date("Y")-$Minus_year)."-".($setting_period)."-30";

        $average_start =date('Y-m-d', strtotime((date('Y') - 1-$Minus_year)."-".$setting_period."-01"));
        $average_stop =date('Y-m-t', strtotime((date('Y')-$Minus_year)."-".($setting_period-1)));

        $datas = [];
        $this->db->select(array(
            't1.member_id', "t1.share_early_value", 't1.share_date', 't1.share_type'
        ));
        $this->db->from('coop_mem_share as t1');
        $this->db->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner");
        $this->db->where("t1.share_type in ('SPA','SPM', 'SRP','SB', 'MS', 'RX', 'RM','SDP')  and t1.share_date BETWEEN '" .$period_second_start."' AND '".$period_last_month."'  AND t1.share_early_value <> 0 AND t1.share_status in (1,5) AND t2.member_status != '2'");
        $this->db->order_by("member_id asc,share_date asc");
        $res = $this->db->get()->result_array();

        if(isset($_GET['debug']) && $_GET['section'] == 'share'){
            echo $this->db->last_query();
            echo "<br>";
            echo "<br>";
        }

        $stmt  = "SELECT a.member_id,m.share_collect_value AS `share_collect_value`,a.share_date FROM (
SELECT max(x.share_collect_value) AS share_collect_value,x.share_date,x.member_id FROM (
SELECT member_id,MAX(share_date) AS share_date FROM coop_mem_share WHERE share_date<='".$period_first_start."' AND share_status != '3' GROUP BY member_id) v INNER JOIN coop_mem_share x ON v.member_id=x.member_id AND v.share_date=x.share_date GROUP BY x.member_id) a INNER JOIN coop_mem_share m ON a.member_id=m.member_id AND a.share_date=m.share_date AND a.share_collect_value=m.share_collect_value INNER JOIN coop_mem_apply t3 ON a.member_id=t3.member_id AND t3.member_status != '2' HAVING share_collect_value> 0 ORDER BY member_id ASC";
        $result = $this->db->query($stmt)->result_array();
        $share_collect_value = [];
        foreach ($result as $key => $value){
            $share_collect_value[$value['member_id']] = $value['share_collect_value'];
        }
        unset($result);

        if(isset($_GET['debug']) && $_GET['section'] == 'share'){
            echo $this->db->last_query();
            echo "<br>";
            echo "<br>";
        }

        #หารายการที่มีหุ้นคงเหลือเป็น 0
        $stmt = "SELECT member_id, SUM(share_early_value) AS share_early_value FROM (
SELECT `member_id`,IF (`share_type`='SRP',`share_early_value`*-1,`share_early_value`) AS `share_early_value`,`share_date`,`share_type`,`share_status` FROM `coop_mem_share` WHERE `share_type` IN ('SPA','SPM', 'SRP','SB', 'MS', 'RX', 'RM','SDP') AND `share_date` BETWEEN '" . $period_start."' AND '" .$period_end."' AND `share_early_value`<> 0 AND `share_status` IN (1,5) ORDER BY `share_date` ASC) t1 GROUP BY member_id HAVING share_early_value <= 0";
        $unlike = $this->db->query($stmt)->result_array();

        $resolve = [];
        foreach ($unlike as $key => $val){
            $resolve[$val['member_id']] = $val['share_early_value'];
        }
        unset($unlike);


        if($_GET['debug'] == 'test'){
            echo $this->db->last_query();
        }

        $this->db->select('cal_day, switch_cal_dividend, switch_cal_average');
        $setting_dividend_average = $this->db->get('coop_setting_dividend_average')->row();
        $cal_day =  $setting_dividend_average->cal_day;
        $switch_cal_dividend =  $setting_dividend_average->switch_cal_dividend;
        if(empty($setting_period)){
            $cal_day = 365;
        }
        if(empty($setting_period)){
            $switch_cal_dividend = 2;
        }
        $switch_cal_dividend = 2;
        $list_month_cal = [];
        $a = $setting_period;
        $b = 1;
        $arr = [];
        $data_insert = [];
        $first_time = 1;
        for ($i = 12; $i >= 0; $i--) {
            if ($i >= $setting_period) {
                $list_month_cal[(date('Y') - 1)][sprintf('%02d', $a)] = ($i-1) / 12;
                $a++;
            } else {
                if($first_time == 1){
                    $first_time = 0;
                    $cut = 1;
                    if($setting_period == 1){
                        $cut++;
                        $i2 = 13;
                    }else{
                        $i2 = $setting_period;
                    }
                    $list_month_cal[(date('Y')-$cut)][sprintf('%02d', $i2-1)] = 1;
                }
                $list_month_cal[(date('Y') )][sprintf('%02d', $b)] = ($i-1) / 12;
                if($list_month_cal[(date('Y') )][sprintf('%02d', $b)] < 0){
                    $list_month_cal[(date('Y') )][sprintf('%02d', $b)] = 0;
                }
                $b++;
            }

        }

        #หาเงินปันผลในรอบปี
        $data1 = array(); $chk = [];

        $no = 1;
        foreach($share_collect_value as $index => $val ){
            $data1[$index] += $val * $dividend_percent * 1;
            if(isset($_GET['debug']) && isset($_GET['member_id']) && $index == $member){
                echo $no. " :: ". ($val * $dividend_percent * 1) ." :: ". $val." * ".$dividend_percent." * 1 <br>" ;
            }
        }
        unset($share_collect_value);


        foreach ($res as $index => $val) {

            $listd = explode("-", $val['share_date']);
            $dmz = intval($listd[2]);

            if ($dmz < 8) {

                $mo = str_pad(intval($listd[1]) - 1 , 2, "0", STR_PAD_LEFT);
                if($mo == "00"){
                    $mo = "12";
                    $listd[0] -= 1;
                }
            } else {
                $mo = str_pad(intval($listd[1]), 2, "0", STR_PAD_LEFT);
                if($mo == $setting_period){
//                    $mo -= 1;

                }
            }


//            if($list_month_cal[$listd[0]][$mo] == 1){
//                $val['share_early_value'] = $share_collect_value[$val['member_id']];
//            }
            $new_share_date = date("Y-m-d", strtotime($val['share_date']));
            $diff_day = $this->center_function->diff_day($period_first_start, $new_share_date);
            $cal_day = $day_of_year-$diff_day;
            if ($val['share_type'] == "SRP" || $val['share_type'] == "RX" || $val['share_type'] == "RM") {
                if($switch_cal_dividend == '2'){
                    // สำนักงบ
                    $cal_dividend_month = $val['share_early_value'] * $dividend_percent * $list_month_cal[$listd[0]][$mo];
                    $cal_dividend_month = round($cal_dividend_month, 2);
                    $data1[$val['member_id']] -= $cal_dividend_month;
                }else{
                    // ตำรวจ
                    if(($cal_day>0)) {
                        $cal_dividend_month = $val['share_early_value'] * $dividend_percent * $cal_day / $day_of_year;
                        $cal_dividend_month = round($cal_dividend_month, 2);
                        $data1[$val['member_id']] -= $cal_dividend_month;
                    }
                }
            } else {
                if($switch_cal_dividend == '2') {
                    $cal_dividend_month = $val['share_early_value'] * $dividend_percent * $list_month_cal[$listd[0]][$mo];
                    $cal_dividend_month = round($cal_dividend_month, 2);
                    $data1[$val['member_id']] += $cal_dividend_month;
                }else{
                    if(($cal_day>0)) {
                        $cal_dividend_month = $val['share_early_value'] * $dividend_percent * $cal_day / $day_of_year;
                        $cal_dividend_month = round($cal_dividend_month, 2);
                        $data1[$val['member_id']] += $cal_dividend_month;
                    }
                }
            }

            if(isset($_GET['debug']) && isset($_GET['member_id']) && $val['member_id'] == $member){
                if($switch_cal_dividend == '2') {
                    echo ++$no . " :: " . ROUND($val['share_early_value'] * @$dividend_percent * @$list_month_cal[$listd[0]][$mo], 2) . " :: " . $val['share_early_value'] . " " . $dividend_percent . " " . $list_month_cal[$listd[0]][$mo] . " " . $listd[0] . " " . $mo . " ". $dmz ."<br>";
                }else{
                    if(($cal_day>0)) {
                        echo ++$no . " :: " . $listd[0] . " " . $mo . " " . ($val['share_early_value'] * @$dividend_percent * $cal_day / $day_of_year) . " = " . $val['share_early_value'] . " * " . $dividend_percent . " * " . ($cal_day / $day_of_year) . "<br>";
                    }
                }
            }
        }
        unset($res);
//        exit;

        #หาดอกเบี้ยสะสม
        $z = 0;
        $sql = "SELECT member_id,SUM(return_amount) AS return_amount 
        FROM (
            SELECT t1.member_id,t1.interest AS `return_amount`,t1.payment_date AS `date` 
            FROM coop_finance_transaction as t1
            LEFT JOIN coop_receipt as t2 ON t1.receipt_id = t2.receipt_id
            LEFT JOIN coop_loan as t3 ON t1.loan_id = t3.id 
            WHERE t1.payment_date BETWEEN '" . $average_start. "' AND '" . $average_stop . "' AND t1.interest<> 0 AND (t2.receipt_status != '2' OR t2.receipt_status is null)
            AND t3.contract_number NOT LIKE '%/%'
            UNION ALL SELECT member_id,(return_principal-return_amount) AS return_amount,return_time AS `date` FROM coop_process_return WHERE return_time BETWEEN '" . $average_start. "' AND '" . $average_stop . "'
        ) T GROUP BY member_id";

        $rs = $this->db->query($sql);

        if( isset($_GET['debug']) && $_GET['section'] == "interest"){
            echo $this->db->last_query();
            echo "<br>";
            echo "<br>";
        }

        $row_transaction = $rs->result_array();
        $key_transaction = array();
        foreach ($row_transaction as $value) {
            if(empty($key_transaction[$value['member_id']])) {
                $key_transaction[$value['member_id']] = $value['return_amount'];
            }else{
                $key_transaction[$value['member_id']] += $value['return_amount'];
            }
        }
        unset($row_transaction);

        #หารายชื่อคน
        $this->db->select(
            array(
                "m.member_id AS member_id",
                "m.apply_type_id AS apply_type_id",
                "m.mem_type AS mem_type",
                "p.prename_full AS prename_full",
                "m.firstname_th AS firstname_th",
                "m.lastname_th AS lastname_th",
                "mg.mem_group_name AS mem_group_name",
                "mg.mem_group_id AS mem_group_id",
                "m.level",
                "re.info as receive_id",
                "re.name as receive_name"
            )
        );
        $this->db->from('coop_mem_apply m');
        $this->db->join('coop_mem_group mg', 'm.level = mg.id');
        $this->db->join('coop_prename p', 'm.prename_id = p.prename_id', 'left');
        $this->db->join('coop_mem_req_resign r', 'm.member_id = r.member_id', 'left');
        $this->db->join('coop_dividend_average_receive re', "m.average_receive = re.info AND re.group = 'P05'", 'left');
        $this->db->where(" mem_type=1 OR (mem_type=2 AND `r`.`resign_date` BETWEEN '".(date('Y').'-'.$setting_period.'-01')."' AND '".$period_last_month."') ");

        $rs_mem = $this->db->get()->result_array();

        if( isset($_GET['debug']) && $_GET['section'] == "member"){
            echo $this->db->last_query();
            exit;
        }

        $res_men_key = array();
        foreach ($rs_mem as $value) {
            $res_men_key[$value['member_id']] = $value;
        }
        unset($rs_mem);

        $arr_data = array();
        $arr_data['minus_year'] = $Minus_year;
        $status = '0';
        if($status == '1') {
            $sql = "SELECT * FROM `sotr`.`allowance` WHERE `YEAR` LIKE '%2563%'";
            $rs = $this->db->query($sql);
            $row_allowance = $rs->result_array();
            $chack_allowance = array();
            foreach ($row_allowance as $key => $item) {
                $member_id = substr($item['ID'], -5);
                $chack_allowance[$member_id]['AW1'] = $item['AW1'];
                $chack_allowance[$member_id]['AW2'] = $item['AW2'];
            }
        }
        foreach ($data1 as $indexx => $vall) {
            $return = $key_transaction[$indexx] * $average_percent;
            $gift = $gift_varchar;
            $vall = ROUND($vall);
            $return = ROUND($return);
            $sum_val = $vall + $return + $gift;

            if(isset($_GET['debug']) && isset($_GET['member_id']) && $indexx == $member) {
                echo " sum_val  = ".$sum_val. " ::: member = ". (isset($res_men_key[$indexx]) ? "true" : "false") . " ::: resolve = ". (isset($resolve[$indexx]) == false ? "true" : "false")  .'  member_id :: '.$indexx."<br>";
            }

            if (isset($res_men_key[$indexx]) && !isset($resolve[$indexx]) && $sum_val) {

//                if(!isset($_GET['debug'])) {
//                    $gift = $res_men_key[$indexx]['apply_type_id'] == '1' ? $gift_varchar : 0;
//                }

                $arr_list['member_id']          = $indexx;
                $arr_list['master_id']          = "";
                $arr_list['prename_full']       = $res_men_key[$indexx]['prename_full'];
                $arr_list['firstname_th']       = $res_men_key[$indexx]['firstname_th'];
                $arr_list['lastname_th']        = $res_men_key[$indexx]['lastname_th'];
                $arr_list['prename_full']       = $res_men_key[$indexx]['prename_full'];
                $arr_list['mem_group_name']     = $res_men_key[$indexx]['mem_group_name'];
                $arr_list['mem_group_id']       = $res_men_key[$indexx]['mem_group_id'];
                $arr_list['receive_id']         = $res_men_key[$indexx]['receive_id'];
                $arr_list['receive_name']       = $res_men_key[$indexx]['receive_name'];
                $arr_list['divide_percent']     = $_GET['dividend_percent'];
                $arr_list['return_percent']     = $_GET['average_percent'];
                $arr_list['date_create']        = date('Y-m-d H:i:s');
                $arr_list['year']               = date('Y');
                $arr_list['sum_dividend']       = $vall;
                $arr_list['sum_return']         = $return;
                if($status == '1') {
                    $arr_list['check_dividend'] = $chack_allowance[$indexx]['AW1'];
                    $arr_list['check_return']   = $chack_allowance[$indexx]['AW2'];
                }
                $arr_list['gift_varchar']       = $gift;
                $arr_list['sum_divide_return']  = round($vall + $return + $gift, 2);
                $arr_data['data'][]             = $arr_list;
            }
            $z++;
        }
        unset($key_transaction, $key_row, $data1);

        $arr_data['month_arr'] = array('1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม');
        $arr_data['month_short_arr'] = array('1' => 'ม.ค.', '2' => 'ก.พ.', '3' => 'มี.ค.', '4' => 'เม.ย.', '5' => 'พ.ค.', '6' => 'มิ.ย.', '7' => 'ก.ค.', '8' => 'ส.ค.', '9' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.');

//        echo '<pre>';print_r($arr_data);exit;
        $this->load->view('average_dividend/average_dividend_excel', $arr_data);

    }

    public function average_dividend_excel_tests($data = array())
    {

        $arr_data = array();

        $arr_data['month_arr'] = array('1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม');
        $arr_data['month_short_arr'] = array('1' => 'ม.ค.', '2' => 'ก.พ.', '3' => 'มี.ค.', '4' => 'เม.ย.', '5' => 'พ.ค.', '6' => 'มิ.ย.', '7' => 'ก.ค.', '8' => 'ส.ค.', '9' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.');
        $arr_data['data'] = $data;

        $this->load->view('average_dividend/average_dividend_excel', $arr_data);
    }

    function average_dividend_excel()
    {
        $arr_data = array();

        if(!empty($_GET['year'])){
            $year = $_GET['year'];
        }else{
            $year = date('Y');
        }

        $arr_data['month_arr'] = array('1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม');
        $arr_data['month_short_arr'] = array('1' => 'ม.ค.', '2' => 'ก.พ.', '3' => 'มี.ค.', '4' => 'เม.ย.', '5' => 'พ.ค.', '6' => 'มิ.ย.', '7' => 'ก.ค.', '8' => 'ส.ค.', '9' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.');

        if(isset($_GET['type']) && $_GET['type'] == 'transfer'){
            $cond = " AND ( `M`.`transfer_date` IS NOT NULL OR `M`.`transfer_gift_date` IS NOT NULL) ";
        }else if(isset($_GET['type']) && $_GET['type'] == 'no_transfer'){
            $cond = " AND ( `M`.`transfer_date` IS NULL OR `M`.`transfer_gift_date` IS NULL) ";
        }else{
            $cond = "";
        }

        $stmt = "SELECT `M`.`master_id`, `M`.`transfer_date`, `M`.`transfer_gift_date`, `M`.`member_id`,`prename_full`,`firstname_th`,`lastname_th`,`mem_group_name`,`mem_group_id`,`sum_dividend`,`sum_return`,`gift_varchar`,`divide_percent`,`return_percent`,`sum_divide_return`,`year`,`1`,`2`,`3`,`4`,`5`,`6`,`7`,`8`,`9` FROM `coop_average_dividend_excel2` M LEFT JOIN (SELECT master_id,member_id,SUM(IF (deduct_id=1,amount,0)) AS `1`,SUM(IF (deduct_id=2,amount,0)) AS `2`,SUM(IF (deduct_id=3,amount,0)) AS `3`,SUM(IF (deduct_id=4,amount,0)) AS `4`,SUM(IF (deduct_id=5,amount,0)) AS `5`,SUM(IF (deduct_id=6,amount,0)) AS `6`,SUM(IF (deduct_id=7,amount,0)) AS `7`,SUM(IF (deduct_id=8,amount,0)) AS `8`,SUM(IF (deduct_id=9,amount,0)) AS `9` FROM (SELECT `master_id`,`member_id`,`deduct`.`deduct_id`,`amount`,deduct_name FROM coop_dividend_deduct `deduct` INNER JOIN coop_dividend_deduct_type `deduct_type` ON `deduct`.deduct_id=`deduct_type`.deduct_id) T GROUP BY member_id) N ON M.master_id=N.master_id AND M.member_id=N.member_id WHERE `M`.`master_id` = '".$_GET['master_id']."' AND `M`.`year` = '".$year."' ".$cond;
        $query = $this->db->query($stmt);

        $arr_data['data'] = $query->result_array();

        $this->db->select('*');
        $this->db->from('coop_dividend_average_master');
        $this->db->where(array('id' => $_GET['master_id'], 'year' => $_GET['year']));
        $result = $this->db->get()->row();

        $arr_data['approve_date'] = $result->apporve_date;


        $this->db->select(array('deduct_id', 'deduct_name'));
        $this->db->from('coop_dividend_deduct_type');
        $this->db->where('deduct_status = 1');
        $res = $this->db->get()->result_array();
        $type = [];
        foreach ($res as $key => $val){
            $type[$val['deduct_id']] =  $val['deduct_name'];
        }
        $arr_data['type'] = $type;
        unset($type);

//        echo '<pre>'; print_r($arr_data);exit;
        $this->load->view('average_dividend/average_dividend_excel', $arr_data);
    }

    function approve()
    {
        $arr_data = array();

        $this->db->select(array(
            'id',
            'year',
            'dividend_percent',
            'average_percent',
            'dividend_value',
            'average_return_value',
            'gift_varchar',
            'status',
            'approve_date'
        ));
        $this->db->from('coop_dividend_average_master');
        $this->db->order_by("id DESC");
        $row = $this->db->get()->result_array();
        $arr_data['data'] = @$row;

        $arr_data['month_short_arr'] = array('1' => 'ม.ค.', '2' => 'ก.พ.', '3' => 'มี.ค.', '4' => 'เม.ย.', '5' => 'พ.ค.', '6' => 'มิ.ย.', '7' => 'ก.ค.', '8' => 'ส.ค.', '9' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.');


        $this->libraries->template('average_dividend/approve', $arr_data);
    }

    function status_to(){

        if (@$_POST) {

            $status = false;

            #โอนเงินเข้าบัญชีสหกรณ์ บัญชี 21 ของสมาชิก
            if (@$_POST['status_to'] == '1') {

                //หนวงเวลากันกดซ้ำ
                usleep(500);

                $this->db->select('*');
                $this->db->from('coop_dividend_average_master');
                $this->db->where(array('id' => $_POST['id']));
                $chk = $this->db->get()->row();

                $this->db->select('*');
                $this->db->from('coop_account_transaction');
                $this->db->where(" transaction_time between '".date('Y-m-d '). " 00:00:00' and '".date('Y-m-d')." 23:59:59' AND transaction_list = 'YPF' ");
                $count_chk = $this->db->get()->num_rows();

                if((isset($chk) && $chk->status == '3') || (isset($count_chk) && ($count_chk > 1))) {
                    header("Content-type: application/json");
                    echo json_encode(array('status' => false, 'msg' => 'padding'));
                    exit;
                }

                #อัพเดทสถานะการอนุมัติปันผลเฉลี่ยคืน
                $data_insert = array();
                $data_insert['status'] = 3; //status pending
                $this->db->where('id', @$_POST['id']);
                $this->db->update('coop_dividend_average_master', $data_insert);

                if(isset($chk) &&$chk->status == '0') {

                    $stmt = "SELECT m.id, m.member_id,cast(((`m`.`dividend_value`+`m`.`average_return_value`)-IF(`deduct`.`amount` IS NULL, 0, `deduct`.`amount`)) AS DECIMAL (18,2)) AS `dividend_value`,a.account_id,b.transaction_id,b.transaction_balance, IF(t.ignore_return = 0, NULL, t.ignore_return) as `ignore_return` FROM coop_dividend_average m INNER JOIN coop_maco_account a ON m.member_id=a.mem_id AND a.account_status='0' LEFT JOIN coop_dividend_ignore_transfer t ON m.master_id=t.master_id AND m.member_id=t.member_id LEFT JOIN (
SELECT `member_id`,`master_id`,ROUND(SUM(amount),2) AS `amount` FROM coop_dividend_deduct GROUP BY member_id) `deduct` ON `m`.`master_id`=`deduct`.`master_id` AND `m`.`member_id`=`deduct`.`member_id` LEFT JOIN (SELECT t1.*FROM coop_account_transaction t1 INNER JOIN (SELECT account_id,max(transaction_id) AS transaction_id FROM coop_account_transaction GROUP BY account_id) t2 ON t1.transaction_id=t2.transaction_id) b ON a.account_id=b.account_id WHERE m.master_id='{$_POST['id']}' AND a.type_id=2 AND dividend_value IS NOT NULL AND dividend_value > 0";
                    $rs = $this->db->query($stmt)->result_array();

                    $data_insert = array();
                    $number = 0;
                    $confirm = [];
                    foreach ($rs as $key => $row) {

                        if ($row['ignore_return'] <> '1') {

                            $timestamp = date('Y-m-d H:i:s');
                            $transaction_balance = @$row['transaction_balance'] + $row['dividend_value'];
                            $data_insert[$number]['transaction_time'] = $timestamp;
                            $data_insert[$number]['transaction_list'] = 'YPF';
                            $data_insert[$number]['transaction_withdrawal'] = '0';
                            $data_insert[$number]['transaction_deposit'] = @$row['dividend_value'];
                            $data_insert[$number]['transaction_balance'] = @$transaction_balance;
                            $data_insert[$number]['user_id'] = @$_SESSION['USER_ID'];
                            $data_insert[$number]['account_id'] = @$row['account_id'];

                            $confirm[$number]['id'] = $row['id'];
                            $confirm[$number]['transfer_date'] = $timestamp;

                            $number++;

                        }

                    }
                    if (sizeof($data_insert)) {
                        $this->db->insert_batch('coop_account_transaction', $data_insert);
                    }

                    if(sizeof($confirm)){
                        $this->db->update_batch('coop_dividend_average', $confirm, 'id');
                    }

                    $stmt = "SELECT m.id, m.member_id,cast(`m`.`gift_varchar` AS DECIMAL (18,2)) AS `gift_varchar`,a.account_id,b.transaction_id,b.transaction_balance,IF(t.ignore_gift = 0, NULL, t.ignore_gift) as `ignore_gift` FROM coop_dividend_average m INNER JOIN coop_maco_account a ON m.member_id=a.mem_id AND a.account_status='0' LEFT JOIN coop_dividend_ignore_transfer t ON m.master_id=t.master_id AND m.member_id=t.member_id LEFT JOIN (
SELECT `member_id`,`master_id`,ROUND(SUM(amount),2) AS `amount` FROM coop_dividend_deduct GROUP BY member_id) `deduct` ON `m`.`master_id`=`deduct`.`master_id` AND `m`.`member_id`=`deduct`.`member_id` LEFT JOIN (SELECT t1.*FROM coop_account_transaction t1 INNER JOIN (SELECT account_id,max(transaction_id) AS transaction_id FROM coop_account_transaction GROUP BY account_id) t2 ON t1.transaction_id=t2.transaction_id) b ON a.account_id=b.account_id WHERE m.master_id='{$_POST['id']}' AND a.type_id=2 AND gift_varchar IS NOT NULL AND gift_varchar > 0";
                    $rs = $this->db->query($stmt)->result_array();

                    $data_insert = [];
                    $confirm = [];
                    $number = 0;
                    foreach ($rs as $key => $row) {

                        if ($row['ignore_gift'] != '1') {

                            $timestamp =  date('Y-m-d H:i:s');

                            $transaction_balance = @$row['transaction_balance'] + $row['gift_varchar'];
                            $data_insert[$number]['transaction_time'] = $timestamp;
                            $data_insert[$number]['transaction_list'] = 'YPF';
                            $data_insert[$number]['transaction_withdrawal'] = '0';
                            $data_insert[$number]['transaction_deposit'] = @$row['gift_varchar'];
                            $data_insert[$number]['transaction_balance'] = @$transaction_balance;
                            $data_insert[$number]['user_id'] = @$_SESSION['USER_ID'];
                            $data_insert[$number]['account_id'] = @$row['account_id'];

                            $confirm[$number]['id'] = $row['id'];
                            $confirm[$number]['transfer_gift_date'] = $timestamp;

                            $number++;

                        }

                    }
                    if (sizeof($data_insert)) {
                        $this->db->insert_batch('coop_account_transaction', $data_insert);
                    }

                    if($confirm){
                        $this->db->update_batch('coop_dividend_average', $confirm, 'id');
                    }

                    $this->db->select(array(
                        'dividend_value',
                        'average_return_value',
                        'gift_varchar',
                        'year'
                    ));
                    $this->db->from('coop_dividend_average_master');
                    $this->db->where("id = '" . $_POST['id'] . "'");
                    $row = $this->db->get()->result_array();
                    $row = @$row[0];


                    $data['coop_account']['account_description'] = "โอนเงินปันผลและเฉลี่ยคืนให้สมาชิก";
                    $data['coop_account']['account_datetime'] = date('Y-m-d H:i:s');
                    $i = 0;
                    $data['coop_account_detail'][$i]['account_type'] = 'debit';
                    $data['coop_account_detail'][$i]['account_amount'] = ($row['dividend_value'] + $row['average_return_value']);
                    $data['coop_account_detail'][$i]['account_chart_id'] = '50700';
                    $i++;
                    $data['coop_account_detail'][$i]['account_type'] = 'credit';
                    $data['coop_account_detail'][$i]['account_amount'] = ($row['dividend_value'] + $row['average_return_value']);
                    $data['coop_account_detail'][$i]['account_chart_id'] = '20100';

                    $this->account_transaction->account_process($data);

                    $data['coop_account']['account_description'] = "โอนเงินของขวัญปีใหม่ให้สมาชิก";
                    $data['coop_account']['account_datetime'] = date('Y-m-d H:i:s');
                    $i = 0;
                    $data['coop_account_detail'][$i]['account_type'] = 'debit';
                    $data['coop_account_detail'][$i]['account_amount'] = ($row['gift_varchar']);
                    $data['coop_account_detail'][$i]['account_chart_id'] = '50700';
                    $i++;
                    $data['coop_account_detail'][$i]['account_type'] = 'credit';
                    $data['coop_account_detail'][$i]['account_amount'] = ($row['gift_varchar']);
                    $data['coop_account_detail'][$i]['account_chart_id'] = '20100';

                    $this->account_transaction->account_process($data);

                    $this->db->where(array('status' => '0', 'year' => @$row['year']));
                    $this->db->update('coop_dividend_average_master', array('status' => '2'));

                    #อัพเดทสถานะการอนุมัติปันผลเฉลี่ยคืน
                    $data_insert = array();
                    $data_insert['status'] = 1;
                    $data_insert['approve_date'] = date("Y-m-d H:i:s");
                    $this->db->where('id', @$_POST['id']);
                    $this->db->update('coop_dividend_average_master', $data_insert);

                    $this->add_receipt();
                }
            }

            #อัพเดทสถานะการอนุมัติปันผลเฉลี่ยคืน
            $data_insert = array();
            $data_insert['status'] = @$_POST['status_to'];
            $this->db->where('id', @$_POST['id']);
            $this->db->update('coop_dividend_average_master', $data_insert);

            $status = true;
            header("Content-type: application/json");
            echo json_encode(array('status' => $status));
            exit;
            //$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
            //echo "<script>document.location.href='" . base_url(PROJECTPATH . '/average_dividend/approve') . "'</script>";
        }
    }

    public function add_receipt(){

        if(@$_POST) {

            $this->db->select(array('*'))->from('coop_dividend_average_receipt')->where('master_id', $_POST['id']);
            $chk = $this->db->get()->num_rows() === 0;

            if ($chk) {

                #ใบเสร้จรับเงิน
                $yymm = (date("Y") + 543) . date("m");
                $mm = date("m");
                $yy = (date("Y") + 543);
                $yy_full = (date("Y") + 543);
                $yy = substr($yy, 2);
                $this->db->select('*');
                $this->db->from('coop_receipt');
                $this->db->where("receipt_id LIKE '" . $yy_full . $mm . "%'");
                $this->db->order_by("receipt_id DESC");
                $this->db->limit(1);
                $row = $this->db->get()->result_array();

                $id = (int)substr($row[0]["receipt_id"], 6); //เลขที่ใบเสร็จล่าสุด

                $this->db->select(array('`member_id`', ' sum(`amount`) as `sumcount` '));
                $this->db->from('coop_dividend_deduct');
                $this->db->where('master_id', $_POST['id']);
                $this->db->group_by('member_id');
                $cons_men = $this->db->get()->result_array();

                //echo $this->db->last_query(); exit;

                $member_receipt = [];
                $ins_coop_recipt = [];
                $row = 0;
                foreach ($cons_men as $index => $val) {

                    //เลขที่ใบเสร็จล่าสุด
                    $rep = $receipt_number = $yymm . sprintf("%06d", ++$id);

                    $member_receipt[$val['member_id']] = $rep;

                    $ins_coop_recipt[$row]['member_id'] = $val['member_id'];
                    $ins_coop_recipt[$row]['sumcount'] = $val['sumcount'];
                    $ins_coop_recipt[$row]['receipt_id'] = $rep;
                    $ins_coop_recipt[$row]['receipt_datetime'] = date('Y') == 2018 ? '2018-12-23 18:12:56' :  date('Y-m-d H:i:s');
                    $ins_coop_recipt[$row]['admin_id'] = $_SESSION['USER_ID'];
                    $ins_coop_recipt[$row]['pay_type'] = "1";
                    $row++;

                }

                if (isset($ins_coop_recipt) && sizeof($ins_coop_recipt)) {
                    $this->db->insert_batch('coop_receipt', $ins_coop_recipt);
                }
                unset($ins_coop_recipt);

                $this->db->select(array('a.member_id', 'b.deduct_name', 'a.amount', 'b.account_list_id'));
                $this->db->from('coop_dividend_deduct a');
                $this->db->join('coop_dividend_deduct_type b', 'a.deduct_id = b.deduct_id', 'inner');
                $this->db->where(array('master_id' => $_POST['id']));
                $this->db->order_by('a.member_id');
                $dec = $this->db->get()->result_array();
                $dedect = [];
                foreach ($dec as $index => $value) {
                    $dedect[$value['member_id']][] = $value;
                }
                unset($dec);

                $receipts = [];
                $num = 0;
                $rows = 0;
                $receipt_deteil = [];
                $dedects = [];
                foreach ($member_receipt as $member => $receipt) {

                    foreach ($dedect[$member] as $index => $value) {

                        $receipts[$num]['member_id'] = $value['member_id'];
                        $receipts[$num]['receipt_id'] = $receipt;
                        $receipts[$num]['principal_payment'] = round($value['amount'], 2);
                        $receipts[$num]['total_amount'] = round($value['amount'], 2);
                        $receipts[$num]['payment_date'] = date('Y') == 2018 ? '2018-12-23 18:12:56' : date('Y-m-d');
                        $receipts[$num]['createdatetime'] = date('Y') == 2018 ? '2018-12-23 18:12:56' :  date('Y-m-d H:i:s');
                        $receipts[$num]['account_list_id'] = $value['account_list_id'];
                        $receipts[$num]['transaction_text'] = $value['deduct_name'];

                        $receipt_deteil[$num]['receipt_id'] = $receipt;
                        $receipt_deteil[$num]['receipt_list'] = $value['account_list_id'];
                        $receipt_deteil[$num]['receipt_count'] = round($value['amount'], 2);

                        $num++;
                    }
                    $deducts[$rows]['member_id'] = $value['member_id'];
                    $deducts[$rows]['master_id'] = $_POST['id'];
                    $deducts[$rows]['create_date'] = date('Y') == 2018 ? '2018-12-23 18:12:56' :  date('Y-m-d H:i:s');
                    $deducts[$rows]['receipt_id'] = $receipt;
                    $rows++;
                }

                if (isset($receipts) && sizeof($receipts)) {
                    $this->db->insert_batch('coop_finance_transaction', $receipts);
                }

                if (isset($receipt_deteil) && sizeof($receipt_deteil)) {
                    $this->db->insert_batch('coop_receipt_detail', $receipt_deteil);
                }

                if (isset($deducts) && sizeof($deducts)) {
                    $this->db->insert_batch('coop_dividend_average_receipt', $deducts);
                }

                header("Content-Type: application/json");
                echo json_encode(array('status' => true, 'msg' => 'successfuly'));
                exit;
            }
        }else{
            header("Content-Type: application/json");
            echo json_encode(array('status' => false, 'msg' => 'pendding'));
            exit;
        }

        header("Content-Type: application/json");
        echo json_encode(array('status' => false, 'msg' => 'failure'));
        exit;
    }

    public function reset_receipt(){
        $this->db->select('receipt_id')->from('coop_dividend_average_receipt')->where(array('member_id' => '1'));
        $res = $this->db->get()->result_array();

        $receipt = [];
        foreach ($res as $key => $val){
            $receipt[] = $val['receipt_id'];
        }

        if(!empty($receipt)){

            $this->db->where_in('receipt_id', $receipt);
            $this->db->delete('coop_receipt_detail');

            $this->db->where_in('receipt_id', $receipt);
            $this->db->delete('coop_finance_transaction');

            $this->db->where_in('receipt_id', $receipt);
            $this->db->delete('coop_receipt');

            $this->db->where_in('receipt_id', $receipt);
            $this->db->delete('coop_dividend_average_receipt');
        }
        echo "success";
        exit;
    }

    public function deposit(){
        if(@$_POST){

            $this->db->select('*')->from('coop_dividend_mng_process')
            ->where(array('process' => 'deposite', 'master_id' => $_POST['id']));
            $chk = $this->db->get()->row();

            if($chk->status <> '1') {

                $checker = [
                  'process' => 'deposite',
                  'status' => '1',
                  'master_id' =>  $_POST['id']
                ];
                $this->db->insert('coop_dividend_mng_process', $checker);


                $stmt = "SELECT A.id,A.member_id,A.type_id,B.account_id,amount AS transaction_deposit,transaction_balance AS transaction_balance_previous,cast((IFNULL(transaction_balance,0)+IFNULL(amount,0)) AS DECIMAL (18,2)) AS transaction_balance FROM (SELECT*,IF (deduct_id=4,5,IF (deduct_id=3,6,IF (deduct_id=2,7,NULL))) AS type_id FROM coop_dividend_deduct WHERE deduct_id IN (2,3,4)) A LEFT JOIN coop_maco_account B ON A.member_id=B.mem_id AND A.type_id=B.type_id LEFT JOIN ( SELECT A.* FROM coop_account_transaction A INNER JOIN (SELECT MAX(transaction_time) transaction_time,account_id,transaction_balance FROM coop_account_transaction GROUP BY account_id) B ON A.account_id=B.account_id AND A.transaction_time=B.transaction_time GROUP BY A.account_id ) C ON B.account_id=C.account_id WHERE A.master_id = '" . $_POST['id'] . "' AND B.account_id IS NOT NULL AND B.account_status <> 1 GROUP BY A.member_id,type_id ORDER BY type_id,member_id";

                $result = $this->db->query($stmt)->result_array();

                $confirm = [];
                $data_insert = [];
                $number = 0;

                foreach ($result as $key => $row) {

                    $timestamp = date('Y-m-d H:i:s');
                    $data_insert[$number]['transaction_time'] = $timestamp;
                    $data_insert[$number]['transaction_list'] = 'YPF';
                    $data_insert[$number]['transaction_withdrawal'] = '0';
                    $data_insert[$number]['account_id'] = @$row['account_id'];
                    $data_insert[$number]['transaction_deposit'] = @$row['transaction_deposit'];
                    $data_insert[$number]['transaction_balance'] = @$row['transaction_balance'];
                    $data_insert[$number]['user_id'] = @$_SESSION['USER_ID'];

                    $confirm[$number]['id'] = $row['id'];
                    $confirm[$number]['transfer_date'] = $timestamp;

                    $number++;
                }

                if (sizeof($data_insert)) {
                    $this->db->insert_batch('coop_account_transaction', $data_insert);
                }

                if (sizeof($confirm)) {
                    $this->db->update_batch('coop_dividend_deduct', $confirm, 'id');
                }

                header("Content-Type: application/json");
                echo json_encode(array('status' => true, 'msg' => 'successfuly'));
                exit;
            }

            header("Content-Type: application/json");
            echo json_encode(array('status' => false, 'msg' => 'pending'));
            exit;

        }
        header("Content-Type: application/json");
        echo json_encode(array('status' => false, 'msg' => 'failure'));
        exit;
    }

    public function receipt(){


        $this->load->model('Memgroup_model');

        $master_id      = $_GET['id'];
        $limit          = 100;
        $page           = isset($_GET['page']) ? ($_GET['page'] - 1 ) : 0 ;
        $member_id      = isset($_GET['member_id']) && !empty($_GET['member_id']) ? str_pad($_GET['member_id'], 6, '0', STR_PAD_LEFT) : "";

        $stmt = "SELECT `deduct`.member_id,`deduct`.`deduct_id`,`deduct`.`amount`,`receipt`.`receipt_id` FROM coop_dividend_deduct `deduct` INNER JOIN coop_dividend_average_receipt `receipt` ON `deduct`.`member_id`=`receipt`.`member_id` WHERE `deduct`.master_id='".$master_id."' GROUP BY member_id ORDER BY `deduct`.member_id";
        $count = $this->db->query($stmt)->num_rows();

        $list = [];
        for($number = 1; $number <= ($count/$limit) + 1; $number++){

            $list[($number - 1)]['page'] = $number;
            if($number > 1){
                $txt    = ($number-1) * $limit + 1;
            }else{
                $txt    = $number;
            }

            $stmt_loop = "SELECT min(member_id) as `min`, max(member_id) as `max` FROM (".$stmt." LIMIT {$limit} OFFSET ".(($number-1) * $limit)." ) T ";
            $p = $this->db->query($stmt_loop)->row();

            if(($p->max - $p->min) < $limit){
                $min_limit = $txt + ($p->max - $p->min);
            }else{
                $min_limit = $number * $limit;
            }

            $list[($number - 1)]['text'] = $txt." - ".($min_limit)." (รหัส ".$p->min." - ".$p->max.") ";

        }
        $arr_data['page_numbers'] = $list;

        $_limit = "";
        if(isset($page)) {
            $_limit = "LIMIT " . $limit . " OFFSET " . ($page * $limit);
        }

        $cond = "";
        if(!empty($member_id)){
            $cond = "AND m.member_id = '{$member_id}' ";
        }

        $arr_data['member_id'] = $member_id;
        $arr_data['master_id'] = $master_id;

        // get all department
        $arr_data['departments'] = $this->Memgroup_model->get_department_all();


        if(isset($_GET['type']) && $_GET['type'] == 'copy'){
            $this->libraries->template('average_dividend/receipt_copy', $arr_data);
        }else {
            $this->libraries->template('average_dividend/receipt', $arr_data);
        }
    }

    public function find_receipt_mem_group(){

        $arr_data = array();
        $this->db->select('id, mem_group_name');
        $this->db->from('coop_mem_group');
        $this->db->where("mem_group_parent_id = '".$this->input->post('mem_group_id')."'");
        $this->db->order_by('mem_group_id');
        $row = $this->db->get()->result_array();
        $arr_data['mem_group'] = $row;

        $this->db->select('accm_month_ini');
        $account_period_setting = $this->db->get('coop_account_period_setting')->row();
        $setting_period =  $account_period_setting->accm_month_ini;

        $average_start = date('Y-m-d', strtotime((date('Y') - 1)."-".$setting_period."-01"));
        $average_stop = date('Y-m-t', strtotime(date('Y')."-".($setting_period-1)));

        $limit = 100;

        $master_id = $this->input->post('id');
        $section = $this->input->post('section');
        $mem_group_id = $this->input->post('mem_group_id');


        $stmt = "SELECT `deduct`.member_id,`deduct`.`deduct_id`,`deduct`.`amount`,`receipt`.`receipt_id` FROM coop_dividend_deduct `deduct` INNER JOIN coop_dividend_average_receipt `receipt` ON `deduct`.`member_id`=`receipt`.`member_id` INNER JOIN (SELECT t3.member_id,IF (t4.faction_old IS NULL,t3.faction,t4.faction_old) AS faction,IF (t4.level_old IS NULL,t3.LEVEL,t4.level_old) AS LEVEL,t3.firstname_th,t3.lastname_th,t3.prename_id,IF (t4.department_old IS NULL,t3.department,t4.department_old) AS department FROM coop_mem_apply AS t3 LEFT JOIN (SELECT member_id,department_old,faction_old,level_old,date_move FROM coop_mem_group_move WHERE date_move BETWEEN {$average_start} AND {$average_stop} GROUP BY member_id ORDER BY date_move ASC) AS t4 ON t3.member_id=t4.member_id WHERE 1=1 AND member_status<> 3) AS `mem_apply` ON `deduct`.member_id=`mem_apply`.member_id INNER JOIN coop_mem_group AS `group` ON `mem_apply`.{$section} =`group`.`id` WHERE `deduct`.master_id= {$master_id} AND `group`.id= {$mem_group_id} GROUP BY member_id ORDER BY `deduct`.member_id";

        $count = $this->db->query($stmt)->num_rows();



        if($count === 0){
            $arr_data['page_numbers'][0] = array('text' => " ไม่มีสมาชิก " );
        }else if($count == 1) {
            $stmt_loop = "SELECT min(member_id) as `min`, max(member_id) as `max` FROM (" . $stmt . " LIMIT 1 ) T ";
            $p = $this->db->query($stmt_loop)->row();

            $arr_data['page_numbers'][0] = array('text' => "1 (รหัส " . $p->min . ") " );

        }else{


            $inc = 0;
            $list = [];

            for ($number = 1; $number <= ($count / $limit) + 1; $number++) {

                $list[($number - 1)]['page'] = $number;
                if ($number > 1) {
                    $txt = ($number - 1) * $limit + 1;
                } else {
                    $txt = $number;
                }

                $stmt_loop = "SELECT min(member_id) as `min`, max(member_id) as `max` FROM (" . $stmt . " LIMIT {$limit} OFFSET " . (($number - 1) * $limit) . " ) T ";
                $p = $this->db->query($stmt_loop)->row();

                $inc += $limit;
                if ($inc > $count) {
                    $min_limit = $count;
                } else if (($p->max - $p->min) < $limit) {
                    $min_limit = $txt + ($p->max - $p->min);
                } else {
                    $min_limit = $number * $limit;
                }

                $list[($number - 1)]['text'] = $txt . " - " . ($min_limit) . " (รหัส " . $p->min . " - " . $p->max . ") ";

            }
            $arr_data['page_numbers'] = $list;
        }

        header('Content-Type: application/json; Charset: utf-8;');
        echo json_encode($arr_data);
        exit;
    }

    public function reset(){
        $date = date("Y-m-d");
        if($_SERVER['SERVER_NAME'] == 'localhost') {

            $this->db->where(array('transaction_time >=' => $date." 00:00:00", 'transaction_list' => 'YPF'));
            $this->db->delete('coop_account_transaction');

            $this->db->set('status', 0);
            $this->db->where('id', 1);
            $this->db->update('coop_dividend_average_master');

            $this->db->set('status', 0);
            $this->db->where( array('master_id' => 1, 'process' => 'deposite'));
            $this->db->update('coop_dividend_mng_process');

            $this->db->set(array('transfer_date' => null));
            $this->db->where('master_id', 1);
            $this->db->update('coop_dividend_deduct');

            $this->db->set(array('transfer_date' => null, 'transfer_gift_date' => null));
            $this->db->where('master_id', 1);
            $this->db->update('coop_dividend_average');

            echo 'Successfuly';
            exit;
        }
    }

    public function insure_transfer(){

        $stmt = "SELECT `a`.`member_id`,concat(ifnull(c.prename_full,''),b.firstname_th,' ',b.lastname_th) AS `fullname`,a.deduct_id,a.amount,a.transfer_date FROM `coop_dividend_deduct` `a` LEFT JOIN `coop_mem_apply` `b` ON `a`.`member_id`=`b`.`member_id` LEFT JOIN `coop_prename` `c` ON `b`.`prename_id`=`c`.`prename_id` WHERE `a`.`master_id`='".$_GET['id']."' AND `a`.`deduct_id` in (2,3,4) GROUP BY `a`.`member_id`, `a`.`deduct_id` ORDER BY `a`.`member_id`";

        $res = $this->db->query($stmt)->result_array();

        $data = [];
        foreach ($res as $key => $val){
            $data[$val['member_id']]['member_id'] = $val['member_id'];
            $data[$val['member_id']]['fullname'] = $val['fullname'];

            if($val['deduct_id'] == 4) {
                $data[$val['member_id']]['insure_26'] = $val['deduct_id'] == 4 ? $val['amount'] : null;
                $data[$val['member_id']]['tfd_insure_26'] = $val['deduct_id'] == 4 ? $val['transfer_date'] : null;
            }else if($val['deduct_id'] == 3){
                $data[$val['member_id']]['insure_27'] = $val['deduct_id'] == 3 ? $val['amount'] : null;
                $data[$val['member_id']]['tfd_insure_27'] = $val['deduct_id'] == 3 ? $val['transfer_date'] : null;
            }else if($val['deduct_id'] == 2) {
                $data[$val['member_id']]['insure_28'] = $val['deduct_id'] == 2 ? $val['amount'] : null;
                $data[$val['member_id']]['tfd_insure_28'] = $val['deduct_id'] == 2 ? $val['transfer_date'] : null;
            }
        }
        $arr_data['data'] = $data;

        $arr_data['this'] = $this; //override

        $this->load->view('average_dividend/insure_transfer', $arr_data);
    }

    function update_interest()
    {
        $_POST['step'] = 2;
        if ($_POST['step'] == 1) {
            $sql = "select a.member_id,(b.interest-sum(a.interest)) as interest from coop_finance_transaction a INNER JOIN coop_temp_ping1 b 
			ON a.member_id = b.member_id 
			where MONTH(a.payment_date) = 9 and a.account_list_id =  15 and a.interest <> 0 group by a.member_id";
            $rs = $this->db->query($sql);
            $row = $rs->result_array();
            $data = [];
            foreach ($row as $index => $val) {
                $data['member_id']          = $row[$index]['member_id'];
                $data['account_list_id']    = 15;
                $data['principal_payment']  = 0;
                $data['interest']           = $row[$index]['interest'];
                $data['total_amount']       = $row[$index]['interest'];
                $data['payment_date']       = date('2018-08-31');
                $data['createdatetime']     = date('2018-08-31  H:i:s');
                $data['deduct_type']        = "all";
                $this->db->insert('coop_finance_transaction', $data);
            }
            echo "<pre>";
            print_r($row);
        } else if ($_POST['step'] == 2) {
            $sql = "select id,member_id from coop_loan GROUP BY member_id";
            $rs = $this->db->query($sql);
            $row = $rs->result_array();
            foreach ($row as $index => $val) {
                $sql_up = "UPDATE coop_finance_transaction SET receipt_id = '-',loan_id = '" . $row[$index]['id'] . "'
				 where loan_id is null and member_id = '" . $row[$index]['member_id'] . "' and payment_date = '2018-08-31'";
                $this->db->query($sql_up);
                //echo $sql_up."<br/>";
            }
        }
    }

    function delete(){

        $master_id = $_POST['master_id'];
        if($master_id) {
            $this->db->delete("coop_dividend_average", array("master_id" => $master_id));
            $this->db->delete("coop_dividend_average_master", array("id" => $master_id));
            header("Content-Type: application/json");
            echo json_encode(
                array(
                    'status' => true,
                    'msg'    => ''
                ));
            exit;
        }else{
            header("Content-Type: application/json");
            echo json_encode(array('status' => false, 'msg' => ''));
        }

    }

    function update_interest_complete(){

        $stmt = "SELECT member_id,SUM(return_amount) AS return_amount FROM (
SELECT member_id,interest AS `return_amount`,payment_date AS `date` FROM coop_finance_transaction WHERE payment_date BETWEEN '2018-09-01' AND '2018-09-30' AND account_list_id IN ('15','31') AND interest <> 0 UNION ALL SELECT member_id,(return_principal-return_amount) AS return_amount,return_time AS `date` FROM coop_process_return WHERE return_time BETWEEN '2018-09-01' AND '2018-09-30') T GROUP BY member_id";

        $res = $this->db->query($stmt);
        $tar = $res->result_array();

        $stmt = "SELECT member_id,(CONVERT (REPLACE (interest_sum,',',''),UNSIGNED INTEGER)) AS return_amount FROM interest_sum_detail WHERE `month`='09'";

        $res = $this->db->query($stmt);
        $src = $res->result_array();

        $target = [];
        foreach($tar as $val){
            $target[$val['member_id']] = $val['return_amount'];
        }

        $res = $this->db->query("SELECT * FROM coop_finance_transaction WHERE payment_date = '2018-08-31'");

        if($res->num_rows()) {
            $this->db->query("DELETE FROM coop_finance_transaction WHERE payment_date = '2018-08-31'");
        }

        $index = 0;
        foreach ($src as $key => $val) {
//            echo $val['member_id']." ::: ".($source[$val['member_id']] - $val['return_amount'])." = ".$source[$val['member_id']]." - ". $val['return_amount']." ::: ".($source[$val['member_id']] - $val['return_amount']) * 0.00925 ." <br><br>";

            $insterest = intval($val['return_amount']) - intval($target[$val['member_id']]);
            $data[$index]['member_id'] = $val['member_id'];
            $data[$index]['account_list_id'] = 15;
            $data[$index]['principal_payment'] = 0;
            $data[$index]['interest'] = $insterest;
            $data[$index]['total_amount'] = $insterest;
            $data[$index]['payment_date'] = date('2018-08-31');
            $data[$index]['createdatetime'] = date('2018-08-31  H:i:s');
            $data[$index]['deduct_type'] = "all";
            $index++;

        }

        $this->db->insert_batch('coop_finance_transaction', $data);

        $sql = "SELECT m.id,m.member_id,n.finance_transaction_id,n.payment_date FROM coop_loan m INNER JOIN coop_finance_transaction n ON m.member_id=n.member_id WHERE n.payment_date='2018-08-31' GROUP BY m.member_id";
        $rs = $this->db->query($sql);
        $row = $rs->result_array();

        $num = 0;
        foreach ($row as $index => $val) {
//            $sql_up = "UPDATE coop_finance_transaction SET receipt_id = '-',loan_id = '" . $row[$index]['id'] . "'
//				 where loan_id is null and member_id = '" . $row[$index]['member_id'] . "' and payment_date = '2018-08-31'";
            $data_ar[$num]['finance_transaction_id'] = $val['finance_transaction_id'];
            $data_ar[$num]['loan_id'] = $val['id'];
            $data_ar[$num]['receipt_id'] = '-';
            $num++;

            //echo $sql_up."<br/>";
        }
        $this->db->update_batch('coop_finance_transaction', $data_ar, 'finance_transaction_id');
        //$this->db->query($sql_up);

        echo "Completed!";
        exit;

    }

    public function receipt_pdf(){

        $master_id = $this->input->get('id');
        $section = $this->input->get('sect');
        $mem_group_id = $this->input->get('mem_group_id');

        if(!empty($mem_group_id)){
            $group = " AND `group`.id= {$mem_group_id} ";
        }

        $limit          = 100;
        $page           = isset($_GET['page_number']) &&  $_GET['page_number'] > 0 ? ($_GET['page_number'] - 1 ) : 0 ;
        $member_id      = isset($_GET['member_id']) && !empty($_GET['member_id']) ? str_pad($_GET['member_id'], 6, '0', STR_PAD_LEFT) : "";

        $this->db->select('accm_month_ini');
        $account_period_setting = $this->db->get('coop_account_period_setting')->row();
        $setting_period =  $account_period_setting->accm_month_ini;

        $average_start = date('Y-m-d', strtotime((date('Y') - 1)."-".$setting_period."-01"));
        $average_stop = date('Y-m-t', strtotime(date('Y')."-".($setting_period-1)));

        if(isset($_GET['member_id']) && isset($_GET['id'])) {

            //one receipt

            $where = ['a.member_id' => $member_id];

        }else if($this->input->get('member_id_begin') && $this->input->get('member_id_end')){
            $where = " a.member_id between '". str_pad($this->input->get('member_id_begin'), 6, '0', STR_PAD_LEFT)."' and '".str_pad($this->input->get('member_id_end'), 6, '0', STR_PAD_LEFT)."' ";
        }else{

            //many receipt
            $_limit = "";
            if(isset($page)) {
                $_limit = "LIMIT " . $limit . " OFFSET " . ($page * $limit);
            }

            $stmt = "SELECT `deduct`.member_id,`deduct`.`deduct_id`,`deduct`.`amount`,`receipt`.`receipt_id` FROM coop_dividend_deduct `deduct` INNER JOIN coop_dividend_average_receipt `receipt` ON `deduct`.`member_id`=`receipt`.`member_id` INNER JOIN (SELECT t3.member_id,IF (t4.faction_old IS NULL,t3.faction,t4.faction_old) AS faction,IF (t4.level_old IS NULL,t3.LEVEL,t4.level_old) AS LEVEL,t3.firstname_th,t3.lastname_th,t3.prename_id,IF (t4.department_old IS NULL,t3.department,t4.department_old) AS department FROM coop_mem_apply AS t3 LEFT JOIN (SELECT member_id,department_old,faction_old,level_old,date_move FROM coop_mem_group_move WHERE date_move BETWEEN {$average_start} AND {$average_stop} GROUP BY member_id ORDER BY date_move ASC) AS t4 ON t3.member_id=t4.member_id WHERE 1=1 AND member_status<> 3) AS `mem_apply` ON `deduct`.member_id=`mem_apply`.member_id INNER JOIN coop_mem_group AS `group` ON `mem_apply`.{$section} =`group`.`id` WHERE `deduct`.master_id= {$master_id} {$group} GROUP BY member_id ORDER BY `deduct`.member_id ".$_limit;

            $res = $this->db->query($stmt)->result_array();
            $where = " a.member_id in ('".implode("', '",array_map(function($v){ return $v['member_id']; }, $res))."') AND a.master_id = '".$master_id."'";

        }

        $fields = [
            '`a`.`member_id` as `member_id`',
            "concat(ifnull(`e`.`prename_full`,''),`d`.`firstname_th`, ' ', `d`.`lastname_th`) as `fullname`",
            "`b`.`receipt_id` as `receipt_id`",
            "`b`.`receipt_datetime` as `receipt_datetime`",
            "`b`.`sumcount` as `sumcount`",
            "`f`.`mem_group_name` as `mem_group_name`"
        ];

        $this->db->select($fields)
            ->from('coop_dividend_average_receipt a')
            ->join('coop_mem_apply d', 'a.member_id = d.member_id', 'inner')
            ->join('coop_prename e', 'd.prename_id = e.prename_id', 'left')
            ->join('coop_receipt b', 'a.receipt_id = b.receipt_id', 'inner')
            ->join('coop_mem_group f', 'd.level = f.id', 'left')
            ->where($where)
            ->order_by('member_id');

        $res1 = $this->db->get()->result_array();

//        echo $this->db->last_query();
//        echo "<br>";

        $fields = [
            "`a`.`member_id` as `member_id`",
            "`b`.`sumcount` as `sumcount`",
            "`c`.`principal_payment` as `principal_payment`",
            "`c`.`total_amount` as `total_amount`",
            "`c`.`transaction_text` as `transaction_text`"
        ];

        $this->db->select($fields)->from('coop_dividend_average_receipt a')
            ->join('coop_receipt b', 'a.receipt_id = b.receipt_id', 'inner')
            ->join('coop_finance_transaction c', 'b.receipt_id = c.receipt_id', 'inner')
            ->join('coop_account_list f','c.account_list_id = f.account_id', 'inner')
            ->where($where)
            ->order_by('member_id');

        $res2 = $this->db->get()->result_array();

//       echo $this->db->last_query(); exit;
//       echo '<pre>'; print_r($res2); exit;

        $receipt = [];
        foreach ($res1 as $index => $item){
            $receipt[$index] = $item;
            foreach ($res2 as $key => $val){
                if($val['member_id'] === $item['member_id']) {
                    $receipt[$index]['transaction'][] = $val;
                }
            }
        }

        $data['receipts'] = $receipt;

        //ลายเซ็นต์
        $date_signature = date('Y-m-d');
        $this->db->select(array('*'));
        $this->db->from('coop_signature');
        $this->db->where("start_date <= '{$date_signature}'");
        $this->db->order_by('start_date DESC');
        $this->db->limit(1);

        $row = $this->db->get()->result_array();
        $data['signature'] = @$row[0];

        $this->load->view('average_dividend/receipt_pdf', $data);

    }

    public function receipt_pdf_rev(){

        $this->load->library(array('libTFPDF'));

        $master_id      = $_GET['id'];
        $limit          = 100;
        $page           = isset($_GET['page']) ? ($_GET['page'] - 1 ) : 0 ;
        $member_id      = isset($_GET['member_id']) && !empty($_GET['member_id']) ? str_pad($_GET['member_id'], 6, '0', STR_PAD_LEFT) : "";

        if(isset($_GET['member_id']) && isset($_GET['id'])) {

            //one receipt

            $where = ['a.member_id' => $member_id];
        }else{

            //many receipt
            $_limit = "";
            if(isset($page)) {
                $_limit = "LIMIT " . $limit . " OFFSET " . ($page * $limit);
            }

            $stmt = "SELECT `deduct`.member_id,`deduct`.`deduct_id`,`deduct`.`amount`,`receipt`.`receipt_id` FROM coop_dividend_deduct `deduct` INNER JOIN coop_dividend_average_receipt `receipt` ON `deduct`.`member_id`=`receipt`.`member_id` WHERE `deduct`.master_id='".$master_id."' GROUP BY member_id ORDER BY `deduct`.member_id ".$_limit;

            $res = $this->db->query($stmt)->result_array();
            $where = " a.member_id in (".implode(', ',array_map(function($v){ return $v['member_id']; }, $res)).") ";

        }

        $fields = [
            '`a`.`member_id` as `member_id`',
            "concat(`e`.`prename_full`,`d`.`firstname_th`, ' ', `d`.`lastname_th`) as `fullname`",
            "`b`.`receipt_id` as `receipt_id`",
            "`b`.`receipt_datetime` as `receipt_datetime`",
            "`b`.`sumcount` as `sumcount`",
            "`f`.`mem_group_name` as `mem_group_name`"
        ];

        $this->db->select($fields)
            ->from('coop_dividend_average_receipt a')
            ->join('coop_mem_apply d', 'a.member_id = d.member_id', 'inner')
            ->join('coop_prename e', 'd.prename_id = e.prename_id', 'left')
            ->join('coop_receipt b', 'a.receipt_id = b.receipt_id', 'inner')
            ->join('coop_mem_group f', 'd.level = f.id', 'left')
            ->where($where)
            ->order_by('member_id');

        $res1 = $this->db->get()->result_array();

        $fields = [
            "`a`.`member_id` as `member_id`",
            "`b`.`sumcount` as `sumcount`",
            "`c`.`principal_payment` as `principal_payment`",
            "`c`.`total_amount` as `total_amount`",
            "`c`.`transaction_text` as `transaction_text`"
        ];

        $this->db->select($fields)->from('coop_dividend_average_receipt a')
            ->join('coop_receipt b', 'a.receipt_id = b.receipt_id', 'inner')
            ->join('coop_finance_transaction c', 'b.receipt_id = c.receipt_id', 'inner')
            ->join('coop_account_list f','c.account_list_id = f.account_id', 'inner')
            ->where($where)
            ->order_by('member_id');

        $res2 = $this->db->get()->result_array();

        $receipt = [];
        foreach ($res1 as $index => $item){
            $receipt[$index] = $item;
            foreach ($res2 as $key => $val){
                if($val['member_id'] === $item['member_id']) {
                    $receipt[$index]['transaction'][] = $val;
                }
            }
        }

        $data['receipts'] = $receipt;

        //ลายเซ็นต์
        $date_signature = date('Y-m-d');
        $this->db->select(array('*'));
        $this->db->from('coop_signature');
        $this->db->where("start_date <= '{$date_signature}'");
        $this->db->order_by('start_date DESC');
        $this->db->limit(1);

        $row = $this->db->get()->result_array();
        $data['signature'] = @$row[0];

        $this->load->view('average_dividend/receipt_pdf_rev', $data);

    }

    public function round25($B){
        $B = number_format($B, 2);
        $satang=explode(".",$B);//แยกสตางค์จากจำนวนเต็ม
        $B_=$satang['0'];//จำนวนเต็มบาท
        $B_stang=$satang['1'];//เศษสตางค์
        $B_stang = substr($B_stang, 0, 2);
        if($B_stang>=12.5 && $B_stang<37.5)
        {
            $Bath=$B_.".25";
        }
        elseif($B_stang>=37.5  && $B_stang<62.5)
        {
            $Bath=$B_.".50";
        }
        elseif($B_stang > 62.5 && $B_stang < 87.5)
        {
            $Bath=$B_.".75";
        }
        elseif($B_stang >= 87.5)
        {
            $B_++;
            $Bath=$B_.".00";
        }else{
            $Bath=$B_.".00";
        }
        $Bath = round($Bath, 2);
        return $Bath;
    }

}
