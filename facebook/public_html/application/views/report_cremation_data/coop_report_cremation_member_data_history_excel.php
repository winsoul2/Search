<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานการเปลี่ยนแปลงข้อมูลสมาชิก.xls"); 
date_default_timezone_set('Asia/Bangkok');
?>
<pre>
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<style>
				.num {
				  mso-number-format:General;
				}
				.text{
				  mso-number-format:"\@";/*force text*/ 
				}
				.text-center{
					text-align: center;
				}
				.text-left{
					text-align: left;
				}
				.table_title{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 22px;
					font-weight: bold;
					text-align:center;
				}
				.table_title_right{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 16px;
					font-weight: bold;
					text-align:right;
				}
				.table_header_top{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border-top: thin solid black;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_mid{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_bot{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border-bottom: thin solid black;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_bot2{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border: thin solid black;
				}
				.table_body{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 21px;
					border: thin solid black;
				}
				.table_body_right{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 21px;
					border: thin solid black;
					text-align:right;
				}
			</style>
		</head>
		<body>
<?php
	if($_GET['month']!='' && $_GET['year']!=''){
		$day = '';
		$month = $_GET['month'];
		$year = $_GET['year'];
		$title_date = " เดือน ".$this->month_arr[$month]." ปี ".($year);
	}else{
		$day = '';
		$month = '';
		$year = ($_GET['year']);
		$title_date = " ปี ".($year);
    }
    
    $runno = 0;
    $mem_type = array('1'=>'สามัญ', '2'=>'สมทบ');

    $input_label = array("mem_type_id" => "ประเภทสมาชิก",
                            "prename_id" => "คำนำหน้าชื่อ",
                            "assoc_firstname" => "ชื่อ",
                            "assoc_lastname" => "นามสกุล",
                            "assoc_birthday" => "วันเกิด",
                            "id_card" => "หมายเลขบัตรประจำตัวประชาชน",
                            "relation" => "ประเภทความสัมพันธ์กับสมาชิก",
                            "ref_member_id" => "สมาชิกสหกรณ์ที่อ้างอิงถึง",
                            "occupation" => "อาชีพ",
                            "position" => "ตำแหน่ง",
                            "workplace" => "สถานที่ทำงาน",
                            "office_phone" => "โทรศัพท์ที่ทำงาน",
                            "addr_no" => "เลขที่่บ้าน(ที่อยู่ตามทะเบียนบ้าน)",
                            "addr_village" => "หมู่บ้าน(ที่อยู่ตามทะเบียนบ้าน)",
                            "addr_moo" => "เลขที่หมู่บ้าน(ที่อยู่ตามทะเบียนบ้าน)",
                            "addr_soi" => "ซอย(ที่อยู่ตามทะเบียนบ้าน)",
                            "addr_street" => "ถนน(ที่อยู่ตามทะเบียนบ้าน)",
                            "province_id" => "จังหวัด(ที่อยู่ตามทะเบียนบ้าน)",
                            "amphur_id" => "อำเภอ/เขต(ที่อยู่ตามทะเบียนบ้าน)",
                            "district_id" => "ตำบล/แขวง(ที่อยู่ตามทะเบียนบ้าน)",
                            "zip_code" => "รหัสไปรษณีย์(ที่อยู่ตามทะเบียนบ้าน)",
                            "cur_addr_no" => "เลขที่่บ้าน(ที่อยู่ปัจจุบัน)",
                            "cur_addr_village" => "หมู่บ้าน(ที่อยู่ปัจจุบัน)",
                            "cur_addr_moo" => "เลขที่หมู่บ้าน(ที่อยู่ปัจจุบัน)",
                            "cur_addr_soi" => "ซอย(ที่อยู่ปัจจุบัน)",
                            "cur_addr_street" => "ถนน(ที่อยู่ปัจจุบัน)",
                            "cur_province_id" => "จังหวัด(ที่อยู่ปัจจุบัน)",
                            "cur_amphur_id" => "อำเภอ/เขต(ที่อยู่ปัจจุบัน)",
                            "cur_district_id" => "ตำบล/แขวง(ที่อยู่ปัจจุบัน)",
                            "cur_zip_code" => "รหัสไปรษณีย์(ที่อยู่ปัจจุบัน)",
                            "marry_name" => "ชื่อคู่สมรส",
                            "receiver_1" => "ผู้รับเงินฌาปนกิจสงเคราะห์ลำดับที่ 1",
                            "receiver_2" => "ผู้รับเงินฌาปนกิจสงเคราะห์ลำดับที่ 2",
                            "receiver_3" => "ผู้รับเงินฌาปนกิจสงเคราะห์ลำดับที่ 3",
                            "funeral_manager" => "ผู้จัดการศพ"
                        );
?>
			<table class="table table-bordered">
				<tr>
					<tr>
						<th class="table_title" colspan="17">แบบทะเบียนสมาชิก</th>
					</tr>
					<tr>
						<th class="table_title" colspan="17">สมาคมฌาปนกิจสงเคราะห์<?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="17">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="17">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>
			<table class="table table-bordered">
				<thead>
                    <tr>
                        <th rowspan="2" class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
                        <th rowspan="2" class="table_header_top" style="vertical-align: middle;">ชื่อสมาชิก</th>
                        <th rowspan="2" class="table_header_top" style="vertical-align: middle;">เลขประจำตัวสมาชิก</th>
                        <th rowspan="2" class="table_header_top" style="vertical-align: middle;">วันเดือนปีที่เข้าเป็นสมาชิก</th>
                        <th rowspan="2" class="table_header_top" style="vertical-align: middle;">ประเภทสมาชิก</th>
                        <th rowspan="2" class="table_header_top" style="vertical-align: middle;">วันเดือนปีเกิด</th>
                        <th rowspan="2" class="table_header_top" style="vertical-align: middle;">ที่อยู่ปัจจุบัน</th>
                        <th rowspan="2" class="table_header_top" style="vertical-align: middle;">ชื่อสามีหรือภรรยา</th>
                        <th rowspan="2" class="table_header_top" style="vertical-align: middle;">ชื่อผู้รับเงินสงเคราะห์ที่ระบุไว้</th>
                        <th rowspan="2" class="table_header_top" style="vertical-align: middle;">ชื่อผู้จัดการศพที่ระบุไว้</th>
                        <th rowspan="2" class="table_header_top" style="vertical-align: middle;">เรื่องที่เปลี่ยนแปลง</th>
                        <th colspan="3" class="table_header_top" style="vertical-align: middle;">การเปลี่ยนแปลงกรณีทั่วไป</th>
                        <th colspan="2" class="table_header_top" style="vertical-align: middle;">การเปลี่ยนแปลงกรณีพ้นจากสมาชิกภาพ</th>
                        <th rowspan="2" class="table_header_top" style="vertical-align: middle;">หมายเหตุ</th>
                    </tr>
                    <tr>
                        <th class="table_header_top" style="vertical-align: middle;">วันเดือนปีที่เปลี่ยนแปลง</th>
                        <th class="table_header_top" style="vertical-align: middle;">เดิม</th>
                        <th class="table_header_top" style="vertical-align: middle;">เปลี่ยนเป็น</th>
                        <th class="table_header_top" style="vertical-align: middle;">วันเดือนปีที่พ้นจากสมาชิกภาพ</th>
                        <th class="table_header_top" style="vertical-align: middle;">สาเหตุ</th>
                    </tr>
				</thead>
				<tbody>
                
                    <?php
                        foreach($datas as $row) {
                            $address = !empty($row["no"]) ? " ".$row["no"] : "";
                            $address .= !empty($row["village"]) ? " ".$row["village"] : "";
                            $address .= !empty($row["cur_addr_moo"]) ? " ".$row["cur_addr_moo"] : "";
                            $address .= !empty($row["cur_addr_soi"]) ? " ".$row["cur_addr_soi"] : "";
                            $address .= !empty($row["cur_addr_street"]) ? " ".$row["cur_addr_street"] : "";
                            if($row["province_code"] == '10') {
                                $address .= !empty($row["district_name"]) ? " แขวง".$row["district_name"] : "";
                                $address .= !empty($row["amphur_name"]) ? " เขต".$row["amphur_name"] : "";
                                $address .= !empty($row["province_name"]) ? " ".$row["province_name"] : "";
                            } else {
                                $address .= !empty($row["district_name"]) ? " ตำบล".$row["district_name"] : "";
                                $address .= !empty($row["amphur_name"]) ? " อำเภอ".$row["amphur_name"] : "";
                                $address .= !empty($row["province_name"]) ? " จังหวัด".$row["province_name"] : "";
                            }
                            $address .= !empty($row["cur_zip_code"]) ? " ".$row["cur_zip_code"] : "";
    
                            $receiver = "";
                            $receiver .= !empty($row["receiver_1"]) ? $row["receiver_1"] : "";
                            $receiver .= !empty($row["receiver_2"]) ? ", ".$row["receiver_2"] : "";
                            $receiver .= !empty($row["receiver_3"]) ? ", ".$row["receiver_3"] : "";
                    ?>
                    <tr>
                        <td class="table_body" style="vertical-align: middle;"><?php echo ++$runno;?></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $row["prename_full"].$row["firstname_th"]." ".$row["lastname_th"];?></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $row["member_cremation_id"];?></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate($row["approve_date"],1,0);?></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $mem_type[$row["mem_type_id"]];?></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate($row["birth_day"],1,0);?></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $address;?></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $row["marry_name"];?></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $receiver;?></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $row["funeral_manager"];?></td>
                        <?php
                            if($row["input_name"] == "type") {
                        ?>
                        <td class="table_body" style="vertical-align: middle;"></td>
                        <td class="table_body" style="vertical-align: middle;"></td>
                        <td class="table_body" style="vertical-align: middle;"></td>
                        <td class="table_body" style="vertical-align: middle;"></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate($row["created_at"],1,0);?></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $row["resign_cause_name"];?></td>
                        <?php
                            } else {
                                $origin_value = $row["origin_value"];
                                $new_value = $row["new_value"];
                                if($row["input_name"] == "cur_province_id" || $row["input_name"] == "province_id") {
                                    $origin_value = !empty($origin_value) ? $this->db->select("province_name")->from("coop_province")->where("province_id = '".$origin_value."'")->get()->row()->province_name : "";
                                    $new_value = !empty($new_value) ? $this->db->select("province_name")->from("coop_province")->where("province_id = '".$new_value."'")->get()->row()->province_name : "";
                                } else if($row["input_name"] == "cur_amphur_id" || $row["input_name"] == "amphur_id") {
                                    $origin_value = !empty($origin_value) ? $this->db->select("amphur_name")->from("coop_amphur")->where("amphur_id = '".$origin_value."'")->get()->row()->amphur_name : "";
                                    $new_value = !empty($new_value) ? $this->db->select("amphur_name")->from("coop_amphur")->where("amphur_id = '".$new_value."'")->get()->row()->amphur_name : "";
                                } else if($row["input_name"] == "cur_district_id" || $row["input_name"] == "district_id") {
                                    $origin_value = !empty($origin_value) ? $this->db->select("district_name")->from("coop_district")->where("district_id = '".$origin_value."'")->get()->row()->district_name : "";
                                    $new_value = !empty($new_value) ? $this->db->select("district_name")->from("coop_district")->where("district_id = '".$new_value."'")->get()->row()->district_name : "";
                                }
                        ?>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $input_label[$row["input_name"]];?></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate($row["created_at"],1,0);?></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $origin_value;?></td>
                        <td class="table_body" style="vertical-align: middle;"><?php echo $new_value;?></td>
                        <td class="table_body" style="vertical-align: middle;"></td>
                        <td class="table_body" style="vertical-align: middle;"></td>
                        <?php
                            }
                        ?>
                        <td class="table_body" style="vertical-align: middle;"></td>
                    </tr>
                    <?php
                        }
                    ?>
				</tbody>
			</table>
		</body>
	</html>
</pre>