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
</style>
<h1 style="margin-bottom: 0">เพิ่ม/ลดหุ้น</h1>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<a class="link-line-none" class="fancybox_share fancybox.iframe" href="<?php echo base_url(PROJECTPATH.'/increase_share'); ?>" style="float:right;">
			<button class="btn btn-primary btn-lg bt-add" type="button"><span class="icon icon-plus-circle"></span> เพิ่มรายการใหม่</button>
		</a>
	</div>
</div>

<div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                <?php $this->load->view('search_member_new'); ?>
				<div class="" style="padding-top:0;">
                <h3 >ข้อมูลหุ้น</h3>
				<div class="g24-col-sm-24">
					<div class="form-group g24-col-sm-8">
						<label class="g24-col-sm-10 control-label ">จำนวนหุ้นสะสม</label>
						<div class="g24-col-sm-14">
							<input class="form-control" type="text" value="<?php echo number_format($count_share,0); ?>"  readonly>
						</div>
					</div>
					<div class="form-group g24-col-sm-8">
						<label class="g24-col-sm-10 control-label ">คิดเป็นมูลค่า</label>
						<div class="g24-col-sm-14">
							<input class="form-control" type="text" value="<?php echo number_format($cal_share,2); ?>"  readonly>
						</div>
					</div>
				</div>
				<?php 
					
				?>
			  <div class="g24-col-sm-24">
				  <div class="form-group g24-col-sm-8">
						<label class="g24-col-sm-10 control-label ">เดิมส่งจำนวน</label>
						<div class="g24-col-sm-14">
							<input class="form-control" type="text" value="<?php echo number_format($share_per_month,0); ?>" readonly>
						</div>
                  </div>
				  
				  <div class="form-group g24-col-sm-8">
						<label class="g24-col-sm-10 control-label ">คิดเป็นมูลค่า</label>
						<div class="g24-col-sm-14">
							<input class="form-control " type="text" value="<?php echo number_format($share_per_month*$share_value,2); ?>"  readonly>
						</div>
                  </div>
				  
				  <div class="form-group g24-col-sm-8">
						<div class="g24-col-sm-24">
						<button class="btn btn-primary btn-after-input" onclick="return check_form()"><span class="icon icon-plus-square-o" style="vertical-align: middle;"></span><span> เพิ่ม/ลดหุ้น</<span></button>
						</div>
                  </div>
                  
      			</div>

			</div>
			<span style="display:none;"><a class="link-line-none" data-toggle="modal" data-target="#confirmIncrease" id="confirmIncreaseModal" class="fancybox_share fancybox.iframe" href="#"></a></span>
			
			<span style="display:none;"><a class="link-line-none" data-toggle="modal" data-target="#alert" id="alertModal" class="fancybox_share fancybox.iframe" href="#"></a></span>
         <div class="g24-col-sm-24 m-t-1">
          <div class="bs-example" data-example-id="striped-table">
             <table class="table table-bordered table-striped table-center">
             <thead> 
                <tr class="bg-primary">
					<th>วันที่ทำรายการ</th>
					<th>รายการ</th>
					<th >จำนวนหุ้น</th>
					<th>ยอดเงิน</th> 
					<th>มีผลวันที่</th>
					<th width="30%">ผู้ทำรายการ</th> 
					<th>สถานะ</th> 
					<th>จัดการ</th> 
                </tr> 
             </thead>
                <tbody id="table_first">
                  <?php
				  $change_type = array('increase'=>'เพิ่มหุ้น', 'decrease'=>'ลดหุ้น');
				  $change_share_status = array('1'=>'ปกติ', '2'=>'รออนุมัติยกเลิก', '3'=>'ยกเลิกรายการ');
				  foreach($data as $key => $row){ 
                   ?>
                  <tr> 
                  <td><?php echo @$this->center_function->ConvertToThaiDate(@$row['create_date']); ?></td>
                  <td ><?php echo @$change_type[@$row['change_type']]; ?></td> 
                  <td ><?php echo number_format(@$row['change_value'],2); ?></td> 
                  <td ><?php echo number_format(@$row['change_value_price'],2); ?></td>
				  <td ><?php echo @$this->center_function->mydate2date(@$row['active_date']); ?></td>
				  <td ><?php echo @$row['user_name']; ?></td> 
				  <td><span id="change_share_status_<?php echo @$row['change_share_id']; ?>"><?php echo @$change_share_status[@$row['change_share_status']];?></span></td>
                  <td style="padding:0px;vertical-align:middle;">
				  <?php if($row['change_share_status']=='1'){ ?>
				  <a class="link-line-none" id="cancel_<?php echo @$row['change_share_id']; ?>" style="font-size: 19px;" data-toggle="modal" data-target="#confirmCancel" id="confirmCancelModal" class="fancybox_share fancybox.iframe" href="#" alt="ยกเลิกรายการ" title="ยกเลิกรายการ" onclick="get_id('change_share_id','<?php echo @$row['change_share_id']; ?>')">
					<span style="cursor: pointer;" class="icon icon-times-circle-o"></span></a>
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
<input type="hidden" id="change_share_id" value="">
<input type="hidden" id="share_value" value="<?php echo $share_value; ?>">
	</div>
</div>
<div class="modal fade" id="confirmIncrease"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
      <div class="modal-content">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h2 class="modal-title">เพิ่ม/ลดหุ้น</h2>
        </div>
        <div class="modal-body center">
		<form action="<?php echo base_url(PROJECTPATH.'/increase_share/save_increase_share'); ?>" method="POST" id="form_increase">
		<input type="hidden" name="share_value" value="<?php echo $share_value; ?>">
		<input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
		<input type="hidden" name="check_rule" id="check_rule" value="">
			<div class="form-group g24-col-sm-24" style="font-size:18px;">
				<?php 
					 $text_title_share = (@$row_member['type_share'] == '2')?'เพิ่ม/ลดส่งหุ้นเป็นปีละ':'เพิ่ม/ลดส่งหุ้นเป็นเดือนละ';
				?>
				<p class="g24-col-sm-10"><?php echo $text_title_share;?></p>
				<p class="g24-col-sm-4"><?php echo $share_per_month; ?></p>
				<p class="g24-col-sm-1">หุ้น</p>
				<p class="g24-col-sm-4">เป็นเงิน</p>
				<p class="g24-col-sm-4"><?php echo $share_per_month*$share_value; ?></p>
				<p class="g24-col-sm-1">บาท</p>
			</div>
			<div class="form-group g24-col-sm-24">
				<p class="g24-col-sm-10" style="font-size:18px;"><?php echo $text_title_share;?></p>
				<div class="g24-col-sm-4">
					<input class="form-control" name="change_value" id="change_value" onkeypress="return chkNumber(this)" onKeyUp="cal_share_result()" type="text">
				</div>
				<p class="g24-col-sm-1" style="font-size:18px;">หุ้น</p>
				<p class="g24-col-sm-4" style="font-size:18px;">เป็นเงิน</p>
				<div class="g24-col-sm-4">
					<input class="form-control" name="change_value_price" id="change_value_price" type="text" value="" readonly>
				</div>
				<p class="g24-col-sm-1" style="font-size:18px;">บาท</p>
			</div>
		</form>
        
		  <button class="btn btn-info" onclick="submit_form()">บันทึก</button>&nbsp;&nbsp;&nbsp;
          <button class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="confirmCancel"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
      <div class="modal-content">
        <div class="modal-header modal-header-alert">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h2 class="modal-title">ยืนยันยกเลิกการเพิ่ม/ลดหุ้น</h2>
        </div>
        <div class="modal-body center">
		  
          <p style="font-size:18px;">ท่านต้องการยกเลิกการเพิ่ม/ลดหุ้นใช่หรือไม่?</p>
        </div>
        <div class="modal-footer center">
		  <button class="btn btn-danger" onclick="cancel_change_share()" data-dismiss="modal">ยืนยัน</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="alert"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
      <div class="modal-content">
        <div class="modal-header modal-header-alert">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h2 class="modal-title">เกิดข้อผิดพลาด</h2>
        </div>
        <div class="modal-body center">
          <p style="font-size:18px;"><span id="alert_text"></span></p>
        </div>
        <div class="modal-footer center">
          <button type="button" class="btn btn-danger" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-info" id="bt_save_increase"  onclick="submit_form_increase()">ดำเนินการต่อ</button>
        </div>
      </div>
    </div>
</div>
<?php $this->load->view('search_member_new_modal'); ?>
<script>
	function get_id(id, value){
		$('#'+id).val(value);
	}
 function cal_share_result(){
	 var change_value = $('#change_value').val();
	 var share_value = $('#share_value').val();
	 if(change_value!=''){
		$('#change_value_price').val(parseFloat(change_value)*parseFloat(share_value));
	 }
 }
 function submit_form(){
	 var alert_text = '';
	 if($('#change_value').val()==''){
		 alert_text += '- กรุณากรอกจำนวนที่ต้องการเปลี่ยนแปลง<br>\n';
		$("#bt_save_increase").hide();
	 }else if($('#change_value').val() == '<?php echo $share_per_month; ?>'){
		 alert_text += '- จำนวนที่ท่านกรอกตรงกับข้อมูลเดิม<br>\n';
		 $("#bt_save_increase").hide();
	 }else{
		 $("#bt_save_increase").show();
		 var change_value_price = $('#change_value_price').val();
		 //console.log(change_value_price);
		  $.post(base_url+"increase_share/check_min_max_share", 
			{	
				member_id: '<?php echo $member_id; ?>',
				change_value_price: change_value_price
			}
			, function(result){
				//console.log(result);
				if(result == true){
					var change_value = $('#change_value').val();
					$.post(base_url+"increase_share/check_decrease_share", 
					{	
						member_id: '<?php echo $member_id; ?>',
						change_value: change_value
					}
					, function(result){
						//console.log(result);
						if(result!='pass'){
							console.log(result);
							alert_text += result+'<br>\n';
							 $('#alert_text').html(alert_text);
							 $("#alertModal").trigger("click");
						}else{
							$.post(base_url+"increase_share/check_share_rule", 
							{	
								member_id: '<?php echo $member_id; ?>',
								change_value_price: change_value_price
							}
							, function(data){
								//console.log(data);
								var chack = data.split('|');
								if(chack[0] == "true"){
									$("#form_increase").submit();
								}else{									
									var alert_text = 'เพิ่ม/ลดหุ้นต้องมากกว่าเกณฑ์การถือหุ้นแรกเข้า  '+chack[1]+' บาท';
									$('#alert_text').html(alert_text);
									$("#alertModal").trigger("click");
									return false;
								}
							});
						}
					});
				}else{
					alert_text += 'สมาชิกแบบสมทบ จะชำระครั้งเดียว 10,000 บาท ไม่เกิน 100,000 บาท <br>ไม่มีเรียกเก็บรายเดือน';
					$('#alert_text').html(alert_text);
					$("#alertModal").trigger("click");
				}				 
			});		 
	 }
	 if(alert_text != ''){
		 $('#alert_text').html(alert_text);
		 $("#alertModal").trigger("click");
	 }
	 
	 return false;
 }  
 
 function check_form(){
	 var alert_text = '';
	 if('<?php echo $member_id; ?>'==''){
		 alert_text += '- กรุณากรอกข้อมูลสมาชิก<br>\n';
	 }else{
		 $.post(base_url+"increase_share/check_increase_share", 
		{	
			member_id: '<?php echo $member_id; ?>'
		}
		, function(result){
			//console.log(result);
			if(result!='NOT FOUND'){
				alert_text += result+'<br>\n';
			}
			if(alert_text == ''){
				 $("#confirmIncreaseModal").trigger("click");
			 }else{
				 $('#alert_text').html(alert_text);
				 $("#alertModal").trigger("click");
			 }
		});
	 }
	 if(alert_text != ''){
		$('#alert_text').html(alert_text);
		 $("#alertModal").trigger("click");
	 }
 }
 
function chkNumber(ele){
	var vchar = String.fromCharCode(event.keyCode);
	if ((vchar<'0' || vchar>'9') && (vchar != '.')) return false;
	ele.onKeyPress=vchar;
}
function cancel_change_share(){
	var change_share_id = $('#change_share_id').val();
	 $.post(base_url+"increase_share/save_increase_share", 
	{	
		cancel_change_share: "1", 
		change_share_id: change_share_id
	}
	, function(result){
		$('#change_share_status_'+change_share_id).html('รออนุมัติยกเลิก');
		$('#cancel_'+change_share_id).hide();
    });
 }
$( document ).ready(function() {
	$('#confirmIncrease').on('shown.bs.modal', function() {
		$('#change_value').focus();
	});
});

function submit_form_increase(){
	$("#form_increase").submit(); 
}	 
</script>