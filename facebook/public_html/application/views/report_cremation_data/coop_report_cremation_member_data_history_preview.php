<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}
	.table-view-2>thead>tr>th{
	    border-top: 1px solid #000 !important;
		border-bottom: 1px solid #000 !important;
		font-size: 16px;
	}
	.table-view-2>tbody>tr>td{
	    border: 0px !important;
		font-family: Tahoma;
		font-size: 12px;
	}	
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}
	.table-view-2>tbody>tr>td>span{
		font-family: Tahoma;
		font-size: 12px !important;
	}
	.foot-border{
	    border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}
	.table {
		color: #000;
	}
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		padding:6px;
	}
</style>
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

	foreach($datas AS $page=>$data){
?>
<div style="width: 11.69in;" class="page-break">
	<div class="panel panel-body" style="padding-top:10px !important;height: 8.27in;">
		<table style="width: 100%;">
		<?php 
			if(@$page == 1){
		?>	
			<tr>
				<td style="width:100px;vertical-align: top;">

				</td>
				<td class="text-center">
					<img src="<?php echo base_url(PROJECTPATH.$this->logo_path); ?>" alt="Logo" style="height: 80px;" />
                    <h3 class="title_view">แบบทะเบียนสมาชิก</h3>
					<h3 class="title_view">สมาคมฌาปนกิจสงเคราะห์<?php echo @$_SESSION['COOP_NAME'];?></h3>
				</td>
				<td style="width:100px;vertical-align: top;" class="text-right">
					<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
					<?php
						$get_param = '?';
						foreach(@$_GET as $key => $value){
							$get_param .= $key.'='.$value.'&';
						}
						$get_param = substr($get_param,0,-1);
					?>
					<a class="no_print"  target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_cremation_data/coop_report_member_data_history_excel'.$get_param); ?>">
						<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
					</a>
				</td>
			</tr>
		<?php } ?>
			<tr>
				<td colspan="3" style="text-align: right;">
					<span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>
				</td>
			</tr> 
			<tr>
				<td colspan="3" style="text-align: right;">
					<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>
				</td>
			</tr> 
			<tr>
				<td colspan="3" style="text-align: right;">
					<span class="title_view">เวลา <?php echo date('H:i:s');?></span>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: right;">
					<span class="title_view">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
				</td>
			</tr>
		</table>

		<table class="table table-view table-center">
			<thead>
				<tr>
					<th rowspan="2" style="vertical-align: middle; width:4%;">ลำดับ</th>
					<th rowspan="2" style="vertical-align: middle; width:6%;">ชื่อสมาชิก</th>
					<th rowspan="2" style="vertical-align: middle; width:6%;">เลขประจำตัวสมาชิก</th>
					<th rowspan="2" style="vertical-align: middle; width:6%;">วันเดือนปีที่เข้าเป็นสมาชิก</th>
					<th rowspan="2" style="vertical-align: middle; width:6%;">ประเภทสมาชิก</th>
					<th rowspan="2" style="vertical-align: middle; width:6%;">วันเดือนปีเกิด</th>
					<th rowspan="2" style="vertical-align: middle; width:6%;">ที่อยู่ปัจจุบัน</th>
					<th rowspan="2" style="vertical-align: middle; width:6%;">ชื่อสามีหรือภรรยา</th>
					<th rowspan="2" style="vertical-align: middle; width:6%;">ชื่อผู้รับเงินสงเคราะห์ที่ระบุไว้</th>
					<th rowspan="2" style="vertical-align: middle; width:6%;">ชื่อผู้จัดการศพที่ระบุไว้</th>
					<th rowspan="2" style="vertical-align: middle; width:6%;">เรื่องที่เปลี่ยนแปลง</th>
					<th colspan="3" style="vertical-align: middle; width:18%;">การเปลี่ยนแปลงกรณีทั่วไป</th>
					<th colspan="2" style="vertical-align: middle; width:12%;">การเปลี่ยนแปลงกรณีพ้นจากสมาชิกภาพ</th>
					<th rowspan="2" style="vertical-align: middle; width:6%;">หมายเหตุ</th>
				</tr>
                <tr>
                    <th style="vertical-align: middle; width:6%;">วันเดือนปีที่เปลี่ยนแปลง</th>
                    <th style="vertical-align: middle; width:6%;">เดิม</th>
                    <th style="vertical-align: middle; width:6%;">เปลี่ยนเป็น</th>
                    <th style="vertical-align: middle; width:6%;">วันเดือนปีที่พ้นจากสมาชิกภาพ</th>
                    <th style="vertical-align: middle; width:6%;">สาเหตุ</th>
                </tr>
			</thead>
			<tbody>
                <?php
                    foreach($data as $row) {
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
                    <td class="text-center" style="vertical-align: middle;"><?php echo ++$runno;?></td>
                    <td class="text-left" style="vertical-align: middle;"><?php echo $row["prename_full"].$row["firstname_th"]." ".$row["lastname_th"];?></td>
                    <td class="text-center" style="vertical-align: middle;"><?php echo $row["member_cremation_id"];?></td>
                    <td class="text-center" style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate($row["approve_date"],1,0);?></td>
                    <td class="text-center" style="vertical-align: middle;"><?php echo $mem_type[$row["mem_type_id"]];?></td>
                    <td class="text-center" style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate($row["birth_day"],1,0);?></td>
                    <td class="text-left" style="vertical-align: middle;"><?php echo $address;?></td>
                    <td class="text-left" style="vertical-align: middle;"><?php echo $row["marry_name"];?></td>
                    <td class="text-left" style="vertical-align: middle;"><?php echo $receiver;?></td>
                    <td class="text-left" style="vertical-align: middle;"><?php echo $row["funeral_manager"];?></td>
                    <?php
                        if($row["input_name"] == "type") {
                    ?>
                    <td class="text-center" style="vertical-align: middle;"></td>
                    <td class="text-center" style="vertical-align: middle;"></td>
                    <td class="text-center" style="vertical-align: middle;"></td>
                    <td class="text-center" style="vertical-align: middle;"></td>
                    <td class="text-center" style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate($row["created_at"],1,0);?></td>
                    <td class="text-center" style="vertical-align: middle;"><?php echo $row["resign_cause_name"];?></td>
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
                    <td class="text-center" style="vertical-align: middle;"><?php echo $input_label[$row["input_name"]];?></td>
                    <td class="text-center" style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate($row["created_at"],1,0);?></td>
                    <td class="text-center" style="vertical-align: middle;"><?php echo $origin_value;?></td>
                    <td class="text-center" style="vertical-align: middle;"><?php echo $new_value;?></td>
                    <td class="text-center" style="vertical-align: middle;"></td>
                    <td class="text-center" style="vertical-align: middle;"></td>
                    <?php
                        }
                    ?>
                    <td class="text-center" style="vertical-align: middle;"></td>
                </tr>
                <?php
                    }
                ?>
			</tbody>
		</table>
	</div>
</div>
<?php
	}
?>