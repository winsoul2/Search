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
  .form-group{
    margin-bottom: 5px;
  }
</style>
<h1 class="title_top">ยกเลิกการกู้เงิน</h1>
<?php $this->load->view('breadcrumb'); ?>
<div class="row gutter-xs">

        <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
		  <h3 >รายการขอยกเลิกการกู้เงิน</h3>
             <table class="table table-bordered table-striped table-center">
             <thead> 
                <tr class="bg-primary">
					<th>วันที่ทำรายการ</th>
					<th>ชื่อสมาชิก</th>
					<th>เลขที่สัญญา</th>
					<th>ประเภทการกู้เงิน</th>
					<th>ยอดเงินกู้</th>
					<th>ผู้ทำรายการ</th>
					<th>สถานะ</th>
					<th>จัดการ</th> 
                </tr> 
             </thead>
                <tbody id="table_first">
                  <?php 
					$loan_status = array('2'=>'รอการยืนยัน', '3'=>'ยกเลิกรายการแล้ว');
					
					foreach($data as $key => $row){ ?>
					  <tr> 
						  <td><?php echo $this->center_function->ConvertToThaiDate(@$row['cancel_date']); ?></td>
						  <td><?php echo @$row['firstname_th']." ".@$row['lastname_th']; ?></td> 
						  <td><?php echo @$row['contract_number']; ?></td> 
						  <td><?php echo @$loan_type[@$row['loan_type']]; ?></td> 
						  <td><?php echo number_format(@$row['loan_amount'],2); ?></td> 
						  <td><?php echo @$row['user_name']; ?></td> 
						  <td><span id="loan_status_<?php echo @$row['loan_id']; ?>" ><?php echo @$loan_status[@$row['loan_status']]; ?></span></td>
						  <td style="font-size: 18px;">
							<?php 
								if($row['loan_status']=='3'){
									$display_1 = 'display:none;'; 
									$display_2 = ''; 
								}else if($row['loan_status']=='2'){
									$display_1 = ''; 
									$display_2 = 'display:none;'; 
								}else{
									$display_1 = 'display:none;'; 
									$display_2 = 'display:none;'; 
								}
							?>
								<a class="link-line-none" id="cancel_<?php echo @$row['loan_id']; ?>_1" data-toggle="modal" data-target="#confirmCancel" id="confirmCancelModal" class="fancybox_share fancybox.iframe" href="#" title="ยกเลิกการกู้เงิน" onclick="cancel_loan('<?php echo @$row['loan_id']; ?>','3')" style="<?php echo $display_1; ?>">
									<span style="cursor: pointer;color:red;" class="icon icon-times-circle-o"></span>
								</a>
								<a class="link-line-none" id="cancel_<?php echo @$row['loan_id']; ?>_2" data-toggle="modal" data-target="#confirmCancel" id="confirmCancelModal" class="fancybox_share fancybox.iframe" href="#" title="ยกเลิกการยกเลิกการกู้เงิน" onclick="cancel_loan('<?php echo @$row['loan_id']; ?>','2')" style="<?php echo $display_2; ?>">
									<span style="cursor: pointer;" class="icon icon-times-circle-o"></span>
								</a>
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
<script>
 function cancel_loan(loan_id, status_to){
	 if(status_to=='2'){
		 var title = 'ยกเลิกการยกเลิกคำขอกู้เงิน';
	 }else{
		 var title = 'ยกเลิกคำขอกู้เงิน';
	 }
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
           document.location.href = base_url+'/loan/loan_cancel?loan_id='+loan_id+'&status_to='+status_to;
        } else {
			
        }
    });
 }
</script>