<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.modal-header-confirmSave {
				padding:9px 15px;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}
			.modal-header-alert {
				padding:9px 15px;
				border:1px solid #FF0033;
				background-color: #FF0033;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}
			.center {
				text-align: center;
			}
			.right {
				text-align: right;
			}
			.modal-dialog-data {
				margin:auto;
				margin-top:7%;
			}
			label{
				padding-top:7px;
			}
			.odd {
				background-color: #eee;
			}
		</style>

		<style type="text/css">
			.form-group{
				margin-bottom: 5px;
			}
		</style>
		<h1 style="margin-bottom: 0">ประนอมหนี้</h1>
		<div class="g24-col-sm-24 padding-l-r-0">
			<div class="g24-col-xs-12 g24-col-sm-14 g24-col-md-16 g24-col-lg-18 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="g24-col-xs-6 g24-col-sm-5 g24-col-md-4 g24-col-lg-3 padding-l-r-0 text-center">
				<a class="link-line-none text-center" href="<?php echo base_url(PROJECTPATH.'/compromise/guarantees_process'); ?>">
					<button style="width:100% !important;" class="btn btn-primary btn-lg bt-add" type="button">
						ประนอมหนี้ผู้ค้ำ
					</button>
				</a>
			</div>
			<div class="g24-col-xs-6 g24-col-sm-5 g24-col-md-4 g24-col-lg-3 padding-l-r-0 text-center">
				<a class="link-line-none text-center" href="<?php echo base_url(PROJECTPATH.'/compromise/loaner_process'); ?>">
					<button style="width:100% !important;" class="btn btn-primary btn-lg bt-add" type="button">
						ประนอมหนี้ผู้กู้
					</button>
				</a>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<br>
					<table class="table table-bordered table-center">
						<thead> 
							<tr class="bg-primary">
								<th>วันที่ประนอมหนี้</th>
								<th>เลขที่สัญญาเดิม</th>
								<th>เลขที่สัญญาใหม่</th>
								<th>รหัสสมาชิก</th>
								<th>ชื่อนามสกุล</th>
								<th>เงินต้นคงเหลือ</th>
								<th>ผู้ทำรายการ</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						<?php
							if (!empty($datas)) {
								foreach($datas as $key_data => $data) {
									$detail_count = count($data["details"]);
									foreach($data["details"] as $key => $detail) {
						?>
							<tr class="<?php echo $key_data%2 == 0 ? 'odd' : ''; ?>">
						<?php
							if($key == 0) {
						?>	
								<td rowspan="<?php echo $detail_count; ?>"><?php echo $this->center_function->ConvertToThaiDate($data['created_at']);?></td>
								<td rowspan="<?php echo $detail_count; ?>">
									<?php echo $data["contract_number"];?>
								</td>
						<?php
							}
						?>
								<td>
									<a title="แก้ไข" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo PROJECTPATH."/loan/index?member_id=".$detail['member_id']."&loan_id=".$detail['id']."&loan_type=".$detail['loan_type']; ?>"><?php echo $detail["contract_number"];?></a>
								</td>
								<td><?php echo $detail["member_id"]?></td>
								<td><?php echo $detail["prename_full"].$detail["firstname_th"]." ".$detail["lastname_th"];?></td>
								<td><?php echo number_format($detail["loan_amount_balance"],2);?></td>
								<td><?php echo $data["user_name"];?></td>
						<?php
							if($key == 0) {
						?>	
								<td rowspan="<?php echo $detail_count;?>" >
						<?php
								if(($detail["type"] == 1 || $detail["type"] == 5 || $detail["type"] == 6) && $detail["status"] == 1) {
						?>
								<a class="link-line-none" href="<?php echo base_url(PROJECTPATH.'/compromise/return_process?compromise_id='.$data["compromise_id"]); ?>">
									<button style="width:35px;" class="btn btn-primary btn-after-input process_btn" data-loan-id="<?php echo $data["loan_id"];?>" type="button"><span>R</span></button>
								</a>
						<?php
								}
						?>
								</td>
						<?php
							}
						?>
							</tr>
						<?php
									}
								}
							} else {
						?>
							<tr><td colspan="9"><br><br>ไม่พบข้อมูล<br><br></td></tr>
						<?php
							}
						?>
						</tbody> 
					</table>
					<div class="text-center">
						<?php echo $paging;?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>