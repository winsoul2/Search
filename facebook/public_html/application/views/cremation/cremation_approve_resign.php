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
        .btn-position-top{
            margin-top:  -1em;
        }
		</style> 
		<h1 style="margin-bottom: 0">อนุมัติการลาออก</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
                <button id="btn_approve" type="button" class="btn btn-primary btn-lg btn-position-top">
                    <span>อนุมัติ</span>
                </button>
                <button id="btn_reject" type="button" class="btn btn-danger btn-lg btn-position-top">
                    <span>ไม่อนุมัติ</span>
                </button>
            </div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<h3 ></h3>
                    <table class="table table-bordered table-striped table-center">
                        <thead> 
                            <tr class="bg-primary">
                                <th style="width: 45px;">
                                    <div class="form-check">
                                        <input type="checkbox" name="chk-approve" value="" id="chk-all" onclick="check_all_page()">
                                    </div>
                                </th>
                                <th style="width:150px;">วันที่ทำรายการ</th>
                                <th>เลขฌาปนกิจ</th>
                                <th>ชื่อสมาชิก</th>
                                <th>ประเภทสมาชิก</th>
                                <th>ผู้ทำรายการ</th>
                                <th>สถานะ</th>
                                <th style="width:150px;"></th> 
                            </tr> 
                        </thead>
                        <tbody id="table_first">
                            <form action="" id="form1" method="POST" enctype="multipart/form-data">
                                <input type="hidden" id="action" name="action" value=""/>
                            <?php
                                $cremation_status = array('0'=>'รอการอนุมัติ','1'=>'อนุมัติ','2'=>'ไม่อนุมัติ','3'=>'โอนเงินแล้ว');
                                foreach($data as $key => $row ){
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                            if(@$row['status']=='0'){
                                        ?>
                                            <div class="form-check">
                                                <input class="chk-approve" type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>" data-type="<?php echo $row['status']; ?>" id="chk-mem-id-<?php echo $row['id'];?>">
                                                <label class="form-check-label" for="chk-approve">
                                                </label>
                                            </div>
                                        <?php
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo $this->center_function->ConvertToThaiDate($row['created_at']); ?></td>
                                    <td><?php echo @$row['member_cremation_id']; ?></td>
                                    <td class="text-left"><?php echo $row['prename_full'].$row['assoc_firstname']." ".$row['assoc_lastname']; ?></td>
                                    <td class="text-left"><?php echo $row['mem_type_id'] == '1' ? 'สามัญ' : 'สมทบ'; ?></td>
                                    <td class="text-left"><?php echo $row['user_name']; ?></td> 
                                    <td><span id="cremation_status_<?php echo @$row['cremation_request_id']; ?>" ><?php echo @$cremation_status[$row['status']]; ?></span></td>
                                    <td>
                                        <?php 
                                            if(@$row['status']=='0'){
                                        ?>
                                            <a class="btn-radius btn-info approve_btn" data-id="<?php echo $row['id']; ?>" id="approve_<?php echo @$row['id']; ?>" title="อนุมัติ">
                                                อนุมัติ
                                            </a>
                                            <a class="btn-radius btn-danger reject_btn" data-id="<?php echo $row['id']; ?>" id="reject_<?php echo @$row['id']; ?>" title="ไม่อนุมัติ">
                                                ไม่อนุมัติ
                                            </a>
                                        <?php
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </form>
                        </tbody> 
                    </table> 
                </div>
            </div>
		</div>
		<?php echo @$paging ?>
	</div>
</div>
<form action="" id="form2" method="POST" enctype="multipart/form-data">
    <input type="hidden" id="form2_action" name="action" value=""/>
    <input type="hidden" id="form2_id" name="id" value=""/>
</form>
<script>
$(document).ready(function(){
    $("#btn_approve").click(function(){
        swal({
            title: 'อนุมัติการสมัคร',
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#0288d1',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                $("#action").val("approve_all");
                $("#form1").submit();
            }
        });
    });
    $("#btn_reject").click(function(){
        swal({
            title: 'ไม่อนุมัติการลาออก',
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#0288d1',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                $("#action").val("reject_all");
                $("#form1").submit();
            }
        });
    });
    $(".approve_btn").click(function(){
        id = $(this).attr('data-id');
        swal({
            title: 'อนุมัติการลาออก',
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#0288d1',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                $("#form2_action").val("approve");
                $("#form2_id").val(id);
                $("#form2").submit();
            }
        });
    });
    $(".reject_btn").click(function(){
        id = $(this).attr('data-id');
        swal({
            title: 'ไม่อนุมัติการลาออก',
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#0288d1',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                $("#form2_action").val("reject");
                $("#form2_id").val(id);
                $("#form2").submit();
            }
        });
    });
});

function check_all_page(){
    if($('#chk-all').is(':checked') === true) {
        $('.chk-approve').prop('checked', true);
    }else{
        $('.chk-approve').prop('checked', false);
    }
}
</script>