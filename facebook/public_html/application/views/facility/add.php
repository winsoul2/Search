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
	.picture-m{
		border: 1px solid #ccc;
		padding-top: 40px;
		padding-bottom: 40px;
		text-align: center;
	}

	.picture-m > i{
		font-size: 100px;color: #ccc;
	}
</style>

<h1 style="margin-bottom: 0">ลงทะเบียนพัสดุ</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>

</div>
<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12">
        <div class="panel panel-body">
            <form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/facility/save_add'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="myForm">
                <?php $store_id = @$_GET['s_id'];?>
				<input type="hidden" name="n_id" id="n_id" value="<?php echo @$_GET['n_id']; ?>"/>
                <input type="hidden" name="store_id" id="store_id" value="<?php echo (!empty($data))?$data['store_id']:''; ?>"/>
				<input name="store_run"  type="hidden" value="<?php echo @$data['store_run']; ?>" required>
				<input name="store_code"  type="hidden" value="<?php echo @$data['store_code']; ?>" required>
				<input name="facility_main_code_old"  type="hidden" value="<?php echo @$data['facility_main_code']; ?>" required>
                <div class="m-t-1">

                    <div class="g24-col-sm-20">

                        <div class="form-group">
                            <label class="g24-col-sm-3 control-label">รหัสพัสดุ <span id="naja"></span> </label>
                            <div class="g24-col-sm-8">
                                <div class="form-group">
									<div class="input-group">
										<input id="facility_main_code" name="facility_main_code" class="form-control" style="text-align:left;" type="text" value="<?php echo empty($data) ? '' : @$data['facility_main_code']; ?>" readonly="readonly" required title="กรุณาป้อน รหัสพัสดุ" />
										<span class="input-group-btn">
											<a data-toggle="modal" data-target="#myModal" id="test" class="fancybox_share fancybox.iframe" href="#">
												<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span>
												</button>
											</a>
										</span>	
									</div>                                    
                                </div>
                            </div>
							<label class="g24-col-sm-3 control-label datepicker1" for="budget_year">ปีงบประมาณ</label>
                            <div class="g24-col-sm-6">
                                <div class="form-group">
                                    <!-- <input type="text" class="form-control m-b-1" name="budget_year" id="budget_year" value="<?php echo @$data['budget_year']?>" required title="กรุณาเลือก ปีงบประมาณ"> -->
                                    <select id="budget_year" name="budget_year" class="form-control m-b-1" required title="กรุณาเลือก จัดซื้อโดย">
                                        <option value="">เลือกปีงบประมาณ</option>
                                            <?php
                                                $this_year_b = date("Y")+543;
                                                $next_year_b = date("Y")+543+1;
                                            ?>
                                            <?php
                                                if($data['budget_year'] < $this_year_b && !empty($data['budget_year'])) {
                                            ?>
                                            <option value="<?php echo $data['budget_year']; ?>" selected="selected"><?php echo $data['budget_year']; ?></option>
                                            <?php
                                                }
                                            ?>
                                            <option value="<?php echo $this_year_b; ?>"<?php if($this_year_b == $data['budget_year']) { ?> selected="selected"<?php } ?>><?php echo $this_year_b; ?></option>
                                            <option value="<?php echo $next_year_b; ?>"<?php if($next_year_b == $data['budget_year']) { ?> selected="selected"<?php } ?>><?php echo $next_year_b; ?></option>
                                    </select>
                                </div>
                            </div>
							<label class="g24-col-sm-2 control-label datepicker1">&nbsp;</label>
                        </div>
						<div class="form-group">
                            <label class="g24-col-sm-3 control-label">ชื่อพัสดุ </label>
                            <div class="g24-col-sm-18">
                                <div class="form-group">
                                    <input type="text" class="form-control m-b-1" name="store_name" id="store_name" value="<?php echo @$data['store_name']?>"  readonly="readonly">
                                </div>
                            </div>
                        </div>
						
						<div class="form-group">
                            <label class="g24-col-sm-3 control-label">หน่วยนับ </label>
                            <div class="g24-col-sm-3">
                                <div class="form-group">
                                    <input type="hidden" class="form-control m-b-1" name="unit_type_id" id="unit_type_id" value="<?php echo @$data['unit_type_id'];?>"  readonly="readonly">
                                    <input type="text" class="form-control m-b-1" name="unit_type_name" id="unit_type_name" value="<?php echo @$arr_unit_type[@$data['unit_type_id']];?>"  readonly="readonly">
                                </div>
                            </div>
							<label class="g24-col-sm-2 control-label">ราคา </label>
                            <div class="g24-col-sm-3">
                                <div class="form-group">
                                    <input type="text" class="form-control m-b-1" name="store_price" id="store_price" value="<?php echo @$data['store_price']?>"  readonly="readonly">
                                </div>
                            </div>

							<label class="g24-col-sm-2 control-label"></label>
                            <div class="g24-col-sm-6">
                                <div class="form-group">
                                    <input type="checkbox" name="is_alert_remain" id="is_alert_remain" value="1"<?php if($data['is_alert_remain']) { ?> checked="checked"<?php } ?>> เตือนเมื่อต่ำกว่า
									<input type="text" class="form-control m-b-1" name="alert_remain" id="alert_remain" value="<?php echo empty($data['alert_remain']) ? '' : @$data['alert_remain']?>" style="display: inline; width: 100px;"> หน่วย
                                </div>
                            </div>
                        </div>

						<?php /*<div class="form-group" style="margin-bottom: 5px;">
                            <label class="g24-col-sm-3 control-label">หน่วยงาน <span id="naja"></span> </label>
                            <div class="g24-col-sm-11">
                                <div class="form-group">
									<input id="department_id" name="department_id" class="form-control" style="text-align:left;" type="hidden" value="<?php echo empty($data) ? '' : @$data['department_id']; ?>" />
									<div class="input-group">
										<input id="department_name" name="department_name" class="form-control" style="text-align:left;" type="text" value="<?php echo empty($data) ? '' : @$data['department_name']; ?>" readonly="readonly" required title="กรุณาป้อน หน่วยงาน" />
										<span class="input-group-btn">
											<a data-toggle="modal" data-target="#myModalDepartment" id="test" class="fancybox_share fancybox.iframe" href="#">
												<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span>
												</button>
											</a>
										</span>	
									</div>                                    
                                </div>
                            </div>
							<!-- <label class="g24-col-sm-3 control-label" for="store_no">เลขทะเบียน</label>
                            <div class="g24-col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control m-b-1" name="store_no" id="store_no" value="<?php echo @$data['store_no']?>" required title="กรุณาเลือก เลขทะเบียน">
                                </div>
                            </div> -->
                        </div>*/ ?>
						
                        <div class="form-group">							
                            <label class="g24-col-sm-3 control-label" for="receive_date">วันที่รับ</label>
                            <div class="g24-col-sm-6">
                                <div class="form-group">
                                    <input id="receive_date" name="receive_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(empty($data) ? date("Y-m-d") : @$data['receive_date']); ?>" data-date-language="th-th" required title="กรุณาป้อน วันที่รับ">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
							
                            <label class="g24-col-sm-3 control-label" for="personnel_id">จัดซื้อโดย</label>
                            <div class="g24-col-sm-12">
                                <div class="form-group">
                                    <select id="personnel_id" name="personnel_id" class="form-control m-b-1" required title="กรุณาเลือก จัดซื้อโดย">
										<option value="">เลือกจัดซื้อโดย</option>
                                        <?php foreach($personnel as $key => $value) { ?>
                                            <option value="<?php echo $value["personnel_id"]; ?>"<?php if($value["personnel_id"] == @$data["personnel_id"]) { ?> selected="selected"<?php } ?>><?php echo $value["personnel_name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
						
                        <div class="form-group">							
                            <label class="g24-col-sm-3 control-label" for="means_id">ประเภทการจัดซื้อ</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <select id="means_id" name="means_id" class="form-control m-b-1" required title="กรุณาเลือก ประเภทการจัดซื้อ">
										<option value="">เลือกประเภทการจัดซื้อ</option>
                                        <?php foreach($means as $key => $value) { ?>
                                            <option value="<?php echo $value["means_id"]; ?>"<?php if($value["means_id"] == @$data["means_id"]) { ?> selected="selected"<?php } ?>><?php echo $value["means_name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
							<label class="g24-col-sm-3 control-label" for="certificate_no">เลขที่ใบสำคัญ</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
									<input type="text" class="form-control m-b-1" name="certificate_no" id="certificate_no" value="<?php echo @$data['certificate_no']?>" required title="กรุณาเลือก เลขที่ใบสำคัญ">
                                </div>
                            </div>
                        </div>
						
						<?php /*<div class="form-group">       
							<label class="g24-col-sm-3 control-label" for="type_money_id">ประเภทเงิน</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <select id="type_money_id" name="type_money_id" class="form-control m-b-1" required title="กรุณาเลือก ประเภทเงิน">
										<option value="">เลือกประเภทเงิน</option>
                                        <?php foreach($type_money as $key => $value) { ?>
                                            <option value="<?php echo $value["type_money_id"]; ?>"<?php if($value["type_money_id"] == @$data["type_money_id"]) { ?> selected="selected"<?php } ?>><?php echo $value["type_money_name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>*/ ?>
						
						<div class="form-group">       
							<label class="g24-col-sm-3 control-label" for="type_evidence_id">หลักฐาน</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <select id="type_evidence_id" name="type_evidence_id" class="form-control m-b-1" required title="กรุณาเลือก หลักฐาน">
										<option value="">เลือกหลักฐาน</option>
                                        <?php foreach($type_evidence as $key => $value) { ?>
                                            <option value="<?php echo $value["evidence_id"]; ?>"<?php if($value["evidence_id"] == @$data["type_evidence_id"]) { ?> selected="selected"<?php } ?>><?php echo @$value["evidence_name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
							<label class="g24-col-sm-3 control-label" for="start_date">ลงวันที่</label>
                            <div class="g24-col-sm-9">
                                <div class="form-group">
                                    <input id="start_date" name="start_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(empty($data) ? date("Y-m-d") : @$data['start_date']); ?>" data-date-language="th-th" required title="กรุณาป้อน ลงวันที่">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
						
						<div class="form-group">       
							<label class="g24-col-sm-3 control-label" for="seller_id">ผู้ขาย</label>
                            <div class="g24-col-sm-21">
                                <div class="form-group">
                                    <select id="seller_id" name="seller_id" class="form-control m-b-1" required title="กรุณาเลือก ผู้ขาย">
										<option value="">เลือกผู้ขาย</option>
                                        <?php foreach($seller as $key => $value) { ?>
                                            <option value="<?php echo $value["seller_id"]; ?>"<?php if($value["seller_id"] == @$data["seller_id"]) { ?> selected="selected"<?php } ?>><?php echo @$value["seller_name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="g24-col-sm-4">

                        <div class="g24-col-sm-24 m-b-1 text-right">
                            <div class="mem_pic" style="margin-bottom:20px;display: block;margin: 0 auto;">								
								<?php $store_pic = empty($data['store_pic']) ? "default.png" : $data['store_pic'];?>
								<img id="store_pic" src="<?php echo base_url(PROJECTPATH."/assets/uploads/facility/".$store_pic); ?>" alt="" />								
								
								<button type="button" id="btn_store_pic" class="btn btn-info">
									<i class="fa fa-picture-o" aria-hidden="true"></i> 
									แนบรูปภาพ
								</button>
                            </div>
                        </div>
                    </div>                  
                </div>
				
				<div class="m-t-1">
					<div class="g24-col-sm-24">
						<div class="form-group text-center p-y-lg">						
							<button type="button" onclick="check_form()" class="btn btn-primary min-width-100">
								<span class="icon icon-save"></span>
								บันทึก
							</button>
							<?php if(!empty($data['store_id'])){ ?>
							<button type="button" onclick="add_quantity()" class="btn btn-primary min-width-100">
								<span class="icon icon-plus-circle"></span>
								เพิ่มจำนวน
							</button>
							<?php } ?>
						</div>
					</div>
				</div>
            </form>
			
			<form method="post" action="<?php echo base_url(PROJECTPATH.'/facility/del_all'); ?>" class="g24 form form-horizontal" id="form_del_all">   
				<input type="hidden" id="type_del" name="type_del" value="add"/>
				<input type="hidden" id="store_id" name="store_id" value="<?php echo $store_id;?>"/>
				<div class="bs-example" data-example-id="striped-table">
					<div id="tb_wrap">
						<table class="table table-bordered table-striped table-center">
							<thead>
							<tr class="bg-primary">
								<th><input type="checkbox" class="check-all" id="check-all" value=""></th>
								<th>ลำดับ</th>
								<th>เลขพัสดุ</th>
								<th>วันที่ลงทะเบียนรับ</th>
								<th>เลขเครื่อง</th>
								<th>สถานะ</th>
								<th>จัดการ</th>
							</tr>
							</thead>
							<tbody id="table_data">
							<?php
							$j=1;						
							if(!empty($row)){
								foreach(@$row as $key => $value){ ?>
									<tr>
										<td style="width: 80px;"><input type="checkbox" class="store-id-checkbox" id="store_id[<?php echo @$value['store_id'];?>]" name="store_id[<?php echo @$value['store_id'];?>]" value="<?php echo @$value['store_id'];?>"></td>
										<td style="width: 80px;"><?php echo @$j++; ?></td>
										<td style="width: 20%;"><?php echo @$value['store_code']; ?></td>
										<td style="width: 150px;"><?php echo $this->center_function->ConvertToThaiDate(@$value['receive_date'],true,false);?></td>
										<td><?php echo @$value['store_serial']; ?></td>
										<td style="width: 200px;"><?php echo @$value['facility_status_name']; ?></td>
										<td style="width: 200px;">
											<span class="text-edit" onclick="add_serial('<?php echo @$value['store_id'] ?>','<?php echo @$store_id?>')">แก้ไข</span>
											|
											<span class="text-del del"  onclick="del_coop_data('<?php echo @$value['store_id'] ?>','<?php echo @$store_id?>')">ลบ</span>
                                            |
                                            <span class="text-warning print" style="cursor: pointer;" onclick="print_coop_data('<?php echo @$value['store_id'] ?>','<?php echo @$store_id?>')">พิมพ์</span>
										</td>
									</tr>
							<?php 
								}
							} 
							?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="m-t-1">
					<div class="g24-col-sm-24">
						<div class="form-group p-y-lg">						
							<button type="button" onclick="del_all()" class="btn btn-primary">
								ลบ
                            </button>
                            <button type="button" onclick="print_all()" class="btn btn-primary">
                                พิมพ์
							</button>
						</div>
					</div>
				</div>
			</form>
        </div>
    </div>
</div>
    </div>
</div>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display:flex;">
                <h4 class="modal-title">ข้อมูลพัสดุ</h4> -->
                <div style="width:75%">
                    <h4 class="modal-title">ข้อมูลพัสดุ</h4>
                </div>
                <div style="width:20%">
                    <a data-toggle="modal" id="madal-add-supply" href="<?php echo base_url(PROJECTPATH.'/setting_facility_data/facility_main'); ?>">
                        <button type="button" class="btn btn-primary" style="padding: 0;height: unset;">เพิ่มพัสดุ</button>
                    </a>
                </div>
                <div style="width:5%">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
            </div>
            <div class="modal-body">
                <div class="input-with-icon">
                    <input class="form-control input-thick pill m-b-2" type="text" placeholder="กรอกรหัสหรือชื่อพัสดุ" name="search_facility_text" id="search_facility_text" onkeyup="get_search_facility()">
                    <span class="icon icon-search input-icon"></span>
                </div>
                <div class="bs-example scrollbar" data-example-id="striped-table">
					<div class="force-overflow">
						<table class="table table-striped">

							<tbody id="table_data_facility">
								
							</tbody>

						</table>
					</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalDepartment" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ข้อมูลหน่วยงาน</h4>
            </div>
            <div class="modal-body">
                <div class="input-with-icon">
                    <input class="form-control input-thick pill m-b-2" type="text" placeholder="กรอกหน่วยงาน" name="search_department_text" id="search_department_text" onkeyup="get_search_department()">
                    <span class="icon icon-search input-icon"></span>
                </div>
                <div class="bs-example scrollbar" data-example-id="striped-table">
					<div class="force-overflow">
						<table class="table table-striped">

							<tbody id="table_data_department">

							</tbody>

						</table>
					</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalQuantity" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">เพิ่มจำนวน</h4>
            </div>
            <div class="modal-body">
				<form method="post" action="?" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="form_quantity">
					<input type="hidden" id="store_id" name="store_id" value="<?php echo (!empty($data))?$data['store_id']:''; ?>"/>
					<div class="form-group">						
						<label class="g24-col-sm-6 control-label" for="store_quantity">จำนวน</label>
						<div class="g24-col-sm-9">
							<div class="form-group">
								<input type="text" class="form-control m-b-1" name="store_quantity" id="store_quantity" value="<?php echo (empty($data['store_quantity']))?'1':@$data['store_quantity'];?>" required title="กรุณาเลือก จำนวน">
							</div>
						</div>
						<button type="button" onclick="check_quantity()" class="btn btn-primary min-width-100" style="margin-left: 5px;">
							<span class="icon icon-save"></span>
							บันทึก
						</button>
					</div>					
				</form>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalSerial" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">เพิ่มเลขเครื่อง</h4>
            </div>
            <div class="modal-body">
				<form method="post" action="?" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="form_quantity">
					<input type="hidden" id="store_id_serial" name="store_id_serial" value=""/>
					<input type="hidden" id="store_id_s" name="store_id_s" value=""/>
					<div class="form-group">
						<label class="g24-col-sm-6 control-label" for="store_serial">เลขเครื่อง</label>
						<div class="g24-col-sm-9">
							<div class="form-group">
								<input type="text" class="form-control m-b-1" name="store_serial" id="store_serial" value="" required title="กรุณาเลือก จำนวน">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="g24-col-sm-6 control-label" for="facility_status_id">สถานะ</label>
						<div class="g24-col-sm-9">
							<div class="form-group">
								<select id="facility_status_id" name="facility_status_id" class="form-control m-b-1">
									<option value="">- กรุณาเลือก -</option>
									<?php foreach($facility_status as $key => $value) { ?>
										<option value="<?php echo $value["facility_status_id"]; ?>"><?php echo @$value["facility_status_name"]; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="g24-col-sm-6 control-label" for="remark">หมายเหตุ</label>
						<div class="g24-col-sm-17">
							<div class="form-group">
								<input type="text" class="form-control m-b-1" name="remark" id="remark" value="">
							</div>
						</div>
					</div>
					<div class="form-group text-center">
						<button type="button" onclick="check_serial()" class="btn btn-primary min-width-100" style="margin-left: 5px;">
							<span class="icon icon-save"></span>
							บันทึก
						</button>
					</div>					
				</form>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/jquery.cookies.2.2.0.min.js',
    'type' => 'text/javascript'
);
echo script_tag($link);

$link = array(
    'src' => 'assets/js/facility_add.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>