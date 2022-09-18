<?php
function chkBrowser($nameBroser){
    return preg_match("/".$nameBroser."/",$_SERVER['HTTP_USER_AGENT']);
}
?>
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
			margin-bottom: 5px;
		}
		
		input[type=checkbox], input[type=radio] {
			margin: 11px 0 0;
		}
	</style>
<h1 style="margin-bottom: 0">ส่งหักรายเดือน</h1>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">

    </div>

</div>
    <div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
            <div class="panel panel-body" style="padding-top:0px !important;">                                
				<div class="" style="padding-top:0;">
					<h3></h3>
					<div class="g24-col-sm-24">
						<div class="row">
							<div class="form-group g24-col-sm-24">
								<label class="g24-col-sm-6 control-label" for="form-control-2">รหัสสมาชิก</label>
								<div class="g24-col-sm-6" >
									<div class="input-group">											
										<input id="mem_id" name="member_id"  class="form-control" type="text" value="<?php echo @$_GET['member_id'];?>" onkeypress="check_member_id();">
										<span class="input-group-btn">
											<a data-toggle="modal" data-target="#myModal" id="test" class="fancybox_share fancybox.iframe" href="#">
												<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
											</a>
										</span>	
									</div>										
								</div>
							</div>
						</div>
					</div>
						
					<form id="form2" method="POST" action="<?php echo base_url(PROJECTPATH.'/save_money/save_deposit_month'); ?>"> 
						<input id="member_id" name="member_id"  class="form-control" type="hidden" value="<?php echo @$row_member['member_id'];?>">
						<div class="g24-col-sm-24">
							<div class="row">
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-6 control-label " for="form-control-2">เลขที่บัญชีเงินฝาก</label>
									<div class="g24-col-sm-6">
										<select id="account_id" name="account_id" class="form-control">
											<option value="">เลือกบัญชี</option>
											<?php foreach($row_accounts as $key => $value){ ?>
												<option value="<?php echo $value["account_id"]; ?>" data-account-name="<?php echo $value["account_name"]; ?>"><?php echo $this->center_function->format_account_number($value['account_id']); ?></option>
											<?php } ?>
										</select>
									</div>
									
									<label class="g24-col-sm-2 control-label " for="form-control-2">ชื่อบัญชี</label>
									<div class="g24-col-sm-8">
									  	<input id="account_name" name="account_name" class="form-control" type="text" value=""  readonly>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-6 control-label " for="form-control-2">เงินเดือน+รายได้อื่นๆ</label>
									<div class="g24-col-sm-6">
									  <input id="salary" name="salary" class="form-control" type="text" value="<?php echo @$row_member['salary_income'];?>"  readonly>
									</div>
								</div>		
							</div>
							<div class="row">
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-6 control-label" for="form-control-2">การหักส่ง</label>
									<div class="g24-col-sm-6">
										<span>
											<input type="radio" name="deduction_type" id="deduction_type_0" value="0" onclick="change_type()" checked> หักส่งรายเดือน &nbsp;&nbsp;
											<input type="radio" name="deduction_type" id="deduction_type_1" value="1" onclick="change_type()"> งดส่ง &nbsp;&nbsp;
										</span>
									</div>
								</div>		
							</div>
							<div class="row">
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-6 control-label " for="form-control-2"></label>
									<label class="g24-col-sm-1 control-label text-left" for="form-control-2" style="white-space: nowrap;">เริ่มเดือน</label>
									<div class="g24-col-sm-4">
										<select id="month" name="month" class="form-control">
											<?php foreach($month_arr as $key => $value){ ?>
												<option value="<?php echo $key; ?>" <?php echo $key==((int)date('m'))?'selected':''; ?>><?php echo $value; ?></option>
											<?php } ?>
										</select>
									</div>
									<label class="g24-col-sm-1 control-label" for="form-control-2">ปี</label>
									<div class="g24-col-sm-2">
										<select id="year" name="year" class="form-control">
											<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
												<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
											<?php } ?>
										</select>
									</div>
									<label class="g24-col-sm-2 control-label show_total_amount" for="form-control-2">จำนวนเงิน</label>
									<div class="g24-col-sm-6 show_total_amount">
									  <input id="total_amount" name="total_amount"  class="form-control " type="text" value="" onkeyup="format_the_number_decimal(this)" maxlength="12">
									</div>									
								</div>		
							</div>
							
							<div class="row">
								<div class="g24-col-sm-24">
									<div class="form-group g24-col-sm-24">
										<div class="g24-col-sm-24 text-center m-t-2">
											<button type="button" id="submit_btn" class="btn btn-primary btn-after-input" onclick="check_form()" style="width: 110px;"><span class="icon icon-save" style="margin-top: 1px;"></span><span> บันทึก</span></button>
										</div>
									</div>												
								</div>
							</div>
						</div>
					</form>						
				</div>
                
                <div class="g24-col-sm-24 m-t-1">
					<h3>ประวัติการทำรายการ</h3>
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-bordered table-striped table-center">
                            <thead>
                            <tr class="bg-primary">
								<th>วันที่ทำรายการ</th>
								<th>เลขที่บัญชี</th>
                                <th>รายการ</th>
                                <th>ยอดเงิน</th>
                                <th>หักส่งเดือน/ปี</th>
                               <th width="20%">ผู้ทำรายการ</th>
                            </tr>
                            </thead>
                            <tbody id="table_first">
                            <?php
							//echo '<pre>'; print_r($data); echo '</pre>';
							$arr_type = array('0'=>'หักส่งรายเดือน','1'=>'งดส่ง ');
							if(!empty($data)){
								foreach($data as $key => $row){
                            ?>
                                <tr>
									<td><?php echo @$this->center_function->ConvertToThaiDate(@$row['createdatetime']); ?></td>
									<td><?php echo $this->center_function->format_account_number($row['account_id']); ?></td>
                                    <td align="left">
										<?php 
											echo @$arr_type[@$row['deduction_type']];
										?>
									</td>
                                    <td align="left"><?php echo (@$row['deduction_type'] == '1')?'-':number_format(@$row['total_amount'],2); ?></td>
                                    <td align="left"><?php echo $month_arr[@$row['deduction_month']].'/'.@$row['deduction_year'];?></td>
                                    <td align="center"><?php echo @$row['user_name']; ?></td>
                                </tr>
                            <?php 
								}
							}		
							?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <?php echo @$paging; ?>
        </div>
    </div>
    </div>
