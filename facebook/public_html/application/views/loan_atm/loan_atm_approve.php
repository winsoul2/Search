<div class="layout-content">
    <div class="layout-content-body">
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
	.modal-dialog-account {
		margin:auto;
		margin-top:7%;
	}
  .form-group{
    margin-bottom: 5px;
  }
  .modal-dialog-data {
		width:90% !important;
		margin:auto;
		margin-top:1%;
		margin-bottom:1%;
	}
</style>
<h1 class="title_top">อนุมัติสัญญาเงินกู้ฉุกเฉิน ATM</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php
		$get_param = '?';
		foreach(@$_GET as $key => $value){
			if($key != 'month' && $key != 'year' && $value != ''){
				$get_param .= $key.'='.$value.'&';
			}
		}
		$get_param = substr($get_param,0,-1);
		?>
		<a class="btn btn-primary btn-lg bt-add" target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_loan_data/loan_atm_ready_to_transfer_report'.$get_param); ?>">
			 รายงานการสั่งจ่ายเงินกู้
		</a>
	</div>
</div>
<div class="row gutter-xs">

        <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
		  <h3 >รายการขออนุมัติเงินกู้</h3>
				<form method="GET" action="">
					<div class="g24-col-sm-24">						
						<label class="g24-col-sm-3 control-label datepicker1" for="approve_date">วันที่สั่งจ่ายเงินกู้</label>
						<div class="input-with-icon g24-col-sm-3">
							<div class="form-group">
								<input id="approve_date" name="approve_date" class="form-control m-b-1 form_date_picker" type="text" value="<?php echo (@$_GET['approve_date'] != '')?@$_GET['approve_date']:''; ?>" data-date-language="th-th" autocomplete="off">
								<span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>
						<label class="g24-col-sm-1 control-label datepicker1 text-center" style="text-align:center" for="thru_date">ถึง</label>
						<div class="input-with-icon g24-col-sm-3">
							<div class="form-group">
								<input id="thru_date" name="thru_date" class="form-control m-b-1 form_date_picker" type="text" value="<?php echo (@$_GET['thru_date'] != '')?@$_GET['thru_date']:''; ?>" data-date-language="th-th" autocomplete="off">
								<span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>
							
							
						<label class="g24-col-sm-1 control-label">สถานะ</label>
						<div class="g24-col-sm-3 m-b-1">
							<select class="form-control" name="loan_status" id="loan_status">
								<option value="">ทั้งหมด</option>
								<option value="0" <?php echo @$_GET['loan_status']=='0'?'selected':''; ?>>รออนุมัติ</option>
								<option value="1" <?php echo @$_GET['loan_status']=='1'?'selected':''; ?>>อนุมัติ</option>
								<option value="5" <?php echo @$_GET['loan_status']=='5'?'selected':''; ?>>ไม่อนุมัติ</option>
							</select>
						</div>
						<div class="g24-col-sm-1">
							<input type="submit" class="btn btn-primary" value="ค้นหา">
						</div>
					</div>
				</form>
             <table class="table table-bordered table-striped table-center">
             <thead> 
                <tr class="bg-primary">
					<th>วันที่ทำรายการ</th>
					<th>วันที่สั่งจ่ายเงินกู้</th>
					<th>ชื่อสมาชิก</th>
					<th>เลขที่คำร้อง</th>
					<th>ยอดเงินกู้</th>
					<th>ผู้ทำรายการ</th>
					<th>สถานะ</th>
					<th>จัดการ</th> 
                </tr> 
             </thead>
                <tbody id="table_first">
                  <?php 
					if(!empty($data)){
					foreach($data as $key => $row ){ ?>
					  <tr> 
						  <td><?php echo $this->center_function->ConvertToThaiDate($row['createdatetime']); ?></td>
						  <td><?php echo $this->center_function->ConvertToThaiDate($row['approve_date']); ?></td>
						  <td><?php echo $row['firstname_th']." ".$row['lastname_th']; ?></td> 
						  <td>
							<a href="<?php echo base_url(PROJECTPATH.'/loan_atm/petition_emergent_atm_pdf/'.$row['loan_atm_id']); ?>" target="_blank"><?php echo $row['petition_number']; ?></a>  
							<?php 
							if($row['loan_atm_status']=='0'){
							?>
							 | <a title="เอกสารพิจารณาเงินกู้" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo PROJECTPATH."/report_loan_data/coop_report_loan_atm_detail_preview?member_id=".$row['member_id']."&loan_id=".$row['loan_atm_id']; ?>" target="_blank"><span class="icon icon-list-alt"></span></a>
							<?php }?>
						  </td> 
						  <td><?php echo number_format($row['total_amount'],2); ?></td> 
						  <td><?php echo $row['user_name']; ?></td> 
						  <td><span id="loan_status_<?php echo $row['loan_atm_id']; ?>" ><?php echo $loan_atm_status[$row['loan_atm_status']]; ?></span></td>
						  <td style="font-size: 14px;">
							<?php 
								if($row['loan_atm_status']=='0'){
							?>
								<a class="btn btn-info" id="approve_<?php echo $row['loan_atm_id']; ?>_1" title="อนุมัติ" onclick="approve_loan_save('<?php echo $row['loan_atm_id']; ?>')">
									<!--span style="cursor: pointer;" class="icon icon-check-square-o"></span-->
									อนุมัติ
								</a>
								<a class="btn btn-danger" id="approve_<?php echo $row['loan_atm_id']; ?>_1" title="ไม่อนุมัติ" onclick="loan_atm_not_approve('<?php echo $row['loan_atm_id']; ?>','5')">
									<!--span style="cursor: pointer;" class="icon icon-check-square-o"></span-->
									ไม่อนุมัติ
								</a>
							<?php }else{ ?>
								<?php if($row['deduct_receipt_id']!=''){ ?>
									<a href="<?php echo base_url(PROJECTPATH.'/admin/receipt_form_pdf/'.$row['deduct_receipt_id']); ?>" target="_blank">ใบเสร็จ</a>
								<?php } ?>
							<?php } ?>
						  </td>
					  </tr>
                  <?php } 
					}else{?>
					<tr> 
						  <td colspan="7">ไม่พบข้อมูล</td>
					  </tr>
					<?php } ?>
                  </tbody> 
                  </table> 
          </div>
          </div>
                </div>
                  <?php echo $paging ?>
	</div>
