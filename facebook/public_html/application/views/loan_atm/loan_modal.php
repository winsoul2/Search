		<input type="hidden" id="loan_id" name="loan_id" value="">
		<input type="hidden" id="loan_atm_id" name="loan_atm_id" value="<?php echo $row_loan_atm['loan_atm_id']; ?>">
		<input type="hidden" id="min_loan_amount" value="<?php echo $loan_atm_setting['min_loan_amount']; ?>">
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label">รหัสสมาชิก</label>
				<div class="g24-col-sm-5">
					<input class="form-control" id="member_id" type="text" name="member_id" value="<?php echo @$row_member['member_id']; ?>" readonly>
				</div>
				<label class="g24-col-sm-3 control-label ">ชื่อ-สกุล</label>
				<div class="g24-col-sm-7">
					<input class="form-control" type="text" value="<?php echo @$row_member['firstname_th'].' '.@$row_member['lastname_th'] ?>" readonly>
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input m-b-1">
				<label class="g24-col-sm-4 control-label">จำนวนเงิน</label>
				<div class="g24-col-sm-5">
					<input class="form-control" type="text" id="loan_amount" name="loan_amount" onkeyup="format_the_number(this)" value="">
				</div>
				<label class="g24-col-sm-3 control-label">การรับเงิน</label>
				<div class="g24-col-sm-10">
					<input type="radio" name="pay_type" id="pay_type_0" onclick="change_pay_type()" value="0"> เงินสด &nbsp;&nbsp;
					<input type="radio" name="pay_type" id="pay_type_3" onclick="change_pay_type()" value="3"> โอนเงินบัญชีสหกรณ์ &nbsp;&nbsp;
					<input type="radio" name="pay_type" id="pay_type_1" onclick="change_pay_type()" value="1"> โอนเงินบัญชีกรุงไทย &nbsp;&nbsp;
					<input type="radio" name="pay_type" id="pay_type_2" onclick="change_pay_type()" value="2"> ATM
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input m-b-1" id="account_choose_space" style="display:none;">
				<label class="g24-col-sm-4 control-label"></label>
				<div class="g24-col-sm-5">
				</div>
				<label class="g24-col-sm-3 control-label">เลขที่บัญชี</label>
				<div class="g24-col-sm-7 coop_account" style="display:none;">
					<select name="account_id" class="form-control" id="account_id">
						<option value="">เลือกบัญชี</option>
						<?php foreach($account_id as $key => $value){ ?>
							<option value="<?php echo $value['account_id']; ?>"><?php echo $value['account_id']." : ".$value['account_name']; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="g24-col-sm-4 bank_account" style="display:none;">
					<select name="bank_id" id="bank_id" class="form-control">
						<option value="">เลือกธนาคาร</option>
						<?php foreach($coop_bank as $key => $value){ ?>
							<option value="<?php echo $value['bank_id']; ?>" <?php echo $value['bank_id']==$row_member['dividend_bank_id']?'selected':''; ?>><?php echo $value['bank_name']; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="g24-col-sm-4 bank_account" style="display:none;">
					<input type="text" class="form-control" name="bank_account_id" id="bank_account_id" value="<?php echo $row_member['dividend_acc_num']; ?>">
				</div>
			</div>
			<div class="center">
				<button class="btn btn-primary" id="submit_button" type="button" onclick="check_submit()">บันทึก</button>&nbsp;&nbsp;&nbsp;
				<button class="btn btn-default" type="button" data-dismiss="modal">ยกเลิก</button>
			</div>
			