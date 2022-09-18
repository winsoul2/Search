<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
		    .form-group{
				margin-bottom: 0px;
		    }
			.input-with-icon .form-control{
				padding-left: 40px;
			}
		</style> 
		<h1 style="margin-bottom: 0">ชำระเงินค่าสมัคร</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
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
							<th>ฌาปนกิจสงเคราะห์</th>
							<th>ยอดเงิน</th>
							<th>ผู้ทำรายการ</th>
							<th>สถานะ / วันที่</th>
							<th></th> 
						</tr> 
					 </thead>
					 <tbody id="table_first">
					  <?php 
						foreach($data as $key => $row ){ 
						?>
						  <tr> 
							  <td><?php echo $this->center_function->ConvertToThaiDate(@$row['createdatetime']); ?></td>
							  <td><?php echo @$row['member_id']; ?></td> 
							  <td class="text-left"><?php echo @$row['firstname_th']." ".@$row['lastname_th']; ?></td> 
							  <td class="text-left"><?php echo @$row['cremation_name_short']; ?></td> 
							  <td class="text-right"><?php echo number_format(@$row['cremation_pay_amount'],2); ?></td> 
							  <td><?php echo $row['user_name']; ?></td> 
							  <td>
								<?php echo (@$row['cremation_status'] == '1')?'ชำระเงินค่าสมัครแล้ว':($row['cremation_status'] == '6'? 'อนุมัติแล้ว' : 'ยังไม่ได้ชำระเงินค่าสมัคร'); ?>
								<?php
									$record_date = @$row['cremation_pay_date'];		
									echo (!empty($record_date))?'/'.$this->center_function->ConvertToThaiDate(@$record_date):''; 
								?>
							  </td>
							  <td style="font-size: 14px;">
									<?php 
										if(@$row['cremation_status'] == '0'){
									?>
										<a class="btn-radius btn-info" id="pay_<?php echo @$row['cremation_request_id']; ?>_1" title="ดำเนินการ" style="cursor: pointer;" onclick="pay_cremation('<?php echo @$row['cremation_request_id']; ?>','add')">
											ดำเนินการ
										</a>
									<?php }else{ ?>
										<a class="btn-radius btn-info" id="pay_<?php echo @$row['cremation_request_id']; ?>_1" title="แสดงรายการชำระเงินค่าสมัคร" style="cursor: pointer;" onclick="pay_cremation('<?php echo @$row['cremation_request_id']; ?>','view')">
											พิมพ์ใบเสร็จ
										</a>
									<?php } ?>
							  </td>
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

<div class="modal fade" id="show_pay"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:80%">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" onclick="close_modal('show_pay')">×</button>
                <h2 class="modal-title">ชำระเงินค่าสมัคร</h2>
				
            </div>
            <div class="modal-body">
				<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/cremation/cremation_pay_save'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_pay">
					<input type="hidden" name="cremation_request_id" id="cremation_request_id" class="cremation_request_id" value=""/>
					<input type="hidden" name="member_id" id="member_id" class="member_id" value=""/>
					<input type="hidden" name="receipt_number" id="receipt_number" class="receipt_number" value=""/>
					<input type="hidden" name="cremation_type_id" id="cremation_type_id" class="cremation_type_id" value=""/>
					<input type="hidden" name="action" id="action" value=""/>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">							
							<label class="g24-col-sm-8 control-label">ชื่อฌาปนกิจสงเคราะห์</label>
							<div class="g24-col-sm-14">
								<div class="form-group" id="cremation_type_name">
									<input type="text" class="form-control m-b-1 cremation_type_name" name="cremation_type_name" id="cremation_type_name" value=""  readonly="readonly">
								</div>
							</div>
						</div>						
						<div class="form-group">
							<label class="g24-col-sm-8 control-label">จำนวนเงินค่าสมัคร </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control m-b-1 cremation_pay_amount number_int_only" name="cremation_pay_amount" id="cremation_pay_amount" value=""  required title="กรุณากรอก จำนวนเงินค่าสมัคร">
								</div>
							</div>
							<label class="g24-col-sm-1 control-label">บาท </label>
						</div>
					</div>
				</form>
            </div>
			
            <div class="text-center m-t-1" style="padding-top:10px;">
				<button class="btn btn-info" onclick="check_form_pay();" id="bt_save" style="width: 160px;"><span class="icon icon-save"></span> บันทึก</button>
				<button class="btn btn-info" onclick="check_form_print()" id="bt_print" style="width: 160px;"><span class="icon icon-print"></span> พิมพ์ใบเสร็จ</button>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_cremation_pay.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>