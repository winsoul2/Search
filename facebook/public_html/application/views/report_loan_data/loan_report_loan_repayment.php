<style>
	.modal-dialog {
        width: 700px;
    }
</style>
<div class="layout-content">
    <div class="layout-content-body">
		<?php
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		?>
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
		<h1 style="margin-bottom: 0">รายงานการถอนเงินสามัญหมุนเวียน</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/report_loan_data/coop_report_loan_repayment_preview'); ?>" id="form1" method="GET" target="_blank">
					<br>
                    <div class="form-group g24-col-sm-24" style="margin-top: 10px;">
						<label class="g24-col-sm-6 control-label right"> เลือกสัญญา </label>
						<div class="g24-col-sm-4">
                            <label class="radio-inline">
                                <input type="radio" name="select_type_contract" value="all" id="select_all_contract" checked=""> เลือกทั้งหมด
                            </label>
						</div>
                        <div class="g24-col-sm-4">
                            <label class="radio-inline">
                                <input type="radio" name="select_type_contract" value="select_contract" id="select_contract"> ระบุสัญญา
                            </label>
						</div>
					</div>

					<div class="form-group g24-col-sm-24" id="custom_contract_number" style="display: none;margin-top: 10px;">
						<label class="g24-col-sm-6 control-label right"> เลขที่สัญญา </label>
						<div class="g24-col-sm-11">
                            <div class="form-group">
								<div class="input-group">
									<input id="contract_number" name="contract_number" class="form-control m-b-1" type="text" autocomplete="off">
									<span class="input-group-btn">
										<button type="button" onclick="check_search();" class="btn btn-info btn-search"><span class="icon icon-search" ></span></button>
									</span>
								</div>
							</div>
						</div>
					</div>

                    <div class="form-group g24-col-sm-24"  style="margin-top: 10px;">
						<label class="g24-col-sm-6 control-label right"> สถานะ </label>
						<div class="g24-col-sm-11">
							<select name="status" class="form-control">
                                <option value="all">เลือกทั้งหมด</option>
                                <option value="1">จ่ายเงินแล้ว</option>
                                <option value="0">รอจ่ายเงิน</option>
                            </select>
						</div>
					</div>

                    <div class="form-group g24-col-sm-24"  style="margin-top: 10px;">
						<label class="g24-col-sm-6 control-label right"> เลือกวัน </label>
						<div class="g24-col-sm-4">
                            <label class="radio-inline">
                                <input type="radio" name="select_type_date" value="all" id="select_all" checked=""> เลือกทั้งหมด
                            </label>
						</div>
                        <div class="g24-col-sm-4">
                            <label class="radio-inline">
                                <input type="radio" name="select_type_date" value="select_date" id="select_date"> เลือกวันที่
                            </label>
						</div>
					</div>

					<div class="form-group g24-col-sm-24" id="custom_select" style="display: none;margin-top: 10px;">
						<label class="g24-col-sm-6 control-label right"> วันที่ </label>
						<div class="g24-col-sm-11">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
					</div>

                    

                    


					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"></label>
						<div class="g24-col-sm-11">
							<!-- <input type="submit" class="btn btn-primary" style="width:100%" value="รายงานการถอนเงินสามัญหมุนเวียน" > -->
							<button id="btn_link_preview" type="submit" name="view" value="preview" class="btn btn-primary" style="width:100%">
								รายงานการถอนเงินสามัญหมุนเวียน
							</button>
						</div>
						<!-- <div class="g24-col-sm-4">
							<button id="btn_link_export" type="submit" name="view" value="excel" class="btn btn-default" style="width:100%">
								Export Excel
							</button>
						</div> -->
					</div>
				</form>				
				</div>
			</div>
		</div>
	</div>
</div>
  
<script>	
	var base_url = $('#base_url').attr('class');
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
		$("#type_date").change(function() {
			if($(this).val() == 1) {
				$(".end-date-label").hide();
			} else {
				$(".end-date-label").show();
			}
		});
		
		function link_export() {
			$("#btn_link_export").prop("href", base_url + "/report_deposit_data/coop_report_gov_bank_excel?type_id=" + $("#type_id").val() + " &start_date=" + $("#start_date").val());
		}
		$("#type_id").change(function() {
			link_export();
		});
		$("#start_date").change(function() {
			link_export();
		});
	});

    $( "#select_contract" ).click(function() {
        $("#custom_contract_number").show();
    });

    $( "#select_all_contract" ).click(function() {
        $("#custom_contract_number").hide();
    });


    $( "#select_date" ).click(function() {
        $("#custom_select").show();
    });

    $( "#select_all" ).click(function() {
        $("#custom_select").hide();
    });


    $("form").submit(function(e){
        // e.preventDefault();
        var check_contract = $('input[name=select_type_contract]:checked', '#form1').val();
        var check_date = $('input[name=select_type_date]:checked', '#form1').val();

        if(check_contract!="all" && ($("#contract_number").val()=="" || $("#contract_number").val()==undefined) ){
            swal("ระบุเลขที่สัญญา", "", "warning");
            return false;
        }

        if(check_date!="all" && ($("#start_date").val()=="" || $("#start_date").val()==undefined) ){
            swal("เลือกวันที่", "", "warning");
            return false;
        }
        console.log(check_contract, check_date);
        return true;
    });
    
    $('#contract_number').keyup(function(e){
        if(e.keyCode == 13)
        {
            $.ajax({
                url: base_url+'/ajax/search_loan_repayment_by_contract_number',
                method: 'POST',
                data: {
                    'search_text': $("#contract_number").val(),
                },
                success: function(msg){
                    if(msg=="FALSE"){
                        swal('ไม่พบเลขที่สัญญานี้', '', 'warning');
                    }
                }
            });
        }
    });

    $( "#contract_number" ).change(function() {
        $.ajax({
                url: base_url+'/ajax/search_loan_repayment_by_contract_number',
                method: 'POST',
                data: {
                    'search_text': $("#contract_number").val(),
                },
                success: function(msg){
                    if(msg=="FALSE"){
                        swal('ไม่พบเลขที่สัญญานี้', '', 'warning');
                        $("#contract_number").val("");
                    }
                }
            });
    });

</script>


