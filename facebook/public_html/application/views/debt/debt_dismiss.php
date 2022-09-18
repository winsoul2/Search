<div class="layout-content">
    <div class="layout-content-body">
    <style type="text/css">
        .form-group{
            margin-bottom: 5px;
        }
        .modal-backdrop.in{
            opacity: 0;
        }
        .modal-backdrop {
            position: relative;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            background-color: #000;
        }
        label {
            padding-top: 6px;
            text-align: right;
        }
        .modal-content {
            margin:auto;
            margin-top:7%;
        }
    </style>

    <h1 style="margin-bottom: 0">ให้ออก</h1>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
            <?php $this->load->view('breadcrumb'); ?>
        </div>
    </div>
    <div class="panel panel-body col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-top:0px;margin-top:0px;">
        <?php $this->load->view('search_member_new'); ?>
		<div class="g24-col-sm-24">
			<div class="form-group g24-col-sm-8">
				<label class="g24-col-sm-10 control-label" for="form-control-2">หุ้น</label>
				<div class="g24-col-sm-14" >
					<input id="position_name"  class="form-control " type="text" value="<?php echo number_format(@$cal_share,2); ?>"  readonly>
				</div>
			</div>
			<div class="form-group g24-col-sm-8">
				<label class="g24-col-sm-10 control-label" for="form-control-2">เงินฝากรวม</label>
				<div class="g24-col-sm-14" >
					<input id="mem_type_id"  class="form-control " type="text" value="<?php echo number_format(@$cal_account,2); ?>"  readonly>
				</div>
			</div>
			<div class="form-group g24-col-sm-8">
				<label class="g24-col-sm-10 control-label" for="form-control-2">รายได้รวม</label>
				<div class="g24-col-sm-14" >
					<input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$total_income,2); ?>"  readonly>
				</div>
			</div>
		</div>
		<div class="g24-col-sm-24">							
			<div class="form-group g24-col-sm-8">
				<label class="g24-col-sm-10 control-label" for="form-control-2">เงินต้นรวม</label>
				<div class="g24-col-sm-14" >
					<input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$loan_principal,2); ?>"  readonly>
				</div>
			</div>
			<div class="form-group g24-col-sm-8">
				<label class="g24-col-sm-10 control-label" for="form-control-2">ดอกเบี้ยรวม</label>
				<div class="g24-col-sm-14" >
					<input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$loan_interest,2); ?>"  readonly>
				</div>
			</div>
			<div class="form-group g24-col-sm-8">
				<label class="g24-col-sm-10 control-label" for="form-control-2">รายจ่ายรวม</label>
				<div class="g24-col-sm-14" >					
					<input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$total_pay,2); ?>"  readonly>
				</div>
			</div>
		</div>
        <div class="g24-col-sm-24">
			<div class="form-group g24-col-sm-8">
				<label class="g24-col-sm-10 control-label" for="form-control-2">ดอกเบี้ยคงค้าง</label>
				<div class="g24-col-sm-14" >
					<input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format($loan_interest_remain_total,2); ?>"  readonly>
				</div>
			</div>
		</div>
		<div class="g24-col-sm-24">
			<div class="form-group g24-col-sm-8">
				<label class="g24-col-sm-10 control-label" for="form-control-2">ภาระค้ำประกัน</label>
				<div class="g24-col-sm-14" >
					<input id="count_contract"  class="form-control " type="text" value="<?php echo number_format(@$count_contract).' สัญญา'; ?>"  readonly>
				</div>
			</div>
			<div class="form-group g24-col-sm-8">
				<label class="g24-col-sm-10 control-label" for="form-control-2">ยอดค้ำประกันรวม</label>
				<div class="g24-col-sm-14" >
					<input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$sum_guarantee_balance,2); ?>"  readonly>
					<input class="form-control " type="hidden" id="sum_guarantee_balance" name="sum_guarantee_balance" value="<?php echo @$sum_guarantee_balance; ?>"  readonly>
				</div>
			</div>
			<div class="form-group g24-col-sm-8">
				<label class="g24-col-sm-10 control-label" for="form-control-2">สถานะ</label>
				<div class="g24-col-sm-14" >
					<textarea class="form-control" <?php echo @$style_status;?> disabled><?php echo @$status_resignation; ?></textarea>
				</div>
			</div>
		</div>
		<div class="g24-col-sm-24">
			<div class="form-group g24-col-sm-8">
				<label class="g24-col-sm-10 control-label" for="form-control-2"><?php echo $text_amount_all;?></label>
				<div class="g24-col-sm-14" >
					<input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$total_amount_all,2); ?>"  readonly>
					<input class="form-control " type="hidden" id="total_amount_all" name="total_amount_all" value="<?php echo @$total_amount_all; ?>"  readonly>
				</div>
			</div>
            <div class="form-group g24-col-sm-8">
				<div class="g24-col-sm-12" >
                    <?php
                        if($row_member['mem_type']=='2' || $row_member['mem_type']=='5') {
                    ?>
                    <a href="report_member_data/coop_report_member_people_retire_preview?member_id=<?php echo $member_id; ?>" target="_blank"><button class="btn btn-primary" style="width: 100%;" type="button">รายงานสรุป</button></a>
                    <?php
                        } else {
                    ?>
                    <a href="report_member_data/coop_report_member_people_retire_prepare_preview?member_id=<?php echo $member_id; ?>" target="_blank"><button class="btn btn-primary" style="width: 100%;" type="button">รายงานสรุป</button></a>
                    <?php
                        }
                    ?>
				</div>
			</div>
		</div>

        <?php
        $req_resign_status = array('0'=>'ยื่นคำร้อง','1'=>'อนุมัติ','2'=>'ไม่อนุมัติ');
        ?>
        <form action="Debt_dismiss/save_dismiss" id="form1" method="POST" >
            <input type="hidden" name="req_resign_id" value="<?php echo @$data['req_resign_id']; ?>">
            <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
            <input type="hidden" id="delete_resign" name="delete_resign" value="">
            <h3>รายละเอียด</h3>
            <div class="g24-col-sm-24">
                <div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label" for="form-control-2">เลขที่คำร้อง</label>
                    <div class="g24-col-sm-14">
                        <input id="form-control-2" name="req_resign_no" class="form-control " type="text" value="<?php echo @$data['req_resign_no']; ?>" readonly>
                    </div>
                </div>

                <div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label datepicker1" for="form-control-2">วันที่ขอลาออก</label>
                    <div class="input-with-icon g24-col-sm-14">
                        <input  required name="req_resign_date" class="form-control  mydate" type="text" data-provide="datepicker" data-date-language="th-th" data-date-today-highlight="true" value="<?php echo @$data['req_resign_date']==''?$this->center_function->mydate2date(date('Y-m-d')):$this->center_function->mydate2date($data['req_resign_date']); ?>">
                        <span class="icon icon-calendar input-icon m-f-1"></span>
                    </div>
                </div>

                <div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label" for="form-control-2">สถานะใบคำร้อง</label>
                    <div class="g24-col-sm-14">
                        <input id="req_resign_status" class="form-control " type="text" value="<?php echo @$req_resign_status[@$data['req_resign_status']]; ?>" readonly>
						<input name="req_resign_status" type="hidden" value="<?php echo @$data['req_resign_status']; ?>">
                    </div>
                </div>

                <div class="form-group g24-col-sm-8">
                    <div class="g24-col-sm-10 text-right">
                        <label>
                            วันที่อนุมัติ
                        </label>
                    </div>
                    <div class="g24-col-sm-14">
                        <?php if(@$data['approve_date'] != '0000-00-00 00:00:00' && @$data['approve_date'] != ''){
                            $approve_date = $this->center_function->mydate2date(@$data['approve_date']);
                        }else{
                            $approve_date = '';
                        }?>
                        <input class="form-control" id="approve_date" type="text" value="<?php echo $approve_date; ?>" readonly>
                    </div>
                </div>

                <div class="form-group g24-col-sm-8">
                    <div class="g24-col-sm-10 text-right">
                        <label>
                            วันสิ้นสภาพ
                        </label>
                    </div>
                    <div class="input-with-icon g24-col-sm-14">
                        <input  required name="resign_date" class="form-control  mydate" type="text" data-provide="datepicker" data-date-language="th-th" data-date-today-highlight="true" value="<?php echo @$data['resign_date']==''?$this->center_function->mydate2date(date('Y-m-d')):$this->center_function->mydate2date($data['resign_date']); ?>">
                        <span class="icon icon-calendar input-icon m-f-1"></span>
                    </div>
                </div>

                <div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label datepicker1" for="form-control-2" >สาเหตุการลาออก</label>
                    <div class="g24-col-sm-14">
                        <select id="resign_cause_id" class="form-control " name="resign_cause_id">
                            <option value="0">เลือกสาเหตุที่ลาออก</option>
                            <?php foreach($resign_cause as $key => $value){ ?>
                                <option value="<?php echo $value["resign_cause_id"]; ?>" <?php echo @$data['resign_cause_id']==$value["resign_cause_id"]?'selected':''; ?> check_debt="<?php echo @$value["check_debt"]; ?>"><?php echo $value["resign_cause_name"]; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group g24-col-sm-24">
                    <label class="g24-col-sm-3 control-label" for="form-control-2" style="width: 13.5%;">หมายเหตุ</label>
                    <div class="g24-col-sm-20" style="width: 86.5%;">
                        <textarea id="form-control-8" name="remark" class="form-control" rows="3"><?php echo @$data['remark']; ?></textarea>
                    </div>
                </div>

                <div class="form-group g24-col-sm-24">
                    <label class="g24-col-sm-3 control-label" for="form-control-2" style="width: 13.5%;">มติที่ประชุม</label>
                    <div class="g24-col-sm-21" style="width: 86.5%;">
                        <textarea id="form-control-8" name="conclusion" class="form-control" rows="3" readonly><?php echo @$data['conclusion']; ?></textarea>
                    </div>
                </div>

            </div>

            <?php if($member_id != ''){ ?>
                <div class="row ">
                    <div class="form-group text-center">
						<?php if(@$data['req_resign_id'] !='1'){ ?>
                        <button type="button" class="btn btn-primary min-width-100 m-t-2"  onclick="check_form()">ตกลง</button>
						<?php } ?>
                        <?php if(@$data['req_resign_id'] !='' && @$data['req_resign_status']=='0'){ ?>
                            <button class="btn btn-danger min-width-100 m-t-2" type="button" onclick="delete_row()">ยกเลิกการลาออก</button>
                        <?php } ?>
                        <a href="?"><button class="btn btn-danger min-width-100 m-t-2" type="button">ยกเลิก</button></a>
                    </div>
                </div>
            <?php } ?>
        </form>
    </div>
    </div>
</div>
    <?php $this->load->view('search_member_new_modal'); ?>

<script>
	function delete_row(){
		swal({
				title: "ท่านต้องการยกเลิกคำร้องขอลาออกใช่หรือไม่?",
				text: "",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'ยืนยัน',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: false,
				closeOnCancel: true
			},
			function(isConfirm) {
				if (isConfirm) {
					$('#delete_resign').val('1');
					$('#form1').submit();
				} else {
				}
			});

	}
	function check_form(){
		var text_alert = '';
		if($('#resign_cause_id').val()=='0'){
			text_alert += ' - สาเหตุการลาออก\n';
		}
		//เช็คการแจ้งเตือน สินทรัพย์ อ้างอิงจากตาราง coop_mem_resign_cause
		var check_debt = $('#resign_cause_id :selected').attr('check_debt');

		if(check_debt != '1'){
			if(parseInt($('#total_amount_all').val()) < 0){
				text_alert += ' - ไม่สามารถลาออกได้เนื่องจากหนี้สินมากกว่าสินทรัพย์รวม\n';
			}
		}

        <?php
            if($row_member['mem_type']=='2' || $row_member['mem_type']=='5') {
        ?>
            text_alert += ' - สมาชิกท่านนี้ได้ทำการลาออกหรือถูกให้ออกไปแล้ว\n';
        <?php
            }
        ?>
		
		if(text_alert != ''){
			swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
		}else{
            if(parseInt($('#count_contract').val()) >= 1){
                swal({
                    title: "สมาชิกยังมีภาระค้ำประกัน "+$('#count_contract').val()+" สัญญา",
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: "ยกเลิก",
                    closeOnConfirm: false,
                    closeOnCancel: true
                },
                function(isConfirm) {
                    if (isConfirm) {
                        $('#form1').submit();
                    } else {
                        
                    }
                });
                return false;
			}
			$('#form1').submit();
		}
	}

	$(document).ready(function() {
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
</script>