</div>
<?php $this->load->view('search_member_new_modal'); ?>
<script>
	$(document).ready(function() {
		$("#account_id").change(function() {
			account_name = $('option:selected', this).attr('data-account-name');
			$("#account_name").val(account_name);
		});
	});

    function check_form(){
		$("#submit_btn").prop('disabled', true);
		var text_alert = '';
		var member_id = $('#member_id').val();
		var month = $('#month').val();
		var year = $('#year').val();
		var deduction_type = $('input[name=deduction_type]:checked').val();
		var refrain_start_month = $('#refrain_start_month').val();
		var refrain_start_year = $('#refrain_start_year').val();
		var refrain_end_month = $('#refrain_end_month').val();
		var refrain_end_year = $('#refrain_end_year').val();
		var account_id = $("#account_id").val();
		var total_amount = $("#total_amount").val();
		var id_to_focus = [];
		var i=0;

		warning_message = "";
		if(account_id == "") {
			warning_message += "\nกรุณาเลือกเลขที่บัญชี";
		}
		if(total_amount == "" && $('#deduction_type_0').is(':checked')) {
			warning_message += "\nกรุณากรอกจำนวนเงิน";
		}

		if(warning_message == "") {
			$.post(base_url+"save_money/check_deduction_month",
			{
				member_id: member_id,
				deduction_month: month,
				deduction_year: year,
				deduction_type: deduction_type,
				account_id: account_id
			}
			, function(result){
				console.log(result);
				if(result == 'ok'){
					if($('#total_amount').val() == '' && $('#deduction_type_0').is(':checked')){
						swal('กรุณากรอกจำนวนเงิน','','warning');
					}else{
						$('#form2').submit();
					}
				}else{
					swal(result,'','warning');
				}
				$("#submit_btn").prop('disabled', false);
			});
		} else {
			swal('ไม่สามารถทำรายการได้',warning_message,'warning');
			$("#submit_btn").prop('disabled', false);
		}
	}

	function change_type(){
		if($('#deduction_type_0').is(':checked')){
			$('.show_total_amount').show();
		}else if($('#deduction_type_1').is(':checked')){
			$('.show_total_amount').hide();
		}
	}

	function format_the_number_decimal(ele){
		var value = $('#'+ele.id).val();
		value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
		var num = value.split(".");
		var decimal = '';	
		var num_decimal = '';	
		if(typeof num[1] !== 'undefined'){
			if(num[1].length > 2){
				num_decimal = num[1].substring(0, 2);
			}else{
				num_decimal =  num[1];
			}
			decimal =  "."+num_decimal;
			
		}
		
		if(value!=''){
			if(value == 'NaN'){
				$('#'+ele.id).val('');
			}else{		
				value = parseInt(num[0]);
				value = value.toLocaleString()+decimal;
				$('#'+ele.id).val(value);
			}			
		}else{
			$('#'+ele.id).val('');
		}
	}	
	
	function check_member_id(){
		var member_id = $('#mem_id').val();
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			$.post(base_url+"save_money/check_member_id", 
			{	
				member_id: member_id
			}
			, function(result){
				obj = JSON.parse(result);
				console.log(obj.member_id);
				mem_id = obj.member_id;
				if(mem_id != undefined){
					document.location.href = base_url+'/save_money/deposit_month?member_id='+mem_id	
				}else{					
					swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning'); 
				}
			});		
		}	
	}
</script>
