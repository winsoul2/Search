<div class="layout-content">
    <div class="layout-content-body">
<style>
    .form-group { margin-bottom: 0; }
    .border1 { border: solid 1px #ccc; padding: 0 15px; }
    .mem_pic { float: right; width: 150px; }
    .mem_pic img { width: 100%; border: solid 1px #ccc; }
    .mem_pic button { display: block; width: 100%; }

    .hide_error{color : inherit;border-color : inherit;}

    .has-error{color : #d50000;border-color : #d50000;}

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .alert-danger {
        background-color: #F2DEDE;
        border-color: #e0b1b8;
        color: #B94A48;
    }
    .modal-backdrop.in{
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
	.modal-dialog-idcard{
		width : 80%;
	}
	th{
		text-align: center;
	}
	.btn_idcard{
		font-weight:lighter;
		font-size:16px;
		padding: 3px 12px;
	}
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
		vertical-align: middle;
		padding: 4px;
	}
</style>
<!-- <link rel="stylesheet" href="<?=base_url('assets/css/select2.min.css')?>"> -->
<?php
    function birthday($bithdayDate) {
        $date = new DateTime($bithdayDate);
        $now  = new DateTime();
        $interval = $now->diff($date);
        return $interval->y;
    }
?>
<h1 style="margin-bottom: 0">ข้อมูลสมาชิก</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>

    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <a class="btn btn-primary btn-lg bt-add" href="<?php echo base_url(PROJECTPATH.'/manage_member_share/add'); ?>">
            <span class="icon icon-plus-circle"></span>
            เพิ่มคำร้อง
        </a>
    </div>

</div>
<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12">
        <div class="panel panel-body">
            <form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/manage_member_share/save_add'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="myForm">
                <!--input type="hidden" name="mem_apply_id" value="<?php echo (@$_GET['action']=='use_prev_data')?'':@$data['mem_apply_id']; ?>"/-->
				<input type="hidden" name="copy_id" value="<?php echo (@$_GET['action']=='use_prev_data')?@$_GET['id']:''; ?>"/>
                <div class="m-t-1">

                    <div class="g24-col-sm-20">
						<div class="form-group">
                            <label class="g24-col-sm-3 control-label">เลขที่คำร้อง</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="mem_apply_id" name="mem_apply_id" class="form-control" style="text-align:left;" type="text" value="<?php echo (@$_GET['action']=='use_prev_data')?'':@$data['mem_apply_id']; ?>" readonly="readonly"/>
                                </div>
                            </div>

                            <!--div class="g24-col-sm-1">
                                <a data-toggle="modal" data-target="#myModal" id="test" class="fancybox_share fancybox.iframe" href="#">
                                    <button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span>
                                    </button>
                                </a>
                            </div-->

                            <label class="g24-col-sm-3 control-label datepicker1" for="apply_date">วันที่ยื่นคำร้อง</label>
                            <div class="input-with-icon g24-col-sm-9">
                                <div class="form-group">
                                    <input id="apply_date" name="apply_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo (@$_GET['action']=='use_prev_data')?$this->center_function->mydate2date(date("Y-m-d")):$this->center_function->mydate2date(empty($data) ? date("Y-m-d") : @$data['apply_date']); ?>" data-date-language="th-th"  title="กรุณาป้อน วันที่สมัคร">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="g24-col-sm-3 control-label">รหัสสมาชิก <span id="naja"></span> </label>
                            <div class="g24-col-sm-9">
								<div class="form-group">
									<div class="input-group">											
										<input id="member_id" name="member_id" class="form-control" style="text-align:left;" type="text" value="<?php echo (@$_GET['action']=='use_prev_data')?'':@$data['member_id']; ?>" title="กรุณาป้อน เลขสมาชิก" onkeypress="check_member_id();" />
										<span class="input-group-btn">
											<a data-toggle="modal" data-target="#myModal" id="test" class="fancybox_share fancybox.iframe" href="#">
												<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
											</a>
										</span>	
									</div>
								</div>
								<!--		
                                <div class="form-group">
                                    <input id="member_id" name="member_id" class="form-control" style="text-align:left;" type="number" value="<?php echo (@$_GET['action']=='use_prev_data')?'':@$data['member_id']; ?>" readonly="readonly"  title="กรุณาป้อน เลขสมาชิก" />
                                </div>
								-->
                            </div>
                            <label class="g24-col-sm-3 control-label datepicker1" for="apply_date">วันที่อนุมัติ</label>
                            <div class="input-with-icon g24-col-sm-9">
                                <div class="form-group">
                                    <input id="member_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo (@$_GET['action']=='use_prev_data')?'':$this->center_function->mydate2date(@$data['member_date']); ?>" readonly title="กรุณาป้อน วันที่สมัคร">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
						<div class="form-group">
							<label class="g24-col-sm-3 control-label" for="employee_id">รหัสพนักงาน</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="employee_id" name="employee_id" class="form-control m-b-1" type="text" value="<?php echo @$data['employee_id']; ?>">
                                </div>
                            </div>
							
                            <label class="g24-col-sm-3 control-label" for="id_card" style="white-space: nowrap;"> เลขบัตรประชาชน </label>
                            <div class="g24-col-sm-9  m-b-1">
								<div class="form-group">
									<div class="input-group">
										<input id="id_card" name="id_card" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>"  title="กรุณาป้อน เลขบัตรประชาชน" onkeypress="return chkNumber(this)" maxlength='13' onchange="search_idcard('change')">
										<input id="old_id_card"type="hidden" value="<?php echo @$data['id_card']; ?>">
										<span class="input-group-btn">
											<button type="button" onclick="search_idcard('click')" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
										</span>	
									</div>
								</div>
						
                                <!--<div class="form-group">
                                    <input id="id_card" name="id_card" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>"  title="กรุณาป้อน เลขบัตรประชาชน" onkeypress="return chkNumber(this)" maxlength='13'>
                                    <input id="old_id_card"type="hidden" value="<?php echo @$data['id_card']; ?>">
                                </div>-->
                            </div>

                           <!--<div class="g24-col-sm-1">
								<button type="button" onclick="search_idcard()" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                            </div>-->
                        </div>
                        <div class="form-group">
                            <label class="g24-col-sm-3 control-label" for="apply_type_id">ประเภทสมัคร</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <select id="apply_type_id" name="apply_type_id" class="form-control m-b-1" onchange="check_age_limit()" required title="กรุณาเลือก ประเภทสมัคร">
                                        <?php foreach($mem_apply_type as $key => $value) { ?>
                                            <option value="<?php echo $value["apply_type_id"]; ?>"<?php if($value["apply_type_id"] == @$data["apply_type_id"]) { ?> selected="selected"<?php } ?> age_limit="<?php echo $value["age_limit"]; ?>"><?php echo $value["apply_type_name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <label class="g24-col-sm-3 control-label" for="mem_type">สถานะ</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <?php 
									if(@$_GET['action']=='use_prev_data'){
										echo @$member_status['3'];
									}else{
                                        $readonly_type_status = "";

                                        echo '<select id="mem_type" name="mem_type" class="form-control m-b-1"  title="กรุณาเลือก สถานะสมาชิก" '.$readonly_type_status.'>';
                                        foreach ($mem_type_status as $key => $value) {
                                            $selected_mem_type = "";
                                            if($data["mem_type"]==$key || ($this->uri->segment(3)=="" && $key==3))
                                                $selected_mem_type = "selected";
                                            else if(($this->uri->segment(3)==""))
                                                $selected_mem_type = "disabled";
                                            echo '<option value="'.$key.'" '.$selected_mem_type.'>'.$value.'</option>';
                                        }
                                        echo "</select>"; 
									}
									?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <!--<label class="g24-col-sm-3 control-label">รหัสพนักงาน </label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input type="text" class="form-control m-b-1" name="employee_id" id="employee_id" value="<?php echo @$data['employee_id']?>">
                                </div>
                            </div>-->
                            <label class="g24-col-sm-3 control-label" for="mem_type_id">ประเภทสมาชิก</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <select id="mem_type_id" name="mem_type_id" class="form-control m-b-1" required title="กรุณาเลือก ประเภทสมาชิก" onchange="check_salary_and_share()">
										<option value="">เลือกประเภทสมาชิก</option>
										<?php foreach($mem_type as $key => $value){ ?>
										<option value="<?php echo $value['mem_type_id']?>" <?php echo $value['mem_type_id'] == @$data['mem_type_id']?'selected':''; ?>><?php echo $value['mem_type_name']?></option>
										<?php } ?>
                                    </select>
                                </div>
                            </div>
							<label class="g24-col-sm-3 control-label" for="">พินัยกรรม</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
									<label class="control-label"><?php echo @$testament;?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="g24-col-sm-3 control-label" for="prename_id">คำนำหน้า</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <!--<select id="prename_id" name="prename_id" class="form-control m-b-1" required title="กรุณาเลือก คำนำหน้า">-->
                                    <select id="prename_id" name="prename_id" class="form-control m-b-1" title="กรุณาเลือก คำนำหน้า">
											<option value="">เลือกคำนำหน้า</option>
                                        <?php foreach($prename as $key => $value) { ?>
                                            <option value="<?php echo $value["prename_id"]; ?>"<?php if($value["prename_id"] == @$data["prename_id"]) { ?> selected="selected"<?php } ?>><?php echo $value["prename_full"]; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="sex">เพศ</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <!--<select id="sex" name="sex" class="form-control m-b-1" required title="กรุณาเลือก เพศ">-->
                                    <select id="sex" name="sex" class="form-control m-b-1" title="กรุณาเลือก เพศ">
										<option value="">เลือกเพศ</option>
                                        <option value="M"<?php if(@$data["sex"] == "M") { ?> selected="selected"<?php } ?>>ชาย</option>
                                        <option value="F"<?php if(@$data["sex"] == "F") { ?> selected="selected"<?php } ?>>หญิง</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">

                            <label class="g24-col-sm-3 control-label" for="firstname_th">ชื่อ (ภาษาไทย)</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="firstname_th" name="firstname_th" class="form-control m-b-1" type="text" value="<?php echo @$data['firstname_th']; ?>" required title="กรุณาป้อน ชื่อ (ภาษาไทย)">
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="lastname_th">สกุล (ภาษาไทย)</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="lastname_th" name="lastname_th" class="form-control m-b-1" type="text" value="<?php echo @$data['lastname_th']; ?>" title="กรุณาป้อน สกุล (ภาษาไทย)">
                                    <!--<input id="lastname_th" name="lastname_th" class="form-control m-b-1" type="text" value="<?php echo @$data['lastname_th']; ?>" required title="กรุณาป้อน สกุล (ภาษาไทย)">-->
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="g24-col-sm-3 control-label" for="firstname_en">ชื่อ (English)</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="firstname_en" name="firstname_en" class="form-control m-b-1" type="text" value="<?php echo @$data['firstname_en']; ?>">
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="lastname_en">สกุล (English)</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="lastname_en" name="lastname_en" class="form-control m-b-1" type="text" value="<?php echo @$data['lastname_en']; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="g24-col-sm-3 control-label" for="email">E-mail</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="email" name="email" class="form-control m-b-1" type="text" value="<?php echo @$data['email']; ?>">
                                </div>
                            </div>



                            <label class="g24-col-sm-3 g24-col-xs-12 control-label" for="tel">เบอร์บ้าน</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="tel" name="tel" class="form-control m-b-1" type="text" value="<?php echo @$data['tel']; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="g24-col-sm-3 control-label" for="office_tel">เบอร์ที่ทำงาน</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="office_tel" name="office_tel" class="form-control m-b-1" type="text" value="<?php echo @$data['office_tel']; ?>">
                                </div>
                            </div>


                            <label class="g24-col-sm-3 control-label" for="mobile">เบอร์มือถือ</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="mobile" name="mobile" class="form-control m-b-1" type="number" value="<?php echo @$data['mobile']; ?>" title="กรุณาป้อน เบอร์มือถือ"  maxlength="10">
                                    <!--<input id="mobile" name="mobile" class="form-control m-b-1" type="number" value="<?php echo @$data['mobile']; ?>" required title="กรุณาป้อน เบอร์มือถือ"  maxlength="10">-->
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="g24-col-sm-4">

                        <div class="g24-col-sm-24 m-b-1 text-right">
                            <div class="mem_pic" style="margin-bottom:20px;display: block;margin: 0 auto;">
								<?php $member_pic = empty($data['member_pic']) ? "default.png" : $data['member_pic'];?>
                                <img id="member_pic" src="<?php echo base_url(PROJECTPATH.'/assets/uploads/members/'.$member_pic); ?>" alt="" />
								<button type="button" id="btn_member_pic" class="btn btn-info">รูปภาพสมาชิก</button>
                            </div>
                        </div>
						<input type="hidden" name="copy_member_pic" value="<?php echo @$data['member_pic']; ?>">
                    </div>
                    <br />
                    <div class="row">
                        <div class="g24-col-sm-20">

                            <h3 class="m-t-1">ที่อยู่ตามทะเบียนบ้าน</h3><br>

                            <label class="g24-col-sm-3 control-label" for="address_no">เลขที่</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="address_no" name="address_no" class="form-control m-b-1" type="text" value="<?php echo @$data['address_no']; ?>"  title="กรุณาป้อน เลขที่อยู่ตามทะเบียนบ้าน">
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="address_moo" >หมู่</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="address_moo" name="address_moo" class="form-control m-b-1" type="text" value="<?php echo @$data['address_moo']; ?>">
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="address_village">หมู่บ้าน</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="address_village" name="address_village" class="form-control m-b-1" type="text" value="<?php echo @$data['address_village']; ?>">
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="address_soi">ซอย</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="address_soi" name="address_soi" class="form-control m-b-1" type="text" value="<?php echo @$data['address_soi']; ?>">
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="address_road">ถนน</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="address_road" name="address_road" class="form-control m-b-1" type="text" value="<?php echo @$data['address_road']; ?>">
                                </div>
                            </div>


                            <label class="g24-col-sm-3 control-label" for="province_id">จังหวัด</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <select name="province_id" id="province_id" class="form-control m-b-1" onchange="change_province('province_id','amphure','amphur_id','district','district_id')">
                                        <option value="">เลือกจังหวัด</option>
                                        <?php foreach($province as $key => $value){ ?>
                                                <option value="<?php echo $value['province_id']; ?>"<?php echo $value['province_id']==@$data['province_id']?'selected':''; ?>><?php echo $value['province_name']; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <label class="g24-col-sm-3 control-label" for="amphur_id">อำเภอ</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <span id="amphure">
                                         <select name="amphur_id" id="amphur_id" class="form-control m-b-1" onchange="change_amphur('amphur_id','district','district_id')">
                                             <option value="">เลือกอำเภอ</option>
                                             <?php foreach($amphur as $key => $value){ ?>
                                                 <option value="<?php echo $value['amphur_id']; ?>"<?php echo $value['amphur_id']==@$data['amphur_id']?'selected':''; ?>><?php echo $value['amphur_name']; ?></option>
                                             <?php }?>
                                         </select>
                                    </span>
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="district_id">ตำบล</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <span id="district">
                                        <select name="district_id" id="district_id" class="form-control m-b-1">
                                            <option value="">เลือกตำบล</option>
                                            <?php foreach($district as $key => $value){ ?>
                                                <option value="<?php echo $value['district_id']; ?>"<?php echo $value['district_id']==@$data['district_id']?'selected':''; ?>><?php echo $value['district_name']; ?></option>
                                            <?php }?>
                                        </select>
                                    </span>
                                </div>
                            </div>
                            <label class="g24-col-sm-3 control-label" for="zipcode">รหัสไปรษณีย์</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="zipcode" name="zipcode" class="form-control m-b-1" type="text" value="<?php echo @$data['zipcode']; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <br />
                    <div class="row" >
                        <div class="g24-col-sm-20">
                            <div class="row" style="margin-top:1.5em;">
                                <div class="g24-col-sm-3">
                                    <h3 style="margin-top:0;">ที่อยู่ปัจจุบัน</h3>
                                </div>
                                <div class="g24-col-sm-10" >
                                    <div class="checkbox" style="margin-top:0px;margin-top:0px;padding-top:3px;">
                                        <label>
                                            <input type="checkbox" id="is_c_address"  value="1" onclick="dupp_address(this)" /> ใช้ที่อยู่ตามทะเบียนบ้าน
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <br>

                            <label class="g24-col-sm-3 control-label" for="c_address_no" id="c_address_no_label">เลขที่</label>
                            <div class="g24-col-sm-9" id="address_no_con">
                                <div class="form-group">
                                    <input id="c_address_no" name="c_address_no" class="form-control m-b-1" type="text" value="<?php echo @$data['c_address_no']; ?>"  title="กรุณาป้อน เลขที่อยู่ปัจจุบัน">
                                </div>
                            </div>
                            <label class="g24-col-sm-3 control-label" for="c_address_moo">หมู่</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="c_address_moo" name="c_address_moo" class="form-control m-b-1" type="text" value="<?php echo @$data['c_address_moo']; ?>">
                                </div>
                            </div>
                            <label class="g24-col-sm-3 control-label" for="c_address_village">หมู่บ้าน</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="c_address_village" name="c_address_village" class="form-control m-b-1" type="text" value="<?php echo @$data['c_address_village']; ?>">
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="c_address_soi">ซอย</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="c_address_soi" name="c_address_soi" class="form-control m-b-1" type="text" value="<?php echo @$data['c_address_soi']; ?>">
                                </div>
                            </div>
                            <label class="g24-col-sm-3 control-label" for="c_address_road">ถนน</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="c_address_road" name="c_address_road" class="form-control m-b-1" type="text" value="<?php echo @$data['c_address_road']; ?>">
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="c_province_id" id="c_province_id_label">จังหวัด</label>
                            <div class="g24-col-sm-9" id="province_con">
                                <div class="form-group">
                                    <select name="c_province_id" id="c_province_id" class="form-control m-b-1" onchange="change_province('c_province_id','c_amphure','c_amphur_id','c_district','c_district_id')">
                                        <option value="">เลือกจังหวัด</option>
                                        <?php foreach($province as $key => $value){ ?>
                                            <option value="<?php echo $value['province_id']; ?>"<?php echo $value['province_id']==@$data['c_province_id']?'selected':''; ?>><?php echo $value['province_name']; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="c_amphur_id" id="c_amphur_id_label">อำเภอ</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group" id="amphure_con">
                                    <span id="c_amphure">
                                        <select name="c_amphur_id" id="c_amphur_id" class="form-control m-b-1" onchange="change_amphur('c_amphur_id','c_district','c_district_id')">
                                            <option value="">เลือกอำเภอ</option>
                                            <?php foreach($c_amphur as $key => $value){ ?>
                                                <option value="<?php echo $value['amphur_id']; ?>"<?php echo $value['amphur_id']==@$data['c_amphur_id']?'selected':''; ?>><?php echo $value['amphur_name']; ?></option>
                                            <?php }?>
                                        </select>
                                    </span>
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="c_district_id" id="c_district_id_label">ตำบล</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group"  id="district_con">
                                    <span id="c_district">
                                        <select name="c_district_id" id="c_district_id" class="form-control m-b-1">
                                            <option value="">เลือกตำบล</option>
                                            <?php foreach($c_district as $key => $value){ ?>
                                                <option value="<?php echo $value['district_id']; ?>"<?php echo $value['district_id']==@$data['c_district_id']?'selected':''; ?>><?php echo $value['district_name']; ?></option>
                                            <?php }?>
                                        </select>
                                    </span>
                                </div>
                            </div>
                            <label class="g24-col-sm-3 control-label" for="c_zipcode">รหัสไปรษณีย์</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="c_zipcode" name="c_zipcode" class="form-control m-b-1" type="text" value="<?php echo @$data['c_zipcode']; ?>">
                                </div>
                            </div>

                        </div>

                        <div class="g24-col-sm-12">

                        </div>
                    </div>

                    <br />

                    <div class="row">
                        <div class="g24-col-sm-20">
                            <h3 style="margin-top:0;">ข้อมูลส่วนตัว</h3><br>

                                <label class="g24-col-sm-3 control-label" for="marry_status">สถานะสมรส</label>
                                <div class="g24-col-sm-9">
                                    <div class="form-group">
                                        <select id="marry_status" name="marry_status" class="form-control m-b-1" required title="กรุณาเลือก สถานะสมรส">
                                            <option value="1"<?php if(@$data["marry_status"] == 1) { ?> selected="selected"<?php } ?>>โสด</option>
                                            <option value="2"<?php if(@$data["marry_status"] == 2) { ?> selected="selected"<?php } ?>>สมรส</option>
                                            <option value="3"<?php if(@$data["marry_status"] == 3) { ?> selected="selected"<?php } ?>>หย่า</option>
                                            <option value="4"<?php if(@$data["marry_status"] == 4) { ?> selected="selected"<?php } ?>>หม้าย</option>
                                        </select>
                                    </div>
                                </div>
                            <label class="g24-col-sm-3 control-label" for="nationality">สัญชาติ</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="nationality" name="nationality" class="form-control m-b-1" type="text" value="<?php echo @$data['nationality']; ?>"  title="กรุณาป้อน สัญชาติ">
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="birthday"> วันเกิด </label>
                            <div class="input-with-icon g24-col-sm-4" id="birthday_con">
                                <div class="form-group">
                                    <input id="birthday" name="birthday" class="form-control m-b-1" data-mask="00/00/0000" style="padding-left: 40px;" type="text" value="<?php echo $this->center_function->mydate2date(@$data['birthday']); ?>" data-date-language="th-th" title="กรุณาเลือก วันเกิด" maxlength="10">
                                    <!--<input id="birthday" name="birthday" class="form-control m-b-1" data-mask="00/00/0000" style="padding-left: 40px;" type="text" value="<?php echo $this->center_function->mydate2date(@$data['birthday']); ?>" data-date-language="th-th" required title="กรุณาเลือก วันเกิด" maxlength="10">-->
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>


                            <div id="calendar-2"></div>
                            <div id="result-2"></div>

                            <label class="g24-col-sm-2 control-label">อายุ</label>
                            <div class="g24-col-sm-3">
                                <div class="form-group" id="birthday_border">
                                    <input id="age" class="form-control m-b-1" type="text" value="<?php echo birthday(@$data['birthday']) == 0 ? NULL : birthday(@$data['birthday'])?>" readonly="readonly"  title="&nbsp;">
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="father_name">ชื่อบิดา</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="father_name" name="father_name" class="form-control m-b-1" type="text" value="<?php echo @$data['father_name']; ?>"  title="กรุณาป้อน ชื่อบิดา">
                                </div>
                            </div>

                            <label class="g24-col-sm-3 control-label" for="mother_name">ชื่อมารดา</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="mother_name" name="mother_name" class="form-control m-b-1" type="text" value="<?php echo @$data['mother_name']; ?>"  title="กรุณาป้อน ชื่อมารดา">
                                </div>
                            </div>


                            <div class="row">
                                <div class="g24-col-sm-20">

                                    <h3>ข้อมูลที่ทำงาน</h3><br>
                                </div>
                                <div class="g24-col-sm-24">
                                    <label class="g24-col-sm-3 control-label" for="position">ตำแหน่ง</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
											<!--<select id="position_id" name="position_id" class="form-control m-b-1" required title="กรุณาเลือก ตำแหน่ง" onchange="">-->
											<select id="position_id" name="position_id" class="form-control m-b-1" title="กรุณาเลือก ตำแหน่ง" onchange="">
												<option value="">เลือกตำแหน่ง</option>
												<?php foreach($mem_position as $key => $value){ ?>
												<option value="<?php echo $value['position_id']?>" <?php echo $value['position_id'] == @$data['position_id']?'selected':''; ?>><?php echo $value['position_name']?></option>
												<?php } ?>
											</select>
                                            <!--<input id="position" name="position" class="form-control m-b-1" type="hidden" value="<?php echo @$data['position']; ?>"  title="กรุณาป้อน ตำแหน่ง">-->
                                        </div>
                                    </div>

                                    <label class="g24-col-sm-3 control-label" for="department">หน่วยงานหลัก</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <select class="form-control m-b-1" name="department" id="department" required title="กรุณาเลือกหน่วยงานหลัก" onchange="change_mem_group('department', 'faction')">
                                                <option value="">เลือกข้อมูล</option>
                                                <?php
                                                foreach($department as $key => $value){ ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo @$data['department']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <label class="g24-col-sm-3 control-label" for="faction">ฝ่าย</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group" id="faction_space">
                                            <select class="form-control m-b-1" name="faction" id="faction" required title="กรุณาเลือกอำเภอ" onchange="change_mem_group('faction','level')">
                                                <option value="">เลือกข้อมูล</option>
                                                <?php foreach($faction as $key => $value){ ?>
                                                        <option value="<?php echo $value['id']; ?>" <?php echo @$data['faction']==$value['id']?'selected':'';?>><?php echo $value['mem_group_id']." - ".$value['mem_group_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <label class="g24-col-sm-3 control-label" for="level">ที่อยู่จัดส่งเอกสาร</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group" id="level_space">
                                            <select class="form-control m-b-1" name="level" id="level" required title="กรุณาเลือกที่อยู่จัดส่งเอกสาร">
                                                <option value="">เลือกข้อมูล</option>
                                                <?php foreach($level as $key => $value){ ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo @$data['level']==$value['id']?'selected':'';?>><?php echo $value['mem_group_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <label class="g24-col-sm-3 control-label" for="work_district">แขวง</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="work_district" name="work_district" class="form-control m-b-1" type="text" value="<?php echo @$data['work_district']; ?>">
                                        </div>
                                    </div>
									<!--
                                    <label class="g24-col-sm-3 control-label" for="salary_type">ประเภท</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <select id="salary_type" name="salary_type" class="form-control m-b-1"  title="กรุณาเลือก ประเภทรายวันรายเดือน">
                                                <option value="1"<?php if(@$data["salary_type"] == 1) { ?> selected="selected"<?php } ?>>รายวัน</option>
                                                <option value="2"<?php if(@$data["salary_type"] == 2) { ?> selected="selected"<?php } ?>>รายเดือน</option>
                                            </select>
                                        </div>
                                    </div>
									-->

                                    <!--<label class="g24-col-sm-3 control-label" for="work_id_card">เลขพนักงาน</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="work_id_card" name="work_id_card" class="form-control m-b-1" type="text" value="<?php echo @$data['work_id_card']; ?>"  title="กรุณาป้อน เลขพนักงาน">
                                        </div>
                                    </div>-->

                                    <label class="g24-col-sm-3 control-label" for="work_date">วันบรรจุ</label>
                                    <div class="input-with-icon g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="work_date" name="work_date" class="form-control m-b-1" type="text" style="padding-left: 45px;" value="<?php echo (@$data['work_date'] == '0000-00-00' || empty($data['work_date']))?'':$this->center_function->mydate2date(@$data['work_date']); ?>" data-provide="datepicker" data-date-today-highlight="true" data-date-format="dd/mm/yyyy" data-date-language="th-th">
                                            <span class="icon icon-calendar input-icon m-f-1"></span>
                                        </div>
                                    </div>
                                    <label class="g24-col-sm-3 control-label" for="retry_date">เกษียณ</label>
                                    <div class="input-with-icon g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="retry_date" name="retry_date" class="form-control m-b-1" type="text"  style="padding-left: 45px;" value="<?php echo (@$data['retry_date'] == '0000-00-00' || empty($data['retry_date']))?'':$this->center_function->mydate2date(@$data['retry_date']); ?>" data-provide="datepicker" data-date-today-highlight="true" data-date-format="dd/mm/yyyy" data-date-language="th-th">
                                            <span class="icon icon-calendar input-icon m-f-1"></span>
                                        </div>
                                    </div>

                                    <span style="<?php echo $salary_display; ?>">
							<label class="g24-col-sm-3 control-label" for="salary">เงินเดือน</label>
							<div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="salary" name="salary" class="form-control m-b-1" type="number" value="<?php echo @$data['salary']; ?>" title="กรุณาป้อน เงินเดือน" onchange="check_salary_and_share()">
                                </div>
                            </div>
							<label class="g24-col-sm-3 control-label" for="other_income">เงินอื่นๆ</label>
							<div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="other_income" name="other_income" class="form-control m-b-1" type="number" value="<?php echo @$data['other_income']; ?>" title="กรุณาป้อน เงินอื่นๆ" onchange="check_salary_and_share()">
                                </div>
                            </div>
						</span>

                                    <label class="g24-col-sm-3 control-label" for="salary">ลายเซ็นต์</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
											<label class="fileContainer btn btn-info">
												<span class="icon icon-paperclip"></span> 
												เลือกไฟล์
												<input id="signature" name="signature" class="form-control m-b-1" type="file" value="" style="height: auto;">
											</label>
                                            <?php if(!empty($data['signature'])) { ?>
                                                <div>
                                                    <img src="<?php echo base_url(PROJECTPATH."/assets/uploads/members/".@$data['signature']); ?>" alt="" style="width: 120px;" />
                                                </div>
                                            <?php } ?>
											<input type="hidden" name="copy_signature" value="<?php echo @$data['signature']; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="g24-col-sm-24">
                                    <h3 class="m-t-1">ข้อมูลคู่สมรส</h3><br>

                                    <label class="g24-col-sm-3 control-label" for="marry_name">ชื่อคู่สมรส</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="marry_name" name="marry_name" class="form-control m-b-1" type="text" value="<?php echo @$data['marry_name']; ?>">
                                        </div>
                                    </div>

                                    <label class="g24-col-sm-3 control-label" style="white-space: nowrap;" for="m_id_card">เลขบัตรประชาชน</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="m_id_card" name="m_id_card" class="form-control m-b-1" type="text" value="<?php echo @$data['m_id_card']; ?>" onkeypress="return chkNumber(this)" maxlength='13'>
                                        </div>
                                    </div>
									<!--
                                    <label class="g24-col-sm-3 control-label" for="m_work_id_card">รหัสพนักงาน</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="m_work_id_card" name="m_work_id_card" class="form-control m-b-1" type="text" value="<?php echo @$data['m_work_id_card']; ?>">
                                        </div>
                                    </div>
									-->

                                    <label class="g24-col-sm-3 control-label" for="m_address_no">เลขที่</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="m_address_no" name="m_address_no" class="form-control m-b-1" type="text" value="<?php echo @$data['m_address_no']; ?>">
                                        </div>
                                    </div>
                                    <label class="g24-col-sm-3 control-label" for="m_address_moo">หมู่</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="m_address_moo" name="m_address_moo" class="form-control m-b-1" type="text" value="<?php echo @$data['m_address_moo']; ?>">
                                        </div>
                                    </div>
                                    <label class="g24-col-sm-3 control-label" for="m_address_village">หมู่บ้าน</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="m_address_village" name="m_address_village" class="form-control m-b-1" type="text" value="<?php echo @$data['m_address_village']; ?>">
                                        </div>
                                    </div>

                                    <label class="g24-col-sm-3 control-label" for="m_address_soi">ซอย</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="m_address_soi" name="m_address_soi" class="form-control m-b-1" type="text" value="<?php echo @$data['m_address_soi']; ?>">
                                        </div>
                                    </div>
                                    <label class="g24-col-sm-3 control-label" for="m_address_road">ถนน</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="m_address_road" name="m_address_road" class="form-control m-b-1" type="text" value="<?php echo @$data['m_address_road']; ?>">
                                        </div>
                                    </div>

                                    <label class="g24-col-sm-3 control-label" for="form-control-2">จังหวัด</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <select name="m_province_id" id="m_province_id" class="form-control m-b-1" onchange="change_province('m_province_id','m_amphure','m_amphur_id','m_district','m_district_id')">
                                                <option value="">เลือกจังหวัด</option>
                                                <?php foreach($province as $key => $value){ ?>
                                                    <option value="<?php echo $value['province_id']; ?>"<?php echo $value['province_id']==@$data['m_province_id']?'selected':''; ?>><?php echo $value['province_name']; ?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>

                                    <label class="g24-col-sm-3 control-label" for="form-control-2">อำเภอ</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <span id="m_amphure">
                                                <select name="m_amphur_id" id="m_amphur_id" class="form-control m-b-1" onchange="change_amphur('m_amphur_id','m_district','m_district_id')">
                                                    <option value="">เลือกอำเภอ</option>
                                                    <?php foreach($m_amphur as $key => $value){ ?>
                                                        <option value="<?php echo $value['amphur_id']; ?>"<?php echo $value['amphur_id']==@$data['m_amphur_id']?'selected':''; ?>><?php echo $value['amphur_name']; ?></option>
                                                    <?php }?>
                                                </select>
                                            </span>
                                        </div>
                                    </div>

                                    <label class="g24-col-sm-3 control-label" for="form-control-2">ตำบล</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <span id="m_district">
                                                <select name="m_district_id" id="m_district_id" class="form-control m-b-1">
                                                    <option value="">เลือกตำบล</option>
                                                    <?php foreach($m_district as $key => $value){ ?>
                                                        <option value="<?php echo $value['district_id']; ?>"<?php echo $value['district_id']==@$data['m_district_id']?'selected':''; ?>><?php echo $value['district_name']; ?></option>
                                                    <?php }?>
                                                </select>
                                            </span>
                                        </div>
                                    </div>

                                    <label class="g24-col-sm-3 control-label" for="m_zipcode">รหัสไปรษณีย์</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="m_zipcode" name="m_zipcode" class="form-control m-b-1" type="text" value="<?php echo @$data['m_zipcode']; ?>">
                                        </div>
                                    </div>

                                    <label class="g24-col-sm-3 control-label" for="m_tel">โทรศัพท์</label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
                                            <input id="m_tel" name="m_tel" class="form-control m-b-1" type="text" value="<?php echo @$data['m_tel']; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <div class="g24-col-sm-24">
                                    <h3 class="m-t-1">ส่งค่าหุ้นรายเดือน</h3><br>
                                    <label class="g24-col-sm-3 control-label" for="address_no"> เดือนละ </label>
                                    <div class="g24-col-sm-9">
                                        <div class="form-group">
											<?php 
												if(@$data['mem_apply_id']){
													$share_readonly = ' readonly="readonly" ';
												}else{
													$share_readonly = '';
												}
											?>
                                            <input id="share_month" name="share_month" min=""  placeholder="" class="form-control m-b-1" type="number" value="<?php echo @$data['share_month']; ?>" <?php echo @$share_readonly;?> onchange="check_salary_and_share()"  title="กรุณากรอก ค่าหุ้นรายเดือน" >
                                        </div>
                                    </div>
                                    <label class="g24-col-sm-2 control-label"  style="text-align:left;" for="address_no"> บาท</label>
                                    <div id="share_show" style="display:none;"><label class="g24-col-sm-10 control-label" style="text-align:left;" for="address_no"> เกณฑ์การถือหุ้นแรกเข้าต้องมากกว่า <span id="share_month_text"></span>  บาท </label> </div>
                                </div>
                            </div>
                            <br />
                            <!-- ธนาคาร -->
                            <div class="row">
                                
                            <div class="g24-col-sm-24">
                                <h3 class="m-t-1">เลขบัญชีสมาชิก</h3></br>
                            </div>


                            <!-- <div class="g24-col-sm-1"></div> -->
                                <div class="g24-col-sm-12" style="margin-bottom: 20px;" id="account_zone">

									<input type="hidden" name="dividend_bank_act_id" value='1'>
									<div class="row m-b-1">
										<?php
											if(@$data['bank_type'] == ''){
												$checked_1 = 'checked';
												$checked_2 = '';
											}else{
												if(@$data['bank_type'] == '1'){
													$checked_1 = 'checked';
													$checked_2 = '';
												}else if(@$data['bank_type'] == '2'){
													$checked_1 = '';
													$checked_2 = 'checked';
												}
											}
										?>
										<div class="g24-col-sm-6" style="text-align:right;display:none;"><input type="radio" id="bank_choose_1" name="bank_type" value="1" onclick="change_bank_type()" ></div>
										<div class=" g24-col-sm-18" style="display:none;">
											<div class="form-group">
												บัญชีสหกรณ์ <input type="radio" id="bank_choose_2" name="bank_type" value="2" onclick="change_bank_type()" checked> บัญชีธนาคาร 
											</div>
										</div>
									</div>
									<div id="bank_type_1" style="display:none;">
										<label class="g24-col-sm-6 control-label" for="">ธนาคาร</label>
										<div class=" g24-col-sm-18">
											<div class="form-group">
												<select id="coop_account_id" name="coop_account_id" class="form-control m-b-1">
													<option value="">เลือกบัญชี</option>
													<?php foreach($coop_account as $key => $value) { ?>
													<option value="<?php echo $value["account_id"]; ?>" <?php if($value["account_id"]==@$data["coop_account_id"]) { ?> selected="selected"<?php } ?> > <?php echo $value["account_id"]." : ".$value["account_name"]; ?>
														</option><?php } ?>
												</select>
											</div>
										</div>
									</div>

                                    <!-- $mem_bank_list -->
									<div id="bank_type_2" >
										<label class="g24-col-sm-6 control-label" for="">ธนาคาร</label>
										<div class="g24-col-sm-4">
											<div class="form-group">
												<input id="bank_id_show_0" class="form-control m-b-1 group-bank-left" type="text" value="<?php echo @$data["dividend_bank_id"]!=''?@$data["dividend_bank_id"]:'006'; ?>" readonly>
											</div>
										</div>
										<div class=" g24-col-sm-14">
											<div class="form-group">
												<select id="dividend_bank_id_0" name="dividend_bank_id[]" class="form-control m-b-1 group-bank-right js-data-example-ajax" onchange="change_bank(0)">
													<option value="">เลือกธนาคาร</option>
													<?php foreach($bank as $key => $value) {
														if($value["bank_id"]=='006'){
                                                            $selected = "selected";
                                                        }else{
                                                            $selected = "";
                                                        }
													 ?>
													<option value="<?php echo $value["bank_id"]; ?>" <?php echo @$selected; ?> > <?php echo $value["bank_name"]; ?>
														</option><?php } ?>
												</select>
											</div>
										</div>

                                        <div class="clearfix"></div>

										<label class="g24-col-sm-6 control-label clearfix" for="">สาขา</label>
										<div class="g24-col-sm-4">
											<div class="form-group">
												<input id="branch_id_show_0" class="form-control m-b-1 group-bank-left" type="text" value="<?php echo @$data["dividend_bank_branch_id"]; ?>" readonly>
											</div>
										</div>

										<div class=" g24-col-sm-14">
											<div class="form-group">
												<span id="bank_branch_0">
													<select id="dividend_bank_branch_id_0"  name="dividend_bank_branch_id[]" data-id="0" class="form-control m-b-1 group-bank-right js-data-example-ajax-branch" onchange="change_branch(0, this)">
														<option value="">เลือกสาขาธนาคาร</option>
														<?php foreach($bank_branch as $key => $value) { ?>
															<option value="<?php echo $value["branch_id"]; ?>" <?php if($value["branch_id"] == @$data["dividend_bank_branch_id"]) { ?> selected="selected"<?php } ?>><?php echo $value["branch_name"]; ?></option>
														<?php } ?>
													</select>
												</span>
											</div>
										</div>

                                        <div class="clearfix"></div>

										<label class="g24-col-sm-6 control-label" for="">เลขที่บัญชี</label>
										<div class=" g24-col-sm-18">
											<div class="form-group">
												<input id="dividend_acc_num_0" class="form-control m-b-1 clear_pay" name="dividend_acc_num[]"  type="text" value="<?php echo @$data["dividend_acc_num"]; ?>">
											</div>
										</div>

									</div>
								</div>

                                

                            </div>

                            <div class="row g24-col-sm-12">
                                    <div class="g24-col-sm-6"></div>
                                    <div class="g24-col-sm-18">
                                        <div class="form-group">
                                            <button type="button" id="addBank" class="btn btn-primary min-width-100"> <span class="icon icon-plus-circle"></span> เพิ่ม</button>
                                            <button type="button" id="removeBank" class="btn btn-danger min-width-100"> <span class="icon icon-minus-circle"></span> ลบ</button>
                                        </div>
                                        <div class="form-group">
                                            
                                        </div>
                                    </div>
                            </div>
                            
							<div class="row">
								<div class="g24-col-sm-20">
                                    <h3>หลักฐานการสมัครสมาชิก</h3><br>
                                </div>
                                <div class="g24-col-sm-12" style="margin-bottom: 20px;">
									<label class="g24-col-sm-6 control-label"></label>
									<div class=" g24-col-sm-18">
										<div class="form-group">
											<label class="fileContainer btn btn-info">
												<span class="icon icon-paperclip"></span> 
												เลือกไฟล์
												<input type="file" class="form-control" name="register_file[]" value="" multiple aria-invalid="false" onchange="readURL(this);">
											</label>
										</div>
									</div>
								</div>
							</div>
                            <div class="g24-col-sm-24">
                                <div class="row">
                                    <div class="g24-col-sm-20">
                                        <h3 class="m-t-1">หมายศาล</h3><br>
                                    </div>
                                    <div class="g24-col-sm-24">
                                        <div class="form-group">
                                            <label class="g24-col-sm-3 control-label" for="court_writ">หมายศาล</label>
                                            <div class="g24-col-sm-9">
                                                <div class="form-group">
                                                    <select name="court_writ" id="court_writ" class="form-control m-b-1">
                                                        <option <?php echo $data['court_writ'] == 0 ? 'selected' : ''; ?> value="0">ไม่มีหมายศาล</option>
                                                        <option <?php echo $data['court_writ'] == 1 ? 'selected' : ''; ?> value="1">มีหมายศาล</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="g24-col-sm-24" id="court_writ_con" <?php echo $data['court_writ'] == 1 ? '' : 'style="display: none"'; ?>>
                                        <div class="form-group">
                                            <label class="g24-col-sm-3 control-label" for="court_writ_note">บันทึกย่อ</label>
                                            <div class="g24-col-sm-9" >
                                                <div class="form-group">
                                                    <textarea name="court_writ_note" id="court_writ_note" style="margin-top: 0px;margin-bottom: 5px;min-height: 99px;height: 99px;" class="form-control m-b-1" ><?php echo $data['court_writ_note']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="g24-col-sm-20">
                                    <h3>ประวัติการแก้ไขข้อมูล</h3><br>
                                </div>
                                <div class="g24-col-sm-24" style="margin-bottom: 20px;">
									<label class="g24-col-sm-2 control-label"></label>
									<div class=" g24-col-sm-22">
                                        <table class="table table-bordered table-striped table-center">
                                            <thead> 
                                                <tr class="bg-primary">
                                                    <th>วันที่เวลา</th>
                                                    <th>รายการแก้ไข</th>
                                                    <th>ผู้ทำรายการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                if(!empty($change_history)){
                                                    foreach($change_history as $change) {
                                            ?>
                                                    <tr> 
                                                        <td><?php echo $change["created_at"]?></td>
                                                        <td>
                                                        <?php
                                                            $total = count($change["change_list"]);
                                                            foreach($change["change_list"] as $key => $change_info) {
                                                        ?>
                                                            <a href="#" data-toggle="modal" data-change-name="<?php echo $change_info["name"];?>" data-change-id="<?php echo $change_info["id"];?>" data-target="#changeModal" class="change_link_pop_up"><?php echo $change_info["name"];?></a>
                                                        <?php
                                                                if(($key+1) != $total) {
                                                                    echo ", ";
                                                                }
                                                            }
                                                        ?>
                                                        </td>
                                                        <td><?php echo $change["user"]; ?></td> 
                                                    </tr>
                                            <?php
                                                    }
                                                }else{
                                            ?>
                                                <tr><td colspan="9">ไม่พบข้อมูล</td></tr>
                                                <?php } ?>
                                            </tbody> 
                                        </table> 
                                        <label class="g24-col-sm-24 control-label text-right">
                                            <a href="<?php echo base_url(PROJECTPATH.'/manage_member_share/member_data_change_history?member_id='.$data['member_id']); ?>" >**ดูทั้งหมด</a>
                                        </label>
									</div>
								</div>
                            </div>
							<div class="row">
                                <div class="g24-col-sm-24" style="margin-bottom: 20px;">
									<label class="g24-col-sm-6 control-label"></label>
									<div class=" g24-col-sm-18">
										<div class="form-group">
											<?php if(!empty($register_file)){
												foreach($register_file as $key => $value){ ?>
												<span style="margin-right:20px" id="prev_img_<?php echo $value['register_file_id']; ?>">
													<img id="register_file_<?php echo $value['register_file_id']; ?>" src="<?php echo base_url(PROJECTPATH.'/assets/uploads/members/'.$value['register_file_name']); ?>" style="border:1px solid" width="100px" height="100px">
													<a class="icon icon-trash-o" style="color:red;cursor:pointer" onclick="del_img('<?php echo $value['register_file_id']; ?>')"></a> 
												</span>
											<?php 
												}
											} ?>
											<span id="register_file_space">
												
											</span>
										</div>
									</div>
								</div>
							</div>


                        </div>
                    </div>

                    <div class="form-group text-center p-y-lg">
                        <button type="button" onclick="check_form()" class="btn btn-primary min-width-100">ตกลง</button>
                        <a href="<?php echo base_url(PROJECTPATH.'/manage_member_share'); ?>" class="btn btn-danger min-width-100">ยกเลิก</a>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>
    </div>
</div>

<?php $this->load->view('manage_member_share/search_member_add_modal'); ?>

<div class="modal fade" id="search_idcard_modal" role="dialog">
    <div class="modal-dialog modal-dialog-idcard">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ข้อมูลสมาชิก</h4>
            </div>
            <div class="modal-body">
                <div class="bs-example" data-example-id="striped-table">
                    <table class="table table-striped">
						<thead>
							<th>เลขที่คำร้อง</th>
							<th>รหัสสมาชิก</th>
							<th>รหัสพนักงาน</th>
							<th>เลขบัตรประชาชน</th>
							<th width="30%">ชื่อ-สกุล</th>
							<th>วันที่เป็นสมาชิก</th>
							<th>วันที่ลาออก</th>
							<th></th>
						</thead>
                        <tbody id="table_idcard_data">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="changeModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">ข้อมูลการแก้ไข</h4>
            </div>
            <div class="modal-body">
                <div class="">
                    <table class="table table-striped">
						<thead>
							<th></th>
							<th class="text-center">ข้อมูลเดิม</th>
							<th class="text-center">ข้อมูลใหม่</th>
						</thead>
                        <tbody id="changeModal_tbody">
                            <td id="changeModal_td_name" class="text-center"></td>
                            <td id="changeModal_td_old_val" class="text-center"></td>
                            <td id="changeModal_td_new_val" class="text-center"></td>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer text-center">
                <button type="button" id="changeModal_close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="year_quite" value="<?php echo $year_quite; ?>">
<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/jquery.cookies.2.2.0.min.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
$link = array(
    'src' => PROJECTJSPATH.'assets/js/manage_member_share.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);

$link = array(
    'src' => PROJECTJSPATH.'assets/js/zepto.min.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
$link = array(
    'src' => PROJECTJSPATH.'assets/js/jquery.mask.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
$link = array(
    'src' => PROJECTJSPATH.'assets/js/select2.full.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
<script>
    $("#removeBank").hide();
    function get_search_member(){
        $.ajax({
            type: "POST",
            url: base_url+'manage_member_share/get_search_member',
            data: {
                search_text : $("#search_text").val(),
				form_target : 'add'
            },
            success: function(msg) {
                $("#table_data").html(msg);
            }
        });
    }
	function chkNumber(ele){
        var vchar = String.fromCharCode(event.keyCode);
        if ((vchar<'0' || vchar>'9') && (vchar != '.')) return false;
        ele.onKeyPress=vchar;
    }


    var template_bank = "";
    var countBank = 1;
    var bank_acc;

    $(document).on('keypress', '#member_id', function(e){
        console.log($(this).val().match(/[0-9sS]+/g));
        $(this).val($(this).val().match(/[0-9sS]+/g));
    });

    jQuery( document ).ready(function() {


        // get_branch_json
        template_bank = '<div class="bank_form">'+jQuery("#account_zone").html()+'</div>';
        var tmp = <?=$mem_bank_list?>;
        
        initBank(tmp);

        createSelect2();

        $(".change_link_pop_up").click(function() {
            id = $(this).attr("data-change-id")
            name = $(this).attr("data-change-name")
            $.ajax({
                type: "POST",
                url: base_url+'manage_member_share/get_change_detail',
                data: {
                    id : id
                },
                success: function(result) {
                    data = JSON.parse(result)
                    $("#changeModal_td_name").html(name)
                    $("#changeModal_td_old_val").html(data.old_value)
                    $("#changeModal_td_new_val").html(data.new_value)
                }
            })
        });
    });

    async function initBank(tmp){
        if(tmp!="" && tmp!="null"){
            var data = tmp;
            countBank--;
            if(data.length!=0)
                jQuery("#bank_type_2" ).empty();
                
            for (let index = 0; index < data.length; index++) {
                const element = data[index];
                await addBank(countBank);
                jQuery("#bank_id_show_"+countBank).val(element.dividend_bank_id);
                jQuery("#dividend_acc_num_"+countBank).val(element.dividend_acc_num);

                // jQuery('#dividend_bank_id_'+index+' option[value="'+element.divident_bank_id+'"]').attr('selected','selected');
                // jQuery('#dividend_bank_branch_id_'+index+' option[value="'+element.dividend_bank_branch_id+'"]').attr('selected','selected');

                // jQuery('#dividend_bank_id_'+index+' option[value="'+element.divident_bank_id+'"]').prop('selected', true)
                // jQuery('#dividend_bank_branch_id_'+index+' option[value="'+element.dividend_bank_branch_id+'"]').prop('selected', true)
                
                // jQuery('#dividend_bank_id_'+index).val(element.divident_bank_id).change();
                // jQuery('#dividend_bank_branch_id_'+index).val(element.dividend_bank_branch_id).change();
                console.log(element);
                var bank_id = element.dividend_bank_id;
                var branch_id = element.dividend_bank_branch_id;
                var branch_name = element.branch_name;
                jQuery("#dividend_bank_id_"+index+"  option[value="+bank_id+"]").attr('selected','selected');
                jQuery("#dividend_bank_id_"+index+"  option[value="+bank_id+"]").prop('selected', true);
                jQuery("#dividend_bank_id_"+index).val(bank_id).change();

                jQuery("#dividend_bank_branch_id_"+index+"  option[value="+branch_id+"]").attr('selected','selected');
                jQuery("#dividend_bank_branch_id_"+index+"  option[value="+branch_id+"]").prop('selected', true);
                jQuery("#dividend_bank_branch_id_"+index).val(branch_id).change();

                $("#dividend_bank_branch_id_"+index).select2("trigger", "select", {
                    data: { id: branch_id, text: branch_name }
                });

                // document.getElementById("dividend_bank_id_"+countBank).onchange();
                
                console.log('set', bank_id);
                console.log("dividend_bank_id_"+index, jQuery('#dividend_bank_id_'+index).val());
                // jQuery("#dividend_bank_id_"+index+"option[value="+element.dividend_bank_id+"]").prop('selected', true) ;
                console.log("dividend_bank_id_"+index, jQuery('#dividend_bank_id_'+index).val());

                jQuery("#branch_id_show_"+countBank).val(element.dividend_bank_branch_id);


                


                countBank++;


            }
        }
    }

    $("#addBank").click(() => {
        console.log("click add");
        $("#removeBank").show();
        addBank(countBank);
        countBank++;
    })

    $("#removeBank").click(() => {
        console.log("click add");
        console.log(countBank)
        if(countBank>1){
            jQuery(".bank_form").last().remove()
            countBank--;
        }
        if(countBank==1){
            $("#removeBank").hide();
        }
    })

    async function addBank(index){
        var bank_form = await template_bank.replace('id="bank_branch_0"', 'id="bank_branch_'+(index)+'"');
        bank_form = await bank_form.replace('id="bank_id_show_0"', 'id="bank_id_show_'+(index)+'"');
        bank_form = await bank_form.replace('id="dividend_bank_id_0"', 'id="dividend_bank_id_'+(index)+'"');
        bank_form = await bank_form.replace('id="branch_id_show_0"', 'id="branch_id_show_'+(index)+'"');
        bank_form = await bank_form.replace('id="dividend_acc_num_0"', 'id="dividend_acc_num_'+(index)+'"');
        
        bank_form = await bank_form.replace('id="dividend_bank_branch_id_0"', 'id="dividend_bank_branch_id_'+(index)+'"');
        bank_form = await bank_form.replace('change_bank(0)', 'change_bank('+index+')');
        bank_form = await bank_form.replace('change_branch(0, this)', 'change_branch('+index+', this)');
        bank_form = await bank_form.replace('data-id="0"', 'data-id="'+index+'"');
        
        jQuery("#bank_type_2" ).append( bank_form );
        
        createSelect2();
        
    }

    function createSelect2(){
        $('.js-data-example-ajax').select2();

        $('.js-data-example-ajax-branch').select2({

            ajax: {
                url: '<?=base_url("ajax/get_branch_json")?>',
                dataType: 'json',
                data: function (params) {
                    console.log($(this).attr('data-id'));
                    console.log(jQuery("#bank_id_show_"+$(this).attr('data-id') ).val());
                    return {
                        q: params.term, // search term
                        bank_id: jQuery("#bank_id_show_"+$(this).attr('data-id') ).val()
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.items
                    };
                }
            }
        });
    }

    $(document).on('change', '#court_writ', function(e){
        console.log("court_writ ==> ", $(this).val());
        if($(this).val() === "1"){
            $("#court_writ_con").show();
        }else{
            $("#court_writ_con").hide();
        }
    });
</script>
