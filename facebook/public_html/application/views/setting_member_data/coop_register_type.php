<div class="layout-content">
    <div class="layout-content-body">
<?php
$act = @$_GET['act'];
$id = @$_GET['id'];
?>   
<style>
	.control-label_2{
		padding-top:7px;
	}
	
	.col-small{
		display: -webkit-inline-box;
	}
	.col-small label,.form-group {
		margin-right: 5px;
	}
</style>
<?php if ($act != "add") { ?>
	<h1 style="margin-bottom: 0">ประเภทการสมัคร</h1>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
	<?php $this->load->view('breadcrumb'); ?>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
	<a class="link-line-none" href="?act=add">
	<button class="btn btn-primary btn-lg bt-add" type="button">
	<span class="icon icon-plus-circle"></span>
	เพิ่มประเภท
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
							 	<th style="width:40px;">#</th>
							    <th style="width:40%;">ประเภทการสมัคร</th>							    
							    <th class="text-center" style="width:20%;">อายุไม่เกิน</th>
								<th class="text-right" style="width:20%;">ค่าธรรมเนียม</th>
								<th></th> 
							    <th style="width:100px;"></th> 
							  </tr> 
						 </thead>

					        <tbody>

                   <?php  
						if(!empty($rs)){
							foreach(@$rs as $key => $row){ 
					?>
					        <tr> 
								<th scope="row"><?php echo $i++; ?></th>
								<td><?php echo @$row['apply_type_name']; ?></td> 
								<td class="text-center"><?php echo (@$row['type_age']=='2')?@$row['age_limit']:'ไม่จำกัดอายุ'; ?></td> 
								<td class="text-right"><?php echo number_format(@$row['fee'],2); ?></td> 								
								<td>&nbsp;</td>
								<td>
									<a href="?act=add&id=<?php echo @$row["apply_type_id"] ?>">แก้ไข</a> |
									<span class="text-del del"  onclick="del_coop_member_data('<?php echo @$row['apply_type_id'] ?>')">ลบ</span>
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
			<div class="col-md-8 col-md-offset-2">
				<h1 class="text-center m-t-1 m-b-2"> <?php echo  (!empty($id)) ? "แก้ไขประเภท" : "เพิ่มประเภท" ; ?></h1>
				<form id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_member_data/coop_register_type_save'); ?>" method="post">	
				<?php if (!empty($id)) { ?>
			    <input name="type_add"  type="hidden" value="edit" required>
			    <input name="id"  type="hidden" value="<?php echo @$id; ?>" required>
			  <?php }else{ ?>
			    <input name="type_add"  type="hidden" value="add" required>
			  <?php } ?>
				<div class="row">
                    <label class="col-sm-4 control-label text-right" for="form-control-2">ประเภทการสมัคร</label>
                    <div class="col-sm-4">
                      <input id="apply_type_name" name="apply_type_name" class="form-control m-b-1" type="text" value="<?php echo @$row['apply_type_name']; ?>" required>
                    </div>
                </div>
				<div class="row">
					<label class="col-sm-4 control-label text-right" for="form-control-2">ค่าธรรมเนียม</label>
					<div class="col-sm-4">
					  <input id="fee" name="fee" class="form-control m-b-1" type="number" value="<?php echo @$row['fee']; ?>" required>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 control-label text-right" for="form-control-2">อายุผู้สมัคร</label>
					<!--<label class="col-sm-6  control-label"><input type="radio" id="type_age_1" name="type_age" value="1" onchange="change_type_age_radio()" <?php echo (@$row['type_age']=='1' || empty($row['type_age']))?'checked':''; ?>> <span>ไม่จำกัดอายุ</span></label>	-->				
					<div class="col-sm-6">
						<input type="radio" id="type_age_1" name="type_age" value="1" onchange="change_type_age_radio()" <?php echo (@$row['type_age']=='1' || empty($row['type_age']))?'checked':''; ?>>
						<label class="control-label"> ไม่จำกัดอายุ</label>					
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 control-label text-right" for="form-control-2"></label>
					<div class="col-sm-8 col-small">
						<!--<label class="control-label" style="white-space: nowrap;"><input type="radio" id="type_age_2" name="type_age" value="2" onchange="change_type_age_radio()" <?php echo @$row['type_age']=='2'?'checked':''; ?>> จำกัดอายุ ไม่เกิน</label>	-->				
						<input type="radio" id="type_age_2" name="type_age" value="2" onchange="change_type_age_radio()" <?php echo @$row['type_age']=='2'?'checked':''; ?>>
						<label class="control-label" style="white-space: nowrap;"> จำกัดอายุ ไม่เกิน</label>					
						<div>
							<div class="form-group">
								<input type="number" class="form-control" name="age_limit" id="age_limit" value="<?php echo @$row['age_limit']; ?>" style="width:50px;" required title="กรุณากรอก อายุผู้สมัคร">
							</div>
						</div>
						<label class="control-label">ปี</label>	
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 control-label text-right" for="form-control-2">ชำระค่าหุ้น</label>
					<!--<label class="col-sm-8 control-label"><input type="radio" id="type_share_1" name="type_share" value="1" onchange="change_type_share_radio()" <?php echo (@$row['type_share']=='1' || empty($row['type_share']))?'checked':''; ?>> ชำระทุกเดือน</label>	-->				
					<div class="col-sm-6">
						<input type="radio" id="type_share_1" name="type_share" value="1" onchange="change_type_share_radio()" <?php echo (@$row['type_share']=='1' || empty($row['type_share']))?'checked':''; ?>>
						<label class="control-label"> ชำระทุกเดือน</label>					
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 control-label text-right" for="form-control-2"></label>
					<!--<label class="col-sm-8 control-label"><input type="radio" id="type_share_2" name="type_share" value="2" onchange="change_type_share_radio()" <?php echo @$row['type_share']=='2'?'checked':''; ?>> ชำระครั้งเดียว ไม่เรียกเก็บรายเดือน </label>-->					
					<div class="col-sm-8">
						<input type="radio" id="type_share_2" name="type_share" value="2" onchange="change_type_share_radio()" <?php echo @$row['type_share']=='2'?'checked':''; ?>>
						<label class="control-label"> ชำระครั้งเดียว ไม่เรียกเก็บรายเดือน </label>					
					</div>
				</div>
				<div class="row">	
					<label class="col-sm-4 control-label text-right" for="form-control-2"></label>
					<div class="col-sm-8 col-small">
						<label class="control-label" style="white-space: nowrap;">ขั้นต่ำ</label>
						<div class="">
							<div class="form-group">
								<input type="number" class="form-control" name="amount_min" id="amount_min" value="<?php echo @$row['amount_min']; ?>" style="width:100px;" required title="กรุณากรอก ไม่เรียกเก็บรายเดือน ขั้นต่ำ">
							</div>
						</div>					
						<label class="control-label">บาท</label>
						<label class="control-label">สูงสุด</label>
						<div class="">
							<div class="form-group">
								<input type="number" class="form-control" name="amount_max" id="amount_max" value="<?php echo @$row['amount_max']; ?>" style="width:100px;" required title="กรุณากรอก ไม่เรียกเก็บรายเดือน สูงสุด">
							</div>
						</div>
						<label class="control-label">บาท</label>					
					</div>
				</div>
				
				<div class="row">
					<label class="col-sm-4 control-label text-right" for="form-control-2"></label>
					<div class="col-sm-8 m-t-2">
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
    'src' => PROJECTJSPATH.'assets/js/coop_register_type.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>