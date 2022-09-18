<?php
	session_start();
	require "config.inc.php";

	$request = json_decode(file_get_contents("php://input"));
	if (!empty($request)){
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
	} 

	$member_no = $_POST['member_no'] ? $mysqli->real_escape_string($_POST['member_no']) : null ;
	$mobile = $_POST['mobile'] ? $mysqli->real_escape_string($_POST['mobile']) : null ;

	$data = ['success' => 0, 'error_message' => ''];

	$sql = "SELECT * FROM macocoop_or_th.coop_mem_apply WHERE member_id = '{$member_no}' AND mobile = '{$mobile}'";					
	$rs = $mysqli->query($sql);

	if( $rs->num_rows ) {

		$number_otp = rand(100000, 999999);

		$time_start 	= date("Y-m-d H:i:s");
		$currentDate 	= strtotime($time_start);
		$futureDate 	= $currentDate + ( 60 * 10 );
		$time_end 		= date("Y-m-d H:i:s", $futureDate);

		$msg = "รหัส OTP สำหรับการดำเนินการคือ  {$number_otp} ใช้ได้ถึง {$time_end}";

	// 	//4 test
	// 	//$mobile = "0818847223" ;
	// 	//$status_sms = 1;
		//$status_sms = send_sms($mobile, $msg);


		if($status_sms) {
			$_SESSION["send_otp"] = $number_otp;
			$_SESSION["mobile"] = $mobile;
			$_SESSION["time_start"] = $time_start;
			$_SESSION["time_end"] = $time_end;
		}

		$data['success'] = 1 ;
		$data['mobile'] = $mobile ;
		$mobile_text = substr_replace($mobile, '****', 3, 4);
		$data["message"] = "ระบบได้ทำการจัดส่งรหัสผ่านชั่วคราว (OTP)\nให้ท่านทางโทรศัพท์มือถือ\n{$mobile_text}\nกรุณาเข้าสู่ระบบด้วยรหัสผ่านที่ได้่รับ\nจากนั้นทำการเปลี่ยนรหัสผ่านใหม่" ;
		$data["message_otp"] = $msg;
		$data['session_id'] = session_id() ;

		$token = md5( $member_no . "-". date("Ymdhis") ) ;
		$sql = "INSERT INTO macocoop_web.cmp_imp_member_login_session (member_no, token, login_type, create_time, create_ip, last_access_time, last_access_ip, event)
					VALUES('{$member_no}', '{$token}', '', NOW(), '{$_SERVER["REMOTE_ADDR"]}', NOW(), '{$_SERVER["REMOTE_ADDR"]}', 'ส่ง OTP สมัคร {$number_otp}')";
		$mysqli->query($sql);
	} else {
		$data = ['success' => 0, 'error_message' => 'หมายเลขโทรศัพท์มือถือที่ท่านป้อน \n ไม่ตรงกับหมายเลขที่ได้ลงทะเบียนไว้กับสหกรณ์ \n กรุณาติดต่อสหกรณ์ โทร 02-579-7070'];
	}


	echo json_encode($data);