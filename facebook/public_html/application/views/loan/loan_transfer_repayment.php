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
					<h1 class="title_top">โอนเงินกู้ (คืนวงเงิน)</h1>
					<?php $this->load->view('breadcrumb'); ?>
				</div>
				<div class="col-sm-6">
					<br>
					<div class="g24-col-sm-24" style="text-align:right;padding-right:0px;margin-right:0px;">
						<a class="link-line-none" href="<?=base_url('report_loan_data/coop_report_loan_repayment')?>">
							<button class="btn btn-primary" style="margin-right:5px;">รายงานโอนเงินกู้</button>
						</a>
					</div>
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
							<th>สถานะ</th>
							<th>จัดการ</th> 
						</tr> 
					 </thead>
						<tbody id="table_first">
						<?php //echo '<pre>'; print_r($data); echo '</pre>';?>
						  <?php 
							if(!empty($loan)){
							foreach($loan as $key => $row ){ ?>							
							  <tr> 
                                    <td><?php echo @$this->center_function->ConvertToThaiDate($row->transaction_time); ?></td>
                                    <td><?php echo @$row->member_id; ?></td> 
                                    <td class="text-left"><?php echo @$row->firstname_th." ".@$row->lastname_th; ?></td> 
                                    <td class="text-right"><?php echo number_format(@$row->loan_request,2); ?></td> 
                                    <td><?php echo @$row->user_name; ?></td> 
                                    <?php
                                        $status = ["รอจ่ายเงิน", "จ่ายเงินแล้ว"];
                                    ?>
                                    <td><?=$status[$row->status]?></td>
                                    <td style="font-size: 14px;">
                                            <a class="btn btn-info" id="" title="จ่ายเงินกู้" onclick="open_transfer_repayment_modal('<?php echo @$row->id; ?>');">
                                                จ่ายเงินกู้
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
	<div class="modal-dialog modal-dialog-file ">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">จ่ายเงินกู้</h2>
			</div>
			<form action="<?php echo base_url(PROJECTPATH.'/loan/sdsdsd')?>" method="POST" id="form_loan_transfer" enctype="multipart/form-data">
				<div class="modal-body">
					<input id="loan_id" name="loan_id" type="hidden">
					<input id="" name="date_transfer" class="form-control m-b-1" type="hidden" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>">
					<input id="time_transfer" name="time_transfer" class="form-control m-b-1" type="hidden" value="<?php echo date('H:i'); ?>">
					<div class="g24-col-sm-24 modal_data_input">
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">เลขที่สัญญา</label>
							<div class="g24-col-sm-14" >
								<input id="contract_number" class="form-control" type="text" value="" readonly>					
							</div>
						</div>

                        <div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">รหัสสมาชิก</label>
							<div class="g24-col-sm-14" >
                                <input class="form-control member_id all_input" id="member_id" type="text" value=""  readonly>			
							</div>
						</div>

                        <div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">ชื่อสกุล</label>
							<div class="g24-col-sm-14" >
                                <input class="form-control all_input" id="member_name" type="text" value=""  readonly>	
							</div>
						</div>

                        <div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">ยอดเงินที่ได้รับ</label>
							<div class="g24-col-sm-14" >
                                <input class="form-control all_input" id="amount_transfer" name="amount_transfer" type="text" value="" required title="กรุณาป้อน ยอดเงินที่ได้รับ"  readonly>
							</div>
						</div>

                        <div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">วิธีการชำระเงิน</label>
							<div class="g24-col-sm-14" >
                                <input class="form-control all_input" id="transfer_type" name="transfer_type" type="text" value="" readonly>
							</div>
						</div>

                        <div class="form-group g24-col-sm-24 account_no">
							<label class="g24-col-sm-6 control-label" for="form-control-2">เลขบัญชีสมาชิก </label>
							<div class="g24-col-sm-14" >
								<input class="form-control all_input" id="account_no" name="account_no" type="text" value="" readonly style="display: block;">
							</div>
						</div>

                        <div class="form-group g24-col-sm-24 bank_name">
							<label class="g24-col-sm-6 control-label" for="form-control-2">ธนาคาร </label>
							<div class="g24-col-sm-14" >
								<input class="form-control all_input" id="bank_name" name="bank_name" type="text" value="" readonly style="display: block;">
							</div>
						</div>

                        <div class="form-group g24-col-sm-24 branch_code">
							<label class="g24-col-sm-6 control-label" for="form-control-2">สาขา </label>
							<div class="g24-col-sm-14" >
								<input class="form-control all_input" id="branch_code" name="branch_code" type="text" value="" readonly style="display: block;">
							</div>
						</div>

                        <div class="form-group g24-col-sm-24 bank_account_no">
							<label class="g24-col-sm-6 control-label" for="form-control-2">เลขที่บัญชี </label>
							<div class="g24-col-sm-14" >
								<input class="form-control all_input" id="bank_account_no" name="bank_account_no" type="text" value="" readonly style="display: block;">
							</div>
						</div>
                        <!-- <div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">วิธีการชำระเงิน</label>
							<div class="g24-col-sm-6" >
								<input class="form-control all_input" id="transfer_type" name="transfer_type" type="text" value="" readonly>
							</div>
						</div>				 -->
                        
						<div class="text-center">
                        <br><br>
                            <input type="hidden" id="loan_transfer_repayment">
                            <input type="hidden" id="loan_id">
							<button class="btn btn-primary" type="button" onclick="save_loan_repayment()">จ่ายเงินกู้</button>
						</div>
					</div>
					&nbsp;
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="report_filter_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-dialog-file">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">รายงานโอนเงินกู้</h2>
			</div>
			<form action="<?php echo base_url(PROJECTPATH.'/report_loan_data/loan_already_transfer_report')?>" method="POST" id="form_print_report" enctype="multipart/form-data" target="_blank">
				<div class="modal-body">
						<div class="form-group g24-col-sm-24" >						
							<label class="g24-col-sm-6 control-label">วันที่</label>
							<div class="input-with-icon g24-col-sm-7" >
								<div class="form-group">
									<input id="date_start" name="date_start" class="form-control" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" required title="" >
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>	
							</div>
							<label class="g24-col-sm-2 control-label" style="text-align:center;">ถึง</label>
							<div class="input-with-icon g24-col-sm-7" >
								<div class="form-group">
									<input id="date_end" name="date_end" class="form-control" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" required title="" >
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>	
							</div>
						</div>	
						<div class="form-group g24-col-sm-24" >						
							<label class="g24-col-sm-6 control-label">ประเภทเงินกู้</label>
							<div class="g24-col-sm-16" >
								<select class="form-control" name="loan_type" id="loan_type" onchange="change_type()">
									<option value="">เลือกประเภทเงินกู้</option>
									<?php foreach($loan_type as $key => $value){ ?>
										<option value="<?php echo $key; ?>" <?php echo $key == @$_GET['loan_type']?'selected':'';?>><?php echo $value; ?></option>
									<?php } ?>
								</select>				
							</div>
						</div>		
						<div class="form-group g24-col-sm-24" >						
							<label class="g24-col-sm-6 control-label">ชื่อเงินกู้</label>
							<div class="g24-col-sm-16" >
								<select class="form-control" name="loan_name" id="loan_name">
									<option value="">เลือกชื่อเงินกู้</option>
								</select>			
							</div>
						</div>						
						<div class="text-center">
							<button class="btn btn-primary" type="submit">พิมพ์รายงาน</button>
						</div>
					&nbsp;
				</div>
			</form>
		</div>
	</div>
</div>
<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/loan_transfer.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>

<script>
    function open_transfer_repayment_modal(id){
        $.ajax({
            url:base_url+"loan/get_loan_transfer_repayment",
            method:"post",
            data:{id:id},
            dataType:"text",
            success:function(data)
            {
                var obj = JSON.parse(data);
                // $('#loan_id').val(loan_id);
                $('#loan_transfer_repayment').val(obj.id);
                $('#contract_number').val(obj.contract_number);
                $('#member_id').val(obj.member_id);
                $('#member_name').val(obj.firstname_th+"  "+obj.lastname_th);
                $('#loan_amount').val(obj.loan_amount);			
                $('#amount_transfer').val(obj.loan_request);			
                
                $("#transfer_type").val(obj.transfer_type_name);

                $("#loan_id").val(obj.loan_id);

                if(obj.transfer_type == 1){
                    $(".account_no").hide();
                    $(".bank_name").hide();
                    $(".branch_code").hide();
                    $(".bank_account_no").hide();
                }else if(obj.transfer_type == 2){
                    $("#account_no").val(obj.account_id);
                    $(".account_no").show(obj.account_id);
                    $(".bank_name").hide();
                    $(".branch_code").hide();
                    $(".bank_account_no").hide();
                }else if(obj.transfer_type == 3){
                    $(".account_no").hide();
                    $(".bank_name").show();
                    $(".branch_code").show();
                    $(".bank_account_no").show();
                    $("#bank_name").val(obj.bank_name);
                    $("#branch_code").val(obj.branch_code);
                    $("#bank_account_no").val(obj.account_no);
                }
                
                
                // $('#dividend_bank_id').val(obj.transfer_bank_id);
                // //$('#dividend_bank_branch_id').val(obj.dividend_bank_branch_id);
                // $('#dividend_acc_num').val(obj.transfer_bank_account_id);
                
                // $('#pay_type_'+obj.transfer_type).attr('checked', true);
                // change_pay_type();

                // list_account(obj.transfer_account_id);
                $('#transfer_modal').modal('show');
            }
	    });	
    }

    function save_loan_repayment(){
        var id = $('#loan_transfer_repayment').val();
        $.ajax({
            url:base_url+"loan/save_loan_repayment_approve",
            method:"post",
            data:{id:id},
            dataType:"text",
            success:function(data)
            {
                if(data=="success"){
                    swal("อนุมัติสำเร็จ", "อนุมัติรายการโอนเงินเรียบร้อย", "success");
                    setTimeout(() => {
							location.reload();
						}, 500);
                }else{
                    swal("เกิดข้อผิดพลาด", "ติดต่อผู้ดูแลลระบบ", "warming");
                }
            }
	    });	
    }
</script>