		<?php
			$param = '';
			if(!empty($_GET)){
				foreach($_GET AS $key=>$val){
					$param .= $key.'='.$val.'&';
				}
			}
			
			if(@$_GET['report_date'] != ''){
				$date_arr = explode('/',@$_GET['report_date']);
				$day = (int)$date_arr[0];
				$month = (int)$date_arr[1];
				$year = (int)$date_arr[2];
				$year -= 543;
				$file_name_text = @$day."_".@$month_arr[$month]."_".(@$year+543);
				$title_date = "วันที่ ".@$day." เดือน ".@$month_arr[$month]." ปี ".(@$year+543);
			}else{
				if(@$_GET['month']!='' && @$_GET['year']!=''){
					$day = '';
					$month = @$_GET['month'];
					$year = (@$_GET['year']-543);
					$file_name_text = @$month_arr[$month]."_".(@$year+543);
					$title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year+543);
				}else{
					$day = '';
					$month = '';
					$year = (@$_GET['year']-543);
					$file_name_text = (@$year+543);
					$title_date = " ปี ".(@$year+543);
				}
			}

			if($month!=''){
				$month_start = @$month;
				$month_end = @$month;
			}else{
				$month_start = 1;
				$month_end = 12;
			}
			$i=0;
			$page_now = 0;
			for($m = $month_start; $m <= $month_end; $m++){
				$i++;
				
				//echo $m.'|'.$month_end.'<hr>';
				if($day!=''){
					$day_start = $day;
					$day_end = $day;
				}else{
					$day_start = '1';
					$day_end = date('t',strtotime($year."-".sprintf("%02d",$m)."-01"));
				}

		?>
		<style>
			.table {
				color: #000;
			}
		</style>
		<div style="width: 1000px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1420px;">
				<table style="width: 100%;">
					<tr>
						<td style="width:100px;vertical-align: top;">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
						</td>
						<td class="text-center">
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">รายงานสรุปการลาออก</h3>
							 <h3 class="title_view">
								<?php echo @$title_date;?>
							</h3>
							 <p>&nbsp;</p>	
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							<a href="<?php echo base_url(PROJECTPATH.'/report_member_data/coop_report_member_retire_excel?'.$param); ?>" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>	
						 </td>
					</tr> 
					<tr>
						<td colspan="3">
							<h3 class="title_view">
							<?php
								if(@$_GET['year']!='' && @$_GET['month']==''){
									echo "วันที่ ".$d." เดือน ".@$month_arr[$m]." ปี ".(@$year+543);
								}							
							?>	
							</h3>
						</td>
					</tr> 
				</table>
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th style="width: 40px;vertical-align: middle;">ลำดับที่</th>
							<th style="width: 90px;vertical-align: middle;">วันที่ลาออก</th>
							<th style="width: 80px;vertical-align: middle;">เลขทะเบียนสมาชิก</th>
							<th style="width: 140px;vertical-align: middle;">ชื่อ - สกุล</th> 
							<th style="width: 210px;vertical-align: middle;">หน่วยงาน</th> 
							<th style="width: 80px;vertical-align: middle;">เงินค่าหุ้น สะสม(บาท)</th> 
							<th style="width: 80px;vertical-align: middle;">เงินค้างชำระ</th> 
							<th style="vertical-align: middle;">เหตุผลในการลาออก</th> 
						</tr> 
					</thead>
					<tbody id="table_first">
					  <?php 
					  $j=1;
					  $share_sum = 0;
					  $loan_sum = 0;
				for($d = $day_start; $d <= $day_end; $d++){
					
					$this->db->select(array('t2.member_id'));
					$this->db->from('coop_mem_req_resign as t1');
					$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
					$this->db->where("t1.req_resign_status = '1' AND t1.resign_date LIKE '".$year.'-'.sprintf("%02d",$m).'-'.sprintf("%02d",$d)."%'");
					$rs_check = $this->db->get()->result_array();
					
					$check_num = 0;
					
					if(!empty($rs_check)){
						foreach($rs_check as $key => $row_check){
							$check_num++;							
						}
						$page_now++;
					}	
					if(@$check_num == 0  && @$_GET['report_date']==''){
						continue;
					}
						$this->db->select(array('t1.resign_date','t2.member_id','t2.employee_id','t3.prename_short','t2.firstname_th','t2.lastname_th','t2.level','t4.resign_cause_name'));
						$this->db->from('coop_mem_req_resign as t1');
						$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
						$this->db->join('coop_prename as t3','t2.prename_id = t3.prename_id','left');
						$this->db->join('coop_mem_resign_cause as t4','t1.resign_cause_id = t4.resign_cause_id','left');
						$this->db->where("t1.req_resign_status = '1' AND resign_date LIKE '".$year.'-'.sprintf("%02d",$m).'-'.sprintf("%02d",$d)."%'");
						$rs = $this->db->get()->result_array();
						
						

						if(!empty($rs)){
							foreach($rs as $key => $row){
								$share_num = 0;
								$this->db->select(array('share_collect'));
								$this->db->from('coop_mem_share');
								$this->db->where("member_id = '".$row['member_id']."' AND share_status IN('1','2')");
								$this->db->order_by('share_id DESC');
								$this->db->limit(1);
								$rs_share = $this->db->get()->result_array();
								$row_share  = @$rs_share[0];
								$share_num = @$row_share['share_collect']*@$share_value;
								$share_sum += @$share_num;
						
								$loan_num = 0;
								$this->db->select(array('loan_amount_balance'));
								$this->db->from('coop_loan');
								$this->db->where("member_id = '".$row['member_id']."' AND loan_status = '1'");
								$rs_loan = $this->db->get()->result_array();
						
								if(!empty($rs_loan)){
									foreach($rs_loan as $key => $row_loan){
										$loan_num += @$row_loan['loan_amount_balance'];
									}
								}
								$loan_sum += $loan_num;
						?>
						  <tr> 
							  <td style="text-align: center;"><?php echo @$j++;?></td>
							  <td style="text-align: center;"><?php echo @$row['resign_date'];?></td>
							  <td style="text-align: center;"><?php echo @$row['member_id']; ?></td>						 
							  <td style="text-align: left;"><?php echo @$row['prename_short'].@$row['firstname_th'].'  '.@$row['lastname_th']; ?></td>						 
							  <td style="text-align: left;"><?php echo @$mem_group_arr[@$row['level']]; ?></td> 							 
							  <td style="text-align: right;"><?php echo number_format(@$share_num,2); ?></td> 							 
							  <td style="text-align: right;"><?php echo number_format(@$loan_num,2); ?></td> 							 
							  <td style="text-align: left;"><?php echo @$row['resign_cause_name']; ?></td> 							 
						  </tr>
					<?php 
							}
						} 
					}
					?>							
					</tbody> 
					<tfoot> 
						<tr> 
							  <td></td> 							 
							  <td></td> 							 
							  <td></td> 							 
							  <td></td> 							 
							  <td></td> 							 
							  <td style="text-align: right;border-bottom: 3px double #000 !important;"><?php echo number_format($share_sum,2); ?></td> 							 
							  <td style="text-align: right;border-bottom: 3px double #000 !important;"><?php echo number_format($loan_sum,2); ?></td> 							 
							  <td></td> 							 
						</tr>
					</tfoot> 
				</table>
			</div>
		</div>
		
		<?php 

			}
		?>