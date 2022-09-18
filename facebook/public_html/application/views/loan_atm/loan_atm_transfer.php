<div class="layout-content">
    <div class="layout-content-body">
<style>
label {
    padding-top: 6px;
    text-align: right;
}
.text-center{
	text-align:center;
}
</style> 
<h1 class="title_top">โอนเงินกู้ฉุกเฉิน ATM</h1>
<?php $this->load->view('breadcrumb'); ?>
<?php
	$transfer_status = array('0'=>'ยังไม่ได้โอนเงิน','1'=>'โอนเงินแล้ว');
?>
<div class="row gutter-xs">
	<div class="col-xs-12 col-md-12">
		<div class="panel panel-body" style="padding-top:0px !important;">
		<h3></h3>
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label" for="form-control-2">เลขที่สัญญา</label>
                    <div class="g24-col-sm-14" >
						<div class="input-group">
							<input id="contract_number" class="form-control" type="text" value="<?php echo @$row['contract_number']!=''?$row['contract_number']:''; ?>" readonly>
							<span class="input-group-btn">
								<a href="#" onclick="open_other_modal('transfer_list_modal')">
									<button id="" type="button" class="btn btn-info btn-search" ><span class="icon icon-search"></span></button>
								</a>
							</span>	
						</div>						
                    </div>
                  </div>
				<div class="form-group g24-col-sm-8">
					<label class="g24-col-sm-10 control-label" for="form-control-2">รหัสสมาชิก</label>
					<div class="g24-col-sm-14" >
						<input class="form-control member_id all_input" type="text" value="<?php echo @$row['member_id']?>"  readonly>
					</div>
				</div>
				<div class="form-group g24-col-sm-8">
					<label class="g24-col-sm-10 control-label" for="form-control-2">ชื่อสกุล</label>
					<div class="g24-col-sm-14" >
						<input class="form-control all_input" id="member_name" type="text" value="<?php echo @$row['firstname_th']." ".$row['lastname_th']?>"  readonly>
					</div>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-8">
					<label class="g24-col-sm-10 control-label" for="form-control-2">ยอดเงินกู้</label>
					<div class="g24-col-sm-14" >
						<input class="form-control all_input" id="loan_amount" type="text" value="<?php echo number_format(@$row['loan_amount'])?>"  readonly>
					</div>
				</div>
				<div class="form-group g24-col-sm-8">
					<label class="g24-col-sm-10 control-label" for="form-control-2">วันที่ทำรายการ</label>
					<div class="g24-col-sm-14" >
						<input class="form-control all_input" id="loan_date" type="text" value="<?php echo @$row['loan_date']!=''?$this->center_function->mydate2date($row['loan_date'],true):''; ?>"  readonly>
					</div>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-8">
					<label class="g24-col-sm-10 control-label" for="form-control-2"></label>
					<div class="g24-col-sm-14">
						<?php 
								$display_pay_type_1 = '';
								$display_pay_type_2 = 'display:none;';
						?>
						<span id="show_pay_type1" style="<?php echo $display_pay_type_1; ?>">
							<input class="form-control all_input" id="pay_type" type="text" value="<?php echo @$pay_type[$row['pay_type']]; ?>" readonly>
						</span>
						<span id="show_pay_type2" style="<?php echo $display_pay_type_2; ?>">
							<input type="radio" name="pay_type" id="pay_type_0" onclick="change_pay_type()" value="0"> เงินสด 
							<input type="radio" name="pay_type" id="pay_type_1" onclick="change_pay_type()" value="1"> เงินโอน
						</span>
					</div>
				</div>
			</div>
			<div class="g24-col-sm-24 pay_type_1" style="display:none;">
				<div class="form-group g24-col-sm-8">
					<label class="g24-col-sm-10 control-label" for="form-control-2">เลขบัญชีสมาชิก</label>
					<div class="g24-col-sm-14" id="account_list_space">
						<select class="form-control all_input" id="account_list" onchange="change_account()">
							<option value="">เลือกบัญชี</option>
							<?php if(@$row['member_id']!=''){
								foreach($rs_account as $key => $row_account){
							?>
								<option <?php echo @$row['account_id']==$row_account['account_id']?'selected':''; ?> value="<?php echo $row_account['account_id'];?>" account_name="<?php echo $row_account['account_name']; ?>"><?php echo $row_account['account_id']." : ".$row_account['account_name'];?></option>
							<?php }
							} ?>
						</select>
					</div>
				</div>
				<div class="form-group g24-col-sm-8">
					<label class="g24-col-sm-10 control-label" for="form-control-2">ชื่อบัญชี</label>
					<div class="g24-col-sm-14" >
						<input class="form-control all_input" id="account_name" type="text" value="<?php echo @$row['account_name']; ?>"  readonly>
					</div>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-8">
					<label class="g24-col-sm-10 control-label" for="form-control-2">สถานะ</label>
					<div class="g24-col-sm-14" >
						<input class="form-control all_input" id="transfer_status" type="text" value="<?php echo @$row['loan_id']!=''?@$row['transfer_id']==''?'ยังไม่ได้โอนเงิน':@$transfer_status[@$row['transfer_status']]:''; ?>"  readonly>
					</div>
				</div>
				<div class="form-group g24-col-sm-8">
					<label class="g24-col-sm-10 control-label" for="form-control-2">วันที่โอน</label>
					<div class="g24-col-sm-14" >
						<input class="form-control all_input" type="text" id="date_transfer" value="<?php echo @$row['date_transfer']!=''?$this->center_function->mydate2date($row['date_transfer'],true):''; ?>"  readonly>
					</div>
				</div>
				<div class="form-group g24-col-sm-8">
					<label class="g24-col-sm-10 control-label" for="form-control-2">ผู้ทำรายการ</label>
					<div class="g24-col-sm-14" >
						<input class="form-control all_input" id="user_name" type="text" value="<?php echo @$row['user_name']?>"  readonly>
					</div>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-8">
					<label class="g24-col-sm-10 control-label" for="form-control-2">หลักฐานการโอนเงิน</label>
					<label class="g24-col-sm-14" id="file_show" style="text-align:left">
						<?php if($row['file_name']!=''){ ?>
							<a target="_blank" href="<?php echo PROJECTPATH."/assets/uploads/loan_atm_transfer_attach/".@$row['file_name'];?>"><?php echo @$row['file_name']; ?></a>
						<?php } ?>
					</label>
				</div>
			</div>
			<div class="g24-col-sm-24 text-center">
				<button class="btn btn-info pay_type_0" id="btn_open_transfer" style="display:none; width:130px;" onclick="cash_submit()"><span class="icon icon-arrow-circle-o-up"></span> บันทึกการโอนเงิน</button>
				<button class="btn btn-info pay_type_1" id="btn_open_transfer" style="display:none; width:130px;" onclick="open_modal('loan_transfer')"><span class="icon icon-arrow-circle-o-up"></span> บันทึกการโอนเงิน</button>
				<?php if($row['transfer_status']=='0'){ 
					$display = '';
				}else{
					$display = 'display:none;';
				}
				?>
					<!--button class="btn btn-danger" style="<?php echo $display; ?> width:130px" id="btn_cancel_transfer" onclick="cancel_transfer('<?php echo @$row['transfer_id']; ?>','<?php echo @$_GET['loan_id']?>')"><span class="icon icon-close"></span> ยกเลิกการโอนเงิน</button-->
			</div>
		</div>
	</div>
