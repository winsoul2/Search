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
                height:420px;
            }
            .modal-dialog-cal {
				width:80% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.form-group{
				margin-bottom: 5px;
			}.modal-footer{
                border-top:0;
            }
			.input-with-icon .form-control{
				padding-left: 56px !important;
			}

		</style>
		<h1 style="margin-bottom: 0">ประนอมหนี้ผู้ค้ำ</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body ">
                    <input type="hidden" id="balance_cal_left" value="">
                    <input type="hidden" id="interest_cal_left" value="">
                    <form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/compromise/run_compromise_guarantees_process'); ?>" class="g24 form form-horizontal no_print" enctype="multipart/form-data" autocomplete="off" id="form_1">
                        <input type="hidden" name="pay_type" value="2"/>
                        <input type="hidden" name="refrain_loan_id" id="refrain_loan_id" value=""/>
                        <input type="hidden" name="data_interest" id="data_interest" value=""/>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-2 text-right">รหัสสมาชิกผู้กู้</label>
                            <div class="g24-col-sm-6">
                                <div class="input-group g24-col-sm-18">
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
                                <input id="name" class="form-control" type="text" name='name' value="<?php echo $member['prename_full'].$member["firstname_th"]." ".$member["lastname_th"]?>" readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-2 text-right">เลขที่สัญญา</label>
                            <div class="g24-col-sm-6">
                                <select id="contract_number" name="contract_number" class="form-control g24-col-sm-18">
                                    <option value="">เลือกข้อมูล</option>
                                    <?php foreach($loans as $key => $loan){ ?>
                                        <option value="<?php echo $loan["loan_id"]; ?>"><?php echo $loan["contract_number"] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label class="g24-col-sm-2 text-right">ประเภทเงินกู้</label>
                            <div class="g24-col-sm-5">
                                <input id="loan_type" class="form-control  g24-col-sm-22" type="text" value="" readonly>
                            </div>
                            <label class="g24-col-sm-3 text-right">วันที่โอนหุ้นตัดหนี้</label>
                            <div class="g24-col-sm-6">
								<div class="input-with-icon">
									<div class="form-group" style="margin-left: 0px; margin-right: 0px;">
										<input id="resign_date" name="resign_date" class="form-control m-b-1 mydate g24-col-sm-18" type="text" value="<?php echo !empty($resign_info["approve_date"]) ? $this->center_function->mydate2date($resign_info["approve_date"]) : ""; ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-2 text-right">ยอดเงิน</label>
                            <div class="g24-col-sm-6">
                                <input id="loan_amount" class="form-control g24-col-sm-18" type="text" value="" readonly>
                            </div>
                            <label class="g24-col-sm-2 text-right">เงินต้นคงเหลือ</label>
                            <div class="g24-col-sm-5">
                                <input id="loan_amount_balance" class="form-control g24-col-sm-22" type="text" value="" readonly>
                            </div>
                            <label class="g24-col-sm-3 text-right">ดอกเบี้ยค้างชำระ</label>
                            <div class="g24-col-sm-6">
                                <input id="interest_debt" name="interest_debt" class="form-control g24-col-sm-18" type="text" value="" >
                                <input id="interest_debt_base" type="hidden" value=""/>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24" >
                            <label class="g24-col-sm-2 text-right" style="display: none">กองทุน</label>
                            <div class="g24-col-sm-6" style="display: none">
                                <input id="fund_total" class="form-control g24-col-sm-18" type="text" value="" readonly>
                            </div>
                            <label class="g24-col-sm-2 text-right"> อัตรา </label>
                            <div class="g24-col-sm-5">
                                <select name="fund_unit" id="fund_unit" class="form-control g24-col-sm-22">
                                    <option value="1"> บาท </option>
                                    <option value="2"> % </option>
                                </select>
                            </div>
                            <label class="g24-col-sm-3 text-right fund_support" style="display: none">กองทุนช่วยเหลือ</label>
                            <div class="g24-col-sm-6" style="display: none">
                                <input id="fund_support" name="fund_support" onkeyup="format_the_number_decimal(this);" class="form-control g24-col-sm-18 fund_support debt_divide" type="text" value="0">
                                <input id="fund_support_percent" name="fund_support_percent" onkeyup="format_the_number_decimal(this);" class="form-control g24-col-sm-18 fund_support debt_divide" type="text" value="0">
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24 fund_support_interest_div" style="display: none">
                            <label class="g24-col-sm-18 text-right">ดอกเบี้ยกองทุน</label>
                            <div class="g24-col-sm-6">
                                <input id="fund_support_interest" name="fund_support_interest" onkeyup="format_the_number_decimal(this);" class="form-control g24-col-sm-18 fund_support debt_divide" type="text" value="0">
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-2 text-right">รูปแบบ</label>
                            <label class="g24-col-sm-1 control-label text-right option-radio">
								<input type="radio" class="radio-type" name="type" value="1" checked>
							</label>
							<label class="g24-col-sm-3 control-label text-left"> ไม่มีคำพิพากษา</label>
                            <label class="g24-col-sm-1 control-label text-right option-radio">
								<input type="radio" class="radio-type" name="type" value="5">
							</label>
							<label class="g24-col-sm-3 control-label text-left"> ก่อนคำพิพากษา</label>
                            <label class="g24-col-sm-1 control-label text-right option-radio">
								<input type="radio" class="radio-type" name="type" value="6">
							</label>
							<label class="g24-col-sm-3 control-label text-left"> หลังคำพิพากษา</label>
                            <label class="g24-col-sm-4 control-label other-payment-label">ค่าใช้จ่ายอื่นๆ</label>
                            <div class="g24-col-sm-5 other-payment-label">
                                <input id="new_loan_other_debt" name="new_loan_other_debt" class="form-control g24-col-sm-22" onkeyup="format_the_number_decimal(this);" type="text" value="0">
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-24 text-left "><h3>คำนวน</h3></label>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-2 control-label">สัญญา</label>
                            <div class="g24-col-sm-5">
                                <select id="contract_type" name="loan_type" class="form-control g24-col-sm-22">
                                    <option value="">เลือกข้อมูล</option>
                                    <?php foreach($loan_types as $key => $loan_type){ ?>
                                        <option data-interest-rate="<?php echo $interest_rates[$loan_type["loan_name_id"]];?>" value="<?php echo $loan_type["loan_name_id"]; ?>"><?php echo $loan_type["loan_name"] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label class="g24-col-sm-3 text-right">อัตราดอกเบี้ย(%)</label>
                            <div class="g24-col-sm-5">
                                <input type="text" class="form-control modal-input g24-col-sm-22" id="interest_rate" name="interest_rate" value="" readonly/>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-2 control-label "></label>
                            <div class="g24-col-sm-5">
                                <select name="cal_period_type" id="period_type" class="form-control  g24-col-sm-22">
                                <option value="1"> งวดที่ต้องการผ่อน </option>
                                <option value="2"> เงินที่ต้องการผ่อนต่องวด </option>
                                </select>
                            </div>
                            <label class="g24-col-sm-3 text-right">จำนวน</label>
                            <div class="g24-col-sm-5">
                                <input type="text" id="period" class="form-control form-loan inline-block  g24-col-sm-22" />
                                <input type="text" id="period_amount_bath" onkeyup="format_the_number_decimal(this)" class="form-control form-loan inline-block  g24-col-sm-22" />
                            </div>
							<label class="g24-col-sm-3 text-right">วันที่เริ่มต้นสัญญาใหม่</label>
							<div class="g24-col-sm-6">
								<div class="input-with-icon">
									<div class="form-group" style="margin-left: 0px; margin-right: 0px;">
										<input id="date_approve" name="date_approve" class="form-control m-b-1 mydate g24-col-sm-18" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
                        </div>
                        <div class="form-group g24-col-sm-24 guarantee_person">
                            <label class="g24-col-sm-24 text-left "><h3>ผู้ค้ำประกัน</h3></label>
                        </div>
                        <div class="form-group g24-col-sm-24 guarantee_person">
                            <table class="table table-striped table-bordered">
                                <thead class="bg-primary ">
                                    <tr>
                                        <th class="font-normal text-center">ลำดับ</th>
                                        <th class="font-normal text-center">รหัสสมาชิก</th>
                                        <th class="font-normal text-center">ชื่อผู้ค้ำประกัน</th>
                                        <th class="font-normal text-center">เลือกผู้รับภาระหนี้</th>
                                        <th class="font-normal text-center">จำนวนเงินต้น</th>
                                        <th class="font-normal text-center">ดอกเบี้ยคงค้าง</th>
                                        <th class="font-normal text-center">ผ่อนชำระต่องวด</th>
                                        <th class="font-normal text-center">งวดผ่อนชำระทั้งหมด</th>
                                        <th class="font-normal text-center other-payment-label">ค่าใช้จ่ายอื่นๆ</th>
                                    </tr>
                                </thead>
                                <tbody id="guarantee-tbody">
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group g24-col-sm-24 guarantee_person">
                            <div class="g24-col-sm-12"></div>
                            <label class="g24-col-sm-4 text-right">รวม</label>
                            <label class="g24-col-sm-2 text-right" id="text_total_divide">0.00</label>
                        </div>
                        <div class="form-group g24-col-sm-24 guarantee_person" style="display: none">
                            <div class="g24-col-sm-12"></div>
                            <label class="g24-col-sm-4 text-right">กองทุนช่วย</label>
                            <label class="g24-col-sm-2 text-right" id="text_fund_support">0.00</label>
                        </div>
                        <div class="form-group g24-col-sm-24 guarantee_person" style="display: none">
                            <div class="g24-col-sm-12"></div>
                            <label class="g24-col-sm-4 text-right">ดอกเบี้ยกองทุน</label>
                            <label class="g24-col-sm-2 text-right" id="text_fund_interest_support">0.00</label>
                        </div>
                        <div class="form-group g24-col-sm-24 guarantee_person">
                            <div class="g24-col-sm-12"></div>
                            <label class="g24-col-sm-4 text-right">เงินต้นคงเหลือ</label>
                            <label class="g24-col-sm-2 text-right" id="text_debt_balance">0.00</label>
                        </div>
                        <div class="form-group g24-col-sm-24 guarantee_person">
                            <div class="g24-col-sm-12"></div>
                            <label class="g24-col-sm-4 text-right">ดอกเบี้ยคงค้างคงเหลือ</label>
                            <label class="g24-col-sm-2 text-right" id="text_debt_interest">0.00</label>
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
<input type="hidden" id="total_debt_balance" value=""/>
<?php $this->load->view('search_member_new_modal'); ?>

<script>
	$(document).ready(function() {
        $(".guarantee_person").hide()
        $("#period_amount_bath").hide()
        $(".other-payment-label").hide();
        $(".other-payment-th").hide();
        $("#save-btn").hide();
        $("#fund_support_percent").hide()
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
            if($("#contract_number").val()) {
                $.blockUI({
                    message: 'กรุณารอสักครู่...',
                    css: {
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .5,
                        color: '#fff'
                    },
                    baseZ: 2000,
                    bindEvents: false
                });
                $.get(base_url+"compromise/get_loan_detail?loan_id="+$("#contract_number").val(), function(result) {
                    data = JSON.parse(result);
                    console.log(data)
                    $("#loan_type").val(data.loan_name)
                    $("#loan_amount").val(data.loan_amount_text)
                    $("#loan_amount_balance").val(data.loan_amount_balance_text)
                    $("#interest_debt_base").val(data.debt_interest)
                    $("#refrain_loan_id").val(data.refrain_loan_id)

                    $("#fund_total").val(data.total_fund_text)
                    $("#balance_cal_left").val(data.loan_amount_balance)
                    $("#total_debt_balance").val(data.loan_amount_balance)
                    $("#interest_cal_left").val(data.debt_interest)
                    $("#text_debt_balance").html(data.loan_amount_balance_text)
                    $("#text_debt_interest").html(data.debt_interest_text)

                    if($("#resign_date").val()) {
                        $.get(base_url+"/compromise/cal_member_loans_interest?member_id="+$("#member_id").val()+"&from_date="+$("#resign_date").val()+"&loan_id="+$("#contract_number").val(), function(result) {
                            $("#interest_debt").val(format_number(result));
                        });
                    } else {
                        $("#interest_debt").val(data.debt_interest_text);
                    }
                    $("#guarantee-tbody").html("")
                    for(i=0; i < data.guarantees.length; i++) {
                        //Set checkbox data
                        checkbox_data = "data-member-id='"+data.guarantees[i].member_id+"' data-member-name='"+data.guarantees[i].prename_full+data.guarantees[i].firstname_th+" "+data.guarantees[i].lastname_th+"'"
                        index = i + 1
                        var tr = $("<tr></tr>")
                        var td0 = $("<td class='text-center'>"+index+"</td>")
                        var td1 = $("<td class='text-center'>"+data.guarantees[i].member_id+"</td>")
                        var td2 = $("<td class='text-left'>"+data.guarantees[i].prename_full+data.guarantees[i].firstname_th+" "+data.guarantees[i].lastname_th+"</td>")
                        var td3 = $("<td class='text-center'><input type='checkbox' class='gua_persons' id='checkbox_"+data.guarantees[i].member_id+"' name='gua_persons[]' value='"+data.guarantees[i].member_id+"' "+checkbox_data+"></td>")
                        var td4 = $("<td class='text-center' id='text_debt_divide_"+data.guarantees[i].member_id+"'></td>")
                        var td5 = $("<td class='text-center' id='text_interest_debt_divide_"+data.guarantees[i].member_id+"'></td>")
                        var td6 = $("<td class='text-center' id='text_period_"+data.guarantees[i].member_id+"'></td>")
                        var td7 = $("<td class='text-center' id='text_period_count_"+data.guarantees[i].member_id+"'></td>")
                        var td8 = $("<td class='text-center other-payment-label' id='text_other_debt_divide_"+data.guarantees[i].member_id+"'></td>")
                        var hidden0 = $("<input type='hidden' id='debt_divide_"+data.guarantees[i].member_id+"' name='debt_divide[]' class='debt_divide' value=''>")
                        var hidden1 = $("<input type='hidden' id='interest_debt_divide_"+data.guarantees[i].member_id+"' name='interest_debt_divide[]' class='interest_debt_divide' value=''>")
                        var hidden2 = $("<input type='hidden' data-member-id='"+data.guarantees[i].member_id+"' id='period_"+data.guarantees[i].member_id+"' name='period[]' class='contract_number' value=''>")
                        var hidden3 = $("<input type='hidden' id='period_count_"+data.guarantees[i].member_id+"' name='period_count[]' class='loan_type' value=''>")
                        var hidden4 = $("<div id='div_gua_"+data.guarantees[i].member_id+"' style='display:none'></div>")
                        var hidden5 = $("<input type='hidden' id='other_debt_divide_"+data.guarantees[i].member_id+"' name='other_debt[]' class='other-payment-label' value=''>")
                        var hidden6 = $("<input type='hidden' id='fund_support_divide_"+data.guarantees[i].member_id+"' name='fund_support_divide[]' value=''>")
                        var hidden7 = $("<input type='hidden' id='fund_support_interest_divide_"+data.guarantees[i].member_id+"' name='fund_support_interest_divide[]' value=''>")
                        tr.append(td0)
                        tr.append(td1)
                        tr.append(td2)
                        tr.append(td3)
                        tr.append(td4)
                        tr.append(td5)
                        tr.append(td6)
                        tr.append(td7)
                        tr.append(td8)
                        tr.append(hidden0)
                        tr.append(hidden1)
                        tr.append(hidden2)
                        tr.append(hidden3)
                        tr.append(hidden4)
                        tr.append(hidden5)
                        tr.append(hidden6)
                        tr.append(hidden7)
                        $("#guarantee-tbody").append(tr)
                    }
                    $("#text_fund_support").html("0.00")
                    $("#fund_support").val("0")
                    $("#fund_support_interest").html("0")
                    $("#text_fund_support_interest").html("0.00")

                    if(data.guarantees.length > 0) {
                        $(".guarantee_person").show()
                    } else {
                        $(".guarantee_person").hide()
                    }
                    type = $("input[name='type']:checked").val();
                    if(type == 1) {
                        $(".other-payment-label").hide();
                    } else {
                        $(".other-payment-label").show();
                    }
                    $("#resign_date").trigger("change");
                    $("#save-btn").show();
                    $.unblockUI();
                })
            }
        });

        $(document).on('change', '.gua_persons', function() {
            if(this.checked) {
                warnning_text = '';
                if(!$("#contract_number").val()) {
                    warnning_text += " - กรุณาเลือกเลขที่สัญญา\n";
                }
                if(!$("#contract_type").val()) {
                    warnning_text += " - กรุณาเลือกประเภทสัญญา\n";
                }
                if($("#period_type").val() == 1 && !$("#period").val()) {
                    warnning_text += " - กรุณากรอกจำนวนงวดที่ต้องการผ่อน\n";
                }
                if($("#period_type").val() == 2 && !$("#period_amount_bath").val()) {
                    warnning_text += " - กรุณากรอกจำนวนเงินที่ต้องการผ่อนต่องวด\n";
                }
                if(warnning_text != '') {
                    swal('ไม่สามารถคำนวนได้', warnning_text, 'warning');
                    $(this).prop('checked', false);
                } else if($("#contract_number").val()) {
                    calculate_loans();
                }
            } else {
                clear_gua_person($(this).val());
                calculate_loans();
            }
        })
        $(document).on('click', '#modal-close', function() {
            $('#checkbox_'+$("#member_id_modal").val()).prop('checked', false)
            $(".modal-input").val(0)
        })
        previous = 0;
        $("#contract_type").on('focus', function() {
            previous = this.value;
        });
        $(document).on("change", "#contract_type", function() {
            gua_length = $('input[name="gua_persons[]"]:checked').length
            var interest_rate = $('option:selected', this).attr('data-interest-rate');
            if(!interest_rate && gua_length > 0) {
                swal('ไม่สามารถคำนวนได้', ' - กรุณาเลือกประเภทสัญญา', 'warning');
                $('input[name="gua_persons[]"]:checked').each(function(index) {
                    clear_gua_person($(this).val());
                });
            } else {
                $("#interest_rate").val(format_number(interest_rate))
                calculate_loans();
            }
        })
        $("#period").change(function(){
            gua_length = $('input[name="gua_persons[]"]:checked').length
            if(!$("#period").val() && gua_length > 0) {
                swal('ไม่สามารถคำนวนได้', " - กรุณากรอกจำนวนงวดที่ต้องการผ่อน", 'warning');
                $('input[name="gua_persons[]"]:checked').each(function(index) {
                    clear_gua_person($(this).val());
                });
            }
        });
        $("#period_amount_bath").change(function(){
            gua_length = $('input[name="gua_persons[]"]:checked').length
            if(!$("#period_amount_bath").val() && gua_length > 0) {
                swal('ไม่สามารถคำนวนได้', " - กรุณากรอกจำนวนเงินที่ต้องการผ่อนต่องวด", 'warning');
                $('input[name="gua_persons[]"]:checked').each(function(index) {
                    clear_gua_person($(this).val());
                });
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
        $("#resign_date").change(function(){
            if($("#member_id").val() && $(this).val() != "") {
                $.get(base_url+"compromise/cal_member_loans_interest?member_id="+$("#member_id").val()+"&from_date="+$(this).val()+"&loan_id="+$("#contract_number").val(), function(result) {
                    interst_debt_base = !$("#interest_debt_base").val() ? "0" : $("#interest_debt_base").val();
                    interest = $("#refrain_loan_id").val() == "" ? parseFloat(removeCommas(result)) : parseFloat(removeCommas(interst_debt_base));
                    $("#interest_debt").val(format_number(interest));
                    balance = !$("#fund_support_percent").val() ? $("#interest_debt").val() : parseFloat(removeCommas($("#interest_debt").val())) - parseFloat(removeCommas($("#fund_support_percent").val()))
                    $("#text_debt_interest").html(format_number(balance))
                    $("#data_interest").val(result);
                    cal_fund_support();
                    calculate_loans();
                });
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
            gua_length = $('input[name="gua_persons[]"]:checked').length
            if(gua_length > 0) {
                warnning_text = "";
                if($("#period_type").val() == 1 && !$("#period").val()) {
                    warnning_text += " - กรุณากรอกจำนวนงวดที่ต้องการผ่อน\n";
                }
                if($("#period_type").val() == 2 && !$("#period_amount_bath").val()) {
                    warnning_text += " - กรุณากรอกจำนวนเงินที่ต้องการผ่อนต่องวด\n";
                }
                if(warnning_text != "") {
                    swal('ไม่สามารถคำนวนได้', warnning_text, 'warning');
                    $('input[name="gua_persons[]"]:checked').each(function(index) {
                        clear_gua_person($(this).val());
                    });
                } else {
                    calculate_loans();
                }
            }
        });
        $("#fund_unit").change(function(){
            if($(this).val() == 1) {
                $("#fund_support").show();
                $(".fund_support_interest_div").show();
                $("#fund_support_percent").hide();
                $("#fund_support_interest").val('0');
                $("#fund_support").val('0');
                $("#fund_support_percent").val('0');
            } else {
                $(".fund_support_interest_div").hide();
                $("#fund_support_interest").val('0');
                $("#fund_support").val('0');
                $("#fund_support_percent").val('0');
                $("#fund_support").hide();
                $("#fund_support_percent").show();
            }
            cal_fund_support();
            calculate_loans();
        });
        $(".option-radio").click(function(){
            type = $("input[name='type']:checked").val();
            if(type == 1) {
                $(".other-payment-label").hide();
            } else {
                $(".other-payment-label").show();
            }
        });
        $('.fund_support').change(function() {
            cal_fund_support();
            calculate_loans();
        });
        $("#new_loan_other_debt").change(function() {
            calculate_loans();
        });
        $(document).on("click", "#save-btn", function() {
            text_alert = ""

            sum_divide = 0;
            $('.debt_divide').each(function(){
                sum_divide += $(this).val() ? parseFloat(removeCommas($(this).val())) : 0;
            })
            // if(parseFloat($("#balance_cal_left").val()) > sum_divide) {
            //     text_alert += 'จำนวนเงินรับชำระทั้งหมดมีน้อยกว่าจำนวนเงินคงเหลือ\n'
            // }
            if($("#contract_number").val() == "") {
                text_alert += 'กรุณาเลือกสัญญาเงินกู้\n'
            }
            if($('input[name="gua_persons[]"]:checked').length <= 0) {
                text_alert += 'กรุณาเลือกผู้รับภาระหนี้อย่างน้อย 1 คน\n'
            }
            if(text_alert == "") {
                $("#fund_support").val(removeCommas($("#fund_support").val()))
                $("#fund_support_interest").val(removeCommas($("#fund_support_interest").val()));
                $("#interest_debt").val(removeCommas($("#interest_debt").val()));
                $("#new_loan_other_debt").val(removeCommas($("#new_loan_other_debt").val()));
                $("#period_amount_bath").val(removeCommas($("#period_amount_bath").val()))
                $("#form_1").submit()
            } else {
                swal('ไม่สามารถบันทึกข้อมูลได้',text_alert,'warning')
            }
        })
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

    function calculate_loans() {
        total_debt_balance = parseFloat(removeCommas($("#total_debt_balance").val())) - parseFloat(removeCommas($("#fund_support").val()));
        gua_length = $('input[name="gua_persons[]"]:checked').length
        debt_devide = Math.floor(total_debt_balance/gua_length);
        interest_debt_balance = parseFloat(removeCommas($("#interest_debt").val())) - parseFloat(removeCommas($("#fund_support_interest").val()));
        interest_debt_devide = interest_debt_balance/gua_length;
        other_debt_balance = parseFloat(removeCommas($("#new_loan_other_debt").val()));
        other_debt_devide = other_debt_balance/gua_length;
        fund_support_balance = parseFloat(removeCommas($("#fund_support").val()));
        fund_support_devide = fund_support_balance/gua_length;
        fund_interest_balance = parseFloat(removeCommas($("#fund_support_interest").val()));
        fund_interest_devide = fund_interest_balance/gua_length;

        //Get diff of old loan total and new loans total.
        debt_format = debt_devide.toString().indexOf('.') > 0 ? debt_devide.toString().substring(0, debt_devide.toString().indexOf('.')+3) : Math.round(debt_devide, 0);
        diff_total_debt = ((total_debt_balance - (debt_devide * gua_length)));

        //console.log('diff_total_debt :', diff_total_debt);

        // var d = new Date();
        // var day = d.getDate();
		// var month = d.getMonth() + 1;
		// var year = d.getFullYear() + 543;

		var resignDate = $('#resign_date').val().split('/');
		var aDays = resignDate.map((x) => +x );
		var day = aDays[0];
		var month = aDays[1];
		var year = aDays[2];

		//console.log("I'm Here......");

        $('input[name="gua_persons[]"]:checked').each(function(index) {
            id = $(this).attr("data-member-id");
            if(index < diff_total_debt) {
                debt = Math.ceil((debt_devide) * 1 ) / 1 + 1;
                console.log('debt', debt);
                interest = Math.ceil(interest_debt_devide * 1) / 1;
                other_debt = Math.ceil(other_debt_devide * 1) / 1;
                fund_support = Math.ceil(fund_support_devide * 1) / 1;
                fund_interest = Math.ceil(fund_interest_devide * 1) / 1;
            } else {
                debt = debt_devide.toString().indexOf('.') > 0 ? debt_devide.toString().substring(0, debt_devide.toString().indexOf('.')+3) : debt_devide;
                interest = interest_debt_devide.toString().indexOf('.') > 0 ? interest_debt_devide.toString().substring(0, interest_debt_devide.toString().indexOf('.')+3) : interest_debt_devide;
                other_debt = other_debt_devide.toString().indexOf('.') > 0 ? other_debt_devide.toString().substring(0, other_debt_devide.toString().indexOf('.')+3) : other_debt_devide;
                fund_support = fund_support_devide.toString().indexOf('.') > 0 ? fund_support_devide.toString().substring(0, fund_support_devide.toString().indexOf('.')+3) : fund_support_devide;
                fund_interest = fund_interest_devide.toString().indexOf('.') > 0 ? fund_interest_devide.toString().substring(0, fund_interest_devide.toString().indexOf('.')+3) : fund_interest_devide;
            }

            $("#text_debt_divide_"+id).html(format_number(debt));
            $("#debt_divide_"+id).val(debt);
            $("#text_interest_debt_divide_"+id).html(format_number(interest));
            $("#interest_debt_divide_"+id).val(interest);
            $("#text_other_debt_divide_"+id).html(format_number(other_debt));
            $("#other_debt_divide_"+id).val(other_debt);
            $("#fund_support_divide_"+id).val(fund_support);
            $("#fund_support_interest_divide_"+id).val(fund_interest);

            $.ajax({
				type: "POST"
				, url: base_url+"/compromise/compromise_cal_period"
				, data: {
						"loan" : debt
						, "pay_type" : 2
						, "day" : day
						, "month" : month
						, "year" : year
						, "period_type" : $("#period_type").val()
						, "period" : $("#period").val().replace(/,/g, "")
						, "interest" : $("#interest_rate").val()
                        , "period_amount_bath" : $("#period_amount_bath").val().replace(/,/g, "")
                        , "_time" : Math.random()
				}
				, async: false
				, success: function(result) {
                    data = JSON.parse(result);
                    $("#div_gua_"+id).html('');
                    div = $("#div_gua_"+id);
                    for(i = 0; i < data.periods.length; i++) {
                        count = i + 1;
                        if(i == 0) {
                            $("#text_period_"+id).html(format_number(data.periods[i].total_paid_per_month));
                            $("#period_"+id).val(data.periods[i].total_paid_per_month);

                            var input_10 = $(`<input type="hidden" name="data[`+id+`][date_start_period]" value="`+data.periods[i].date_period+`">`);
                            var input_11 = $(`<input type="hidden" name="data[`+id+`][first_interest]" value="`+data.periods[i].interest+`">`);
                            div.append(input_10);
                            div.append(input_11);
                        }
                        // input for coop_loan_period
                        var input_1 = $(`<input type="hidden" name="data[coop_loan_period][`+id+`][`+i+`][date_period]" value="`+data.periods[i].date_period+`">`);
                        var input_2 = $(`<input type="hidden" name="data[coop_loan_period][`+id+`][`+i+`][date_count]" value="`+data.periods[i].date_count+`">`);
                        var input_3 = $(`<input type="hidden" name="data[coop_loan_period][`+id+`][`+i+`][interest]" value="`+data.periods[i].interest+`">`);
                        var input_4 = $(`<input type="hidden" name="data[coop_loan_period][`+id+`][`+i+`][principal_payment]" value="`+data.periods[i].principal_payment+`">`);
                        var input_5 = $(`<input type="hidden" name="data[coop_loan_period][`+id+`][`+i+`][total_paid_per_month]" value="`+data.periods[i].total_paid_per_month+`">`);
                        var input_6 = $(`<input type="hidden" name="data[coop_loan_period][`+id+`][`+i+`][period_count]" value="`+count+`">`);
                        var input_7 = $(`<input type="hidden" name="data[coop_loan_period][`+id+`][`+i+`][outstanding_balance]" value="`+data.periods[i].outstanding_balance+`">`);
                        div.append(input_1);
                        div.append(input_2);
                        div.append(input_3);
                        div.append(input_4);
                        div.append(input_5);
                        div.append(input_6);
                        div.append(input_7);
                    }
                    $("#text_period_count_"+id).html(count);
                    $("#period_count_"+id).val(count);
                    //input for coop_loan
                    var input_8 = $(`<input type="hidden" name="data[`+id+`][loan_interest_amount]" value="`+data.total_loan_int+`">`);
                    var input_9 = $(`<input type="hidden" name="data[`+id+`][loan_amount_total]" value="`+data.total_loan_pay+`">`);
                    div.append(input_8);
                    div.append(input_9);
				}
			});
        });
    }
    function clear_gua_person(id) {
        $("#text_debt_divide_"+id).html('');
        $("#debt_divide_"+id).val('');
        $("#text_interest_debt_divide_"+id).html('');
        $("#interest_debt_divide_"+id).val('');
        $("#text_other_debt_divide_"+id).html('');
        $("#other_debt_divide_"+id).val('');
        $("#text_period_"+id).html('');
        $("#period_"+id).val('');
        $("#div_gua_"+id).html('');
        $("#text_period_count_"+id).html('');
        $("#period_count_"+id).val('');
    }
    function cal_fund_support() {
        if($("#fund_unit").val() == 1) {
            balance = parseFloat($("#balance_cal_left").val()) - parseFloat(removeCommas($("#fund_support").val()));
            if(balance < 0) {
                balance = 0
            }
            $("#text_debt_balance").html(format_number(balance));
            $("#text_fund_support").html($("#fund_support").val());
            interest =  parseFloat(removeCommas($("#interest_debt").val())) - parseFloat(removeCommas($("#fund_support_interest").val()));
            $("#text_debt_interest").html(format_number(interest));
            $("#text_fund_interest_support").html($("#fund_support_interest").val());
        } else {
            percent = parseFloat(removeCommas($("#fund_support_percent").val()));
            balance_cal_left = parseFloat($("#balance_cal_left").val());
            fund_support = ((balance_cal_left / 100) * percent);
            balance = balance_cal_left - fund_support;
            $("#text_debt_balance").html(format_number(balance));
            $("#text_fund_support").html(format_number(fund_support));
            $("#fund_support").val(fund_support);

            interest_debt = parseFloat(removeCommas($("#interest_debt").val()));
            if(interest_debt > 0) {
                interest_support = ((interest_debt * percent) / 100);
                interest = interest_debt - interest_support;
                $("#text_debt_interest").html(format_number(interest));
                $("#text_fund_interest_support").html(format_number(interest_support));
                $("#fund_support_interest").val(interest_support);
            } else {
                $("#text_debt_interest").html(format_number(0));
                $("#text_fund_interest_support").html(format_number(0));
                $("#fund_support_interest").val(0);
            }
        }
    }
	$(document).on('change', '#interest_debt', function(){
		interst_debt_base = !$("#interest_debt_base").val() ? "0" : $("#interest_debt_base").val();
		interest = $("#refrain_loan_id").val() == "" ? parseFloat(removeCommas($(this).val())) : parseFloat(removeCommas(interst_debt_base));
		$("#interest_debt").val(format_number(interest));
		balance = !$("#fund_support_percent").val() ? $("#interest_debt").val() : parseFloat(removeCommas($("#interest_debt").val())) - parseFloat(removeCommas($("#fund_support_percent").val()))
		$("#text_debt_interest").html(format_number(balance));
		cal_fund_support();
		calculate_loans();
	});
</script>
