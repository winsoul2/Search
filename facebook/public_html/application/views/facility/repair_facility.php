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
		<h1 style="margin-bottom: 0">รายการแจ้งซ่อม</h1>

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			 <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php 
				if (@$act != "add") {
				?>
				<a class="btn btn-primary btn-lg bt-add" href="?act=add">
					<span class="icon icon-plus-circle"></span>
					เพิ่มรายการแจ้งซ่อม
				</a>
				<?php
				}
				?>
			</div>
		</div>
		<?php
		if (@$act == "add") {
		?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<form id="form1" method="POST" action="<?php echo base_url(PROJECTPATH.'/facility/repair_facility_save'); ?>">
						<input class="form-control" name="repair_id" id="repair_id" type="hidden" value="<?php echo @$data['repair_id'];?>">
						<div class="" style="padding-top:0;">
							<div class="g24-col-sm-24">
								<div class="form-group">
									<label class="g24-col-sm-4 control-label">เลขพัสดุ</label>
									<div class="g24-col-sm-6">
										<div class="input-group">
											<input type="hidden" id="store_id" name="store_id" value="<?php echo @$data['store_id'];?>">
											<input class="form-control m-b-1" name="store_code" id="store_code" type="text" value="<?php echo @$data['store_code'];?>">
											<span class="input-group-btn">
												<a data-toggle="modal" data-target="#choose_facility" id="test" class="fancybox_share fancybox.iframe" href="#">
													<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span>
													</button>
												</a>
											</span>
										</div>
									</div>
									<label class="g24-col-sm-2 control-label">วันที่ส่งซ่อม</label>
									<div class="g24-col-sm-6">
										<input id="repair_date" name="repair_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(empty($data['repair_date'])?date("Y-m-d"):date("Y-m-d", strtotime($data['repair_date']))); ?>" data-date-language="th-th" required title="วันที่ส่งซ่อม">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>

							<div class="g24-col-sm-24">
								<div class="form-group">
									<label class="g24-col-sm-4 control-label">รายการ</label>
									<div class="g24-col-sm-14">
										<input class="form-control m-b-1" name="store_name" id="store_name" type="text" value="<?php echo @$data['store_name'];?>" readonly="readonly">
									</div>
								</div>
							</div>

							<div class="g24-col-sm-24">
								<div class="form-group">
									<label class="g24-col-sm-4 control-label">ลักษณะอาการ</label>
									<div class="g24-col-sm-14">
										<textarea class="form-control m-b-1" id="problem" name="problem" rows="5"><?php echo @$data['problem'];?></textarea>
									</div>
								</div>
							</div>

							<div class="g24-col-sm-24">
								<div class="form-group">
									<label class="g24-col-sm-4 control-label">บริษัทที่ส่งซ่อม</label>
									<div class="g24-col-sm-14">
										<input class="form-control m-b-1" name="company" id="company" type="text" value="<?php echo @$data['company'];?>">
									</div>
								</div>
							</div>

							<div class="g24-col-sm-24">
								<div class="form-group">
									<label class="g24-col-sm-4 control-label">ที่อยู่บริษัท</label>
									<div class="g24-col-sm-14">
										<textarea class="form-control m-b-1" id="company_address" name="company_address" value="<?php echo @$data['company_address'];?>" rows="5"></textarea>
									</div>
								</div>
							</div>

							<div class="g24-col-sm-24">
								<div class="form-group">
									<label class="g24-col-sm-4 control-label">เบอร์โทรติดต่อ</label>
									<div class="g24-col-sm-6">
										<input class="form-control m-b-1" name="contact_tel" id="contact_tel" type="text" value="<?php echo @$data['contact_tel'];?>">
									</div>
								</div>
							</div>

							<div class="g24-col-sm-24" style="margin-top: 30px;">
								<div class="form-group g24-col-sm-24 text_center">
									<button type="button" id="bt_submit" class="btn btn-primary" style="width:150px;" onclick="check_submit()"><span class="icon icon-save"></span> บันทึก</button>
								</div>
							</div>
						</div>
						<div id="input_space"></div>
					</form>
				</div>
			</div>
		</div>
		<?php
		}elseif(@$act == "detail"){
		?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<div class="" style="padding-top:0;">
						<div class="g24-col-sm-24">
							<div class="form-group">
								<label class="g24-col-sm-4 control-label">เลขพัสดุ</label>
								<div class="g24-col-sm-6">
									<div class="input-group">
										<p class="form-control-static"><?php echo @$data['store_code'];?></p>
									</div>
								</div>
								<label class="g24-col-sm-2 control-label">วันที่ส่งซ่อม</label>
								<div class="g24-col-sm-6">
									<p class="form-control-static"><?php echo empty($data['repair_date']) ? '' : $this->center_function->mydate2date(date("Y-m-d", strtotime($data['repair_date']))); ?></p>
								</div>
							</div>
						</div>

						<div class="g24-col-sm-24">
							<div class="form-group">
								<label class="g24-col-sm-4 control-label">รายการ</label>
								<div class="g24-col-sm-14">
									<p class="form-control-static"><?php echo @$data['store_name'];?></p>
								</div>
							</div>
						</div>

						<div class="g24-col-sm-24">
							<div class="form-group">
								<label class="g24-col-sm-4 control-label">ลักษณะอาการ</label>
								<div class="g24-col-sm-14">
									<p class="form-control-static"><?php echo nl2br(@$data['problem']);?></p>
								</div>
							</div>
						</div>

						<div class="g24-col-sm-24">
							<div class="form-group">
								<label class="g24-col-sm-4 control-label">บริษัทที่ส่งซ่อม</label>
								<div class="g24-col-sm-14">
									<p class="form-control-static"><?php echo @$data['company'];?></p>
								</div>
							</div>
						</div>

						<div class="g24-col-sm-24">
							<div class="form-group">
								<label class="g24-col-sm-4 control-label">ที่อยู่บริษัท</label>
								<div class="g24-col-sm-14">
									<p class="form-control-static"><?php echo nl2br(@$data['company_address']);?></p>
								</div>
							</div>
						</div>

						<div class="g24-col-sm-24">
							<div class="form-group">
								<label class="g24-col-sm-4 control-label">เบอร์โทรติดต่อ</label>
								<div class="g24-col-sm-6">
									<p class="form-control-static"><?php echo @$data['contact_tel'];?></p>
								</div>
								<label class="g24-col-sm-2 control-label">วันที่รับคืน</label>
								<div class="g24-col-sm-6">
									<p class="form-control-static"><?php echo empty($data['return_date']) ? '' : $this->center_function->mydate2date(date("Y-m-d", strtotime($data['return_date']))); ?></p>
								</div>
							</div>
						</div>

						<div class="g24-col-sm-24">
							<div class="form-group">
								<label class="g24-col-sm-4 control-label">ผลการส่งซ่อม</label>
								<div class="g24-col-sm-14">
									<p class="form-control-static"><?php echo @$data['result_status'] == 1 ? "ซ่อมไม่ได้" : "ซ่อมได้";?></p>
								</div>
							</div>
						</div>

						<div class="g24-col-sm-24">
							<div class="form-group">
								<label class="g24-col-sm-4 control-label">ค่าซ่อม</label>
								<div class="g24-col-sm-14">
									<p class="form-control-static"><?php echo number_format(@$data['repair_price']);?></p>
								</div>
							</div>
						</div>

						<div class="g24-col-sm-24">
							<div class="form-group">
								<label class="g24-col-sm-4 control-label">หมายเหตุ</label>
								<div class="g24-col-sm-14">
									<p class="form-control-static"><?php echo @$data['remark'];?></p>
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
		<?php
		}else{
		?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">

					<div class="row">
						<div class="col-sm-6">
							<div class="input-with-icon">
								<input class="form-control input-thick pill m-b-2" type="text" placeholder="ค้นหา" name="search_text" id="search_text" onkeyup="get_search()">
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
									<th>ลำดับ</th>
									<th>วันที่ส่งซ่อม</th>
									<th>ทะเบียนแจ้งซ่อม</th>
									<th>เลขพัสดุ</th>
									<th>รายการ</th>
									<th>บริษัทที่ส่งซ่อม</th>
									<th>สถานะ</th>
									<th style="width:180px;"></th>
								</tr>
								</thead>
								<tbody id="table_data">
								<?php foreach($row as $key => $value){ ?>
									<tr>
										<td><?php echo $i++; ?></td>
										<td><?php echo $this->center_function->ConvertToThaiDate(@$value['repair_date'],true,false);?></td>
										<td><?php echo @$value['repair_code']; ?></td>
										<td><?php echo @$value['store_code']; ?></td>
										<td><?php echo @$value['store_name']; ?></td>
										<td><?php echo @$value['company']; ?></td>
										<td>
											<div id="repair_status_wrap_<?php echo $value['repair_id']; ?>">
												<?php if($value['repair_status'] == 0) { ?>ส่งซ่อม<?php } ?>
												<?php if($value['repair_status'] == 1) { ?>ซ่อมไม่ได้<?php } ?>
												<?php if($value['repair_status'] == 2) { ?>ได้รับคืนแล้ว<?php } ?>
											</div>
										</td>
										<td>
											<?php if($value['repair_status'] == 0) { ?>
												<a href="#" class="btn_return btn btn-primary btn-xs" data-id="<?php echo @$value['repair_id']; ?>">ได้รับคืนแล้ว</a>
											<?php } else { ?>
												<a href="<?php echo '?act=detail&id='.@$value['repair_id'];?>">รายละเอียดการซ่อม</a>
											<?php } ?>
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
		<?php } ?>
    </div>
</div>

<div class="modal fade" id="choose_facility" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display:flex;">
                <div style="width:95%">
                    <h4 class="modal-title">ข้อมูลพัสดุ</h4>
                </div>
                <div style="width:5%">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
            </div>
            <div class="modal-body">
                <div class="input-with-icon">
                    <input class="form-control input-thick pill m-b-2" type="text" placeholder="กรอกรหัสหรือชื่อพัสดุ" name="search_facility_text" id="search_facility_text" onkeyup="get_facility_store_list()">
                    <span class="icon icon-search input-icon"></span>
                </div>
                <div class="bs-example scrollbar" data-example-id="striped-table">
					<div class="force-overflow">
						<table class="table table-striped">

							<tbody id="table_data_facility">

							</tbody>

						</table>
					</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_return"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title"><span class="icon icon-wrench"></span> รายการแจ้งซ่อม</h2>
            </div>
            <div class="modal-body">
				<form method="post" id="frm_return" action="">
					<input type="hidden" id="m_repair_id" name="repair_id" value="">
					<input type="hidden" id="m_store_id" name="store_id" value="">

					<div class="g24-col-sm-24">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">เลขทะเบียนซ่อม</label>
							<div class="g24-col-sm-10">
								<input class="form-control m-b-1" name="repair_code" id="m_repair_code" type="text" value="" readonly="readonly">
							</div>
						</div>
					</div>

					<div class="g24-col-sm-24">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">เลขพัสดุ</label>
							<div class="g24-col-sm-10">
								<input class="form-control m-b-1" name="store_code" id="m_store_code" type="text" value="" readonly="readonly">
							</div>
						</div>
					</div>

					<div class="g24-col-sm-24">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">วันที่ส่งซ่อม</label>
							<div class="g24-col-sm-10">
								<input id="m_repair_date" name="repair_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="" data-date-language="th-th" readonly="readonly">
								<span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>
					</div>

					<div class="g24-col-sm-24">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">รายการ</label>
							<div class="g24-col-sm-18">
								<input class="form-control m-b-1" name="store_name" id="m_store_name" type="text" value="" readonly="readonly">
							</div>
						</div>
					</div>

					<div class="g24-col-sm-24">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">บริษัทที่ส่งซ่อม</label>
							<div class="g24-col-sm-18">
								<input class="form-control m-b-1" name="company" id="m_company" type="text" value="" readonly="readonly">
							</div>
						</div>
					</div>

					<div class="g24-col-sm-24">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">วันที่รับคืน</label>
							<div class="g24-col-sm-10">
								<input id="m_return_date" name="return_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="" data-date-language="th-th">
								<span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>
					</div>

					<div class="g24-col-sm-24">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">ผลการส่งซ่อม</label>
							<div class="g24-col-sm-10">
								<select id="m_result_status" name="result_status" class="form-control m-b-1">
									<option value="0">ซ่อมได้</option>
									<option value="1">ซ่อมไม่ได้</option>
								</select>
							</div>
						</div>
					</div>

					<div class="g24-col-sm-24">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">ค่าซ่อม</label>
							<div class="g24-col-sm-10">
								<input class="form-control m-b-1" name="repair_price" id="m_repair_price" type="text" value="">
							</div>
						</div>
					</div>

					<div class="g24-col-sm-24">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">หมายเหตุ</label>
							<div class="g24-col-sm-18">
								<input class="form-control m-b-1" name="remark" id="m_remark" type="text" value="">
							</div>
						</div>
					</div>
				</form>
				<div class="clearfix"></div>

            </div>
            <div class="text_center m-t-1">
                <button class="btn btn-info" onclick="modal_return_save()"><span class="icon icon-save"></span> บันทึก</button>
				<button class="btn btn-info" onclick="close_modal('modal_return')"><span class="icon icon-close"></span> ออก</button>
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
		
		if($('#store_id').val()==''){
			text_alert += '- เลขพัสดุ\n';
		}
		if($('#repair_date').val()==''){
			text_alert += '- วันที่ส่งซ่อม\n';
		}
		if($('#problem').val()==''){
			text_alert += '- ลักษณะอาการ\n';
		}
		if($('#company').val()==''){
			text_alert += '- บริษัทที่ส่งซ่อม\n';
		}
		if($('#contact_tel').val()==''){
			text_alert += '- เบอร์โทรติดต่อ\n';
		}
		if(text_alert == ''){
			$('#form1').submit();
		}else{
			swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
		}
	}

	function get_search(){
        $.ajax({
            type: "POST",
            url: base_url+'facility/get_search_repair',
            data: {
                search_text : $("#search_text").val()
            },
            success: function(msg) {
                $("#table_data").html(msg);
            }
        });
    }

	function get_facility_store_list() {
		$.ajax({
			type: "POST",
			url: base_url+'facility/get_facility_store_list',
			data: {
				search_text : $("#search_facility_text").val()
			},
			success: function(msg) {
				//console.log(msg);
				$("#table_data_facility").html(msg);
			}
		});
	}

	function choose_facility_store(id) {
		$("#store_code").val(id);
		var e = jQuery.Event("keypress");
		e.which = 13;
		$("#store_code").trigger(e);
		close_modal("choose_facility");
	}

	function modal_return_save() {
		var data = $("#frm_return").serialize();

		$.ajax({
			type: "POST",
			url: base_url+'facility/repair_return_save',
			data: data,
			success: function(msg) {
				var obj = JSON.parse(msg);

				if(obj.status == "TRUE") {
					var repair_status_text = "";
					if(obj.data.repair_status == 0) { repair_status_text = "ส่งซ่อม"; }
					if(obj.data.repair_status == 1) { repair_status_text = "ซ่อมไม่ได้"; }
					if(obj.data.repair_status == 2) { repair_status_text = "ได้รับคืนแล้ว"; }
					$("#repair_status_wrap_" + obj.data.repair_id).html(repair_status_text);
					close_modal("modal_return");
				}
			}
		});
	}

	$( document ).ready(function() {
		$("#repair_date").datepicker({
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

		$("#m_return_date").datepicker({
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

		$("#store_code").keypress(function(e) {
			if(e.which == 13) {
				var id = $(this).val();

				$.ajax({
					type: "POST",
					url: base_url+'facility/get_facility_store',
					data: {
						id : id
					},
					success: function(msg) {
						var obj = JSON.parse(msg);

						if(obj.status == "TRUE") {
							$("#store_id").val(obj.data.store_id);
							$("#store_name").val(obj.data.store_name);
						}
						else {
							$("#store_id").val("");
							$("#store_name").val("");
							swal('ไม่พบเลขพัสดุที่ท่านเลือก','','warning'); 
						}
					}
				});
			}
		});

		$(document).on("click", ".btn_return", function() {
			var id = $(this).data("id");

			$.ajax({
				type: "POST",
				url: base_url+'facility/get_facility_repair',
				data: {
					id : id
				},
				success: function(msg) {
					var obj = JSON.parse(msg);
					
					if(obj.status == "TRUE") {
						$("#m_repair_id").val(obj.data.repair_id);
						$("#m_repair_code").val(obj.data.repair_code);
						$("#m_store_id").val(obj.data.store_id);
						$("#m_store_code").val(obj.data.store_code);
						$("#m_repair_date").val(obj.data.repair_date);
						$("#m_store_name").val(obj.data.store_name);
						$("#m_company").val(obj.data.company);
						$("#m_return_date").val(obj.data.return_date);
						$("#m_result_status").val(obj.data.result_status);
						$("#m_repair_price").val(obj.data.repair_price);
						$("#m_remark").val(obj.data.remark);

						open_modal("modal_return");
					}
				}
			});
			return false;
		});
	});
</script>