</div>
<div class="modal fade" id="loan_approve_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">สร้างสัญญากู้เงินฉุกเฉิน ATM</h2>
			</div>
			<form action="<?php echo base_url(PROJECTPATH.'/loan_atm/loan_approve_save'); ?>" method="POST" id="form_approve" enctype="multipart/form-data">
				<input type="hidden" id="loan_atm_id" name="loan_atm_id" value="">
				<div class="modal-body">
					<div class="g24-col-sm-24 modal_data_input">
						<div class="g24-col-sm-24 m-b-1">
							<label class="g24-col-sm-4 control-label ">คำร้องเลขที่</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" id="petition_number" value="" readonly>
							</div>
						</div>
						<div class="g24-col-sm-24 m-b-1">
							<label class="g24-col-sm-4 control-label">รหัสสมาชิก</label>
							<div class="g24-col-sm-5">
								<input class="form-control" id="member_id" type="text" name="member_id" value="" readonly>
							</div>
							<label class="g24-col-sm-3 control-label ">ชื่อ-สกุล</label>
							<div class="g24-col-sm-7">
								<input class="form-control" type="text" id="member_name" value="" readonly>
							</div>
						</div>
						<div class="g24-col-sm-24 m-b-1" >
							<label class="g24-col-sm-4 control-label">วงเงินที่ขอกู้</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" id="total_amount" value="" readonly>
							</div>
							<label class="g24-col-sm-3 control-label ">วงเงินที่อนุมัติ</label>
							<div class="g24-col-sm-7">
								<input class="form-control" type="text" id="total_amount_approve" name="total_amount_approve" onkeyup="format_the_number(this);cal_balance()" value="">
							</div>
						</div>
						<div class="g24-col-sm-24 m-b-1">
							<label class="g24-col-sm-4 control-label">หักกลบ</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" id="prev_loan" name="prev_loan" value="" readonly>
								<input type="hidden" id="prev_loan_number" name="prev_loan_number" value="">
							</div>
							<label class="g24-col-sm-3 control-label ">คงเหลือ</label>
							<div class="g24-col-sm-7">
								<input class="form-control" type="text" id="total_amount_approve_balance" name="total_amount_approve_balance" value="" readonly>
							</div>
						</div>
						<!--div class="g24-col-sm-24" style="margin-bottom: 20px;">
							<label class="g24-col-sm-4 control-label">หมายเลขบัตร ATM</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" id="atm_card_number" name="atm_card_number" value="" readonly>
							</div>
						</div-->
						<div class="center">
							<button class="btn btn-primary" id="submit_button" type="button" onclick="check_submit()">บันทึก</button>&nbsp;&nbsp;&nbsp;
							<button class="btn btn-default" type="button" data-dismiss="modal">ยกเลิก</button>
						</div>
					</div>
					&nbsp;
				</div>
			</form>
		</div>
	</div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/loan_atm_approve.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>

<script>
$(document).ready(function() {
	$(".form_date_picker").datepicker({
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