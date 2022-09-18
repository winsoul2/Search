<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.modal-header-alert {
				padding:9px 15px;
				border:1px solid #FF0033;
				background-color: #FF0033;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}
			.center {
				text-align: center;
			}
			.right {
				text-align: right;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			label{
				padding-top:7px;
			}
		</style>

		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">รายงานสรุปทุนเรือนหุ้น-เงินกู้คงเหลือ</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/report_share_data/coop_report_loan_share_balance_excel'); ?>" id="form1" method="GET" target="_blank">
		  			<input type="hidden" name="sms_file_type" id="sms_file_type" value="excel"/>
					<h3></h3>
					<input name="type_date" id="type_date"  type="hidden" value="1">
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> เดือน </label>
						<div class="g24-col-sm-4">
							<select id="month" name="month" class="form-control">
								<?php foreach($month_arr as $key => $value){ ?>
									<option value="<?php echo $key; ?>" <?php echo $key==((int)date('m'))?'selected':''; ?>><?php echo $value; ?></option>
								<?php } ?>
							</select>
						</div>
						<label class="g24-col-sm-1 control-label right"> ปี </label>
						<div class="g24-col-sm-4">
							<select id="year" name="year" class="form-control">
								<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
									<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> ประเภทรายงาน </label>
						<div class="g24-col-sm-4">
							<select name="report_type" id="report_type" onchange="" class="form-control">
								<option value="1">รายหน่วยงาน</option>
								<option value="2">รายบุคคล</option>
							</select>
						</div>
					</div>
					<!-- <input type="hidden" name="report_type" value="1"/> -->
					<input name="type_department" id="type_department" type="hidden" value="2">
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"></label>
						<div class="g24-col-sm-3">
							<input type="button" class="btn btn-default" style="width:100%" value="Export Excel" onclick="export_excel()">
						</div>
					</div>
				</form>				
				</div>
			</div>
		</div>
	</div>
</div>
<script>	
	$( document ).ready(function() {
		$(".show_department").hide();
		$(".show_level").hide();
		$("#type_department").change(function() {
			var type_department = $(this).val();
			if(type_department == '2'){
				$(".show_department").hide();
				$(".show_level").hide();
			}else{
				$(".show_department").hide();
				$(".show_level").hide();
			}
		});
		
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
	
	function change_type(id){
		var link_to = '';
		
		if($('#type_date').val() == ''){
			swal("กรุณาเลือกรูปแบบวันที่การค้นหาข้อมูล");
		}else if($('#type_department').val() == ''){
			swal("กรุณาเลือกรูปแบบการค้นหาข้อมูล");
		}else{		
			if($('#type_department').val() == '1' ) {
                link_to = base_url + 'report_share_data/coop_report_share_loan_balance_preview';
            }else if($('#type_department').val() == '2'){
                link_to = base_url + 'report_share_data/coop_report_share_loan_balance_loan_type_preview';
			}else{
				link_to =  base_url+'report_share_data/coop_report_share_loan_balance_person_preview';
			}
			$('#form1').attr('action', link_to);
			$('#form1').submit();
		}
	}

	function export_excel(){
		var link_to = '';
		
		if($('#type_date').val() == ''){
			swal("กรุณาเลือกรูปแบบวันที่การค้นหาข้อมูล");
		}else if($('#type_department').val() == ''){
			swal("กรุณาเลือกรูปแบบการค้นหาข้อมูล");
		}else{		
			if($('#type_department').val() == '1'){
				link_to =  base_url+'report_share_data/coop_report_loan_share_balance_excel';
            }else if($('#type_department').val() == '2'){
                link_to = base_url + 'report_share_data/coop_report_loan_share_balance_loan_type_excel';
			}else{
				link_to =  base_url+'report_share_data/coop_report_loan_share_balance_person_excel';
			}
			$('#form1').attr('action', link_to);
			$('#form1').submit();
		}
	}

	function export_sms_excel(){
		var link_to = '';

		if($('#type_date').val() == '') {
			swal("กรุณาเลือกรูปแบบวันที่การค้นหาข้อมูล");
		} else{
			link_to =  base_url+'report_share_data/coop_report_share_loan_balance_sms_person';
			// link_to =  '/spktcoop/system.spktcoop.com/report_share_data/coop_report_share_loan_balance_sms_person';
			$("#sms_file_type").val("excel");
			$('#form1').attr('action', link_to);
			$('#form1').submit();
		}
	}

	function export_sms_csv(){
		var link_to = '';

		if($('#type_date').val() == '') {
			swal("กรุณาเลือกรูปแบบวันที่การค้นหาข้อมูล");
		} else{
			link_to =  base_url+'report_share_data/coop_report_share_loan_balance_sms_person';
			$("#sms_file_type").val("csv");
			// link_to =  '/spktcoop/system.spktcoop.com/report_share_data/coop_report_share_loan_balance_sms_person';
			$('#form1').attr('action', link_to);
			$('#form1').submit();
		}
	}

</script>


