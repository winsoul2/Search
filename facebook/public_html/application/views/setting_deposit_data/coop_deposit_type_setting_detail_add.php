<div class="layout-content">
    <div class="layout-content-body">
	<style>
		label{
			padding-top:7px;
		}
		.control-label{
			padding-top:7px;
			text-align:right;
		}
		.control-label_2{
			padding-top:7px;
		}
		.center{
			text-align:center;
		}
		.tab_1{
			margin-left: 40px;
		}
		.tab_2{
			margin-left: 60px;
		}
		.col-small{
			display: -webkit-inline-box;
		}
		.col-small label,input, select {
			margin-right: 10px;
		}
		.col-small-input {
			width: 100px;
		}
		.col-small-input-2 {
			width: 50px;
		}
		
		.percent_fee_w{
			margin-left: -25px;
		}
		
		@media (max-width: 768px) {
			.percent_fee_w{
				margin-left: 68px;
			}		
		}
				
		
	</style>
		<h1 style="margin-bottom: 0">ประเภทเงินฝาก</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>	
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
				<form id='form1' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_deposit_data/coop_deposit_type_setting_detail_save'); ?>" method="post">
				<?php if(@$_GET['act'] == "copy"){?>
				<input type="hidden" name="type_detail_id" value="">
				<?php }else{ ?>
                <input type="hidden" name="type_detail_id" value="<?php echo @$row_detail['type_detail_id']; ?>">
                <?php } ?>
				<input type="hidden" class="form-control" name="type_id" id="type_id" value="<?php echo @$row['type_id']; ?>">
					<div class="row m-b-1">
						<label class="control-label g24-col-sm-6">รหัส</label>
						<div class="g24-col-sm-6"><input type="text" class="form-control" name="type_code" id="type_code" value="<?php echo @$row['type_code']; ?>" readonly></div>					
					</div>
					<div class="row m-b-1">
						<label class="control-label g24-col-sm-6">ประเภทเงินฝาก</label>
						<div class="g24-col-sm-6"><input type="text" class="form-control" name="type_name" id="type_name" value="<?php echo @$row['type_name']; ?>" readonly></div>
					</div>
					<div class="row">
						<label class="control-label g24-col-sm-6">มีผลวันที่</label>
						<div class="g24-col-sm-3">
							<input id="start_date" name="start_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(empty($row_detail) ? date("Y-m-d") : @$row_detail['start_date']); ?>" data-date-language="th-th" required title="กรุณากรอกวันที่มีผล">
							<span class="icon icon-calendar input-icon m-f-1"></span>
						</div>
                        <label class="control-label g24-col-sm-1">ถึงวันที่</label>
						<div class="g24-col-sm-3">
							<input id="end_date" name="end_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo empty($row_detail['end_date']) ? "" : $this->center_function->mydate2date($row_detail['end_date']); ?>" data-date-language="th-th">
							<span class="icon icon-calendar input-icon m-f-1"></span>
						</div>
					</div>
					<div class="row">
						<label class="control-label g24-col-sm-6">อายุบัญชี</label>
						<div class="g24-col-sm-2"><input type="number" class="form-control m-b-1" name="max_month" id="max_month" value="<?php echo @$row_detail['max_month']; ?>"></div>
						<label class="control-label_2 g24-col-sm-6">เดือน</label>
					</div>
                    
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 col-small">								
							<input type="checkbox" id="is_open_min" name="is_open_min" value="1"  onchange="change_staus_open_min()" <?php echo @$row_detail['is_open_min']=='1'?'checked':''; ?>>
							<label class="control-label_2">เปิดบัญชีขั้นต่ำ</label>
							<input type="number" class="form-control col-small-input m-b-1" name="open_min" id="open_min" value="<?php echo @$row_detail['open_min']; ?>">
							<label class="control-label_2">บาท</label>
						</div>					
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 col-small">								
							<input type="checkbox" id="is_balance_min" name="is_balance_min" value="1"  onchange="change_staus_balance_min()" <?php echo @$row_detail['is_balance_min']=='1'?'checked':''; ?>>
							<label class="control-label_2">เงินเหลือในบัญชีไม่ต่ำกว่า</label>
							<input type="number" class="form-control col-small-input m-b-1" name="balance_min" id="balance_min" value="<?php echo @$row_detail['balance_min']; ?>">
							<label class="control-label_2">บาท</label>
						</div>					
					</div>

                    <div class="row">
						<label class="control-label g24-col-sm-6">เมื่อครบกำหนดฝาก</label>
                        <label class="g24-col-sm-6"><input type="radio" id="condition_age_0" name="condition_age" value="0" <?php echo @$row_detail['condition_age']==0?'checked':''; ?>> ไม่มี</label>
					</div>
                    <div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-6"><input type="radio" id="condition_age_1" name="condition_age" value="1" <?php echo @$row_detail['condition_age']==1?'checked':''; ?>> ถอนและปิดบัญชีทันที (เป็นยอดๆ) และแจ้งเตือน</label>
					</div>
                    <div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-6"><input type="radio" id="condition_age_2" name="condition_age" value="2" <?php echo @$row_detail['condition_age']==2?'checked':''; ?>> ถอนและปิดบัญชีทันที (ทั้งเล่ม) และแจ้งเตือน</label>
					</div>
                    <div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-6"><input type="radio" id="condition_age_2" name="condition_age" value="3" <?php echo @$row_detail['condition_age']==3?'checked':''; ?>> โอนเข้าบัญชีอื่นๆ  (เลือกบัญชีจากส่วนจัดการข้อมูลสมาชิก)</label>
					</div>
                    
					<div class="row">
						<label class="control-label g24-col-sm-6">เงื่อนไข</label>
						<label class="control-label g24-col-sm-6 text-left">อัตราดอกเบี้ย</label>
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-2"><input type="radio" id="condition_interest_1" name="condition_interest" value="1" onchange="change_interest_radio()" <?php echo (@$row_detail['condition_interest']=='1' || empty($row_detail))?'checked':''; ?>> คงที่</label>
						<div id="show_condition_interest_1" style="display:none;" class="g24-col-sm-16">
							<div class="g24-col-sm-16 tab_1">	
								<label class="control-label g24-col-sm-3 g24-col-xs-5">ดอกเบี้ย</label>
								<div class="g24-col-sm-4 g24-col-xs-5">
									<input type="text" class="form-control" name="stable[0][percent_interest]" value="<?php echo @$row_interest[1][0]['percent_interest']; ?>">
								</div>
								<label class="control-label_2 g24-col-sm-6">%</label>
							</div>
						</div>
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class=" g24-col-sm-6"><input type="radio" id="condition_interest_2" name="condition_interest" value="2" onchange="change_interest_radio()" <?php echo @$row_detail['condition_interest']=='2'?'checked':''; ?>> ขั้นบันได ตามเดือน</label>							
					</div>
					<div id="show_condition_interest_2" style="display:none;">
						<?php for($i=0;$i<=3;$i++){ ?>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="g24-col-sm-6 g24-col-xs-1"></label>
								<label class="control-label g24-col-sm-2  g24-col-xs-5">เดือนที่ </label>
								<div class="g24-col-sm-6  g24-col-xs-5">
									<input type="number" class="form-control" name="staircase_month[<?php echo $i; ?>][num_month]" value="<?php echo @$row_interest[2][$i]['num_month']; ?>">
								</div>
								<label class="control-label g24-col-sm-2  g24-col-xs-5">ดอกเบี้ย</label>
								<div class="g24-col-sm-4  g24-col-xs-5">
									<input type="number" class="form-control" name="staircase_month[<?php echo $i; ?>][percent_interest]" value="<?php echo @$row_interest[2][$i]['percent_interest']; ?>">
								</div>
								<label class="control-label_2 g24-col-sm-2  g24-col-xs-1">%</label>
							</div>
						</div>
						<?php } ?>
					</div>	
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class=" g24-col-sm-6"><input type="radio" id="condition_interest_3" name="condition_interest" value="3" onchange="change_interest_radio()" <?php echo @$row_detail['condition_interest']=='3'?'checked':''; ?>> ขั้นบันได ตามจำนวนเงิน</label>							
					</div>
					<div id="show_condition_interest_3" style="display:none;">
						<?php for($i=0;$i<=3;$i++){ ?>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="g24-col-sm-6  g24-col-xs-1"></label>
								<label class="control-label g24-col-sm-2  g24-col-xs-5">ฝาก </label>
								<div class="g24-col-sm-6  g24-col-xs-5">
									<input type="number" class="form-control" name="staircase_money[<?php echo $i; ?>][amount_deposit]" value="<?php echo @$row_interest[3][$i]['amount_deposit']; ?>">
								</div>
								<label class="control-label g24-col-sm-2  g24-col-xs-5">ดอกเบี้ย</label>
								<div class="g24-col-sm-4  g24-col-xs-5">
									<input type="number" class="form-control" name="staircase_money[<?php echo $i; ?>][percent_interest]" value="<?php echo @$row_interest[3][$i]['percent_interest']; ?>">
								</div>
								<label class="control-label_2 g24-col-sm-2  g24-col-xs-2">%</label>
							</div>
						</div>
						<?php } ?>
						
						<div class="row">
							<label class="g24-col-sm-6  g24-col-xs-1"></label>
							<label class="control-label g24-col-sm-2  g24-col-xs-5">วิธีคำนวณ</label>
							<div class="g24-col-sm-6  g24-col-xs-5">
								<label><input type="radio" name="sub_condition_interest" value="0" <?php echo @$row_detail['sub_condition_interest']==0?'checked':''; ?>> ตามยอดเงิน</label>
								<label><input type="radio" name="sub_condition_interest" value="1" <?php echo @$row_detail['sub_condition_interest']==1?'checked':''; ?>> ตามช่วงเงิน</label>
							</div>
						</div>
					</div>	

					<div class="row">
						<label class="control-label g24-col-sm-6"></label>
						<label class="control-label g24-col-sm-6 text-left">จำนวนวันหาร</label>
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-4 tab_1"><input type="radio" id="days_in_year_1" name="days_in_year" value="1" <?php echo (@$row_detail['days_in_year']=='1' || empty($row_detail))?'checked':''; ?> checked> คงที่ 365 วัน</label>
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-4 tab_1"><input type="radio" id="days_in_year_2" name="days_in_year" value="2" <?php echo (@$row_detail['days_in_year']=='2' || empty($row_detail))?'checked':''; ?>> คงที่ 366 วัน</label>
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-4 tab_1"><input type="radio" id="days_in_year_3" name="days_in_year" value="3" <?php echo (@$row_detail['days_in_year']=='3' || empty($row_detail))?'checked':''; ?>> ตามปฏิทิน</label>
					</div>
					
					<div class="row">
						<label class="control-label g24-col-sm-6"></label>
						<label class="control-label g24-col-sm-6 text-left">การจ่ายดอกเบี้ย</label>
					</div>					
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-4 tab_1"><input type="radio" id="pay_interest_1" name="pay_interest" value="1" onchange="change_pay_radio()" <?php echo (@$row_detail['pay_interest']=='1' || empty($row_detail))?'checked':''; ?>> จ่ายทุกสิ้นเดือน</label>
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-6 tab_1"><input type="radio" id="pay_interest_2" name="pay_interest" value="2" onchange="change_pay_radio()" <?php echo @$row_detail['pay_interest']=='2'?'checked':''; ?>> จ่ายทุกเดือน ตามวันที่ครบกำหนด</label>
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-2 tab_1" style="white-space: nowrap;"><input type="radio" id="pay_interest_3" name="pay_interest" value="3" onchange="change_pay_radio()" <?php echo @$row_detail['pay_interest']=='3'?'checked':''; ?>> จ่าย 2 ครั้ง ต่อปี</label>
						<div class="g24-col-sm-13" style="padding-top: 2px;">
							<label class="g24-col-sm-1  g24-col-xs-5" style="white-space: nowrap;"></label>
							<label class="g24-col-sm-2  g24-col-xs-5" style="white-space: nowrap;">ทุกวันที่</label>
							<div class="g24-col-sm-5  g24-col-xs-10">
								<div class="form-group">
									<input id="pay_date1" name="pay_date1" class="form-control m-b-1" style="padding-left: 50px;width: 150px;" type="text" value="<?php echo (empty($row_detail['pay_date1']) || @$row_detail['pay_date1'] == '0000-00-00 00:00:00')?'':$this->center_function->mydate2date(@$row_detail['pay_date1']); ?>" data-date-language="th-th"  required title="กรุณาเลือกวันที่ครั้งที่1">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
							<label class="g24-col-sm-1  g24-col-xs-5" style="white-space: nowrap;"></label>
							<label class="g24-col-sm-2  g24-col-xs-5" style="white-space: nowrap;">และวันที่</label>
							<div class="g24-col-sm-5  g24-col-xs-10">
								<div class="form-group">
									<input id="pay_date2" name="pay_date2" class="form-control m-b-1" style="padding-left: 50px;width: 150px;" type="text" value="<?php echo (empty($row_detail['pay_date2']) || @$row_detail['pay_date1'] == '0000-00-00 00:00:00')?'':$this->center_function->mydate2date(@$row_detail['pay_date2']); ?>" data-date-language="th-th"  required title="กรุณาเลือกวันที่ครั้งที่2">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>	
						</div>	
					</div>						
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">							
							<input type="radio" id="pay_interest_4" name="pay_interest" value="4" onchange="change_pay_radio()" <?php echo @$row_detail['pay_interest']=='4'?'checked':''; ?>>
							<label class="control-label_2"> จ่ายเมื่อครบกำหนด</label>
							<input type="number" class="form-control col-small-input" name="num_month_maturity" id="num_month_maturity" value="<?php echo @$row_detail['num_month_maturity']; ?>" maxlength="4">
							<label class="control-label_2">เดือน (เงินฝากประจำ)</label>
							<label class="control-label_2"> และหลังจากฝากเงินครั้งล่าสุดครบ</label>
							<input type="number" class="form-control col-small-input" name="ext_num_month_maturity_day" id="ext_num_month_maturity_day" value="<?php echo @$row_detail['ext_num_month_maturity_day']; ?>" maxlength="4">
							<label class="control-label_2">วัน</label>
						</div>
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">
							<input type="radio" id="pay_interest_5" name="pay_interest" value="5" onchange="change_pay_radio()" <?php echo @$row_detail['pay_interest']=='5'?'checked':''; ?>>
							<label class="control-label_2"> จ่ายเมื่อครบกำหนด</label>
							<input type="number" class="form-control col-small-input" name="num_month_maturity_normal" id="num_month_maturity_normal" value="<?php echo @$row_detail['num_month_maturity_normal']; ?>" maxlength="4">
							<label class="control-label_2">เดือน (เงินฝากทั่วไป)</label>
						</div>
					</div>
					 <div class="row">
                        <label class="g24-col-sm-6"></label>
                        <label class="g24-col-sm-4 tab_1"><input type="radio" id="pay_interest_6" name="pay_interest" value="6" onchange="change_pay_radio()" <?php echo (@$row_detail['pay_interest']=='6' || empty($row_detail))?'checked':''; ?>> จ่ายทุกสิ้นปี</label>
                    </div>
					<div class="row">
						<label class="control-label g24-col-sm-6"></label>
						<label class="control-label g24-col-sm-6 text-left">คิดดอกเบี้ย</label>
					</div>					
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-4 tab_1"><input type="radio" id="type_interest_1" name="type_interest" value="1" onchange="change_type_interest()" <?php echo (@$row_detail['type_interest']=='1' || empty($row_detail))?'checked':''; ?>> ดอกเบี้ยทบต้น</label>
					</div>					
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-6 tab_1"><input type="radio" id="type_interest_2" name="type_interest" value="2" onchange="change_type_interest()" <?php echo @$row_detail['type_interest']=='2'?'checked':''; ?>> ดอกเบี้ยเฉพาะเงินต้น</label>
					</div>
                    <div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-6 tab_1"><input type="radio" id="type_interest_4" name="type_interest" value="4" onchange="change_type_interest()" <?php echo @$row_detail['type_interest']=='4'?'checked':''; ?>> ดอกเบี้ยเฉพาะเงินต้น (ถอนดอกเบี้ยออกไปบัญชีหลัก)</label>
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">								
							<!--<input type="checkbox" id="staus_interest" name="staus_interest" value="1"  onchange="change_staus_interest()" <?php echo @$row_detail['staus_interest']=='1'?'checked':''; ?>>-->
							<input type="radio" id="type_interest_3" name="type_interest" value="3" onchange="change_type_interest()" <?php echo @$row_detail['type_interest']=='3'?'checked':''; ?>>
							<label class="control-label_2">&nbsp;&nbsp;ฝากไม่ถึง</label>
							<input type="number" class="form-control col-small-input" name="num_month_no_interest" id="num_month_no_interest" value="<?php echo @$row_detail['num_month_no_interest']; ?>" maxlength="4">
							<label class="control-label_2">เดือน ไม่คิดดอกเบี้ย</label>
						</div>					
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">								
							<input type="checkbox" id="is_day_cal_interest" name="is_day_cal_interest" value="1" <?php echo @$row_detail['is_day_cal_interest']=='1'?'checked':''; ?>>
							<label class="control-label_2">&nbsp;&nbsp;ปิดบัญชีเงินฝากจะคิดดอกเบี้ยก่อนวันถอน 1 วัน</label>
						</div>					
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">
							<input type="checkbox" id="is_non_pay_interest_after_withdraw" name="is_non_pay_interest_after_withdraw" value="1" <?php echo @$row_detail['is_non_pay_interest_after_withdraw']=='1'?'checked':''; ?>>
							<label class="control-label_2">&nbsp;&nbsp;ไม่คิดดอกเบี้ยในวันที่ทำที่มีรายการถอน</label>
						</div>
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">								
							<input type="checkbox" id="is_not_holiday" name="is_not_holiday" value="1" <?php echo @$row_detail['is_not_holiday']=='1'?'checked':''; ?>>
							<label class="control-label_2">&nbsp;&nbsp;คำนวณดอกเบี้ยโดยไม่ใช้วันหยุดสหกรณ์</label>
						</div>
					</div>
					<div class="row">
						<label class="control-label g24-col-sm-6"></label>
						<label class="control-label g24-col-sm-6 text-left">ภาษี</label>
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">
							<input type="checkbox" id="is_tax" name="is_tax" value="1"  onchange="change_is_tax()" <?php echo @$row_detail['is_tax']=='1'?'checked':''; ?>>
							<label class="control-label_2">เสียภาษีอัตรา</label>
							<input type="number" class="form-control col-small-input m-b-1" name="tax_rate" id="tax_rate" value="<?php echo @$row_detail['tax_rate']; ?>">
							<label class="control-label_2">% ของดอกเบี้ย</label>
						</div>					
					</div>
					<div class="row">
						<label class="control-label g24-col-sm-6"></label>
						<label class="control-label g24-col-sm-6 text-left">การฝากเงิน</label>
					</div>					
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">	
							<div class="g24-col-sm-8" style="display:  -webkit-inline-box;margin-left: -8px;">
								<label class="control-label_2" style="white-space: nowrap;">เงินต้นขั้นต่ำ</label>
								<div class="">
									<div class="form-group">
										<input type="number" class="form-control col-small-input" name="amount_min" value="<?php echo @$row_detail['amount_min']; ?>" title="กรุณากรอกเงินต้นขั้นต่ำ" maxlength="10">
									</div>
								</div>
								<label class="control-label_2">บาท</label>
							</div>
							<div class="g24-col-sm-8" style="display:  -webkit-inline-box;">	
								<label class="control-label_2" style="white-space: nowrap;">สูงสุดไม่เกิน</label>
								<div class="">
									<div class="form-group">
										<input type="number" class="form-control col-small-input" name="amount_max_time" value="<?php echo @$row_detail['amount_max_time']; ?>" title="กรุณากรอกจำนวนเงินสูงสุดต่อครั้ง" maxlength="10">
									</div>
								</div>
								<label class="control-label_2">บาท ต่อครั้ง</label>		
							</div>
						</div>	
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">	
							<div class="g24-col-sm-8" style="display:  -webkit-inline-box;margin-left: -8px;">
								<label class="control-label_2" style="white-space: nowrap;">ฝากรวมทั้งหมดต้องไม่เกิน</label>
								<div class="">
									<div class="form-group">
										<input type="number" class="form-control col-small-input" name="amount_max" value="<?php echo @$row_detail['amount_max']; ?>" title="กรุณากรอกเงินต้นสูงสุด" maxlength="10">
									</div>
								</div>
								<label class="control-label_2">บาท</label>
							</div>
						</div>	
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">								
							<input type="checkbox" id="staus_loan_deduct" name="staus_loan_deduct" value="1"  <?php echo @$row_detail['staus_loan_deduct']=='1'?'checked':''; ?>>
							<label class="control-label_2">หักจากเงินกู้</label>
						</div>					
					</div>	
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small m-b-1">	
							<input type="checkbox" id="is_deposit_num" name="is_deposit_num" value="1"  onchange="change_is_deposit_num()" <?php echo @$row_detail['is_deposit_num']=='1'?'checked':''; ?>>
							<label class="control-label_2">ฝากเงินได้ไม่เกิน</label>
							<select id="deposit_num_type" name="deposit_num_type" class="form-control col-small-input">
								<option value="0"<?php if($row_detail['deposit_num_type'] == 0) { ?> selected="selected"<?php } ?>>เดือน</option>
								<option value="1"<?php if($row_detail['deposit_num_type'] == 1) { ?> selected="selected"<?php } ?>>ปี</option>
							</select>
							<label class="control-label_2">ละ</label>
							<input type="number" class="form-control col-small-input" name="deposit_num" id="deposit_num" value="<?php echo @$row_detail['deposit_num']; ?>" maxlength="4">
							<label class="control-label_2">ครั้ง </label>
						</div>					
					</div>
					<div class="row">
						<label class="control-label g24-col-sm-6"></label>
						<label class="control-label g24-col-sm-6 text-left">การถอนเงิน</label>
					</div>					
					<div class="row">
						<label class="g24-col-sm-6 g24-col-xs-1"></label>
						<label class="g24-col-sm-6 tab_1"><input type="radio" id="type_fee_1" name="type_fee" value="1" onchange="change_fee_radio()" <?php echo (@$row_detail['type_fee']=='1' || empty($row_detail))?'checked':''; ?>> ไม่มีค่าธรรมเนียมการถอน</label>
					</div>				
					<div class="row">
						<label class="g24-col-sm-6  g24-col-xs-1"></label>
						<label class="control-label_2 g24-col-sm-4 tab_1" style="white-space: nowrap;"><input type="radio" id="type_fee_2" name="type_fee" value="2" onchange="change_fee_radio()" <?php echo @$row_detail['type_fee']=='2'?'checked':''; ?>> มีค่าธรรมเนียมการถอน</label>
						<div class="g24-col-sm-2  g24-col-xs-5 percent_fee_w">
							<div class="form-group">
								<input type="number" class="form-control" name="percent_fee" id="percent_fee" value="<?php echo @$row_detail['percent_fee']; ?>" required title="กรุณากรอก % ของยอดเงินที่ถอน" maxlength="5">
							</div>
						</div>
						<label class="control-label_2 g24-col-sm-10">% ของยอดเงินที่ถอน</label>			
					</div>				
					<div class="row">
						<label class="g24-col-sm-6 g24-col-xs-1"></label>
						<label class="g24-col-sm-4 tab_1"><input type="radio" id="type_fee_3" name="type_fee" value="3" onchange="change_fee_radio()" <?php echo @$row_detail['type_fee']=='3'?'checked':''; ?>> มีค่าธรรมเนียมการถอน</label>
					</div>	
					
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="control-label g24-col-sm-1 g24-col-xs-5"></label>
						<label class="control-label_2 g24-col-sm-2 g24-col-xs-6" style="white-space: nowrap;">เมื่อถอนก่อน </label>
						<div class="g24-col-sm-2 g24-col-xs-5" style="margin-left: -8px;">
							<div class="form-group">
								<input type="number" class="form-control" name="num_month_before" id="num_month_before" value="<?php echo @$row_detail['num_month_before']; ?>" title="กรุณากรอกจำนวนที่ถอนก่อน" maxlength="4">
							</div>
						</div>
						<label class="control-label_2 g24-col-sm-1 g24-col-xs-6">เดือน  </label>
						<label class="control-label_2 g24-col-sm-3 g24-col-xs-11 text-right">ผู้ฝากได้รับดอกเบี้ย</label>
						<div class=" g24-col-sm-2 g24-col-xs-10">
							<div class="form-group">
								<input type="number" class="form-control" name="percent_depositor" id="percent_depositor" value="<?php echo @$row_detail['percent_depositor']; ?>" title="กรุณากรอกดอกเบี้ยที่ผู้ฝากได้รับ" maxlength="5"> 
							</div>	
						</div>	
						<label class="control-label_2 g24-col-sm-1 g24-col-xs-1">% </label>
						<label class="control-label_2 g24-col-sm-3 g24-col-xs-24 text-right" style="margin-left: -20px;text-align: right;white-space: nowrap;">ที่เหลือสหกรณ์ได้รับดอกเบี้ย</label>											
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="control-label g24-col-sm-1 g24-col-xs-5"></label>
						<div class="g24-col-sm-10 col-small">
							<label class="control-label_2">เสียภาษีอัตรา</label>
							<input type="number" class="form-control col-small-input m-b-1" name="before_due_tax_rate" id="before_due_tax_rate" value="<?php echo @$row_detail['before_due_tax_rate']; ?>">
							<label class="control-label_2">% ของดอกเบี้ย</label>
						</div>
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">								
							<input type="checkbox" id="staus_close_principal" name="staus_close_principal" value="1"  <?php echo @$row_detail['staus_close_principal']=='1'?'checked':''; ?>>
							<label class="control-label_2">ไม่สามารถถอนได้จนกว่าจะปิดเงินต้น</label>
						</div>					
					</div>	
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small m-b-1">	
							<input type="checkbox" id="staus_withdraw" name="staus_withdraw" value="1"  onchange="change_staus_withdraw()" <?php echo @$row_detail['staus_withdraw']=='1'?'checked':''; ?>>
							<label class="control-label_2">ถอนได้</label>
							<select id="withdraw_num_unit" name="withdraw_num_unit" class="form-control col-small-input">
								<option value="0"<?php if($row_detail['withdraw_num_unit'] == 0) { ?> selected="selected"<?php } ?>>เดือน</option>
								<option value="1"<?php if($row_detail['withdraw_num_unit'] == 1) { ?> selected="selected"<?php } ?>>ปี</option>
							</select>
							<label class="control-label_2">ละ</label>
							<input type="number" class="form-control col-small-input" name="withdraw_num" id="withdraw_num" value="<?php echo @$row_detail['withdraw_num']; ?>" maxlength="4">
							<label class="control-label_2">ครั้ง   ครั้งที่</label>
							<input type="number" class="form-control col-small-input" name="withdraw_num_interest" id="withdraw_num_interest" value="<?php echo @$row_detail['withdraw_num_interest']; ?>" maxlength="4">
							<label class="control-label_2">เป็นต้นไปคิด</label>
							<input type="number" class="form-control col-small-input" name="withdraw_percent_interest" id="withdraw_percent_interest" value="<?php echo @$row_detail['withdraw_percent_interest']; ?>" maxlength="5">
							<label class="control-label_2">% จากยอดที่ถอน  แต่ต้องไม่ต่ำกว่า</label>
							<input type="number" class="form-control col-small-input" name="withdraw_percent_min" id="withdraw_percent_min" value="<?php echo @$row_detail['withdraw_percent_min']; ?>">
							<label class="control-label_2">บาท</label>
						</div>					
					</div>	
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">								
							<input type="checkbox" id="staus_maturity" name="staus_maturity" value="1"  onchange="change_staus_maturity()" <?php echo @$row_detail['staus_maturity']=='1'?'checked':''; ?>>
							<label class="control-label_2">ต้องฝากครบ</label>
							<input type="number" class="form-control col-small-input m-b-1" name="maturity_num_year" id="maturity_num_year" value="<?php echo @$row_detail['maturity_num_year']; ?>" maxlength="4">
							<label class="control-label_2">ปี ถ้าไม่ครบปีไม่ได้ ต้องคืนเงิน</label>
						</div>					
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">								
							<input type="checkbox" id="is_withdraw_min" name="is_withdraw_min" value="1"  onchange="change_staus_withdraw_min()" <?php echo @$row_detail['is_withdraw_min']=='1'?'checked':''; ?>>
							<label class="control-label_2">ถอนขั้นต่ำ</label>
							<input type="number" class="form-control col-small-input m-b-1" name="withdraw_min" id="withdraw_min" value="<?php echo @$row_detail['withdraw_min']; ?>">
							<label class="control-label_2">บาท</label>
						</div>					
					</div>
					<!--ปิดไว้ก่อน ถ้าจะใช้ต้องไปปรับที่ระบบการคำนวณดอกเบี้ยอัตโนมัติ-->
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">
							<input type="checkbox" id="is_withdrawal_specify" name="is_withdrawal_specify" value="1" <?php echo @$row_detail['is_withdrawal_specify']=='1'?'checked':''; ?>>
							<label class="control-label_2">ถอนเงินแบบระบุยอดถอนเงินตามยอดฝาก</label>
						</div>
					</div>
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<div class="g24-col-sm-16 tab_1 col-small">
							<input type="checkbox" id="allow_interest_withdrawal_bf_due" name="allow_interest_withdrawal_bf_due" value="1" <?php echo @$row_detail['allow_interest_withdrawal_bf_due']=='1'?'checked':''; ?>>
							<label class="control-label_2">ถอนดอกเบี้ยได้ก่อนครบกำหนด</label>
						</div>
					</div>
					<div class="row">
						<label class="control-label g24-col-sm-6"></label>
						<label class="control-label g24-col-sm-6 text-left">การรับเงิน</label>
					</div>					
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-4 tab_1"><input type="radio" id="type_receive_1" name="type_receive" value="1" <?php echo (@$row_detail['type_receive']=='1' || empty($row_detail))?'checked':''; ?>> เข้าบัญชีสหกรณ์</label>
					</div>					
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-6 tab_1"><input type="radio" id="type_receive_2" name="type_receive" value="2" <?php echo @$row_detail['type_receive']=='2'?'checked':''; ?>> เงินสด/โอนเข้าบัญชีธนาคาร</label>
					</div>			
					<div class="row">
						<label class="control-label g24-col-sm-6"></label>
						<label class="control-label g24-col-sm-6 text-left">การทำรายการ</label>
					</div>		
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-6 tab_1"><input type="radio" id="permission_type_1" name="permission_type" value="1" <?php echo @$row_detail['permission_type']=='1'?'checked':''; ?>> ฝาก/ถอนเงินปกติ </label>
					</div>		
					<div class="row">
						<label class="g24-col-sm-6"></label>
						<label class="g24-col-sm-6 tab_1"><input type="radio" id="permission_type_2" name="permission_type" value="2" <?php echo @$row_detail['permission_type']=='2'?'checked':''; ?>> ไม่สามารถถอนเงินได้ (ต้องปิดบัญชีเท่านั้น)</label>
					</div>
                    <div class="row">
                        <label class="g24-col-sm-6"></label>
                        <div class="g24-col-sm-16 tab_1 col-small">
                            <input type="radio" id="permission_type_3" name="permission_type" value="3" <?php echo @$row_detail['permission_type']=='3'?'checked':''; ?>>
                            <label>ถอนเงินได้เมื่อครบกำหนด</label>
                            <input type="number" class="form-control col-small-input" name="hold_withdraw_month" id="hold_withdraw_month" value="<?php echo @$row_detail['hold_withdraw_month']; ?>" maxlength="2" <?php echo @$row_detail['permission_type']=='3' ? "" : "disabled=\"disabled\""; ?>>
                            <label class="control-label_2">เดือน</label>
                        </div>

                    </div>
					<div class="row">&nbsp;</div>
					<div class="row m-b-1">
						<div class="form-group center">
							<button type="button" class="btn btn-primary" style="width:100px" onclick="submit_form()"> ยืนยัน </button>
							<button type="button" class="btn btn-danger" style="width:100px" onclick="go_back('<?php echo @$row['type_id']; ?>')"> ยกเลิก </button>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div> 
<?php
$v = date('YmdHis');
$link = array(
    'src' => 'assets/js/coop_deposit_type_setting_detail_add.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
