<div class="layout-content">
    <div class="layout-content-body">
<?php
$act = @$_GET['act'];
$id  = @$_GET['id'];
?> 
<style>
  input[type=number]::-webkit-inner-spin-button, 
  input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none; 
    margin: 0; 
  }
  th, td {
      text-align: center;
  }
  .modal-dialog-delete {
		margin:0 auto;
		width: 350px;
		margin-top: 8%;
	}
  .modal-header-delete {
		padding:9px 15px;
		border:1px solid #d50000;
		background-color: #d50000;
		color: #fff;
		-webkit-border-top-left-radius: 5px;
		-webkit-border-top-right-radius: 5px;
		-moz-border-radius-topleft: 5px;
		-moz-border-radius-topright: 5px;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
	}
</style>
<?php if ($act != "add") { ?>
	<h1 style="margin-bottom: 0">เกณฑ์การถือหุ้นแรกเข้า</h1>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
	<?php $this->load->view('breadcrumb'); ?>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
	   <!-- <h4 style="font-size:20.5px;margin-top:0px;text-align:right;" >มูลค่าหุ้นปัจจุบัน 50 บาท </h4> -->
	  <a class="link-line-none" href="?act=add">
	  <button class="btn btn-primary btn-lg bt-add" type="button">
	  <span class="icon icon-plus-circle"></span>
	  เพิ่มเกณฑ์การถือหุ้นแรกเข้า
	  </button>
	  </a>
	  <a id="various1" href="#inline1">
			<button type="button" class="btn btn-primary btn-lg bt-add" style="margin-right:10px;">  <span class="icon icon-exchange"></span> เปลี่ยนแปลงมูลค่าหุ้น</button>
		</a> 
	</div>
	</div>
<?php } ?>

	<div style="display: none;">
	<div id="inline1" style="width:550px;height:300px;overflow:auto;">
	  <div class="col-md-12">
		
			<h1 class="text-center m-t-1 m-b-3"> เปลี่ยนแปลงมูลค่าหุ้น </h1>
			<h3 class="text-center m-t-1 m-b-2"> มูลค่าหุ้นปัจจุบัน  <span style="margin-left:30px;"> 
			
			</span> <?php echo @$row_setting['setting_value'] ?> <span style="text-align:right;margin-left:30px;"> บาท </span></h3>

			<form id='form_change' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_share_data/coop_share_rule_change'); ?>" method="post">	

			<input type="hidden" name="change" value="cost"> 
			<input type="hidden" name="setting_id" value="1"> 
					
			<div class="form-group">
				<label class="col-sm-3 control-label" for="form-control-2">มูลค่าหุ้นใหม่ </label>
				<div class="col-sm-9">
				  <input id="share_cost" name="share_cost" class="form-control m-b-1" type="number" value="<?php echo @$row_setting['setting_value']?>" required>
				</div>
			</div>

			<div class="form-group text-center">
			  <button type="button"  onclick="check_form_change()" class="btn btn-primary min-width-100">ตกลง</button>
			  <a href="?"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
			</div>

			</form>
	  </div>
	</div>
	</div>

