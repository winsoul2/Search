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
			small{
				display: none !important;
			}
		</style>
		<?php
		$act = @$_GET['act'];
		$id = @$_GET['id'];
		?>

		<?php if (@$act != "add") { ?>
		<h1 style="margin-bottom: 0">ประเภทเงินฝาก</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">	
			<button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_type();"><span class="icon icon-plus-circle"></span> เพิ่มประเภทเงินฝาก</button> 
		</div>
		</div>
		<?php } ?>

		<div class="row gutter-xs">
				<div class="col-xs-12 col-md-12">
	              <div class="panel panel-body">				  
					<div class="bs-example" data-example-id="striped-table">
					 <table class="table table-striped"> 

						 <thead> 
						 	  <tr>
							 	<th>ลำดับ</th>
							   	<th>รหัส</th>
							   	<th class="text-left">ชื่อเงินฝาก</th>
							    <th>อัตราดอกเบี้ย</th>
							    <th>มีผลวันที่</th> 
							    <th></th> 
							  </tr> 
						 </thead>

					      <tbody>
						   <?php  
							if(!empty($rs)){
								foreach(@$rs as $key => $row){
									$start_date = @$rs_detail[$row['type_id']]['start_date'];								
									$condition_interest = (@$rs_detail[$row['type_id']]['condition_interest'] == '1')?@$rs_detail[$row['type_id']]['percent_interest']:@$text_interest[@$rs_detail[$row['type_id']]['condition_interest']];		
							?>
									<tr> 
										<td><?php echo @$i++; ?></th>
										<td><?php echo @$row['type_code']; ?></td>
										<td class="text-left"><?php echo @$row['type_name']; ?></td> 
										<td><?php echo @$condition_interest; ?></td> 
										<td><?php echo (empty($start_date))?'':$this->center_function->ConvertToThaiDate(@$start_date,1,0); ?></td> 
										<td>
											<a href="<?php echo base_url(PROJECTPATH.'/setting_deposit_data/coop_deposit_type_setting_detail?type_id='.@$row["type_id"]); ?>">ดูรายละเอียด</a> |
											<a style="cursor:pointer;" onclick="edit_type('<?php echo @$row['type_id']; ?>','<?php echo @$row['type_code']; ?>','<?php echo @$row['type_name']; ?>','<?php echo @$row['type_prefix']; ?>','<?php echo @$row['format_account_number']; ?>','<?php echo @$row['unique_account']; ?>');">แก้ไข</a> | 
											<span class="text-del del"  onclick="del_type('<?php echo @$row['type_id'] ?>')">ลบ</span>
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
	</div>
</div>

<div id="deposit_type_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title"><span id="title_1">เพิ่มประเภทเงินฝาก</span></h2>
			</div>
			<div class="modal-body">
				<div class="form-group">
				<form id='form1' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_deposit_data/coop_deposit_type_setting_save'); ?>" method="post">	
					<input type="hidden" class="form-control" id="type_id" name="type_id" value="">
					<div class="row">
						<label class="col-sm-4 control-label" for="type_prefix">รหัสนำหน้า</label>
						<div class="col-sm-4">
						  <input id="type_prefix" name="type_prefix" class="form-control m-b-1" type="text" value="" maxlength="20">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 control-label" for="type_code">รหัส</label>
						<div class="col-sm-4">
						  <input id="type_code" name="type_code" class="form-control m-b-1" type="text" value="" maxlength="20" required>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 control-label" for="type_name">ประเภทเงินฝาก</label>
						<div class="col-sm-4">
						  <input id="type_name" name="type_name" class="form-control m-b-1" type="text" value="" required>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 control-label" for="format_account_number">รูปแบบการแสดงผลเลขบัญชีเงินฝาก</label>
						<div class="col-sm-4">
						  <input id="format_account_number" name="format_account_number" class="form-control m-b-1" type="text" value="">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 control-label"></label>
						<label class="col-sm-4 control-label text-left"><span style="color: red;font-size: 10px;">*****ตัวอย่างรูปแบบการแสดงผลเลขบัญชีเงินฝาก  ##-#####</span></label>
					</div>					
					<div class="row">
						<label class="col-sm-4 control-label"></label>
						<div class="col-sm-4 col-small">								
							<input type="checkbox" id="unique_account" name="unique_account" value="1">
							<label class="control-label_2">เปิดบัญชีเงินฝากได้เพียงบัญชีเดียว</label>
						</div>					
					</div>
					<div class="row">
						<div class="col-sm-12 m-t-1" style="text-align:center;">
							<button type="button" class="btn btn-primary" onclick="save_type()">บันทึก</button>&nbsp;&nbsp;&nbsp;
							<button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
						</div>
					</div>		
				</form>					
				</div>				
			</div>
		</div>
	</div>
</div>

<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_deposit_type_setting.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
    