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
			.input-with-icon {
				margin-bottom: 5px;
			}
			
			.input-with-icon .form-control{
				padding-left: 40px;
			}
			.modal_data_input{
				margin-left:-5px;
			}
            .modal-dialog {
                width: 700px;
            }
		</style> 
		<h1 style="margin-bottom: 0">อนุมัติขอรับเงินฌาปนกิจสงเคราะห์</h1>
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
							<th style="width:150px;">วันที่ทำรายการ</th>
							<th>เลขฌาปนกิจ</th>
							<th>ชื่อสมาชิก</th>
							<th>ผู้ขอรับเงิน</th>
							<th>ยอดเงิน</th>
							<th>ผู้ทำรายการ</th>
							<th>สถานะ</th>
							<th style="width:150px;"></th> 
						</tr> 
					 </thead>
					 <tbody id="table_first">
					  <?php 
						foreach($data as $key => $row ){ 
						?>
						  <tr> 
							  <td><?php echo @$this->center_function->ConvertToThaiDate($row['createdatetime']); ?></td>
							  <td><?php echo @$row['member_cremation_id']; ?></td>
							  <td class="text-left"><?php echo @$row['firstname_th']." ".@$row['lastname_th']; ?></td> 
							  <td class="text-left"><?php echo @$row[$row["receiver"]]; ?></td> 
							  <td style="text-align: right;"><?php echo number_format(@$row['cremation_balance_amount'],2); ?></td> 
							  <td><?php echo @$row['user_name_transfer']; ?></td>
							  <td>
								<?php echo @$receive_status[@$row['cremation_receive_status']]; ?>
							  </td>							  
							  <td>
									<?php 
										if(@$row['cremation_receive_status'] == '1'){
									?>
									<a class="btn-radius btn-info" id="approve_<?php echo @$row['cremation_receive_id']; ?>_1" title="แสดงรายการอนุมัติขอรับเงินฌาปนกิจสงเคราะห์" style="cursor: pointer;" onclick="transfer_cremation_1('<?php echo @$row['cremation_receive_id']; ?>','view')">
										แสดงรายการ
									</a>
									<?php }else{ ?>
									<a class="btn-radius btn-info" id="approve_<?php echo @$row['cremation_receive_id']; ?>_1" title="ดำเนินการ" style="cursor: pointer;" onclick="transfer_cremation_1('<?php echo @$row['cremation_receive_id']; ?>','add')">
										ดำเนินการ
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



<div class="modal fade" id="show_transfer_1" role="dialog">
	<div class="modal-dialog  modal-dialog-info" >
		<div class="modal-content">
            <form id="from_transfer_1" method="POST" enctype="multipart/form-data" action="<?php echo base_url(PROJECTPATH.'/cremation/approve_request_money'); ?>">
                <input type="hidden" name="cremation_request_id" id="cremation_request_id" class="cremation_request_id" value=""/>
                <input type="hidden" name="cremation_receive_id" id="cremation_receive_id" class="cremation_receive_id" value=""/>
                <input type="hidden" name="action" id="action" value=""/>
                <input type="hidden" name="member_id" id="member_id" class="member_id" value=""/>   
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">ขอรับเงินฌาปนกิจสงเคราะห์</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group  g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">เลขฌาปนกิจสงเคราะห์</label>
                        <div class="g24-col-sm-6">
                            <input id="member_cremation_id_input" class="form-control" style="text-align:left;" type="text" value="" readonly/>
                        </div>
                    </div>
                    <div class="form-group  g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">ชื่อสกุล</label>
                        <div class="g24-col-sm-18">
                            <input id="name" class="form-control" style="text-align:left;" type="text" value="" readonly/>
                        </div>
                    </div>
                    <div class="form-group  g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">สาเหตุการเสียชีวิต</label>
                        <div class="g24-col-sm-18">
                            <input id="reason" name="reason" class="form-control" style="text-align:left;" type="text" value="" readonly/>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">วันที่เสียชีวิต</label>
                        <div class="g24-col-sm-6">
                            <input id="death_date" name="death_date" class="form-control m-b-1 cre-input" style="padding-left: 40px;" type="text" value="" maxlength="10" readonly>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">พินัยกรรม</label>
                        <div class="g24-col-sm-6 testament-label">
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">หลักฐานการเสียชีวิต</label>
                        <div class="g24-col-sm-6 evidence-label">
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">ผู้รับเงินฌาปนกิจ</label>
                        <div class="g24-col-sm-18">
                            <div class="form-group">
                                <input type="text" id="receiver" name="receiver" class="form-control m-b-1"  value="" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label"><span id="formula_span"></span></label>
                        <input type="hidden" id="money_received_per_member" name="money_received_per_member" value="<?php echo $setting["money_received_per_member"];?>"/>
                        <input type="hidden" id="member_amount" name="member_amount" value="<?php echo $count_members?>"/>
                        <div class="g24-col-sm-6">
                            <div class="form-group">
                                <input id="cremation_receive_amount" name="cremation_receive_amount" class="form-control text-right" style="text-align:left;" type="text" value="" readonly/>
                            </div>
                        </div>
                        <label class="g24-col-sm-6 control-label">ค่าดำเนินการ <span id="action_fee_percent_span"></span> %</label>
                        <div class="g24-col-sm-6">
                            <div class="form-group">
                                <input id="action_fee_percent" name="action_fee_percent" class="form-control text-right" style="text-align:left;" type="text" value="" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">คงเหลือ</label>
                        <div class="g24-col-sm-6">
                            <input id="cremation_balance_left" name="cremation_balance_left" class="form-control text-right" style="text-align:left;" type="text" value="" readonly/>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">เงินสงเคราะห์ล่วงหน้า</label>
                        <div class="g24-col-sm-6">
                            <input id="adv_payment_balance" name="adv_payment_balance" class="form-control text-right" style="text-align:left;" type="text" value="" readonly/>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">รวมเงินที่จะได้รับ</label>
                        <div class="g24-col-sm-6">
                            <input id="cremation_balance_amount" name="cremation_balance_amount" class="form-control text-right" style="text-align:left;" type="text" value="" readonly/>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <br>
                    </div>
                </div>
                <div class="text-center m-t-1" style="padding-top:10px;">
                    <button class="btn btn-info bt_save" onclick="check_form_transfer()" id="bt_save"><span class="icon icon-save"></span> บันทึก</button>
                    <button type="button" id="close" class="btn btn-info" data-dismiss="modal"><span class="icon icon-close"></span> ออก</button>
                </div>
                <div class="text_center m-t-1">&nbsp;</div>
            </form>
		</div>
	</div>
</div>
<?php $this->load->view('cremation/cremation_approve_receive_modal_show'); ?>
<?php
$link = array(
	'src' => 'assets/js/coop_cremation_approve_receive.js',
	'type' => 'text/javascript'
);
echo script_tag($link);
?>