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

input[type=checkbox], input[type=radio] {
    margin: 11px 0 0;
}
</style> 
<?php
	$transfer_status = array(''=>'ยังไม่ได้โอนเงิน','0'=>'โอนเงินแล้ว');
	//$transfer_status = array('0'=>'โอนเงินแล้ว','1'=>'รออนุมัติยกเลิก','อนุมัติยกเลิกรายการ');
?>


		<div class="row">
			<div class="form-group">
				<div class="col-sm-6">
					<h1 class="title_top">คืนเงินหลังประมวลผลผ่านรายการ</h1>
					<?php $this->load->view('breadcrumb'); ?>
				</div>
				<div class="col-sm-6">
					<br>
				</div>
			</div>
		</div>
		
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				  <h3></h3>
					 <table class="table table-bordered table-striped table-center">
					 <thead> 
						<tr class="bg-primary">
							<th>วันที่ทำรายการ</th>
							<th>รหัสสมาชิก</th>
							<th>ชื่อสมาชิก</th>
							<th>ยอดเงิน</th>
							<th>ผู้ทำรายการ</th>
							<th>จัดการ</th> 
						</tr> 
					 </thead>
						<tbody id="table_first">
						<?php //echo '<pre>'; print_r($data); echo '</pre>';?>
						  <?php 
							if(!empty($data)){
							foreach($data as $key => $row ){ ?>							
							  <tr> 
								  <td><?php echo @$this->center_function->ConvertToThaiDate($row['approve_date']); ?></td>
								  <td><?php echo @$row['member_id']; ?></td> 
								  <td class="text-left"><?php echo @$row['prename_short'].@$row['firstname_th']." ".@$row['lastname_th']; ?></td> 
								  <td class="text-right"><?php echo number_format(@$row['total_return_amount'],2); ?></td> 
								  <td><?php echo @$row['user_name']; ?></td> 
								  <td style="font-size: 14px;">
										<a class="btn btn-info" id="" title="จ่ายเงิน" onclick="open_transfer_modal('<?php echo @$row['return_profile_id']; ?>');">
											จ่ายเงิน
										</a>
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
		<?php echo @$paging ?>
	</div>
</div>

<div class="modal fade" id="transfer_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-dialog-file">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">จ่ายเงิน</h2>
			</div>
			<form action="<?php echo base_url(PROJECTPATH.'/finance/finance_pay_return_save')?>" method="POST" id="form_return_transfer" enctype="multipart/form-data">
				<div class="modal-body">
					<input id="return_profile_id" name="return_profile_id" type="hidden">
					<input name="date_transfer" type="hidden" value="<?php echo date('Y-m-d H:i:s'); ?>">
					<div class="g24-col-sm-24 modal_data_input">
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">รหัสสมาชิก</label>
							<div class="g24-col-sm-6" >
								<input class="form-control member_id all_input" id="member_id" type="text" value=""  readonly>
							</div>
							<label class="g24-col-sm-3 control-label" for="form-control-2">ชื่อสกุล</label>
							<div class="g24-col-sm-8" >
								<input class="form-control all_input" id="member_name" type="text" value=""  readonly>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">ยอดเงินเงินคืน</label>
							<div class="g24-col-sm-6" >
								<input class="form-control all_input" id="total_return_amount" name="total_return_amount" type="text" value=""  readonly>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">วิธีการชำระเงิน</label>
							<div class="g24-col-sm-18">
								<span id="show_pay_type2" style="">
									<input type="radio" name="pay_type" id="pay_type_0" onclick="change_pay_type()" value="0"> เงินสด &nbsp;&nbsp;
									<input type="radio" name="pay_type" id="pay_type_1" onclick="change_pay_type()" value="1"> โอนเงินบัญชีสหกรณ์ &nbsp;&nbsp;
									<input type="radio" name="pay_type" id="pay_type_2" onclick="change_pay_type()" value="2"> โอนเงินบัญชีธนาคาร
								</span>
							</div>
						</div>
						
						<div class="g24-col-sm-24 pay_type_1" style="display:none;">
							<div class="form-group g24-col-sm-24">
								<label class="g24-col-sm-6 control-label" for="form-control-2">เลขบัญชีสมาชิก</label>
								<div class="g24-col-sm-17" id="account_list_space">
									
								</div>
							</div>
						</div>
			
						<div class="form-group g24-col-sm-24 pay_type_2" style="display:none;">						
							<label class="g24-col-sm-6 control-label" for="form-control-2">บัญชี</label>
							<div class="g24-col-sm-17" >
								<input class="form-control" id="dividend_bank_id" name="dividend_bank_id" type="hidden" value="">
								<input class="form-control" id="dividend_bank_branch_id" name="dividend_bank_branch_id" type="hidden" value="">
								<input class="form-control all_input" id="dividend_acc_num" name="dividend_acc_num" type="text" value=""  readonly>								
							</div>
						</div>								
						<div class="text-center">
							<button class="btn btn-primary" type="button" onclick="return cash_submit()">จ่ายเงินกู้</button>
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
    'src' => PROJECTJSPATH.'assets/js/finance_pay_return.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
