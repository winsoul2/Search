<div class="layout-content">
    <div class="layout-content-body">
<?php
$act = @$_GET['act'];
$id = @$_GET['id'];
?>   

<?php if (@$act != "add") { ?>
<h1 style="margin-bottom: 0">คำนำหน้าชื่อ</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
<?php $this->load->view('breadcrumb'); ?>
</div>
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
<a class="link-line-none" href="?act=add">
<button class="btn btn-primary btn-lg bt-add" type="button">
<span class="icon icon-plus-circle"></span>
เพิ่มรายการ
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
							 	  <th>#</th>
								  <th>ชื่อคำนำหน้า</th>
								  <th>คำย่อ</th>
							   	<th>เพศ</th>
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
							<td><?php echo @$row['prename_full']; ?></td> 
							<td><?php echo @$row['prename_short']; ?></td> 
							<td><?php echo @$row['sex']; ?></td> 
							<td>
							  <a href="?act=add&id=<?php echo @$row["prename_id"] ?>">แก้ไข</a> |
							   <span class="text-del del"  onclick="del_coop_member_data('<?php echo @$row['prename_id'] ?>')">ลบ</span>
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

				<h1 class="text-center m-t-1 m-b-2"> <?php echo  (!empty($id)) ? "แก้ไขรายการ" : "เพิ่มรายการ" ; ?></h1>

			<form id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_member_data/coop_prename_save'); ?>" method="post">	
			<?php if (!empty($id)) { ?>
	       <input name="type_add"  type="hidden" value="edit" required>
	       <input name="id"  type="hidden" value="<?php echo @$id; ?>" required>
	      <?php }else{ ?>
	       <input name="type_add"  type="hidden" value="add" required>
	      <?php } ?>
					
				<div class="form-group">
                    <label class="col-sm-3 control-label" for="form-control-2">ชื่อคำนำหน้า</label>
                    <div class="col-sm-9">
                      <input id="prename_full" name="prename_full" class="form-control m-b-1" type="text" value="<?php echo @$row['prename_full']; ?>" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="form-control-2">คำย่อ</label>
                    <div class="col-sm-9">
                      <input id="prename_short" name="prename_short" class="form-control m-b-1" type="text" value="<?php echo @$row['prename_short']; ?>" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="form-control-2">เพศ</label>
                    <div class="col-sm-9 m-b-1">
                      <div class="radio">
                        <label>
                          <input type="radio" name="sex" id="sex1" value="M" required <?php echo (@$row['sex'] == "M") ? "checked" : "" ; ?>> ชาย
                        </label>
                        <label>
                          <input type="radio" name="sex" id="sex2" value="F" required <?php echo (@$row['sex'] == "F") ? "checked" : "" ; ?>> หญิง
                        </label>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group text-center">
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
    'src' => PROJECTJSPATH.'assets/js/coop_prename.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>