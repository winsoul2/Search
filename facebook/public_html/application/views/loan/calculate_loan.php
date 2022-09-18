<?php
    $day   = date('d');
    $month = date('n');
    $year  = date('Y') + 543;
?>
<div class="panel-body" style="padding:0px; margin:0px;">
<h3 style="padding:0px; margin:0px;">คำนวณเงินกู้</h3>
<input type="hidden" id="datenow" value="<?php echo date('Y-m-d'); ?>">
<input type="hidden" id="datenow_is" value="<?php echo date('Y-m-d H:i:s'); ?>">
<input type="hidden" name="updatetimestamp" id="updatetimestamp">
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-6 control-label ">วงเงินกู้</label>
				<div class="g24-col-sm-5">
					<input type="text" id="loan" onBlur="copy_value('loan', 'loan_amount');re_already_cal();check_share();check_loan_deduct();check_life_insurance();" onkeyup="format_the_number_decimal(this);" class="form-control form-loan inline-block loan"/>
				</div>
				<label class="g24-col-sm-1 control-label ">บาท</label>
				<label class="g24-col-sm-5 control-label ">อัตราดอกเบี้ย</label>
				<div class="g24-col-sm-5">
					<input type="number" id="interest" class="form-control form-loan interest_rate" step="0.01" value="" readonly>
				</div>
				<label class="g24-col-sm-1 control-label ">%</label>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-6 control-label ">จำนวน</label>
				<div class="g24-col-sm-5">
					<select name="data[coop_loan][period_type]" id="period_type" class="form-control">
					  <option value="1"> งวดที่ต้องการผ่อน </option>
					  <option value="2"> เงินที่ต้องการผ่อนต่องวด </option>
					</select>
				</div>
				<div class="g24-col-sm-5">
					<input type="nuumber" id="period" onkeyup="format_the_number(this)" class="form-control form-loan inline-block validation" data-meta="max_period" data-optional="loan" />
					<input type="nuumber" id="period_amount_bath" onkeyup="format_the_number(this)" class="form-control form-loan inline-block" />
					<input type="hidden" id="period_old" onkeyup="format_the_number(this)" class="form-control form-loan inline-block" />
				</div>
				<label class="g24-col-sm-1 control-label " id="type_period"></label>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-6 control-label ">ประเภทการชำระเงิน</label>
				<div class="g24-col-sm-5">
					<select id="pay_type" name="data[coop_loan][pay_type]"  class="form-control">
						<option value="1" >ชำระต้นเท่ากันทุกงวด</option>
						<option value="2" selected>ชำระยอดเท่ากันทุกงวด</option>
					</select>
				</div>
				<label class="g24-col-sm-6 control-label " style="display: none;">วันที่เริ่มคำนวณ</label>
				<div class="input-with-icon g24-col-sm-5">
					<div class="form-group"  style="display: none;">
						<input id="apply_date" name="apply_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" required title="" >
						<span class="icon icon-calendar input-icon m-f-1"></span>
					</div>
				</div>
			</div>
            <?php if(isset($loan_deduct_list) && count($loan_deduct_list) >= 1){ ?>
			<h3 style="padding:0px; margin:0px;">รายการหัก</h3>
			<?php 
			$j = 1;
			for($i=0;$i<round(count($loan_deduct_list)/2);$i++){
			?>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-6 control-label" <?php echo $loan_deduct_list_odd[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : '';?> ><?php echo (($loan_deduct_list_odd[$i]['loan_deduct_show'] == 1) ? "" : $j++.". ") .$loan_deduct_list_odd[$i]['loan_deduct_list']; ?></label>
				<div class="g24-col-sm-5" <?php echo $loan_deduct_list_odd[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : '';?> >
					<div class="form-group">
						<input class="form-control loan_deduct" type="text" name="data[loan_deduct][<?php echo $loan_deduct_list_odd[$i]['loan_deduct_list_code']; ?>]"  id="<?php echo $loan_deduct_list_odd[$i]['loan_deduct_list_code']; ?>" onkeyup="format_the_number_decimal(this);<?php echo (($loan_deduct_list_odd[$i]['loan_deduct_show'] == 0))  ? '' : 'cal_estimate_money()'; ?>">
					</div>
				</div>
				<!--ระบบประกันชีวิต-->
				<?php if(@$loan_deduct_list_odd[$i]['loan_deduct_list_code'] == 'deduct_insurance' && @$loan_deduct_list_odd[$i]['loan_deduct_show'] == '1'){ ?>
				<label class="g24-col-sm-11 control-label text-left cremation_show" >
					<?php
						$insurance_amount_old = @number_format(@$insurance_old,2);
						$insurance_amount_new = @number_format(@$insurance_new,2);
						
						$text_insurance = "";
						$text_insurance .= "ทุนประกันเดิม <span id='insurance_old'>".$insurance_amount_old."</span>.-  ";
						$text_insurance .= "ทุนประกันใหม่ <span id='insurance_new'>".$insurance_amount_new."</span>.- ";						
						echo "(".$text_insurance.")";
					?>
					<input type="button" class="btn btn-primary btn-excel" value="XLS" title = "คำนวณเบี้ยประกันชีวิต" style="width: auto !important;">   
					<input type="hidden" name="insurance_year" id="insurance_year" value="<?php echo @$insurance_year; ?>">
					<input type="hidden" name="insurance_date" id="insurance_date" value="<?php echo @$insurance_date; ?>">
					<input type="hidden" name="insurance_amount" id="insurance_amount" value="<?php echo @$insurance_amount; ?>">
					<input type="hidden" name="insurance_premium" id="insurance_premium" value="<?php echo @$insurance_premium; ?>">
					<input type="hidden" name="insurance_new_input" id="insurance_new_input" value="<?php echo @$insurance_amount_old; ?>">
					<input type="hidden" name="insurance_old_input" id="insurance_old_input" value="<?php echo @$insurance_amount_new; ?>">
				</label>	
				<label class="g24-col-sm-24 control-label text-left cremation_show">
					<label class="g24-col-sm-6 control-label" >ต้องการนำมาหัก</label>
					<div class="g24-col-sm-18">
						<div class="form-group">
							<label class="custom-control custom-control-primary custom-checkbox" style="padding-top: 9px;">
								<input type="checkbox" class="custom-control-input cremation_checkbox" id="cremation_type_1" name="cremation_type[1][id]" value="1" onclick="change_cremation_type()" attr_index="1" attr_data="<?php echo @$cremation_balance_1; ?>">
								<span class="custom-control-indicator" style="margin-top: 9px;"></span>
								<span class="custom-control-label">ชสอ. <span id="text_ch_s_o"><?php echo number_format(@$cremation_balance_1,2)?></span> .-</span>
								<input type="hidden" name="cremation_type[1][amount_balance]" id="cremation_amount_1" value="<?php echo @$cremation_balance_1; ?>">
								<input type="hidden" name="cremation_import_1" id="cremation_import_1" value="<?php echo @$cremation_balance_1; ?>">
							</label>
						
							<label class="custom-control custom-control-primary custom-checkbox" style="padding-top: 9px;">
								<input type="checkbox" class="custom-control-input cremation_checkbox" id="cremation_type_2" name="cremation_type[2][id]" value="2" onclick="change_cremation_type()" attr_index="2" attr_data="<?php echo @$cremation_balance_2; ?>">
								<span class="custom-control-indicator" style="margin-top: 9px;"></span>
								<span class="custom-control-label">สสอค. <span id="text_s_s_o_k"><?php echo number_format(@$cremation_balance_2,2)?></span> .-</span>
								<input type="hidden" name="cremation_type[2][amount_balance]" id="cremation_amount_2" value="<?php echo @$cremation_balance_2; ?>">
								<input type="hidden" name="cremation_import_2" id="cremation_import_2" value="<?php echo @$cremation_balance_2; ?>">
							</label>							
							<input type="hidden" id="cremation_all_total" value="<?php echo $cremation_all_total; ?>">
						</div>
					</div>
				</label>	
				<?php } ?>
				<!--ระบบประกันชีวิต-->
				<?php if(@$loan_deduct_list_even[$i]['loan_deduct_list'] != ''){ ?>
				<label class="g24-col-sm-6 control-label" <?php echo $loan_deduct_list_even[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : '';?>><?php echo (($loan_deduct_list_even[$i]['loan_deduct_show'] == 1) ? "" : $j++.". ").$loan_deduct_list_even[$i]['loan_deduct_list']; ?></label>
				<div class="g24-col-sm-5" <?php echo $loan_deduct_list_even[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : '';?>>
					<div class="form-group">
						<input class="form-control loan_deduct" type="text" name="data[loan_deduct][<?php echo $loan_deduct_list_even[$i]['loan_deduct_list_code']; ?>]"  id="<?php echo $loan_deduct_list_even[$i]['loan_deduct_list_code']; ?>" onkeyup="format_the_number(this);<?php echo (($loan_deduct_list_even[$i]['loan_deduct_show'] == 0))  ? '' : 'cal_estimate_money()'; ?>check_life_insurance();">
					</div>
				</div>
				<?php } ?>
			</div>
			<?php
			} 
			?>
			<?php } ?>
            <?php if(isset($loan_buy_list) && count($loan_buy_list) >= 1){ ?>
			<h3 style="padding:0px; margin:0px;">รายการซื้อ</h3>
			<?php 
			$j = 1;
                for ($i = 0; $i < round(count($loan_buy_list) / 2); $i++) {
                    ?>

                    <div class="g24-col-sm-24 modal_data_input">
                        <label class="g24-col-sm-6 control-label" <?php echo $loan_buy_list_odd[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : ''; ?>><?php echo $j++ . ". " . $loan_buy_list_odd[$i]['loan_deduct_list']; ?></label>
                        <div class="g24-col-sm-5" <?php echo $loan_buy_list_odd[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : ''; ?>>
                            <div class="form-group">
                                <input class="form-control loan_deduct" type="text"
                                       name="data[loan_deduct][<?php echo $loan_buy_list_odd[$i]['loan_deduct_list_code']; ?>]"
                                       id="<?php echo $loan_buy_list_odd[$i]['loan_deduct_list_code']; ?>"
                                       onkeyup="format_the_number_decimal(this);cal_estimate_money()">
                            </div>
                        </div>
                        <?php if (@$loan_buy_list_even[$i]['loan_deduct_list'] != '') { ?>
                            <label class="g24-col-sm-6 control-label" <?php echo $loan_buy_list_even[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : ''; ?>><?php echo $j++ . ". " . $loan_buy_list_even[$i]['loan_deduct_list']; ?></label>
                            <div class="g24-col-sm-5" <?php echo $loan_buy_list_even[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : ''; ?>>
                                <div class="form-group">
                                    <input class="form-control loan_deduct" type="text"
                                           name="data[loan_deduct][<?php echo $loan_buy_list_even[$i]['loan_deduct_list_code']; ?>]"
                                           id="<?php echo $loan_buy_list_even[$i]['loan_deduct_list_code']; ?>"
                                           onkeyup="format_the_number(this);cal_estimate_money();check_life_insurance();">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php
                }
            }
			?>
			
			<input class="loan_deduct" type="hidden" name="data[loan_deduct][deduct_pay_prev_loan]"  id="deduct_pay_prev_loan">

			<?php
			if(!empty($prev_loan_active)){
				$i=0;
			foreach($prev_loan_active as $key => $value){ ?>
				<div class="g24-col-sm-24 modal_data_input">
					<label class="g24-col-sm-6 control-label" ><?php echo $i==0?'หักกลบสัญญาเดิม':''; ?></label>
					<div class="g24-col-sm-5">
						<div class="form-group">
							<label class="custom-control custom-control-primary custom-checkbox" style="padding-top: 9px;">
								<input type="checkbox" class="custom-control-input prev_loan_checkbox" id="prev_loan_checkbox_<?php echo $i; ?>" name="prev_loan[<?php echo $i; ?>][id]" value="<?php echo $value['id']; ?>" onclick="change_prev_loan_pay_type()" ref_id="<?php echo $value['id']; ?>" data_type="<?php echo $value['type']; ?>" attr_index="<?php echo $i; ?>">
								<span class="custom-control-indicator" style="margin-top: 9px;"></span>
								<span class="custom-control-label"><?php echo  "".$value['loan_name']." ( ".$value['contract_number']." ) "; ?></span>
							</label>
							<input type="hidden" name="prev_loan[<?php echo $i; ?>][type]" value="<?php echo $value['type']; ?>">
							<input type="hidden" id="prev_loan_total_<?php echo $i; ?>" value="<?php echo number_format($value['prev_loan_total'],2); ?>">
							<input type="hidden" id="principal_without_finance_month_<?php echo $i; ?>" value="<?php echo number_format($value['principal_without_finance_month'],2); ?>">
						</div>
					</div>
					<div class="g24-col-sm-6">
						<label class="custom-control custom-control-primary custom-checkbox" style="padding-top: 3px;">
						<input type="radio" name="prev_loan[<?php echo $i; ?>][pay_type]" id="prev_loan_pay_type_1_<?php echo $i; ?>" onclick="change_prev_loan_pay_type()" value="principal" <?=($value['checked']=="principal" ? "checked" : "")?>> คืนดอกเบี้ยส่วนต่าง
						<input type="radio" name="prev_loan[<?php echo $i; ?>][pay_type]" id="prev_loan_pay_type_2_<?php echo $i; ?>" onclick="change_prev_loan_pay_type()" value="all" <?=($value['checked']=="all" ? "checked" : "")?>> คืนต้นและดอก
						</label>
					</div>
					<label class="g24-col-sm-2" >ยอดเงิน</label>
					<div class="g24-col-sm-3">
						<input type="hidden" name="prev_loan[<?php echo $i; ?>][interest]" value="">
						<input class="form-control prev_loan_amount" attr_index="<?php echo $i; ?>" type="text" name="prev_loan[<?php echo $i; ?>][amount]" id="prev_loan_amount_<?php echo $i; ?>">
					</div>
				</div>
			<?php $i++; }
			}			?>
			<h3 style="padding:0px; margin:0px;">ประมาณการ</h3>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-6 control-label" >หักกลบสัญญาเดิม เงินต้น</label>
				<div class="g24-col-sm-5">
					<input class="form-control" type="text" id="joker-principal" value="" readonly>
				</div>
				<label class="g24-col-sm-6 control-label" for="joker-interest">หักกลบสัญญาเดิม ดอกเบี้ย</label>
				<div class="g24-col-sm-5">
					<input class="form-control" type="text" id="joker-interest" value="" readonly>
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-6 control-label" >ยอดเงินที่จะได้รับโดยประมาณ</label>
				<div class="g24-col-sm-5">
					<div class="form-group">
						<input class="form-control estimate_value" type="text" name="data[loan_deduct_profile][estimate_receive_money]" id="estimate_receive_money" readonly>
					</div>
				</div>
				<label class="g24-col-sm-6 control-label" >ได้รับเงินประมาณ วันที่</label>
				<div class="g24-col-sm-5">
					<div class="form-group">
						<div class="input-with-icon">
							<div class="form-group">
								<input id="date_receive_money" name="data[loan_deduct_profile][date_receive_money]" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d'));?>" data-date-language="th-th" required title="" >
								<span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-6 control-label" >วันที่ชำระเงินงวดแรก</label>
				<div class="g24-col-sm-5">
					<div class="form-group">
						<input class="form-control estimate_value" type="text" id="date_first_period_label" readonly>
						<input type="hidden" class="estimate_value" name="data[loan_deduct_profile][date_first_period]" id="date_first_period" readonly>
					</div>
				</div>
				<label class="g24-col-sm-6 control-label" >ดอกเบี้ยในการชำระงวดแรก</label>
				<div class="g24-col-sm-5">
					<div class="form-group">
						<input class="form-control estimate_value" type="text" name="data[loan_deduct_profile][first_interest]" id="first_interest" readonly>
					</div>
				</div>
			</div>
			
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-6 control-label" >คงเหลือ</label>
				<div class="g24-col-sm-5">
					<div class="form-group">
						<input class="form-control estimate_value" type="text" name="salary_balance" id="salary_balance" readonly>
					</div>
				</div>
				<label class="g24-col-sm-6 control-label" >คิดเป็น</label>
				<div class="g24-col-sm-5">
					<div class="form-group">
						<input class="form-control estimate_value" type="text" name="percent_salary_balance" id="percent_salary_balance" readonly>
					</div>
				</div>
				<label class="g24-col-sm-1 control-label ">%</label>
			</div>

            <div class="g24-col-sm-24 modal_data_input">
                <label class="g24-col-sm-6 control-label">ดอกเบี้ย ณ ที่จ่าย</label>
                <div class="g24-col-sm-5">
                    <div class="form-group">
                        <input class="form-control interest_current_value" type="text" name="interest_current" id="interest_current" readonly>
                    </div>
                </div>
            </div>
	
			<div class="center" style="margin-top: 5px;margin-bottom: 15px;">
				<input type="button" class="btn btn-primary btn-calculate" value="คำนวณ"> 
				<input type="button" class="btn btn-primary" id="btn_calculate_choose" onclick="close_modal('cal_period_normal_loan')" style="width: auto;" value="เลือกใช้ค่าคำนวณ">
				<button type="button" class="btn btn-primary" id="bt_show_table" onclick="show_cal_table();" style="width: auto !important;">แสดงการคำนวณการส่งค่างวด</button>
				<button type="button" class="btn btn-primary" id="bt_hide_table" onclick="hide_cal_table();" style="width: auto !important;">ซ่อนการคำนวณการส่งค่างวด</button>
				<button type="button" class="btn btn-primary btn-calculate" onclick="printElem('cal_table');">พิมพ์ตาราง</button>
				<button type="button" class="btn btn-primary" onclick="print_estimate();">พิมพ์ประมาณการ</button>
			</div>
			<div id="result_wrap"></div>
</div>
  <div class="modal fade" id="alertLaon" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header modal-header-info">
          <button type="button" class="close" onclick="close_modal('alertLaon')">&times;</button>
          <h4 class="modal-title">
            <h4>แจ้งเตือน</h4>
          </h4>
        </div>
        <div class="modal-body">
          <p style="font-size:18px;" id="alert_space"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" onclick="close_modal('alertLaon')">Close</button>
        </div>
      </div>
    </div>
  </div>

<script>
	Number.prototype.format = function(n, x, s, c) {
	    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
		    num = this.toFixed(Math.max(0, ~~n));
	    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
	};
	function get_data_from_modal(){
		$('.period_amount').val($('#max_period').val());
		$('.date_start_period_label').val($('#first_date_period_label').val());
		$('.date_start_period').val($('#first_date_period').val());
		$('#last_date_period').val($('#last_period').val());
		 
		$('#date_period_1').val($('#first_date_period_label').val());
		$('#date_period_2').val($('#second_date_period_label').val());
		$('#money_period_1').val($('#first_pay').val());
		$('#money_period_2').val($('#second_pay').val());
		$('#summonth_period_1').val($('#first_summonth').val());
		$('#summonth_period_2').val($('#second_summonth').val());

		$('#date_first_period').val($('#first_date_period').val());
		$('#date_first_period_label').val($('#first_date_period_label').val());
		$('#first_interest').val($('#first_interest_amount').val());
		
		cal_estimate_money();
		
	}
	function cal_estimate_money(){
		if($("#loan").val() != ''){
			var estimate_receive_money = parseFloat($("#loan").val().replace(/,/g, ""));
			$('.loan_deduct').each(function(){
				var deduct_amount = $(this).val().replace(/,/g, "");
				if(deduct_amount!='' && deduct_amount > 0){
					estimate_receive_money = parseFloat(estimate_receive_money).toFixed(2) - parseFloat(deduct_amount).toFixed(2);
				}
			});

			$('.deduct_return').each(function(){
				var deduct_return = parseFloat(removeCommas( $(this).val()));

				if(deduct_return!='' && deduct_return > 0){
					console.log("ADD", deduct_return);
					estimate_receive_money = parseFloat(estimate_receive_money) + deduct_return;
				}
			});

			if(estimate_receive_money < 0){
				$('#estimate_receive_money').val('0');
			}else{
				$('#estimate_receive_money').val(estimate_receive_money.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				$('.display#estimate-money').val(estimate_receive_money.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				$('.cheque-amount:eq(0)').val(estimate_receive_money.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
			}
		}
	}
	function cal(){
		var date = $('#date_receive_money').val().split('/');
		var day = date[0];
		var month = date[1];
		var year = date[2];
		console.log($("#interest").val());
      $.ajax({
				type: "POST"
				, url: base_url+"/loan/ajax_calculate_loan"
				, data: {
						"ajax" : 1
						, "do" : "cal"
						, "loan" : $("#loan").val().replace(/,/g, "")
						, "pay_period" : $("#pay_period").val()
						, "pay_type" : $("#pay_type").val()
						, "day" : day
						, "month" : month
						, "year" : year
						, "period_type" : $("#period_type").val()
						, "period" : $("#period").val().replace(/,/g, "")
						, "interest" : $("#interest").val()
						, "_time" : Math.random()
						, "loan_type" : $("#loan_type").val()
						, "money_period_2" : $("#money_period_2").val().replace(/,/g, "")
						, "period_old" : $("#period_old").val()
						, "period_amount_bath" : $("#period_amount_bath").val().replace(/,/g, "")
				}
				, async: true
				, success: function(msg) {
					$("#result_wrap").html(msg);
					hide_cal_table(); //ปิดไว้พี่รสปิดส่วนของการแสดงตารางการคำนวณงวดส่ง 2019-02-25
					get_data_from_modal();
					check_salary_balance();
                    show_interest_current();
                    setTimeout(function(){
                        cal_estimate_money();
                    }, 800)
				}
			});
		}
	$('document').ready(function() {
		$("#btn_calculate_choose").hide(); 
		$("#bt_hide_table").hide();	
		$("#bt_show_table").hide();	
		//$('#cal_period_normal_loan').modal('show');		
		$(".btn-calculate").click(function(e){
			 //if($.trim($('#loan').val()) == '' || $.trim($('#period').val()) == '' || $.trim($('#date_receive_money').val()) == ''){
				 var alert_text = '';
				 if($.trim($('#loan').val()) == ''){
					alert_text += '- กรุณากรอกจำนวนวงเงินกู้\n';
				 }
				 if($.trim($('#period').val()) == '' && $.trim($('#period_amount_bath').val()) == '') {
					alert_text += '- กรุณากรอกจำนวนงวด หรือ จำนวนเงินต่องวด\n';
				 }
				 if($.trim($('#date_receive_money').val()) == '') {
					alert_text += '- กรุณากรอกวันที่ ได้รับเงินประมาณ\n';
				 }
				 var date = $('#date_receive_money').val().split('/');
				 var receive_date = (parseInt(date[2])-543)+"-"+date[1]+"-"+date[0];
					var receive_date = new Date();
					receive_date.setFullYear((parseInt(date[2])-543),(parseInt(date[1])-1),date[0]);
					var today = new Date();
				 //if(receive_date < today){
				 //	 alert_text += '- กรุณาเปลี่ยนวันที่ได้รับเงิน\n';
				 //}
				 /*if($.trim($('#apply_date').val()) == '') {
					alert_text += '- กรุณากรอกวันที่เริ่มชำระเงิน\n';
				 }*/
			 if(alert_text!=''){
				 swal(alert_text);
			 } else {
				cal();
				//check_loan_deduct();
			 }
			$("#btn_calculate_choose").show(); 
		});

		//$("#select_interest option").filter(function() {
		  //return $(this).val() == $("#interest").val();
		//}).attr('selected', true);

		//$("#select_interest").live("change", function() {
			//$("#interest").val($(this).find("option:selected").attr("value"));
		//});

		$('#loan').keyup(function(event) {
		  if(event.which >= 37 && event.which <= 40) return;
		  /*$(this).val(function(index, value) {
			return value
			.replace(/\D/g, "")
			.replace(/\B(?=(\d{3})+(?!\d))/g, ",")
			;
		  });*/
		});

		$( "#period_type" )
		.change(function () {
		  var str = " ";
		  $( "#period_type option:selected" ).each(function() {
			if ($(this).val() == 1) {
				str += "งวด";
				$("#period_amount_bath").hide();
				$("#period").show();
			} else {
				str += "บาท";		
				$("#period_amount_bath").show();
				$("#period").hide()
			}
		  });
		  $( "#type_period" ).text( str );
		})
		.change();
		
		$("#createdatetime").datepicker({
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
		
		$("#apply_date").datepicker({
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
		$("#date_receive_money").datepicker({
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
		
		if($('#type_deduct_2').is(':checked')){
			$("#deduct_share").val("");
		}
		
		$("#type_deduct_1").click(function(){
			var  deduct_blue_deposit = $("#deduct_blue_deposit").val();
			$("#deduct_share").val(deduct_blue_deposit);
			$("#deduct_blue_deposit").val("");
			
		});
		
		$("#type_deduct_2").click(function(){
			var  deduct_share = $("#deduct_share").val();
			$("#deduct_blue_deposit").val(deduct_share);
			$("#deduct_share").val("");
		});	

	});
	
	function show_cal_table(){
		$("#cal_table").show();
		$("#bt_hide_table").show();	
		$("#bt_show_table").hide();
		//แสดงการคำนวณการส่งค่างวด
	}
	
	function hide_cal_table(){
		$("#cal_table").hide();
		$("#bt_hide_table").hide();	
		$("#bt_show_table").show();		
		//ซ่อนการคำนวณการส่งค่างวด
	}

	function show_interest_current(){
	    var int_current = $('#interest_current_value').val();
	    $('#interest_current').val(int_current);
	    $('#deduct_before_interest').val(int_current);
    }
</script>
