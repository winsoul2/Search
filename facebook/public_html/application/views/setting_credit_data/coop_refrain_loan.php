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
		<h1 style="margin-bottom: 0">ตั้งค่างดต้นเงินกู้</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>

		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<div class="form-group text-center">&nbsp;</div>
					<form id='form_save' action="<?php echo base_url(PROJECTPATH.'/setting_credit_data/coop_refrain_loan'); ?>" method="post" data-toggle="validator" novalidate="novalidate" >	
					<input name="id"  type="hidden" value="<?php echo @$row['id']; ?>">
						<div class="row">
							<label class="col-sm-5 control-label text-right" >สัญญางดได้ไม่เกิน</label>
							<div class="col-sm-2">
								<input  name="max_period_month" id="max_period_month" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['max_period_month'] == '')?'':@$row['max_period_month']; ?>">
							</div>
							<label class="col-sm-4 control-label_2" >เดือน</label>
						</div>
						<div class="row">
							<label class="col-sm-5 control-label text-right" >ครั้งละไม่เกิน</label>
							<div class="col-sm-2">
								<input  name="max_time_month" id="max_time_month" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['max_time_month'] == '0')?'':@$row['max_time_month']; ?>">
							</div>
							<label class="col-sm-4 control-label_2" >เดือน</label>
						</div>
						<div class="row">
							<label class="col-sm-5 control-label text-right" >ปีละไม่เกิน</label>
							<div class="col-sm-2">
								<input  name="max_year_time" id="max_year_time" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['max_year_time'] == '0')?'':@$row['max_year_time']; ?>">
							</div>
							<label class="col-sm-4 control-label_2" >ครั้ง</label>
						</div>
						
						<div class="form-group text-center">&nbsp;</div>

						<div class="form-group text-center">
							<button type="button" class="btn btn-primary min-width-100" onclick="submit_form()">ตกลง</button>
							<a href="?"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
						</div>

					</form>
				</div>
			</div>
		</div>
	</div>
</div>


<script>
 function submit_form(){
	var text_alert = '';
	if($.trim($('#max_period_month').val())== ''){
		text_alert += ' - จำนวนเดือนที่สัญญางดได้\n';
	}
	if($.trim($('#max_time_month').val())== ''){
		text_alert += ' - จำนวนครั้งละที่งดต่อเดือน\n';
	}
	
	if($.trim($('#max_year_time').val())== ''){
		text_alert += ' - จำนวนครั้งที่งดได้ต่อปี\n';
	}
	
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form_save').submit();
	}
 }
</script>
