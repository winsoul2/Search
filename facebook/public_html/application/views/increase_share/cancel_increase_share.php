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

<style type="text/css">
  .form-group{
    margin-bottom: 5px;
  }
</style>
<h1 style="margin-bottom: 0">ยกเลิกการเพิ่ม/ลดหุ้น</h1>
<?php $this->load->view('breadcrumb'); ?>
<div class="row gutter-xs">

        <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
		  <h3 >รายการขอยกเลิกการเพิ่ม/ลดหุ้น</h3>
             <table class="table table-bordered table-striped table-center">
             <thead> 
                <tr class="bg-primary">
					<th>วันที่ทำรายการ</th>
					<th>ชื่อสมาชิก</th>
					<th>รายการ</th>
					<th>จำนวนหุ้น</th>
					<th>ยอดเงิน</th>
					<th>ผู้ทำรายการ</th>
					<th>สถานะ</th>
					<th>จัดการ</th> 
                </tr> 
             </thead>
                <tbody id="table_first">
                  <?php 
				  $change_type = array('increase'=>'เพิ่มหุ้น', 'decrease'=>'ลดหุ้น');
				  $change_share_status = array('2'=>'รอการยืนยัน', '3'=>'ยกเลิกรายการแล้ว');
				  foreach($data as $key => $row){ ?>
					  <tr> 
						  <td><?php echo $this->center_function->ConvertToThaiDate($row['cancel_date']); ?></td>
						  <td><?php echo $row['prename_short'].$row['firstname_th']." ".$row['lastname_th']; ?></td> 
						  <td><?php echo $change_type[$row['change_type']]; ?></td> 
						  <td><?php echo $row['change_value']; ?></td> 
						  <td><?php echo number_format($row['change_value_price'],2); ?></td> 
						  <td><?php echo $row['user_name']; ?></td> 
						  <td><span id="change_share_status_<?php echo $row['change_share_id']; ?>" ><?php echo $change_share_status[$row['change_share_status']]; ?></span></td>
						  <td style="font-size: 18px;">
							<?php 
								if($row['change_share_status']=='3'){
									$display_1 = 'display:none;'; 
									$display_2 = ''; 
								}else if($row['change_share_status']=='2'){
									$display_1 = ''; 
									$display_2 = 'display:none;'; 
								}else{
									$display_1 = 'display:none;'; 
									$display_2 = 'display:none;'; 
								}
							?>
								<a class="link-line-none" id="cancel_<?php echo $row['change_share_id']; ?>_1" title="ยกเลิกการเพิ่ม/ลดหุ้น" onclick="cancel_change_share('<?php echo $row['change_share_id']; ?>','3')" style="<?php echo $display_1; ?>">
									<span style="cursor: pointer;color:red;" class="icon icon-times-circle-o"></span>
								</a>
								<a class="link-line-none" id="cancel_<?php echo $row['change_share_id']; ?>_2" title="ยกเลิกการยกเลิกเพิ่ม/ลดหุ้น" onclick="cancel_change_share('<?php echo $row['change_share_id']; ?>','2')" style="<?php echo $display_2; ?>">
									<span style="cursor: pointer;" class="icon icon-times-circle-o"></span>
								</a>
						  </td>
					  </tr>
                  <?php } ?>
                  </tbody> 
                  </table> 
          </div>
          </div>
					<input type="hidden" id="change_share_id">
					<input type="hidden" id="status_to">
                </div>
                  <?php echo @$paging ?>
	  </div>
</div>
<script>
function get_change_share_id(change_share_id, status_to){
	 $('#change_share_id').val(change_share_id);
	 $('#status_to').val(status_to);
	 if(status_to=='2'){
		$('#btn_cancel').html('ยกเลิกการยกเลิกเพิ่ม/ลดหุ้น');
	 }else if(status_to=='3'){
		$('#btn_cancel').html('ยืนยันการยกเลิกเพิ่ม/ลดหุ้น');
	 }
 }
 
 function cancel_change_share(change_share_id,status_to){	
	swal({
        title: "ยืนยันการยกเลิกเพิ่ม/ลดหุ้น",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: "ยกเลิก",
        closeOnConfirm: false,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {			
			$.ajax({
				url: '',
				method: 'POST',
				data: {
					cancel_change_share: "1", 
					change_share_id: change_share_id,
					status_to:status_to
				},
				success: function(msg){
				  if(status_to=='2'){
					$('#cancel_'+change_share_id+'_1').show();
					$('#cancel_'+change_share_id+'_2').hide();
					$('#change_share_status_'+change_share_id).html('รอการยืนยัน');
					
				 }else if(status_to=='3'){
					$('#cancel_'+change_share_id+'_2').show();
					$('#cancel_'+change_share_id+'_1').hide();
					$('#change_share_status_'+change_share_id).html('ยกเลิกรายการแล้ว');
				 }
				 swal.close();
				}
			});
        } else {
			
        }
    });
	
}
</script>