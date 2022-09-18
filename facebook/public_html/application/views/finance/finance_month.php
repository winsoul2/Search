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
  .form-group{
    margin-bottom: 5px;
  }
</style>
<h1 style="margin-bottom: 0">รายการเรียกเก็บประจำเดือน</h1>
<?php $this->load->view('breadcrumb'); ?>
<div class="row gutter-xs">
	<div class="col-xs-12 col-md-12">
		<div class="panel panel-body" style="padding-top:0px !important;">
			<form method="GET" action="<?php echo base_url(PROJECTPATH.'/finance/finance_all_money_report'); ?>" target="_blank">
			<h3></h3>
			<div class="form-group g24-col-sm-24">
				<input type="hidden" name="yymm_now" id="yymm_now" value="<?php echo (date('Y')+543).date('m');?>">
				<label class="g24-col-sm-6 control-label right"> เดือน </label>
				<div class="g24-col-sm-4">
					<select id="month" name="month" class="form-control">
						<?php foreach($month_arr as $key => $value){ ?>
						<?php 
							if($key < ((int)date('m'))){
								//$check_disabled = "disabled";		
								$check_disabled = "";	
							}else{
								$check_disabled = "";
							}
						?>
							<option value="<?php echo $key; ?>" <?php echo $key==((int)date('m'))?'selected':''; ?> <?php echo $check_disabled;?>><?php echo $value; ?></option>
						<?php } ?>
					</select>
				</div>
				<label class="g24-col-sm-1 control-label right"> ปี </label>
				<div class="g24-col-sm-4">
					<select id="year" name="year" class="form-control">
						<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
							<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group g24-col-sm-24">
				<label class="g24-col-sm-6 control-label right"> สังกัดหน่วยงาน </label>
				<div class="g24-col-sm-9">
					<select name="department" id="department" onchange="change_mem_group('department', 'faction')" class="form-control">
						<option value="">เลือกข้อมูล</option>
						<?php 
							foreach($row_mem_group as $key => $value){
							?>
							<option value="<?php echo $value['id']; ?>"><?php echo $value['mem_group_name']; ?></option>
						<?php 
						} ?>
					</select>
				</div>
				<!---<label class="g24-col-sm-1 control-label right"> อำเภอ </label>
				<div class="g24-col-sm-4">
					<select name="faction" id="faction" onchange="change_mem_group('faction','level')" class="form-control">
						<option value="">เลือกข้อมูล</option>
					</select>
				</div>
				-->
			</div>
			<!--<div class="form-group g24-col-sm-24">
				<label class="g24-col-sm-6 control-label right"> หน่วยงานย่อย </label>
				<div class="g24-col-sm-4">
					<select name="level" id="level" class="form-control">
						<option value="">เลือกข้อมูล</option>
					</select>
				</div>
			</div>-->
			<div class="form-group g24-col-sm-24">
				<label class="g24-col-sm-6 control-label right"></label>
				<div class="g24-col-sm-9">
					<input type="button" id="processing" onclick="confirm_user()" class="btn btn-primary" style="width:100%" value="รายการเรียกเก็บรายเดือน">
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="in_process" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
          <h4 class="modal-title">ระบบประมวลผล</h4>
        </div>
        <div class="modal-body">
			
			<div class="progress">
				<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%" id="percent">
				0%
				</div>
			</div>
			<div id="c_list" style="    border: #9e9e9e solid black;
    height: 300px;overflow-y: scroll;">
				<ul style="list-style-type:none" id="item_list">
					<li>รวบรวมข้อมูลที่เกี่ยวข้อง</li>
				</ul>  
			</div>

        </div>
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
        </div>
      </div>
    </div>
</div>

