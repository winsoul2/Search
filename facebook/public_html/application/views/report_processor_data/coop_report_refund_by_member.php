<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th, .table-view>tbody>tr>td {
		font-size: 12px;
		padding: 6px 2px;
		/* word-break: break-all; */
	}
	.table-view>tbody>tr>td {
		height: 43px;
	}
	.table {
		color: #000;
	}
</style>
<?php
	$all_total = 0;
	$col_total = array();
    if (!empty($data)) {
        foreach (@$data AS $page=>$data_row) {
?>
		<div style="min-width: 1500px; display: table;"  class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 950px;">
				<table style="width: 100%;">
				<?php
					if($page == 1){
				?>	
					<tr>
						<td style="width:100px;vertical-align: top;">

						</td>
						<td class="text-center">
                            <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
							<h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							<h3 class="title_view">รายงานการคืนเงิน</h3>
							<h3 class="title_view">ประจำ<?php echo $title_date?></h3>
							<h3 class="title_view">หน่วยงาน <?php echo $department_name?></h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
						</td>
					</tr>
                <?php
                    }
                    ?>
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
				<table class="table table-view table-center" >
					<thead>
						<tr>
                            <th rowspan="3" style="width:40px; vertical-align: middle;">ลำดับ</th>
                            <th rowspan="3" style="vertical-align: middle;">เลขที่สมาชิก</th>
                            <th rowspan="3" style="vertical-align: middle;">ชื่อ - นามสกุล</th>
							<?php
								if (!empty($header_type['emergent'])) {
							?>
							<th colspan="<?php echo count($header_type['emergent']) * 2?>" style="vertical-align: middle;">ฉุกเฉิน</th>
							<?php
								}
								if (!empty($header_type['normal'])) {
							?>
							<th colspan="<?php echo count($header_type['normal']) * 2?>" style="vertical-align: middle;">สามัญ</th>
							<?php
								}
								if (!empty($header_type['special'])) {
							?>
							<th colspan="<?php echo count($header_type['special']) * 2?>" style="vertical-align: middle;">พิเศษ</th>
							<?php
								}
							?>
							<th rowspan="3" style="vertical-align: middle;">หุ้น</th>
							<th rowspan="3" style="vertical-align: middle;">รวม</th>
						</tr>
						<tr>
							<?php
								$head_count = 0;
								foreach($header_type as $heads) {
									foreach($heads as $head) {
							?>
								<th colspan="2" style="vertical-align: middle;"><?php echo $head['name']?></th>
							<?php
										$head_count++;
									}
								}
							?>
						</tr>
						<tr>
							<?php
								for($i = 0; $i < $head_count; $i++) {
							?>
								<th style="vertical-align: middle;">เงินต้น</th>
								<th style="vertical-align: middle;">ดอกเบี้ย</th>
							<?php
								}
							?>
						</tr>
					</thead>
					<tbody>
					<?php
                        $run_no = $last_run_no;
                        if(!empty($data_row)){
                            foreach(@$data_row as $key => $row){
                                $run_no++;
					?>
						<tr> 
							<td style="text-align: center;"><?php echo $run_no; ?></td>
							<td style="text-align: center;"><?php echo $row['member_id'] ?></td>
							<td style="text-align: left;"><?php echo $row['prename_full'].$row['firstname_th']." ".$row['lastname_th'] ?></td>
					<?php
								$row_total = 0;
								foreach($header_type as $loan_type_code => $heads) {
									foreach($heads as $loan_name_id => $head) {
										$interest = "";
										$principal = "";
										if (!empty($body_data[$row['member_id']][$loan_type_code][$head['id']]['interest'])){
											$interest = $body_data[$row['member_id']][$loan_type_code][$head['id']]['interest'];
											$row_total += $interest;
											$col_total[$loan_type_code][$head['id']]['interest'] += $interest;
										}
										if (!empty($body_data[$row['member_id']][$loan_type_code][$head['id']]['principal'])) {
											$principal = $body_data[$row['member_id']][$loan_type_code][$head['id']]['principal'];
											$row_total += $principal;
											$col_total[$loan_type_code][$head['id']]['principal'] += $principal;
										}
										
					?>
							<td style="text-align: right;"><?php echo !empty($principal) ? number_format($principal,2) : ""; ?></td>
							<td style="text-align: right;"><?php echo !empty($interest) ? number_format($interest,2) : ""; ?></td>
					<?php
									}
								}
								$all_total += $row_total;
					?>
							<td style="text-align: right;"></td>
							<td style="text-align: right;"><?php echo number_format($row_total,2);?></td>
						</tr>
					<?php
                            }
                            $last_run_no = $run_no;
						}
						if($page == $page_all){
					?>
						<tr> 
							<td colspan="3" style="text-align: center;">รวมทั้งสิ้น</td>
					<?php
							foreach($header_type as $loan_type_code => $heads) {
								foreach($heads as $loan_name_id => $head) {
					?>
							<td style="text-align: right;"><?php echo !empty($col_total[$loan_type_code][$head['id']]['interest']) ? number_format($col_total[$loan_type_code][$head['id']]['interest'],2) : number_format(0,2)?></td>
							<td style="text-align: right;"><?php echo !empty($col_total[$loan_type_code][$head['id']]['principal']) ? number_format($col_total[$loan_type_code][$head['id']]['principal'],2) : number_format(0,2)?></td>
					<?php
								}
							}
					?>
							<td style="text-align: right;">0.00</td>
							<td style="text-align: right;"><?php echo number_format($all_total,2); ?></td>
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