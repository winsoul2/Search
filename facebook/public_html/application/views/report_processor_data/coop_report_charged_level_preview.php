<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}		
	.table {
		color: #000;
	}
	@page { size: landscape; }
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
		
		$where = "";
		
		//if(@$_GET['department'] == ''){
		//	$where .= " AND  mem_group_type = '3'";
		//}
		//echo $where.'<hr>';
		if(@$_GET['department'] != ''  && @$_GET['faction'] == ''  && @$_GET['level'] == ''){
			$where .= " AND mem_group_parent_id = '{$_GET['department']}'";
		}else if(@$_GET['department'] != '' && @$_GET['faction'] != '' && @$_GET['level'] == ''){
			$where .= " AND id = '{$_GET['faction']}'";
		}else if(@$_GET['department'] != '' && @$_GET['faction'] != '' && @$_GET['level'] != ''){
			$where .= " AND id = '{$_GET['level']}'";
		}	
		
		$rs_group_parent = $this->db->select(array('mem_group_id','mem_group_name','id','mem_group_parent_id'))->from('coop_mem_group')->where("1=1 {$where}")->get()->result_array();
		$arr_group_parent = array();
		foreach($rs_group_parent AS $key=>$value){
			$arr_group_parent[] = $value['id'];
		}
		
		$where_group = "";
		if(@$_GET['department'] != ''  && @$_GET['faction'] == ''  && @$_GET['level'] == ''){
			$where_group .= " AND mem_group_parent_id IN (".implode(",",$arr_group_parent).")";
		}else if(@$_GET['department'] != '' && @$_GET['faction'] != '' && @$_GET['level'] == ''){
			$where_group .= " AND mem_group_parent_id IN (".implode(",",$arr_group_parent).")";
		}else if(@$_GET['department'] != '' && @$_GET['faction'] != '' && @$_GET['level'] != ''){
			$where_group .= " AND id = '{$_GET['level']}'";
		}else{
			$where_group .= " AND  mem_group_type = '3'";
		}	
		
		$rs_count = $this->db->select(array('id'))->from('coop_mem_group')->where("1=1  {$where_group}")->get()->result_array();
		//echo $this->db->last_query(); echo '<hr>';
		//echo '<pre>'; print_r($rs_count); echo '</pre>';
		$num_rows = count($rs_count);
		$page_limit = 20;			
		$page_all = @ceil($num_rows/$page_limit);
		$total_data = array();
		$member_share_total = 0;
		$member_total = 0;
		
		$page_limit_first = 18;
		$page_link_limit = 20;
		$diff_limit = $page_link_limit-$page_limit_first;
		
		for($page = 1;$page<=$page_all;$page++){
			if($page == 1){	
				$page_limit = $page_limit_first;
				$page_start = (($page-1)*$page_limit);
				$per_page = $page*$page_limit;
			}else{	
				$page_limit = $page_link_limit;
				$page_start = (($page-1)*$page_limit)-$diff_limit;
				$per_page = $page*$page_limit-$diff_limit;
			}
	?>
		
		<div style="width: 1500px;"  class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;height: 950px;">
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
							 <h3 class="title_view">สรุปรายการเรียกเก็บ แยกตามหน่วยงาน</h3>
							 <h3 class="title_view">
								<?php echo " ประจำ ".@$title_date;?>
							</h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
						</td>
					</tr> 
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></span>				
						</td>
					</tr>  
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
						</td>
					</tr>  
				<?php } ?>
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>						
						</td>
					</tr> 
				</table>
			
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th rowspan="2" style="width: 40px;vertical-align: middle;">รหัส</th>
							<th rowspan="2" style="width: 300px;vertical-align: middle;">หน่วยงาน</th>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">จำนวนสมาชิก</th> 
							<th colspan="2" style="width: 80px;vertical-align: middle;">หุ้น</th>
							<?php 
							foreach($loan_type AS $key=>$row_loan_type){
							?>
							<th colspan="3" style="width: 80px;vertical-align: middle;"><?php echo str_replace('เงินกู้','',$row_loan_type['loan_type']);?></th> 
							<?php } ?>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">เงินฝาก</th> 
							<th rowspan="2" style="width: 80px;vertical-align: middle;">ฌสอ สป</th> 
							<th rowspan="2" style="width: 80px;vertical-align: middle;">ค่าธรรมเนียมแรกเข้า</th> 
							<th rowspan="2" style="width: 80px;vertical-align: middle;">อื่นๆ</th> 
							<th rowspan="2" style="width: 80px;vertical-align: middle;">ชำระหนี้ค้ำประกัน</th> 
							<th rowspan="2" style="width: 80px;vertical-align: middle;">รวม</th> 
						</tr> 
						<tr>
							<th style="width: 80px;vertical-align: middle;">จำนวน</th>
							<th style="width: 80px;vertical-align: middle;">จำนวนเงิน</th>
							<th style="width: 80px;vertical-align: middle;">จำนวน</th> 
							<th style="width: 80px;vertical-align: middle;">เงินต้น</th> 
							<th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th> 
							<th style="width: 80px;vertical-align: middle;">จำนวน</th> 
							<th style="width: 80px;vertical-align: middle;">เงินต้น</th> 
							<th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th> 
							<th style="width: 80px;vertical-align: middle;">จำนวน</th> 
							<th style="width: 80px;vertical-align: middle;">เงินต้น</th> 
							<th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th> 
						</tr> 
					</thead>
					<tbody>
					<?php	
						$rs = $this->db->select(array('mem_group_id','mem_group_name','id','mem_group_parent_id'))
									->from('coop_mem_group')
									->where("1=1 {$where_group}")
						//$this->db->where("mem_group_parent_id IN (".implode(",",$arr_group_parent).")");
									->order_by(" mem_group_id ASC")
									->limit($page_limit, $page_start)
									->get()->result_array();
						//echo $this->db->last_query(); echo '<hr>';
						$date_range['start'] 	= date("Y-m", strtotime(($year-543)."-".$month."-01")	) . "-01";
						if(!empty($rs)){
							$member_where = "";
							if (!empty($_GET["mem_type"])){
								$member_where .= " AND mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
							}
							foreach(@$rs as $key => $row){
								//จำนวนสมาชิก
								$rs_department = $this->db->select(array('COUNT(department) AS count_department'))
											->from('coop_mem_apply')
											->where("level = '".$row['id']."'".$member_where)
											->get()->result_array();
								$count_department = $rs_department[0]['count_department'];
								$total_data['count_department'] += $count_department;

								$rs_data = $this->db->select(array('t3.member_id', 't3.deduct_code', 't3.loan_id', 't3.loan_atm_id', 't3.pay_type', "SUM(t3.pay_amount) as sum_pay_amount",'t1.level'))
												->from("(SELECT member_id, 
												IF((select level_old from coop_mem_group_move where coop_mem_apply.member_id = coop_mem_group_move.member_id AND date_move >= '".$date_range['start']."' ORDER BY date_move ASC limit 1),
												(select level_old from coop_mem_group_move where coop_mem_apply.member_id = coop_mem_group_move.member_id AND date_move >= '".$date_range['start']."' ORDER BY date_move ASC limit 1),
												`level`)  as level FROM coop_mem_apply WHERE 1=1  AND member_status <> 3 {$member_where}) as t1")
												->join("coop_finance_month_profile as t2","t2.profile_month = '".$month."' AND t2.profile_year = '".$year."'","inner")
												->join("coop_finance_month_detail as t3","t1.member_id = t3.member_id AND t2.profile_id = t3.profile_id","inner")
												->where("t1.level = '".$row['id']."'  AND t3.pay_amount > 0 ")
												// ->from("(SELECT * FROM coop_finance_month_profile WHERE profile_month = '".$month."' AND profile_year = '".$year."') as t2")
												// ->join("coop_finance_month_detail as t3","t2.profile_id = t3.profile_id","inner")
												->group_by("t3.deduct_code, t3.member_id, t3.pay_type, t3.loan_id, t3.loan_atm_id")
												->order_by("t3.member_id")
												->get()->result_array();
								//if(@$_GET['dev'] == 'dev'){
									//echo $this->db->last_query(); echo '<hr>';
								//}
								
								$array_data = array();

								$member_share = 0;
								$member_count = 0;

								$rs_data_count = count($rs_data);
								$rs_data_index = 0;

								foreach($rs_data as $key_data => $value_data){
									$is_cal = false;
									if($value_data['deduct_code']=='LOAN'){
										$loan_where = "1=1";
										if ($_GET['term_of_loan']) {
											$loan_where .= " AND t1.loan_type = ".$_GET['term_of_loan'];
										}
										$rs_loan = $this->db->select(array('t3.id as loan_type_id'))
														->from("(SELECT * FROM coop_loan WHERE id = '".$value_data['loan_id']."') as t1")
														->join('coop_loan_name as t2','t1.loan_type = t2.loan_name_id','inner')
														->join('coop_loan_type as t3','t2.loan_type_id = t3.id','inner')
														->where($loan_where)
														->get()->result_array();
										if(!empty($rs_loan)) {
											$array_data[$value_data['deduct_code']][$rs_loan[0]['loan_type_id']]['loan_id'][$value_data['loan_id']] = 1;
											$total_data[$value_data['deduct_code']][$rs_loan[0]['loan_type_id']]['loan_id'][$value_data['loan_id']] = 1;
											if($value_data['pay_type']=='principal'){
												$array_data[$value_data['deduct_code']][$rs_loan[0]['loan_type_id']]['principal'] += $value_data['sum_pay_amount'];
												$total_data[$value_data['deduct_code']][$rs_loan[0]['loan_type_id']]['principal'] += $value_data['sum_pay_amount'];
											}else{
												$array_data[$value_data['deduct_code']][$rs_loan[0]['loan_type_id']]['interest'] += $value_data['sum_pay_amount'];
												$total_data[$value_data['deduct_code']][$rs_loan[0]['loan_type_id']]['interest'] += $value_data['sum_pay_amount'];
											}
											$is_cal = true;
										}
									}else if($value_data['deduct_code']=='ATM' && empty($_GET['term_of_loan'])){
										$array_data[$value_data['deduct_code']]['loan_atm_id'][$value_data['loan_atm_id']] = 1;
										$total_data[$value_data['deduct_code']]['loan_atm_id'][$value_data['loan_atm_id']] = 1;
										if($value_data['pay_type']=='principal'){
											$array_data[$value_data['deduct_code']]['principal'] += $value_data['sum_pay_amount'];
											$total_data[$value_data['deduct_code']]['principal'] += $value_data['sum_pay_amount'];
										}else{
											$array_data[$value_data['deduct_code']]['interest'] += $value_data['sum_pay_amount'];
											$total_data[$value_data['deduct_code']]['interest'] += $value_data['sum_pay_amount'];
										}
										$is_cal = true;
									} else if ($value_data['deduct_code']=='SHARE' && empty($_GET['term_of_loan'])) {
										$member_share++;
										$member_share_total++;
										$array_data[$value_data['deduct_code']] += $value_data['sum_pay_amount'];
										$total_data[$value_data['deduct_code']] += $value_data['sum_pay_amount'];
										$is_cal = true;
									} else if(empty($_GET['term_of_loan'])){
										$array_data[$value_data['deduct_code']] += $value_data['sum_pay_amount'];
										$total_data[$value_data['deduct_code']] += $value_data['sum_pay_amount'];
										$is_cal = true;
									}
									if ($is_cal) {
										$array_data['total_amount'] += $value_data['sum_pay_amount'];
										$total_data['total_amount'] += $value_data['sum_pay_amount'];
									}
									$rs_data_index++;
									if ($rs_data_index == $rs_data_count || $rs_data[$rs_data_index]['member_id'] != $rs_data[$rs_data_index - 1]['member_id']) {
										$member_count++;
										$member_total++;
									}
								}	
					?>
							<tr> 
							  <td style="text-align: center;"><?php echo @$row['mem_group_id']; ?></td>
							  <td style="text-align: left;"><?php echo @$row['mem_group_name']; ?></td>						 
							  <td style="text-align: center;"><?php echo number_format($member_count);?></td>						 
							  <!-- <td style="text-align: center;"><?php echo number_format(($array_data['SHARE']/10));?></td> 							  -->
							  <td style="text-align: center;"><?php echo number_format($member_share);?></td>
							  <td style="text-align: right;"><?php echo number_format(@$array_data['SHARE']); ?></td> 
							  
							<?php
								foreach($loan_type AS $key=>$row_loan_type){
									if($row_loan_type['loan_type_code'] == 'emergent'){
										$count_loan = (!empty($array_data['LOAN'][$row_loan_type['id']]['loan_id']) ? count($array_data['LOAN'][$row_loan_type['id']]['loan_id']) : 0)  + (!empty($array_data['ATM']['loan_atm_id']) ? count($array_data['ATM']['loan_atm_id']) : 0);
										$principal = $array_data['LOAN'][$row_loan_type['id']]['principal'] + $array_data['ATM']['principal'];
										$interest = $array_data['LOAN'][$row_loan_type['id']]['interest'] + $array_data['ATM']['interest'];
									}else{
										$count_loan = !empty($array_data['LOAN'][$row_loan_type['id']]['loan_id']) ? count($array_data['LOAN'][$row_loan_type['id']]['loan_id']) : 0;
										$principal = $array_data['LOAN'][$row_loan_type['id']]['principal'];
										$interest = $array_data['LOAN'][$row_loan_type['id']]['interest'];
									}
								?>
								  <td style="text-align: center;"><?php echo number_format(@$count_loan);?></td> 					 
								  <td style="text-align: right;"><?php echo number_format(@$principal,2);?></td> 					 
								  <td style="text-align: right;"><?php echo number_format(@$interest,2);?></td> 
							<?php 
								}
							?>
							  <td style="text-align: right;"><?php echo number_format(@$array_data['DEPOSIT'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$array_data['CREMATION'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$array_data['REGISTER_FEE'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$array_data['OTHER'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$array_data['GUARANTEE_AMOUNT'],2);?></td> 				 
							  <td style="text-align: right;"><?php echo number_format(@$array_data['total_amount'],2);?></td> 							 
						  </tr>						
					
					<?php
							}
						}
						
						if($page == $page_all){	
					?>
						   <tr> 
							  <td style="text-align: center;" colspan="2">รวมทั้งสิ้น</td>
							  <td style="text-align: center;"><?php echo number_format($member_total);?></td>						 
							  <!-- <td style="text-align: center;"><?php echo number_format((@$total_data['SHARE']/10));?></td> -->
							  <td style="text-align: center;"><?php echo number_format($member_share_total);?></td>					 
							  <td style="text-align: right;"><?php echo number_format(@$total_data['SHARE']);?></td> 
								<?php
									foreach($loan_type AS $key=>$row_loan_type){
										if($row_loan_type['loan_type_code'] == 'emergent'){
											$count_loan = @count($total_data['LOAN'][$row_loan_type['id']]['loan_id']) + count($total_data['ATM']['loan_atm_id']);
											$principal = @$total_data['LOAN'][$row_loan_type['id']]['principal'] + $total_data['ATM']['principal'];
											$interest = @$total_data['LOAN'][$row_loan_type['id']]['interest'] + $total_data['ATM']['interest'];
										}else{
											$count_loan = @count($total_data['LOAN'][$row_loan_type['id']]['loan_id']);
											$principal = @$total_data['LOAN'][$row_loan_type['id']]['principal'];
											$interest = @$total_data['LOAN'][$row_loan_type['id']]['interest'];
										}
								?>
								  <td style="text-align: center;"><?php echo number_format($count_loan);?></td> 					 
								  <td style="text-align: right;"><?php echo number_format(@$principal,2);?></td> 					 
								  <td style="text-align: right;"><?php echo number_format(@$interest,2);?></td> 
								<?php 
									}
								?>				 
							  <td style="text-align: right;"><?php echo number_format(@$total_data['DEPOSIT'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$total_data['CREMATION'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$total_data['REGISTER_FEE'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$total_data['OTHER'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$total_data['GUARANTEE_AMOUNT'],2);?></td> 				 
							  <td style="text-align: right;"><?php echo number_format(@$total_data['total_amount'],2);?></td> 	 						 
						  </tr>
					<?php } ?>	  
					</tbody>    
				</table>
			</div>
		</div>
		<?php } ?>