<?php if ($act != "add") { ?>

<div class="row gutter-xs">
				<div class="col-xs-12 col-md-12">
	              <div class="panel panel-body">
					<div class="bs-example" data-example-id="striped-table">
					<div class="form-group" style="display: -webkit-inline-box;">
						<label class="control-label text-left" for="filter" style="margin-right: 10px;">เลือกประเภทสมาชิก</label>
						<div class="">
						  <select class="form-control m-b-1" id="filter" name="filter" onchange="">
								<option value="">เลือกประเภทสมาชิก</option>
								<?php  
									if(!empty($rs_type)){
										foreach(@$rs_type as $key => $row_type){ 
										$selected = (@$row_type['mem_type_id'] == @$_GET['filter'])?'selected':'';
								?>
										<option value="<?php echo @$row_type['mem_type_id']; ?>" <?php echo $selected;?>><?php echo @$row_type['mem_type_name']; ?></option>
								<?php 
										}
									} 
								?>
							</select>
						</div>
                    </div>
				  
					 <table class="table table-striped"> 
						 <thead> 
						 	  <tr>
							 	  <th>#</th>
								  <th>ประเภทสมาชิก</th>
								  <th>เงินเดือนมากกว่า</th>
								  <!-- <th>หุ้นแรกเข้า</th> -->
								  <th>หุ้นรายเดือน</th>
								  <th>คิดเป็น(บาท)</th>
							      <th></th> 
							  </tr> 
						 </thead>
					    <tbody>
                  
					<?php  
						if(!empty($rs)){
							foreach(@$rs as $key => $row){ 
								$amount = @$row['share_salary']*@$row_setting['setting_value'];
					?>
					        <tr> 
								<th scope="row"><?php echo $i++; ?></th>
								<td><?php echo @$row['mem_type_name']; ?></td> 
								<td><?php echo number_format(@$row['salary_rule']); ?></td> 
								<!-- <td><?php echo @$row['share_first']; ?></td>  -->
								<td><?php echo number_format(@$row['share_salary']); ?></td> 
								<td><?php echo number_format(@$amount); ?></td> 
								<td>
								  <a href="?act=add&id=<?php echo @$row["share_rule_id"] ?>">แก้ไข</a> |
								  <span class="text-del del"  onclick="del_coop_share_data('<?php echo @$row['share_rule_id'] ?>')">ลบ</span>
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
              <?php echo $paging ?>
	         </div>
</div>

<?php } else { ?>

			<div class="col-md-8 col-md-offset-2">

				<h1 class="text-center m-t-1 m-b-2"> <?php echo  (!empty($id)) ? "แก้ไขเกณฑ์การถือหุ้นแรกเข้า" : "เพิ่มเกณฑ์การถือหุ้นแรกเข้า" ; ?></h1>

				<form id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_share_data/coop_share_rule_save'); ?>" method="post">	
					<?php if (!empty($id)) { ?>
					<input name="type_add"  type="hidden" value="edit" required>
					<input name="id"  type="hidden" value="<?php echo $id; ?>" required>
					<?php }else{ ?>
					<input name="type_add"  type="hidden" value="add" required>
					<?php } ?>		
					<div class="row">
						<label class="col-sm-4 control-label text-right" for="mem_type_id">เลือกประเภทสมาชิก</label>
						<div class="col-sm-4">
							<select class="form-control m-b-1" id="mem_type_id" name="mem_type_id" onchange="" required>
								<option value="">เลือกประเภทสมาชิก</option>
								<?php  
									if(!empty($rs_type)){
										foreach(@$rs_type as $key => $row_type){ 
										$selected = (@$row_type['mem_type_id'] == @$row['mem_type_id'])?'selected':'';
								?>
										<option value="<?php echo @$row_type['mem_type_id']; ?>" <?php echo $selected;?>><?php echo @$row_type['mem_type_name']; ?></option>
								<?php 
										}
									} 
								?>
							</select>
						</div>
					</div>
					
					<div class="row">
						<label class="col-sm-4 control-label text-right" for="salary_rule">เงินเดือนมากกว่า</label>
						<div class="col-sm-4">
						<input id="salary_rule" name="salary_rule" class="form-control m-b-1" type="number" value="<?php echo @$row['salary_rule']; ?>" required>
						</div>
					</div>

					<!-- <div class="form-group">
					<label class="col-sm-4 control-label" for="form-control-2">หุ้นแรกเข้า</label>
					<div class="col-sm-8">
					<input id="form-control-2" name="share_first" class="form-control m-b-1" type="number" value="<?php echo @$row['share_first']; ?>" required>
					</div>
					</div> -->

					<div class="row">
						<label class="col-sm-4 control-label text-right" for="form-control-2">หุ้นรายเดือน</label>
						<div class="col-sm-4">
						<input id="share_salary" name="share_salary" class="form-control m-b-1" type="number" value="<?php echo @$row['share_salary']; ?>" required>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 control-label text-right" for="form-control-2"></label>
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
    'src' => PROJECTJSPATH.'assets/js/coop_share_rule.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>


