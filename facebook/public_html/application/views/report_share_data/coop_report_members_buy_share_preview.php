<?php
			$param = '';
			if(!empty($_GET)){
				foreach($_GET AS $key=>$val){
					$param .= $key.'='.$val.'&';
				}
			}

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

			$title_date = $start_date != $end_date ? "ระหว่างวันที่ ".$this->center_function->ConvertToThaiDate($start_date)." ถึง วันที่ ".$this->center_function->ConvertToThaiDate($end_date) : "วันที่ ".$this->center_function->ConvertToThaiDate($start_date);

			// $month_array = array();
            $year_array = array();
			$where = " AND share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";

			$this->db->select(array('share_id',
									'member_id',
									'employee_id',
									'prename_short',
									'firstname_th',
									'lastname_th',
									'share_early_value',
									'share_date',
									'faction'
									));
			$this->db->from('coop_mem_share_report');
			$this->db->where("share_status IN ('1', '2') AND share_type = 'SPA' {$where}");
			$this->db->order_by('share_date DESC');
			$rs = $this->db->get()->result_array();
			$data3 = array();
			if(!empty($rs)){
				foreach(@$rs as $key => $row){
					$createdatetime = explode(' ',@$row['share_date']);
					$createdate = explode('-',@$createdatetime[0]);
					$create_month = (int)@$createdate[1];
					$create_year = (int)@$createdate[0];
					$data3[$create_year][@$create_month][@$row['share_id']] = @$row;
					if(!array_key_exists($create_year, $year_array)) {
						$year_array[$create_year][] = $create_month;
					} else if (!in_array($create_month, $year_array[$create_year])) {
						$year_array[$create_year][] = $create_month;
					}
//                    print_r($year_array);
				}
			}
//exit;

			$i=0;
			foreach($year_array as $year => $month_array) {
				foreach($month_array as $m){
					$i++;
							
		?>
		<style>
			.table {
				color: #000;
			}
		</style>
		<div style="width: 1000px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1440px;">
				<table style="width: 100%;">
					<tr>
						<td style="width:100px;vertical-align: top;">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
						</td>
						<td class="text-center">
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">เรื่องสมาชิกขอซื้อหุ้นพิเศษรายเดือน</h3>
							 <h3 class="title_view">
								<?php echo @$title_date;?>
							</h3>
							 <p>&nbsp;</p>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<?php if($i == '1'){?>
								<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
								<a href="<?php echo base_url(PROJECTPATH.'/report_share_data/coop_report_members_buy_share_excel?'.$param); ?>" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>
							<?php } ?>
						 </td>
					</tr>
				</table>

				<h3 class="title_view text-left">
				<?php
					$count = 0;
					if(!empty($data3[$year][$m])){
						foreach($data3[$year][$m] as $key => $row){
							$count++;
						}
					}
					echo "ในระหว่างเดือน ".@$month_arr[$m]."  ".(@$year+543)."  สมาชิกสหกรณ์ฯขอซื้อหุ้นพิเศษจำนวน  จำนวน ".@$count." ราย ดังนี้";								
				?>	
				</h3>
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th style="min-width: 40px;vertical-align: middle;">ลำดับที่</th>
							<th style="min-width: 80px;vertical-align: middle;">เลขทะเบียนสมาชิก</th>
							<th style="min-width: 50px;vertical-align: middle;">รหัสพนักงาน</th>
							<th style="min-width: 200px;vertical-align: middle;">ชื่อ - สกุล</th>
							<th style="min-width: 200px;vertical-align: middle;">หน่วยงาน</th>
							<th style="min-width: 70px;vertical-align: middle;">จำนวนเงิน (บาท)</th>
						</tr> 
					</thead>
					<tbody>
					  <?php 
						$j = 1;
						$share1 = 0;
						if(!empty($data3[$year][$m])){
							foreach($data3[$year][$m] as $key => $row){
						?>
						  <tr> 
							  <td style="text-align: center;"><?php echo @$j++;?></td>
							  <td style="text-align: center;"><?php echo @$row['member_id']; ?></td>
							  <td style="text-align: center;"><?php echo @$row['employee_id']; ?></td> 							 
							  <td style="text-align: left;"><?php echo @$row['prename_short'].@$row['firstname_th'].'  '.@$row['lastname_th']; ?></td>						 
							  <td style="text-align: left;"><?php echo @$mem_group_arr[@$row['faction']]; ?></td>
							  <td style="text-align: right;"><?php echo number_format(@$row['share_early_value'],2); ?></td>
						  </tr>
					<?php 
								$share1 += @$row['share_early_value'];
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
							  <td style="text-align: right;border-bottom: 3px double #000 !important;"><?php echo number_format($share1,2); ?></td> 							 
							  <td></td> 							 
						</tr>
					</tfoot> 
				</table>
			</div>
		</div>
		
		<?php 
				}
			}
		?>