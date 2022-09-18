<div class="layout-content">
    <div class="layout-content-body">
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
			.modal-header-confirmSave {
				padding:9px 15px;
				border:1px solid #0288d1;
				background-color: #0288d1;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			.control-label{
				text-align:right;
				padding-top:5px;
			}
		</style>
		<h1 style="margin-bottom: 0">รายการผังบัญชี</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_account_chart()">
				<span class="icon icon-plus-circle"></span>
				เพิ่มรายการ
			</button>
		</div>
		</div>
		<div class="row gutter-xs">
		  <div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<div class="bs-example" data-example-id="striped-table">
					<table class="table table-striped"> 
					   <thead> 
						 <tr>
							<th class = "font-normal" width="5%">#</th>
							<th class = "font-normal" width="30%"> รหัสผังบัญชี </th>
							<th class = "font-normal text-left"> ผังบัญชี </th>
							<th class = "font-normal"> จัดการ </th>
						</tr> 
					   </thead>
					   <tbody>
				<?php  
					if(!empty($rs)){
						foreach(@$rs as $key => $row){ 
				?>
							<tr> 
							  <td scope="row"><?php echo $i++; ?></td>
							  <td><?php echo @$row['account_chart_id']; ?></td> 
							  <td class="text-left"><?php echo @$row['account_chart']; ?></td> 
							  <td>
							  <?php if(@$row['is_fix']!='1'){ ?>
								  <a href="#" onclick="edit_account_chart('<?php echo @$row['account_chart_id']; ?>','<?php echo @$row['account_chart']; ?>')">แก้ไข</a> |
								  <a href="#" onclick="del_coop_account_data('<?php echo @$row['account_chart_id']; ?>')" class="text-del"> ลบ </a> 
							  <?php } ?>
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

	<div id="add_account_chart" tabindex="-1" role="dialog" class="modal fade">
		<div class="modal-dialog modal-dialog-account">
			<div class="modal-content">
				<div class="modal-header modal-header-confirmSave">
					<h2 class="modal-title" id="modal_title">เพิ่มผังบัญชี</h2>
				</div>
				<div class="modal-body">
					<form action="<?php echo base_url(PROJECTPATH.'/setting_account_data/coop_account_chart_save'); ?>" method="post" id="form1">
					<input type="hidden" name="action_delete" id="action_delete" class="type_input" value="">
					<input id="old_account_chart_id" name="old_account_chart_id" class="type_input" type="hidden" value="">
					<div class="form-group">
						<label class="col-sm-3 control-label">รหัสผังบัญชี</label>
						<div class="col-sm-9">
							
							<input id="account_chart_id" name="account_chart_id" class="form-control m-b-1 type_input" type="text" value="">
						</div>

						<label class="col-sm-3 control-label">ผังบัญชี</label>
						<div class="col-sm-9">
							<input id="account_chart" name="account_chart" class="form-control m-b-1 type_input" type="text" value="">
						</div>
					</div>

					<div class="form-group text-center">
						<button type="button" class="btn btn-primary min-width-100" onclick="form_submit()">ตกลง</button>
						<button class="btn btn-danger min-width-100" type="button" onclick="close_modal('add_account_chart')">ยกเลิก</button>
					</div>

					</form>
				</div>
			</div>
		</div>
	</div>
	
	
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_account_chart.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>