</div>
	</div>
</div>
<form action="<?php echo base_url(PROJECTPATH.'/loan_atm/loan_atm_transfer_save')?>" method="POST" id="form_loan_transfer" enctype="multipart/form-data">
<input class="loan_id" name="loan_id" type="hidden">
<input id="account_id" name="account_id" type="hidden">
<div class="modal fade" id="loan_transfer" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">โอนเงินกู้ฉุกเฉิน ATM</h2>
			</div>
			<div class="modal-body">
				<div class="g24-col-sm-24 modal_data_input">
					<label class="g24-col-sm-6 control-label " >วันที่โอนเงิน</label>
					<div class="input-with-icon g24-col-sm-10">
						<div class="form-group">
							<input id="date_transfer_picker" name="date_transfer" class="form-control m-b-1" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
							<span class="icon icon-calendar input-icon m-f-1"></span>
						</div>
					</div>
				</div>
				<div class="g24-col-sm-24 modal_data_input">
					<label class="g24-col-sm-6 control-label " >เวลาโอนเงิน</label>
					<div class="input-with-icon g24-col-sm-10">
						<div class="form-group">
							<input id="time_transfer" name="time_transfer" class="form-control m-b-1" type="text" value="<?php echo date('H:i'); ?>">
							<span class="icon icon-clock-o input-icon m-f-1"></span>
						</div>
					</div>
				</div>
				<div class="g24-col-sm-24 modal_data_input">
					<label class="g24-col-sm-6 control-label " >หลักฐานการโอนเงิน</label>
					<div class="input-with-icon g24-col-sm-10">
						<div class="form-group">
							<input type="file" name="file_attach" id="file_attach" class="form-control" OnChange="readURL(this);">
						</div>
					</div>
				</div>
				<div class="g24-col-sm-24 modal_data_input">
					<label class="g24-col-sm-6 control-label " ></label>
					<div class="input-with-icon g24-col-sm-10">
						<div class="form-group">
							<img id="ImgPreview" src="<?php echo base_url(PROJECTPATH.'/assets/images/default.jpg'); ?>" width="248" height="248" />
						</div>
					</div>
				</div>
				<div class="text-center" style="margin-top: 5px;">
					<button class="btn btn-primary" type="button" onclick="check_form()">บันทึก</button>&nbsp;&nbsp;&nbsp;
				</div>
			</div>
		</div>
	</div>
</div>
</form>
<div class="modal fade" id="transfer_list_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">รายการขอกู้เงิน</h2>
			</div>
			<div class="modal-body">
				<table class="table table-striped">
					<thead>
						<tr>
							<th style="text-align:center">ลำดับ</th>
							<th width="30%" style="text-align:center">เลขที่สัญญา</th>
							<th width="30%" style="text-align:center">จำนวนเงิน</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php if(!empty($transfer_list)){ $i=1; ?>
							<?php foreach($transfer_list as $key => $value){ ?>
								<tr>
									<td align="center"><?php echo $i++; ?></td>
									<td align="center"><?php echo $value['contract_number']; ?></td>
									<td align="center"><?php echo number_format($value['loan_amount']); ?></td>
									<td align="center"><button class="btn btn-primary" onclick="search_loan('<?php echo $value['loan_id']; ?>')">เลือก</button></td>
								</tr>
							<?php } ?>
						<?php }else{ ?>
							<tr><td colspan="4" align="center">ไม่พบข้อมูล</td></tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/loan_atm_transfer.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>