		<input type="hidden" id="loan_type" name="data[coop_loan][loan_type]" value="">
		<input type="hidden" id="loan_id" name="loan_id" value="">
		<input type="hidden" id="is_compromise" name="is_compromise" value="">
			
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label ">วันที่ทำรายการ</label>
				<div class="input-with-icon g24-col-sm-5">
					<div class="form-group">
						<input id="createdatetime" name="createdatetime" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" required title="" >
						<span class="icon icon-calendar input-icon m-f-1"></span>
					</div>
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label ">คำร้องเลขที่</label>
				<div class="g24-col-sm-5">
					<input class="form-control" type="text" id="petition_number" name="data[coop_loan][petition_number]" value="">
				</div>
				<label class="g24-col-sm-3 control-label ">แนบไฟล์คำขอกู้</label>
				<div class="g24-col-sm-5">
					<label class="fileContainer btn btn-info">
						<span class="icon icon-paperclip"></span> 
						เลือกไฟล์
						<input type="file" class="form-control" name="file_attach[]" value="" multiple>
					</label>
					
				</div>
				<div class="g24-col-sm-1">
					<button class="btn btn-primary" id="btn_show_file" type="button" onclick="show_file()" style="display:none;">แสดงไฟล์แนบ</button>
				</div>
				<label class="g24-col-sm-3 control-label contract_number">สัญญาเลขที่</label>
				<div class="g24-col-sm-3">
					<input class="form-control contract_number" type="text" id="contract_number" value="" readonly>
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label">รหัสสมาชิก</label>
				<div class="g24-col-sm-5">
					<input class="form-control" id="member_id" type="text" name="data[coop_loan][member_id]" value="<?php echo @$row_member['member_id']; ?>" readonly>
				</div>
				<label class="g24-col-sm-3 control-label ">ชื่อ-สกุล</label>
				<div class="g24-col-sm-7">
					<input class="form-control" type="text" value="<?php echo @$row_member['firstname_th'].' '.@$row_member['lastname_th'] ?>" readonly>
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label">จำนวนเงินที่ขอกู้</label>
				<div class="g24-col-sm-5">
					<input class="form-control loan_amount validation" type="text" id="loan_amount" name="data[coop_loan][loan_amount]" onBlur="copy_value('loan_amount', 'loan');re_already_cal();cal_guarantee_person();check_share();check_loan_deduct();create_input_garantor();check_life_insurance();format_the_number_decimal(this);" value="" data-meta="credit_limit">
				</div>
				<label class="g24-col-sm-3 control-label ">เหตุผลการกู้</label>
				<div class="g24-col-sm-9">
					<select name="data[coop_loan][loan_reason]" class="form-control" id="loan_reason">
						<option value="">ไม่ระบุ</option>
						<?php 
						foreach($rs_loan_reason as $key => $row_loan_reason){
						?>
						<option value="<?php echo $row_loan_reason['loan_reason_id']; ?>"><?php echo $row_loan_reason['loan_reason']; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label"></label>
				<div class="g24-col-sm-5">
					<input type="button" class="btn btn-primary" style="width:150px" value="อัพเดทรายได้สมาชิก" onclick="open_modal('update_salary_modal')">
				</div>
			</div>
	<div id="type_1" style="display:none;">
		<h3>หลักประกัน</h3>
		<div id="type_1_1" style="display:none;">
		<?php $guarantee_type="1"; ?>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-5 control-label left"><input type="checkbox" name="data[coop_loan_guarantee][<?php echo $guarantee_type; ?>][guarantee_type]" value="<?php echo $guarantee_type; ?>" id="guarantee_<?php echo $guarantee_type; ?>" onclick="choose_guarantee('guarantee_<?php echo $guarantee_type; ?>')"> ใช้ผู้ค้ำประกัน</label>
			</div>
			<div id="sec_garantor">
			
			</div>
			<?php
			for($i=1;$i<=0;$i++){
			?>
				<div class="g24-col-sm-24 modal_data_input">
					<label class="g24-col-sm-4 control-label ">ผู้ค้ำลำดับที่ <?php echo $i;?> </label>
					<div class="g24-col-sm-4">
						<div class="input-group">
							<input class="form-control guarantee_<?php echo $guarantee_type; ?> guarantee_person_id" guarantee_person_id='<?php echo $i;?>' type="text" id="guarantee_person_id_<?php echo $i;?>" name="data[coop_loan_guarantee][<?php echo $guarantee_type; ?>][coop_loan_guarantee_person][id][<?php echo $i;?>]" value="" disabled="true" readonly>
							<span class="input-group-btn btn_search_member">
								<button type="button" class="btn btn-info guarantee_<?php echo $guarantee_type; ?> btn-search" disabled="true" onclick="search_member_modal('<?php echo $i;?>')"><span class="icon icon-search"></span></button>
							</span>	
						</div>
					</div>
					<div class="g24-col-sm-1">
						<span class="btn_delete_member" id="btn_delete_<?php echo $i;?>" style="display:none">
							<button type="button" class="btn btn-danger guarantee_<?php echo $guarantee_type; ?> btn-search" disabled="true" onclick="delete_guarantee_person('<?php echo $i;?>')"><span class="icon icon-trash"></span></button>
						</span>
					</div>
					<label class="g24-col-sm-3 control-label ">ชื่อ-สกุล </label>
					<div class="g24-col-sm-4">
						<input class="form-control" type="text" id="guarantee_person_name_<?php echo $i;?>" value="" readonly>
					</div>
					<label class="g24-col-sm-3 control-label ">สังกัด </label>
					<div class="g24-col-sm-4">
						<input class="form-control" type="text" id="guarantee_person_dep_<?php echo $i;?>" value="" readonly>
					</div>
					
				</div>

				<div class="g24-col-sm-24 modal_data_input">
					<label class="g24-col-sm-4 control-label ">ภาระค้ำประกัน </label>
					<div class="g24-col-sm-4">
						<input class="form-control guarantee_person_<?php echo $i;?>" type="text" id="guarantee_person_amount_<?php echo $i;?>" name="data[coop_loan_guarantee][<?php echo $guarantee_type; ?>][coop_loan_guarantee_person][guarantee_person_amount][<?php echo $i;?>]" value="" disabled="true">
					</div>
					<label class="g24-col-sm-4 control-label " style="display:none;">เลขที่สัญญาค้ำประกัน </label>
					<div class="g24-col-sm-4" style="display:none;">
						<input class="form-control guarantee_person_<?php echo $i;?>" type="text" id="guarantee_person_contract_number_<?php echo $i;?>" name="data[coop_loan_guarantee][<?php echo $guarantee_type; ?>][coop_loan_guarantee_person][guarantee_person_contract_number][<?php echo $i;?>]" value="" disabled="true">
					</div>
					<label class="g24-col-sm-4 control-label ">ค้ำแล้ว </label>
					<label class="g24-col-sm-1 control-label" id="count_guarantee_<?php echo $i;?>"></label>
					<label class="g24-col-sm-1 control-label ">สัญญา </label>
				</div>
			<?php		
			}
			?>
		</div>
		<div id="type_1_2" style="display:none;">
			<?php $guarantee_type="2"; ?>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-5 control-label left"><input type="checkbox" name="data[coop_loan_guarantee][<?php echo $guarantee_type; ?>][guarantee_type]" value="<?php echo $guarantee_type; ?>" id="guarantee_<?php echo $guarantee_type; ?>" onclick="choose_guarantee('guarantee_<?php echo $guarantee_type; ?>')"> ใช้หุ้นค้ำประกัน</label>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label ">จำนวนหุ้นสะสม</label>
				<div class="g24-col-sm-5">
					<input class="form-control guarantee_<?php echo $guarantee_type; ?>" type="text" id="guarantee_amount_<?php echo $guarantee_type; ?>" name="data[coop_loan_guarantee][<?php echo $guarantee_type; ?>][amount]" onkeyup="cal_share_result(this.value)" value="<?php echo number_format(@$count_share); ?>" disabled="true" readonly>
				</div>
				<label class="g24-col-sm-3 control-label ">คิดเป็นมูลค่า</label>
				<div class="g24-col-sm-4">
					<input class="form-control share_price" type="text" id="guarantee_price_<?php echo $guarantee_type; ?>" name="data[coop_loan_guarantee][<?php echo $guarantee_type; ?>][price]" value="<?php echo number_format(@$cal_share,2); ?>" readonly>
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input" style="display:none;">
				<label class="g24-col-sm-4 control-label ">มูลค่ากองทุนสำรองเลี้ยงชีพ</label>
				<div class="g24-col-sm-5">
					<input class="form-control guarantee_<?php echo $guarantee_type; ?>" type="text" id="guarantee_other_price_<?php echo $guarantee_type; ?>" name="data[coop_loan_guarantee][<?php echo $guarantee_type; ?>][other_price]" onblur="format_the_number_decimal(this)" value="" disabled="true">
				</div>
			</div>
		</div>
		<div id="type_1_3" style="display:none;">
			<?php $guarantee_type="3"; ?>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-5 control-label left"><input type="checkbox" name="data[coop_loan_guarantee][<?php echo $guarantee_type; ?>][guarantee_type]" value="<?php echo $guarantee_type; ?>" id="guarantee_<?php echo $guarantee_type; ?>" onclick="choose_guarantee('guarantee_<?php echo $guarantee_type; ?>')"> ใช้เงินฝากค้ำประกัน</label>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label ">เงินฝาก</label>
				<div class="g24-col-sm-10">
					<?php
					foreach($saving_list as $key => $value){
					?>
						<input type="checkbox" id="guarantee_saving_<?=$value['account_id']?>" name="guarantee_saving[]" value="<?=$value['account_id']?>">
						<label for="guarantee_saving_<?=$value['account_id']?>"><?=$value['account_id']?> คงเหลือ <?=number_format($value['transaction_balance'], 2)?> บาท</label><br>
					<?php
					}
					?>
					<!-- <input class="form-control guarantee_<?php echo $guarantee_type; ?>" type="text" id="guarantee_amount_<?php echo $guarantee_type; ?>" name="data[coop_loan_guarantee][<?php echo $guarantee_type; ?>][amount]" value="<?php echo number_format(@$deposit_amount, 2); ?>" disabled="true" readonly> -->
				</div>
			</div>

		</div>
		<div id="type_1_4" style="display:none;">
			<?php $guarantee_type="4"; ?>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-5 control-label left"><input type="checkbox" name="data[coop_loan_guarantee][<?php echo $guarantee_type; ?>][guarantee_type]" value="<?php echo $guarantee_type; ?>" id="guarantee_<?php echo $guarantee_type; ?>" onclick="choose_guarantee('guarantee_<?php echo $guarantee_type; ?>')"> ใช้อสังหาริมทรัพย์ค้ำประกัน</label>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label ">ตำแหน่งที่ดิน</label>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label ">ระวาง</label>
				<div class="g24-col-sm-2">
					<input class="form-control guarantee_<?php echo $guarantee_type; ?>" id="real_estate_position_1" type="text" name="data[coop_loan_guarantee_real_estate][real_estate_position_1]" value="" disabled="true">
				</div>
				<label class="g24-col-sm-1" style="text-align:center">|||</label>
				<div class="g24-col-sm-2">
					<input class="form-control guarantee_<?php echo $guarantee_type; ?>" id="real_estate_position_2" type="text" name="data[coop_loan_guarantee_real_estate][real_estate_position_2]" value="" disabled="true">
				</div>
				<label class="g24-col-sm-3 control-label ">เลขที่ดิน</label>
				<div class="g24-col-sm-4">
					<input class="form-control guarantee_<?php echo $guarantee_type; ?>" id="land_number" type="text" name="data[coop_loan_guarantee_real_estate][land_number]" value="" disabled="true">
				</div>
				<label class="g24-col-sm-3 control-label ">หน้าสำรวจ</label>
				<div class="g24-col-sm-4">
					<input class="form-control guarantee_<?php echo $guarantee_type; ?>" id="survey_page" type="text" name="data[coop_loan_guarantee_real_estate][survey_page]" value="" disabled="true">
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label ">จังหวัด</label>
				<div class="g24-col-sm-5">
					<select name="province_id" id="province_id" class="form-control m-b-1 guarantee_<?php echo $guarantee_type; ?>" onchange="change_province('province_id','amphure','amphur_id','district','district_id')" disabled="true">
						<option value="">เลือกจังหวัด</option>
						<?php foreach($province as $key => $value){ ?>
								<option value="<?php echo $value['province_id']; ?>"<?php echo $value['province_id']==@$data['province_id']?'selected':''; ?>><?php echo $value['province_name']; ?></option>
						<?php }?>
					</select>
				</div>
				<label class="g24-col-sm-3 control-label ">อำเภอ</label>
				<div class="g24-col-sm-4">
					<span id="amphure">
						<select class="form-control m-b-1 guarantee_<?php echo $guarantee_type; ?>" name="amphur_id" name="amphur_id" disabled="true">
							<option value="">เลือกอำเภอ</option>
						</select>
					</span>
				</div>
				<label class="g24-col-sm-3 control-label ">ตำบล</label>
				<div class="g24-col-sm-4">
					<span id="district">
						<select class="form-control m-b-1 guarantee_<?php echo $guarantee_type; ?>" id="district_id" name="district_id" disabled="true">
							<option value="">เลือกตำบล</option>
						</select>
					</span>
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label ">โฉนดที่ดิน</label>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label ">เลขที่</label>
				<div class="g24-col-sm-5">
					<input class="form-control guarantee_<?php echo $guarantee_type; ?>" id="deed_number" type="text" name="data[coop_loan_guarantee_real_estate][deed_number]" value="" disabled="true">
				</div>
				<label class="g24-col-sm-3 control-label ">เล่ม</label>
				<div class="g24-col-sm-4">
					<input class="form-control guarantee_<?php echo $guarantee_type; ?>" id="deed_book" type="text" name="data[coop_loan_guarantee_real_estate][deed_book]" value="" disabled="true">
				</div>
				<label class="g24-col-sm-3 control-label ">หน้า</label>
				<div class="g24-col-sm-4">
					<input class="form-control guarantee_<?php echo $guarantee_type; ?>" id="deed_page" type="text" name="data[coop_loan_guarantee_real_estate][deed_page]" value="" disabled="true">
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label ">จำนวนที่ดิน</label>
				<div class="g24-col-sm-4">
					<input class="form-control guarantee_<?php echo $guarantee_type; ?>" id="rai" type="text" name="data[coop_loan_guarantee_real_estate][rai]" value="" disabled="true">
				</div>
				<label class="g24-col-sm-1" style="text-align:left">ไร่</label>
				<label class="g24-col-sm-3 control-label "></label>
				<div class="g24-col-sm-3">
					<input class="form-control guarantee_<?php echo $guarantee_type; ?>" id="ngan" type="text" name="data[coop_loan_guarantee_real_estate][ngan]" value="" disabled="true">
				</div>
				<label class="g24-col-sm-1" style="text-align:left">งาน</label>
				<label class="g24-col-sm-3 control-label "></label>
				<div class="g24-col-sm-3">
					<input class="form-control guarantee_<?php echo $guarantee_type; ?>" id="tarangwah" type="text" name="data[coop_loan_guarantee_real_estate][tarangwah]" value="" disabled="true">
				</div>
				<label class="g24-col-sm-2" style="text-align:left">ตารางวา</label>
			</div>
		</div>
	</div>
		<h3>ค่าใช้จ่าย</h3>

            <?php if(isset($outgoings) && count($outgoings) >= 1){

                for($row = 0; $row <= ceil(count($outgoings)+count($outgoings)%2); $row+=2 ){ ?>

                    <div class="g24-col-sm-24 modal_data_input">
                        <label class="g24-col-sm-4 control-label " <?php echo $outgoings[$row]['outgoing_show'] <> 1 ? 'style="display: none;"' : ''; ?>><?php echo $outgoings[$row]['outgoing_name']; ?></label>
                        <div class="g24-col-sm-5" <?php echo $outgoings[$row]['outgoing_show'] <> 1 ? 'style="display: none;"' : ''; ?>>
                            <input class="form-control maxlength_number_decimal loan_cost" id="<?php echo $outgoings[$row]['outgoing_code']; ?>" type="text" name="data[coop_loan_cost][<?php echo $outgoings[$row]['outgoing_code']; ?>]" value="" onblur="format_the_number_decimal(this);">
                        </div>
                        <label class="g24-col-sm-4 control-label " <?php echo $outgoings[$row+1]['outgoing_show'] <> 1 ? 'style="display: none;"' : ''; ?>><?php echo $outgoings[$row+1]['outgoing_name']; ?></label>
                        <div class="g24-col-sm-5" <?php echo $outgoings[$row+1]['outgoing_show'] <> 1 ? 'style="display: none;"' : ''; ?>>
                            <input class="form-control maxlength_number_decimal loan_cost" id="<?php echo $outgoings[$row+1]['outgoing_code']; ?>" type="text" name="data[coop_loan_cost][<?php echo $outgoings[$row+1]['outgoing_code']; ?>]" value="" onblur="format_the_number_decimal(this);" >
                        </div>
                    </div>
                    <?php } ?>
            <?php } ?>

		<h3>การจ่ายเงินกู้</h3>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label "></label>

                <?php
                foreach ($transfers as $key => $transfer){
                if($transfer['transfer_type'] == 'CASH' && $transfer['transfer_status'] == '1'){ ?>
				<label class="g24-col-sm-4 control-label left">
					<input type="radio" name="data[coop_loan][transfer_type]" class="transfer_type"  id="transfer_type_0" value="0" onclick="choose_transfer_type()"> เงินสด
				</label>
                <?php }
                if($transfer['transfer_type'] == 'COOP' && $transfer['transfer_status'] == '1'){ ?>
				<label class="g24-col-sm-4 control-label left">
					<input type="radio" name="data[coop_loan][transfer_type]" class="transfer_type"  id="transfer_type_1" value="1" onclick="choose_transfer_type()"> โอนเงินบัญชีสหกรณ์
				</label>
                <?php }
                if($transfer['transfer_type'] == 'BANK' && $transfer['transfer_status'] == '1'){ ?>
				<label class="g24-col-sm-4 control-label left">
						<input type="radio" name="data[coop_loan][transfer_type]" class="transfer_type"  id="transfer_type_2" value="2" onclick="choose_transfer_type()"> โอนเงินบัญชีธนาคาร 
				</label>
                <?php }
                if($transfer['transfer_type'] == 'PROMPTPAY' && $transfer['transfer_status'] == '1'){ ?>
                <label class="g24-col-sm-4 control-label left">
                    <input type="radio" name="data[coop_loan][transfer_type]" class="transfer_type" id="transfer_type_3" value="3" onclick="choose_transfer_type()" > พร้อมเพย์
                </label>
				<?php }
                if($transfer['transfer_type'] == 'CHEQUE' && $transfer['transfer_status'] == '1'){ ?>
                <label class="g24-col-sm-4 control-label left">
                    <input type="radio" name="data[coop_loan][transfer_type]" class="transfer_type" id="transfer_type_4" value="4" onclick="choose_transfer_type()" > เช็ค
                </label>
                <?php }} ?>

			</div>
			<div class="g24-col-sm-24 modal_data_input transfer_bank_id" style="display:none;">
				<label class="g24-col-sm-4 control-label ">บัญชีธนาคาร</label>
				<div class="g24-col-sm-4">	
					<select name="data[coop_loan][transfer_bank_id]" id="transfer_bank_id" class="form-control">
						<option value="">เลือกธนาคาร</option>
						<?php foreach($rs_bank as $key => $value){ ?>
						<option value="<?php echo $value['bank_id']; ?>" <?php echo $value['bank_id']==@$row_member['dividend_bank_id']?'selected':''; ?>><?php echo $value['bank_name']; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="g24-col-sm-4">	
					<input type="text" class="form-control" name="data[coop_loan][transfer_bank_account_id]" id="transfer_bank_account_id" value="<?php echo @$row_member['dividend_acc_num']; ?>">
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input transfer_account_id" style="display:none;">
				<label class="g24-col-sm-4 control-label ">บัญชีสหกรณ์</label>
				<div class="g24-col-sm-4">	
					<select name="data[coop_loan][transfer_account_id]" id="transfer_account_id" class="form-control">
						<option value="">เลือกบัญชี</option>
						<?php foreach($data_account as $key => $value){ ?>
						<option value="<?php echo $value['account_id']; ?>"><?php echo $value['account_id']." : ".$value['account_name']; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
            <div class="g24-col-sm-24 modal_data_input transfer_prompt_pay" style="display: none">
                <label class="g24-col-sm-4">หมายเลขพร้อมเพย์</label>
                <div class="g24-col-sm-4">
                    <input class="form-control" name="data[coop_loan][transfer_prompt_pay]" id="transfer_prompt_pay" value="">
                </div>
            </div>
			<div class="g24-col-sm-offset-4 g24-col-sm-20 modal_data_input transfer_cheque_multi " style="display: none">
				<?php $this->load->view('loan/cheque_dialog'); ?>
			</div>

<!--		<h3>ชำระหนี้สถาบันการเงิน</h3>-->
<!--			--><?php //for($i=0;$i<0;$i++){ ?>
<!--				<div class="g24-col-sm-24 modal_data_input">-->
<!--					<label class="g24-col-sm-4 control-label">ชื่อสถาบันการเงิน</label>-->
<!--					<div class="g24-col-sm-4">-->
<!--						<input class="form-control financial_institutions_name" id_index="--><?php //echo $i; ?><!--" type="text" id="financial_institutions_name_--><?php //echo $i; ?><!--" name="data[coop_loan_financial_institutions][--><?php //echo $i; ?><!--][financial_institutions_name]" value="">-->
<!--					</div>-->
<!--					<label class="g24-col-sm-4 control-label">จำนวนเงิน</label>-->
<!--					<div class="g24-col-sm-4">-->
<!--						<input class="form-control maxlength_number_decimal" type="text" id="financial_institutions_amount_--><?php //echo $i; ?><!--" name="data[coop_loan_financial_institutions][--><?php //echo $i; ?><!--][financial_institutions_amount]" value="" onblur="format_the_number_decimal(this);">-->
<!--					</div>-->
<!--				</div>-->
<!--			--><?php //} ?>
<!--		-->
		<h3>การส่งค่างวด</h3>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label ">ดอกเบี้ยต่อปี</label>
				<div class="g24-col-sm-5">
					<input class="form-control interest_rate" id="interest_per_year" type="text" name="data[coop_loan][interest_per_year]" value="" readonly>
				</div>
				<label class="g24-col-sm-3 control-label ">จำนวนงวด</label>
				<div class="g24-col-sm-4">
					<input class="form-control period_amount" id="period_amount" type="text" name="data[coop_loan][period_amount]" value="" readonly>
				</div>
				<div class="g24-col-sm-3">
					<a class="link-line-none" data-toggle="modal" data-target="#cal_period_normal_loan" id="cal_period_btn" class="fancybox_share fancybox.iframe" href="#">
						<button class="btn btn-info  btn-after-input"><span>คำนวณ</span></button>
					</a>
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-4 control-label ">วันเริ่มชำระค่างวด</label>
				<div class="g24-col-sm-5">
					<input class="form-control date_start_period_label" id="date_start_period_label" type="text" value="" readonly>
					<input class="date_start_period" id="date_start_period" name="data[coop_loan][date_start_period]" type="hidden" value="" readonly>
				</div>
				<label class="g24-col-sm-3 control-label">ยอดเงินที่จะได้รับโดยประมาณ</label>
				<div class="g24-col-sm-4">
					<input class="form-control display" id="estimate-money" type="text" readonly>
				</div>
			</div>
		<div id="type_2"  style="display:none;">		
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-3 control-label ">งวดที่ 1 วันที่</label>
				<div class="input-with-icon g24-col-sm-5">
                    <input id="date_period_1" name="data[coop_loan][date_period_1]" class="form-control  mydate" type="text" data-provide="datepicker" data-date-language="th-th" data-date-today-highlight="true">
                    <span class="icon icon-calendar input-icon m-f-1"></span>
                  </div>
				<label class="g24-col-sm-3 control-label ">จำนวนเงิน</label>
				<div class="g24-col-sm-5">
					<input class="form-control" id="money_period_1" name="data[coop_loan][money_period_1]" type="text" value="">
					<input class="form-control" id="summonth_period_1" name="data[coop_loan][summonth_period_1]" type="text" value="">
				</div>
			</div>
			<div class="g24-col-sm-24 modal_data_input">
				<label class="g24-col-sm-3 control-label ">งวดที่ 2 วันที่</label>
				<div class="input-with-icon g24-col-sm-5">
					<input id="date_period_2" name="data[coop_loan][date_period_2]" class="form-control  mydate" type="text" data-provide="datepicker" data-date-language="th-th" data-date-today-highlight="true">
                    <span class="icon icon-calendar input-icon m-f-1"></span>
				</div>
				<label class="g24-col-sm-3 control-label ">จำนวนเงิน</label>
				<div class="g24-col-sm-5">
					<input class="form-control" id="money_period_2" name="data[coop_loan][money_period_2]" type="text" value="">
					<input class="form-control" id="summonth_period_2" name="data[coop_loan][summonth_period_2]" type="text" value="">
				</div>
			</div>
	</div>
			<input class="form-control" id="last_date_period" type="hidden" value="">
			<div class="center" style="margin-top: 5px;">
				<button class="btn btn-primary" id="submit_button" type="button" onclick="check_submit()">บันทึกคำร้อง</button>&nbsp;&nbsp;&nbsp;
				<button class="btn btn-default" type="button" data-dismiss="modal">ยกเลิก</button>
			</div>

<script>
var blue_print = `<div class="g24-col-sm-24 modal_data_input">
					<label class="g24-col-sm-4 control-label ">ผู้ค้ำลำดับที่ no[] </label>
					<div class="g24-col-sm-4">
						<div class="input-group">
							<input class="form-control guarantee_<?php echo $guarantee_type; ?> guarantee_person_id" guarantee_person_id='no[]' type="text" id="guarantee_person_id_no[]" name="data[coop_loan_guarantee][1][coop_loan_guarantee_person][id][no[]]" value="" readonly>
							<span class="input-group-btn btn_search_member">
								<button type="button" class="btn btn-info guarantee_<?php echo $guarantee_type; ?> btn-search" onclick="search_member_modal('no[]')"><span class="icon icon-search"></span></button>
							</span>	
						</div>
					</div>
					<div class="g24-col-sm-1">
						<span class="btn_delete_member" id="btn_delete_no[]" style="display:none">
							<button type="button" class="btn btn-danger guarantee_<?php echo $guarantee_type; ?> btn-search" onclick="delete_guarantee_person('no[]')"><span class="icon icon-trash"></span></button>
						</span>
					</div>
					<label class="g24-col-sm-3 control-label ">ชื่อ-สกุล </label>
					<div class="g24-col-sm-4">
						<input class="form-control" type="text" id="guarantee_person_name_no[]" value="" readonly>
					</div>
					<label class="g24-col-sm-3 control-label ">สังกัด </label>
					<div class="g24-col-sm-4">
						<input class="form-control" type="text" id="guarantee_person_dep_no[]" value="" readonly>
					</div>
					
				</div>

				<div class="g24-col-sm-24 modal_data_input">
					<label class="g24-col-sm-4 control-label ">ภาระค้ำประกัน </label>
					<div class="g24-col-sm-4">
						<input class="form-control guarantee_person_no[]" type="text" id="guarantee_person_amount_no[]" name="data[coop_loan_guarantee][1][coop_loan_guarantee_person][guarantee_person_amount][no[]]" value="" disabled="true">
					</div>
					<label class="g24-col-sm-4 control-label " style="display:none;">เลขที่สัญญาค้ำประกัน </label>
					<div class="g24-col-sm-4" style="display:none;">
						<input class="form-control guarantee_person_no[]" type="text" id="guarantee_person_contract_number_no[]" name="data[coop_loan_guarantee][1][coop_loan_guarantee_person][guarantee_person_contract_number][no[]]" value="" disabled="true">
					</div>
					<label class="g24-col-sm-4 control-label ">ค้ำแล้ว </label>
					<label class="g24-col-sm-1 control-label" id="count_guarantee_no[]"></label>
					<label class="g24-col-sm-1 control-label ">สัญญา </label>
				</div>`;
</script>

<!-- Modal -->
<div id="modal_select_garantor" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">คลิกเลือกเงื่อนไขผู้ค้ำประกัน</h4>
      </div>
      <div class="modal-body" id="modal_select_garantor_body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>

  </div>
</div>
<!-- End Modal -->
