<?php
function getAge($birthday) {
    $then = strtotime($birthday);
    return(floor((time()-$then)/31556926));
}

function getRetiredYears($birthday,$retire_age) {
    $arr_birthday = explode('-',@$birthday);				
	$birth_year = @$arr_birthday[0];
	$retired_years = ( @$retire_age +  @$birth_year)+543;
    return $retired_years;
}
?>
<style>
  .form-group{
    margin-bottom: 5px;
  }
	/*.modal-backdrop.in{
		opacity: 0;
	}
	.modal-backdrop {
		position: relative;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		z-index: 1040;
		background-color: #000;
	}
  */
  label {
    padding-top: 6px;
    text-align: right;
  }
  .modal-content {
		margin:auto;
		margin-top:7%;
	}
</style>
<div class="" style="padding-top:0;">
                <h3 >ข้อมูลสมาชิก</h3>

      			<div class="g24-col-sm-24" style="/*padding-right: 0px !important;margin-right: 0px !important;*/">
				  <div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label" for="form-control-2">รหัสสมาชิก</label>
                    <div class="g24-col-sm-14" >
						<div class="input-group">
							<input id="form-control-2"  class="form-control member_id" type="text" value="<?php echo @$row_member['member_id']; ?>" onkeypress="check_member_id();">
							<span class="input-group-btn">
								<a data-toggle="modal" data-target="#myModal" id="test" class="fancybox_share fancybox.iframe" href="#">
									<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
								</a>
							</span>	
						</div>
                    </div>
                  </div>

                  <!--<div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label " for="form-control-2">วันที่อนุมัติสมาชิก</label>
                     <div class="g24-col-sm-14">
                     <?php if (!empty($row_member['member_date'])) { ?>
                      <input id="form-control-2"  class="form-control " type="text" value="<?php echo $this->center_function->mydate2date(empty($row_member['member_date']) ? date("Y-m-d") : @$row_member['member_date']); ?>"  readonly>
                     <?php }else{ ?>
                      <input id="form-control-2"  class="form-control " type="text" value=""  readonly>
                     <?php } ?>
                    </div>
                  </div>-->
				
				  <div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label " for="form-control-2">เลขบัตรประชาชน</label>
                     <div class="g24-col-sm-14">
                      <input id="form-control-2"  class="form-control " type="text" value="<?php echo @$row_member['id_card']; ?>"  readonly>
                    </div>
                  </div>			  		
					
                  <div class="form-group g24-col-sm-8" style="/*padding-right: 0px !important;*/">
                    <label class="g24-col-sm-10 control-label " for="form-control-2">สถานะ</label>
                     <div class="g24-col-sm-14">
						 <?php if (@$row_member['mem_type'] == 1) { ?>
                      <input id="form-control-2"  class="form-control " type="text" value="ปกติ"  readonly>
                      <?php }else if (@$row_member['mem_type'] == 2) { 
                        $str_status = $this->db->get_where("coop_mem_req_resign", array("member_id" => $row_member['member_id'], "req_resign_status" => 1) )->result_array()[0]['approve_date'];
                        ?>
                      <input id="form-control-2" style="color: red" class="form-control " type="text" value="ลาออก <?=$this->center_function->mydate2date(@$str_status) ?>"  readonly>
                      <?php }else if (@$row_member['mem_type'] == 3){ ?>
                      <input id="form-control-2"  class="form-control " type="text" value="รออนุมัติ"  readonly>
					  <?php }else if (@$row_member['mem_type'] == 4){ ?>
                      <input id="form-control-2"  class="form-control " type="text" value="ประนอมหนี้"  readonly>
					  <?php }else if (@$row_member['mem_type'] == 5){ ?>
                      <input id="form-control-2"  class="form-control " type="text" value="โอนหุ้นตัดหนี้"  readonly>
                         <?php }else if (@$row_member['mem_type'] == 7){ ?>
                             <input id="form-control-2"  class="form-control " type="text" value="รอโอนย้าย"  readonly>
                         <?php }else if (@$row_member['mem_type'] == 8){ ?>
                         <input id="form-control-2"  class="form-control " type="text" value="รอส่งบำนาญ"  readonly>
                         <?php }else if (@$row_member['mem_type'] == 9){ ?>
                             <input id="form-control-2"  class="form-control " type="text" value="ไม่หักไปที่เงินเดือน"  readonly>
                         <?php }else { ?>
                      <input id="form-control-2"  class="form-control " type="text" value=""  readonly>
                      <?php } ?>
                    </div>
                 </div>
				 
				 <div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label " for="form-control-2">ชื่อ-สกุล</label>
                     <div class="g24-col-sm-14">
                      <input id="form-control-2"  class="form-control " type="text" value="<?php echo@$row_member['prename_short'].@$row_member['firstname_th'].' '.@$row_member['lastname_th'] ?>"  readonly>
                    </div>
                  </div>

                  

                  <div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label " for="form-control-2">ประเภทสมาชิก</label>
                     <div class="g24-col-sm-14">
                   		<?php if (@$row_member['apply_type_id'] == 2) { ?>
                      <input id="form-control-2"  class="form-control " type="text" value="สมทบ"  readonly>
                      	<?php }else{ ?>
                      <input id="form-control-2"  class="form-control " type="text" value="ปกติ"  readonly>
                      	<?php } ?>
                    </div>
                 </div>
				 <!--
                  <div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label " for="form-control-2">ประเภทสมาชิก</label>
                     <div class="g24-col-sm-14">
                   		<?php if (@$row_member['mem_type'] == 1) { ?>
                      <input id="form-control-2"  class="form-control " type="text" value="ปกติ"  readonly>
                      	<?php }elseif (@$row_member['mem_type'] == 2) { ?>
                      <input id="form-control-2"  class="form-control " type="text" value="สมทบ"  readonly>
                      	<?php }else{ ?>
                      <input id="form-control-2"  class="form-control " type="text" value=""  readonly>
                      	<?php } ?>
                    </div>
                 </div>
				 -->

                 <div class="form-group g24-col-sm-8" style="/*padding-right: 0px !important;margin-right: 0px !important;*/">
                    <label class="g24-col-sm-10 control-label " for="form-control-2">วันที่เป็นสมาชิก</label>
					 <?php $apply_yy_mm = "(".$this->center_function->cal_age(@$row_member['apply_date'])." ปี ".$this->center_function->cal_age(@$row_member['apply_date'],'m')." เดือน)";?>
                     <div class="g24-col-sm-14" style="/*padding-right: 0px !important;margin-right: 0px !important;*/">
                     <?php if (!empty($row_member['apply_date'])) { ?>
                      <input id="form-control-2"  class="form-control " type="text" value="<?php echo $this->center_function->mydate2date(empty($row_member['apply_date']) ? date("Y-m-d") : $row_member['apply_date']); ?>  <?php echo @$apply_yy_mm;?>"  readonly>
                     <?php }else{ ?>
                      <input id="form-control-2"  class="form-control " type="text" value=""  readonly>
                     <?php } ?>		 					  
                    </div>
                 </div>
				 
				 <div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label " for="form-control-2">ปีที่เกษียณ</label>
                     <div class="g24-col-sm-14">
					 
					 <?php if (!empty($row_member['birthday']) && !empty($retire_age)) { ?>
                      <input  id="retired_years"  class="form-control " type="text" value="<?php echo getRetiredYears($row_member['birthday'],$retire_age);  ?>"  readonly>
                     <?php }else{ ?>
                      <input  id="retired_years"  class="form-control " type="text" value=""  readonly>
                     <?php } ?>
                    </div>
                 </div>
				
				
				<div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label " for="form-control-2">วันเดือนปีเกิด</label>
                     <div class="g24-col-sm-14">
					 <?php $birthday_yy_mm = "(".$this->center_function->cal_age(@$row_member['birthday'])." ปี ".$this->center_function->cal_age(@$row_member['birthday'],'m')." เดือน)";?>                     
                     <?php if (!empty($row_member['birthday'])) { ?>
                      <input id="form-control-2"  class="form-control " type="text" value="<?php echo $this->center_function->mydate2date(empty($row_member['birthday']) ? date("Y-m-d") : $row_member['birthday']); ?> <?php echo @$birthday_yy_mm;?>"  readonly>
                     <?php }else{ ?>
                      <input id="form-control-2"  class="form-control " type="text" value=""  readonly>
                     <?php } ?>
                    </div>
                </div>				
				
				<div class="form-group g24-col-sm-8">
					<label class="g24-col-sm-10 control-label" for="form-control-2">ประเภท</label>
					<div class="g24-col-sm-14" >
						<input id="mem_type_id"  class="form-control " type="text" value="<?php echo @$mem_type_list[@$row_member['mem_type_id']]; ?>"  readonly>
					</div>
				</div>
				
				<div class="form-group g24-col-sm-8">
					<label class="g24-col-sm-10 control-label" for="form-control-2">ตำแหน่ง</label>
					<div class="g24-col-sm-14" >
						<input id="position_name"  class="form-control " type="text" value="<?php echo @$row_member['position']; ?>"  readonly>
					</div>
				</div>
				
				<div class="form-group g24-col-sm-16" style="/*padding-right: 0px !important;margin-right: 0px !important;*/">
                    <label class="g24-col-sm-5 control-label " for="form-control-2">สังกัด</label>
                     <div class="g24-col-sm-19" style="/*padding-right: 0px !important;margin-right: 0px !important;*/">
                      <input id="form-control-2"  class="form-control " type="text" value="<?php echo @$row_member['mem_group_name']; ?>" style="margin-left: -3px;width: 100.4%;"  readonly>
                    </div>
                 </div> 
               
                 <!--<div class="form-group g24-col-sm-8">
                    <label class="g24-col-sm-10 control-label " for="form-control-2">อายุ</label>
                     <div class="g24-col-sm-14">
                     <?php if (!empty($row_member['birthday'])) { ?>
                      <input  id="age_member"  class="form-control " type="text" value="<?php echo getAge($row_member['birthday']);  ?>"  readonly>
                     <?php }else{ ?>
                      <input  id="age_member"  class="form-control " type="text" value=""  readonly>
                     <?php } ?>
                    </div>
                 </div>		
                 <div class="form-group g24-col-sm-8" style="/*padding-right: 0px !important;margin-right: 0px !important;*/">
                    <label class="g24-col-sm-10 control-label " for="form-control-2">สังกัด</label>
                     <div class="g24-col-sm-14" style="/*padding-right: 0px !important;margin-right: 0px !important;*/">
                      <input id="form-control-2"  class="form-control " type="text" value="<?php echo @$row_member['mem_group_name']; ?>"  readonly>
                    </div>
                 </div> 
				 -->
      			</div>

</div>
<script>
  function check_member_id() {
   var member_id = $('.member_id').first().val();
   var keycode = (event.keyCode ? event.keyCode : event.which);
   if(keycode == '13'){
     $.post(base_url+"save_money/check_member_id", 
     {	
       member_id: member_id
     }
     , function(result){
        obj = JSON.parse(result);
        console.log(obj.member_id);
        mem_id = obj.member_id;
        if(mem_id != undefined){
          document.location.href = '<?php echo base_url(uri_string())?>?member_id='+mem_id
        }else{					
          swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning'); 
        }
      });		
    }
  }
</script>
