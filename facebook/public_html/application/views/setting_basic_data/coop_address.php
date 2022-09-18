<div class="layout-content">
    <div class="layout-content-body">
<?php
$act = @$_GET['act'];
$province_id = @$_GET['province_id'];
?>

<?php if (@$act != "add") { ?>
<h1 style="margin-bottom: 0">ข้อมูลที่อยู่</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
<?php $this->load->view('breadcrumb'); ?>
</div>
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
<a class="link-line-none" href="?act=add&add_type=district">
<button class="btn btn-primary btn-lg bt-add m-f-1" type="button">
<span class="icon icon-plus-circle"></span>
เพิ่มตำบล
</button>
</a>
<a class="link-line-none" href="?act=add&add_type=amphur">
<button class="btn btn-primary btn-lg bt-add m-f-1" type="button">
<span class="icon icon-plus-circle"></span>
เพิ่มอำเภอ
</button>
</a>
<a class="link-line-none" href="?act=add&add_type=province">
<button class="btn btn-primary btn-lg bt-add m-f-1" type="button">
<span class="icon icon-plus-circle"></span>
เพิ่มจังหวัด
</button>
</a>
</div>
</div>
<?php } ?>

<?php if (@$act != "add") { ?>

<div class="row gutter-xs">

				<div class="col-xs-12 col-md-12">
	              <div class="panel panel-body">
	                		<div class="form-group">
		                    <div class="col-sm-6">
		                      <div class="input-with-icon">
		                        <input class="form-control input-thick pill m-b-2" type="text" placeholder="ค้นหา" name="search_text" id="search_text">
		                        <span class="icon icon-search input-icon"></span>
		                      </div>
		                    </div>
		                  </div>

					<div class="bs-example" data-example-id="striped-table">
					 <table class="table table-striped"> 
						 <thead> 
						 	  <tr>
							 	<th>#</th>
							   	<th>จังหวัด</th>
							   	<th>อำเภอ</th>
							    <th>ตำบล</th>
							    <th>รหัสไปรษณีย์</th>
							    <th></th> 
							  </tr> 
						 </thead>

                <tbody id="result">
                </tbody>

					      <tbody id="table_first">
                  <?php  
					if(!empty($rs)){
						foreach(@$rs as $key => $row){ ?>
					        <tr> 
					        <th scope="row"><?php echo @$i++;?></th>
					        <td><?php echo @$row['province_name']; ?></td> 
					        <td><?php echo @$row['amphur_name']; ?></td> 
					        <td><?php echo @$row['district_name']; ?></td> 
					        <td><?php echo @$row['zipcode']; ?></td> 
							<td><a href="?act=add&province_id=<?php echo @$row["province_id"] ?>&amphur_id=<?php echo @$row['amphur_id']?>&district_id=<?php echo @$row["district_id"] ?>&zipcode_id=<?php echo @$row["id"] ?>">แก้ไข</a> | <span class="text-del del"  onclick="del_coop_address('<?php echo @$row['id'] ?>')">ลบ</span></td> 
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
      <?php  $add_type = @$_GET['add_type']; ?>
			<div class="col-md-6 col-md-offset-3">

				<h1 class="text-center m-t-1 m-b-2">
            <?php 
              if (@$add_type == "province") {
                  echo "เพิ่มจังหวัด";
              }else if (@$add_type == "amphur") {
                  echo "เพิ่มอำเภอ";
              }else if (@$add_type == "district"){
                  echo "เพิ่มตำบล";
              }else{
                  echo "แก้ไข";
              }
             ?>    
        </h1>

			<form data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_address_save'); ?>" method="post">
        <?php if (!empty($province_id)) { ?>
         <input name="type_add"  type="hidden" value="edit" required>
        <?php }else{ ?>
         <input name="type_add"  type="hidden" value="add" required>
        <?php } ?>
                    
              <?php if (@$add_type == "province") {  ?>

              <!-- เพิ่มจังหวัด -->
              <input name="type"  type="hidden" value="<?php echo @$add_type; ?>" >
              
                  <div class="row">
                    <label class="col-sm-3 control-label" for="province_name">ชื่อจังหวัด</label>
                    <div class="col-sm-9">
						<div class="form-group">
						  <input id="form-control-1" name="province_name" class="form-control m-b-1" type="text" value="" required title="กรุณากรอก ชื่อจังหวัด">
						</div>
                    </div>
                  </div>
              <!-- เพิ่มจังหวัด -->

              <?php }elseif (@$add_type == "amphur") { ?>

              <!-- เพิ่มอำเภอ -->
              <input name="type"  type="hidden" value="<?php echo @$add_type; ?>" >
                  <div class="row">
                    <label class="col-sm-3 control-label" for="province_id">จังหวัด</label>
                    <div class="col-sm-9">                     
						<div class="form-group">
						  <select name="province_id" id="province_id" class="form-control province m-b-1" required title="กรุณาเลือก จังหวัด">
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
                  </div>

                  <div class="row">
                    <label class="col-sm-3 control-label" for="amphur_name">ชื่ออำเภอ</label>
                    <div class="col-sm-9">
						<div class="form-group">
						  <input id="amphur_name" name="amphur_name" class="form-control m-b-1" type="text" value="" required title="กรุณากรอก ชื่ออำเภอ">
						</div>
                    </div>
                  </div>
              <!-- เพิ่มอำเภอ -->

              <?php }else if (@$add_type == "district"){ ?>

                    <!-- เพิ่มตำบล -->
                    <input name="type"  type="hidden" value="<?php echo @$add_type; ?>" >

                  <div class="row">
                    <label class="col-sm-3 control-label" for="province_id">จังหวัด</label>
                    <div class="col-sm-9">
						<div class="form-group">
						 <select name="province_id" id="province_id" class="form-control province m-b-1" required title="กรุณาเลือก จังหวัด">
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
                  </div>

                    <?php if (!empty($row["amphur_id"])) { ?>
          
                  <div class="row">
                    <label class="col-sm-3 control-label" for="amphur_id">อำเภอ</label>
                    <div class="col-sm-9">
						<div class="form-group">
						<select name="amphur_id" id="amphur_id" class="form-control amphur m-b-1" required title="กรุณาเลือก อำเภอ">
						   <?php   
							if(!empty($rs_amphur)){
								foreach(@$rs_amphur as $key => $row_amphur){ 
							?>

						  <?php if (@$row_amphur['amphur_id'] == @$row["amphur_id"]) { ?>
							<option value="<?php echo @$row_amphur['amphur_id'];?>" selected><?php echo @$row_amphur['amphur_name'];?></option>
						  <?php }else{ ?>
							<option value="<?php echo @$row_amphur['amphur_id'];?>"><?php echo $row_amphur['amphur_name'];?></option>
						  <?php } ?>
						  
							<?php 
								}
							}
							?>
						  </select>
						</div>
                    </div>
                  </div>
                <?php }else{ ?>
                <div class="row">
                    <label class="col-sm-3 control-label" for="amphur_id">อำเภอ</label>
                    <div class="col-sm-9">
						<div class="form-group">
						<select name="amphur_id" id="amphur_id" class="form-control amphur m-b-1" required title="กรุณาเลือก อำเภอ">
							<option value="">เลือกอำเภอ</option>
						  </select>
						</div>
                    </div>
                  </div>
                <?php } ?>

                <div class="row">
                    <label class="col-sm-3 control-label" for="district_name">ชื่อตำบล</label>
                    <div class="col-sm-9">
						<div class="form-group">
						  <input id="district_name" name="district_name" class="form-control m-b-1" type="text" value="" required title="กรุณากรอก ชื่อตำบล">
						</div>
                    </div>
                  </div>

                  <div class="row">
                    <label class="col-sm-3 control-label" for="zipcode">รหัสไปรษณีย์</label>
                    <div class="col-sm-9">
						<div class="form-group">
						  <input id="zipcode" name="zipcode" class="form-control m-b-1" type="text" value="" required maxlength="5" title="กรุณากรอก รหัสไปรษณีย์">
						</div>
                    </div>
                  </div>
              <!-- เพิ่มตำบล -->

              <?php }else{ ?>

                    <!-- แก้ไข -->
                  <div class="row">
                    <label class="col-sm-3 control-label" for="province_id">จังหวัด</label>
                    <div class="col-sm-9 m-b-1">
						<div class="form-group">
						 <select  name="province_id" id="province_id" class="form-control province m-b-1" required title="กรุณาเลือก จังหวัด">
							<option value="">เลือกจังหวัด</option>
						<?php   
						if(!empty($rs_province)){
							foreach(@$rs_province as $key => $row_province){ ?>

						  <?php if (@$row_province['province_id'] == @$_GET["province_id"]) { ?>
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
                  </div>

                    <?php if (!empty($_GET["amphur_id"])) { ?>

                  <div class="row">
                    <label class="col-sm-3 control-label" for="amphur_id">อำเภอ</label>
                    <div class="col-sm-9">
						<div class="form-group">
						<select  name="amphur_id" id="amphur_id" class="form-control amphur m-b-1" required title="กรุณาเลือก อำเภอ">
						   
						   <?php   
							if(!empty($rs_amphur)){
								foreach(@$rs_amphur as $key => $row_amphur){ 
							?>

						  <?php if (@$row_amphur['amphur_id'] == @$_GET["amphur_id"]) { ?>
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
                  </div>
                <?php }else{ ?>
                <div class="row">
                    <label class="col-sm-3 control-label" for="amphur_id">อำเภอ</label>
                    <div class="col-sm-9">
						<div class="form-group">
						<select name="amphur_id" id="amphur_id" class="form-control amphur m-b-1" required title="กรุณาเลือก อำเภอ">
							<option value="">เลือกอำเภอ</option>
						  </select>
						</div>
                    </div>
                  </div>
                <?php } ?>
                  
                  <div class="row">
                    <label class="col-sm-3 control-label" for="district_name">ชื่อตำบล</label>
                    <div class="col-sm-9">
						<div class="form-group">
						  <input id="district_name" name="district_name" class="form-control m-b-1" type="text" value="<?php echo @$row_district['district_name']; ?>" required title="กรุณากรอก ชื่อตำบล">
						  <input type="hidden" name="district_id" value="<?php echo @$_GET['district_id']; ?>">
						</div>
                    </div>
                  </div>

                  <div class="row">
                    <label class="col-sm-3 control-label" for="zipcode">รหัสไปรษณีย์</label>
                    <div class="col-sm-9">
						<div class="form-group">
						  <input id="zipcode" name="zipcode" class="form-control m-b-1" type="text" value="<?php echo @$row_zipcode["zipcode"];  ?>" required maxlength="5" title="กรุณากรอก รหัสไปรษณีย์">
						  <input type="hidden" name="zipcode_id" value="<?php echo @$_GET['zipcode_id']; ?>">
						</div>
                    </div>
                  </div>
              <!-- แก้ไข -->

              <?php } ?>
                  <div class="form-group text-center m-t-1">
                    <button type="submit" class="btn btn-primary min-width-100">ตกลง</button>
                    <a href="?"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
                  </div>

                  </form>

			</div>


<?php } ?>
	</div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_address.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>

    