<style>
	.modal-dialog {
        width: 700px;
    }
</style>
<div class="layout-content">
    <div class="layout-content-body">
		<?php
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		?>
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
			.right {
				text-align: right;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			label{
				padding-top:7px;
			}
		</style>

		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">รายงานการทำรายการ</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/report_deposit_data/coop_report_transaction_preview'); ?>" id="form1" method="GET" target="_blank">
					<h3></h3>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> วันที่ </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
						<label class="g24-col-sm-1 control-label right"> ถึง </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="end_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
					</div>
					
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> ประเภทบัญชี </label>
						<div class="g24-col-sm-9">
							<select class="form-control m-b-1" id="type_id"  name="type_id" >
								<option value="">เลือกประเภทบัญชี</option>
								<option value="all">ทั้งหมด</option>
								<?php foreach($type_id as $key => $value){ ?>
									<option value="<?php echo $value['type_id']; ?>" <?php echo $value['type_id']==@$row['type_id']?'selected':''; ?>><?php echo $value['type_code']." ".$value['type_name']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> รหัสบัญชี </label>
						<div class="g24-col-sm-18">
							<label style="font-weight: normal;">
								<input type="checkbox" id="transaction_list_all" name="transaction_list_all" value="1"> (ทั้งหมด)
							</label>
							&nbsp;
							<?php  foreach($transaction_lists as $key => $transaction_list){ ?>
								<label style="font-weight: normal;">
									<input type="checkbox" name="transaction_lists[]" value="<?php echo $transaction_list; ?>"> <?php echo str_replace("'", "", $transaction_list); ?>
								</label>
								&nbsp;
							<?php } ?>
						</div>
					</div>
					
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> ประเภทการทำรายการ </label>
						<div class="g24-col-sm-9">
							<select class="form-control m-b-1" id="type_transaction"  name="type_transaction" >
								<option value="">เลือกประเภทการทำรายการ</option>
								<?php 
								if(!empty($type_transaction)){
									foreach(@$type_transaction as $key_transaction  => $value_transaction ){ ?>
									<option value="<?php echo $key_transaction; ?>"><?php echo $value_transaction; ?></option>
								<?php 
									}
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24 show_user">
						<label class="g24-col-sm-6 control-label right"> ผู้ทำรายการ </label>
						<div class="g24-col-sm-9">
							<select class="form-control m-b-1" id="user_id"  name="user_id" >
								<option value="">เลือกผู้ทำรายการ</option>
								<?php 
								if(!empty($row_user)){
									foreach(@$row_user as $key_user  => $value_user ){ ?>
									<option value="<?php echo $value_user['user_id']; ?>"><?php echo $value_user['user_name']; ?></option>
								<?php 
									}
								}
								?>
							</select>							
						</div>
					</div>	
					<div class="form-group g24-col-sm-24 show_member">
						<label class="g24-col-sm-6 control-label right"> ผู้ทำรายการ </label>
						<div class="g24-col-sm-9">	
							<div class="input-group">
								<input id="member_id" name="member_id" class="form-control member_id" type="text" value="" onkeypress="check_member_id();" placeholder="รหัสสมาชิก">
								<span class="input-group-btn">
									<a data-toggle="modal" data-target="#search_member_modal" id="test" class="fancybox_share fancybox.iframe" href="#">
										<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
									</a>
								</span>	
							</div>
							
						</div>
					</div>
					
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-5 control-label right"></label>
						<div class="g24-col-sm-10">
							<input type="button" class="btn btn-primary" style="width:100%" value="รายงานการทำรายการ" onclick="check_empty()">
							<!--<input type="submit" class="btn btn-primary" style="width:100%" value="รายงานการทำรายการ">-->
						</div>
					</div>
				</form>				
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="search_member_modal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">ข้อมูลสมาชิก</h4>
        </div>
        <div class="modal-body">
       		<div class="input-with-icon">
					  <!-- <input class="form-control input-thick pill m-b-2" type="text" placeholder="กรอกเลขทะเบียนหรือชื่อ-สกุล" name="search_text" id="search_mem">
            <span class="icon icon-search input-icon"></span> -->
              <div class="row">
              <div class="col">

                <label class="col-sm-2 control-label">รูปแบบค้นหา</label>
                <div class="col-sm-4">
                  <div class="form-group">
                    <select id="search_list" name="search_list" class="form-control m-b-1">
                      <option value="">เลือกรูปแบบค้นหา</option>
                      <option value="member_id">รหัสสมาชิก</option>
                      <option value="id_card">หมายเลขบัตรประชาชน</option>
                      <option value="firstname_th">ชื่อสมาชิก</option>
                      <option value="lastname_th">นามสกุล</option>
                    </select>
                  </div>
                </div>
                
                <label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
                <div class="col-sm-4">
                  <div class="form-group">
                    <div class="input-group">
                      <input id="search_text" name="search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>">
                      <span class="input-group-btn">
                        <button type="button" id="member_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                      </span>	
                    </div>
                  </div>
                </div>	
              </div>
            </div>
					</div>

			<div class="bs-example" data-example-id="striped-table">
					  <table class="table table-striped">
              <tbody id="result_member">
              </tbody>
					  </table>
					</div>

        </div>
        <div class="modal-footer">
          <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
        </div>
      </div>
    </div>
</div>
  
<script>	
	var base_url = $('#base_url').attr('class');
	$( document ).ready(function() {
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
		
		$('#member_search').click(function(){
			if($('#search_list').val() == '') {
				swal('กรุณาเลือกรูปแบบค้นหา','','warning');
			} else if ($('#search_text').val() == ''){
				swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
			} else {
				$.ajax({  
				  url: base_url+"ajax/search_member_by_type_to_input",
				  method:"post",  
				  data: {
					search_text : $('#search_text').val(), 
					search_list : $('#search_list').val()
				  },  
				  dataType:"text",  
				  success:function(data) {
					$('#result_member').html(data);  
				  }  ,
				  error: function(xhr){
					  console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
				  }
			  });  
		  }
		});
		
		
		$(".show_user").hide();
		$(".show_member").hide();
		$("#type_transaction").change(function() {
			var type_transaction = $(this).val();
			if(type_transaction == '1'){
				$(".show_user").show();
				$(".show_member").hide();
			}else if(type_transaction == '2'){
				$(".show_user").hide();
				$(".show_member").show();
			}
		});
	});
	
	function check_empty(){
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		var type_id = $('#type_id').val();
		var user_id = $('#user_id').val();
		var member_id = $('#member_id').val();
		var type_transaction = $('#type_transaction').val();
		var transaction_list_all = $('#transaction_list_all:checked').val();
		
		var transaction_lists = [];
		$('input[name=transaction_lists\\[\\]]:checked').each(function() {
			transaction_lists.push($(this).val());
		});
		
		if(type_id == ''){
			swal("กรุณาเลือกประเภทบัญชี");
			return false;
		}
		$.ajax({
			url: base_url+'/report_deposit_data/check_report_transaction',	
			 method:"post",
			 data:{ 
				 start_date: start_date, 
				 end_date: end_date,
				 type_id: type_id,
				 user_id: user_id,
				 member_id: member_id,
				 type_transaction: type_transaction,
				 transaction_list_all: transaction_list_all,
				 "transaction_lists[]": transaction_lists
			 },
			 dataType:"text",
			 success:function(data){
				console.log(data);
				if(data == 'success'){
					$('#form1').submit();
				}else{
					$('#alertNotFindModal').appendTo("body").modal('show');
				}
			 }
		});
	}
	
	function check_member_id() {
	   var member_id = $('.member_id').first().val();
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
			 	$(".member_id").val(mem_id);
			}else{					
			  swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning'); 
			}
		  });		
		}
	}
	
	function get_data(member_id){
		$(".member_id").val(member_id);
		$('#search_member_modal').modal('hide');
		$('#search_list').val('');
		$('#search_text').val('');
		$('#result_member').html('');
	}
	
</script>


