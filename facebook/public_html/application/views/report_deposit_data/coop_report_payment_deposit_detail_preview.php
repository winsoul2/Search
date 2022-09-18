<style>
	.table-view > thead, .table-view > thead > tr > td, .table-view > thead > tr > th {
		font-size: 14px;
	}

	.table-view-2 > thead > tr > th {
		border-top: 1px solid #000 !important;
		border-bottom: 1px solid #000 !important;
		font-size: 16px;
	}

	.table-view-2 > tbody > tr > td {
		border: 0px !important;
		/*font-family: upbean;
		font-size: 16px;*/
		font-family: Tahoma;
		font-size: 12px;
	}

	.border-bottom {
		border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}

	.foot-border {
		border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}

	.table {
		color: #000;
	}
	
	.title_view_small{
		font-size: 12px;
		font-family: THSarabunNew;
		color: #000;
		line-height: 23px;
	}

	@media print {
		.pagination {
			display: none;
		}
	}
</style>
<?php
//class="page-break"
//
$last_runno = 0;
// $sum_interest = 0 ;
$type_name = $data['0']['type_name'];
$page = 1;
$page_all = count($data);
//echo count($data); echo '<br>';
//echo '<pre>'; print_r($data); echo '</pre>'; exit;
if(!empty($data)){
 	foreach(@$data AS $keys=>$datas){
?>
<div style="width: 1000px;" class="page-break">
	<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
		<table style="width: 100%;" class="title_view_small">
			<tr>
				<td style="width:120px;">รายงานรับจ่ายเงินฝาก</td>
				<td colspan="2"><?php echo @$datas['row_head']['type_name']; ?></td>
				<td colspan="7" style="text-align: right;"><?php echo @$text_title_1; ?></td>
			</tr>
			<tr>
				<td ><?php echo $this->center_function->ConvertToThaiDate(@$datas['row_head']['transaction_time'],0,0); ?></td>
				<td colspan="7" style="text-align: right;">หน้าที่ : <?php echo $page; ?></td>
			</tr>	
		</table>
		<table class="table table-view table-center">
			<thead>
			<tr>
				<th style="vertical-align: middle;width: 80px;">เลขที่บัญชี</th>
				<th style="vertical-align: middle;">ชื่อบัญชี</th>
				<th style="vertical-align: middle;">ลำดับ</th>
				<th style="vertical-align: middle;">วันที่</th>
				<th style="vertical-align: middle;">คำย่อ</th>
				<th style="vertical-align: middle;">ถอน</th>
				<th style="vertical-align: middle;">ฝาก</th>
				<th style="vertical-align: middle;">ดอกเบี้ย</th>
				<th style="vertical-align: middle;">เงินฝากคงเหลือ</th>
			</tr>
			</thead>
			<tbody>
			<?php
				$sum_transaction_withdrawal = 0;
				$sum_transaction_deposit = 0;
				$sum_interest = 0;
			if (!empty($datas['row_detail'])) {
				foreach (@$datas['row_detail'] as $key => $row) {
					@$sum_transaction_withdrawal+=@$row['transaction_withdrawal'];
					@$sum_transaction_deposit+=@$row['transaction_deposit'];	
					@$sum_interest+=@$row['interest'];
					@$rest = substr(@$row['transaction_no'],-5);
			?>
					<tr>
						<td style="text-align: center;vertical-align: top;"><?php echo $this->center_function->format_account_number(@$row['account_id']); ?></td>
						<td style="text-align: left;vertical-align: top;"><?php echo @$row['account_name']; ?></td>
						<td style="text-align: center;vertical-align: top;"><?php echo @$row['seq_no'] ;?></td>
						<!--<td style="text-align: center;vertical-align: top;"><?php echo @$rest ;?></td>-->
						<td style="text-align: center;vertical-align: top;"><?php echo  date('d/m/y', strtotime("+543 year", strtotime($row['transaction_time'])));   ?></td>
						<td style="text-align: center;vertical-align: top;"><?php echo @$row['transaction_list']; ?></td>
						<td style="text-align: right;vertical-align: top;"><?php echo @number_format(@$row['transaction_withdrawal'],2)==0 ? "": number_format(@$row['transaction_withdrawal'],2)?>
						<td style="text-align: right;vertical-align: top;"><?php echo @number_format(@$row['transaction_deposit'],2)==0 ? "": number_format(@$row['transaction_deposit'],2)?>
						<td style="text-align: right;vertical-align: top;"><?php echo @number_format(@$row['interest'],2)==0 ? "": number_format(@$row['interest'],2)?>
						<td style="text-align: right;vertical-align: top;"><?php echo @number_format($row['transaction_balance'],2); ?></td>
					</tr>
			<?php
				}
			}
			?>
			<!-- เปิดผลรวมท้ายตาราง -->
			<tr>
				<td style="border: none;"><?php echo count($datas['row_detail']); ?></td>
				<td style="text-align: left;border: none;" colspan="4">เงินรวม <?php echo @$this->center_function->mydate2date(@$row['transaction_time']); ?></td>
				<td style="text-align: right;border: none;"> <?php echo @number_format(@$sum_transaction_withdrawal,2)?></td>
				<td style="text-align: right;border: none;" colspan="2" > <?php echo @number_format(@$sum_interest,2)?></td>
    		</tr>
			<tr>
				<td style="text-align: right;width: 150px; border: none;"   colspan="7"> <?php echo @number_format(@$sum_transaction_deposit,2)?></td>
			</tr>
			<tr>
				<td  style="border: none;"><?php echo count($datas['row_detail']); ?></td>
				<td style="text-align: left;border: none;" colspan="4">เงินรวม <?php echo @$datas['row_head']['type_name']; ?></td>
				<td style="text-align: right;border: none;"> <?php echo @number_format(@$sum_transaction_withdrawal,2)?></td>
				<td style="text-align: right;border: none;" colspan="2"> <?php echo @number_format(@$sum_interest,2)?></td>
			</tr>
			<tr>
			<td style="text-align: right;width: 150px;border: none; "   colspan="7"> <?php echo @number_format(@$sum_transaction_deposit,2)?></td>
			</tr>
			<!-- ปิดผลรวมท้ายตาราง -->
			</tbody>
		
 
		</table>
	</div>
</div>
<?php
		$page++;
	}
 }	
?>
<script>
	function export_excel() {
		var url = window.location.href + "&excel=export";
		window.location = url;
	}
</script>
