<div class="layout-content">
    <div class="layout-content-body">
		<?php
		$act = @$_GET['act'];
		$id = @$_GET['id'];
		?>

		<?php if (@$act != "add") { ?>
		<h1 style="margin-bottom: 0">บุคลากร</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<a class="link-line-none" href="?act=add">
		<button class="btn btn-primary btn-lg bt-add" type="button">
		<span class="icon icon-plus-circle"></span>
		เพิ่มบุคลากร
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
							<th class="text-center" style="width: 80px;">ลำดับ</th>
							<th>ชื่อบุคลากร</th>
							<th>หน่วยงานภายในที่สังกัด</th>
							<th style="width: 150px;"></th> 
						  </tr> 
					 </thead>
					  <tbody>
			   <?php  
				if(!empty($rs)){
					foreach(@$rs as $key => $row){ ?>
						<tr> 
							<td class="text-center"><?php echo @$i++; ?></d>
							<td><?php echo @$row['personnel_name']; ?></td> 
							<td><?php echo @$row['department_name']; ?></td> 
							<td>
								<a href="?act=add&id=<?php echo @$row["personnel_id"] ?>">แก้ไข</a> | 
								<span class="text-del del"  onclick="del_coop_data('<?php echo @$row['personnel_id'] ?>')">ลบ</span>
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
		<h1 class="text-center m-t-1 m-b-2"><?php echo  (!empty($id)) ? "แก้ไขบุคลากร" : "เพิ่มบุคลากร" ; ?></h1>
			<form id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_facility_data/personnel_save'); ?>" method="post">	
				<?php if (!empty($id)) { ?>
				<input name="type_add"  type="hidden" value="edit" required>
				<input name="id"  type="hidden" value="<?php echo $id; ?>" required>
				<?php }else{ ?>
				<input name="type_add"  type="hidden" value="add" required>
				<?php } ?>
				
				<div class="row">
					<label class="col-sm-4 control-label" for="personnel_name">ชื่อบุคลากร</label>
					<div class="col-sm-8">
						<div class="form-group">
							<input id="personnel_name" name="personnel_name" class="form-control m-b-1" type="text" value="<?php echo @$row['personnel_name'] ?>" required title="กรุณากรอก ชื่อบุคลากร">
						</div>
					</div>
				</div>
				
				<div class="row">
                    <label class="col-sm-4 control-label" for="department_id">หน่วยงานภายในที่สังกัด</label>
                    <div class="col-sm-8">
                      <div class="form-group">
						  <select class="form-control m-b-1" id="department_id" name="department_id" onchange="" required title="กรุณาเลือก หน่วยงานภายในที่สังกัด">
								<option value="">เลือกหน่วยงานภายในที่สังกัด</option>
								<?php  
									if(!empty($rs_department)){
										foreach(@$rs_department as $key => $row_department){ 
										$selected = (@$row_department['department_id'] == @$row['department_id'])?'selected':'';
								?>
										<option value="<?php echo @$row_department['department_id']; ?>" <?php echo $selected;?>><?php echo @$row_department['department_name']; ?></option>
								<?php 
										}
									} 
								?>
							</select>
						</div>
                    </div>
                </div>
				
				<div class="row">
					<label class="col-sm-4 control-label"></label>
					<div class="col-sm-8 m-t-1">
						<button type="button"  onclick="check_form()" class="btn btn-primary min-width-100">ตกลง</button>
						<a href="?"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
					</div>
				</div>
			</form>
		</div>

<?php } ?>

	</div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_personnel.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
    