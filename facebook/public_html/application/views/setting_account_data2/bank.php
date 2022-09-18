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
		<h1 style="margin-bottom: 0">บัญชีธนาคาร</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_bank_account()">
				<span class="icon icon-plus-circle"></span>
				เพิ่ม
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
								<th class="font-normal text-center"> ธนาคาร </th>
								<th class="font-normal text-center"> เลขที่บัญชี </th>
								<th class="font-normal text-center"> ผังบัญชี </th>
								<th class="font-normal"></th>
							</tr>
					   </thead>
					   <tbody>
				<?php
					if(!empty($datas)){
                        $i = 1;
						foreach(@$datas as $key => $data){
				?>
							<tr>
								<td scope="row"><?php echo $i++; ?></td>
								<td class="text-left"><?php echo $data['account_bank_name']; ?></td>
								<td class="text-center"><?php echo $data['account_bank_number']; ?></td>
								<td class="text-center"><?php echo $data['account_chart_id']; ?></td>
								<td>
									<a href="#" id="edit_<?php echo $data['account_bank_id'];?>" data_id="<?php echo $data['account_bank_id'];?>" class="edit_btn">แก้ไข</a> |
									<a href="#" id="del_<?php echo $data['account_bank_id'];?>" data_id="<?php echo $data['account_bank_id'];?>" class="del_btn text-danger"> ยกเลิก </a>
								</td>
							</tr>
				<?php
						}
					} else {
                ?>
                            <tr>
                                <td class="font-normal text-center" colspan="4">ไม่พบข้อมูล</td>
                            </tr>
                <?php
                    }
				?>
						</tbody>
					  </table>
					</div>
				</div>
			 </div>
		</div>
  </div>
</div>
<div id="add_account_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-account">
		<div class="modal-content">
			<div class="modal-header modal-header-confirmSave">
				<h2 class="modal-title" id="modal_title">เพิ่มผังบัญชี</h2>
			</div>
			<div class="modal-body">
				<form action="<?php echo base_url(PROJECTPATH.'/setting_account_data2/coop_account_bank_save'); ?>" method="post" id="form1">
					<input type="hidden" name="id" id="id" value="">
					<div class="form-group">
						<label class="col-sm-3 control-label">ธนาคาร</label>
						<div class="col-sm-9">
							<select id="bank_code" name="bank_code" class="form-control m-b-1">
								<?php foreach($banks as $key => $bank){ ?>
									<option value="<?php echo $bank["bank_code"]; ?>"><?php echo $bank["bank_name"];?></option>
								<?php } ?>
							</select>
						</div>
						<label class="col-sm-3 control-label">เลขที่บัญชี</label>
						<div class="col-sm-9">
                            <input id="account_bank_number" name="account_bank_number" class="form-control m-b-1 type_input" type="text" value="">
						</div>
                        <label class="col-sm-3 control-label">ผังบัญชี</label>
                        <div class="col-sm-9">
                            <select id="account_chart_id" name="account_chart_id" class="form-control m-b-1 js-data-example-ajax">
                                <option value="">เลือกรหัสผังบัญชี</option>
                                <?php 
                                    foreach($charts as $key => $row) {
                                ?>
                                <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
					</div>
					<div class="form-group text-center">
						<button type="button" class="btn btn-primary min-width-100" id="submit_btn">ตกลง</button>
						<button class="btn btn-danger min-width-100" type="button" id="modal_cancel_btn">ยกเลิก</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_account_bank.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
