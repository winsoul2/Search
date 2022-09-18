<div class="layout-content">
	<div class="layout-content-body">
		<h1 style="margin-bottom: 0"> รายการการ เปิด-ปิด บัญชีเงินฝาก </h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0" id="breadcrumb">
					<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
		<div class="panel panel-body col-xs-12 col-sm-12 col-md-12 col-lg-12 " >
			<form action="<?=base_url('Report_deposit_data/coop_report_account_status_detail_preview')?>" method="post" target="_blank" id="coop_report_account_status_detail_preview">
				<div class="row">
                    <div class="col-md-offset-4 col-md-2">
						<div class="form-group">
							<label for="">เลือกสถานะบัญชี</label>
							<br>
							<?php
								foreach ($account_status as $key => $value) {
									?>
										<input type="checkbox" name="account_status[]" value="<?=$key?>"> <?=$value?><br>
									<?php
								}
							?>
						</div>
					</div>
					<!-- <div class="col-md-2">
						<div class="form-group">
							<label for="">ประเภทสมาชิก</label>
							<br>
							<?php
								foreach ($mem_type as $key => $value) {
									?>
										<input type="checkbox" name="mem_type_id[]" value="<?=$value['mem_type_id']?>"> <?=$value['mem_type_name']?><br>
									<?php
								}
							?>
						</div>
					</div> -->
					
				</div>
                <br>
                <div class="row">
                    <label class="col-md-4 control-label right"> วันที่ </label>
					<div class="col-sm-4">
						<div class="input-with-icon">
							<div class="form-group">
								<input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
								<span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>
					</div>
                </div>
                <br>
				<div class="row">
					<div class="col-md-offset-4 col-md-4">
						<table class="table table-striped table-hover" id="table">
							<thead>
								<tr>
									<th width="20%"><a href="#" id="checkAll">เลือกทั้งหมด</a></th>
									<th>ฟิลล์ข้อมูล</th>
									<th>เรียงลำดับ</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$c = 0;
									foreach ($column as $key => $value) {
										?>
											<tr id="col_<?=$c?>">
												<td>
													<input type="checkbox" class="column" name="column[]" value="<?=$key?>">
												</td>
												<td>
													<?=$value?>
												</td>
												<td style="padding: 0px !important;">
													<a href="#" class="up <?=$c?>" data-col="<?=$c?>" data-value="<?=$value?>"><i class="fa fa-caret-up" style="font-size:16px"></i></a>
													<br>
													<a href="#" class="down <?=$c?>" data-col="<?=$c++?>" data-value="<?=$value?>"><i class="fa fa-caret-down" style="font-size:16px"></i></a>
												</td>
												
											</tr>
										<?php
									}
								?>
							</tbody>
						</table>
					</div>
				</div>

				
			</form>
			<div class="col-md-offset-4 col-md-4 text-center">
					<button class="btn btn-primary" type="button" data-value="0" onclick="submited(0)">รายงาน</button>
					<button class="btn btn-primary" type="button" data-value="1" onclick="submited(1)"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span> Excel</button>
				</div>
		</div>
	</div>
</div>

<script>
var status_check = true;
$( document ).ready(function() {
		$(".mydate").datepicker({
			prevText : "ก่อนหน้า",
			nextText: "ถัดไป",
			currentText: "Today",
			changeMonth: true,
			changeYear: true,
			isBuddhist: true,
			monthNamesShort: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
			dayNamesMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
			constrainInput: true,
			dateFormat: "dd/mm/yy",
			yearRange: "c-50:c+10",
			autoclose: true,
		});
});
$("#checkAll").click(function(){
    $('.column').not(this).prop('checked', status_check);
	status_check = !status_check;
});

$(".up").click(function(){
	var rowCount = $('#table tr').length - 1;
    var col = $( this ).data( "col" );
	var element = $( this );
	if(col==0){
		return;
	}

	$("#col_"+col).after($("#col_"+(col-1)));
	var c = 0;
	$(".up").each(function() {
		$(this).data("col", c);
		c++;
	});

	c = 0;
	$(".down").each(function() {
		$(this).data("col", c);
		c++;
	});

	var count = 0;
	$("#table tr").each(function() {
		jQuery(this).attr("id","col_"+(count-1));
		count++;
	});

});

$(".down").click(function(){
	var rowCount = $('#table tr').length - 1;
    var col = $( this ).data( "col" );
	console.log("down: "+ col)
	var element = $( this );
	if(col >= rowCount-1){
		console.log("returnnn");
		return;
	}

	$("#col_"+(col)).before($("#col_"+(col+1)));

	var c = 0;
	$(".up").each(function() {
		$(this).data("col", c);
		c++;
	});

	c = 0
	$(".down").each(function() {
		$(this).data("col", c);
		c++;
	});

	

	var count = 0;
	$("#table tr").each(function() {
		jQuery(this).attr("id","col_"+(count-1));
		count++;
	});

});

function submited(isExcel){
	console.log("isExcel: "+isExcel);
	if(isExcel==0){
		$('#coop_report_account_status_detail_preview').attr('action', base_url+'Report_deposit_data/coop_report_account_status_detail_preview');
	}else{
		$('#coop_report_account_status_detail_preview').attr('action', base_url+'Report_deposit_data/coop_report_account_status_detail_preview?download=excel');
		console.log("change to!");
	}

	$("#coop_report_account_status_detail_preview").submit();
}
</script>
