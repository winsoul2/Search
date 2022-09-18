<style type="text/css">

    .title_header {
        font-family: upbean;
        font-size: 24px !important;
        margin-bottom: 8px;
    }

    .padding-line {
        padding-bottom: 8px;
    }

	.btn.btn-sm{
		width: 95px !important;
	}
	.btn.btn-free{
		width: auto;
	}

	.modal-header.modal-bg-primary{
		background-color: #ea7032;
		color: #FFFFFF;
		border-radius: 8px 8px 0 0;
	}
</style>
<div class="layout-content">
    <div class="layout-content-body">
        <h1 class="title_top">อนุมัติการโอนเงินกู้</h1>
        <p style="font-family: upbean; font-size: 20px; margin-bottom:5px;"><?php $this->load->view('breadcrumb'); ?></p>
        <div class="row gutter-xs">
            <div class="panel panel-body" style="padding-top:0px !important;">
                <p class="g24-col-lg-24 title_header">รายละเอียดการกู้</p>
                <div class="g24-col-sm-24 padding-line">
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">รหัสสมาชิก</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="member_id" type="text"
                                   value="<?php echo $member['member_id'] ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">ชื่อ-สกุล</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="full_name_th" type="text"
                                   value="<?php echo $member['full_name_th'] ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">วงเงินที่อนุมัติ</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="loan_amount_balance" type="text"
                                   value="<?php echo number_format($contract['loan_amount'], 2); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="g24-col-sm-24 padding-line">
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">เลขที่สัญญา</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="contract_number" type="text"
                                   value="<?php echo $contract['contract_number']; ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">จำนวนงวดที่ขอกู้</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="loan_amount" type="text"
                                   value="<?php echo number_format($contract['loan_amount'], 2); ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">ชำระต่องวด</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="payment_per_period" type="text"
                                   value="<?php echo number_format($contract['money_period_1'], 2); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="g24-col-sm-24 padding-line">
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">ประเภทการส่งหัก</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="pay_type" type="text"
                                   value="<?php echo $contract['pay_type'] == 1 ? "คงต้น" : "คงยอด"; ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">วันที่เริ่มชำระ</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="date_start_period" type="text"
                                   value="<?php echo $this->center_function->mydate2date($contract['date_start_period']); ?>"
                                   readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">วันที่อนุมัติ</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="approve_date" type="text"
                                   value="<?php echo $this->center_function->mydate2date($contract['approve_date']); ?>"
                                   readonly>
                        </div>
                    </div>
                </div>
                <div class="g24-col-sm-24 padding-line">
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">จำนวนเงินรับจริง</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="real_pay_amount" type="text"
                                   value="<?php echo number_format(array_sum(array_column($installment, "amount")), 2); ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">ยอดเงินคงเหลือ</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="loan_receipt_balance" type="text"
                                   value="<?php echo number_format($contract['loan_amount_balance'] , 2); ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">วงเงินกู้คงเหลือ</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="loan_amount_balance_approve" type="text"
                                   value="<?php echo number_format($contract['loan_amount']- array_sum(array_column($installment, "amount")), 2) ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="g24-col-sm-24 padding-line">
                    <div class="g24-col-lg-16">
                        <label class="g24-col-sm-5 control-label ">เหตุผลการกู้</label>
                        <div class="g24-col-sm-19">
                            <input class="form-control" id="reason_loan" type="text"
                                   value="<?php echo $contract['loan_reason'] ?>" readonly>
                        </div>
                    </div>
                </div>
                <p class="g24-col-lg-24 title_header">จัดการงวดจ่ายเงินกู้</p>
                <div class="g24-col-sm-24 padding-line">
                    <div class="g24-col-lg-18">
                        <label class="g24-col-sm-4 control-label">จำนวนงวดสูงสุดที่แบ่งจ่าย</label>
                        <div class="g24-col-sm-4">
                            <div class="input-group" id="group-installing">
                                <input id="installment" type="text" class="form-control m-b-1 text-right" <?php echo  $contract['installment_amount'] == "" ? "" : 'readonly="readonly"';?> value="<?php echo $contract['installment_amount']; ?>">
                                <span class="input-group-btn">
                                <?php if($contract['installment_amount'] == ""){?>
                                    <button id="" type="button" class="btn btn-info btn-search" onclick="btnSave()"><span class="icon icon-save"></span></button>
                                <?php }else{ ?>
                                    <button id="" type="button" class="btn btn-info btn-search" onclick="btnEdit()"><span class="icon icon-pencil"></span></button>
                                <?php } ?>
                                </span>
                            </div>
                        </div>
                        <label class="g24-col-sm-10 control-label text-left" for="installment">
                            <span>งวด</span>
                            <span class="" style="color: red">(**กรุณากรอกจำนวนงวดและบันทึกก่อนทำรายการอนุมัติเงินกู้)</span>
                        </label>
                    </div>
                </div>
                <div class="g24-col-sm-24">
                    <table id="installing" class="table table-bordered table-striped table-center">
                        <thead class="bg-primary">
                        <tr>
                            <th style="width: 5%;">ลำดับ</th>
                            <th style="width: 15%;">วันที่</th>
                            <th style="width: 10%;">วงเงินกู้คงเหลือ</th>
                            <th style="width: 10%;">ยอดจ่าย</th>
							<th style="width: 15%;">ยอดหัก</th>
							<th style="width: 10%;">ยอดโอนจริง</th>
                            <th style="width: 10%;">สถานะ</th>
                            <th style="width: 10%;">ผู้อนุมัติ</th>
                            <th style="width: 15%;">จัดการ</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; $tmp_bal = null; if(sizeof(@$installment)){ ?>
                            <?php foreach ($installment as $key => $value ){ $tmp_bal = $value['balance'] ?>
                                <tr>
                                    <td><span class="line-number line-<?php echo $i; ?>"><?php echo $i; ?></span></td>
                                    <td>
                                        <div class="input-with-icon g24-col-sm-24">
                                            <div class="form-group">
                                                <input id="approve_date_<?php echo $i; ?>" name="data[<?php echo $i; ?>]['approve_date']"
                                                       class="form-control m-b-1 datepicker required" style="padding-left: 50px;"
                                                       type="text" value="<?php echo $this->center_function->mydate2date($value['transaction_datetime']); ?>"
                                                       data-date-language="th-th" required title=""
													   <?php if($value['approve_status'] == '1'){?> readonly="readonly" disabled="disabled" <?php } ?>>
                                                <span class="icon icon-calendar input-icon m-f-1"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="loan_amount_balance"><? echo number_format($value['balance'], 2); ?></span></td>
                                    <td>
                                        <input type="text" class="form-control m-b-1 text-right calc required amount_receiver"  <?php if($value['approve_status'] == '1'){?> readonly="readonly" <?php } ?>
                                               name="data[]['loan_amount_receiver']" onblur="calc(this)"
                                               value="<?php echo number_format($value['amount'], 2); ?>">
                                    </td>
									<td>
										<div class="g24-col-sm-18">
											<input type="text" class="form-control m-b-1 text-right calc required deduct-all" readonly="readonly"
												   name="data[]['deduct']" style="font-weight: 700; color: green" value="<?php echo number_format($value['total_deduct'], 2); ?>">
										</div>
										<div class="g24-col-sm-6">
											<button class="btn btn-free btn-primary dialog_transfer_detail" type="button" onclick="btnOnDetailDeductListener(this)"><i class="fa fa-calculator"></i></button>
										</div>
									</td>
									<td>
										<input type="text" class="form-control m-b-1 text-right calc required total" readonly="readonly"
											   name="data[]['loan_amount_real_receiver']" style="font-weight: 700; color: red" id="loan_amount_real_receiver"
											   value="<?php echo number_format($profile_deduct[$i]['estimate_receive_money'], 2); ?>">
										<input type="hidden" class="hide-deduct-<?php echo $i; ?> deduct_before_interest" name="data['deduct']['deduct_before_interest']" value="<?php echo $deduct[$i]['deduct_before_interest'];?>">
										<?php foreach ($deduct_list as $key => $item){ ?>
											<input type="hidden" class="hide-deduct-<?php echo $i; ?> <?php echo $item['loan_deduct_list_code'];?>" name="data['deduct']['<?php echo $item['loan_deduct_list_code']; ?>']" value="<?php echo $deduct[$i][$item['loan_deduct_list_code']];?>">
										<?php } ?>
										<?php if(isset($cheque_list[$i])){ foreach ($cheque_list[$i] as $key => $cheque){ ?>
											<input type="hidden" class="hide-cheque-<?php echo $i;?> cheque-seq-<?php echo $cheque['seq'];?> amount" name="cheque[<?=$cheque['installment_seq']?>][<?=$cheque['seq']?>]['amount']" value="<?php echo $cheque['amount']?>">
											<input type="hidden" class="hide-cheque-<?php echo $i;?> cheque-seq-<?php echo $cheque['seq'];?> receiver" name="cheque[<?=$cheque['installment_seq']?>][<?=$cheque['seq']?>]['receiver']" value="<?php echo $cheque['receiver']?>">
										<?php } }else{ ?>
											<input type="hidden" class="hide-cheque-<?php echo $i;?> cheque-seq-1 amount" value="">
											<input type="hidden" class="hide-cheque-<?php echo $i;?> cheque-seq-1 receiver" value="">
										<?php } ?>
										<input type="hidden" class="hide-cheque-<?php echo $i;?> conter-cheque-all" value="<?php if(isset($cheque_list[$i])){ sizeof($cheque_list[$i]); } ?>">
									</td>
                                    <td><?php echo $status[$value['transfer_status']]; ?></td>
                                    <td>
                                        <?php echo $value['user_name'];?>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary required btn-app" type="button" <?php if($value['approve_status'] == '1'){?> disabled="disabled" <?php } ?> onclick="btnOnClickListener(this)">
                                            <i class="fa fa-check-circle-o "></i><span> อนุมัติ</span></button>
                                    </td>
                                </tr>
                            <?php $i++; } ?>
                        <?php } ?>
                        <?php if($contract['installment_amount'] >= $i && ($contract['installment_amount'] != 0 && ( $tmp_bal != 0 || $tmp_bal == null))){ ?>
                            <tr>
                                <td><span class="line-number"><?php echo $i; ?></span></td>
                                <td>
                                    <div class="input-with-icon g24-col-sm-24">
                                        <div class="form-group">
                                            <input id="approve_date_0" name="data[]['approve_date']"
                                                   class="form-control m-b-1 datepicker required" style="padding-left: 50px;"
                                                   type="text" value="<?php echo $this->center_function->mydate2date( (empty($profile_deduct[$i]['date_receive_money']) ? date('Y-m-d') : $profile_deduct[$i]['date_receive_money'])); ?>"
                                                   data-date-language="th-th" required title="" >
                                            <span class="icon icon-calendar input-icon m-f-1"></span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="loan_amount_balance"><? echo number_format($contract['loan_amount']- (($i ==  1) ? $contract['loan_amount']  : array_sum(array_column($installment, "amount"))), 2); ?></span></td>
                                <td>
                                    <input type="text" class="form-control m-b-1 text-right calc required amount_receiver"
                                           name="data[]['loan_amount_receiver']" onblur="calc(this)"
                                           value="<?php echo number_format($i == 1 ? $contract['loan_amount'] : 0, 2); ?>">
                                </td>
								<td>
									<div class="g24-col-sm-18">
										<input type="text" class="form-control m-b-1 text-right calc required deduct-all" readonly="readonly"
											   name="data[]['deduct']" style="font-weight: 700; color: green" value="<?php echo (isset($deduct[$i]) ? number_format(array_sum(array_values($deduct[$i])), 2) : 0.00); ?>">
									</div>
									<div class="g24-col-sm-6">
										<button class="btn btn-free btn-primary dialog_transfer_detail" type="button" onclick="btnOnDetailDeductListener(this)"><i class="fa fa-calculator"></i></button>
									</div>
								</td>
								<td>
									<input type="text" class="form-control m-b-1 text-right calc required total" readonly="readonly"
										   name="data[]['loan_amount_real_receiver']" style="font-weight: 700; color: red" id="loan_amount_real_receiver"
										   value="<?php echo number_format($profile_deduct[$i]['estimate_receive_money'], 2)?>">
									<input type="hidden" class="hide-deduct-<?php echo $i; ?> deduct_before_interest" name="data['deduct']['deduct_before_interest']" value="<?php echo $deduct[$i]['deduct_before_interest'];?>">
									<?php foreach ($deduct_list as $key => $item){ ?>
										<input type="hidden" class="hide-deduct-<?php echo $i; ?> <?php echo $item['loan_deduct_list_code'];?>" name="data['deduct']['<?php echo $item['loan_deduct_list_code']; ?>']" value="<?php echo $deduct[$i][$item['loan_deduct_list_code']];?>">

									<?php } ?>
									<?php if($cheque_list[$i]){ foreach ($cheque_list[$i] as $key => $cheque){ ?>
										<input type="hidden" class="hide-cheque-<?php echo $i;?> cheque-seq-<?php $cheque['seq'];?> amount" value="<?php echo $cheque['amount']?>">
										<input type="hidden" class="hide-cheque-<?php echo $i;?> cheque-seq-<?php $cheque['seq'];?> receiver" value="<?php echo $cheque['receiver']?>">
									<?php } }else{ ?>
										<input type="hidden" class="hide-cheque-<?php echo $i;?> cheque-seq-1 amount" value="">
										<input type="hidden" class="hide-cheque-<?php echo $i;?> cheque-seq-1 receiver" value="">
									<?php } ?>
									<input type="hidden" class="hide-cheque-<?php echo $i;?> conter-cheque-all" value="<?=sizeof($cheque_list[$i])?>">
								</td>
								<td>#N/A</td>
                                <td>#N/A</td>
                                <td>
                                    <button class="btn btn-sm btn-primary required btn-app" type="button" onclick="btnOnClickListener(this)">
                                        <i class="fa fa-check-circle-o "></i><span> อนุมัติ</span></button>
                                    <!-- <button class="btn btn-sm  btn-primary btn-add-row required" type="button" onclick="add(this)" >
                                        <i class="fa fa-plus"></i><span> เพิ่มงวด</span>
                                    </button> -->
                                </td>
                            </tr>
                        <?php $i++; } ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" id="loan_id" value="<?php echo $contract['id']?>">
            </div>
        </div>
    </div>
