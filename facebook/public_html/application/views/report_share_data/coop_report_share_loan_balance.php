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
				<form action="<?php echo base_url(PROJECTPATH.'/report_share_data/coop_report_share_loan_balance_preview'); ?>" id="form1" method="GET" target="_blank">
		  			<input type="hidden" name="sms_file_type" id="sms_file_type" value="excel"/>
					<h3></h3>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> รูปแบบการค้นหา </label>
						<div class="g24-col-sm-4">
							<select name="type_date" id="type_date" onchange="change_type_date()" class="form-control">
							    <option value="">เลือกรูปแบบการค้นหา</option>
								<option value="1">ทั้งหมดถึงวันที่เลือก</option>
								<option value="2">เฉพาะวันที่เลือก</option>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> วันที่ </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" autocomplete="off" >
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
<!--                        <div class="box_end_date hide">-->
                        <label class="g24-col-sm-1 control-label right box_end_date hide"> ถึง </label>
                        <div class="g24-col-sm-4">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="end_date" name="end_date" class="form-control m-b-1 mydate box_end_date hide" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" autocomplete="off" >
                                    <span class="icon icon-calendar input-icon m-f-1 box_end_date hide"></span>
                                </div>
                            </div>
                        </div>
<!--                        </div>-->
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> รูปแบบ </label>
						<div class="g24-col-sm-4">
							<select name="type_department" id="type_department" onchange="" class="form-control">
							    <option value="">เลือกรูปแบบ</option>
								<option value="1">หน่วยงานหลัก</option>
								<option value="2">หน่วยงานย่อย</option>
								<option value="3">รายบุคคล</option>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"></label>
						<div class="g24-col-sm-3">
							<input type="button" class="btn btn-primary" style="width:100%" value="แสดงรายงาน" onclick="change_type()">
						</div>
						<div class="g24-col-sm-3">
							<input type="button" class="btn btn-default" style="width:100%" value="Export Excel" onclick="export_excel()">
						</div>
						<!--<div class="g24-col-sm-3">
							<input type="button" class="btn btn-default" style="width:100%" value="Export SMS Excel" onclick="export_sms_excel()">
						</div>
						<div class="g24-col-sm-3">
							<input type="button" class="btn btn-default" style="width:100%" value="Export SMS CSV" onclick="export_sms_csv()">
						</div>-->
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
				link_to =  base_url+'report_share_data/coop_report_share_loan_balance_excel';
            }else if($('#type_department').val() == '2'){
                link_to = base_url + 'report_share_data/coop_report_share_loan_balance_loan_type_excel';
			}else{
				link_to =  base_url+'report_share_data/coop_report_share_loan_balance_person_excel';
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

    function change_type_date() {
        var type = document.getElementById('type_date').value;
        if(type == 2){
            $(".box_end_date").removeClass("hide");
        }else{
            $(".box_end_date").addClass("hide");
        }
    }

</script>


