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
	.modal.fade {
	  z-index: 10000000 !important;
	}
	.form-group{
		margin-bottom: 5px;
	}
	
	.text-p{
		font-family: upbean;
	}
</style>
<h1 style="margin-bottom: 0">งดหุ้น</h1>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php 
		if(@$member_id != ''){
		?>
		<a>
			<button type="button" class="btn btn-primary btn-lg bt-add" id="permanent" onclick="refrain_permanent();">งดหุ้นถาวร</button>
		</a>
		
		<a>
			<button class="btn btn-primary btn-lg bt-add" type="button" style="margin-right:10px;" id="temporary" onclick="refrain_temporary();">งดหุ้นชั่วคราว</button>
		</a>
		
		<?php 
		}
		?>
	</div>
</div>
<div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                <?php $this->load->view('search_member_new'); ?>
				<div class="" style="padding-top:0;">
				<div class="g24-col-sm-24">
					<div class="form-group g24-col-sm-8">
						<label class="g24-col-sm-10 control-label ">ส่งหุ้นแล้ว</label>
						<div class="g24-col-sm-14">
							<input class="form-control" type="text" value="<?php echo (@$member_id == '')?'':number_format(@$cal_share,2);?>"  readonly>
						</div>
					</div>
					<div class="form-group g24-col-sm-8">
						<label class="g24-col-sm-10 control-label ">ภาระหนี้รวม</label>
						<div class="g24-col-sm-14">
							<input class="form-control" type="text" value="<?php echo (@$member_id == '')?'':number_format(@$sum_debt_balance,2);?>"  readonly>
						</div>
					</div>
					<div class="form-group g24-col-sm-8">
						<label class="g24-col-sm-10 control-label ">ภาระค้ำประกัน</label>
						<div class="g24-col-sm-14">
							<input class="form-control" type="text" value="<?php echo (@$member_id == '')?'':number_format(@$sum_guarantee_balance,2);?>"  readonly>
						</div>
					</div>
				</div>

				<div class="g24-col-sm-24">
					<div class="form-group g24-col-sm-8">
						<label class="g24-col-sm-10 control-label ">สถานะ</label>
						<div class="g24-col-sm-14">
							<input class="form-control" type="text" id="type_refrain_name" name="type_refrain_name" value="<?php echo @$status_refrain;?>" readonly>
							<input class="form-control" type="hidden" id="type_refrain" name="type_refrain" value="<?php echo @$type_refrain;?>" readonly>
						</div>
					</div>                 
      			</div>
		
				<div class="g24-col-sm-24 m-t-1">
				  <div class="bs-example" data-example-id="striped-table">
					 <table class="table table-bordered table-striped table-center">
					 <thead> 
						<tr class="bg-primary">
							<th>ลำดับ</th>
							<th>วันที่ทำรายการ</th>
							<th>ประเภทการงดหุ้น</th>
							<th>เดือน/ปี</th>
							<th>ยอดเงิน</th>
							<th width="30%">ผู้ทำรายการ</th> 
							<th></th>  
						</tr> 
					 </thead>
						<tbody id="table_first">
						  <?php
						  $i = 0;
						  foreach($data as $key => $row){ 
							$i++;
							$mmyy = (@$row['month_refrain'] != '')?@$month_arr[@$row['month_refrain']]."/".@$row['year_refrain']:'';
						   ?>
						 
						  <tr> 
						  <td><?php echo @$i; ?></td>
						  <td><?php echo @$this->center_function->ConvertToThaiDate(@$row['createdatetime']); ?></td>
						  <td><?php echo @$type_refrain_list[@$row['type_refrain']]; ?></td> 
						  <td><?php echo @$mmyy; ?></td> 
						  <td><?php echo number_format(@$row['total_amount'],2); ?></td>
						  <td><?php echo @$row['user_name']; ?></td> 
						  <td style="padding:0px;vertical-align:middle;">
							 <span class="text-del del"  onclick="del_coop_refrain_share('<?php echo @$row['refrain_id'] ?>')">ลบ</span>
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

	</div>
</div>

<div class="modal fade" id="refrainPermanentModal"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
      <div class="modal-content">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h2 class="modal-title">งดหุ้นถาวร</h2>
        </div>
        <div class="modal-body center">
		<form action="" method="POST" id="form_permanent">

			<div class="form-group g24-col-sm-24" style="font-size:24px;">
				<div id="show_no">
					<p class="g24-col-sm-24 text-p">ไม่สามารถงดหุ้นถาวรได้</p>
					<p class="g24-col-sm-24 text-p">เนื่องจากท่านต้องส่งค่าหุ้นแล้วไม่น้อยกว่า 180 เดือน</p>
					<p class="g24-col-sm-24 text-p">และไม่มีหนี้ ไม่ติดค้ำประกัน</p>
				</div>	
				<div id="show_ok">				
					<p class="g24-col-sm-24"><span class="fa fa-check-circle-o" style="font-size: 4em;color: #467542;"></span></p>
					<p class="g24-col-sm-24 text-p">ทำการงดหุ้นเรียบร้อย</p>
				</div>
			</div>
		</form>
        
          <button class="btn btn-info" data-dismiss="modal">ปิดหน้าต่าง</button>
        </div>
      </div>
    </div>
</div>

<div class="modal fade" id="refrainTemporaryModal"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
      <div class="modal-content">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h2 class="modal-title">งดหุ้นชั่วคราว</h2>
        </div>
        <div class="modal-body center">
					<form action="" method="POST" id="form_temporary">
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-8 control-label right"> เริ่มเดือน </label>
							<div class="g24-col-sm-5">
								<select id="from_month" name="from_month" class="form-control">
									<?php foreach($month_arr as $key => $value){ ?>
										<option value="<?php echo $key; ?>" <?php echo $key==((int)date('m'))?'selected':''; ?>><?php echo $value; ?></option>
									<?php } ?>
								</select>
							</div>
							<label class="g24-col-sm-2 control-label right"> ปี </label>
							<div class="g24-col-sm-5">
								<select id="from_year" name="from_year" class="form-control">
									<?php for($i=((date('Y')+543)); $i<=((date('Y')+543)+5); $i++){ ?>
										<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<label class="g24-col-sm-8 control-label right"> ถึงเดือน </label>
							<div class="g24-col-sm-5">
								<select id="to_month" name="to_month" class="form-control">
									<?php foreach($month_arr as $key => $value){ ?>
										<option value="<?php echo $key; ?>" <?php echo $key==((int)date('m'))?'selected':''; ?>><?php echo $value; ?></option>
									<?php } ?>
								</select>
							</div>
							<label class="g24-col-sm-2 control-label right"> ปี </label>
							<div class="g24-col-sm-5">
								<select id="to_year" name="to_year" class="form-control">
									<?php for($i=((date('Y')+543)); $i<=((date('Y')+543)+5); $i++){ ?>
										<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</div>
					</form>
					<div class="form-group g24-col-sm-24 text-center">
					<br>
					</div>
				</div>
				<div class="modal-footer center" style="border-top:0;">
					<div class="form-group g24-col-sm-24 text-center">
						<button class="btn btn-info" onclick="save_refrain_temporary();">บันทึก</button>
					</div>
				</div>
      </div>
    </div>
</div>

<div class="modal fade" id="refrainTemporaryConfirmModal"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
      <div class="modal-content">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h2 class="modal-title">งดหุ้นชั่วคราว</h2>
        </div>
        <div class="modal-body center">
			<form action="<?php echo base_url(PROJECTPATH.'/refrain_share/save_refrain_confirm_temporary'); ?>" method="POST" id="form_temporary_confirm">			
				<input type="hidden" name="member_id" id="member_id">
				<input type="hidden" name="from_month_refrain" id="from_month_refrain">
				<input type="hidden" name="from_year_refrain" id="from_year_refrain">
				<input type="hidden" name="to_month_refrain" id="to_month_refrain">
				<input type="hidden" name="to_year_refrain" id="to_year_refrain">
				<div id="show_no_t">				
					<div class="form-group g24-col-sm-24" style="font-size:24px;">				
						<p class="g24-col-sm-24"><span class="fa fa-times-circle-o" style="font-size: 4em;color: #d50000;"></span></p>
						<p class="g24-col-sm-24 text-p">ไม่สามารถงดหุ้นชั่วคราวได้</p>
						<p class="g24-col-sm-24 text-p">เนื่องจากเกินจำนวนครั้งที่กำหนด</p>
					</div>
				      
					<button class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
					<button class="btn btn-info" >ยืนยัน</button>
				</div>	
		   
				<div id="show_ok_t">	
					<div class="form-group g24-col-sm-24" style="font-size:24px;">	
						<p class="g24-col-sm-24"><span class="fa fa-check-circle-o" style="font-size: 4em;color: #467542;"></span></p>
						<p class="g24-col-sm-24 text-p">ทำการงดหุ้นเรียบร้อย</p>
					</div>
					<button class="btn btn-info" data-dismiss="modal">ปิดหน้าต่าง</button>					
				</div>      
			      
			</form>  
        </div>
      </div>
    </div>
</div>

<div class="modal fade" id="checkRrefrainTemporaryModal"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
      <div class="modal-content">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h2 class="modal-title">แจ้งเตือน</h2>
        </div>
        <div class="modal-body center">
			<form action="" method="POST" id="form_alert">	
				<div class="form-group g24-col-sm-24" style="font-size:24px;">	
					<p class="g24-col-sm-24 text-p">เดือนนี้ได้มีการงดหุ้นชั่วคราวแล้ว</p>
					<p class="g24-col-sm-24 text-p">กรุณาเลือกเดือนใหม่</p>
				</div>
				<button class="btn btn-info" data-dismiss="modal">ปิดหน้าต่าง</button>					
			</form>  
        </div>
      </div>
    </div>
</div>

<?php $this->load->view('search_member_new_modal'); ?>
<script>
	$( document ).ready(function() {	
		if($("#type_refrain").val() == 1){
			$("#permanent").hide();
			$("#temporary").hide();
		}else{
			$("#permanent").show();
			$("#temporary").show();
		}		
	});			

	function refrain_permanent(){
		$.post(base_url+"refrain_share/save_refrain_permanent", 
		{	
			member_id: '<?php echo $member_id; ?>'
		}
		, function(data){
			//console.log(data);
			if(data == 'ok'){
				$("#show_no").hide();
				$("#show_ok").show();
				$("#type_refrain").val(1);
				$("#type_refrain_name").val('งดถาวร');
				$("#permanent").hide();
				$("#temporary").hide();
				get_refrain();
			}else{
				$("#show_no").show();
				$("#show_ok").hide();
			}					
			$('#refrainPermanentModal').modal('show');
		});		
	}
	function refrain_temporary(){	
		$('#refrainTemporaryModal').modal('show');		
	}
	
	function save_refrain_temporary(){		
		var from_month = $("#from_month").val();
		var from_year = $("#from_year").val();
		var to_month = $("#to_month").val();
		var to_year = $("#to_year").val();
		
		$("#from_month_refrain").val(from_month);
		$("#from_year_refrain").val(from_year);
		$("#to_month_refrain").val(to_month);
		$("#to_year_refrain").val(to_year);
		$("#member_id").val('<?php echo $member_id; ?>');
		
		$.post(base_url+"refrain_share/check_refrain_temporary", 
		{	
			member_id: '<?php echo $member_id; ?>',
			from_month_refrain: from_month,
			from_year_refrain: from_year,
			to_month_refrain: to_month,
			to_year_refrain: to_year
		}
		, function(result){
			if(result == 'ok'){
				$.post(base_url+"refrain_share/save_refrain_temporary", 
				{	
					member_id: '<?php echo $member_id; ?>',
					from_month_refrain: from_month,
					from_year_refrain: from_year,
					to_month_refrain: to_month,
					to_year_refrain: to_year
				}
				, function(data){
					console.log(data);
					if(data == 'ok'){
						$("#show_no_t").hide();
						$("#show_ok_t").show();
						get_refrain();
					}else{
						$("#show_no_t").show();
						$("#show_ok_t").hide();
					}
					$('#refrainTemporaryModal').modal('hide');	
					$('#refrainTemporaryConfirmModal').modal('show');
				});	
			}else{				
				$('#checkRrefrainTemporaryModal').modal('show');
				return false; 
			}
		});	
	}
	
	function get_refrain(){
		$.post(base_url+"refrain_share/get_refrain", 
		{	
			member_id: '<?php echo $member_id; ?>'
		}
		, function(data){
			$("#table_first").html(data);
		});			
	}
	
	function del_coop_refrain_share(id){	
		var member_id ='<?php echo $member_id; ?>';
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
					url: base_url+'/refrain_share/del_coop_refrain_share',
					method: 'POST',
					data: {
						'id': id
					},
					success: function(msg){
						if(msg == 1){
						  document.location.href = base_url+'refrain_share?member_id='+member_id;
						}else{

						}
					}
				});
			} else {
				
			}
		});
		
	}
</script>