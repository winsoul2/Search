<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 16px;
	}		
	@page { 
		size: landscape; 			
	}
	.table {
		color: #000;
	}
	</style>		
<?php
if(@$_GET['report_date'] != ''){
	$date_arr = explode('/',@$_GET['report_date']);
	$day = (int)@$date_arr[0];
	$month = (int)@$date_arr[1];
	$year = (int)@$date_arr[2];
	$year -= 543;
	$file_name_text = $day."_".$month_arr[$month]."_".($year+543);
}else{
	if(@$_GET['month']!='' && @$_GET['year']!=''){
		$day = '';
		$month = @@$_GET['month'];
		$year = (@$_GET['year']-543);
		$file_name_text = @$month_arr[@$month]."_".(@$year+543);
	}else{
		$day = '';
		$month = '';
		$year = (@$_GET['year']-543);
		$file_name_text = (@$year+543);
	}
}

if($month!=''){
	$month_start = @$month;
	$month_end = @$month;
}else{
	$month_start = 1;
	$month_end = 12;
}

$where = '';
if(@$day != '' && @$month != ''){
	$s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
	$e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
	$where .= " AND createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
}else if($day == '' && $month != ''){
	$s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
	$e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
	$where .= " AND createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
}else{
	$where .= " AND createdatetime BETWEEN '".$year."-01-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";
}

