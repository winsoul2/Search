<div class="layout-content">
    <div class="layout-content-body">
	<style>
	.indent{
		text-indent: 40px;
		.modal-dialog-data {
			width:90% !important;
			margin:auto;
			margin-top:1%;
			margin-bottom:1%;
		}
	}
	table>thead>tr>th{
		text-align: center;
	}
	table>tbody>tr>td{
		text-align: center;
	}

	label {
		padding-top: 6px;
		text-align: right;
	}
	.text-center{
		text-align:center;
	}
	.bt-add{
		float:none;
	}
	.modal-dialog{
		width:80%;
	}
	</style>
	<h1 style="margin-bottom: 0">กลุ่มพัสดุ</h1>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 " style="padding-right:0px;padding-left:0px">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;padding-left:0px">
			<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
			<button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_group('1')"><span class="icon icon-plus-circle"></span> เพิ่มกลุ่มหลัก</button> 
			<button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_group('2')"><span class="icon icon-plus-circle"></span> เพิ่มย่อย</button> 
			<button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_group('3')"><span class="icon icon-plus-circle"></span> เพิ่มกลุ่ม</button>
		</div>
	</div>


	<div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                  
          <div class="bs-example" data-example-id="striped-table">

           <table class="table table-striped"> 
             <thead> 
                <tr>
					<th>รหัสกลุ่มพัสดุ</th>
					<th class="text-left">กลุ่มหลัก</th>
					<th class="text-left">กลุ่มย่อย</th>
					<th class="text-left">กลุ่ม</th>
					<th>จัดการ</th> 
                </tr> 
             </thead>
                  <tbody class="mem_group_space">
						<?php  
							if(!empty($rs)){
								foreach(@$rs as $key => $row3){ 
						?>
								<tr>
									<td class="text-center"><?php echo @$row3['facility_group_full_code']; ?></td> 
									<td class="text-left"><?php echo @$row3['t3_code'].'-'.@$row3['t3_name']; ?></td> 
									<td class="text-left"><?php echo @$row3['t2_code'].'-'.@$row3['t2_name']; ?></td> 
									<td class="text-left"><?php echo @$row3['facility_group_code'].'-'.@$row3['facility_group_name']; ?></td> 
									<td align="right">
										<a style="cursor:pointer;" onclick="edit_facility_group('<?php echo @$row3['facility_group_id']; ?>','<?php echo @$row3['facility_group_code']; ?>','<?php echo @$row3['facility_group_name']; ?>','<?php echo @$row3['facility_group_type']; ?>','<?php echo @$row3['facility_group_parent_id']; ?>');">แก้ไข</a> 
										| 
										<a style="cursor:pointer;" onclick="delete_facility_group('<?php echo @$row3['facility_group_id']; ?>');" class="text-del">ลบ</a>
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
			  <?php echo @$paging ?>
		  </div>
		</div>

	</div>
</div>

<div id="department_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title"><span id="title_1">เพิ่มข้อมูลฝ่าย</span></h2>
			</div>
			<div class="modal-body">
				<div class="form-group" style="padding-bottom: 30px;">
				<form action="<?php echo base_url(PROJECTPATH.'/setting_facility_data/facility_group_save'); ?>" method="POST" id="form1">
					<div id="choose_group" class="col-sm-12" style="display:none;">
						<div class="col-sm-4" style="text-align:right;">
							<label style=""><span>กลุ่มหลัก</span><label>
						</div>
						<div class="col-sm-4">
							<select class="form-control m-b-1" id="main_group" name="main_group" onchange="change_group()">
								<option value="">เลือกกลุ่มหลัก</option>
								<?php  
									if(!empty($rs_group)){
										foreach(@$rs_group as $key => $row_group){ 
								?>
										<option value="<?php echo @$row_group['facility_group_id']; ?>" group_code="<?php echo $row_group['facility_group_code']; ?>"><?php echo @$row_group['facility_group_code']."-".@$row_group['facility_group_name']; ?></option>
								<?php 
										}
									} 
								?>
							</select>
							<input type="hidden" name="main_group_code" id="main_group_code">
						</div>
					</div>
					<div id="choose_department" class="col-sm-12" style="display:none;">
						<div class="col-sm-4" style="text-align:right;">
							<label style=""><span>กลุ่มย่อย</span><label>
						</div>
						<div class="col-sm-4" id="parent_group_space"> 
							<select class="form-control m-b-1" id="parent_group" name="parent_group" onchange="change_parent_group()">
								<option value="">เลือกกลุ่มย่อย</option>
							</select>
						</div>
						<input type="hidden" name="parent_group_code" id="parent_group_code">
					</div>
					<div class="col-sm-12" style="">
						<div class="col-sm-4" style="text-align:right;">
							<label style=""><span id="title_2">รหัสกลุ่ม</span><label>
						</div>
						<div class="col-sm-4">
							<input type="hidden" class="form-control" id="facility_group_id" name="facility_group_id">
							<input type="hidden" class="form-control" id="facility_group_type" name="facility_group_type">
							<input type="text" class="form-control  m-b-1" id="facility_group_code" name="facility_group_code">
						</div>
					</div>
					<div class="col-sm-12" style="">
						<div class="col-sm-4" style="text-align:right;">
							<label style=""><span id="title_3">ชื่อกลุ่ม</span><label>
						</div>
						<div class="col-sm-4">
							<input type="text" class="form-control m-b-1" id="facility_group_name" name="facility_group_name">
						</div>
					</div>
					<div class="col-sm-12" style="text-align:center;margin-top:20px;margin-bottom:20px;">
								<button type="button" class="btn btn-primary" onclick="save_facility_group()">บันทึก</button>&nbsp;&nbsp;&nbsp;
								<button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
					</div>
					
					<table id="group_table" class="table table-bordered table-striped table-center">
						<thead> 
							<tr class="bg-primary">
								<th width="25%">รหัสกลุ่มหลัก</th>
								<th width="55%">ชื่อกลุ่มหลัก</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						<?php  
							if(!empty($rs_group)){
								foreach(@$rs_group as $key => $row_group){ 
						?>
							<tr> 
								<td><?php echo @$row_group['facility_group_code']; ?></td>
								<td style="text-align:left;"><?php echo @$row_group['facility_group_name']; ?></td>
								<td>
								<a style="cursor:pointer;" onclick="edit_facility_group('<?php echo @$row_group['facility_group_id']; ?>','<?php echo @$row_group['facility_group_code']; ?>','<?php echo @$row_group['facility_group_name']; ?>','<?php echo @$row_group['facility_group_type']; ?>','<?php echo @$row_group['facility_group_parent_id']; ?>');">แก้ไข</a> 
								| 
								<a style="cursor:pointer;" onclick="delete_facility_group('<?php echo @$row_group['facility_group_id']; ?>');" class="text-del">ลบ</a>
								</td>
							</tr>
						<?php 
								}
							} 
						?>
						</tbody> 
					</table> 
					
					<table id="department_table" class="table table-bordered table-striped table-center">
						<thead> 
							<tr class="bg-primary">
								<th>กลุ่มหลัก</th>
								<th>รหัสกลุ่มย่อย</th>
								<th>ชื่อกลุ่มย่อย</th>
								<th width="15%"></th>
							</tr>
						</thead>
						<tbody>
						<?php  
							if(!empty($rs_group2)){
								foreach(@$rs_group2 as $key => $row_group2){ 
						?>
							<tr> 
								<td style="text-align:left;"><?php echo @$row_group2['parent_code'].'-'.@$row_group2['parent_name']; ?></td>
								<td><?php echo @$row_group2['facility_group_full_code']; ?></td>
								<td style="text-align:left;"><?php echo @$row_group2['facility_group_code'].'-'.@$row_group2['facility_group_name']; ?></td>
								<td>
									<a style="cursor:pointer;" onclick="edit_facility_group('<?php echo @$row_group2['facility_group_id']; ?>','<?php echo @$row_group2['facility_group_code']; ?>','<?php echo @$row_group2['facility_group_name']; ?>','<?php echo @$row_group2['facility_group_type']; ?>','<?php echo @$row_group2['facility_group_parent_id']; ?>');">แก้ไข</a> 
									| 
									<a style="cursor:pointer;" onclick="delete_facility_group('<?php echo @$row_group2['facility_group_id']; ?>');" class="text-del">ลบ</a>
								</td>
							</tr>
						<?php 
								}
							} 
						?>
						</tbody> 
					</table> 
				</form>
				</div>
				<span>&nbsp;</span>
			</div>
		</div>
	</div>
</div>

<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/facility_group.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>