</div>
<!-- MODAL CONFIRM USER-->
<div class="modal fade" id="modal_confirm_user" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ยืนยันสิทธิ์การใช้งาน</h4>
            </div>
            <div class="modal-body">
                <p>ชื่อผู้มีสิทธิ์อนุมัติ</p>
                <input type="text" class="form-control" id="confirm_user">
                <p>รหัสผ่าน</p>
                <input type="password" class="form-control" id="confirm_pwd">
                <br>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button type="button" class="btn btn-info" id="submit_confirm_user">บันทึก</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<!-- MODAL CONFIRM USER-->
<?php $this->load->view("installment/dialog/add_detail");?>
<?php $this->load->view("installment/dialog/transfer_detail");?>
<script type="application/javascript">
    $(function () {
        $('.datepicker').datepicker({
            prevText: "ก่อนหน้า",
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
        })
    });

    $(document).ready(function(){
        if($("#installment").val() === ""){
            disabledTable();
        }
    });

    const btn_add = `<button class="btn btn-primary btn-add-row" type="button" onclick="add(this)">
                        <i class="fa fa-plus"></i><span> เพิ่มงวด</span>
                    </button>`;

    const removeCommas = (number) => {
        return parseFloat(number.split(',').join(''));
    };

    const addComma = (number) => {
    	if(typeof number === "string") number = parseFloat(number);
        return number.toLocaleString('en', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    };

    const calc = (_element) => {
        const _target = $(_element).closest("tr");
        const _number = removeCommas(_target.find('.line-number').html());
        const _bal = removeCommas(_target.find('.loan_amount_balance').html());
        const _n = removeCommas($(_element).val());
        const balance = _bal - _n;
        let t_balance = 0;
        //_target.find('.loan_amount_balance').html(addComma(balance));
        //console.log("element: ", _n);
        $(_element).val(addComma(_n));
        let _cal = 0;
        let _balance = 0;
        _target.closest('tbody').find('tr').each((i, item) => {
            _balance = removeCommas($(item).find(".loan_amount_balance").html());
            _cal = removeCommas($(item).find(".calc").val());
            if(i === 0){
                t_balance = removeCommas($("#loan_amount_balance").val());
                if(_cal === 0){
                    $(item).find('.loan_amount_balance').html(addComma(t_balance));
                }
            }
            if(i >= _number) {
                t_balance -= _cal;
                console.log("find 1: ", t_balance);
                $(item).find('.loan_amount_balance').html(addComma(t_balance));
            }else{
                console.log("find 2: ", t_balance);
                if((t_balance - _cal) < 0){
                    $(item).find('.loan_amount_balance').html(addComma(0.00));
                    $(_element).val(addComma(t_balance));
                    $(".btn-add-row").remove();
                }else {
                    t_balance -= _cal;
                    $(item).find('.loan_amount_balance').html(addComma(t_balance));
                    addBtn(_element);
                }
            }
        });

    };


    const addBtn = (_element) => {
        const _target = $(_element).closest("tr");
        const count = $(_element).closest("tbody").find("tr").length-1;
        const _max_amount = $("#installment").val()-1;
        if(_target.find("button").hasClass("btn-add-row") === false && count === _target.index() && _max_amount < _target.index()) {
            _target.find(".btn-app").after(btn_add);
        }
    };

    const add = (_element) => {
        const _target = $(_element).closest('tr');
        const _limiter =  parseInt($('#installment').val());
        const number = parseInt(_target.find('.line-number').html())+1;
        const amount = _target.find('.loan_amount_balance').html();

        if(_limiter < number){

            return false;
        }

        const template =
            `<tr>
                <td>
                    <span class="line-number">${number}</span>
                </td>
                <td>
                    <div class="input-with-icon g24-col-sm-24">
                        <div class="form-group">
                            <input id="approve_date_${number}" name="data[]['approve_date']" class="form-control m-b-1 datepicker" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" required title="" >
                            <span class="icon icon-calendar input-icon m-f-1"></span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="loan_amount_balance">${amount}</span>
                </td>
                <td>
                    <input type="text" class="form-control m-b-1 text-right calc" value="0.00" onblur="calc(this)" >
                </td>
                <td>
                    N/A
                </td>
                <td>
                    N/A
                </td>
                <td>
                     <button class="btn btn-primary btn-app" type="button" onclick="b">
                                <i class="fa fa-check-circle-o "></i><span> อนุมัติ</span>
                     </button>
                    <button class="btn btn-primary btn-add-row" type="button" onclick="add(this)">
                        <i class="fa fa-plus"></i><span> เพิ่มงวด</span>
                    </button>
                </td>
            </tr>`;


        const table  = $('#installing tbody');

        $(".btn-add-row").remove();

        table.append(template);

        if(_limiter === number ){
            $(".btn-add-row").remove();
        }

        $('.datepicker').datepicker({
            prevText: "ก่อนหน้า",
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
        })

    }

    const btnEdit = () => {
        installmentEnabled();
    };

    const btnSave = () => {
        const installment = $('#installment');
        if(installment.val() === ""){
            swal('กรุณากรอกจำนวนงวดในช่องแบ่งจ่าย ! ', "", "warning");
            installment.focus();
            return false;
        }

        swal({
            title: "ท่านต้องการบันทึกข้อมูลนี้ใช่หรือไม่ ! ",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },(isConfirm) => {
            if(isConfirm){
                sendInstallment().then((res) => {
                    if(res.status === 200 && res.status_code === "success"){
                        swal("บันทึกสำเร็จ", "", "success");
                        installmentDisable();
                        enableTable();
                    }else{
                        swal("บันทึกไม่สำเร็จ", "", "error");
                        installmentEnabled();
                    }
                }).catch((err) => {
                    swal("บันทึกไม่สำเร็จ", "", "error");
                    installmentEnabled();
                    console.log(err);
                })
            }
        });
    };

    const sendInstallment = () => {
        let data = {loan_id: $('#loan_id').val(), amount: removeCommas($('#installment').val())};
        return new Promise((resolve, reject) => {
            $.post(base_url+"/installment/update_amt", data, (res, status, xhr) => {
                console.log(res, status, xhr);
                resolve(res);
            }).error((err) => {
                reject(err);
            })
        })
    };

    const installmentDisable = () => {
        $('#installment').attr('readonly', 'readonly');
        $('#group-installing .icon').removeClass('fa-save').addClass('fa-pencil');
        $('#group-installing button').attr('onclick', 'btnEdit()');
    };

    const installmentEnabled = () => {
        $('#installment').removeAttr('readonly');
        $('#group-installing .icon').removeClass('fa-pencil').addClass('fa-save');
        $('#group-installing button').attr('onclick', 'btnSave()');
    };

    const enableTable = () => {
        $(".required").removeAttr('disabled').removeAttr('readonly');
    };

    const disabledTable = () => {
        $(".required").attr('disabled', 'disabled').attr('readonly', 'readonly');
    };

    const btnOnClickListener = (_element) => {
        const _target = $(_element).closest("tr");
        const _num = _target.find(".line-number").html();
        let data = {};
        data.loan_id    = $("#loan_id").val();
        data.datetime   = _target.find(".datepicker").val();
        data.balance    = removeCommas(_target.find(".loan_amount_balance").text());
        data.amount     = removeCommas(_target.find(".calc").val());
        data.seq        = _target.index()+1;

        let data2 = {};
        data2.loan_id    = $("#loan_id").val();
        data2.status_to  = 1;
        data2.amount     = removeCommas(_target.find(".calc").val());
        data2.esitmate   = removeCommas(_target.find("#loan_amount_real_receiver").val());
        data2.interest 	 = removeCommas(_target.find(".hide-deduct-"+_num+".deduct_before_interest").val());
        data2.date_approve   = _target.find(".datepicker").val();

        let data3 = {};
		data3.loan_id    = $("#loan_id").val();
		console.log("item: "+'.hide-deduct-'+_num);
        $('.hide-deduct-'+_num).each((i, k) => {

			data3[$(k).attr('class').split(' ')[1]] = $(k).val();
		});

        let data4 = {};
        data4.loan_id = $("#loan_id").val();
        data4.loan_amount_real_receiver = _target.find("#loan_amount_real_receiver").val();

        let data5 = [];
        $('input[type=hidden][class*=hide-cheque-'+_num+']').each((i, k) => {
        	if($(k).hasClass("conter-cheque-all")){
        		return;
			}
        	let obj = $(k).attr("class").split(" ").map((i, v) => {
        		if(i === "amount" || i === "receiver") return  i;
        		return i.replace(/[A-Za-z|\-]/gm, '');
        	});

        	data5[i] = {"installment_seq": obj[0], "seq": obj[1], "type": obj[2], "value": $(k).val() };
		});


		let request = {installment: data, loan: data2, deduct: data3, cheque: data5};
        console.log("request: ", request);

       if( data.amount === 0){
            swal("กรุณากรอกข้อมูลจำนวนเงินที่ต้องการอนุมัติ","", "warning");
            _target.find(".calc").focus();
        }else {
            confirmSummit(request);
        }
    };

    const confirmSummit = (data) => {
        swal({
            title: "ท่านต้องการบันทึกข้อมูลนี้ใช่หรือไม่ ! ",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },(isConfirm) => {
            if(isConfirm) {
                approveCallback(data).then((res) => {
                    if (res.status === 200 && res.status_code === "success") {
                        swal("บันทึกสำเร็จ", "", "success");

                    } else {
                        swal("บันทึกไม่สำเร็จ", "", "error");
                    }
                    return;
                }).then(() => {
                    $.get(base_url+"installment/index/"+data.loan.loan_id, (res) => {
                        $(".gutter-xs").replaceWith($(res).find(".gutter-xs"));
                        $('.datepicker').datepicker({
                            prevText: "ก่อนหน้า",
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
                    })
                }).catch((err) => {
                    swal("บันทึกไม่สำเร็จ", "", "error");
                    console.log(err);
                })
            }
        });
    };

    const btnOnSubmitListener = () => {

    };

    const permissionCallback  = () => {

    };

    const requestCallback = () => {
        return new Promise((resolve, reject) => {
            $.post(base_url+'', data, (res, status, xhr) => {
                if(res.status === 200){
                    resolve(res);
                }else{
                    reject(res);
                }
            })
        });
    };

    const approveCallback = (data) => {
        return new Promise((resolve, reject) => {
            $.post(base_url+"installment/approve", data, (res, status, xhr) => {
                if(res.status === 200){
                    resolve(res);
                }else{
                    reject(res);
                }
            })
        })
    };

    const setStorage = (token) => {
        sessionStorage.setItem("_token", token);
    };

    const clearStorage = () => {
        sessionStorage.clear();
    };

    $(document).on("click", "#submit_confirm_user", (e) => {
        btnOnClickListener();
    });

    const btnOnDetailDeductListener = (element) => {

    	const _target = $(element);
    	const _row = _target.closest("tr");
		const _raw = _row.find("input[type!=hidden]");
		let data = {};
		_raw.each( (index, item) => {
			let keys = $(item).attr("name").toString().replace("data", "").replace(/[(0-9)\[\[\]\']/gm, "");
			data[keys] = $(item).val();
		});
		console.log(data);
		data.loan_id = $("#loan_id").val();
		data.member_id = $("#member_id").val();

		let number = _row.find('.line-number').html();
		data.seq = number;

		$("#modal_transfer_detail #ref_line_number").val(number);
		calculateInterest(data, number)
		.then(setDialogDeduction)
		.then(openDialogDeduct)
		.catch((xhr) => {
			console.log(xhr);
		})
	};

	const calculateInterest = (data, number) => {
    	return new Promise((resolve, reject) => {
    		$.post(base_url+"/installment/get_calc_installments", data, (res, status, xhr) => {
    			if(status === "success" && res.status === "success"){
    				console.log("cheque: ", res.cheque);
    				return resolve({deduct: res, number: number});
				}else{
    				return resolve(xhr);
				}
			});
		} )
	};

	const setDialogDeduction = (res) => {
    	if(res.deduct.status === "success"){
			$("#modal_transfer_detail input").each((i, k)=>{ $(k).prop("disabled", false) });
			const number = res.number;
			const disabled= $("#installing tr:eq("+(number)+") .btn-app").is(":disabled");
			const hKey = ".hide-deduct-" + number;
			//const deduct = ['interest', 'deduct_mortgage_fee', 'deduct_cheque', 'deduct_survey_fee', 'deduct_law']
			const deduct = res.deduct.deduct_key;
			const _target = $("#modal_transfer_detail");
			let height = 62;
			let freeze = true;

			$("input[type=hidden].hide-deduct-"+number).each((k, i)=>{
				console.log($(i).attr("id")+": ", $(i).val());
				if (removeCommas($(i).val()) > 0.0){
					freeze = (freeze) ? false: freeze;
				}
			});
			const chequeCounter = $(".hide-cheque-"+number+".conter-cheque-all").val();

			try {
				const chequeList = res.deduct.cheque;
				const size = Object.keys(chequeList).length;
				console.log("size: ", size);
				$(".cheque-list").remove();
				if (size > 0) {
					for (let i = 1; i <= size; i++) {
						$(".cheque-container").append(createTemplateCheque(chequeList[i].seq, chequeList[i].receiver, addComma(chequeList[i].amount),));
					}
					height += (size*5);
				} else {
					$(".cheque-container").append(createTemplateCheque(1, res.deduct.fullname_th, addComma(res.deduct.loan_amount_receiver)));
				}
			}catch (e) {
				console.log(e);
				$(".cheque-container").append(createTemplateCheque(1, res.deduct.fullname_th, addComma(res.deduct.loan_amount_receiver)));
			}

			$("#modal_transfer_detail .modal-body").css("height", height+'vh')
			if(freeze) {
				_target.find("input[type!=hidden]").each((i, k) => {
					$(k).val(0.0);
				});
			}

			_target.find("#loan_real_amount").val(res.deduct.loan_amount_receiver);
			_target.find("#date_transfer").val(res.deduct.approve_date);
			_target.find("#deduct_before_interest").val(res.deduct.deduct_before_interest).trigger("blur");
			//_target.find("#fullname_th").val(res.deduct.fullname_th);

			$.each(deduct, (index, key) => {
				if(removeCommas(res.deduct.deduct_before_interest) > 0 && key === "deduct_before_interest") {
					//do nothing
				}else{
					$("#modal_transfer_detail #" + key).val($(hKey + "." + key).val());
				}
			});

			if(disabled) {
				$("#modal_transfer_detail input").each((i, k) => {
					$(k).attr("disabled", "disabled")
				});
			}

		}
    };

	var isOpened = false;
    const openDialogDeduct = () => {
    	isOpened = true;
    	$("#modal_transfer_detail").modal("show");
	};

	$(document).on("blur", ".decrease", () => {
		sumDecrease().then(sumDecrease).then(sumTotal).catch(err => {
			console.log(err);
		})
	});

	const sumDecrease = () => {
    	return new Promise(resolve => {
			let sum = 0;
			const _target = $("#modal_transfer_detail .decrease");
			_target.each(function(i){
				let amt = removeCommas($(this).val());
				amt = !isNaN(amt) ? amt : 0;
				if(!isNaN(amt)){
					$(this).val(addComma(amt));
					sum += amt;
				}
				if((_target.length-1) === i ) return resolve(sum);
			});
		});
	};

	const sumTotal = (decrease) => {
    	const loan_real_amount = removeCommas($("#modal_transfer_detail #loan_real_amount").val());
    	let total = loan_real_amount - decrease;
    	$("#modal_transfer_detail #deduct_all_total").val(addComma(decrease));
		$("#modal_transfer_detail #total_all_receiver").val(addComma(total)).trigger("change");
		modifyChequeNO();
	};

	const modifyChequeNO = () => {
		let amount = [];
		let loan_amount_receiver = removeCommas($("#total_all_receiver").val());
		$("input[class*=cheque-seq-][type!=hidden].amount").each((i, k) => {
			amount[i] = $(k).val();
		})
		amount.reverse();
		$.each(amount, (i, k) => {
			if(amount.length === (i+1)){
				amount[i] = addComma(loan_amount_receiver);
			}else {
				loan_amount_receiver -= removeCommas(k);
			}
		});
		amount.reverse();
		$("input[class*=cheque-seq-][type!=hidden].amount").each((i, k) => {
			$(k).val(amount[i]);
		});

	}


	$("#modal_transfer_detail #date_transfer, #modal_transfer_detail #loan_real_amount").change((e) => {
		let data = {};
		if(!isOpened) return; //break modal non clicked

		const number = $("#modal_transfer_detail #ref_line_number").val();
		const _target = $("#installing tr:eq("+number+")");
		const _datepicker = $("#modal_transfer_detail #date_transfer").val();
		_target.find(".datepicker").val(_datepicker);

		data.approve_date = _datepicker;
		data.loan_amount_receiver = $("#modal_transfer_detail #loan_real_amount").val();
		data.loan_id = $("#loan_id").val();
		data.member_id = $("#member_id").val();
		data.seq = number;

		calculateInterest(data, number)
		.then(setDialogDeduction)
		.then(openDialogDeduct)
		.catch((xhr) => {
			console.log(xhr);
		});
	});

	const confirmBtnDeduct = () => {
		const modalName = "#modal_transfer_detail";
		const src_deduct_all = $(modalName+" #deduct_all_total").val();
		const src_total = $(modalName +" #total_all_receiver").val();
		const number = $(modalName +" #ref_line_number").val();
		const realReceive = $(modalName +" #loan_real_amount").val();
		const _target = $("#installing tr:eq("+number+")");

		_target.find(".deduct-all").val(src_deduct_all);
		_target.find(".total").val(src_total);
		_target.find(".amount_receiver").val(realReceive).trigger("blur");

		const hKey = ".hide-deduct-"+number;
		const deduct = [ 'deduct_before_interest', 'deduct_mortgage_fee', 'deduct_cheque', 'deduct_survey_fee', 'deduct_law']
		$.each(deduct, (index, key) => {
			$(hKey+"."+key).val($(modalName+" #"+key).val());
		});
		//$(".hide-cheque-"+number).remove();
		$("input[class*=cheque-seq-][type!=hidden].amount").each((i, k) => {
			$("input[class*=hide-cheque-"+number+"][class*=cheque-seq-"+(i+1)+"][type=hidden].amount").val($(k).val());
		});
		$("#modal_transfer_detail").modal("hide");
	}

	const createTemplateCheque = (no, receiver, amount) => {

		return `\t\t\t\t\t<div class="col-sm-12 cheque-list" style="margin-top: 2px; margin-bottom: 2px">
		\t\t\t\t\t\t<div class="form-group col-sm-6">
		\t\t\t\t\t\t\t<label class="col-sm-6 text-right control-label" for="receiver">
		\t\t\t\t\t\t\t\tชื่อผู้รับเงิน
		\t\t\t\t\t\t\t</label>
		\t\t\t\t\t\t\t<div class="col-sm-6">
		\t\t\t\t\t\t\t\t<input class="form-control cheque-seq-${no} receiver" type="text" value="${receiver}">
		\t\t\t\t\t\t\t</div>
		\t\t\t\t\t\t</div>
		\t\t\t\t\t\t<div class="form-group col-sm-6">
		\t\t\t\t\t\t\t<label class="col-sm-6 text-right control-label" for="total_all_receiver">
		\t\t\t\t\t\t\t\tจำนวนเงินจ่ายจริง
		\t\t\t\t\t\t\t</label>
		\t\t\t\t\t\t\t<div class="col-sm-6">
		\t\t\t\t\t\t\t\t<input class="form-control cheque-seq-${no} amount" type="text" value="${amount}">
		\t\t\t\t\t\t\t</div>
		\t\t\t\t\t\t</div>
		\t\t\t\t\t</div>`;
	}

</script>
