    <form  action="" method="post" id="form1"  class="g24 form form-horizontal">
        <input name="gain_detail_id" type="hidden" value="<?php echo $gain_detail_id; ?>" >
        <input name="member_id" type="hidden" value="<?php echo $member_id; ?>" >

            <h2 class="text-left m-t-1 m-b-1">เพิ่มผู้รับผลประโยชน์</h2>

            <div class="m-t-1">

                 <div class="row">
          
                  <div class=" g24-col-sm-8 form-group">
                    <label class="g24-col-sm-7  control-label">คำนำหน้า</label>
                      <div class=" g24-col-sm-17 form-group">
                      <select name="g_prename_id" id="g_prename_id" class="form-control province">
                          <option value=""> - เลือกคำนำหน้า - </option>
                          <?php foreach($prename as $key => $value) { ?>
                              <option value="<?php echo $value["prename_id"]; ?>" <?php echo @$data['g_prename_id']==$value["prename_id"]?'selected':''; ?>><?php echo $value["prename_full"]; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                  </div>

                  <div class="g24-col-sm-8 form-group">
                    <label class="g24-col-sm-8 control-label ">ชื่อ</label>
                    <div class=" g24-col-sm-16">
                       <input id="g_firstname" name="g_firstname" class="form-control " type="text" value="<?php echo @$data['g_firstname']; ?>" >
                    </div>
                  </div>

                  <div class=" g24-col-sm-8 form-group">
                    <label class="g24-col-sm-7 control-label " >สกุล</label>
                    <div class=" g24-col-sm-17">
                    <input id="g_lastname" name="g_lastname" class="form-control " type="text" value="<?php echo @$data['g_lastname']; ?>" >
                  </div>
                  </div>

                  </div>


                  <div class="row">

                  <div class=" g24-col-sm-8 form-group">
                    <label class="g24-col-sm-7 control-label " >เกี่ยวข้องเป็น</label>
                    <div class="g24-col-sm-17 form-group">
                    <select name="g_relation_id" id="g_relation_id" class="form-control province" >
                        <option value=""> - เลือกความสัมพันธ์ - </option>
                        <?php foreach($relation as $key => $value) { ?>
                            <option value="<?php echo $value["relation_id"]; ?>" <?php echo @$data['g_relation_id']==$value["relation_id"]?'selected':''; ?>><?php echo $value["relation_name"]; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class=" g24-col-sm-8 form-group" >
                    <label class="g24-col-sm-8 control-label ">เลขบัตรประชาชน</label>
                    <div class=" g24-col-sm-16">
                    <input  id="g_id_card" name="g_id_card" class="form-control " type="text" onkeypress="return chkNumber(this)" value="<?php echo @$data['g_id_card'];?>" maxlength="13" >
                  </div>
                  </div>

                  <div class=" g24-col-sm-8 form-group" >
                    <label class="g24-col-sm-7 control-label ">ได้รับส่วนแบ่ง</label>
                    <div class=" g24-col-sm-17">
                    <input  id="g_share_rate" name="g_share_rate" type="number" step="0.01" class="form-control " value="<?php echo @$data['g_share_rate'];?>" placeholder="ส่วนแบ่งคิดเป็น % ">
                  </div>

                  </div>

                 </div>
              </div>


              <h3>ที่อยู่ปัจจุบัน</h3>

              <div class=" m-t-1">

                 <div class="row">
          
                  <div class=" g24-col-sm-8 form-group">
                    <label class="g24-col-sm-7  control-label">เลขที่</label>
                      <div class=" g24-col-sm-17 form-group">
                         <input id="g_address_no" name="g_address_no" class="form-control " type="text" value="<?php echo @$data['g_address_no']; ?>">
                      </div>
                  </div>

                  <div class="g24-col-sm-8 form-group">
                    <label class="g24-col-sm-8 control-label ">หมู่</label>
                    <div class=" g24-col-sm-16">
                       <input id="g_address_moo" name="g_address_moo" class="form-control " type="text" value="<?php echo @$data['g_address_moo']; ?>">
                    </div>
                  </div>

                  <div class=" g24-col-sm-8 form-group">
                    <label class="g24-col-sm-7 control-label ">หมู่บ้าน</label>
                      <div class=" g24-col-sm-17">
                        <input id="g_address_village" name="g_address_village" class="form-control " type="text" value="<?php echo @$data['g_address_village']; ?>" >
                    </div>
                  </div>

                  </div>

                  <div class="row">
          
                  <div class=" g24-col-sm-8 form-group">
                    <label class="g24-col-sm-7  control-label" >ซอย</label>
                      <div class=" g24-col-sm-17 form-group">
                      <input id="g_address_soi" name="g_address_soi" class="form-control " type="text" value="<?php echo @$data['g_address_soi']; ?>" >
                      </div>
                  </div>

                  <div class="g24-col-sm-8 form-group">
                    <label class="g24-col-sm-8 control-label ">ถนน</label>
                    <div class=" g24-col-sm-16">
                     <input id="g_address_road" name="g_address_road" class="form-control " type="text" value="<?php echo @$data['g_address_road']; ?>" >
                    </div>
                  </div>
              
                  <div class=" g24-col-sm-8 form-group">
                    <label class="g24-col-sm-7 control-label " >จังหวัด</label>
                      <div class=" g24-col-sm-17">
                        <select name="g_province_id" id="g_province_id" class="form-control province" onchange="change_province('g_province_id','amphur','g_amphur_id','district','g_district_id')">
                          <option value=""> - เลือกจังหวัด - </option>
                            <?php foreach($province as $key => $value) { ?>
                                <option value="<?php echo $value["province_id"]; ?>" <?php echo @$data['g_province_id']==$value["province_id"]?'selected':''; ?>><?php echo $value["province_name"]; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                  </div>

                  </div>

                  <div class="row">

                  <div class=" g24-col-sm-8 form-group">
                    <label class="g24-col-sm-7 control-label ">อำเภอ</label>
                    <div class="g24-col-sm-17 form-group" id="amphur">
                        <select name="g_amphur_id" id="g_amphur_id" class="form-control" onchange="change_amphur('g_amphur_id','district','g_district_id')">
                          <option value=""> - เลือกอำเภอ - </option>
                            <?php foreach($amphur as $key => $value){ ?>
                                <option value="<?php echo $value['amphur_id']; ?>"<?php echo $value['amphur_id']==@$data['g_amphur_id']?'selected':''; ?>><?php echo $value['amphur_name']; ?></option>
                            <?php }?>
                        </select>
                    </div>
                  </div>

                  <div class=" g24-col-sm-8 form-group" >
                    <label class="g24-col-sm-8 control-label ">ตำบล</label>
                    <div class=" g24-col-sm-16" id="district">
                      <select  name="g_district_id" id="g_district_id" class="form-control">
                        <option value=""> - เลือกตำบล - </option>
                          <?php foreach($district as $key => $value){ ?>
                              <option value="<?php echo $value['district_id']; ?>"<?php echo $value['district_id']==@$data['g_district_id']?'selected':''; ?>><?php echo $value['district_name']; ?></option>
                          <?php }?>
                      </select>
                  </div>
                  </div>

                  <div class=" g24-col-sm-8 form-group" >
                    <label class="g24-col-sm-7 control-label ">รหัสไปรษณีย์</label>
                    <div class=" g24-col-sm-17">
                    <input type="text" name="g_zipcode" id="g_zipcode" value="<?php echo @$data['g_zipcode']; ?>" class="form-control">
                    </div>
                  </div>

                 </div>


                 <div class="row">

                  <div class=" g24-col-sm-8 form-group">
                    <label class="g24-col-sm-7 control-label " for="form-control-2">เบอร์บ้าน</label>
                    <div class="g24-col-sm-17 form-group">
                       <input id="g_tel" name="g_tel" class="form-control " type="number" value="<?php echo @$data['g_tel']; ?>" >
                    </div>
                  </div>

                  <div class=" g24-col-sm-8 form-group" >
                    <label class="g24-col-sm-8 control-label " for="form-control-2">เบอร์ที่ทำงาน</label>
                    <div class=" g24-col-sm-16">
                       <input id="g_office_tel" name="g_office_tel" class="form-control" type="number" value="<?php echo @$data['g_office_tel']; ?>" >
                  </div>
                  </div>

                  <div class=" g24-col-sm-8 form-group" >
                    <label class="g24-col-sm-7 control-label " for="form-control-2">มือถือ</label>
                    <div class=" g24-col-sm-17">
                        <input id="g_mobile" name="g_mobile" class="form-control " type="number" value="<?php echo @$data['g_mobile']; ?>" maxlength="10">
                    </div>
                  </div>
                  
                 </div>

                 
                 <div class="row">

                  <div class=" g24-col-sm-8 form-group">
                    <label class="g24-col-sm-7 control-label ">E-mail</label>
                    <div class="g24-col-sm-17 form-group">
                         <input id="g_email" name="g_email" class="form-control " type="text" value="<?php echo @$data['g_email']; ?>" >
                    </div>
                  </div>
                  
                 </div>
              </div>
<div class="row m-t-1">
               <div class="form-group text-center g24-col-sm-24 m-t-3">
                  <button type="button" class="btn btn-primary min-width-100" onclick="check_form()" id="show-toast">ตกลง</button>
                  <a href="?member_id=<?php echo $member_id;?>" onclick="window.parent.parent.location.reload();"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
               </div>
</div>
              </form>
<script>
  function chkNumber(ele){
        var vchar = String.fromCharCode(event.keyCode);
        if ((vchar<'0' || vchar>'9') && (vchar != '.')) return false;
        ele.onKeyPress=vchar;
    }
</script>