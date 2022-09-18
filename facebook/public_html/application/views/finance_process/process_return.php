<style>
	.datepick {
		text-align: center;
		width: 150px !important;
	}
	#table_process_return {
		margin-top: 30px;
	}
	#table_process_return thead tr th {
		text-align: center !important;
	}
	#table_process_return tbody tr td {
		text-align: center;
	}
	#table_process_return tbody tr td:first-child {
		text-align: left;
	}
</style>
<div class="layout-content">
	<div class="layout-content-body">
		<h1 style="margin-bottom: 0"> ประมวลผลเงินคืน </h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0" id="breadcrumb">
					<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<a class="btn btn-primary btn-lg bt-add" id="btn-show-return-manual">
					<span class="icon icon-hand-paper-o"></span> คืนเงินด้วยตัวเอง
				</a>
				<a class="btn btn-primary btn-lg bt-add" id="btn-show-statement-edit" style="margin-right:10px">
					<span class="icon icon-pencil"></span> แก้ไข statement
				</a>
			</div>
		</div>
		<div class="panel panel-body col-xs-12 col-sm-12 col-md-12 col-lg-12 " >

			<div class="form-inline text-center">
				<div class="form-group">
					<label class="control-label">วันที่&nbsp;</label>
					<input type="text" class="form-control datepick" id="date_s" name="date_s" value="<?php echo $this->center_function->mydate2date(date("Y-m-1")); ?>" data-date-language="th-th" />
					<label class="control-label">&nbsp;ถึงวันที่&nbsp;</label>
					<input type="text" class="form-control datepick" id="date_e" name="date_e" class="form-control datepick" value="<?php echo $this->center_function->mydate2date(date("Y-m-t")); ?>" data-date-language="th-th" />
					<button type="button" class="btn btn-primary" id="btn_get_data" name="btn_get_data">แสดงข้อมูล</button>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-sm-offset-2">
					<table class="table" id="table_process_return">
						<thead>
							<tr>
								<th>รายการคืนเงิน</th>
								<th>ทั้งหมด</th>
								<th>ไม่คืนเงิน</th>
								<th>คืนเงินแล้ว</th>
								<th>เก็บเพิ่ม</th>
								<th>ค้างโอน</th>
								<th>การดำเนินการ</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
</div>
<?php
	$link = [
		'src' => PROJECTJSPATH.'assets/js/process_return.js?v='.date("Ymdhi"),
		'type' => 'text/javascript'
	];
	echo script_tag($link);
?>