<!-- MODAL CONFIRM USER-->
<div class="modal fade" id="modal_confirm_user" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">ยืนยันสิทธิ์การใช้งาน</h4>
        </div>
        <div class="modal-body">
          	<p>ชื่อผู้มีสิทธิ์อนุมัติ</p>
		  	<input type="text" class="form-control" id="confirm_user">
		  	<p>รหัสผ่าน</p>
		  	<input type="password" class="form-control" id="confirm_pwd">
			  <br>
			<!--<input type="hidden" id="transaction_id_err">-->
			<div class="row">
				<div class="col-sm-12 text-center">
					<button class="btn btn-info" id="submit_confirm_user">บันทึก</button>
				</div>
			</div>
        </div>
        <div class="modal-footer">
        </div>
      </div>
    </div>
</div>
<!-- MODAL CONFIRM USER-->

<script>
	var res = window.location.pathname;
	var limit = 50;
	function change_mem_group(id, id_to){
		var mem_group_id = $('#'+id).val();
		$('#level').html('<option value="">เลือกข้อมูล</option>');
		$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/get_mem_group_list',
			data: {
				mem_group_id : mem_group_id
			},
			success: function(msg){
				$('#'+id_to).html(msg);
			}
		});
	}

	// $( "#processing" ).click(function() {
		
		
	// });

	async function state_process(){
		var month = $("#month").val();
		var year =  $("#year").val();
		if(getCookie("proc_month") === month && getCookie("proc_year") === year) {
			if (getCookie('proc_state') === "idle" || getCookie("proc_state") === "finish") {
				return 1;
			} else {
				if (getCookie('proc_state') === "processing") {
					return getCookie("proc_counter") ? parseInt(getCookie("proc_counter")) : 1;
				}
			}
		}else{
			return 1;
		}
	}

	function setCookie(name,value,days) {
		days = typeof days === "undefined" ? 1 : days;
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days*24*60*60*1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value || "")  + expires + "; path=/";
	}

	function getCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}

	function eraseCookie(name) {
		document.cookie = name+'=; Max-Age=-99999999;';
	}

	async function do_process(){
		swal('กำลังประมวลผล โปรดอย่าออกจากหน้านี้');
		$.blockUI({
                message: '',
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff'
                },
                baseZ: 2000,
                bindEvents: false
            });


		var row_total = await get_total_process();
		row_total = parseInt(row_total);
		var key = await state_process();

		if(getCookie("proc_state") === null || getCookie("proc_state") === "finish") {
			setCookie("proc_state", "idle");
		}
		$("#percent").text( "0%");
		$("#percent").data("aria-valuenow", 0); 
		$("#percent").css( "width", "0%" );

		$('#in_process').modal('show');
		jQuery('#in_process').data('bs.modal').options.backdrop = 'static';
		var txt_status = false;
		while (true) {
			// await do_process(key);
			var result = await do_processing(key);
			if(txt_status){
				document.title = ".กำลังประมวลผล";
			}else{
				document.title = "กำลังประมวลผล";
			}
			txt_status = !txt_status;
			reset_time_out(res);
			if(result.result != "next"){
				setCookie("proc_state", "finish");
				swal('ประมวลผลสำเร็จ');
				jQuery('#in_process').data('bs.modal').options.backdrop = '';
				$('#in_process').modal('hide');
				$.unblockUI();
				document.title = "ประมวลผลสำเร็จ";
				alert("ประมวลผลสำเร็จ");
				break;
			}else{
				// console.log(result.item);
				setCookie("proc_state", "processing");
				var item = result.item;
				item.forEach(element => {
					$("#item_list").append("<li>+ รหัสสมาชิก "+element.member_id+" รายการ "+element.deduct_code+" "+element.pay_type+" "+element.pay_amount+"</li>");
					$("#c_list").scrollTop($( "#item_list" ).height());
					$('#c_list').animate({
						scrollTop: $('#item_list').get(0).scrollHeight,
						opacity: "show"
					}, {
						duration: "fast"
					});
				});

			}

			var percent = parseInt(key * limit * 100 / row_total);
			$("#percent").text( percent+"%");
			$("#percent").data("aria-valuenow", percent); 
			$("#percent").css( "width", percent+"%" );

			key++;
		}
	}

	function get_total_process(){
		return new Promise(resolve => {
			var month 		= $("#month").val();
			var year 		= $("#year").val();
			var department 	= $("#department").val();
			$.ajax({
				method: 'GET',
				url: base_url+'finance/finance_all_money_report_count',
				data: {
					department 	: department,
				},
				success: function(msg){
					var obj = JSON.parse(msg);
					resolve(obj.result);
				},
				error: function(xhr,status,error){
					// $('#in_process').modal('hide');
					// swal('ประมวลผลไม่สำเร็จ', 'โปรดลองใหม่อีกครั้ง');
					// $.unblockUI();
				}
			});
		});
	}


	function do_processing(key){
		
		return new Promise(resolve => {
			var month 		= $("#month").val();
			var year 		= $("#year").val();
			var department 	= $("#department").val();
			setCookie("proc_counter", key);
			setCookie("proc_month", month);
			setCookie("proc_year", year);
			$.ajax({
				method: 'GET',
				url: base_url+'finance/finance_all_money_report',
				data: {
					month 		: month,
					year 		: year,
					department 	: department,
					run_current	: key,
					limit		: limit
				},
				success: function(msg){
					var obj = JSON.parse(msg);
					resolve(obj);
				},
				error: function(xhr,status,error){
					$('#in_process').modal('hide');
					swal('ประมวลผลไม่สำเร็จ', 'โปรดลองใหม่อีกครั้ง');
					$.unblockUI();
				}
			});
		});

	}
	
	function confirm_user(){
		var yymm_now = $('#yymm_now').val();	
		var month = ("0" + $('#month').val()).slice(-2);
		var year = $('#year').val();
		var yymm = year+month;
		if(yymm >= yymm_now){
			if(sessionStorage.getItem("check_permission_confirm") == 'confirm'){
				alertContinueProcess(); //เรียก function การออกเรียกเก็บ รายเดือน
			}else{
				$('#modal_confirm_user').modal('show');
			}			
		}else{
			swal("กรุณาเลือกเดือน  ปี ใหม่อีกครั้ง ", "เนื่องจาก เดือน ปี ที่ทำรายการเรียกเก็บประจำเดือน น้อยกว่าเดือนปัจุบัน", "warning");
		}
	}

	function alertContinueProcess() {

		if (getCookie("proc_state") === null || getCookie("proc_state") === "finish") {
			do_process();
		} else {
			swal({
					title: "ตรวจพบการประมวลผลไม่สำเร็จ!",
					text: "ต้องการประมวลผลต่อจากครั้งก่อนหรือไม่",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: '#DD6B55',
					confirmButtonText: 'ทำต่อ',
					cancelButtonText: "เริ่มใหม่",
					closeOnConfirm: true,
					closeOnCancel: true
				},
				function (isConfirm) {
					if (!isConfirm) {
						setCookie("proc_state", "idle");
						setCookie("proc_counter", 1)
					}
					do_process();
				});
		}
	}
	
	//CONFIRM USER
	$("#submit_confirm_user").on('click', function (){
		var confirm_user = $('#confirm_user').val();
		var confirm_pwd = $('#confirm_pwd').val();	
		var permission_id = '<?php echo $_SESSION['permission_id'];?>';	
		$.ajax({
				method: 'POST',
				url: base_url+'auth/authen_confirm_user',
				data: {
					confirm_user : confirm_user,
					confirm_pwd : confirm_pwd,
					permission_id : permission_id //รหัสระบบรายการเรียกเก็บประจำเดือน
				},
				dataType: 'json',
				success: function(data){
					//console.log(data);
					if(data.result=="true"){
						
						if(data.permission=="true"){
							sessionStorage.check_permission_confirm = "confirm";
							alertContinueProcess(); //เรียก function การออกเรียกเก็บ รายเดือน
							$('#modal_confirm_user').modal('toggle');
						}else{
							swal("ไม่มีสิทธิ์ทำรายการ");
						}
					}else{
						swal("ตรวจสอบข้อมูลให้ถูกต้อง");
					}
				}
		});
	});
	//CONFIRM USER
	
	$(".logout").on('click', function(event){
	   sessionStorage.clear();
	});
</script>
