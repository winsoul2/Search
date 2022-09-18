<div class="layout-content">
    <div class="layout-content-body">
		<?php
		$act = @$_GET['act'];
		$id = @$_GET['id'];
		?>
		<style>
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			.form-group{
				margin-bottom: 5px;
			}
			.text_center{
				text-align:center;
			}
			label{
				padding-top:7px;
				text-align:right;
			}
		</style>
		<h1 style="margin-bottom: 0">เบิกพัสดุ</h1>

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			 <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php 
				if (@$act != "add") {
				?>
				<a class="btn btn-primary btn-lg bt-add" href="<?php echo base_url(PROJECTPATH.'/facility/take_facility?act=add');?>">
					<span class="icon icon-plus-circle"></span>
					เพิ่มการเบิก
				</a>
				<?php
				}
				?>
			</div>
		</div>
		<?php 
		if (@$act != "add") {
		?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">


					<div class="row">
						<div class="col-sm-6">
							<div class="input-with-icon">
								<input class="form-control input-thick pill m-b-2" type="text" placeholder="ค้นหา" name="search_text" id="search_text" onkeyup="get_search_take()">
								<span class="icon icon-search input-icon"></span>
							</div>
						</div>

						<div class="col-sm-6 text-right">
							
						</div>
					</div>

					<div class="bs-example" data-example-id="striped-table">
						<div id="tb_wrap">
							<table class="table table-striped">
								<thead>
								<tr>
									<th>รหัสการเบิก</th>
									<th>วันที่เบิก</th>
									<th>เลขที่ใบเบิก</th>
									<th>หน่วยงาน</th>
									<th>ผู้เบิก</th>
									<th style="width:150px;"></th>
								</tr>
								</thead>
								<tbody id="table_data">
								<?php foreach($row as $key => $value){ ?>
									<tr>
										<td><?php echo @$value['receive_no']; ?></td>
										<td><div id="sign_date_wrap_<?php echo @$value['facility_take_id']; ?>"><?php echo $this->center_function->ConvertToThaiDate(@$value['sign_date'],true,false);?></div></td>
										<td><?php echo @$value['voucher_no']; ?></td>
										<td><div id="department_name_wrap_<?php echo @$value['facility_take_id']; ?>"><?php echo @$value['department_name']; ?></div></td>
										<td><div id="receive_name_wrap_<?php echo @$value['facility_take_id']; ?>"><?php echo @$value['receive_name']; ?></div></td>
										<td>
											<a href="#" class="btn_transfer" data-id="<?php echo @$value['facility_take_id']; ?>">โอนย้าย</a> |
											<a href="<?php echo '?act=add&id='.@$value['facility_take_id'];?>">ดูรายการ</a>
										</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div id="page_wrap">
					<?php echo @$paging ?>
				</div>
			</div>
		</div>
		<?php 
		}else{
		?>
		
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<form id="form1" method="POST" action="<?php echo base_url(PROJECTPATH.'/facility/take_facility_save'); ?>">
						<input class="form-control" name="facility_take_id" id="facility_take_id" type="hidden" value="<?php echo @$data['facility_take_id'];?>">
						<div class="" style="padding-top:0;">
							<div class="g24-col-sm-24">								
								<div class="form-group g24-col-sm-8">
									<label class="g24-col-sm-10 control-label">รหัสการเบิก</label>
									<div class="g24-col-sm-14">
										<input class="form-control" name="receive_no" id="receive_no" type="text" value="<?php echo @$data['receive_no'];?>" readonly="readonly">
									</div>
								</div>
								<div class="form-group g24-col-sm-8">
									<label class="g24-col-sm-10 control-label">เลขที่ใบเบิก</label>
									<div class="g24-col-sm-14">
										<input class="form-control" id="voucher_no" name="voucher_no" type="text" value="<?php echo @$data['voucher_no'];?>" readonly="readonly">
									</div>
								</div>
								<div class="form-group g24-col-sm-8">
									<label class="g24-col-sm-10 control-label">วันที่เบิก</label>
									<div class="g24-col-sm-14">
										<input id="sign_date" name="sign_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(empty($data['sign_date'])?date("Y-m-d"):date("Y-m-d", strtotime($data['sign_date']))); ?>" data-date-language="th-th" required title="วันที่รับ">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
							<div class="g24-col-sm-24">
								<div class="form-group g24-col-sm-8">
									<label class="g24-col-sm-10 control-label">หน่วยงาน</label>
									<div class="g24-col-sm-14">
										<select id="department_id" name="department_id" class="form-control">
											<option value="">เลือกหน่วยงาน</option>
											<?php
											foreach($department as $key => $value){ 
												$select = ($value['department_id'] == $data['department_id'])?'selected':'';
											?>
												<option value="<?php echo $value['department_id']; ?>" <?php echo $select;?>><?php echo $value['department_name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group g24-col-sm-8">
									<label class="g24-col-sm-10 control-label">ผู้เบิก</label>
									<div class="g24-col-sm-14">
										<input type="hidden" id="receiver_id_select" value="<?php echo $data['receiver_id'];?>">
										<div id="receiver_wrap"></div>
									</div>
								</div>
							</div>
							<div class="g24-col-sm-24" style="margin-top: 10px;">
								<div class="form-group g24-col-sm-24 text_center">
									<button type="button" id="bt_choose" class="btn btn-primary" style="width:150px;" onclick="choose_facility()"><span class="icon icon-briefcase"></span> เลือกรายการเบิก</button>
									<button type="button" id="bt_submit" class="btn btn-primary" style="width:150px;" onclick="check_submit()"><span class="icon icon-save"></span> บันทึก</button>
									
								</div>
							</div>
						</div>
						<div id="input_space"></div>
					</form>
					<div class="g24-col-sm-24 m-t-1">
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-bordered table-striped table-center">
								<thead>
									<tr class="bg-primary">
										<th width="10%"><input type="checkbox" id="chk_all" onclick="check_all()"></th>
										<th width="20%">เลขพัสดุ</th>
										<th width="50%">รายการ</th>
										<th width="20%">ราคา</th>
									</tr>
								</thead>
								<tbody id="store_space">
								<?php 
									$result  = '';
									if(!empty($detail)){
										foreach(@$detail as $key => $value){										
											$result .= "<tr class='tr_choose_store' id='tr_choose_id_".$value['store_id']."' store_id='".$value['store_id']."'>";
												$result .= "<td><input type='checkbox' id='store_chk_".$value['store_id']."' store_id='".$value['store_id']."' store_code='".$value['store_code']."' store_name='".$value['store_name']."' store_price='".$value['store_price']."' store_price_label='".number_format($value['store_price'],2)."' class='store_chk'></td>";
												$result .= "<td>".$value['store_code']."</td>";
												$result .= "<td>".$value['store_name']."</td>";
												$result .= "<td>".number_format($value['store_price'],2)."</td>";
											$result .= "</tr>";
											}
									}								
									
									echo @$result;
								?>	
								</tbody>
							</table>
						</div>
						<button type="button" id="del_store" class="btn btn-primary" onclick="del_store()"><span class="icon icon-trash"></span> ลบ</button>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
    </div>
</div>
<div class="modal fade" id="choose_facility"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:80%">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title"><span class="icon icon-briefcase"></span> เลือกรายการเบิก</h2>
            </div>
            <div class="modal-body">
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-8 control-label">ค้นหา</label>
					<div class="g24-col-sm-8">
						<input type="text" id="facility_keyword" name="facility_keyword" value="" class="form-control" placeholder="ป้อนเลขพัสดุ หรือ ชื่อรายการ">
					</div>
				</div>
                <div class="g24-col-sm-24 m-t-1">
					<div class="bs-example" data-example-id="striped-table">
						<table class="table table-bordered table-striped table-center">
							<thead>
								<tr class="bg-primary">
									<th><input type="checkbox" id="store_chk_all" onclick="store_check_all()"></th>
									<!--th>ลำดับ</th-->
									<th>เลขพัสดุ</th>
									<th>รายการ</th>
									<th>ราคา</th>
									<th>สถานะ</th>
								</tr>
							</thead>
							<tbody id="choose_store_space">

							</tbody>
						</table>
					</div>
				</div>
            </div>
            <div class="text_center m-t-1">
                <button class="btn btn-info" onclick="choose_store()"><span class="icon icon-save"></span> บันทึก</button>
				<button class="btn btn-info" onclick="close_modal('choose_facility')"><span class="icon icon-close"></span> ออก</button>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_transfer"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title"><span class="icon icon-exchange"></span> โอนย้าย</h2>
            </div>
            <div class="modal-body">
				<div id="modal_transfer_wrap"></div>
            </div>
            <div class="text_center m-t-1">
                <button class="btn btn-info" onclick="modal_transfer_save()"><span class="icon icon-save"></span> บันทึก</button>
				<button class="btn btn-info" onclick="close_modal('modal_transfer')"><span class="icon icon-close"></span> ออก</button>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<script>
	function open_modal(id){
		$('#'+id).modal('show');
	}
	function close_modal(id){
		$('#'+id).modal('hide');
	}
	function store_check_all(){
		if($('#store_chk_all').is(':checked')){
			$('.store_chk').attr('checked','checked');
		}else{
			$('.store_chk').removeAttr('checked');
		}
	}
	function check_all(){
		if($('#chk_all').is(':checked')){
			$('.chk').attr('checked','checked');
		}else{
			$('.chk').removeAttr('checked');
		}
	}
	function del_store(){
		$('.chk').each(function(){
			if($(this).is(':checked')){
				$('#tr_store_'+$(this).attr('store_id')).remove();
			}
		});
		process_input();
		process_choose_store();
	}
	function choose_department(){
		var department_id = $('#choose_department').val();
		$.ajax({
			method: 'POST',
			url: base_url+'facility/get_store',
			data: {
				department_id : department_id
			},
			success: function(msg){
				$('#choose_store_space').html(msg);
				process_choose_store();
			}
		});
	}
	function choose_facility() {
		$('#choose_facility').on('shown.bs.modal', function () {
			$('#facility_keyword').focus();
		})
		open_modal('choose_facility');
		document.getElementById("facility_keyword").focus();
	}
	function choose_store(){
		var store_ids = [];
		$('.store_chk').each(function(){
			if($(this).is(':checked')){
				store_ids.push($(this).attr('store_id'));
			}
		});

		if(store_ids.length == 0) {
			swal('กรุณาเลือกรายการ','','warning');
			return false;
		}

		$.ajax({
			method: 'POST',
			url: base_url+'facility/chk_facility_status',
			data: {
				store_ids : store_ids
			},
			success: function(msg){
				var obj = JSON.parse(msg);
				
				if(obj.status == "TRUE") {
					var result = '';
					$('.store_chk').each(function(){
						if($(this).is(':checked')){
							result += "<tr class='tr_store' id='tr_store_"+$(this).attr('store_id')+"' store_id='"+$(this).attr('store_id')+"'>\n";
								result += "<td><input type='checkbox' id='chk_id_"+$(this).attr('store_id')+"' store_id='"+$(this).attr('store_id')+"' class='chk'></td>\n";
								result += "<td>"+$(this).attr('store_code')+"</td>\n";
								result += "<td>"+$(this).attr('store_name')+"</td>\n";
								result += "<td>"+$(this).attr('store_price_label')+"</td>\n";
							result += "</tr>\n";
						}
					});
					$('#store_space').append(result);
					process_input();
					process_choose_store();
					$('#store_chk_all').removeAttr('checked');
					$('.store_chk').removeAttr('checked');
					close_modal('choose_facility');
				}
				else {
					swal('ไม่สามารถเลือกใช้งานได้ เนื่องจาก ' + obj.error,'','warning');
				}
			}
		});
	}
	function process_choose_store(){
		$('.tr_choose_store').show();
		$('.tr_store').each(function(){
			var store_id = $(this).attr('store_id');
			/*$('.tr_choose_store').each(function(){
				if(store_id == $(this).attr('store_id')){
					$(this).hide();
				}
			});*/
			$('#tr_choose_id_'+store_id).hide();
		});
	}
	function process_input(){
		var result = '';
		var i = 0;
		$('.tr_store').each(function(){
			result += '<input type="hidden" class="store_input" name="store_id['+i+']" value="'+$(this).attr('store_id')+'">\n';
			i++;
		});
		$('#input_space').html(result);
	}
	function check_submit(){
		var text_alert = '';
		/*if($('#receive_no').val()==''){
			text_alert += '- รหัสการเบิก\n';
		}
		*/
		if($('#receive_date').val()==''){
			text_alert += '- วันที่รับ\n';
		}
		if($('#budget_year').val()==''){
			text_alert += '- ปีงบประมาณ\n';
		}
		/*if($('#voucher_no').val()==''){
			text_alert += '- เลขที่ใบสำคัญ\n';
		}*/
		if($('#type_evidence_id').val()==''){
			text_alert += '- ประเภทหลักฐาน\n';
		}
		if($('#sign_date').val()==''){
			text_alert += '- ลงวันที่\n';
		}
		if($('#department_id').val()==''){
			text_alert += '- หน่วยงาน\n';
		}
		if($('#receive_name').val()==''){
			text_alert += '- ผู้เบิก\n';
		}
		var i = 0;
		$('.store_input').each(function(){
			i++;
		});
		if(i == 0){
			text_alert += '- รายการพัสดุ\n';
		}
		if(text_alert == ''){
			$('#form1').submit();
		}else{
			swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
		}
		
	}
	
	function get_search_take(){
        $.ajax({
            type: "POST",
            url: base_url+'facility/get_search_take',
            data: {
                search_text : $("#search_text").val(),
				form_target : 'index'
            },
            success: function(msg) {
                $("#table_data").html(msg);
            }
        });
    }
	
	function modal_transfer_save() {
		var text_alert = '';
		if($('#m_department_id').val()==''){
			text_alert += '- หน่วยงานใหม่\n';
		}
		if($('#receiver_id').val()==''){
			text_alert += '- ผู้ขอโอนย้าย\n';
		}
		if(text_alert != ''){
			swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
			return false;
		}

		var data = $("#frm_transfer").serialize();

		$.ajax({
			type: "POST",
			url: base_url+'facility/transfer_save',
			data: data,
			success: function(msg) {
				var obj = JSON.parse(msg);

				if(obj.status == "TRUE") {
					$("#department_name_wrap_" + obj.data.facility_take_id).html(obj.data.department_name);
					$("#receive_name_wrap_" + obj.data.facility_take_id).html(obj.data.receive_name);
					$("#sign_date_wrap_" + obj.data.facility_take_id).html(obj.data.sign_date);
					close_modal("modal_transfer");
				}
			}
		});
	}

	$( document ).ready(function() {
		$("#receive_date").datepicker({
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
			autoclose: true
		});
		$("#sign_date").datepicker({
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
			autoclose: true
		});
		
		var id = $("#facility_take_id").val();
		if(id){
			$("#bt_choose").prop("disabled", true);
			$("#bt_submit").prop("disabled", true);
			$("#del_store").prop("disabled", true);
			$('input').prop("disabled", true);
			$('select').prop("disabled", true);
		}

		$("#department_id").change(function() {
			var id = $(this).val();
			var select_id = $("#receiver_id_select").val();

			$.ajax({
				type: "POST",
				url: base_url+'facility/get_department',
				data: {
					id : id,
					select_id : select_id
				},
				success: function(msg) {
					$("#receiver_wrap").html(msg);
				}
			});
		});
		$("#department_id").trigger("change");

		$("#facility_keyword").keyup(function() {
			var keyword = $(this).val();
			$.ajax({
				method: 'POST',
				url: base_url+'facility/get_store',
				data: {
					keyword : keyword
				},
				success: function(msg){
					$('#choose_store_space').html(msg);
					process_choose_store();
				}
			});
		});

		$(document).on("click", ".btn_transfer", function() {
			var id = $(this).data("id");

			$.ajax({
				method: 'POST',
				url: base_url+'facility/get_transfer_form',
				data: {
					id : id
				},
				success: function(msg){
					var obj = JSON.parse(msg);

					if(obj.status == "TRUE") {
						$('#modal_transfer_wrap').html(obj.html);

						$("#m_department_id").change(function() {
							var department_id = $(this).val();
							var select_id = "";

							$.ajax({
								type: "POST",
								url: base_url+'facility/get_department',
								data: {
									id : department_id,
									select_id : select_id
								},
								success: function(msg) {
									$("#m_receiver_wrap").html(msg);
								}
							});
						});
						$("#m_department_id").trigger("change");

						open_modal("modal_transfer");
					}
				}
			});

			return false;
		});
	});
	
</script>