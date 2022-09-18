<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php
if(@$_GET['update_log_atm'] == 'update_log_atm'){
/****connect log ATM เพื่อดึงไฟล์มาไว้าที่ system	******/
$ftp_server = "61.91.58.222";
$ftp_user = "spcoop";
$ftp_pass = "4Yc%D8Gs*k#98+7F";

$server_file = "httpdocs/log/atm.log";
// open local file to write to
$local_file = "log_atm/atm.log";
$conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 

if(@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
	//echo "Connected as {$ftp_user}@{$ftp_server}<br>";	
	//print_r(ftp_nlist($conn_id, "."));
	// download server file and save it to open local file
	if (ftp_get($conn_id, $local_file, $server_file, FTP_ASCII, 0))
	{
	  //echo "Successfully written to $local_file.";
	}
	else
	{
	  //echo "Error downloading $server_file.";
	}	
	//echo "Complete";
}
else {
	//echo "Couldn't connect as {$ftp_user}";
}
ftp_close($conn_id);

/**************************************/
}

function ConvertToThaiDate($value,$short='1',$need_time='1',$need_time_second='0') {
	$date_arr = explode(' ', $value);
	$date = $date_arr[0];
	if(isset($date_arr[1])){
		$time = $date_arr[1];
	}else{
		$time = '';
	}

	$value = $date;
	if($value!="0000-00-00" && $value !='') {
		$x=explode("-",$value);
		if($short==false)
			$arrMM=array(1=>"มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
		else
			$arrMM=array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
		// return $x[2]." ".$arrMM[(int)$x[1]]." ".($x[0]>2500?$x[0]:$x[0]+543);
		if($need_time=='1'){
			if($need_time_second == '1'){
				$time_format = $time!=''?date('H:i:s น.',strtotime($time)):'';
			}else{
				$time_format = $time!=''?date('H:i น.',strtotime($time)):'';
			}
		}else{
			$time_format = '';
		}

		return (int)$x[2]." ".$arrMM[(int)$x[1]]." ".($x[0]>2500?$x[0]:$x[0]+543)." ".$time_format;
	} else
		return "";
}

function ConvertToSQLDate($date) {
	if(!empty($date)) {
		if(strpos($date, "/")!==false) {
			$x = explode("/", $date);
			$x[2] = ($x[2] > 2500 ? $x[2] - 543 : $x[2]);
			$x[1] = sprintf("%02d", (int)$x[1]);
			$return = "{$x[2]}-{$x[1]}-{$x[0]}";
		} elseif(strpos($date, "-")!==false) {
			$x = explode("-", $date);
			$x[0] = ($x[0] > 2500 ? $x[0] - 543 : $x[0]);
			$x[1] = sprintf("%02d", (int)$x[1]);
			$return = "{$x[0]}-{$x[1]}-{$x[2]}";
		} else $return = "0000-00-00";
	} else $return = "";
	return $return;
}
	
$member_id = (@$_GET['member_id'] != '')?sprintf("%06d",@$_GET['member_id']):'';
$date_start = @ConvertToSQLDate(@$_GET['date_start']).' 00:00:00';
$date_end = @ConvertToSQLDate(@$_GET['date_end']).' 23:59:59';

/****connect system	******/
define("HOSTNAME","61.47.42.43") ;
define("DBNAME","spktsys_com");
define("USERNAME","spktsys_com");
define("PASSWORD",'x4zOmINFa');

$mysqli = new mysqli( HOSTNAME , USERNAME , PASSWORD );
$mysqli->select_db(DBNAME);
$mysqli->query("SET NAMES utf8");
/**************************************/

//echo 'date_start='.$date_start.'<br>';
//echo 'date_end='.$date_end.'<br>';
/**************data in log to array**************/
$arr_log = array();
$i=0;

$arr_file = array();  
$log_file = scandir("log_atm", 1);
foreach($log_file AS $file_name){
	if($file_name != '.' && $file_name != '..'){	
		$arr_file[]=$file_name;
	}
}

foreach($arr_file AS $file_log){
	//foreach (file('https://dev.spktcoop.com/log_atm/'.$file_log) as $line) {
	foreach (file('https://system.spktcoop.com/log_atm/'.$file_log) as $line) {
		/*$num_req = strpos($line,'req');		
		$data_req = substr($line,($num_req+5));	
		$type_req = substr($line,$num_req,3);	
		if($num_req>0){
			$arr_req = json_decode($data_req, true);
			$arr_log[$i]['type'] = $type_req;
			$arr_log[$i]['data'] = $arr_req;
			$i++;
		}
		*/		
		$num_res = strpos($line,'res');		
		$data_res = substr($line,($num_res+5));	
		$type_res = substr($line,$num_res,3);	
		if($num_res>0){	
			$arr_res = json_decode($data_res, true);
			$arr_log[$i]['type'] = $type_res;
			$arr_log[$i]['data'] = $arr_res;
			$i++;
		}
	}
}
/**************************************/

$sql_account = "SELECT account_id,mem_id FROM coop_maco_account WHERE type_id = '2'";
$rs_account = $mysqli->query($sql_account);
$arr_account = array();
while(( $row_account = $rs_account->fetch_assoc() )) {		
	$arr_account[$row_account['account_id']] = $row_account['mem_id'];
}	
//echo '<pre>'; print_r($arr_account); echo '</pre>';

$type_list = array('' => 'ผูกบัตร',
									'30' => 'สอบถามยอด',
									'10' => 'ถอนเงิน',
									'40' => 'โอนเงินไปยังบัญชีภายในบัตร',
									'31' => 'สอบถามยอด',
									'41' => 'โอนเงินไปยังบัญชีบุคคลอื่นภายในสหกรณ์');
$arr_check_upbean = array('10','40','41');	

$arr_data = array();
$check_mem = ($member_id != '')? '':'';
$j=0;
foreach($arr_log AS $key_log=>$value){
	if($value['type'] == 'res' && @$value['data']['tranType'] != ''){
		//วันที่ เวลา
		$transactionDate = date('Y').@$value['data']['transactionDate'];
		$transaction_yy = substr(@$transactionDate,0,4);//ปี
		$transaction_mm = substr(@$transactionDate,4,2);//เดือน
		$transaction_dd = substr(@$transactionDate,6,2);//วัน
		
		$transactionTime = @$value['data']['transactionTime'];
		$transaction_h = substr(@$transactionTime,0,2);//ชั่วโมง
		$transaction_i = substr(@$transactionTime,2,2);//นาที
		$transaction_s = substr(@$transactionTime,4,2);//วินาที
		$createdatetime = $transaction_yy.'-'.$transaction_mm.'-'.$transaction_dd.' '.$transaction_h.':'.$transaction_i.':'.$transaction_s;
		//

		//จำนวนเงิน	
		$transaction_integer = substr(@$value['data']['transactionAmount'],0,6);//จำนวนเต็ม
		$transaction_decimal = substr(@$value['data']['transactionAmount'],6,2);//ทศนิยม
		$transaction_amount = $transaction_integer.'.'.$transaction_decimal;//ยอดเงินที่ถอน	
		//
		
		//บัญชี 21
		$perfix_account = substr(@$value['data']['fromAcctNo'],0,5);
		//echo $perfix_account.'|'.$createdatetime.'<br>';
		
		//เช็คการทำรายการกับ ตาราง coop_account_transaction
		$sql_account_transaction = "SELECT transaction_time,transaction_list,transaction_withdrawal,transaction_deposit 
						FROM coop_account_transaction 
						WHERE account_id = '".@$value['data']['fromAcctNo']."' 
							AND member_id_atm IS NOT NULL 
							AND transaction_time = '".$createdatetime."'
						";
		$rs_account_transaction = $mysqli->query($sql_account_transaction);
		$row_account_transaction = $rs_account_transaction->fetch_assoc();
		if(!empty($row_account_transaction)){
			$check_upbean = '/';
		}else{
			if(in_array(@$value['data']['tranType'],$arr_check_upbean)){
				$check_upbean = 'X';
			}else{
				$check_upbean = '/';
			}
		}
		
		if(trim($perfix_account) == "00121"){
			if($createdatetime >= $date_start && $createdatetime <= $date_end){						
				if($member_id !=''){ 
					if($member_id == @$arr_account[@$value['data']['fromAcctNo']]){
						$arr_data[$j]['createdatetime']  = $createdatetime;
						$arr_data[$j]['member_id']  = @$arr_account[@$value['data']['fromAcctNo']];
						$arr_data[$j]['account_id']  = @$value['data']['fromAcctNo'];
						$arr_data[$j]['type_list']  = (@$value['data']['messageType'] == '0410')?'คืนเงิน':@$type_list[@$value['data']['tranType']];
						$arr_data[$j]['transaction_amount']  = @number_format($transaction_amount,2);
						$arr_data[$j]['check_d1']  = (@$value['data']['responseCode']=='000')?'/':'X';
						$arr_data[$j]['check_upbean']  = $check_upbean;
						$arr_data[$j]['response_code']  = (@$value['data']['responseCode']=='000')?'Complete':'Not Complete';
						$j++; 
					}
				}else{
					$arr_data[$j]['createdatetime']  = $createdatetime;
					$arr_data[$j]['member_id']  = @$arr_account[@$value['data']['fromAcctNo']];
					$arr_data[$j]['account_id']  = @$value['data']['fromAcctNo'];
					$arr_data[$j]['type_list']  = (@$value['data']['messageType'] == '0410')?'คืนเงิน':@$type_list[@$value['data']['tranType']];
					$arr_data[$j]['transaction_amount']  = @number_format($transaction_amount,2);
					$arr_data[$j]['check_d1']  = (@$value['data']['responseCode']=='000')?'/':'X';
					$arr_data[$j]['check_upbean']  = $check_upbean;
					$arr_data[$j]['response_code']  = (@$value['data']['responseCode']=='000')?'Complete':'Not Complete';
					$j++; 
				}
			}
		}
	}
}

//เรียงลำดับวันที่
array_multisort( array_column($arr_data, "createdatetime"),SORT_ASC,$arr_data);
	
?>
<center>
	<h3>รายงานตรวจสอบการทำงาน ระบบ ATM</h3>
	<h3><?php echo 'วันที่ '.@$_GET['date_start'].' ถึง วันที่ '.@$_GET['date_end']; ?></h3>					
	<br>

	<table style="width: 1200px;" border="1" cellpadding="1" cellspacing="0" class="table table-view table-center">
		<thead>
			<tr>
				<th style='width: 15%;'>วันเวลา</th>
				<th style='width: 10%;'>รหัสสมาชิก</th>
				<th style='width: 10%;'>เลขที่บัญชี</th>
				<th style='width: 20%;'>รายการ</th>
				<th style='width: 10%;'>จำนวนเงิน</th>
				<th style='width: 10%;'>D1</th>
				<th style='width: 10%;'>Upbean</th>
				<th style='width: 15%;'>Status</th>
			</tr>
		</thead>
		<tbody>								
		<?php 
			foreach($arr_data AS $key=>$value){
		?>
				<tr>
					<td>
						<?php								
							echo (@$value['createdatetime'] !='')?ConvertToThaiDate(@$value['createdatetime'],1,1,1):'';
						?>						
					</td>
					<td><?php echo @$value['member_id'];?></td>
					<td><?php echo @$value['account_id']; ?></td>
					<td class="text-left"><?php echo @$value['type_list']; ?></td>
					<td style="text-align: right;"><?php echo @$value['transaction_amount']; ?></td>
					<td style="text-align: center;"><?php echo @$value['check_d1']; ?></td>
					<td style="text-align: center;"><?php echo @$value['check_upbean']; ?></td>
					<td><?php echo @$value['response_code']; ?></td>
				</tr>
		<?php
			}
		?>
		</tbody>
	</table>	
</center>