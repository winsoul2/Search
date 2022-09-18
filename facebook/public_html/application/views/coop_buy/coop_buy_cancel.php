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
<h1 class="title_top">ยกเลิกรายการซื้อ</h1>
<?php $this->load->view('breadcrumb'); ?>
<div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
			<div class="panel panel-body" style="padding-top:0px !important;">
		  <h3 >รายการขอยกเลิกรายการซื้อ</h3>
             <table class="table table-bordered table-striped table-center">
             <thead> 
                <tr class="bg-primary">
					<th>วันที่ทำรายการ</th>
					<th>เลขที่ใบสำคัญ</th>
					<th>วิธีชำระเงิน</th>
					<th>จ่ายให้</th>
					<th>จำนวนเงินรวม</th>
					<th>สถานะ</th>
					<th>จัดการ</th> 
                </tr> 
             </thead>
                <tbody id="table_first">
                  <?php 
					$pay_type = array('cash'=>'เงินสด', '2'=>'เช็คธนาคาร');
					$pay_status = array('0'=>'ปกติ', '1'=>'รออนุมัติยกเลิก', '2'=>'ยกเลิกรายการ');
					foreach($data as $key => $row){ ?>
					  <tr> 
						  <td><?php echo $this->center_function->ConvertToThaiDate($row['cancel_date']); ?></td>
						  <td><?php echo $row['account_buy_number']; ?></td> 
						  <td><?php echo $pay_type[$row['pay_type']]; ?></td> 
						  <td><?php echo $row['pay_for']; ?></td> 
						  <td><?php echo number_format($row['total_amount'],2); ?></td> 
						  <td><?php echo $pay_status[$row['account_buy_status']]; ?></td> 
						  <td style="font-size: 18px;">
							<?php if($row['account_buy_status']=='1'){ ?>
								<a class="link-line-none" href="#" title="อนุมัติยกเลิกรายการซื้อ" onclick="cancel_buy('<?php echo $row['account_buy_id']; ?>','2')">
									<span style="cursor: pointer;color:red;" class="icon icon-times-circle-o"></span>
								</a>
							<?php } ?>
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
var base_url = $('#base_url').attr('class');
	function cancel_buy(account_buy_id, status_to){
		 var title = 'ยกเลิกรายการซื้อ';
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
				document.location.href = base_url+'coop_buy/coop_buy_cancel?account_buy_id='+account_buy_id+'&status_to='+status_to;
			} else {
				
			}
		});
	 }
</script>