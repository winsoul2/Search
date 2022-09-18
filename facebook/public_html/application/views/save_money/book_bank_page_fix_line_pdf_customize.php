<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}
function format_date($text) {
    if($text!=''){
        $date = date('d/m/Y',strtotime($text));
        $date_arr = explode('/',$date);
        $date = (int)$date_arr[0]."/".(int)$date_arr[1]."/".$date_arr[2];
        return $date;
    }else{
        return '';
    }
}
function add_star($text){
    if($text!=''){
        $text_arr = explode('.',$text);
        $number = $text_arr[0];
        $number_without_commas = str_replace(',','',$number);
        $decimal = @$text_arr[1]!=''?$text_arr[1]:'00';
        $count_number = strlen($number_without_commas);
        $star = '';
        $count_star = 13-$count_number;
        for($i=0;$i<=$count_star;$i++){
            $star .= '*';
        }
        $number = number_format($number_without_commas.".".$decimal,2);
        $text_return = $star.$number;
        return $text_return;
    }else{
        return '';
    }
}
function cal_age($birthday,$type = 'y'){//รูปแบบการเก็บค่าข้อมูลวันเกิด
    $birthday = date("Y-m-d",strtotime($birthday));
    $today = date("Y-m-d");//จุดต้องเปลี่ยน
    list($byear, $bmonth, $bday)= explode("-",$birthday);//จุดต้องเปลี่ยน
    list($tyear, $tmonth, $tday)= explode("-",$today);//จุดต้องเปลี่ยน
    $mbirthday = mktime(0, 0, 0, $bmonth, $bday, $byear);
    $mnow = mktime(0, 0, 0, $tmonth, $tday, $tyear );
    $mage = ($mnow - $mbirthday);
    $u_y=date("Y", $mage)-1970;
    $u_m=date("m",$mage)-1;
    $u_d=date("d",$mage)-1;
    if($type=='y'){
        return $u_y;
    }else if($type=='m'){
        return $u_m;
    }else{
        return $u_d;
    }
}

$pdf = new FPDI('P','mm', array($style['width_page'], $style['height_page']));
$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$border = 0;
$number_start = $this->input->get('number');

$this->db->select(array('*'));
$this->db->from('coop_maco_account');
$this->db->where("account_id = '".$this->input->get('account_id')."'");
$row_account = $this->db->get()->result_array();

$book_number = $row_account[0]['book_number'];

$data = array();
$tran_ids = json_decode($_GET["tran_ids"]);
if (!empty($tran_ids)) {
    $this->db->select(array('coop_account_transaction.*','coop_user.user_name'));
    $this->db->from('coop_account_transaction_view  AS coop_account_transaction');
    $this->db->join('coop_user', 'coop_account_transaction.user_id = coop_user.user_id', 'left');
    $this->db->where("account_id = '".$this->input->get('account_id')."' AND transaction_id IN ".str_replace("]",")",str_replace("[","(",$_GET["tran_ids"])));
    //$this->db->order_by('seq_no ASC,c_num ASC');
    $this->db->order_by('transaction_time ASC,transaction_id ASC,c_num ASC');
    $data = $this->db->get()->result_array();
}

$this->db->select(array('*'));
$this->db->from('coop_user');
$this->db->where("user_id = '".$_SESSION['USER_ID']."'");
$data_user = $this->db->get()->result_array();
$data_user = $data_user[0];

$count=0;
$number_now = $number_start;
$result = array();
$sum_all = array();
$s=1;
$line_start = $_GET["line_start"];
if(empty($line_start)) {
    foreach($data as $key => $row){
        //Add Position to result
        $position = array_search($row['transaction_id'], $tran_ids);
        $result[1][$position] = $row;
        if($row['member_id_atm']!=''){
            $result[1][$position]['user_name'] = 'ATM';
        }
        $number_now++;
        $sum_all['transaction_list'] = 'BF';
        $sum_all['transaction_time'] = date('Y-m-d H:i:s');
        @$sum_all['transaction_deposit'] += $row['transaction_deposit'];
        @$sum_all['transaction_withdrawal'] += $row['transaction_withdrawal'];
        $sum_all['transaction_balance'] = $row['transaction_balance'];
        $sum_all['transaction_no_in_balance'] = $row['transaction_no_in_balance'];
        $sum_all['user_name'] = $data_user['user_name'];
        $s++;
        $count++;
    }
    $line_start = $this->db->get_where("coop_maco_account", array(
        "account_id" => $this->input->get('account_id')
    ))->result_array()[0]['print_number_point_now'];
    if($line_start==""){
        $line_start = 1;
    }
} else {
    //Set begin position
    $position = $line_start;
    foreach($data as $key => $row){
        //Add Position to result
        $page = floor($position/27)+1;
        $line = $position <=26 ? $position%27 : ($position%27) + 1;
        $result[$page][$line] = $row;
        if($row['member_id_atm']!=''){
            $result[$page][$line]['user_name'] = 'ATM';
        }
        $number_now++;
        $sum_all['transaction_list'] = 'BF';
        $sum_all['transaction_time'] = date('Y-m-d H:i:s');
        @$sum_all['transaction_deposit'] += $row['transaction_deposit'];
        @$sum_all['transaction_withdrawal'] += $row['transaction_withdrawal'];
        $sum_all['transaction_balance'] = $row['transaction_balance'];
        $sum_all['transaction_no_in_balance'] = $row['transaction_no_in_balance'];
        $sum_all['user_name'] = $data_user['user_name'];
        $s++;
        $count++;
        $position++;
    }

}
$number_start = $line_start;

