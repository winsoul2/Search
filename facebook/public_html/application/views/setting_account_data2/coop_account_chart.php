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
			<button class="btn btn-primary btn-lg bt-add" style="margin-right: 5px;width: 110px !important;" type="button" onclick="window.location.href='<?php echo base_url(PROJECTPATH.'/setting_account_data2/coop_account_chart_preview'); ?>'">
				รายงาน
			</button>
			<button class="btn btn-primary btn-lg bt-add" style="margin-right: 5px;width: 110px !important;" type="button" onclick="window.location.href='<?php echo base_url(PROJECTPATH.'/setting_account_data2/coop_account_chart_excel'); ?>'">
				Export Excel
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
								<th class="font-normal" width="5%">#</th>
								<th class="font-normal" width="10%"> รหัสผังบัญชี </th>
								<th class="font-normal text-center" width="40%"> ผังบัญชี </th>
								<th class="font-normal text-center"> บัญชีคุม </th>
								<th class="font-normal text-center"> ระดับ </th>
								<th class="font-normal text-center"> ประเภท </th>
								<th class="font-normal text-center"> สถานะ </th>
								<th class="font-normal"> จัดการ </th>
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
								<td class="text-center"><?php echo $row['account_parent_id']; ?></td>
								<td class="text-center"><?php echo $row['level']; ?></td>
								<td class="text-center"><?php echo $row['type'] == 1 || $row["type"] == 2 ? "บัญชีคุม" : "บัญชีย่อย"; ?></td>
								<td class="text-center"><?php echo $row["cancel_status"] == 1 ? "ไม่ใช้งาน" : "ใช้งาน"; ?></td>
								<td>
				<?php
					if($row["cancel_status"] == 1) {
				?>
									<a href="#" onclick="use_coop_account_data('<?php echo @$row['account_chart_id']; ?>')" class="text-primary"> เปิดใช้งาน </a> 
				<?php
					} else {
				?>
									<a href="#" onclick="edit_account_chart('<?php echo @$row['account_chart_id']; ?>','<?php echo @$row['account_chart']; ?>','<?php echo $row['type']?>','<?php echo $row['account_parent_id']?>')">แก้ไข</a> |
									<a href="#" onclick="del_coop_account_data('<?php echo @$row['account_chart_id']; ?>')" class="text-del"> ยกเลิก </a> 
				<?php
					}
				?>
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
				<form action="<?php echo base_url(PROJECTPATH.'/setting_account_data2/coop_account_chart_save'); ?>" method="post" id="form1">
					<input type="hidden" name="action_delete" id="action_delete" class="type_input" value="">
					<input id="old_account_chart_id" name="old_account_chart_id" class="type_input" type="hidden" value="">
					<div class="form-group">
						<label class="col-sm-3 control-label">รหัสผังบัญชี</label>
						<div class="col-sm-9">
							<input id="account_chart_id" name="account_chart_id" class="form-control m-b-1 type_input" type="text" value="">
						</div>
						<label class="col-sm-3 control-label">ชื่อผังบัญชี</label>
						<div class="col-sm-9">
							<input id="account_chart" name="account_chart" class="form-control m-b-1 type_input" type="text" value="">
						</div>
					<?php
						if(!empty($account_chart_groups)) {
					?>
						<label class="col-sm-3 control-label">บัญชีคุม</label>
						<div class="col-sm-9">
							<select id="account_parent_id" name="account_parent_id" class="form-control m-b-1">
								<option value="">ไม่เลือก</option>
								<?php foreach($account_chart_groups as $key => $chart){ ?>
									<option value="<?php echo $chart["account_chart_id"]; ?>"><?php echo $chart["account_chart_id"]." : ".$chart["account_chart"];?></option>
								<?php } ?>
							</select>
						</div>
					<?php
						}
					?>
						<label class="col-sm-3 control-label">ประเภท</label>
						<div class="col-sm-9">
							<select id="type" name="type" class="form-control m-b-1">
								<option value="child">บัญชีย่อย</option>
								<option value="parent">บัญชีคุม</option>
							</select>
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
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_account_chart.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>

