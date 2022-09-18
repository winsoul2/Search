<div class="layout-content">
    <div class="layout-content-body">
		<?php
		$act = @$_GET['act'];
		$id = @$_GET['id'];
		?>

		<?php if (@$act != "add") { ?>
		<h1 style="margin-bottom: 0">พัสดุหลัก</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<a class="link-line-none" href="?act=add">
		<button class="btn btn-primary btn-lg bt-add" type="button">
		<span class="icon icon-plus-circle"></span>
		เพิ่มพัสดุหลัก
		</button>
		</a>
		</div>
		</div>
		<?php } ?>

		<?php if (@$act != "add") { ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
			  <div class="panel panel-body">
				
				<div class="bs-example" data-example-id="striped-table">
				 <table class="table table-striped"> 
					 <thead> 
						  <tr>
							<th class="text-center" style="width: 150px;">รหัส</th>
							<th style="width: 10%;">ประเภทพัสดุ</th>
							<th style="width: 20%;">ชื่อพัสดุ</th>
							<th style="width: 20%;">หมายเหตุ</th>
							<th class="text-right">ราคา</th>
							<th style="width: 15%;" class="text-center">หน่วยนับ</th>
							<th style="width: 10%;">ค่าเสื่อม</th>
							<th style="width: 150px;"></th> 
						  </tr> 
					 </thead>
					  <tbody>
			   <?php  
				if(!empty($rs)){
					foreach(@$rs as $key => $row){ ?>
						<tr> 
							<td class="text-center"><?php echo @$row['facility_main_code']; ?></d>
							<td><?php echo @$row['facility_type_name']; ?></td> 
							<td><?php echo @$row['facility_main_name']; ?></td> 
							<td><?php echo @$row['facility_main_note']; ?></td> 
							<td class="text-right"><?php echo number_format(@$row['facility_main_price'],2); ?></td> 
							<td class="text-center"><?php echo @$row['unit_type_name']; ?></td> 
							<td><?php echo @$row['depreciation_name']; ?></td> 
							<td>
								<a href="?act=add&id=<?php echo @$row["facility_main_id"] ?>">แก้ไข</a> | 
								<span class="text-del del"  onclick="del_coop_data('<?php echo @$row['facility_main_id'] ?>')">ลบ</span>
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
			  <?php echo @$paging; ?>
			</div>
		</div>
<?php }else{ ?>

		<div class="col-md-6 col-md-offset-3">
		<h1 class="text-center m-t-1 m-b-2"><?php echo  (!empty($id)) ? "แก้ไขพัสดุหลัก" : "เพิ่มพัสดุหลัก" ; ?></h1>
			<form id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_facility_data/facility_main_save'); ?>" method="post">	
				<?php if (!empty($id)) { ?>
				<input name="type_add"  type="hidden" value="edit" required>
				<input name="id"  type="hidden" value="<?php echo $id; ?>" required>
				<input name="facility_group_full_code"  type="hidden" value="<?php echo $row['facility_group_full_code']; ?>" required>
				<input name="facility_main_run"  type="hidden" value="<?php echo $row['facility_main_run']; ?>" required>
				<?php }else{ ?>
				<input name="type_add"  type="hidden" value="add" required>
				<?php } ?>
				
				<div class="row">
                    <label class="col-sm-3 control-label" for="facility_main_code">รหัส</label>
                    <div class="col-sm-9">
                      <div class="form-group">
						  <select class="form-control m-b-1" id="facility_main_code" name="facility_main_code" onchange="" required title="กรุณาเลือก จากกลุ่มพัสดุ">
								<option value="">เลือกจากกลุ่มพัสดุ</option>
								<?php  
									if(!empty($rs_group)){
										foreach(@$rs_group as $key => $row_group){ 
										$selected = (@$row_group['facility_group_full_code'] == @$row['facility_group_full_code'])?'selected':'';
								?>
										<option value="<?php echo @$row_group['facility_group_full_code']; ?>" <?php echo $selected;?>><?php echo @$row_group['facility_group_name']; ?></option>
								<?php 
										}
									} 
								?>
							</select>
						</div>
                    </div>
                </div>
				
				<div class="row">
                    <label class="col-sm-3 control-label" for="facility_type_id">ประเภทพัสดุ</label>
                    <div class="col-sm-9">
                      <div class="form-group">
						  <select class="form-control m-b-1" id="facility_type_id" name="facility_type_id" onchange="" required title="กรุณาเลือก ประเภทพัสดุ">
								<option value="">เลือกประเภทพัสดุ</option>
								<?php  
									if(!empty($rs_type)){
										foreach(@$rs_type as $key => $row_type){ 
										$selected = (@$row_type['facility_type_id'] == @$row['facility_type_id'])?'selected':'';
								?>
										<option value="<?php echo @$row_type['facility_type_id']; ?>" <?php echo $selected;?>><?php echo @$row_type['facility_type_name']; ?></option>
								<?php 
										}
									} 
								?>
							</select>
						</div>
                    </div>
                </div>
				
				<div class="row">
					<label class="col-sm-3 control-label" for="facility_main_name">ชื่อพัสดุ</label>
					<div class="col-sm-9">
						<div class="form-group">
							<input id="facility_main_name" name="facility_main_name" class="form-control m-b-1" type="text" value="<?php echo @$row['facility_main_name'] ?>" required title="กรุณากรอก ชื่อพัสดุ">
						</div>
					</div>
				</div>
				
				<div class="row">
					<label class="col-sm-3 control-label" for="facility_main_note">หมายเหตุ</label>
					<div class="col-sm-9">
						<div class="form-group">
							<input id="facility_main_note" name="facility_main_note" class="form-control m-b-1" type="text" value="<?php echo @$row['facility_main_note'] ?>" >
						</div>
					</div>
				</div>
				
				<div class="row">
					<label class="col-sm-3 control-label" for="facility_main_price">ราคา</label>
					<div class="col-sm-9">
						<div class="form-group">
							<input id="facility_main_price" name="facility_main_price" class="form-control m-b-1" type="number" value="<?php echo @$row['facility_main_price'] ?>"  required title="กรุณากรอก ราคา">
						</div>
					</div>
				</div>
				
				<div class="row">
                    <label class="col-sm-3 control-label" for="unit_type_id">หน่วยนับ</label>
                    <div class="col-sm-9">
                      <div class="form-group">
						  <select class="form-control m-b-1" id="unit_type_id" name="unit_type_id" onchange="" required title="กรุณาเลือก หน่วยนับ">
								<option value="">เลือกหน่วยนับ</option>
								<?php  
									if(!empty($rs_unit)){
										foreach(@$rs_unit as $key => $row_unit){ 
										$selected = (@$row_unit['unit_type_id'] == @$row['unit_type_id'])?'selected':'';
								?>
										<option value="<?php echo @$row_unit['unit_type_id']; ?>" <?php echo $selected;?>><?php echo @$row_unit['unit_type_name']; ?></option>
								<?php 
										}
									} 
								?>
							</select>
						</div>
                    </div>
                </div>
				
				<div class="row">
                    <label class="col-sm-3 control-label" for="depreciation_id">ค่าเสื่อม</label>
                    <div class="col-sm-9">
                      <div class="form-group">
						  <select class="form-control m-b-1" id="depreciation_id" name="depreciation_id" onchange="" required title="กรุณาเลือก ค่าเสื่อม">
								<option value="">เลือกค่าเสื่อม</option>
								<?php  
									if(!empty($rs_depreciation)){
										foreach(@$rs_depreciation as $key => $row_depreciation){ 
										$selected = (@$row_depreciation['depreciation_id'] == @$row['depreciation_id'])?'selected':'';
								?>
										<option value="<?php echo @$row_depreciation['depreciation_id']; ?>" <?php echo $selected;?>><?php echo @$row_depreciation['depreciation_name']; ?></option>
								<?php 
										}
									} 
								?>
							</select>
						</div>
                    </div>
                </div>
				
				<div class="form-group text-center m-t-1">
					<button type="button"  onclick="check_form()" class="btn btn-primary min-width-100">ตกลง</button>
					<a href="?"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
				</div>
			</form>
		</div>

<?php } ?>

	</div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_facility_main.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
    