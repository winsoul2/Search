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
	}
</style>
<link rel="stylesheet" href="/html/css/custom-grid24.css">
<style type="text/css">
  .form-group{
    margin-bottom: 5px;
  }
</style>
<h1 class="title_top">ยกเลิกรายการชำระประมวลผลผ่านรายการ</h1>
<?php $this->load->view('breadcrumb'); ?>
<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12">
        <div class="panel panel-body">
            <div class="row"  style="padding-bottom:15px !important;">
                <div class="col-sm-12">
                    <label class="col-sm-2 control-label">รูปแบบค้นหา</label>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <select id="search_list" name="search_list" class="form-control m-b-1">
                                <option value="">เลือกรูปแบบค้นหา</option>
                                <option value="member_id">รหัสสมาชิก</option>
								<option value="employee_id">รหัสพนักงาน</option>
                                <option value="firstname_th">ชื่อสมาชิก</option>
                                <option value="lastname_th">นามสกุล</option>
                                <option value="receipt_id">เลขที่ใบเสร็จ</option>
                            </select>
                        </div>
                    </div>

                    <label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="input-group">
                                <input id="search_text" name="search_text" class="form-control m-b-1"
                                        type="text" value="<?php echo @$data['id_card']; ?>">
                                <span class="input-group-btn">
                                    <button type="button" onclick="check_search();"
                                            class="btn btn-info btn-search"><span
                                            class="icon icon-search"></span></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <table class="table table-bordered table-striped table-center">
                <thead> 
                    <tr class="bg-primary">
                        <th>ปี/เดือน</th>
                        <th>รหัสสมาชิก</th>
                        <th>ชื่อสกุล</th>
                        <th>วันที่ทำรายการ</th>
                        <th>ผู้ทำรายการ</th>
                        <th>เลขที่ใบเสร็จ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="table_first">
                <?php
					foreach($data as $key => $row){
                ?>
					<tr>
                        <td><?php echo $row['year_receipt'].'/'.$month_arr[$row['month_receipt']]; ?></td>
                        <td><?php echo $row['member_id']; ?></td> 
                        <td class="text-left"><?php echo $row['prename_full'].$row['firstname_th'].'  '.$row['lastname_th'];?></td>											
                        <td><?php echo $this->center_function->ConvertToThaiDate($row['receipt_datetime'],1,0);?></td>
                        <td><?php echo $row['user_name'];?></td>											
                        <td>
							<?php $is_void = (@$row['receipt_status']==2) ? "style='color: red;'" : ""; ?>
                            <a href="<?php echo base_url(PROJECTPATH.'/admin/receipt_form_pdf/'.$row['receipt_id']); ?>" target="_blank" <?php echo $is_void;?>><?php echo $row['receipt_id'];?></a>
                        </td>
                        <td>
                            <?php
                                if($row['receipt_status']!=2){
                                    ?>
                                        <button name="bt_add" id="bt_add" type="button" class="btn btn-primary" onclick="cancel('<?php echo $row['receipt_id'];?>')">
                                            <span>ยกเลิก</span>
                                        </button>
                                    <?php
                                }else{
                                    ?>
										ยกเลิกเมื่อ <?php echo $this->center_function->ConvertToThaiDate($row['cancel_date'],1,1);?>
										 โดย <?php echo $row['user_name_cancel'];?>
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
<?php echo $paging ?>
	</div>
</div>
<form action="<?php echo base_url(PROJECTPATH.'/receipt_cancel/coop_finance_month_process_receipt_cancel'); ?>" id="cancel_form" method="POST">
    <input type="hidden" name="receipt_id" id="cancel_receipt_id" value=""/>
</form>

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
					<button class="btn btn-info bt_check_submit" id="submit_confirm_user">บันทึก</button>
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
	function cancel(receipt_id){
        var title = 'ยกเลิกรายการชำระหนี้คงค้าง';
        swal({
            title: title,
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ปิดหน้าต่าง",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                // document.location.href = base_url+'/receipt_cancel/coop_finance_month_process_receipt_cancel?receipt_id='+receipt_id;
               $("#cancel_receipt_id").val(receipt_id)
               //$("#cancel_form").submit()
			   $('#modal_confirm_user').modal('show');
            } else {
            }
        });
    }
    $('#member_search').click(function(){
        if($('#search_list').val() == '') {
            swal('กรุณาเลือกรูปแบบค้นหา','','warning');
        } else if ($('#search_text').val() == ''){
            swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
        } else {
            $.ajax({  
                url: base_url+"ajax/search_member_by_type",
                method:"post",  
                data: {
                    search_text : $('#search_text').val(), 
                    search_list : $('#search_list').val()
                },  
                dataType:"text",  
                success:function(data) {
                    $('#result_member').html(data.replace("?member_id=", "?id="));  
                }  ,
                error: function(xhr){
                    console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                }
            });
        }
    });
    function check_member_id() {
		var member_id = $('#member_id').val();
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if (keycode == '13') {
			$.post(base_url + "ajax/get_member",
				{
					member_id: member_id
				}
				, function(result){
                obj = JSON.parse(result);
                mem_id = obj.member_id;
                if(mem_id != undefined){
                    document.location.href = '<?php echo base_url(uri_string())?>?member_id='+mem_id
                }else{
                    swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning');
                }
            });
		}
	}
    function check_search() {
		if ($('#search_list').val() == '') {
			swal('กรุณาเลือกรูปแบบค้นหา', '', 'warning');
		} else if ($('#search_text').val() == '') {
			swal('กรุณากรอกข้อมูลที่ต้องการค้นหา', '', 'warning');
		} else {
            document.location.href = base_url +'receipt_cancel/coop_finance_month_process_receipt_cancel?search_text='+$('#search_text').val()+"&search_list="+$('#search_list').val();
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
					permission_id : permission_id //รหัสระบบยกเลิกรายการชำระประมวลผลผ่านรายการ
				},
				dataType: 'json',
				success: function(data){
					//console.log(data);
					if(data.result=="true"){
						
						if(data.permission=="true"){
							$("#cancel_form").submit();
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
</script>
