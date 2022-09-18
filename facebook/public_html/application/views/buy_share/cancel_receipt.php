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
		<h1 style="margin-bottom: 0">ยกเลิกใบเสร็จ</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
		  <h3 >รายการขอยกเลิกใบเสร็จ</h3>
             <table class="table table-bordered table-striped table-center">
             <thead> 
                <tr class="bg-primary">
					<th>วันที่ทำรายการ</th>
					<th>เลขที่ใบเสร็จ</th>
					<th>รายการ</th>
					<th>ยอดเงิน</th>
					<th>ผู้ทำรายการ</th>
					<th>สถานะ</th>
					<th>จัดการ</th> 
                </tr> 
             </thead>
                <tbody id="table_first">
                  <?php 
				  $receipt_status = array('1'=>'รอการยืนยัน', '2'=>'ยกเลิกใบเสร็จแล้ว');
				  foreach($data as $key => $row){ ?>
					  <tr> 
						  <td><?php echo $this->center_function->ConvertToThaiDate(@$row['cancel_date']); ?></td>
						  <td><?php echo @$row['receipt_id']; ?></td>
						  <td class="set_left">
							<?php
								foreach(@$row['receipt_detail'] as $key2 => $value2){
									echo @$account_list[$value2['receipt_list']]."<br>";
								}
							?>
						  </td> 
						  <td><?php echo @$row['sumcount']; ?></td> 
						  <td><?php echo @$row['user_name']; ?></td> 
						  <td><span id="receipt_status_<?php echo @$row['receipt_id']; ?>" ><?php echo @$receipt_status[$row['receipt_status']]; ?></span></td>
						  <td style="font-size: 18px;padding:0px;vertical-align:middle;">
							<?php 
								if(@$row['receipt_status']=='2'){
									$display_1 = 'display:none;'; 
									$display_2 = ''; 
								}else if(@$row['receipt_status']=='1'){
									$display_1 = ''; 
									$display_2 = 'display:none;'; 
								}else{
									$display_1 = 'display:none;'; 
									$display_2 = 'display:none;'; 
								}
							?>
							<span id="status_<?php echo @$row['receipt_id']; ?>_1" style="<?php echo $display_1; ?>">
								<a class="link-line-none" id="cancel_<?php echo @$row['receipt_id']; ?>" title="ยกเลิกใบเสร็จ" onclick="cancel_receipt('<?php echo @$row['receipt_id']; ?>','2')" >
									<span style="cursor: pointer;color:red;" class="icon icon-times-circle-o"></span>
								</a>
							</span>
							<span id="status_<?php echo @$row['receipt_id']; ?>_2" style="<?php echo $display_2; ?>">
								<a class="link-line-none" id="cancel_<?php echo @$row['receipt_id']; ?>" title="ยกเลิกการยกเลิกใบเสร็จ" onclick="cancel_receipt('<?php echo @$row['receipt_id']; ?>','1')" >
									<span style="cursor: pointer;" class="icon icon-times-circle-o"></span>
								</a>
							</span>
						  </td>
					  </tr>
                  <?php } ?>
                  </tbody> 
                  </table> 
          </div>
          </div>
			<input type="hidden" id="receipt_id">
			<input type="hidden" id="status_to">
			<input type="hidden" id="receipt_list">
                </div>
                  <?php echo $paging ?>
	</div>
</div>

<script>
function get_receipt_id(receipt_id,status_to){
	 $('#receipt_id').val(receipt_id);
	 $('#status_to').val(status_to);
	 if(status_to=='1'){
		$('#btn_cancel').html('ยกเลิกการยกเลิกใบเสร็จ');
	 }else if(status_to=='2'){
		$('#btn_cancel').html('ยืนยันการยกเลิกใบเสร็จ');
	 }
 }

 function cancel_receipt(receipt_id,status_to){	
	swal({
        title: "ท่านต้องการยกเลิกใบเสร็จใช่หรือไม่?",
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
					cancel_receipt: "1", 
					receipt_id: receipt_id,
					status_to: status_to
				},
				success: function(msg){
					if(status_to=='1'){
						$('#status_'+receipt_id+'_1').show();
						$('#status_'+receipt_id+'_2').hide();
						$('#receipt_status_'+receipt_id).html('รอการยืนยัน');
					}else if(status_to=='2'){
						$('#status_'+receipt_id+'_2').show();
						$('#status_'+receipt_id+'_1').hide();
						$('#receipt_status_'+receipt_id).html('ยกเลิกใบเสร็จแล้ว');
					}
					swal.close();
				}
			});
        } else {
			
        }
    });
	
}
</script>