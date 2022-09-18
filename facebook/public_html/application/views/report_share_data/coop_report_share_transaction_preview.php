<style>
	.table {
		font-size: 11px;
		font-family: THSarabunNew;
		color: #000;
	}
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 11px;
		font-family: THSarabunNew;
	}
	.title_view{
		font-size: 16px;
		font-family: THSarabunNew;
		margin-bottom: 10px;
		color: #000;
	}
	.title_view_mid{
		font-size: 14px;
		font-family: THSarabunNew;
		/* margin-bottom: 10px; */
		color: #000;
	}
	.title_view_small{
		font-size: 10px;
		font-family: THSarabunNew;
		color: #000;
	}
	/*@page { size: landscape; }*/
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}

</style>
<?php

$runno = 0;

if(!empty($data)){
	foreach(@$data AS $page=>$data_row){
?>

		<div style="width: 950px;"  class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1000px;">
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
							 <h3 class="title_view">รายงาน การซื้อหุ้น-ถอนหุ้น (Statement)</h3>
							 <h3 class="title_view">
								<?php
									if (!empty($start_date)) {
										echo " ณ วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
										echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึง  ".$this->center_function->ConvertToThaiDate($end_date);
									} else {
										echo " ณ วันที่ ".$this->center_function->ConvertToThaiDate($end_date);
									}
								?>
							</h3>
						</td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							<?php
							$get_param = '?';
							foreach(@$_GET as $key => $value){
								$get_param .= $key.'='.$value.'&';
							}
							$get_param = substr($get_param,0,-1);
							?>
							<a class="no_print"  target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_share_data/coop_report_share_transaction_excel'.$get_param); ?>">
								<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
							</a>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="text-align: center;">
							<table style="width: 100%;">
								<tr>
									<td class="text-align: left;">
										<span class="title_view_mid">เลขที่สมาชิก <?php echo $_GET['member_id'];?></span>
									</td>
									<td class="text-align: center;">
										<span class="title_view_mid">ชื่อ-นามสกุล <?php echo $member_name;?></span>
									</td>
									<td class="text-align: right;">
										<span class="title_view_mid">ทุนเรือนหุ้นคงเหลือ <?php echo number_format($total,2);?></span>
									</td>
								</tr>
							</table>
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
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view_small">หน้าที่ <?php echo $page.'/'.$page_all;?></span><br>
						</td>
					</tr>
				</table>

				<table class="table table-view table-center">
					<thead>
						<tr>
							<th style="vertical-align: middle;">ลำดับ</th>
							<th style="vertical-align: middle;">เลขที่สมาชิก</th>
							<th style="vertical-align: middle;">วันที่</th>
							<th style="vertical-align: middle;">เลขที่ใบเสร็จ</th>
							<th style="vertical-align: middle;">สถานะ</th>
							<th style="vertical-align: middle;">จำนวนหุ้น</th>
							<th style="vertical-align: middle;">ทุนเรือนหุ้น</th>
							<th style="vertical-align: middle;">จำนวนหุ้นคงเหลือ</th>
							<th style="vertical-align: middle;">ทุนเรือนหุ้นคงเหลือ</th>
							<th style="vertical-align: middle;">ผู้บันทึก</th>
						</tr>
					</thead>
					<tbody>					
					<?php
						foreach(@$data_row as $key => $row){
							$runno++;
					?>
							<tr>
								<td style="vertical-align: middle;"><?php echo $runno;?></td>
								<td style="vertical-align: middle;"><?php echo $row['member_id'];?></td>
								<td style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate(substr($row['share_date'],0,10));?></td>
								<td style="vertical-align: middle;"><?php echo $row['share_bill'];?></td>
								<td style="vertical-align: middle;"><?php echo @$share_type[$row['share_type']];?></td>
								<td style="vertical-align: middle; text-align: right;"><?php echo number_format($row['share_early'],0);?></td>
								<td style="vertical-align: middle; text-align: right;"><?php echo number_format($row['share_early_value'],2);?></td>
								<td style="vertical-align: middle; text-align: right;"><?php echo number_format($row['share_collect'],0);?></td>
								<td style="vertical-align: middle; text-align: right;"><?php echo number_format($row['share_collect_value'],2);?></td>
								<td style="vertical-align: middle;"><?php echo $row['user_name'];?></td>
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