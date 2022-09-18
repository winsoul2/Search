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
		<h1 style="margin-bottom: 0">อนุมัติการสมัคร</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
                <button name="btn_approve" id="btn_approve" type="button" class="btn btn-primary btn-lg btn-position-top" onclick="approve_page_all(6)">
                    <span>อนุมัติ</span>
                </button>
                <button name="btn_delete" id="btn_delete" type="button" class="btn btn-danger btn-lg btn-position-top" onclick="approve_page_all(5)">
                    <span>ลบ</span>
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
							<th>เลขที่คำร้อง</th>
							<th>ชื่อสมาชิก</th>
							<th>ประเภทสมาชิก</th>
							<th>ผู้ทำรายการ</th>
							<th>สถานะ</th>
							<th style="width:150px;"></th> 
						</tr> 
					 </thead>
					 <tbody id="table_first">
					  <?php 
						$cremation_status = array('0'=>'รอการอนุมัติ', '1'=>'ชำระเงินแล้ว', '5'=>'ไม่อนุมัติ', '6'=>'อนุมัติ');

						foreach($data as $key => $row ){
						?>
						  <tr>
                              <td>
                                  <?php
                                  if(@$row['cremation_status']=='1'){
                                  ?>
                                      <div class="form-check">
                                          <input class="chk-approve" type="checkbox" name="member[<?php echo $row['member_id']; ?>]" value="<?php echo $row['cremation_request_id']; ?>" data-type="<?php echo $row['cremation_status']; ?>" id="chk-mem-id">
                                          <label class="form-check-label" for="chk-approve">
                                          </label>
                                      </div>
                                  <?php }else if(@$row['cremation_status']=='5'){ ?>
                                      <div class="form-check">
                                          <input class="chk-approve" type="checkbox" name="member[<?php echo $row['member_id']; ?>]" value="<?php echo $row['cremation_request_id']; ?>" data-type="<?php echo $row['cremation_status']; ?>" id="chk-mem-id">
                                          <label class="form-check-label" for="chk-approve">
                                          </label>
                                      </div>
                                  <?php } ?>
                              </td>
							  <td><?php echo $this->center_function->ConvertToThaiDate(@$row['createdatetime']); ?></td>
							  <td><?php echo @$row['cremation_no']; ?></td>
							  <td class="text-left"><?php echo @$row['assoc_firstname']." ".@$row['assoc_lastname']; ?></td>
							  <td class="text-left"><?php echo @$row['mem_type_id'] == '1' ? 'สามัญ' : 'สมทบ'; ?></td>
							  <td class="text-left"><?php echo @$row['user_name']; ?></td> 
							  <td><span id="cremation_status_<?php echo @$row['cremation_request_id']; ?>" ><?php echo @$cremation_status[$row['cremation_status']]; ?></span></td>
							  <td>
								<?php
									if($row['cremation_status']=='1'){
								?>
									<a class="btn-radius btn-info" id="approve_<?php echo @$row['cremation_request_id']; ?>_1" title="อนุมัติ" onclick="approve_cremation('<?php echo @$row['cremation_request_id']; ?>','6')">
										อนุมัติ
									</a>
									<a class="btn-radius btn-danger" id="approve_<?php echo @$row['cremation_request_id']; ?>_2" title="ไม่อนุมัติ" onclick="approve_cremation('<?php echo @$row['cremation_request_id']; ?>','5')">
										ไม่อนุมัติ
									</a>
                                <?php
                                    }
                                ?>
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

<script>
$(document).ready(function(){
    $(".btn-refund").click(function(){
        var req_id = $(this).attr("data-req-id");
        var title = "คืนเงินค่าสมัคร";
        var text = 'จำนวน '+ 1 + ' บาท';
        swal({
            title: title,
            text: text,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#0288d1',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ปิดหน้าต่าง",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                document.location.href = base_url+'/cremation/cremation_refund_resgister_payment?id='+id+'&status_to='+status_to;
                $.post(base_url+'/cremation/cremation_refund_resgister_payment', {'id' : req_id}, function(response){
                    if(response.status === false) {
                        swal("ไม่สามารถทำรายการได้", response.error_message , 'error');
                    }else{
                        document.location.href = base_url+'/cremation/cremation_approve';
                    }
                });
            } else {

            }
        });
    });
});
function approve_cremation(id, status_to){
    var title = status_to == 6 ? 'อนุมัติการสมัคร' : 'ไม่อนุมัติการสมัคร'
    swal({
		title: title,
		text: "",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#0288d1',
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: "ปิดหน้าต่าง",
		closeOnConfirm: false,
		closeOnCancel: true
	},
	function(isConfirm) {
		if (isConfirm) {
			document.location.href = base_url+'/cremation/cremation_approve?id='+id+'&status_to='+status_to;
		} else {

		}
	});
}

function check_all_page(){
    if($('#chk-all').is(':checked') === true) {
        $('.chk-approve').prop('checked', true);
    }else{
        $('.chk-approve').prop('checked', false);
    }
}

function approve_page_all(status){
    var len = 0, chk = 0;
    var data = [];
    $('.chk-approve').each(function(e){
        if($(this).is(':checked') === true){
            var item = { id : $(this).val() , status : status};
            var type = parseInt($(this).attr('data-type'));
            console.log(type, status);
            if(status == 6 && type == 5){
                chk = 5;
            }else if(status == 6 && type == 6){
                chk = 1;
            }
            data.push(item);
            len += $(this).length;
        }
    });

    var msg = {};
    if(status === 6){
        msg.main_title = "ยืนยันการอนุมัติรายการ";
        msg.fail_title = "อนุมัติการไม่สำเร็จ";
        msg.fail_txt = "มีบางอย่างผิดพลาด กรุณาตรวจสอบอีกครั้ง";
        msg.success_title = "อนุมัติการสำเร็จแล้ว";
        msg.check_items = "กรุณาเลือกรายการที่ต้องการอนุมัติ";
    }else{
        msg.main_title = "ยืนยันการอลบรายการ";
        msg.fail_title = "ลบรายการไม่สำเร็จ";
        msg.fail_txt = "มีบางอย่างผิดพลาด กรุณาตรวจสอบอีกครั้ง";
        msg.success_title = "ลบรายการสำเร็จแล้ว";
        msg.check_items = "กรุณาเลือกรายการที่ต้องการลบ";
    }

    if(chk === 5){
        swal('กรุณาครวจสอบความถูกต้อง', 'ไม่สามารถอนุมัติรายการที่มีสถานะ ไม่อนุมัติ ได้', "error");
        return;
    }
    if(chk === 1){
        swal('กรุณาครวจสอบความถูกต้อง', 'ไม่สามารถลบรายการที่มีสถานะ อนุมัติ ได้', "error");
        return;
    }
    if(len === 0){
        swal(msg.check_items,'', 'warning');
        return;
    }

    swal({
        title: msg.main_title,
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#0288d1',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: "ยกเลิก",
        closeOnConfirm: false,
        closeOnCancel: true,
        showLoaderOnConfirm: true
    }, function(isConfirm){
        if(isConfirm){
            if(data.length > 0){
                $.post(base_url+'/cremation/cremation_multi_approve', {data : data}, function(response){
                    if(response.status === false) {
                        swal( msg.fail_title, msg.fail_title , 'error');
                    }else{
                        swal(msg.success_title, '', 'success');
                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);
                    }
                });
            }else{
                swal(msg.fail_title, 'ไม่มีข้อมูลคำร้อง', 'error');
            }
        }
    });
}

</script>