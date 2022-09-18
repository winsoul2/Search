<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.bt-add{
				float:none;
			}						
			.input-with-icon .form-control{
				padding-left: 40px;
			}
			
			input[type=file]{    
				margin-left: -8px;
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
			
			.scrollbar {
				height: 200px;
			}
			
			.text-success{color:#5cb85c;}
			
			.pointer{cursor: pointer;}
		</style>
		<?php
			$param = '';
			if(!empty($_GET)){
				foreach($_GET AS $key=>$val){
					$param .= $key.'='.$val.'&';
				}
			}
		?>
		<h1 style="margin-bottom: 0">สมาชิกค้างชำระเกินกำหนด</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">			   
			<button name="print_btn" id="print_btn" type="button" class="btn btn-primary btn-lg bt-add">
				<span class="icon icon-print"></span>
				<span>พิมพ์เอกสารแจ้งเตือนก่อนออก</span>
			</button>		 
			<!-- <button name="dismiss_btn" id="dismiss_btn" type="button" class="btn btn-primary btn-lg bt-add">
				<span class="icon icon-print"></span>
				<span>พิมพ์เอกสารพ้นสภาพ</span>
			</button>		   -->
		</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">	
					
					<form data-toggle="validator" method="get" action="<?php echo base_url(PROJECTPATH.'/debt/debt_dismiss_member'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="form_search">											
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-24">
								<label class="g24-col-sm-2 control-label" for="department">หน่วยงาน</label>
								<div class="g24-col-sm-9">
									<div class="form-group">
										<select class="form-control m-b-1" name="department" id="department" onchange="change_mem_group('department', 'faction')">
											<option value="">เลือกข้อมูล</option>
											<?php
											foreach($department as $key => $value){ ?>
												<option value="<?php echo $value['id']; ?>" <?php echo @$data['department']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>						
						</div>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-24">					
								<label class="g24-col-sm-2 control-label" for="faction">ฝ่าย</label>
								<div class="g24-col-sm-8">
									<div class="form-group" id="faction_space">
										<select class="form-control m-b-1" name="faction" id="faction" onchange="change_mem_group('faction','level')">
											<option value="">เลือกข้อมูล</option>
											<?php foreach($faction as $key => $value){ ?>
													<option value="<?php echo $value['id']; ?>" <?php echo @$data['faction']==$value['id']?'selected':'';?>><?php echo $value['mem_group_name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>

								<label class="g24-col-sm-3 control-label" for="level">สังกัด</label>
								<div class="g24-col-sm-9">
									<div class="form-group" id="level_space">
										<select class="form-control m-b-1" name="level" id="level">
											<option value="">เลือกข้อมูล</option>
											<?php foreach($level as $key => $value){ ?>
												<option value="<?php echo $value['id']; ?>" <?php echo @$data['level']==$value['id']?'selected':'';?>><?php echo $value['mem_group_name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							
								<div class="g24-col-sm-2">
								</div>
							</div>						
						</div>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-24">					
								<label class="g24-col-sm-2 control-label" for="faction">ค้นหา</label>
								<div class="g24-col-sm-8">
									<div class="form-group">
										<input class="form-control" type="text" placeholder="ป้อนชื่อสกุล หรือ รหัสสมาชิก" name="search_member" id="search_member" value="<?php echo !empty($_GET['search_member']) ? $_GET['search_member'] : ""?>">                       
									</div>
								</div>

								<label class="g24-col-sm-3 control-label" for="level"></label>
								<div class="g24-col-sm-11">
									<div class="form-group">
										<button name="bt_view" id="bt_view" type="submit" class="btn btn-primary" style="width: 90px;" onclick="">
											<span>แสดง</span>
										</button>
									</div>
								</div>
							</div>						
						</div>
					</form>
					
					<div class="bs-example" data-example-id="striped-table">
						<div id="tb_wrap">
							<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/debt/print_warning_dismiss_letter?'.$param); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="form_print_item">
								<input type="hidden" id="action" name="action" value="save">
								<table class="table table-bordered table-striped table-center">
									<thead> 
										<tr class="bg-primary">
											<th><input type="checkbox" id="check_all" onclick="checked_all()"> <!--<a onclick="del_member()" style="cursor:pointer" title="เลือก" class="icon icon-trash-o"></a>--></th>
											<th>รหัสสมาชิก</th>
											<th>ชื่อสกุล</th>
											<!-- <th>ประเภทการค้างชำระ</th>
											<th>จำนวนงวด</th> -->
											<th></th>
											<!-- <th></th> -->
										</tr>
									</thead>
									<tbody>
										<?php
											foreach($datas as $data) {
										?>
										<tr> 											 
											<td>
												<input type="checkbox" class="check_item member_ids" name="member_ids[]" id="checkbox_<?php echo $data['member_id']?>" value="<?php echo @$data['member_id']; ?>">
											</td>
											<td><?php echo $data['member_id'];?></td>
											<td class="text-left"><?php echo $data['prename_full'].$data['firstname_th'].'  '.$data['lastname_th'];?></td>
											<!-- <td><?php echo $data['deduct_code'] == "SHARE" ? "หุ้น" : "หนี้";?></td>
											<td><?php echo $data['count'];?></td> -->
											<td>
												<a href="<?php echo base_url(PROJECTPATH.'/debt/print_warning_dismiss_letter?'.$param); ?>&member_id=<?php echo @$data['member_id']; ?>&type=<?php echo @$data['deduct_code']; ?>">
													พิมพ์เอกสารแจ้งเตือนก่อนออก
												</a>
											</td>
											<!-- <td>
												<a href="#" class="dismiss_a" data-member-id="<?php echo @$data['member_id']; ?>">
													พิมพ์เอกสารพ้นสภาพ
												</a>
											</td> -->
										</tr>
										<?php
											}
										?>
									</tbody> 
								</table>
							</form>
						</div>
					</div>
					<?php echo @$paging ?>
				</div>
			</div>
		</div>
    </div>
</div>
<div class="modal fade" id="dismiss_modal"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:40%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">พิมพ์เอกสารพ้นสภาพ</h2>
            </div>
            <div class="modal-body" style="height: 250px;">
				<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/debt/print_dismiss_letter?'.$param); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="dismiss_modal_form">
					<input type="hidden" name="submit_type" id="submit_type" value=""/>
					<input type="hidden" name="member_id" id="member_id" value=""/>
					<input type="hidden" name="member_ids" id="member_ids" value=""/>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">
							<label class="g24-col-sm-10 control-label">วันที่ประชุม</label>
							<div class="g24-col-sm-12">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="meeting_date" name="meeting_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">
							<label class="g24-col-sm-10 control-label">ประชุมครั้งที่ </label>
							<div class="g24-col-sm-12">
								<div class="form-group">
									<input type="text" class="form-control" name="agenda" id="agenda" value="">
								</div>
							</div>
						</div>
					</div>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">
							<label class="g24-col-sm-10 control-label">คณะกรรมการดำเนินการ ชุดที่ </label>
							<div class="g24-col-sm-12">
								<div class="form-group">
									<input type="text" class="form-control" name="committee_group" id="committee_group" value="">
								</div>
							</div>
						</div>
					</div>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group  text-center">
							<button class="btn btn-primary btn-after-input" type="button" id="modal_print_btn"><span>พิมพ์</span></button>
							<button class="btn btn-default btn-after-input" type="button" id="modal_cancel_btn"><span>ยกเลิก</span></button>
						</div>
					</div>
				</form>
            </div>
        </div>
    </div>
</div>

<script>
	var base_url = $('#base_url').attr('class');
	function change_mem_group(id, id_to){
		var mem_group_id = $('#'+id).val();
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

	function checked_all(){
		if($('#check_all').is(':checked')){
			$('.check_item').prop('checked','checked');
		}else{
			$('.check_item').prop('checked','');
		}
	}

	$(document).ready(function() {
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
		$("#print_btn").click(function() {
			$("#form_print_item").submit()
		})
		$("#dismiss_btn").click(function(e) {
			e.preventDefault()
			$("#submit_type").val("group")
			$("#member_id").val("")
			
			$("#committee_group").val("")
			$("#agenda").val("")
			$("#meeting_date").val("")
			member_id_list = "";
			$('.member_ids').each(function () {
				if(this.checked) {
					if(member_id_list != "") member_id_list += ",";
					member_id_list += $(this).val();
				}
			});
			$("#member_ids").val(member_id_list)
			$("#dismiss_modal").modal('toggle')
		})
		$(".dismiss_a").click(function(e) {
			e.preventDefault()
			$("#submit_type").val("single")
			$("#member_ids").val("")
			$("#member_id").val($(this).attr("data-member-id"))
			$("#committee_group").val("")
			$("#agenda").val("")
			$("#meeting_date").val("")
			$("#dismiss_modal").modal('toggle')
		})
		$("#modal_cancel_btn").click(function() {
			$("#dismiss_modal").modal('hide')
		})
		$("#modal_print_btn").click(function(e) {
			e.preventDefault()

			text_alert = ""
			if(!$("#meeting_date").val()) {
				text_alert += " - วันที่ประชุม\n"
			}
			if(!$("#agenda").val()) {
				text_alert += " - ประชุมครั้งที่\n"
			}
			if(!$("#committee_group").val()) {
				text_alert += " - คณะกรรมการดำเนินการ ชุดที่\n"
			}
			if(text_alert != ""){
				swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
			} else {
				$("#dismiss_modal_form").submit()
			}
		})
	})
</script>
