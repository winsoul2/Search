<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.right {
				text-align: right;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			label{
				padding-top:7px;
			}
		</style>

		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">ตั้งค่าเหตุผลการกู้เงิน</h1>
		
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
						<form id='form1' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_credit_data/coop_loan_reason_save'); ?>" method="post">	
						<h3 ></h3>
						<?php $type_id = '3'; ?>
							<div class="g24-col-sm-24">
								<div class="form-group g24-col-sm-21">
									<label class="g24-col-sm-8 control-label right"> เหตุผลการกู้เงิน </label>
									<div class="g24-col-sm-7">
										<input type="text" class="form-control" name="loan_reason" id="loan_reason" value="">
										<input type="hidden" name="loan_reason_id" id="loan_reason_id" value="">
									</div>
									<div class="g24-col-sm-4">
										<button class="btn btn-primary" type="button" onclick="submit_form()"><span class="icon icon-save"></span> บันทึก</button>
									</div>
								</div>
							</div>
						</form>
						<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_1">
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th>#</th>
										<th>เหตุผลการกู้เงิน</th>
										<th>จัดการ</th>
									</tr> 
								</thead>
								<tbody>
								<?php  
									if(!empty($rs)){
										foreach(@$rs as $key => $row){ 
								?>
									<tr> 
										<td><?php echo $i++; ?></td>
										<td style="text-align:left;"><?php echo @$row['loan_reason']; ?></td>
										<td>
											<a title="แก้ไข" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="edit_loan_reason('<?php echo @$row['loan_reason_id']?>','<?php echo @$row['loan_reason']; ?>')"><span style="cursor: pointer;" class="icon icon-edit"></span>
											</a>
											|
											<a title="ลบ" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="del_coop_credit_data('<?php echo @$row['loan_reason_id']?>')"><span style="cursor: pointer;" class="icon icon-trash-o"></span>
											</a>
										</td> 
									</tr>
								<?php 
										}
									} 
								?>
								</tbody> 
							</table> 
						</div>
						<?php echo @$paging ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_loan_reason.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
