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
		width: 70% !important;
	}
	.modal-dialog-alert {
		margin:auto;
		margin-top:7%;
		width: 400px;
	}
	.modal.fade {
	  z-index: 10000000 !important;
	}
	.form-group{
		margin-bottom: 5px;
	}
	
	.text-p{
		font-family: upbean;
	}
</style>
<h1 style="margin-bottom: 0">งดต้น/ดอกเบี้ยเงินกู้</h1>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php 
		if(@$member_id != ''){
		?>		
		<a>
			<button class="btn btn-primary btn-lg bt-add" type="button" style="margin-right:10px;" id="temporary" onclick="add_item();">เพิ่มรายการ</button>
		</a>
		
		<?php 
		}
		?>
	</div>
</div>
<div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                <?php $this->load->view('search_member_new'); ?>
				<div class="" style="padding-top:0;">
				<div class="g24-col-sm-24">
					<div class="form-group g24-col-sm-8">
						<label class="g24-col-sm-10 control-label ">ส่งหุ้นแล้ว</label>
						<div class="g24-col-sm-14">
							<input class="form-control" type="text" value="<?php echo (@$member_id == '')?'':number_format(@$cal_share,2);?>"  readonly>
						</div>
					</div>
					<div class="form-group g24-col-sm-8">
						<label class="g24-col-sm-10 control-label ">ภาระหนี้รวม</label>
						<div class="g24-col-sm-14">
							<input class="form-control" type="text" value="<?php echo (@$member_id == '')?'':number_format(@$sum_debt_balance,2);?>"  readonly>
						</div>
					</div>
					<div class="form-group g24-col-sm-8">
						<label class="g24-col-sm-10 control-label ">ภาระค้ำประกัน</label>
						<div class="g24-col-sm-14">
							<input class="form-control" type="text" value="<?php echo (@$member_id == '')?'':number_format(@$sum_guarantee_balance,2);?>"  readonly>
						</div>
					</div>
				</div>

				<div class="g24-col-sm-24">
					<div class="form-group g24-col-sm-8">
						<label class="g24-col-sm-10 control-label ">สถานะ</label>
						<div class="g24-col-sm-14">
							<!-- <input class="form-control" type="text" id="type_refrain_name" name="type_refrain_name" value="<?php echo @$status_refrain;?>" readonly> -->
							<input class="form-control" type="text" id="type_refrain_name" name="type_refrain_name" value="-" readonly>
							<input class="form-control" type="hidden" id="type_refrain" name="type_refrain" value="<?php echo @$type_refrain;?>" readonly>
						</div>
					</div>                 
      			</div>
		
				<div class="g24-col-sm-24 m-t-1">
				  <div class="bs-example" data-example-id="striped-table">
					 <table class="table table-bordered table-striped table-center">
					 <thead> 
						<tr class="bg-primary">
							<th>ลำดับ</th>
							<th>วันที่ทำรายการ</th>
							<th>สัญญาเลขที่</th>
							<th>ประเภท</th>
							<th>เดือน/ปี</th>
							<th>รูปแบบงด</th>
							<th width="30%">ผู้ทำรายการ</th> 
							<th></th>  
						</tr> 
					 </thead>
						<tbody id="table_first">
						  <?php
						  $refrain_status = ["", "งดต้น", "งดดอกเบี้ย", "งดต้นและดอกเบี้ย"];
						  $i = 0;
						  foreach($data as $key => $row){ 
							$i++;
							$mmyy = "";
							$mmyy .= (@$row['month_start'] != '')?@$month_arr[@$row['month_start']]."/".@$row['year_start']:'';
							$mmyy .= (@$row['month_end'] != '')?" ถึง ".@$month_arr[@$row['month_end']]."/".@$row['year_end']:'';
						   ?>
						 
						  <tr> 
						  <td><?php echo @$i; ?></td>
						  <td><?php echo @$this->center_function->ConvertToThaiDate(@$row['createdatetime']); ?></td>
						  <td><?php echo @$row['contract_number']; ?></td> 						  
						  <td><?php echo $row['period_type'] == 1 ? "งดชั่วคราว" : "งดถาวร";?></td>
						  <td><?php echo $mmyy; ?></td>
						  <td><?php echo @$refrain_status[$row['refrain_type']]; ?></td>					  
						  <td><?php echo @$row['user_name']; ?></td> 
						  <td style="padding:0px;vertical-align:middle;">
							 <span class="text-edit edit" onclick="edit_coop_refrain_loan('<?php echo @$row['refrain_loan_id'] ?>')" title="แก้ไข"><span style="cursor: pointer;" class="icon icon-edit"></span></span> | 
							 <span class="text-del del"  onclick="del_coop_refrain_loan('<?php echo @$row['refrain_loan_id'] ?>')" title="ลบ"><span class="icon icon-trash-o"></span></span>
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

	</div>
</div>

<div class="modal fade" id="addItemModal"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
      <div class="modal-content">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h2 class="modal-title">เงินต้น/ดอกเบี้ยเงินกู้</h2>
        </div>
        <div class="modal-body center">
		<form action="<?php echo base_url(PROJECTPATH.'/refrain_loan/save_refrain_loan'); ?>" method="POST" id="form_save" data-toggle="validator" class="g24 form form-horizontal">
			<input class="form-control" type="hidden" id="contract_number" name="contract_number" value="">						
			<input class="form-control" type="hidden" id="member_id" name="member_id" value="">	
			<input class="form-control" type="hidden" id="refrain_loan_id" name="refrain_loan_id" value="">	
			<div class="form-group g24-col-sm-24">
				<label class="g24-col-sm-3 control-label right"> สัญญาเงินกู้ </label>
				<div class="g24-col-sm-9">
					<select id="loan_id" name="loan_id" class="form-control">
							<option value="">เลือกสัญญาเงินกู้</option>
						<?php foreach($rs_loan AS $key=>$row_loan){ ?>
							<option value="<?php echo @$row_loan['id']; ?>"><?php echo @$row_loan['contract_number']; ?></option>
						<?php } ?>
					</select>
				</div>				
				<label class="g24-col-sm-3 control-label right"> เงินต้นคงเหลือ </label>
				<div class="g24-col-sm-9">
					<input class="form-control" type="text" id="loan_amount_balance" name="loan_amount_balance" value=""  readonly>					
				</div>	
			</div>
			<div class="form-group g24-col-sm-24">
				<label class="g24-col-sm-3 control-label right"> ประเภท </label>
				<div class="g24-col-sm-5">
					<label><input type="radio" name="period_type" id="period_type_1" value='1' <?php ?>checked> งดชั่วคราว</label>
				</div>
				<div class="g24-col-sm-5">
					<label><input type="radio" name="period_type" id="period_type_2" value='2'> งดถาวร</label>
				</div>
			</div>
			<div class="form-group g24-col-sm-24">
				<label class="g24-col-sm-3 control-label right"> งดต้น/ดอกเบี้ยปี เริ่มต้น </label>
				<div class="g24-col-sm-4">
					<select id="year_start" name="year_start" class="form-control">
						<option value="">เลือกปี</option>
						<?php for($i=((date('Y')+543)-1); $i<=((date('Y')+543)+5); $i++){ ?>
							<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
						<?php } ?>
					</select>
				</div>
				
				<label class="g24-col-sm-1 control-label right"> เดือน </label>
				<div class="g24-col-sm-4">
					<select id="month_start" name="month_start" class="form-control">
						<option value="">เลือกเดือน</option>
						<?php foreach($month_arr as $key => $value){ ?>
							<option value="<?php echo $key; ?>" <?php echo $key==((int)date('m'))?'selected':''; ?>><?php echo $value; ?></option>
						<?php } ?>
					</select>
				</div>	
				
				<label class="g24-col-sm-3 control-label right"> ถึงปี </label>
				<div class="g24-col-sm-4">
					<select id="year_end" name="year_end" class="form-control period-type-1-input">
						<option value="">เลือกปี</option>
						<?php for($i=((date('Y')+543)-1); $i<=((date('Y')+543)+5); $i++){ ?>
							<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
						<?php } ?>
					</select>
				</div>
				
				<label class="g24-col-sm-1 control-label right"> เดือน </label>
				<div class="g24-col-sm-4">
					<select id="month_end" name="month_end" class="form-control period-type-1-input">
						<option value="">เลือกเดือน</option>
						<?php foreach($month_arr as $key => $value){ ?>
							<option value="<?php echo $key; ?>" <?php echo $key==((int)date('m'))?'selected':''; ?>><?php echo $value; ?></option>
						<?php } ?>
					</select>
				</div>	
			</div>
			<div class="form-group g24-col-sm-24">
				<label class="g24-col-sm-3 control-label right"> รูปแบบงด </label>
				<div class="g24-col-sm-5">
					<label><input type="radio" name="refrain_type" id="refrain_type_1" value='1' checked> งดต้น</label>
				</div>
				<div class="g24-col-sm-5">
					<label><input type="radio" name="refrain_type" id="refrain_type_2" value='2'> งดดอกเบี้ย</label>
				</div>
				<div class="g24-col-sm-5">
					<label><input type="radio" name="refrain_type" id="refrain_type_3" value='3'> งดต้นดอกเบี้ย</label>
				</div>
			</div>
			
		</form>
          <button class="btn btn-info m-t-2" onclick="save_form();">บันทึก</button>
        </div>
      </div>
    </div>
</div>


<div class="modal fade" id="alert"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-alert">
      <div class="modal-content">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h2 class="modal-title">แจ้งเตือน</h2>
        </div>
        <div class="modal-body center">
			<form action="" method="POST" id="form_alert">	
				<div class="form-group g24-col-sm-24" style="font-size:24px;">	
					<br>
					<p class="g24-col-sm-24 text-p" id="text_alert"></p>
					<br>					
					<br>					
				</div>
				<button class="btn btn-info" data-dismiss="modal">ปิดหน้าต่าง</button>					
			</form>  
        </div>
      </div>
    </div>
</div>

<?php $this->load->view('search_member_new_modal'); ?>
<script>

	$( document ).ready(function() {	
		$( "#loan_id" ).change(function() {		  
			var loan_id = $(this).val();
			get_loan_amount_balance(loan_id)
		});
		$('input[name=period_type]').change(function(){
			var value = $( 'input[name=period_type]:checked' ).val();
			if(value == 1) {
				$(".period-type-1-input").attr("disabled", false);
			} else {
				$(".period-type-1-input").attr("disabled", true);
			}
		});
	});

	function get_loan_amount_balance(loan_id){
		$.post(base_url+"refrain_loan/get_loan_amount_balance", 
		{	
			loan_id: loan_id
		}
		, function(data){
			obj = JSON.parse(data);
			//console.log(obj);
			$("#loan_amount_balance").val(obj.loan_amount_balance);
			$("#contract_number").val(obj.contract_number);
			$("#member_id").val(obj.member_id);
			
		});		
	}	

	function add_item(){	
		$('#addItemModal').modal('show');
		$("#refrain_loan_id").val('');
		$("#member_id").val('');
		$("#loan_id").val('');
		$("#year_start").val('');
		$("#month_start").val('');
		$("#year_end").val('');
		$("#month_end").val('');
		$("#contract_number").val('');
		$("#loan_amount_balance").val('');
		$('#refrain_type_1').attr('checked',true);	
		
	}	

	function save_form(){	
		var member_id = $("#member_id").val();
		var year_start = $("#year_start").val();
		var month_start = $("#month_start").val();
		var year_end = $("#year_end").val();
		var month_end = $("#month_end").val();
		var loan_id = $("#loan_id").val();
		var text_alert = '';
		var refrain_loan_id = $("#refrain_loan_id").val();
		var period_type = $('input[name=period_type]:checked').val();
		var refrain_type = $('input[name=refrain_type]:checked').val();

		if($.trim($('#loan_id').val())== ''){			
			text_alert = ' กรุณาเลือกสัญญาเงินกู้';
			$('#text_alert').html(text_alert);
			$('#alert').modal('show');			
		}else if($.trim($('#year_start').val())== ''){			
			text_alert = ' กรุณาเลือกปีที่เริ่มงดต้น/ดอกเบี้ย';
			$('#text_alert').html(text_alert);
			$('#alert').modal('show');			
		}else if($.trim($('#month_start').val())== ''){			
			text_alert = ' กรุณาเลือกเดือนที่เริ่มงดต้น/ดอกเบี้ย';
			$('#text_alert').html(text_alert);
			$('#alert').modal('show');			
		}else if($.trim($('#year_end').val())== '' && period_type == 1){
			text_alert = ' กรุณาเลือกปีที่สิ้นสุดงดต้น/ดอกเบี้ย';
			$('#text_alert').html(text_alert);
			$('#alert').modal('show');			
		}else if($.trim($('#month_end').val())== '' && period_type == 1){
			text_alert = ' กรุณาเลือกเดือนที่สิ้นสุดงดต้น/ดอกเบี้ย';
			$('#text_alert').html(text_alert);
			$('#alert').modal('show');			
		}else{
			if(period_type == 1) {
				$.post(base_url+"refrain_loan/check_refrain_loan",
				{
					loan_id: loan_id,
					year_start: year_start,
					month_start: month_start,
					year_end: year_end,
					month_end: month_end,
					refrain_loan_id: refrain_loan_id
				}
				, function(data){
					// console.log(data);
					var result = JSON.parse(data);
					if(result.action == "ok"){
						$('#form_save').submit();
						swal.close();
					}else{
						// $('#text_alert').html(data);
						// $('#alert').modal('show');	
						$('#addItemModal').modal('hide');
						swal({
							title: result.msg,
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
								if(result.action == "warning"){
									$('#form_save').submit();
								}
								swal.close();
							} else {
								$('#addItemModal').modal('show');
							}
						});
					}
				});
			} else {
				var text_warning = refrain_type == 1 ? "ต้องการงดต้นถาวร" : (refrain_type == 2 ? "ต้องการงดดอกเบี้ยถาวร" : "ต้องการงดเงินต้นและดอกเบี้ยถาวร");
				$('#addItemModal').modal('hide');
				swal({
					title: text_warning,
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
						$('#form_save').submit();
						swal.close();
					} else {
						$('#addItemModal').modal('show');
					}
				});
			}
		}	
	}
	
	function del_coop_refrain_loan(id){	
		var member_id ='<?php echo $member_id; ?>';
		swal({
			title: "คุณต้องการที่จะลบ",
			text: "",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ลบ',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: false,
			closeOnCancel: true
		},
		function(isConfirm) {
			if (isConfirm) {			
				$.ajax({
					url: base_url+'/refrain_loan/del_coop_refrain_loan',
					method: 'POST',
					data: {
						'id': id
					},
					success: function(msg){
						if(msg == 1){
						  document.location.href = base_url+'refrain_loan?member_id='+member_id;
						}else{

						}
					}
				});
			} else {
				
			}
		});
		
	}
	
	function edit_coop_refrain_loan(id){
		$('#addItemModal').modal('show');
		
		$.post(base_url+"refrain_loan/get_refrain_loan", 
			{	
				refrain_loan_id: id
			}
			, function(data){
				// console.log(data);
				var result = JSON.parse(data);
				//console.log(result);
				$("#refrain_loan_id").val(result.refrain_loan_id);
				$("#member_id").val(result.member_id);
				$("#loan_id").val(result.loan_id);
				$("#year_start").val(result.year_start);
				$("#month_start").val(result.month_start);
				$("#year_end").val(result.year_end);
				$("#month_end").val(result.month_end);
				$("#contract_number").val(result.contract_number);
				$("#loan_amount_balance").val(addCommas(parseFloat(result.loan_amount_balance).toFixed(2)));
				if(result.refrain_type == '1'){
					$('#refrain_type_1').attr('checked',true);
				}else if(result.refrain_type == '2'){
					$('#refrain_type_2').attr('checked',true);
				}else if(result.refrain_type == '3'){
					$('#refrain_type_3').attr('checked',true);
				}
				if(result.peroid_type == 1) {
					$('#period_type_1').attr('checked',true);
					$(".period-type-1-input").attr("disabled", false);
				} else {
					$('#period_type_2').attr('checked',true);
					$(".period-type-1-input").attr("disabled", true);
				}
			});	
	}

	function removeCommas(str) {
		return(str.replace(/,/g,''));
	}
	function addCommas(x){
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
</script>