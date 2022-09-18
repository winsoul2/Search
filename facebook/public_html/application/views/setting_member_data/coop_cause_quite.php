<div class="layout-content">
    <div class="layout-content-body">
<?php
$act = @$_GET['act'];
$id = @$_GET['id'];
?>   

<?php if ($act != "add") { ?>
	<h1 style="margin-bottom: 0">สาเหตุการลาออก</h1>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
	<?php $this->load->view('breadcrumb'); ?>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
	<a class="link-line-none" href="?act=add">
	<button class="btn btn-primary btn-lg bt-add" type="button">
	<span class="icon icon-plus-circle"></span>
	เพิ่มสาเหตุ
	</button>
	</a>
	</div>
	</div>
<?php } ?>

<?php if ($act != "add") { ?>
		<div class="row gutter-xs">
				<div class="col-xs-12 col-md-12">
	              <div class="panel panel-body">
	                
					<div class="bs-example" data-example-id="striped-table">
						<table class="table table-striped"> 
							 <thead> 
								  <tr>
									<th>#</th>
									<th>สาเหตุการลาออก</th>
									<th></th> 
								  </tr> 
							 </thead>

							<tbody>

					  <?php  
							if(!empty($rs)){
								foreach(@$rs as $key => $row){ 
						?>
								<tr> 
									<th scope="row"><?php echo @$i++; ?></th>
									<td><?php echo @$row['resign_cause_name']; ?></td> 
									<td>
										<a href="?act=add&id=<?php echo @$row["resign_cause_id"] ?>">แก้ไข</a> |
										<span class="text-del del"  onclick="del_coop_member_data('<?php echo @$row['resign_cause_id'] ?>')">ลบ</span>
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
<?php }else{ ?>
		<div class="col-md-6 col-md-offset-3">

			<h1 class="text-center m-t-1 m-b-2"> <?php echo  (!empty($id)) ? "แก้ไขสาเหตุ" : "เพิ่มสาเหตุ" ; ?></h1>

			<form id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_member_data/coop_cause_quite_save'); ?>" method="post">	
			<?php if (!empty($id)) { ?>
			<input name="type_add"  type="hidden" value="edit" required>
			<input name="id"  type="hidden" value="<?php echo $id; ?>" required>
			<?php }else{ ?>
			<input name="type_add"  type="hidden" value="add" required>
			<?php } ?>
				
			<div class="form-group">
				<label class="col-sm-3 control-label" for="form-control-2">สาเหตุการลาออก</label>
				<div class="col-sm-9">
				  <input id="resign_cause_name" name="resign_cause_name" class="form-control m-b-1" type="text" value="<?php echo @$row['resign_cause_name']; ?>" required>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="form-control-2"></label>
				<div class="col-sm-9">
					<input type="checkbox" name="check_debt" value="check_debt" <?php echo $row["check_debt"] ? "checked" : "";?>> ไม่พิจารณายอดคงค้าง<br>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="form-control-2"></label>
				<div class="col-sm-9">
					<input type="checkbox" name="cal_interest_af" value="cal_interest_af" <?php echo $row["cal_interest_af"] ? "checked" : "";?>> ไม่คำนวนดอกเบี้ยหลังจากพ้นสภาพ<br>
				</div>
			</div>
			<div class="form-group text-center">
			<button type="button"  onclick="check_form()" class="btn btn-primary min-width-100 m-t-1">ตกลง</button>
			<a href="?"><button class="btn btn-danger min-width-100 m-t-1" type="button">ยกเลิก</button></a>
			</div>

			</form>

		</div>


<?php } ?>

	</div>
</div>

<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_cause_quite.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>

