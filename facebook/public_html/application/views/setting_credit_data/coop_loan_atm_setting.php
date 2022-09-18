<div class="layout-content">
    <div class="layout-content-body">
	<style>
		label{
			padding-top:7px;
		}
		.control-label{
			padding-top:7px;
			text-align: right;
		}
		.control-label_2{
			padding-top:7px;
		}
		.indent{
				text-indent: 40px;
				.modal-dialog-data {
					width:90% !important;
					margin:auto;
					margin-top:1%;
					margin-bottom:1%;
				}
			}
			table>thead>tr>th{
				text-align: center;
			}
			table>tbody>tr>td{
				text-align: center;
			}

			.text-center{
				text-align:center;
			}
			.text-right{
				text-align:right;
			}
			.bt-add{
				float:none;
			}
			.modal-dialog{
				width:80%;
			}
			small{
				display: none !important;
			}
	</style>
		
		<?php
		$act = @$_GET['act'];
		$id  = @$_GET['id'];
		?> 
		<h1 style="margin-bottom: 0">ตั้งค่ากู้เงินฉุกเฉิน ATM</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<?php if ($act != "add") { ?>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">	
					<a class="link-line-none" href="?act=add">
						<button class="btn btn-primary btn-lg bt-add" type="button"><span class="icon icon-plus-circle"></span> เพิ่มรายการ </button>
					</a>
				</div>
			<?php } ?>
		</div>

		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<?php if ($act != "add") { ?>
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-striped"> 
								<thead> 
									<tr>
										<th class = "font-normal" width="5%">#</th>
										<th class = "font-normal" style="width: 31%"> รหัสสัญญา </th>
										<th class = "font-normal" style="width: 10%"> วงเงินกู้สูงสุด </th>
										<th class = "font-normal text-center" style="width: 8%"> อัตราดอกเบี้ย </th>
										<th class = "font-normal text-center" style="width: 10%"> มีผลวันที่ </th>
										<th class = "font-normal text-center" style="width: 8%"> สถานะ </th>
										<th class = "font-normal" style="width: 8%"> จัดการ </th>
									</tr> 
								</thead>
								<tbody>
								<?php  
								if(!empty($rs)){
									foreach(@$rs as $key => $row){
										$this->db->select(array('*'));
										$this->db->from('coop_loan_atm_setting_template');
										$this->db->where("start_date <= '".date('Y-m-d')."'");
										$this->db->order_by('start_date DESC, run_id DESC');
										$row_status = $this->db->get()->row_array();
									?>
										<tr> 
											<td scope="row"><?php echo $i++; ?></td>
											<td><?php echo @$row['prefix_code']; ?></td> 
											<td><?php echo @$row['max_loan_amount']; ?></td> 
											<td class="text-center"><?php echo @$row['interest_rate']; ?></td> 
											<td class="text-center"><?php echo $this->center_function->ConvertToThaiDate(@$row['start_date'],'1','0'); ?></td> 
											<td><?php echo $row_status['run_id']==@$row['run_id']?'ใช้งาน':'ไม่ใช้งาน'; ?></td>
											<td>
											<a href="?act=add&id=<?php echo @$row["run_id"] ?>">แก้ไข</a> |
											<a href="#" onclick="del_data('<?php echo @$row['run_id']; ?>')" class="text-del"> ลบ </a>
											</td> 
										</tr>
									<?php 
									}
								} 
								?>
								</tbody> 
							</table>
						</div>
					<?php } else { ?>
						<form id='form_save' action="<?php echo base_url(PROJECTPATH.'/setting_credit_data/coop_loan_atm_setting_save'); ?>" method="post" data-toggle="validator" novalidate="novalidate" >	
						<input name="id"  type="hidden" value="<?php echo @$row['run_id']; ?>">
							<div class="row">					
								<label class="col-sm-4 control-label text-right" >มีผลวันที่</label>
								<div class="col-sm-4">
									<input id="start_date" name="start_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(empty($row['start_date']) ? date('Y-m-d') : @$row['start_date']); ?>" data-date-language="th-th" required>
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
								<label class="col-sm-4 control-label" >&nbsp;</label>
							</div>
							<div class="row">
								<label class="col-sm-4 control-label text-right" >รหัสนำหน้าสัญญา</label>
								<div class="col-sm-4">
									<input  name="prefix_code" id="prefix_code" class="form-control m-b-1" type="text" value="<?php echo (@$row['prefix_code'] == '')?'':@$row['prefix_code']; ?>">
								</div>
								<label class="col-sm-4 control-label_2" >บาท</label>
							</div>
							<div class="row">
								<label class="col-sm-4 control-label text-right" >วงเงินกู้</label>
								<div class="col-sm-4">
									<input  name="max_loan_amount" id="max_loan_amount" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['max_loan_amount'] == '0')?'':@$row['max_loan_amount']; ?>">
								</div>
								<label class="col-sm-4 control-label_2" >บาท</label>
							</div>
							<div class="row">
								<label class="col-sm-4 control-label text-right" >อัตราดอกเบี้ย</label>
								<div class="col-sm-4">
									<input  name="interest_rate" id="interest_rate" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['interest_rate'] == '0')?'':@$row['interest_rate']; ?>">
								</div>
								<label class="col-sm-4 control-label_2" >%</label>
							</div>
							<div class="row">
								<label class="col-sm-4 control-label text-right" >ถอนเงิน กู้เงิน รวมกัน ฟรี</label>
								<div class="col-sm-4">
									<input  name="use_atm_count" id="use_atm_count" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['use_atm_count'] == '0')?'':@$row['use_atm_count']; ?>">
								</div>
								<label class="col-sm-4 control-label_2" >ครั้งต่อเดือน</label>
							</div>
							<div class="row">
								<label class="col-sm-4 control-label text-right" >เกินกว่านั้นมีค่าบริการ</label>
								<div class="col-sm-4">
									<input  name="use_atm_over_count_fee" id="use_atm_over_count_fee" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['use_atm_over_count_fee'] == '0')?'':@$row['use_atm_over_count_fee']; ?>">
								</div>
								<label class="col-sm-4 control-label_2" >บาทต่อครั้ง</label>
							</div>
							<div class="row">
								<label class="col-sm-4 control-label text-right" >เรียกเก็บขั้นต่ำ</label>
								<div class="col-sm-4">
									<input  name="min_loan_amount" id="min_loan_amount" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['min_loan_amount'] == '0')?'':@$row['min_loan_amount']; ?>">
								</div>
								<label class="col-sm-4 control-label_2" >บาท</label>
							</div>
							<div class="row">
								<label class="col-sm-4 control-label text-right" >กู้ได้หลังจากส่งหุ้นแล้ว</label>
								<div class="col-sm-4">
									<input  name="min_month_share" id="min_month_share" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['min_month_share'] == '0')?'':@$row['min_month_share']; ?>">
								</div>
								<label class="col-sm-4 control-label_2" >เดือน</label>
							</div>
							<div class="row">
								<label class="col-sm-4 control-label text-right" >จำนวนงวด</label>
								<div class="col-sm-4">
									<input  name="max_period" id="max_period" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['max_period'] == '0')?'':@$row['max_period']; ?>">
								</div>
								<label class="col-sm-4 control-label_2" >งวด</label>
							</div>
							<div class="row">
								<label class="col-sm-4 control-label text-right" >ถอนเงินได้ไม่เกินวันละ</label>
								<div class="col-sm-4">
									<input  name="max_withdraw_amount_day" id="max_withdraw_amount_day" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['max_withdraw_amount_day'] == '0')?'':@$row['max_withdraw_amount_day']; ?>">
								</div>
								<label class="col-sm-4 control-label_2" >บาท</label>
							</div>
							<div class="form-group text-center">&nbsp;</div>
	
							<div class="form-group text-center">
								<button type="submit" class="btn btn-primary min-width-100">ตกลง</button>
								<a href="?"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
							</div>
	
						</form>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var base_url = $('#base_url').attr('class');
	$( document ).ready(function() {
		$("#start_date").datepicker({
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
			//startDate: '+0d',
			autoclose: true,
		});
	});
	
	function del_data(id){
		swal({
			title: "ท่านต้องการลบข้อมูลนี้ใช่หรือไม่",
			text: "",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ลบ',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: false,
			closeOnCancel: true
		},
		function(isConfirm) {
			if (isConfirm) {			
				$.ajax({
					url: base_url+'/setting_credit_data/coop_loan_atm_setting_delete',
					method: 'POST',
					data: {
						'id': id
					},
					success: function(msg){
					  //console.log(msg); return false;
						if(msg == 1){
						  document.location.href = base_url+'setting_credit_data/coop_loan_atm_setting';
						}else{
	
						}
					}
				});
			} else {
				
			}
		});
		
	}
</script>