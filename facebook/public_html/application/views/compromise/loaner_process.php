<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.modal-header-confirmSave {
				padding:9px 15px;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}
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
			.modal-dialog-data {
				margin:auto;
				margin-top:7%;
			}
			label{
				padding-top:7px;
			}
            .modal-body-data {
                height:220px;
            }
            .table {
                border-collapse: collapse;
                border-radius: 4px;
                overflow: hidden;
                margin-bottom:0;
            }
            #guarantee-tbody > tr {
                background-color:#e0e0e0;
            }
            #guarantee-tbody>tr>td {
                border-top:0;
            }
		</style>

		<style type="text/css">
			.form-group{
				margin-bottom: 5px;
			}
		</style>
		<h1 style="margin-bottom: 0">ประนอมหนี้ผู้กู้</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body ">
                    <input type="hidden" id="balance_cal_left" value="">
                    <form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/compromise/run_compromise_loaner_process'); ?>" class="g24 form form-horizontal no_print" enctype="multipart/form-data" autocomplete="off" id="form_1">
                        <input type="hidden" name="refrain_loan_id" id="refrain_loan_id" value=""/>
                        <input type="hidden" name="data_interest" id="data_interest" value=""/>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">รหัสสมาชิกผู้กู้</label>
                            <div class="g24-col-sm-5">
                                <div class="input-group g24-col-sm-20">
                                    <input id="member_id" class="form-control " type="text" name='member_id' value="<?php echo $member['member_id']?>">
                                    <span class="input-group-btn">
                                        <a data-toggle="modal" data-target="#myModal" id="test" class="" href="#">
                                            <button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                                        </a>
                                    </span>	
                                </div>
                            </div>
                            <label class="g24-col-sm-2 text-right">ชื่อสกุล</label>
                            <div class="g24-col-sm-14">
                                <input id="name" class="form-control " type="text" name='name' value="<?php echo $member['prename_full'].$member["firstname_th"]." ".$member["lastname_th"]?>" readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">เลขที่สัญญา</label>
                            <div class="g24-col-sm-5">
                                <select id="contract_number" name="contract_number" class="form-control g24-col-sm-20">
                                    <option value="">เลือกข้อมูล</option>
                                    <?php foreach($loans as $key => $loan){ ?>
                                        <option value="<?php echo $loan["loan_id"]; ?>"><?php echo $loan["contract_number"] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label class="g24-col-sm-2 text-right">ประเภทเงินกู้</label>
                            <div class="g24-col-sm-5">
                                <input id="loan_type" class="form-control" type="text" value="" readonly>
                            </div>
                            <label class="g24-col-sm-3 text-right">วันที่โอนหุ้นตัดหนี้</label>
                            <div class="g24-col-sm-6">
								<div class="input-with-icon">
									<div class="form-group" style="margin-left: 0px; margin-right: 0px;">
										<input id="resign_date" name="resign_date" class="form-control m-b-1 mydate g24-col-sm-24" type="text" value="<?php echo !empty($resign_info["approve_date"]) ? $this->center_function->mydate2date($resign_info["approve_date"]) : ""; ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">ยอดเงิน</label>
                            <div class="g24-col-sm-5">
                                <input id="loan_amount" class="form-control g24-col-sm-20" type="text" value="" readonly>
                            </div>
                            <label class="g24-col-sm-2 text-right">เงินต้นคงเหลือ</label>
                            <div class="g24-col-sm-5">
                                <input id="loan_amount_balance" class="form-control g24-col-sm-22" type="text" value="" readonly>
                            </div>
                            <label class="g24-col-sm-3 text-right">ดอกเบี้ยค้างชำระ</label>
                            <div class="g24-col-sm-6">
                                <input id="interest_debt" class="form-control g24-col-sm-24" type="text" value="" readonly>
                                <input id="interest_debt_base" type="hidden" value=""/>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">กองทุน</label>
                            <div class="g24-col-sm-5">
                                <input id="fund_total" class="form-control g24-col-sm-20" type="text" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">ผู้ค้ำประกัน</label>
                            <div class="g24-col-sm-8">
                                <table class="table ">
                                    <tbody id="guarantee-tbody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">รูปแบบ</label>
                            <label class="g24-col-sm-1 control-label text-right option-radio">
								<input type="radio" class="radio-type" name="type" value="7">
							</label>
							<label class="g24-col-sm-3 control-label text-left"> ไม่มีคำพิพากษา</label>
                            <label class="g24-col-sm-1 control-label text-right option-radio">
								<input type="radio" class="radio-type" name="type" value="4">
							</label>
							<label class="g24-col-sm-3 control-label text-left"> ก่อนคำพิพากษา</label>
                            <label class="g24-col-sm-1 control-label text-right option-radio">
								<input type="radio" class="radio-type" name="type" value="3">
							</label>
							<label class="g24-col-sm-3 control-label text-left"> หลังคำพิพากษา</label>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">เงินต้น</label>
                            <div class="g24-col-sm-5">
                                <input id="new_loan_amount_balance" name="new_loan_amount_balance" class="form-control g24-col-sm-20" onkeyup="format_the_number_decimal(this);" type="text" value="0">
                            </div>
                            <label class="g24-col-sm-3 text-right">ดอกเบี้ยค้างชำระ</label>
                            <div class="g24-col-sm-5">
                                <input id="new_loan_interest_debt" name="new_loan_interest_debt" class="form-control g24-col-sm-20" onkeyup="format_the_number_decimal(this);" type="text" value="0">
                            </div>
                            <label class="g24-col-sm-3 text-right">ค่าใช้จ่ายอื่นๆ</label>
                            <div class="g24-col-sm-5">
                                <input id="new_loan_other_debt" name="new_loan_other_debt" class="form-control g24-col-sm-20" onkeyup="format_the_number_decimal(this);" type="text" value="0">
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">สัญญาใหม่เลขที่</label>
                            <div class="g24-col-sm-5">
                                <input id="new_contract_number" name="new_contract_number" class="form-control g24-col-sm-20" type="text" value="" readonly>
                            </div>
                            <label class="g24-col-sm-3 text-right">ประเภทสัญญา</label>
                            <div class="g24-col-sm-5">
                                <select id="new_loan_type" name="new_loan_type" class="form-control g24-col-sm-20">
                                    <option value="">เลือกข้อมูล</option>
                                    <?php foreach($loan_types as $key => $loan_type){ ?>
                                        <option data-interest-rate="<?php echo $interest_rates[$loan_type["loan_name_id"]];?>" value="<?php echo $loan_type["loan_name_id"]; ?>"><?php echo $loan_type["loan_name"] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label class="g24-col-sm-3 text-right">อัตราดอกเบี้ย(%)</label>
                            <div class="g24-col-sm-5">
                                <input id="new_interest_rate" name="new_interest_rate" class="form-control g24-col-sm-20" type="text" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 control-label "></label>
                            <div class="g24-col-sm-5">
                                <select name="cal_period_type" id="period_type" class="form-control  g24-col-sm-20">
                                <option value="1"> งวดที่ต้องการผ่อน </option>
                                <option value="2"> เงินที่ต้องการผ่อนต่องวด </option>
                                </select>
                            </div>
                            <label class="g24-col-sm-3 text-right">จำนวน</label>
                            <div class="g24-col-sm-5">
                                <input type="text" id="period" name="period" class="form-control form-loan inline-block  g24-col-sm-20" />
                                <input type="text" id="period_amount_bath" name="period_amount_bath" onkeyup="format_the_number_decimal(this)" class="form-control form-loan inline-block  g24-col-sm-20" />
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24 text-center">
                            <button class="btn btn-primary" id="save-btn" type="button"><span>บันทึก</span></button>
                        </div>
                    </form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('search_member_new_modal'); ?>

<script>
	$(document).ready(function() {
        $("#period_amount_bath").hide();
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
        $("#member_id").keypress(function() {
            var member_id = $('#member_id').first().val();
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                $.post(base_url+"save_money/check_member_id", 
                {
                member_id: member_id
                }
                , function(result) {
                    obj = JSON.parse(result);
                    mem_id = obj.member_id;
                    if(mem_id != undefined){
                        document.location.href = '<?php echo base_url(uri_string())?>?member_id='+mem_id
                    }else{
                        swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning'); 
                    }
                });
            }
        })
        $("#contract_number").change(function() {
            if($(this).val()) {
                $.get(base_url+"compromise/get_loan_detail?loan_id="+$(this).val(), function(result) {
                    data = JSON.parse(result);
                    $("#loan_type").val(data.loan_name)
                    $("#loan_amount").val(data.loan_amount_text)
                    $("#loan_amount_balance").val(data.loan_amount_balance_text)
                    $("#new_loan_amount_balance").val(data.loan_amount_balance_text)
                    $("#interest_debt_base").val(data.debt_interest)
                    $("#balance_cal_left").val(data.loan_amount_balance)
                    $("#refrain_loan_id").val(data.refrain_loan_id)
                    $(".radio-type").prop('checked', false);
                    $("#new_contract_number").val("")
                    $("#guarantee-tbody").html("")
                    for(i=0; i < data.guarantees.length; i++) {
                        //Set checkbox data
                        index = i + 1
                        var tr = $("<tr></tr>")
                        var td0 = $("<td class='text-center'>"+index+"</td>")
                        var td1 = $("<td class='text-center'>"+data.guarantees[i].member_id+"</td>")
                        var td2 = $("<td class='text-left'>"+data.guarantees[i].prename_full+data.guarantees[i].firstname_th+" "+data.guarantees[i].lastname_th+"</td>")
                        tr.append(td0)
                        tr.append(td1)
                        tr.append(td2)
                        $("#guarantee-tbody").append(tr)
                    }
                    $("#resign_date").trigger("change");
                })
            }
        })
        $(".radio-type").change(function() {
            type = "";
            if($(this).val()==4) {
                type = 'BJ'
            } else if($(this).val()==3) {
                type = 'J'
            } else {
                type = 'N'
            }
            new_contract_number = $("#contract_number option:selected").text()+"/"+type
            $("#new_contract_number").val(new_contract_number)
        })
        $(document).on("click", "#save-btn", function() {
            text_alert = "";
            if(parseFloat($("#balance_cal_left").val()) > parseFloat(removeCommas($("#new_loan_amount_balance").val()))) {
                text_alert += 'จำนวนเงินต้นมีน้อยกว่าจำนวนเงินคงเหลือ\n'
                swal('ไม่สามารถบันทึกข้อมูลได้',text_alert,'warning')
            }
            if($("#new_loan_amount_balance").val() == "") {
                text_alert += 'กรุณากรอกข้อมูลเงินต้น\n'
            }
            if($("#contract_number").val() == "") {
                text_alert += 'กรุณาเลือกสัญญาเงินกู้\n'
            }
            if($("#new_loan_type").val() == "") {
                text_alert += 'กรุณาเลือกประเภทสัญญา\n'
            }
            if($("#period_type").val() == 1 && !$("#period").val()) {
                text_alert += " - กรุณากรอกจำนวนงวดที่ต้องการผ่อน\n";
            }
            if($("#period_type").val() == 2 && !$("#period_amount_bath").val()) {
                text_alert += " - กรุณากรอกจำนวนเงินที่ต้องการผ่อนต่องวด\n";
            }
            if(text_alert == "") {
                $("#new_loan_amount_balance").val(removeCommas($("#new_loan_amount_balance").val()))
                $("#new_loan_interest_debt").val(removeCommas($("#new_loan_interest_debt").val()))
                $("#new_loan_other_debt").val(removeCommas($("#new_loan_other_debt").val()))
                $("#period_amount_bath").val(removeCommas($("#period_amount_bath").val()))
                $("#form_1").submit()
            } else {
                swal('ไม่สามารถบันทึกข้อมูลได้',text_alert,'warning')
            }
        })
        $(document).on("change", "#new_loan_type", function() {
            var interest_rate = $('option:selected', this).attr('data-interest-rate');
            $("#new_interest_rate").val(format_number(interest_rate))
        })
        $("#resign_date").change(function(){
            if($(this).val() != "") {
                $.get(base_url+"compromise/cal_member_loans_interest?member_id="+$("#member_id").val()+"&from_date="+$(this).val()+"&loan_id="+$("#contract_number").val(), function(result) {
                    interst_debt_base = !$("#interest_debt_base").val() ? "0" : $("#interest_debt_base").val();
                    interest = $("#refrain_loan_id").val() == "" ? parseFloat(removeCommas(result)) : parseFloat(removeCommas(interst_debt_base));
                    $("#interest_debt").val(format_number(interest));
                    $("#new_loan_interest_debt").val(format_number(interest));
                    $("#data_interest").val(result);
                });
            } else {
                interst_debt_base = !$("#interest_debt_base").val() ? "0" : $("#interest_debt_base").val();
                interest = parseFloat(removeCommas(interst_debt_base));
                $("#interest_debt").val(format_number(interest));
                $("#new_loan_interest_debt").val(format_number(interest));
            }

        })
        $("#period_type").change(function() {
            if($(this).val() == 1) {
                $("#period").show();
                $("#period_amount_bath").hide();
            } else if ($(this).val() == 2) {
                $("#period").hide();
                $("#period_amount_bath").show();
            }
        });
    })

    function format_the_number_decimal(ele){
        var value = $('#'+ele.id).val();
        value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        var num = value.split(".");
        var decimal = '';
        var num_decimal = '';
        if(typeof num[1] !== 'undefined'){
            if(num[1].length > 2){
                num_decimal = num[1].substring(0, 2);
            }else{
                num_decimal =  num[1];
            }
            decimal =  "."+num_decimal;
        }

        if(value!=''){
            if(value == 'NaN'){
                $('#'+ele.id).val(0);
            }else{		
                value = (num[0] == '')?0:parseInt(num[0]);
                value = value.toLocaleString()+decimal;
                $('#'+ele.id).val(value);
            }
        }else{
            $('#'+ele.id).val(0);
        }
    }

    function removeCommas(str) {
        return(str.replace(/,/g,''));
    }
</script>