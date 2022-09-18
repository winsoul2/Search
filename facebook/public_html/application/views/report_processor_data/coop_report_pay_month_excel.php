<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานเงินเก็บได้.xls"); 
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
	if(@$_GET['month']!='' && @$_GET['year']!=''){
		$day = '';
		$month = @$_GET['month'];
		$year = (@$_GET['year']);
		$title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year);
	}else{
		$day = '';
		$month = '';
		$year = (@$_GET['year']);
		$title_date = " ปี ".(@$year);
	}

	?>
			<table class="table table-bordered">
				<tr>
					<tr>
						<th class="table_title" colspan="6"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="6">รายงานเงินเก็บได้</th>
					</tr>
					<tr>
						<th class="table_title" colspan="6"><?php echo " ประจำ ".$title_date;?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="6">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="6">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>

			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="table_header_top" rowspan="2" style="width: 40px;vertical-align: middle;">ลำดับ</th>
						<th class="table_header_top" rowspan="2" style="width: 40px;vertical-align: middle;">เลขที่สมาชิก</th>
						<th class="table_header_top" rowspan="2" style="width: 200px;vertical-align: middle;">ชื่อ-นามสกุล</th>
						<th class="table_header_top" colspan="3" style="width: 100px;vertical-align: middle;">จำนวนเงิน</th>
						<th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">วันที่เก็บได้</th> 
					</tr> 
					<tr>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;">เงินหัก</th> 
						<th class="table_header_top" style="width: 80px;vertical-align: middle;">เก็บได้</th> 
						<th class="table_header_top" style="width: 80px;vertical-align: middle;">คงเหลือ</th> 
					</tr> 
				</thead>
				<tbody>
					<?php

						$runno = 0;
						$count_member = 0;
						$pay_amount = 0;
						$real_pay_amount = 0;
						$balance = 0;

						$finannce_profile = $this->db->from("coop_finance_month_profile")
													->where("profile_month = '".$month."' AND profile_year = '".$year."'")
													->get()->row();
						$where = " AND t4.profile_month = '".$month."' AND t4.profile_year = '".$year."' AND t3.run_status = '1'";

						$members = $this->db->select(array('t1.member_id',
															't1.id_card',
															't1.prename_id',
															't1.firstname_th',
															't1.lastname_th',
															't1.level',
															't2.prename_short',
														))
											->from('coop_mem_apply as t1')
											->join("coop_prename as t2","t1.prename_id = t2.prename_id","left")
											->where("t1.member_status <> 3")
											->order_by("t1.member_id ASC")
											->get()->result_array();
											

						foreach($members as $member) {

							$details = $this->db->select(array(
													't3.pay_amount',
													't3.real_pay_amount',
													't3.member_id',
													't5.receipt_datetime'
												))
										->from("(SELECT run_status, member_id, pay_amount, real_pay_amount FROM coop_finance_month_detail WHERE profile_id = '".$finannce_profile->profile_id."' AND member_id = '".$member['member_id']."') as t3")
										->join("(SELECT * FROM coop_receipt WHERE finance_month_profile_id = '".$finannce_profile->profile_id."') as t5","t3.member_id = t5.member_id","inner")
										->order_by('t3.member_id')
										->get()->result_array();

							$row = array();
							if(!empty($details)){
								$count_member++;
								foreach(@$details as $key => $detail){
									$row['pay_amount'] += $detail["pay_amount"]; 
									$row['real_pay_amount'] += $detail["real_pay_amount"]; 
									$row['receipt_datetime'] = $detail["receipt_datetime"]; 
								}
								$runno++;	
								$full_name = $member['prename_short'].$member['firstname_th'].'  '.$member['lastname_th'];	
					?>
					<tr>
						<td class="table_body" style="text-align: center;"><?php echo $runno; ?></td>
						<td class="table_body" style="text-align: center;"><?php echo $member['member_id']; ?></td>
						<td class="table_body" style="text-align: left;"><?php echo @$full_name; ?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($row['pay_amount'],2);?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($row['real_pay_amount'],2);?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($row['pay_amount']-$row['real_pay_amount'],2);?></td>
						<td class="table_body" style="text-align: center;"><?php echo $this->center_function->ConvertToThaiDate($row['receipt_datetime'],0,0); ?></td>
					</tr>
					<?php
								$pay_amount += $row['pay_amount'];
								$real_pay_amount += $row['real_pay_amount'];
								$balance += ($row['pay_amount']-$row['real_pay_amount']);

							}
						}
					?>
					<tr>
						<td class="table_body" style="text-align: center;" colspan="3"><?php echo "รวมทั้งสิ้น ".number_format($count_member)." รายการ";?></td>					 
						<td class="table_body" style="text-align: right;"><?php echo number_format($pay_amount,2);?></td> 					 
						<td class="table_body" style="text-align: right;"><?php echo number_format($real_pay_amount,2);?></td> 						 
						<td class="table_body" style="text-align: right;"><?php echo number_format($balance,2);?></td> 						 
						<td class="table_body" style="text-align: right;"></td> 	
					</tr>
					<?php exit;?>
				</tbody>
			</table>
		</body>
	</html>
</pre>