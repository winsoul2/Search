<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.left {
				text-align: left;
			}
			.modal-dialog-data {
				width:70% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal_data_input{
				margin-bottom: 5px;
			}
			.form-group{
				margin-bottom: 5px;
			}
			.red{
				color: red;
			}
			.green{
				color: green;
			}			   
	</style> 
		<div class="row">
			<div class="form-group">
				<div class="col-sm-6">
					<h1 class="title_top">ย้ายสังกัด</h1>
					<?php $this->load->view('breadcrumb'); ?>
				</div>
				<div class="col-sm-6">
				<br>
					<div class="g24-col-sm-24 text-right">
					<?php if($member_id!=''){ ?>
						<a class="link-line-none" id="add_move_btn">
							<button class="btn btn-primary" onclick="open_group_move_modal();">เพิ่มการย้ายสังกัด</button>
						</a>
					<?php }?>	
					</div>
				</div>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
						<?php $this->load->view('search_member_new'); ?>
					
					<div class="g24-col-sm-24 m-t-1">
						<h3>ประวัติการย้ายสังกัด</h3>
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th style="width:80px;">ลำดับ</th>
										<th style="width:25%">สังกัดเดิม</th>
										<th style="width:25%">สังกัดใหม่</th>
										<th style="width:10%">วันที่ย้ายสังกัด</th>
										<th style="width:10%">สถานะ</th>
										<th style="width:10%">หมายเหตุ</th>
										<th style="width:10%">ผู้ทำรายการ</th>
										<!--<th style="width:8%;"></th>-->
									</tr>
								</thead>
								<tbody>
								<?php
									//echo '<pre>'; print_r($faction_list); echo '</pre>';
									$arr_status = array('0'=>'หน่วยงานเดิม','1'=>'หน่วยงานปัจจุบัน');
									$i=1;
									if(!empty($data)){
									foreach(@$data as $key => $row){
										$department_old = "";
										$department_old .= @$department_list[@$row["department_old"]];
										$department_old .= (@$faction_list[@$row["faction_old"]]== 'ไม่ระบุ')?"":"  ".@$faction_list[@$row["faction_old"]];
										$department_old .= "  ".@$level_list[@$row["level_old"]];
										
										$department_new = "";
										$department_new .= @$department_list[@$row["department"]];
										$department_new .= (@$faction_list[@$row["faction"]]== 'ไม่ระบุ')?"":"  ".@$faction_list[@$row["faction"]];
										$department_new .= "  ".@$level_list[@$row["level"]];
								?>
									<tr> 
										<td><?php echo @$i; ?></td>
										<td class="text-left"><?php echo @$department_old; ?></td>
										<td class="text-left"><?php echo @$department_new; ?></td> 
										<td><?php echo @$this->center_function->ConvertToThaiDate(@$row['date_move'],1,0); ?></td> 
										<td class="text-left"><?php echo $arr_status[@$row['status_move']];?></td>
										<td class="text-left"><?php echo @$row['note'];?></td> 
										<td class="text-center"><?php echo @$row['user_name'];?></td>
										<!--<td>
											<span class="text-edit"  onclick="open_group_move_modal('<?php echo @$row['id'] ?>');">แก้ไข</span> | 
											<span class="text-del del"  onclick="del_group_move('<?php echo @$row['id'] ?>','<?php echo @$row['member_id'] ?>')">ลบ</span>
										</td>
										-->
									</tr>
									<?php $i++; }
									}else{ ?>
									<tr><td colspan="7">ไม่พบข้อมูล</td></tr>
									<?php } ?>
								</tbody> 
							</table> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('search_member_new_modal'); ?>
<div class="modal fade" id="search_member_loan_modal" role="dialog"> 
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">ข้อมูลสมาชิก</h4>
        </div>
        <div class="modal-body">
       		<div class="input-with-icon">
					  <input class="form-control input-thick pill m-b-2" type="text" placeholder="กรอกเลขทะเบียนหรือชื่อ-สกุล" name="search_text" id="search_member_loan">
					  <span class="icon icon-search input-icon"></span>
					</div>

			<div class="bs-example" data-example-id="striped-table">
				 <table class="table table-striped">
					<tbody id="result_member_search">
					</tbody>
				</table>
			</div>
        </div>
        <div class="modal-footer">
			<input type="hidden" id="input_id">
			<button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
        </div>
      </div>
    </div>
</div>

<form action="<?php echo base_url(PROJECTPATH.'/manage_member_share/group_move_save'); ?>" method="POST" id="form_move" enctype="multipart/form-data">
<div class="modal fade" id="group_move_modal" tabindex="-1" role="dialog"> 
    <div class="modal-dialog modal-dialog-data">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3 class="modal-title">ย้ายสังกัด</h3>
        </div>
        <div class="modal-body">
		
			<input type="hidden" id="action" name="action" class="form-control" value="add">
			<input type="hidden" id="member_id" name="member_id" class="form-control" value="">
			<input type="hidden" id="id_move" name="id_move" class="form-control" value="">
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group">
						<h3>สังกัดเดิม</h3>
					</div>
				</div>
			</div>
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label col-sm-2">หน่วยงานหลัก</label>
						<div class="col-sm-10">
							<input type="hidden" id="department_old" name="department_old" class="form-control" value="" readonly>
							<input type="text" id="department_name_old" name="department_name_old" class="form-control" value="" readonly>
						</div>
					</div>
				</div>
			</div>
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label col-sm-2">อำเภอ</label>
						<div class="col-sm-4">
							<input type="hidden" id="faction_old" name="faction_old" class="form-control" value="" readonly>
							<input type="text" id="faction_name_old" name="faction_name_old" class="form-control" value="" readonly>
						</div>
						<label class="control-label col-sm-2">หน่วยงานย่อย</label>
						<div class="col-sm-4">
							<input type="hidden" id="level_old" name="level_old" class="form-control" value="" readonly>
							<input type="text" id="level_name_old" name="level_name_old" class="form-control" value="" readonly>
						</div>
					</div>
				</div>
			</div>
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group">
						<h3>สังกัดใหม่</h3>
					</div>
				</div>
			</div>
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label col-sm-2">หน่วยงานหลัก</label>
						<div class="col-sm-10">
							<div class="form-group">
								<select class="form-control m-b-1 department_new" name="department" id="department" required title="กรุณาเลือกฝ่าย" onchange="change_mem_group('department', 'faction')">
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
			</div>
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label col-sm-2">อำเภอ</label>
						<div class="col-sm-4">
							<div class="form-group" id="faction_space">
								<select class="form-control m-b-1 department_new" name="faction" id="faction" required title="กรุณาเลือกแผนก" onchange="change_mem_group('faction','level')">
									<option value="">เลือกข้อมูล</option>
									<?php foreach($faction as $key => $value){ ?>
											<option value="<?php echo $value['id']; ?>" <?php echo @$data['faction']==$value['id']?'selected':'';?>><?php echo $value['mem_group_name']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<label class="control-label col-sm-2">หน่วยงานย่อย</label>
						<div class="col-sm-4">
							<div class="form-group" id="level_space">
								<select class="form-control m-b-1 department_new" name="level" id="level" required title="กรุณาเลือกหน่วยงาน">
									<option value="">เลือกข้อมูล</option>
									<?php foreach($level as $key => $value){ ?>
										<option value="<?php echo $value['id']; ?>" <?php echo @$data['level']==$value['id']?'selected':'';?>><?php echo $value['mem_group_name']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label col-sm-2"></label>
						<label class="col-sm-4 control-label left">
							<input type="checkbox" name="status_move" id="status_move" value="1" onclick="">  
							ตั้งค่าเป็นหน่วยงานปัจจุบัน
						</label>
						<label class="control-label col-sm-2">วันที่ย้ายสังกัด</label>
						<div class="col-sm-4">
							<div class="form-group">
								<input id="date_move" name="date_move" class="form-control m-b-1 date" style="padding-left: 50px;" type="text" value="<?php echo (@$data['date_move'] == '')?$this->center_function->mydate2date(date("Y-m-d")):$this->center_function->mydate2date(empty($data) ? date("Y-m-d") : @$data['date_move']); ?>" data-date-language="th-th" required title="กรุณาป้อน วันที่ย้ายสังกัด">
								<span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label col-sm-2">หมายเหตุ</label>
						<div class="col-sm-4">
							<input type="text" id="note" name="note" class="form-control" value="">
						</div>
					</div>
				</div>
			</div>
			<div class="row m-b-1 m-t-2">
				<div class="col-sm-12">
					<div class="form-group" style="text-align:center">
						<input type="button" class="btn btn-primary" value="บันทึก" onclick="check_submit()">
					</div>
				</div>
			</div>
        </div>
      </div>
    </div>
</div>
</form>


<script>
var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
    $(".date").datepicker({
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
});		
	
function open_group_move_modal(id=''){
	if(id != ''){
		$("#action").val('edit');
		$("#id_move").val(id);
	}else{
		$("#action").val('add');
	}
	var  member_id = '<?php echo @$_GET['member_id'];?>';
	 $.ajax({
        method: 'POST',
        url: base_url+'manage_member_share/get_mem_group_move',
        data: {
            member_id : member_id,
            id : id
        },
        success: function(result){
			console.log("id="+id);
			//console.log(result);
			obj = JSON.parse(result);
			console.log(obj);
            $("#member_id").val(member_id);
            $("#department_old").val(obj.department_old);
			$("#department_name_old").val(obj.department_name_old);
            $("#faction_old").val(obj.faction_old);
			$("#faction_name_old").val(obj.faction_name_old);
            $("#level_old").val(obj.level_old);                     
            $("#level_name_old").val(obj.level_name_old);
			
			if(id != ''){
				$('#department option[value='+obj.department+']').attr('selected','selected');
				$('#faction option[value='+obj.faction+']').attr('selected','selected');
				$('#level option[value='+obj.level+']').attr('selected','selected');
				if(obj.status_move == 1){
					$("#status_move").attr("checked", true);
				}else{
					$("#status_move").attr("checked", false);
				}
				
				$("#note").val(obj.note);
			}else{
				$("#department").val('');
				$("#faction").val('');
				$("#level").val('');
				$("#status_move").attr("checked", true);
				$("#note").val('');
			}
			$("#date_move").val(obj.date_move);
        }
    });
	
	$('#group_move_modal').modal('show');
}

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

function check_submit(){
	var text_alert = '';
	if($('#department').val() == ''){
		text_alert += '-หน่วยงานหลัก\n';
	}
	if($('#faction').val() == ''){
		text_alert += '-อำเภอ\n';
	}
	if($('#level').val() == ''){
		text_alert += '-หน่วยงานย่อย\n';
	}
	
	if(text_alert != ''){
		swal("กรุณาเลือก", text_alert, "warning")
	}else{
		$('#form_move').submit();
	}
}

function del_group_move(id,member_id){	
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
				url: base_url+'/manage_member_share/del_group_move',
				method: 'POST',
				data: {
					'id': id
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'manage_member_share/group_move?member_id='+member_id;
					}else{

					}
				}
			});
        } else {
			
        }
    });	
}
</script>