$this->db->select(array('loan_id','contract_number','createdatetime','member_id','employee_id','prename_short','firstname_th','lastname_th','level','period_amount','loan_amount','money_period_1','loan_reason'));
$this->db->from('coop_report_loan_normal_excel_1');
$this->db->where("loan_type = '".@$_GET['loan_type']."' AND loan_status IN ('1','2','4') {$where}");
$this->db->order_by('createdatetime ASC');
$rs = $this->db->get()->result_array();
$data = array();
$i = 0;
if(!empty($rs)){
	foreach(@$rs as $key => $row){

		$this->db->select(array('date_period'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".@$row['loan_id']."'");
		$this->db->order_by('period_count ASC');
		$this->db->limit(1);
		$rs_period = $this->db->get()->result_array();
		$row_period  = @$rs_period[0];
		$first_period = @$row_period['date_period'];
		
		$this->db->select(array('date_period'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".@$row['loan_id']."'");
		$this->db->order_by('period_count DESC');
		$this->db->limit(1);
		$rs_period2 = $this->db->get()->result_array();
		$row_period2  = @$rs_period2[0];
		$last_period = @$row_period2['date_period'];
		
		$createdatetime = explode(' ',@$row['createdatetime']);
		$createdate = explode('-',@$createdatetime[0]);
		$create_month = (int)@$createdate[1];
		$data[@$create_month][@$row['loan_id']] = @$row;
		$data[@$create_month][@$row['loan_id']]['first_period'] = @$first_period;
		$data[@$create_month][@$row['loan_id']]['last_period'] = @$last_period;
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_guarantee');
		$this->db->where("loan_id = '".@$row['loan_id']."'");
		$this->db->order_by('guarantee_type ASC');
		$rs_guarantee = $this->db->get()->result_array();

		$loan_guarantee = array();
		if(!empty($rs_guarantee)){
			foreach($rs_guarantee as $key => $row_guarantee){
				$loan_guarantee[] = @$row_guarantee;
			}
		}
		$data[@$create_month][@$row['loan_id']]['loan_guarantee'] = @$loan_guarantee;
	}
}
//echo"<pre>";print_r($data);exit;
for($m = $month_start; $m <= $month_end; $m++){
$i++;	
?>
		
		<div style="width: 1500px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;height: 1000px;">
				<table style="width: 100%;">
					<tr>
						<td style="width:100px;vertical-align: top;">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
						</td>
						<td class="text-center">
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view"><?php echo "ทะเบียน".@$loan_type[@$_GET['loan_type']]."  เดือน  ".@$month_arr[$m]." ".(@$year+543);?></h3>
							 <p>&nbsp;</p>	
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<?php if($i == '1'){?>
								<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
								<a class="no_print" onclick="export_excel('<?php echo @$_GET['loan_type']?>','<?php echo @$_GET['year']?>');"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>	
							<?php } ?>
						 </td>
					</tr> 
					<tr>
						<td colspan="3">
							<h3 class="title_view">
							</h3>
						</td>
					</tr> 
				</table>
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th rowspan="3" style="width: 100px;vertical-align: middle;">หนังสือกู้สำหรับ<br><?php echo $loan_type[@$_GET['loan_type']];?><br>ที่</th>
							<th rowspan="3" style="width: 80px;vertical-align: middle;">วันที่</th>
							<th rowspan="3" style="width: 80px;vertical-align: middle;">สมาชิก<br>เลขทะเบียน</th>
							<th rowspan="3" style="width: 80px;vertical-align: middle;">รหัส<br>พนักงาน</th> 
							<th rowspan="3" style="width: 200px;vertical-align: middle;">ผู้กู้<br>ชื่อ-สกุล</th> 
							<th rowspan="3" style="width: 200px;vertical-align: middle;">หน่วย<br>งาน</th> 
							<th rowspan="3" style="width: 85px;vertical-align: middle;">ระยะ<br>การชำระ<br>(งวด)</th> 
							<th rowspan="3" style="width: 85px;vertical-align: middle;">จำนวน<br>เงินกู้<br>(บาท)</th> 
							<th colspan="3" style="width: 85px;vertical-align: middle;">การส่งเงินงวดชำระหนี้</th> 
							<th colspan="2" style="width: 85px;vertical-align: middle;">หนังสือค้ำประกัน</th> 
							<th rowspan="3" style="width: 85px;vertical-align: middle;">ผู้ค้ำประกัน<br>ชื่อ-สกุล</th> 
							<th rowspan="3" style="width: 85px;vertical-align: middle;">สมาชิกเลข<br>ทะเบียน</th> 
							<th rowspan="3" style="width: 85px;vertical-align: middle;">หน่วย<br>งาน</th> 
							<th rowspan="3" style="width: 85px;vertical-align: middle;">หมายเหตุ</th> 
						</tr> 
						<tr>
							<th style="width: 85px;vertical-align: middle;">งวดละ</th>
							<th style="width: 85px;vertical-align: middle;">ตั้งแต่</th>
							<th style="width: 85px;vertical-align: middle;">ถึง</th> 
							<th style="width: 85px;vertical-align: middle;">ที่</th> 
							<th style="width: 85px;vertical-align: middle;">วันที่</th> 
						</tr> 
					</thead>
					<tbody>
					  <?php 
						$count_loan = 0;
						$loan_amount=0;
						//echo '<pre>'; print_r(@$data[$m]); echo '</pre>';
						if(!empty($data[$m])){
							foreach($data[$m] as $key => $value){
								$i+=1;
								$loan_amount += @$value['loan_amount'];							
						?>
						  <tr> 
							  <td style="text-align: center;"><?php echo @$value['contract_number']?></td>
							  <td style="text-align: center;"><?php echo $this->center_function->mydate2date(@$row['createdatetime']); ?></td>						 
							  <td style="text-align: left;"><?php echo @$value['member_id']; ?></td>						 
							  <td style="text-align: left;"><?php echo @$value['employee_id']; ?></td> 							 
							  <td style="text-align: left;"><?php echo @$value['prename_short'].@$value['firstname_th'].'  '.@$value['lastname_th']; ?></td> 							 
							  <td style="text-align: left;"><?php echo @$mem_group_arr[@$value['level']]; ?></td> 							 
							  <td style="text-align: center;"><?php echo @$value['period_amount']; ?></td> 						 
							  <td style="text-align: right;"><?php echo number_format(@$value['loan_amount'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$value['money_period_1'],2);?></td> 					 
							  <td style="text-align: center;"><?php echo @$month_short_arr[(int)date('m',strtotime(@$value['first_period']))]." ".substr((date('Y',strtotime(@$value['first_period']))+543),2,2);?></td> 					 
							  <td style="text-align: center;"><?php echo @$month_short_arr[(int)date('m',strtotime(@$value['last_period']))]." ".substr((date('Y',strtotime(@$value['last_period']))+543),2,2);?></td> 
							<?php							
							if(!empty($value['loan_guarantee'])){
								foreach(@$value['loan_guarantee'] as $key2 => $value2){
									if(@$value2['guarantee_type'] == '1'){									
										$loan_guarantee = array();
										$this->db->select(array('guarantee_person_id','prename_short','firstname_th','lastname_th','level','guarantee_person_contract_number'));
										$this->db->from('guarantee_person_view');
										$this->db->where("loan_id = '".@$value['loan_id']."'");
										$this->db->order_by('id ASC');
										$rs_guarantee = $this->db->get()->result_array();					
										if(!empty($rs_guarantee_person)){
											foreach(@$rs_guarantee_person as $key => $row_guarantee_person){	
								?>
											<td style="text-align: center;"><?php echo @$row_guarantee_person['guarantee_person_contract_number'];?></td>
											<td style="text-align: center;"><?php echo $this->center_function->mydate2date(@$value['createdatetime']);?></td>
											<td style="text-align: left;"><?php echo @$row_guarantee_person['prename_short'].@$row_guarantee_person['firstname_th']." ".@$row_guarantee_person['lastname_th'];?></td>
											<td style="text-align: left;"><?php echo @$row_guarantee_person['guarantee_person_id'];?></td>
											<td style="text-align: left;"><?php echo @$mem_group_arr[@$row_guarantee_person['level']];?></td>
								<?php	
											}
										}else{
								?>
											<td style="text-align: center;"></td>
											<td style="text-align: center;"></td>
											<td style="text-align: left;"></td>
											<td style="text-align: left;"></td>
											<td style="text-align: left;"></td>
								<?php			
										}
										
									}else if(@$value2['guarantee_type'] == '2'){
								?>
											<td style="text-align: center;"></td>
											<td style="text-align: center;"></td>
											<td style="text-align: left;"><?php echo (@$value2['other_price']=='')?'ใช้หุ้นค้ำประกัน':'ใช้หุ้น+กองทุนฯส่วนของพนักงานค้ำประกัน';?></td>
											<td style="text-align: left;"></td>
											<td style="text-align: left;"></td>
								<?php											
									}else{
								?>
									<td style="text-align: center;"></td>
									<td style="text-align: center;"></td>
									<td style="text-align: left;"></td>
									<td style="text-align: left;"></td>
									<td style="text-align: left;"></td>
								<?php	
									}
								}
							}else{
							?>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: left;"></td>
								<td style="text-align: left;"></td>
								<td style="text-align: left;"></td>
							<?php	
							}
							  
							?>
							  <td style="text-align: left;"><?php echo @$value['loan_reason'];?></td> 			 						 
						  </tr>
					<?php 
							$count_loan++;
							}
						} 
					?>							
					</tbody>  
				</table>
				
				<table style="width: 100%;" class="m-t-2">
					<tr>
						<td style="width: 200px;"></td>
						<td style="width: 150px;"><h3 class="title_view"><?php echo "เดือน ".$month_arr[$m];?></h3></td>
						<td style="width: 40px;"><h3 class="title_view"><?php echo "รวม " ;?></h3></td>
						<td style="width: 50px;    text-align: center;"><h3 class="title_view"><?php echo number_format($count_loan);?></h3></td>
						<td style="width: 150px;"><h3 class="title_view"><?php echo "สัญญา ";?></h3></td>
						<td style="width: 110px;"><h3 class="title_view"><?php echo "เป็นเงินจำนวน " ;?></h3></td>
						<td style="width: 150px;    text-align: center;"><h3 class="title_view"><?php echo number_format($loan_amount) ;?></h3></td>
						<td style="width: 50px;"><h3 class="title_view"><?php echo "บาท " ;?></h3></td>
						<td></td>
					</tr>
				</table>
			</div>
		</div>
<?php } ?>	

<script>
function export_excel(loan_type,year){					
	window.open('coop_report_loan_normal_excel?loan_type='+loan_type+'&year='+year,'_blank');
	window.open('coop_report_loan_normal_excel?loan_type='+loan_type+'&year='+year+'&second_half=1','_blank');	
}
</script>	