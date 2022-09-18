<div class="layout-content">
    <div class="layout-content-body">
<style>
    /*.form-group { margin-bottom: 0; }*/
    .border1 { border: solid 1px #ccc; padding: 0 15px; }
    .mem_pic { float: right; width: 150px; }
    .mem_pic img { width: 100%; border: solid 1px #ccc; }
    .mem_pic button { display: block; width: 100%; }

    .hide_error{color : inherit;border-color : inherit;}

    .has-error{color : #d50000;border-color : #d50000;}

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .alert-danger {
        background-color: #F2DEDE;
        border-color: #e0b1b8;
        color: #B94A48;
    }
    .modal-backdrop.in{
        opacity: 0;
    }
    .modal-backdrop {
        position: relative;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1040;
        background-color: #000;
    }
    .modal.fade {
        z-index: 10000000 !important;
    }
	.control-label{
		text-align: right;
		margin-bottom: 0;
		padding-top: 7px;
	}
	th{
		text-align: center;
	}
	
	.modal-dialog-data {
		width:60% !important;
		margin:auto;
		margin-top:1%;
		margin-bottom:1%;
	}

	.radio-div {
		padding-top: 7px;
	}
	.bottom-search-div {
		padding-bottom: 10px;
	}
	.resign {
		background-color: #ffc286 !important;
	}
</style>
<h1 style="margin-bottom: 0">อนุมัติคืนเงินหลังประมวลผล</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php
		$get_param = '?month='.$month.'&year='.$year;
		foreach(@$_GET as $key => $value){
			if($key != 'month' && $key != 'year' && $value != ''){
				$get_param .= '&'.$key.'='.$value;
			}
		}
		?>
	</div>
</div>
<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12">
        <div class="panel panel-body">
			<form method="GET" id="from_1" action="">
				<div class="g24-col-sm-24">
					<label class="g24-col-sm-3 control-label">ปี</label>
					<div class="g24-col-sm-3 m-b-1">
						<select class="form-control" name="year">
							<?php for($y=(date('Y')+540);$y<=(date('Y')+546);$y++){ ?>
								<option value="<?php echo $y; ?>" <?php echo $y==$year?'selected':''; ?>><?php echo $y; ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="g24-col-sm-2 control-label">เดือน</label>
					<div class="g24-col-sm-3 m-b-1">
						<select class="form-control" name="month">
							<?php foreach($month_arr as $key => $value){ ?>
								<option value="<?php echo $key; ?>" <?php echo $key==$month?'selected':''; ?>><?php echo $value; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="g24-col-sm-24">
					<label class="g24-col-sm-3 control-label">หน่วยงานหลัก</label>
					<div class="g24-col-sm-3 m-b-1">
						<select class="form-control" name="department" id="department" onchange="change_mem_group('department', 'faction')">
							<option value="">เลือกข้อมูล</option>
							<?php foreach($mem_group as $key => $value){ ?>
								<option value="<?php echo $value['id']; ?>" <?php echo $_GET['department']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="g24-col-sm-2 control-label right"> ฝ่าย </label>
					<div class="g24-col-sm-3">
						<select name="faction" id="faction" onchange="change_mem_group('faction','level')" class="form-control">
							<option value="">เลือกข้อมูล</option>
							<?php foreach($faction as $key => $value){ ?>
								<option value="<?php echo $value['id']; ?>" <?php echo $_GET['faction']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="g24-col-sm-4 g24-col-md-3 control-label right"> สังกัด </label>
					<div class="g24-col-sm-3">
						<select name="level" id="level" class="form-control">
							<option value="">เลือกข้อมูล</option>
							<?php foreach($level as $key => $value){ ?>
								<option value="<?php echo $value['id']; ?>" <?php echo $_GET['level']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="g24-col-sm-24">
					<label class="g24-col-sm-3 control-label">ประเภทสมาชิก</label>
					<div class="g24-col-sm-3 m-b-1">
						<select class="form-control" name="mem_type_id" id="mem_type_id">
							<option value="">เลือกข้อมูล</option>
							<?php foreach($mem_type as $key => $value){ ?>
								<option value="<?php echo $value['mem_type_id']; ?>" <?php echo @$_GET['mem_type_id']==$value['mem_type_id']?'selected':''; ?>><?php echo $value['mem_type_name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="g24-col-sm-2 control-label right"> รหัสสมาชิก </label>
					<div class="g24-col-sm-3">
						<input class="form-control" type="text" name="member_id" id="member_id" value="<?php echo @$_GET['member_id']; ?>">
					</div>
					<label class="g24-col-sm-4 g24-col-md-3 control-label right"> จำนวนรายการ </label>
					<div class="g24-col-sm-3">
						<select name="show_row" id="show_row" class="form-control">
							<option value="20" <?php echo $_GET['show_row']=='20'?'selected':''; ?>>20</option>
							<option value="50" <?php echo $_GET['show_row']=='50'?'selected':''; ?>>50</option>
							<option value="100" <?php echo $_GET['show_row']=='100'?'selected':''; ?>>100</option>
						</select>
					</div>
				</div>
				<div class="g24-col-sm-24">
					<label class="g24-col-sm-3 control-label">ประเภท</label>
					<div class="g24-col-sm-21 m-b-1 radio-div" >
						<input type="radio" id="return_from_all" name="return_from" value="all" <?php echo $_GET['return_from']!='monthly' && $_GET['return_from']!='occasional'?' checked="checked"':'';?>/>
						<span>ทั้งหมด</span>&nbsp&nbsp
						<input type="radio" id="return_from_monthly" name="return_from" value="monthly" <?php echo $_GET['return_from']=='monthly'?' checked="checked"':'';?>/>
						<span>การชำระเงินผ่านรายการเรียกเก็บรายเดือน</span>&nbsp&nbsp
						<input type="radio" id="return_from_occasional" name="return_from" value="occasional" <?php echo $_GET['return_from']=='occasional'?' checked="checked"':'';?>/>
						<span>การชำระเงินผ่านเคาน์เตอร์</span>
					</div>
				</div>
				<div class="g24-col-sm-24 bottom-search-div">
					<div class="g24-col-sm-2">
						<input type="button" class="btn btn-primary" value="อนุมัติ" onclick="approve_check_return('all')">
					</div>
					<label class="g24-col-sm-1 control-label right"></label>
					<div class="g24-col-sm-2">
						<input type="button" class="btn btn-primary" id="search-btn" value="ค้นหา">
					</div>
					<div class="g24-col-sm-2">
						<input type="button" class="btn btn-primary" id="export-excel-btn" value="Export Excel">
					</div>
				</div>
			</form>
            <div class="bs-example" data-example-id="striped-table">
				<table class="table table-bordered table-striped table-center">
					<thead>
					<tr class="bg-primary">
						<th><input type="checkbox" class="return-id-checkbox-all check_box" id="return-id-all" name="ids[]" value="all"></th>
						<th>ลำดับ</th>
						<th>รหัสสมาชิก</th>
						<th>ชื่อ - นามสกุล</th>
						<th>หน่วยงาน</th>
						<th>คืนเงินจาก</th>
						<th>จำนวนเงิน</th>
						<th width="120px"></th>
					</tr>
					</thead>
					<tbody id="table_data">
						<form id="list-submit" action="<?php echo base_url(PROJECTPATH.'/finance/approve_return'); ?>" method="POST">
						<!-- <form id="list-submit" action="<?php echo base_url('/finance/approve_return'); ?>" method="POST"> -->
							<input type="hidden" name="month" value="<?php echo $month; ?>">
							<input type="hidden" name="year" value="<?php echo $year; ?>">
							<input type="hidden" name="page" value="<?php echo $_GET['page']; ?>">
							<input type="hidden" name="faction" value="<?php echo $_GET['faction']; ?>">
							<input type="hidden" name="department" value="<?php echo $_GET['department']; ?>">
							<input type="hidden" name="level" value="<?php echo $_GET['level']; ?>">
							<input type="hidden" name="mem_type_id" value="<?php echo $_GET['mem_type_id']; ?>">
							<input type="hidden" name="member_id" value="<?php echo $_GET['member_id']; ?>">
							<input type="hidden" name="show_row" value="<?php echo $_GET['show_row']; ?>">
							<input type="hidden" name="all" value="<?php echo $_GET['all']; ?>">
					<?php 	
					if(!empty($row)){				
						foreach($row as $key => $value){ 
					?>
						<tr class="<?php echo $value['mem_type'] == 2 ? 'resign' : ''?>">
							<td>
								<?php if($value['return_profile_status'] == '0'){ ?>
									<input type="checkbox" class="return-id-checkbox check_box" id="return-id-<?php echo $value['return_profile_id']; ?>" name="return_profile_id[]" value="<?php echo $value['return_profile_id']; ?>">
								<?php } ?>
							</td>
							<td>
								<?php echo $i++; ?>
							</td>
							<td><?php echo $value['member_id']; ?></td>
							<td style="text-align:left;"><?php echo $value['prename_short'].$value['firstname_th']." ".$value['lastname_th']; ?></td>
							<td ><?php echo $value['mem_group_name']; ?></td>
							<td>
								<?php
									if ($value['return_from'] == "occasional"){
										echo "ชำระเงินผ่านเคาน์เตอร์";
									} else {
										echo "ชำระเงินผ่านรายการเรียกเก็บรายเดือน";
									}
								?>
							</td>
							<td style="text-align:right;"><?php echo number_format($value['total_return_amount'],2); ?></td>
							<td >
							<?php if($value['return_profile_status'] == '0'){ ?>
							<input type="button" class="btn btn-primary" value="อนุมัติ" onclick="approve_return('<?php echo $value['return_profile_id']; ?>')">
							<?php } ?>
							</td>
						</tr>
					<?php 
						} 
					}
					?>
						</form>
					</tbody>
				</table>
            </div>
        </div>
        <div id="page_wrap">
            <?php echo @$paging ?>
        </div>
    </div>
</div>
    </div>
</div>
<div class="modal fade" id="approve_return_modal" role="dialog"> 
    <div class="modal-dialog modal-dialog-file">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3 class="modal-title">รายการคืนเงิน</h3>
        </div>
        <div class="modal-body">
		<form action="<?php echo base_url(PROJECTPATH.'/finance/approve_return'); ?>" method="POST">
		<!-- <form action="<?php echo base_url('/finance/approve_return'); ?>" method="POST"> -->
			<input type="hidden" id="return_profile_id" name="return_profile_id[]">
			<input type="hidden" name="month" value="<?php echo $month; ?>">
			<input type="hidden" name="year" value="<?php echo $year; ?>">
				<div class="bs-example" data-example-id="striped-table">
					<table class="table table-bordered table-striped table-center">
						<thead>
							<tr class="bg-primary">
								<th>รายการ</th>
								<th>จำนวนเงิน</th>
							</tr>
						<thead>
						<tbody id="return_detail">
						</tbody>
					</table>
				</div>
				<div class="row m-b-1">
					<div class="col-sm-12">
						<div class="form-group" style="text-align:center">
							<input type="submit" class="btn btn-primary" value="อนุมัติ">
						</div>
					</div>
				</div>
			</form>
        </div>
      </div>
    </div>
</div>
<script>
	function approve_return(return_profile_id){
		$('#return_profile_id').val(return_profile_id);
		$.post(base_url+"finance/get_return_detail", 
		// $.post("/spktcoop/system.spktcoop.com/finance/get_return_detail", 
		{	
			return_profile_id: return_profile_id
		}
		, function(result){
			$('#return_detail').html(result);
			$('#approve_return_modal').modal('show');
		});
	}
	function change_mem_group(id, id_to){
		var mem_group_id = $('#'+id).val();
		$('#level').html('<option value="">เลือกข้อมูล</option>');
		$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/get_mem_group_list',
			data: {
				mem_group_id : mem_group_id
			},
			success: function(msg){
				$('#'+id_to).html(msg);
			}
		});
	}

	function approve_check_return(){
		swal({
        title: "ท่านต้องการอุมนัติรายการใช่หรือไม่?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: "ยกเลิก",
        closeOnConfirm: false,
        closeOnCancel: true
		},
		function(isConfirm) {
			if (isConfirm) {
				$("#list-submit").submit()
			} else {
				
			}
		});
	}

	$(document).ready(function() {
		$("#return-id-all").click(function() {
			if($('#return-id-all').is(':checked')){
				$('.return-id-checkbox').attr('checked',true);
			}else{
				$('.return-id-checkbox').attr('checked',false);
			}
		});

		$("#export-excel-btn").click(function(){
			$("#from_1").attr("action", "<?php echo base_url(PROJECTPATH.'/finance/finance_month_return_excel'); ?>")
			$("#from_1").submit()
		})

		$("#search-btn").click(function(){
			$("#from_1").attr("action", "")
			$("#from_1").submit()
		})
	});
</script>
