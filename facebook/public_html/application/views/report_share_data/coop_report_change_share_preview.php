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

			$where = '';
			$where .= " AND create_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$this->db->select(array('t1.member_id',
									'employee_id',
									'prename_short',
									'firstname_th',
									'lastname_th',
									't1.change_value_price',
									't1.change_share_id',
									'salary',
									'create_date',
									't1.faction'
									));
			$this->db->from('coop_change_share_report as t1');
			$this->db->where("change_share_status IN ('1', '2') {$where}");
			$this->db->order_by('change_share_id DESC');
			$rs = $this->db->get()->result_array();

			// $month_array = array();
			$year_array = array();
			$data = array();
			$data2 = array();
			if(!empty($rs)){
				foreach(@$rs as $key => $row){
					$createdatetime = explode(' ',@$row['create_date']);
					$createdate = explode('-',@$createdatetime[0]);
					$create_month = (int)@$createdate[1];
					$create_year = (int)@$createdate[0];
					$prev_change_shares = $this->db->select("*")
													->from("coop_change_share")
													->where("member_id = '".$row["member_id"]."' AND create_date < '".$row["create_date"]."' AND change_share_status IN ('1', '2')")
													->order_by("create_date DESC")
													->get()->result_array();

					if(!empty($prev_change_shares)) $row['prev_change_share'] = $prev_change_shares[0]["change_value_price"];

					if(@$row['prev_change_share'] == ''){
						$prev_shares = $this->db->select("*")
												->from("coop_mem_share")
												->where("member_id = '".$row['member_id']."' AND share_type = 'SPM' AND share_date < '".$row['create_date']."'")
												->order_by("share_date DESC")
												->get()->result_array();
						$prev_share = $prev_shares[0]["share_early_value"];
					}else{
						$prev_share = @$row['prev_change_share'];
					}
					if($prev_share < $row['change_value_price']) {
						$row["prev_share"] = $prev_share;
						$data[$create_year][@$create_month][$row['change_share_id']] = $row;
					} else {
						$row["prev_share"] = $prev_share;
						$data2[$create_year][@$create_month][$row['change_share_id']] = $row;
					}
					// if(!in_array($create_month, $month_array)) $month_array[] = $create_month;
					if(!array_key_exists($create_year, $year_array)) {
						$year_array[$create_year][] = $create_month;
					} else if (!in_array($create_month, $year_array[$create_year])) {
						$year_array[$create_year][] = $create_month;
					}
				}
			}
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
							 <h3 class="title_view">เรื่องสมาชิกเปลี่ยนแปลงอัตราส่งเงินค่าหุ้นรายเดือน</h3>
							 <h3 class="title_view">
								<?php echo @$title_date;?>
							</h3>
							 <p>&nbsp;</p>	
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<?php if($i == '1'){?>
								<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
								<a href="<?php echo base_url(PROJECTPATH.'/report_share_data/coop_report_change_share_excel?'.$param); ?>" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>	
							<?php } ?>
						 </td>
					</tr> 
					<tr>
						<td colspan="3">
							<h3 class="title_view">
							<?php
								$count = 0;
								if(!empty($data[$year][$m])){
									foreach($data[$year][$m] as $key => $row){
										$count++;
									}
								}
								echo "ในระหว่างเดือน ".@$month_arr[$m]." ".(@$year+543)." สมาชิกสหกรณ์ฯขอเปลี่ยนแปลงอัตราค่าหุ้นเพิ่มขึ้น  จำนวน  ".@$count." ราย ดังนี้";								
							?>	
							</h3>
						</td>
					</tr> 
				</table>
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th style="width: 40px;vertical-align: middle;">ลำดับที่</th>
							<th style="width: 80px;vertical-align: middle;">เลขทะเบียนสมาชิก</th>
							<th style="width: 50px;vertical-align: middle;">รหัสพนักงาน</th>
							<th style="width: 200px;vertical-align: middle;">ชื่อ - สกุล</th> 
							<th style="width: 200px;vertical-align: middle;">หน่วยงาน</th> 
							<th style="width: 70px;vertical-align: middle;">ค่างวดหุ้น เดิม</th> 

							<th style="width: 70px;vertical-align: middle;">ค่างวดหุ้น ใหม่</th>
                            <th style="width: 70px;vertical-align: middle;">ค่างวดหุ้น ที่เพิ่ม</th>
                        </tr>
					</thead>
					<tbody>
					  <?php 
						$j = 1;
						$share1 = 0;
						$share2 = 0;
						$share3 = 0;
						if(!empty($data[$year][$m])){
							foreach($data[$year][$m] as $key => $row){
								$loan_guarantee = array();
						?>
						  <tr> 
							  <td style="text-align: center;"><?php echo @$j++;?></td>
							  <td style="text-align: center;"><?php echo @$row['member_id']; ?></td>
							  <td style="text-align: center;"><?php echo @$row['employee_id']; ?></td> 							 
							  <td style="text-align: left;"><?php echo @$row['prename_short'].@$row['firstname_th'].'  '.@$row['lastname_th']; ?></td>						 
							  <td style="text-align: left;"><?php echo @$mem_group_arr[@$row['faction']]; ?></td>
							  <td style="text-align: right;"><?php echo number_format($row['prev_share'],2); ?></td>
							  <td style="text-align: right;"><?php echo number_format(@$row['change_value_price'],2); ?></td>
                              <td style="text-align: right;"><?php echo number_format((@$row['change_value_price']-$row['prev_share']),2); ?></td>
						  </tr>
					<?php 
								$share1 += $row['prev_share'];
								$share2 += (@$row['change_value_price']-@$row['prev_share']);
								$share3 += @$row['change_value_price'];
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

							  <td style="text-align: right;border-bottom: 3px double #000 !important;"><?php echo number_format($share3,2); ?></td>
                            <td style="text-align: right;border-bottom: 3px double #000 !important;"><?php echo number_format($share2,2); ?></td>
						</tr>
					</tfoot> 
				</table>
				
				<h3 class="title_view text-left">
				<?php
					$count = 0;
					if(!empty($data2[$year][$m])){
						foreach($data2[$year][$m] as $key => $row){
							$count++;
						}
					}

					echo "ในระหว่างเดือน ".$month_arr[$year][$m]."  ".($year+543)." สมาชิกสหกรณ์ฯขอเปลี่ยนแปลงอัตราค่าหุ้นลดลง  จำนวน ".$count." ราย ดังนี้";								
				?>	
				</h3>
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th style="width: 40px;vertical-align: middle;">ลำดับที่</th>
							<th style="width: 80px;vertical-align: middle;">เลขทะเบียนสมาชิก</th>
							<th style="width: 50px;vertical-align: middle;">รหัสพนักงาน</th>
							<th style="width: 200px;vertical-align: middle;">ชื่อ - สกุล</th> 
							<th style="width: 200px;vertical-align: middle;">หน่วยงาน</th> 
							<th style="width: 70px;vertical-align: middle;">ค่างวดหุ้น เดิม</th>

							<th style="width: 70px;vertical-align: middle;">ค่างวดหุ้น ใหม่</th>
                            <th style="width: 70px;vertical-align: middle;">ค่างวดหุ้น ที่ลด</th>
						</tr> 
					</thead>
					<tbody>
					  <?php 
						$j = 1;
						$share1 = 0;
						$share2 = 0;
						$share3 = 0;
						if(!empty($data2[$year][$m])){
							foreach($data2[$year][$m] as $key => $row){
						?>
						  <tr> 
							  <td style="text-align: center;"><?php echo @$j++;?></td>
							  <td style="text-align: center;"><?php echo @$row['member_id']; ?></td>
							  <td style="text-align: center;"><?php echo @$row['employee_id']; ?></td> 							 
							  <td style="text-align: left;"><?php echo @$row['prename_short'].@$row['firstname_th'].'  '.@$row['lastname_th']; ?></td>						 
							  <td style="text-align: left;"><?php echo @$mem_group_arr[@$row['faction']]; ?></td>
							  <td style="text-align: right;"><?php echo number_format($row['prev_share'],2); ?></td>

							  <td style="text-align: right;"><?php echo  number_format(@$row['change_value_price'],2); ?></td>
                              <td style="text-align: right;"><?php echo  number_format(($row['prev_share']-@$row['change_value_price']),2); ?></td>
						  </tr>
					<?php 
								$share1 += $row['prev_share'];
								$share2 += ($row['prev_share']-@$row['change_value_price']);
								$share3 += @$row['change_value_price'];
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

							  <td style="text-align: right;border-bottom: 3px double #000 !important;"><?php echo number_format($share3,2); ?></td>
                              <td style="text-align: right;border-bottom: 3px double #000 !important;"><?php echo number_format($share2,2); ?></td>
						</tr>
					</tfoot> 
				</table>
			</div>
		</div>
		
		<?php 
				}
			}
		?>