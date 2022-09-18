<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}		
	.table {
		color: #000;
	}
</style>		
	<?php
		//echo '<pre>'; print_r($_GET); echo '</pre>';
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
		
		$where = " AND t4.profile_month = '".$month."' AND t4.profile_year = '".$year."' AND t3.run_status = '1'";
		
		//coop_finance_month_detail ตารางเก็บข้อมูลทั้งหมด ที่เดือนนั้นๆ ต้องเรียกเก็บ
		// $this->db->select(array(
		// 	't1.member_id',
		// ));
		// $this->db->from('coop_mem_apply as t1');
		// $this->db->join("(SELECT run_status, profile_id, member_id FROM coop_finance_month_detail GROUP BY member_id) as t3","t1.member_id = t3.member_id","inner");
		// $this->db->join("coop_finance_month_profile as t4","t3.profile_id = t4.profile_id","inner");
		// $this->db->join("coop_receipt as t5","t4.profile_id = t5.finance_month_profile_id","inner");
		// $this->db->where("t1.mem_type = '1' {$where}");
		// $this->db->group_by('t1.member_id');
		// $rs_count = $this->db->get()->result_array();


		$rs_data = array();
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
		//echo $this->db->last_query(); echo '<hr>';
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
				$row['full_name'] = $member['prename_short'].$member['firstname_th'].'  '.$member['lastname_th'];
				$row['member_id'] = $member['member_id'];
				$row['runno'] = $runno;
				$rs_data[] = $row;
			}
			
		}
		$num_rows = count($rs_data);
		$page_limit = 40;			
		$page_all = @ceil($num_rows/$page_limit);
		$arr_total = array();	
		for($page = 1;$page<=$page_all;$page++){	
			$page_start = (($page-1)*$page_limit);
			$per_page = $page*$page_limit ;
	?>
		
		<div style="width: 1000px;"  class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1420px;">
				<table style="width: 100%;">
				<?php 
					if(@$page == 1){
				?>	
					<tr>
						<td style="width:100px;vertical-align: top;">
							
						</td>
						<td class="text-center">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">รายงานเงินเก็บได้ <?php echo " ประจำ ".@$title_date;?></h3>
							 <h3 class="title_view">หน่วยงานเบิกเงินเดือน </h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							<?php
								$get_param = '?';
								foreach(@$_GET as $key => $value){
									if($key != 'mem_type'){
										$get_param .= $key.'='.$value.'&';
									}
									
									if($key == 'mem_type'){
										foreach($value as $key2 => $value2){
											$get_param .= $key.'[]='.$value2.'&';
										}
									}	
								}
								$get_param = substr($get_param,0,-1);
								
							?>
							<a class="no_print"  target="_blank" href="<?php echo base_url('/report_processor_data/coop_report_pay_month_excel'.$get_param); ?>">
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
							<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></span>				
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
							<th rowspan="2" style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th rowspan="2" style="width: 40px;vertical-align: middle;">เลขที่สมาชิก</th>
							<th rowspan="2" style="width: 200px;vertical-align: middle;">ชื่อ-นามสกุล</th>
							<th colspan="3" style="width: 100px;vertical-align: middle;">จำนวนเงิน</th>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">วันที่เก็บได้</th> 
						</tr> 
						<tr>
							<th style="width: 80px;vertical-align: middle;">เงินหัก</th> 
							<th style="width: 80px;vertical-align: middle;">เก็บได้</th> 
							<th style="width: 80px;vertical-align: middle;">คงเหลือ</th> 
						</tr> 	
					</thead>
					<tbody>
					<?php	
						if(!empty($rs_data)){	
							foreach($rs_data as $index => $rs) {
								if ($page_start <= $index && $index < $page_start+$page_limit) {
					?>
							<tr> 
							  <td style="text-align: center;"><?php echo $rs['runno']; ?></td>
							  <td style="text-align: center;"><?php echo $rs['member_id']; ?></td>
							  <td style="text-align: left;"><?php echo $rs['full_name']; ?></td>									 
							  <td style="text-align: right;"><?php echo number_format($rs['pay_amount'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format($rs['real_pay_amount'],2);?></td> 						 
							  <td style="text-align: right;"><?php echo number_format($rs['pay_amount']-$rs['real_pay_amount'],2);?></td> 						 
							  <td style="text-align: center;"><?php echo $this->center_function->ConvertToThaiDate($rs['receipt_datetime'],0,0); ?></td> 						 
						  </tr>										
					
					<?php
								$pay_amount += $rs['pay_amount'];
								$real_pay_amount += $rs['real_pay_amount'];
								$balance += ($rs['pay_amount']-$rs['real_pay_amount']);
								}
							}
						}
						
						if($page == $page_all){	
					?>
						   <tr> 
							  <td style="text-align: center;" colspan="3"><?php echo "รวมทั้งสิ้น ".number_format($count_member)." รายการ";?></td>					 
							  <td style="text-align: right;"><?php echo number_format($pay_amount,2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format($real_pay_amount,2);?></td> 						 
							  <td style="text-align: right;"><?php echo number_format($balance,2);?></td> 						 
							  <td style="text-align: right;"></td> 						 
						  </tr>
					<?php } ?>	  
					</tbody>    
				</table>
			</div>
		</div>
		<?php } ?>