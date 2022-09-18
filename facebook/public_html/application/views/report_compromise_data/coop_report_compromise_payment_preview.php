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
		/*font-family: upbean;
		font-size: 16px;*/
		font-family: Tahoma;
		font-size: 12px;
	}
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}
	.table-view-2>tbody>tr>td>span{
		font-family: Tahoma;
		font-size: 12px !important;
	}
	.foot-border{
	    border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}
	.table {
		color: #000;
	}
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		padding:6px;
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

	$runno = 0;
	$loaner = 0;
	$guarantor = 0;
	$loan_status = array('0'=>'รออนุมัติ', '1'=>'อนุมัติ' , '2'=>'ยื่นขอยกเลิกรายการ', '3'=>'<span style="color:red;">ยกเลิก</span>', '4'=>'ชำระเงินครบถ้วน', '5'=>'ไม่อนุมัติ','6'=>'เบี้ยวหนี้','7'=>'โอนหนี้ไปผู้ค้ำประกัน');

	foreach($datas AS $page=>$data){
?>
<div style="width: 1500px;" class="page-break">
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
					<h3 class="title_view">รายงานการชำระเงินกลุ่มประนอมหนี้</h3>
					<h3 class="title_view">
					<?php
						echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"ระหว่าง";
						echo "วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
						echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึง  ".$this->center_function->ConvertToThaiDate($end_date);
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
					<a class="no_print"  target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_compromise_data/coop_report_compromise_payment_excel'.$get_param); ?>">
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
					<th rowspan="2" style="vertical-align: middle;">ลำดับ</th>
					<th rowspan="2" style="vertical-align: middle;">วันที่ชำระ</th>
					<th rowspan="2" style="vertical-align: middle;">รหัส</th>
					<th rowspan="2" style="vertical-align: middle;">ชื่อ-นามสกุล</th>
					<th rowspan="2" style="vertical-align: middle;">เลขสัญญา</th>
					<th rowspan="2" style="vertical-align: middle;">เลขที่ใบเสร็จ</th>
					<th colspan="3" style="vertical-align: middle;">ยอดชำระ</th>
					<th rowspan="2" style="vertical-align: middle;">รวม</th>
					<th rowspan="2" style="vertical-align: middle;">กองทุน(บาท)</th>
					<th rowspan="2" style="vertical-align: middle;">สถานะ</th>
				</tr>
				<tr>
					<th>
						เงินต้น
					</th>
					<th>
						ดอกเบี้ย
					</th>
					<th>
						ดอกเบี้ยคงค้าง
					</th>
				</tr>
			</thead>
			<tbody>

			<?php
				foreach($data as $key => $row){
					$runno++;
					if($row["type"] == 1) {
						$guarantor++;
					} else {
						$loaner++;
					}
			?>
				<tr>
					<td style="text-align: center;vertical-align: top;"><?php echo $runno; ?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo ($row['payment_date'])?$this->center_function->ConvertToThaiDate($row['payment_date'],1,0):"";?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo $row["member_id"]; ?></td>
					<td style="text-align: left;vertical-align: top;"><?php echo $row["prename_full"].$row["firstname_th"]." ".$row["lastname_th"]; ?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo $row["contract_number"]; ?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo $row["receipt_id"]; ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo number_format($row["principal_payment"],2); ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo number_format($row["interest"],2); ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo number_format($row["loan_interest_remain"],2); ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo number_format($row["principal_payment"] + $row["interest"] + $row["loan_interest_remain"],2); ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo number_format($row["fund_support"],2); ?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo $loan_status[$row['loan_status']]; ?></td>
				</tr>
			<?php
				}
				if($page == $page_all) {
			?>
					<tr>
						<td></td>
						<td colspan="3" style="text-align: right;vertical-align: top;">หมายเหตุ เลือกรายการเป็น ทั้งหมด</td>
						<td style="text-align: right;vertical-align: top;"><?php echo $guarantor+$loaner;?></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td style="text-align: right;vertical-align: top;">ผู็กู้</td>
						<td style="text-align: right;vertical-align: top;"><?php echo $loaner;?></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td style="text-align: right;vertical-align: top;">ผู้ค้ำ</td>
						<td style="text-align: right;vertical-align: top;"><?php echo $guarantor;?></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
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
?>