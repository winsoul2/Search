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
		<h1 style="margin-bottom: 0">ผู้กู้ต้องการชำระต่อ</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body ">
                    <input type="hidden" id="balance_cal_left" value="">
                    <form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/compromise/run_compromise_return_process'); ?>" class="g24 form form-horizontal no_print" enctype="multipart/form-data" autocomplete="off" id="form_1">
                        <input type="hidden" id="compromise_id" name="compromise_id" value="<?php echo $_GET["compromise_id"];?>"/>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">รหัสสมาชิกผู้กู้</label>
                            <div class="g24-col-sm-5">
                                <input id="member_id" class="form-control g24-col-sm-22" type="text" value="<?php echo $loan["member_id"]?>" readonly>
                            </div>
                            <label class="g24-col-sm-2 text-right">ชื่อสกุล</label>
                            <div class="g24-col-sm-14">
                                <input id="name" class="form-control " type="text" name='name' value="<?php echo $member['prename_full'].$member["firstname_th"]." ".$member["lastname_th"]?>" readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">เลขที่สัญญา</label>
                            <div class="g24-col-sm-5">
                                <input id="contract_number" class="form-control g24-col-sm-22" type="text" value="<?php echo $loan["contract_number"]?>" readonly>
                            </div>
                            <label class="g24-col-sm-2 text-right">ประเภทเงินกู้</label>
                            <div class="g24-col-sm-14">
                                <input id="loan_type" class="form-control" type="text" value="<?php echo $loan["loan_name"]?>" readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">ยอดเงิน</label>
                            <div class="g24-col-sm-5">
                                <input id="loan_amount" class="form-control g24-col-sm-22" type="text" value="<?php echo number_format($loan["loan_amount"],2);?>" readonly>
                            </div>
                            <label class="g24-col-sm-2 text-right">เงินต้นคงเหลือ</label>
                            <div class="g24-col-sm-6">
                                <input id="loan_amount_balance" class="form-control g24-col-sm-18" type="text" value="<?php echo number_format($loan["loan_amount_balance"],2);?>" readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">ผู้ค้ำประกัน</label>
                            <div class="g24-col-sm-21">
                                <table class="table ">
                                    <tbody id="guarantee-tbody">
                                    <?php
                                        $index = 0;
                                        $compromise_balance_paid = 0;
                                        $compromise_interest_paid = 0;
                                        $total_loan_amount_balance = 0;
                                        foreach($compromises as $compromise) {
                                            $compromise_balance_paid += $compromise["principal"];
                                            $compromise_interest_paid += $compromise["interest"];
                                            $total_loan_amount_balance += $compromise["loan_amount_balance"];
                                    ?>
                                        <tr>
                                            <td>
                                                <?php echo ++$index;?>
                                            </td>
                                            <td>
                                                <?php echo $compromise["member_id"];?>
                                            </td>
                                            <td>
                                                <?php echo $compromise["prename_full"].$compromise["firstname_th"]." ".$compromise["lastname_th"];?>
                                            </td>
                                            <td>
                                                <?php echo "เลขที่ ".$compromise["contract_number"];?>
                                            </td>
                                            <td>
                                                <?php echo "ชำระเงินต้นไปแล้ว ".number_format($compromise["principal"],2)." บาท";?>
                                            </td>
                                            <td>
                                                <?php echo "ดอกเบี้ย ".number_format($compromise["interest"],2)." บาท";?>
                                            </td>
                                            <td>
                                                <?php echo "รวม ".number_format($compromise["principal"]+$compromise["interest"],2)." บาท";?>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">รวมผู้ค้ำชำระแล้ว</label>
                            <div class="g24-col-sm-5">
                                <input id="compromise_balance_paid" class="form-control g24-col-sm-22" type="text" value="<?php echo number_format($compromise_balance_paid,2);?>" readonly>
                            </div>
                            <label class="g24-col-sm-2 text-right">เงินต้นคงเหลือ</label>
                            <div class="g24-col-sm-6">
                                <input id="compromise_balance_left" class="form-control g24-col-sm-18" type="text" value="<?php echo number_format($total_loan_amount_balance,2);?>" readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 text-right">กองทุน</label>
                            <div class="g24-col-sm-21">
                                <table class="table ">
                                    <tbody id="guarantee-tbody">
                                        <tr>
                                            <td>
                                                ช่วยเหลือ เงินต้น <?php echo number_format($fund,2);?> บาท
                                            </td>
                                            <td>
                                                ดอกเบีี้ย <?php echo number_format($fund_interest,2);?> บาท
                                            </td>
                                            <td>
                                                ชำระเงินต้นไปแล้ว <?php echo number_format($fund-$fund_balance,2);?> บาท
                                            </td>
                                            <td>
                                                ดอกเบี้ย <?php echo number_format($fund_interest-$fund_interest_balance,2);?> บาท
                                            </td>
                                            <td>
                                                ค้างชำระเงินต้น <?php echo number_format($fund_balance,2);?> บาท
                                            </td>
                                            <td>
                                                ดอกเบีี้ย <?php echo number_format($fund_interest_balance,2);?> บาท
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                    <input type="hidden" id="compromise_balance_left_total" value="<?php echo $total_loan_amount_balance;?>"/>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('search_member_new_modal'); ?>

<script>
	$(document).ready(function() {
        $("#period_amount_bath").hide();
        $(document).on("click", "#save-btn", function() {
            text_alert = ""
            if(parseFloat($("#compromise_balance_left_total").val()) > parseFloat(removeCommas($("#new_loan_amount_balance").val()))) {
                text_alert += 'จำนวนเงินต้นมีน้อยกว่าจำนวนเงินคงเหลือ\n'
            }
            if($("#new_loan_amount_balance").val() == "") {
                text_alert += 'กรุณากรอกข้อมูลเงินต้น\n'
            }
            if($("#new_loan_type").val() == "") {
                text_alert += 'กรุณาเลือกประเภทของสัญญา\n'
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
                $("#period_amount_bath").val(removeCommas($("#period_amount_bath").val()))
                $("#new_loan_other_debt").val(removeCommas($("#new_loan_other_debt").val()))
                $("#form_1").submit()
            } else {
                swal('ไม่สามารถบันทึกข้อมูลได้',text_alert,'warning')
            }
        })
        $(document).on("change", "#new_loan_type", function() {
            var interest_rate = $('option:selected', this).attr('data-interest-rate');
            $("#new_interest_rate").val(format_number(interest_rate))
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