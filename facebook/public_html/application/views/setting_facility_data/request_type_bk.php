<div class="layout-content">
    <div class="layout-content-body">
<style>
	.control-label{
		 text-align:right;
		 margin-top: 6px;
	 }
	 .modal-header-confirmSave {
			padding:9px 15px;
			border:1px solid #0288d1;
			background-color: #0288d1;
			color: #fff;
			-webkit-border-top-left-radius: 5px;
			-webkit-border-top-right-radius: 5px;
			-moz-border-radius-topleft: 5px;
			-moz-border-radius-topright: 5px;
			border-top-left-radius: 5px;
			border-top-right-radius: 5px;
		}
</style>
<h1 style="margin-bottom: 0">ตั้งค่าประเภทการขอ</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
    </div>

    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <a class="btn btn-primary btn-lg bt-add" onclick="add_request_type()">
            <span class="icon icon-plus-circle"></span>
            เพิ่มประเภทการขอ
        </a>
    </div>

</div>
<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12">
        <div class="panel panel-body">
            <div class="bs-example" data-example-id="striped-table">
                <div id="tb_wrap">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ประเภทการขอ</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="table_data">
                        <?php foreach($row as $key => $value){ ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $value['request_type']; ?></td>
								<td width="100px"><a onclick="edit_request_type('<?php echo $value['request_type_id']; ?>','<?php echo $value['request_type']; ?>')" style="cursor:pointer;">แก้ไข</a> | <a  style="color:red;cursor:pointer;" onclick="del_request_type('<?php echo $value['request_type_id']; ?>')">ลบ</a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="page_wrap">
            <?php echo $paging ?>
        </div>
    </div>
</div>
    </div>
</div>
<div class="modal fade" id="modal_add_request" role="dialog">
	<div class="modal-dialog modal-dialog-data">
		  <div class="modal-content data_modal">
				<div class="modal-header modal-header-confirmSave">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">บันทึกประเภทการขอ</h4>
		</div>
			<div class="modal-body">
				<form action="" id="form_add_request" method="POST">
					<input name="request_type_id" type="hidden" id="request_type_id">
					<div class="row m-b-1">
						<label class="col-md-3 control-label" >ประเภทการขอ</label>
						<div class="col-md-8">
							<input name="request_type" class="form-control" type="text" id="request_type">
						</div>
					</div>
					<div class="row">
						<div class="col-md-3"></div>
						<div class="col-md-8"><input type="button" value="บันทึก" class="btn btn-primary" onclick="check_submit()"></div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
function open_modal(id){
	$('#'+id).modal('show');
}
function check_submit(){
	var text_alert = '';
	if($('#request_type').val()==''){
		text_alert += 'ประเภทการขอ\n';
	}
	if(text_alert !=''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form_add_request').submit();
	}
}
function add_request_type(){
	$('#request_type_id').val('');
	$('#request_type').val('');
	open_modal('modal_add_request');
}
function edit_request_type(request_type_id, request_type){
	$('#request_type_id').val(request_type_id);
	$('#request_type').val(request_type);
	open_modal('modal_add_request');
}
function del_request_type(request_type_id){
	 swal({
		title: "",
		text: "ท่านต้องการลบข้อมูลใช่หรือไม่?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: "ยกเลิก",
		closeOnConfirm: true,
		closeOnCancel: true
	 },
	 function(isConfirm){
	   if (isConfirm){
			document.location.href = '?request_type_id='+request_type_id+'&do_action=delete';
		}
	 });
}
</script>