<div class="layout-content">
    <div class="layout-content-body">
<?php
$bank_id = @$_GET['bank_id'];
$act = @$_GET['act'];
$id = @$_GET['id'];
?>

<?php if ($act != "add") { ?>
<h1 style="margin-bottom: 0"><?php echo @$bank_id." ".@$bank_name;  ?></h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
<!-- <a class="font-menu-main link-line-none" href="/">หน้าแรก</a>
<a class="font-menu-main link-line-none" href="admin.php"> / ผู้ดูแลระบบ</a> -->
</div>
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0 m-t-1">
<a class="link-line-none" href="?act=add&bank_id=<?php echo @$bank_id; ?>">
<button class="btn btn-primary btn-lg bt-add" type="button" style="margin-top: -3em;">
<span class="icon icon-plus-circle"></span>
เพิ่มสาขา
</button>
</a>
</div>
</div>
<?php } ?>

<?php if ($act != "add") { ?>
                   <div class="form-group">
                        <div class="col-sm-6">
                          <div class="input-with-icon">
                            <input class="form-control input-thick pill m-b-2" type="text" placeholder="ค้นหา" name="search_text" id="search_text">
							<input id="bank_id" name="bank_id"  type="hidden" value="<?php echo @$bank_id; ?>" >
                            <span class="icon icon-search input-icon"></span>
                          </div>
                        </div>
                      </div>
<div class="row gutter-xs">

				<div class="col-xs-12 col-md-12">

	       <div class="panel panel-body">
	                
					<div class="bs-example" data-example-id="striped-table">
					 <table class="table table-striped"> 
						 <thead> 
						 	  <tr>
							 	<th>รหัสสาขา</th>
							   	<th>ชื่อสาขา</th>
							    <th>อำเภอ</th>
							    <th>จังหวัด</th> 
							    <th></th> 
							  </tr> 
						 </thead>

             <tbody id="result">
             </tbody>

                <tbody id="table_first">
                <?php  
					if(!empty($rs)){
						foreach(@$rs as $key => $row){ 
				?>
						<tr> 

						<th scope="row"><?php echo @$row['branch_code']; ?></th>
						<td><?php echo @$row['branch_name']; ?></td> 
						<td><?php echo @$row['amphur_name']; ?></td>
						<td><?php echo @$row['province_name']; ?></td> 

						<td>
						<a href="?act=add&id=<?php echo @$row["branch_id"]; ?>&bank_id=<?php echo @$row['bank_id']; ?>">แก้ไข</a> | 
						<span class="text-del del"  onclick="del_coop_basic_data('<?php echo @$row['branch_id'] ?>','<?php echo @$row['bank_id'] ?>')">ลบ</span>
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

			<h1 class="text-center m-t-1 m-b-2"> <?php echo  (!empty($id)) ? "แก้ไขสาขา" : "เพิ่มสาขา" ; ?></h1>

			<form  id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_bank_branch_save'); ?>" method="post">	
			<?php if (!empty($id)) { ?>
	       <input name="type_add"  type="hidden" value="edit" required>
	       <input name="id"  type="hidden" value="<?php echo @$id; ?>" required>
	      <?php }else{ ?>
	       <input name="type_add"  type="hidden" value="add" required>
	      <?php } ?>
         <input name="bank_id"  type="hidden" value="<?php echo @$_GET['bank_id']; ?>" >
          
                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="form-control-2">รหัสสาขา</label>
                    <div class="col-sm-9">
                      <input id="branch_code" name="branch_code" class="form-control m-b-1" type="text" value="<?php echo @$row["branch_code"]; ?>" required maxlength="4">
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="form-control-2">ชื่อสาขา</label>
                    <div class="col-sm-9">
                      <input id="branch_name" name="branch_name" class="form-control m-b-1" type="text" value="<?php echo @$row["branch_name"]; ?>" required>
                    </div>
                  </div>                  
               
                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="form-control-2">จังหวัด</label>
                    <div class="col-sm-9 m-b-1">
                     <select name="province_id" id="province_id" class="form-control province" required>
                        <option value="">เลือกจังหวัด</option>
						<?php
						if(!empty($rs_province)){
							foreach(@$rs_province as $key => $row_province){ 
						?>

                      <?php if (@$row_province['province_id'] == @$row["province_id"]) { ?>
                        <option value="<?php echo @$row_province['province_id'];?>" selected><?php echo @$row_province['province_name'];?></option>
                      <?php }else{ ?>
                        <option value="<?php echo @$row_province['province_id'];?>"><?php echo @$row_province['province_name'];?></option>
                      <?php } ?>
                      
                        <?php 
							}
						}
						?>
                      </select>
                    </div>
                  </div>

                <?php if (!empty($row["amphur_id"])) { ?>
                   
                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="form-control-2">อำเภอ</label>
                    <div class="col-sm-9 m-b-1">
                    <select name="amphur_id" id="amphur_id" class="form-control amphur" required>
						<?php   
						if(!empty($rs_amphur)){
							foreach(@$rs_amphur as $key => $row_amphur){ 
						?>

                      <?php if (@$row_amphur['amphur_id'] == @$row["amphur_id"]) { ?>
                        <option value="<?php echo @$row_amphur['amphur_id'];?>" selected><?php echo @$row_amphur['amphur_name'];?></option>
                      <?php }else{ ?>
                        <option value="<?php echo @$row_amphur['amphur_id'];?>"><?php echo @$row_amphur['amphur_name'];?></option>
                      <?php } ?>
                      
                        <?php 
							}
						}
						?>
                      </select>
                    </div>
                  </div>
                <?php }else{ ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="form-control-2">อำเภอ</label>
                    <div class="col-sm-9 m-b-1">
                    <select name="amphur_id" id="amphur_id" class="form-control amphur" required>
                        <option value="">เลือกอำเภอ</option>
                      </select>
                    </div>
                  </div>
                <?php } ?>


                  <div class="form-group text-center">
					<button type="button"  onclick="check_form()" class="btn btn-primary min-width-100 m-t-1">ตกลง</button>
                    <a href="?bank_id=<?php echo @$bank_id; ?>"><button class="btn btn-danger min-width-100 m-t-1" type="button">ยกเลิก</button></a>
                  </div>

                  </form>

			</div>


<?php } ?>
	</div>
</div>
<style>
.layout-content{
  margin-left: 0 !important;
}
.layout {
    height: 100%;
    margin: 0;
    max-height: 100%;
    overflow-x: hidden;
    padding-top: 0px !important;
    width: 100%;
}
</style>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_bank_branch.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
    