if(@$_GET['conclude_transaction'] == '1' && !empty($sum_all)){
    $conclude_transaction = array();
    $data_insert = array();
    $data_insert['account_id'] = $this->input->get('account_id');
    $data_insert['transaction_list'] = $sum_all['transaction_list'];
    $data_insert['transaction_deposit'] = $sum_all['transaction_deposit'];
    $data_insert['transaction_withdrawal'] = $sum_all['transaction_withdrawal'];
    $data_insert['transaction_balance'] = $sum_all['transaction_balance'];
    $data_insert['transaction_no_in_balance'] = $sum_all['transaction_no_in_balance'];
    $data_insert['transaction_time'] = $sum_all['transaction_time'];
    $data_insert['user_id'] = $_SESSION['USER_ID'];
    $this->db->insert('coop_account_transaction', $data_insert);
    $sum_all['transaction_id'] = $this->db->insert_id();
    $conclude_transaction[] = $sum_all;
}

// $this->db->limit($count, $line_start);
// $row = $this->db->get_where("coop_book_bank_stagement_rowX", array(
// 	"style_id" => "1"
// ))->result_array();

if(empty($row)){
    die("โปรดตั้งค่าการพิมพ์ก่อน");
}

$total_row = $this->db->select("count('*') as total")->get_where("coop_book_bank_stagement_row", array(
    "style_id" => "1"
))->result_array()[0]['total'];

$number_end = $total_row;
$per_page = $total_row;
$current_line = $number_start;
$pdf->AddPage();

if(!empty($conclude_transaction)){
    array_unshift($data, $conclude_transaction[0]);
}
$border = 0;
foreach ($data as $key => $value) {

    $this->db->join("coop_book_bank_stagement_row_setting", "coop_book_bank_stagement_row_setting.row_id = coop_book_bank_stagement_row.row_id", "inner");
    $row_item = $this->db->get_where("coop_book_bank_stagement_row", array(
        "style_id" => "1",
        "no" => ($current_line)
    ))->result_array();
    //start line.
    foreach ($row_item as $k => $item) {
        $x = $item['x'];
        $y = $item['y'];
        $font_size = $item['font_size'];

        $text = $item['style_value'];
        $width = $item['width'];
        $align = $item['align'];
        $lpad_width = 22;
        // echo $text;
        // echo " ";
        if($item['style_value']=="[no]"){
            $text = $value['seq_no'];
            $text_width =  round($pdf->GetStringWidth($text),2);
        }
        if($item['style_value']=="[date]"){
            $transaction_time = ($value['c_num'] == '2')?$arr_date_due[$value['account_id']][$value['transaction_id'].'_'.$value['c_num']]:$value['transaction_time'];
            $text = date("d/m/", strtotime($transaction_time));
            $text .= substr((date("Y", strtotime($transaction_time))+543),2,2);
        }
        if($item['style_value']=="[code]"){
            $text = $value['transaction_list'];
        }
        if($item['style_value']=="[withdrawal]"){
            $text = ($value['transaction_withdrawal'] == 0)?"":number_format($value['transaction_withdrawal'],2);
            $text_width =  round($pdf->GetStringWidth($text),2);
            $char_width = round($pdf->GetStringWidth("*"),2);
            for ($i=$text_width; $i < $lpad_width; $i+=$char_width) {
                $text = $text;
            }
        }
        if($item['style_value']=="[deposit]"){
            $text = ($value['transaction_deposit'] == 0)?"":number_format($value['transaction_deposit'],2);
            $text_width =  round($pdf->GetStringWidth($text),2);
            $char_width = round($pdf->GetStringWidth("*"),2);
            for ($i=$text_width; $i < $lpad_width; $i+=$char_width) {
                $text = $text;
            }
        }
        if($item['style_value']=="[balance]"){
            $text = number_format($value['transaction_balance'],2);
            $text_width =  round($pdf->GetStringWidth($text),2);
            $char_width = round($pdf->GetStringWidth("*"),2);
            for ($i=$text_width; $i < $lpad_width; $i+=$char_width) {
                $text = $text;
            }
        }
        if($item['style_value']=="[staff]"){
            $text = $value['user_name'];
        }

        $pdf->SetFont('THSarabunNew', '', $font_size );
        $pdf->SetXY( $x, $y );
        $pdf->cell($width, 6, U2T($text), $border, 0, $align);

        $data_insert = array();
        $data_insert['print_status'] = '1';
        $data_insert['print_number_point'] = $current_line;
        $data_insert['book_number'] = $book_number;
        $this->db->where('transaction_id', $value['transaction_id']);
        $this->db->update('coop_account_transaction', $data_insert);

    }
    //end line.

    if($current_line >= $per_page){
        $current_line = 0;
        $pdf->AddPage();
    }

    $current_line++;
}

$data_insert = array();
$data_insert['last_time_print'] = date('Y-m-d H:i:s');
$data_insert['print_number_point_now'] = ($current_line) >= $total_row ? 1 : ($current_line);
$data_insert['book_number'] = $book_number;
$this->db->where('account_id', $this->input->get('account_id'));
$this->db->update('coop_maco_account', $data_insert);

$pdf->Output();
exit;