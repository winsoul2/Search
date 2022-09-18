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
		font-family: upbean;
		font-size: 16px;
	}	
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}
	
	.foot-border{
	    border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}
</style>		
	<?php
		
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "";
		if(@$_GET['type_id']){
			$where .= " AND coop_maco_account.type_id = '".@$_GET['type_id']."'";
		}
		
		if(@$_GET['start_date'] != '' AND @$_GET['end_date'] == ''){
			$where .= " AND coop_account_transaction.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_GET['start_date'] != '' AND @$_GET['end_date'] != ''){
			$where .= " AND coop_account_transaction.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}		
		
		$this->db->select(array('coop_account_transaction.*','coop_user.user_name'));
		$this->db->from('coop_account_transaction');
		$this->db->join("coop_user","coop_account_transaction.user_id = coop_user.user_id","left");
		$this->db->join("coop_maco_account","coop_account_transaction.account_id = coop_maco_account.account_id","inner");
		$this->db->where("1=1 {$where}");
		$rs_count = $this->db->get()->result_array();
		
		$num_rows = count($rs_count)-4;
		$page_limit = 28;			
		$page_all = @ceil($num_rows/$page_limit);
		$arr_total = array();	
		$total_item = @count($rs_count);
		$all_withdrawal = 0;
		$all_deposit = 0;
		$all_balance = 0;
		foreach($rs_count AS $key_count=>$row_count){
			$all_withdrawal += @$row_count['transaction_withdrawal'];
			$all_deposit +=  @$row_count['transaction_deposit'];
			$all_balance +=  @$row_count['transaction_balance'];
		}
		
		for($page = 1;$page<=$page_all;$page++){
			if($page == 1){
				$page_limit = 24;
				$page_start = (($page-1)*$page_limit);
			}else{
				$page_limit = 28;
				$page_start = (($page-1)*$page_limit)-4;
			}
			
			$per_page = $page*$page_limit ;		
			
		//class="page-break"
	?>
		
		<div style="width: 1000px;" >
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
							 <h3 class="title_view">รายงานการทำรายการ ประจำวัน</h3>
							 <h3 class="title_view">
								<?php echo " ประเภทบัญชี ".@$type_deposit[@$_GET['type_id']];?>
							</h3>
							 <h3 class="title_view">
								<?php 
									echo " วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
									echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึง  ".$this->center_function->ConvertToThaiDate($end_date);
								?>
							</h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
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
			
				<table class="table table-view-2 table-center">
					<thead> 
						<tr>
							<th style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th style="width: 100px;vertical-align: middle;">วันที่ทำรายการ</th>
							<th style="width: 80px;vertical-align: middle;">เวลาที่ทำรายการ</th>
							<th style="width: 100px;vertical-align: middle;">หมายเลขบัญชี</th>
							<th style="width: 180px;vertical-align: middle;">ชื่อบัญชี</th>
							<th style="width: 80px;vertical-align: middle;">รายการ</th>
							<th style="width: 80px;vertical-align: middle;">ฝาก</th>
							<th style="width: 80px;vertical-align: middle;">ถอน</th>
							<th style="width: 80px;vertical-align: middle;">คงเหลือ</th>
							<th style="width: 130px;vertical-align: middle;">ผู้บันทึก</th>
						</tr>  
					</thead>
					<tbody>
					
					<?php	
						
						$this->db->select(array('coop_account_transaction.*','coop_user.user_name','coop_maco_account.account_name'));
						$this->db->from('coop_account_transaction');
						$this->db->join("coop_user","coop_account_transaction.user_id = coop_user.user_id","left");
						$this->db->join("coop_maco_account","coop_account_transaction.account_id = coop_maco_account.account_id","inner");
						$this->db->where("1=1 {$where}");
						$this->db->order_by("coop_account_transaction.transaction_time");
						$this->db->limit($page_limit, $page_start);
						$rs = $this->db->get()->result_array();											
						
						$runno = @$page_start;
						$total_transaction_withdrawal = 0;
						$total_transaction_deposit = 0;
						$total_transaction_balance = 0;
						if(!empty($rs)){
							foreach(@$rs as $key => $row){
								$runno++;
								$total_transaction_withdrawal += @$row['transaction_withdrawal'];
								$total_transaction_deposit += @$row['transaction_deposit'];
								$total_transaction_balance += @$row['transaction_balance'];
					?>
							<tr> 
							  <td style="text-align: center;"><?php echo @$runno; ?></td>
							  <td style="text-align: center;"><?php echo (@$row['transaction_time'])?$this->center_function->ConvertToThaiDate(@$row['transaction_time'],1,0):"";?></td>
							  <td style="text-align: center;"><?php echo (@$row['transaction_time'])?date(" H:i" , strtotime(@$row['transaction_time'])):""?></td>						 
							  <td style="text-align: center;"><?php echo @$row['account_id'];?></td>						 
							  <td style="text-align: left;"><?php echo @$row['account_name'];?></td>	
							  <td style="text-align: center;"><?php echo @$row['transaction_list'];?></td> 					 
							  <td style="text-align: right;"><?php echo number_format($row['transaction_withdrawal'],2); ?></td> 					 
							  <td style="text-align: right;"><?php echo number_format($row['transaction_deposit'],2); ?></td> 					 
							  <td style="text-align: right;"><?php echo number_format($row['transaction_balance'],2); ?></td> 						 
							  <td style="text-align: center;"><?php echo @$row['user_name'];?></td> 						 
							</tr>										
					
					<?php								
							}
					?>
							<tr class="border-bottom"> 
							  <td style="text-align: right;"  colspan="6">จำนวนเงิน</td>					 
							  <td style="text-align: right;"><span style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_transaction_withdrawal,2); ?></span></td> 					 
							  <td style="text-align: right;"><span style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_transaction_deposit,2); ?></span></td> 					 
							  <td style="text-align: right;"><span style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_transaction_balance,2); ?></span></td> 						 
							  <td style="text-align: center;">บาท</td> 						 
							</tr>	
					<?php
						}
						
						if(@$page == @$page_all){							
					?>
						   <tr class="foot-border"> 
							  <td style="text-align: center;" colspan="4">รวมทั้งหมด <?php echo @$total_item;?> รายการ</td>					 
							  <td style="text-align: center;" colspan="2">จำนวนเงินทั้งหมด</td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$all_withdrawal,2); ?></td> 						 
							  <td style="text-align: right;"><?php echo number_format(@$all_deposit,2); ?></td> 						 
							  <td style="text-align: right;"><?php echo number_format(@$all_balance,2); ?></td> 						 
							  <td style="text-align: center;">บาท</td> 						 
						  </tr>
					<?php } ?>	  
					</tbody>    
				</table>
			</div>
		</div>
		<?php } ?>