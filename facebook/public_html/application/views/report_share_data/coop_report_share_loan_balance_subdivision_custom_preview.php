<style>
	.table {
		font-size: 11px;
		font-family: THSarabunNew;
		/*color: #000;*/
	}
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 11px;
		font-family: THSarabunNew;
		color: #000;
	}
	.title_view{
		font-size: 16px;
		font-family: THSarabunNew;	
		margin-bottom: 10px;
	    /*color: #000;	*/
	}
	.title_view_small{
		font-size: 10px;
		font-family: THSarabunNew;	
	    /*color: #000;*/
	}	
	@page { size: landscape; }
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
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
		//echo '<pre>'; print_r($_GET); echo '</pre>';
		if(@$_GET['month']!='' && @$_GET['year']!=''){
			$day = '';
			$month = @$_GET['month'];
			$year = @$_GET['year'];
			$title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year);
		}else{
			$day = '';
			$month = '';
			$year = @$_GET['year'];
			$title_date = " ปี ".(@$year);
		}

$last_runno = 0;		
if(!empty($data)){
	foreach(@$data AS $page=> $data_row){
	?>
		
		<div style="width: 1500px;"  class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;height: 1000px;">
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
							 <h3 class="title_view">รายงานสรุปทุนเรือนหุ้น-เงินกู้คงเหลือ  ประจำวัน  (ตามหน่วยงานย่อย,ประเภทเงินกู้หลัก)</h3>
							 <h3 class="title_view">
								<?php 								
									$title_date = (@$_GET['type_date'] == '1')?'ณ วันที่':'ประจำวันที่';								
									echo @$title_date." ".$this->center_function->ConvertToThaiDate($start_date);
								?>
							</h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							<?php
							$get_param = '?';
							foreach(@$_GET as $key => $value){
								//if($key != 'month' && $key != 'year' && $value != ''){
									$get_param .= $key.'='.$value.'&';
								//}
							}
							$get_param = substr($get_param,0,-1);
							?>
							<a class="no_print"  target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_share_data/coop_report_share_loan_balance_loan_type_excel'.$get_param); ?>">
								<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
							</a>
						</td>
					</tr> 
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view_small">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></span>				
						</td>
					</tr>  
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view_small">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
						</td>
					</tr>  
				<?php } ?>
				</table>
			
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th rowspan="3" style="width: 40px;vertical-align: middle;">รหัส</th>
							<th rowspan="3" style="width: 200px;vertical-align: middle;overflow: hidden; white-space: nowrap;">หน่วยงาน</th>
							<th rowspan="2" colspan="4" style="width: 150px;vertical-align: middle;">จำนวนสมาชิก</th>
							<th rowspan="3" style="width: 80px;vertical-align: middle;">หุ้น</th>
							<?php 
							foreach($loan_type AS $key=>$row_loan_type){
							?>
							<th rowspan="1" style="width: 200px;vertical-align: middle; overflow: hidden; white-space: nowrap;"><?php echo str_replace('เงินกู้','',$row_loan_type['loan_type']);?></th>
							<?php }?>
						</tr>
                        <tr>
                            <th style="width: 200px;vertical-align: middle;overflow: hidden; white-space: nowrap;">สามัญโครงการ</th>
                            <th style="width: 200px;vertical-align: middle;overflow: hidden; white-space: nowrap;">สามัญสะสม</th>
                            <th style="width: 200px;vertical-align: middle;overflow: hidden; white-space: nowrap;">เงินกู้พิเศษเพื่อการเคหะสงเคราะห์</th>
                        </tr>
                        <tr>
                            <th rowspan="1" style="width: 10px; vertical-align: middle;">ทั้งหมด</th>
                            <th rowspan="1" style="width: 10px; vertical-align: middle;">มีหนี้</th>
                            <th rowspan="1" style="width: 10px; vertical-align: middle;">ชาย</th>
                            <th rowspan="1" style="width: 10px; vertical-align: middle;">หญิง</th>
                            <th style="width: 200px; vertical-align: middle;overflow: hidden; white-space: nowrap;">สามัญเพื่อการศึกษา</th>
                            <th style="width: 200px; vertical-align: middle;overflow: hidden; white-space: nowrap;">สามัญโครงการพิเศษเพื่อช่วยเหลือสมาชิกที่กู้เงิน</th>
                            <th style="width: 200px; vertical-align: middle;">&nbsp;</th>
                        </tr>
					</thead>
					<tbody>
					<?php
						$runno = $last_runno;						
						$all_share_person = 0;
						$all_share_collect = 0;
						$all_loan_emergent_person = 0;
						$all_loan_emergent_balance = 0;
						$all_loan_normal_person = 0;
						$all_loan_normal_balance = 0;
						$all_loan_special_person = 0;
						$all_loan_special_balance = 0;
						$all_total_loan_balance = 0;
						$all_total_loan_balance = 0;
						$all_share_balance_subdivision = 0;
						$all_loan_balance_subdivision = 0;
						$all_mem_all_amt = 0;
                        $all_mem_has_debt_amt = 0;
                        $all_mem_mem_amt = 0;
                        $all_mem_women_amt = 0;
						if(!empty($data_row)){

							foreach(@$data_row as $key => $rows){

							   //echo "<pre>"; print_r($rows);

							    foreach ($rows as $index => $row) {
                                    $runno++;
                                    $total_share_person += @$row['share_person'];
                                    $total_share_collect += @$row['share_collect'];
                                    $total_loan_emergent_person += @$row['loan_emergent_person'];
                                    $total_loan_emergent_balance += @$row['loan_emergent_balance'];
                                    $total_loan_normal_person += @$row['loan_normal_person'];;
                                    $total_loan_normal_balance += @$row['loan_normal_balance'];
                                    $total_loan_special_person += @$row['loan_special_person'];
                                    $total_loan_special_balance += @$row['loan_special_balance'];

                                    $mem_all_amt +=  @$row['mem_all_amt'];
                                    $mem_has_debt_amt +=  @$row['mem_has_debt'];
                                    $mem_mem_amt +=  @$row['mem_men'];
                                    $mem_women_amt +=  @$row['mem_wemen'];
                                    ?>
                                    <tr>
                                        <td style="text-align: center;vertical-align: top;"><?php echo @$row['mem_group_id']; ?></td>
                                        <td style="text-align: left;vertical-align: top;overflow: hidden; white-space: nowrap;"><?php echo @$row['mem_group_name']; ?></td>
                                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['mem_all_amt']); ?></td>
                                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['mem_has_debt']); ?></td>
                                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['mem_men']); ?></td>
                                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['mem_wemen']); ?></td>
                                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['share_collect'], 2); ?></td>
                                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['loan_emergent_balance'], 2); ?></td>
                                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['loan_normal_balance'], 2); ?></td>
                                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['loan_special_balance'], 2); ?></td>
                                    </tr>

                                    <?php
                                }
							}
						}
						$last_runno = $runno;
						$all_share_collect += @$total_share_collect;
						$all_loan_emergent_balance += @$total_loan_emergent_balance;
						$all_loan_normal_balance += @$total_loan_normal_balance;
						$all_loan_special_balance += @$total_loan_special_balance;

						$all_mem_all_amt += $mem_all_amt;
						$all_mem_has_debt_amt += $mem_has_debt_amt;
                        $all_mem_mem_amt += $mem_mem_amt;
						$all_mem_women_amt += $mem_women_amt;

						//echo "<pre>"; print_r($summary);exit;

						if(@$page == @$page_all){

					?>
						<tr> 
                            <td rowspan="3" style="text-align: center;vertical-align: top;" colspan="2" class="border-bottom">รวมทั้งสิ้น</td>
                            <td rowspan="3" style="text-align: right;vertical-align: top;"><?php echo number_format($all_mem_all_amt); ?></td>
                            <td rowspan="3" style="text-align: right;vertical-align: top;"><?php echo number_format($all_mem_has_debt_amt); ?></td>
                            <td rowspan="3" style="text-align: right;vertical-align: top;"><?php echo number_format($all_mem_mem_amt); ?></td>
                            <td rowspan="3" style="text-align: right;vertical-align: top;"><?php echo number_format($all_mem_women_amt); ?></td>
                            <td rowspan="3" style="text-align: right;vertical-align: top;" class="border-bottom"><?php echo number_format(@$all_share_collect,2); ?></td>
                            <?php foreach (@$summary[0] as $key => $value) { ?>
                                <td style="text-align: right;vertical-align: top;" class="border-bottom"><?php echo number_format(@$value,2); ?></td>
                            <?php } ?>
						</tr>
                        <tr>
                            <?php foreach (@$summary[1]  as $key => $value) { ?>
                                <td style="text-align: right;vertical-align: top;" class="border-bottom"><?php echo number_format(@$value,2); ?></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <?php foreach (@$summary[2]  as $key => $value) { ?>
                                <td style="text-align: right;vertical-align: top;" class="border-bottom"><?php echo number_format(@$value,2); ?></td>
                            <?php } ?>
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
} 
?>
