<div class="layout-content">
	<div class="layout-content-body">
		<style>
			.form-group {
				margin-bottom: 1em;
			}

			.border1 {
				border: solid 1px #ccc;
				padding: 0 15px;
			}

			.mem_pic {
				margin-top: -1em;
				float: right;
				width: 150px;
			}

			.mem_pic img {
				width: 100%;
				border: solid 1px #ccc;
			}

			.mem_pic button {
				display: block;
				width: 100%;
			}

			.modal-backdrop.in {
				opacity: 0;
			}

			.modal-backdrop {
				position: relative;
				top: 0;
				right: 0;
				bottom: 0;
				left: 0;
				z-index: 1040;
				background-color: #000;
			}

			.font-normal {
				font-weight: normal;
			}

			.font-normal2 {
				font-weight: bold;
				font-size: 20px;
			}

			.font-normal3 {
				font-weight: bold;
				font-size: 16px;
			}

			input::-webkit-outer-spin-button,
			input::-webkit-inner-spin-button {
				-webkit-appearance: none;
				margin: 0;
			}

			.btn_deposit {
				margin-right: 5px;
			}

			.alert-success {
				background-color: #DBF6D3;
				border-color: #AED4A5;
				color: #569745;
				font-size: 14px;
			}

			.alert-danger {
				background-color: #F2DEDE;
				border-color: #e0b1b8;
				color: #B94A48;
			}

			.alert {
				border-radius: 0;
				-webkit-border-radius: 0;
				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.11);
				display: table;
				width: 100%;
			}

			.modal-header-deposit {
				padding: 9px 15px;
				border: 1px solid #0288d1;
				background-color: #0288d1;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}

			.modal-header-withdrawal {
				padding: 9px 15px;
				border: 1px solid #d50000;
				background-color: #d50000;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}

			.modal-dialog-account {
				margin: 0 auto;
				margin-top: 10%;
			}

			.modal-dialog-print {
				margin: 0 auto;
				margin-top: 15%;
				width: 350px;
			}

			.center {
				text-align: center;
			}

			th, td {
				text-align: center;
			}

			a {
				text-decoration: none !important;
			}

			a:hover {
				color: #075580;
			}

			a:active {
				color: #757575;
			}

			.bg-table {
				background-color: #0288d1;
				border-color: #0288d1;
				color: #fff;
			}

			.modal-dialog-delete {
				margin: 0 auto;
				width: 350px;
				margin-top: 8%;
			}

			.modal-dialog-add {
				margin: 0 auto;
				width: 60%;
				margin-top: 5%;
			}

			#add_account {
				z-index: 5100 !important;
			}

			#search_member_add_modal {
				z-index: 5200 !important;
			}

			@media (min-width: 768px) {
				.modal-dialog {
					width: 700px;
				}
			}
		</style>
		<h1 style="margin-bottom: 0">จัดการบัญชี</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 padding-l-r-0">
				<a class="link-line-none" onclick="add_account('','')">
					<button class="btn btn-primary btn-lg bt-add" type="button">
						<span class="icon icon-plus-circle"></span>
						เปิดบัญชีใหม่
					</button>
				</a>
				<!--a class="link-line-none" href="?act=account">
                    <button class="btn btn-primary btn-lg bt-add" type="button" style="margin-right:20px;">
                    <i class="fa fa-money" aria-hidden="true"></i>
                        บัญชีเงินฝาก
                    </button>
                </a-->
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">

					<div class="row">
						<div class="col-sm-8">
							<!--<div class="input-with-icon">
							<input class="form-control input-thick pill m-b-2" type="text" placeholder="ค้นหา" name="search_text" id="search_text">
							<span class="icon icon-search input-icon"></span>
							</div>
							-->
							<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select id="search_list" name="search_list" class="form-control m-b-1">
										<option value="">เลือกรูปแบบค้นหา</option>
										<option value="member_id">รหัสสมาชิก</option>
										<option value="employee_id">รหัสพนักงาน</option>
										<option value="id_card">หมายเลขบัตรประชาชน</option>
										<option value="firstname_th">ชื่อสมาชิก</option>
										<option value="lastname_th">นามสกุล</option>
										<option value="account_id">หมายเลขบัญชี</option>
									</select>
								</div>
							</div>

							<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
							<div class="col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="search_text" name="search_text" class="form-control m-b-1"
											   type="text" value="<?php echo @$data['id_card']; ?>">
										<span class="input-group-btn">
											<button type="button" onclick="check_search();"
													class="btn btn-info btn-search"><span
													class="icon icon-search"></span></button>
										</span>
									</div>
								</div>
							</div>
						</div>

						<div class="col-sm-4 text-right">
							<p>จำนวนบัญชีเงินฝากทั้งหมด <?php echo number_format($num_rows); ?> บัญชี</p>
						</div>
					</div>

					<div class="bs-example" data-example-id="striped-table">
						<div id="tb_wrap">
							<table class="table table-bordered table-striped table-center">
								<thead>
								<tr class='bg-primary'>
									<th>ลำดับ</th>
									<th>ประเภทบัญชี</th>
									<th>เลขบัญชี</th>
									<th>ชื่อบัญชี</th>
									<th>รหัสสมาชิก</th>
									<th>ชื่อ - นามสกุล</th>
									<th>วันที่เปิดบัญชี</th>
									<th>สถานะ</th>
									<th>จัดการ</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$account_status = array('0' => 'ใช้งาน', '1' => 'ไม่ใช้งาน');
								foreach ($data as $key => $row) { ?>
									<tr>
										<td><?php echo $i++; ?></td>
										<td><?php echo $row['type_code']; ?></td>
										<td>
											<a href="<?php echo base_url(PROJECTPATH . '/save_money/account_detail?account_id=' . $row['account_id']); ?>">
												<?php echo $this->center_function->format_account_number($row['account_id']); ?>
											</a></td>
										<td style="text-align:left"><?php echo $row['account_name']; ?></td>
										<td><?php echo $row['mem_id']; ?></td>
										<td style="text-align:left"><?php echo $row['member_name']; ?></td>
										<td><?php echo $this->center_function->ConvertToThaiDate($row['created']); ?></td>
										<td><?php echo $account_status[$row['account_status']]; ?></td>
										<td>
											<?php if ($row['account_status'] == '0') { ?>
												<a onclick="add_account('<?php echo @$row["account_id"]; ?>','<?php echo $row['mem_id']; ?>')"
												   style="cursor:pointer;"> แก้ไข </a> |
												<a class="text-del"
												   onclick="close_account('<?php echo @$row["account_id"]; ?>', '<?php echo @$row["mem_id"]; ?>')">ปิดบัญชี</a>
											<?php 
												}else{
													$receipt_refund_id = @$arr_receipt_refund[$row["account_id"]];
													if(@$receipt_refund_id != ''){
											?>
													<a href="<?php echo base_url(PROJECTPATH.'/receipt/receipt_refund/'.$receipt_refund_id); ?>"  target="_blank" style="cursor:pointer;"> ใบเสร็จคืนเงิน </a>
											<?php
													}
												}
											?>
										</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div id="page_wrap">
					<?php echo $paging ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="add_account" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-add">
		<div class="modal-content">
			<div class="modal-header modal-header-info">
				<h2 class="modal-title">บัญชีเงินฝาก</h2>
			</div>
			<div class="modal-body" id="add_account_space">

			</div>
		</div>
	</div>
</div>
<div class="modal modal_in_modal fade" id="search_member_add_modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">ข้อมูลสมาชิก</h4>
			</div>
			<div class="modal-body">
				<div class="input-with-icon">
					<div class="row">
						<div class="col">
							<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select id="member_search_list" name="member_search_list"
											class="form-control m-b-1">
										<option value="">เลือกรูปแบบค้นหา</option>
										<option value="member_id">รหัสสมาชิก</option>
										<option value="id_card">หมายเลขบัตรประชาชน</option>
										<option value="firstname_th">ชื่อสมาชิก</option>
										<option value="lastname_th">นามสกุล</option>
									</select>
								</div>
							</div>
							<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
							<div class="col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="member_search_text" name="member_search_text"
											   class="form-control m-b-1" type="text"
											   value="<?php echo @$data['id_card']; ?>">
										<span class="input-group-btn">
									<button type="button" id="member_search" class="btn btn-info btn-search"><span
											class="icon icon-search"></span></button>
								</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="bs-example" data-example-id="striped-table">
					<table class="table table-striped">
						<tbody id="result_add">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
			</div>
		</div>
	</div>
</div>
<div id="close_account" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-add" style="width:80% !important;">
		<div class="modal-content">
			<div class="modal-header modal-header-info">
				<h2 class="modal-title">ปิดบัญชี</h2>
			</div>
			<div class="modal-body">
				<div class="g24-col-sm-24 ">
					<form data-toggle="validator" novalidate="novalidate"
						  action="<?php echo base_url(PROJECTPATH . '/save_money/close_account'); ?>" method="post">
						<input type="hidden" name="account_id" id="close_account_id">
						<!-- <div class="form-group">
							<?php foreach ($data as $key => $row) { ?>
								<?php if ($key == ''){?>
							<div class="g24-col-sm-24 m-b-1" style="text-align:center !important;">
								<label class="control-label">
										<?php echo substr($row['account_id'], 0, 3) . "-" . substr($row['account_id'], 3, 2) . "-" . substr($row['account_id'], 5, 5) . "-" . substr($row['account_id'], 10); ?>
									</label>
							</div>
							<?php } ?>
							<?php } ?>
						</div> -->
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> รหัสสมาชิก </label>
								<div class="g24-col-sm-2">
									<p id="close_member_id" class="form-control-static"></p>
								</div>
								<label class="control-label g24-col-sm-2 m-b-1"> เลขบัญชี </label>
								<div class="g24-col-sm-4">
									<p id="close_account_id_c" class="form-control-static"></p>
								</div>
								<label class="control-label g24-col-sm-2 m-b-1"> ชื่อบัญชี </label>
								<div class="g24-col-sm-6">
									<p id="close_account_name" class="form-control-static"></p>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="g24-col-sm-24 m-b-1" style="text-align:center !important;">
								<label class="control-label"> จำนวนเงินที่จะได้รับ </label>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> ยอดปัจจุบัน </label>
								<div class="g24-col-sm-11">
									<input type="text" class="form-control" name="close_account_principal"
										   id="close_account_principal" value="" readonly>
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1">&nbsp;</label>
								<div class="g24-col-sm-11">
									<input type="checkbox" name="is_ignore_interest_return" id="is_ignore_interest_return" value="1"> คิดดอกเบี้ยเต็ม ไม่หักส่วนสหกรณ์
									<input type="hidden" id="tmp_close_account_interest_return" value="">
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> ดอกเบี้ย </label>
								<div class="g24-col-sm-11">
									<input type="text" class="form-control cal_close_account_total" name="close_account_interest" id="close_account_interest" value="">
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> คืนดอกเบี้ย </label>
								<div class="g24-col-sm-11">
									<input type="text" class="form-control cal_close_account_total" name="close_account_interest_return" id="close_account_interest_return" value="">
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> เสียภาษี </label>
								<div class="g24-col-sm-11">
									<input type="text" class="form-control cal_close_account_total" name="close_account_tax_return" id="close_account_tax_return" value="">
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> คืนเงินต้นเงินฝาก </label>
								<div class="g24-col-sm-11">
									<input type="text" class="form-control cal_close_account_total" name="close_account_principal_return" id="close_account_principal_return" value="">
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> ยอดปิดบัญชี </label>
								<div class="g24-col-sm-11">
									<p id="close_account_total" class="form-control-static"></p>
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> เลือกการรับเงิน </label>
								<div class="g24-col-sm-11">
									<input type="radio" name="pay_type" value="0" onclick="set_bank('');set_branch_code('');show('');" checked> เงินสด 
									<input type="radio" name="pay_type" value="1" onclick="set_bank('');set_branch_code('');show('xd_sec');"> โอนเงิน
									<input type="radio" name="pay_type" value="2" onclick="set_bank('');set_branch_code('');show('che_sec');"> เช็ค
									<input type="radio" name="pay_type" value="2" onclick="set_bank('');set_branch_code('');show('other_sec');"> อื่นๆ
								</div>
							</div>
						</div>
						<div class="row m-b-1" id="xd_sec" style="display:none;">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"></label>
								<div class="g24-col-sm-17">
									<div id="transfer_deposit">
										<div class="transfer_content">
											<div class="row transfer">
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
														<input type="radio" name="xd_bank_id" id="xd_1" onclick="set_bank('006');set_branch_code('0071');"><label for="xd_1"> ธ.กรุงไทย จำกัด สาขาการปิโตรเลียม</label>
													</div>
												</div>
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
														<input type="radio" name="xd_bank_id" id="xd_2" onclick="set_bank('002');set_branch_code('1082');"><label for="xd_2"> ธ.กรุงเทพ จำกัด สาขาเอนเนอร์ยี่ คอมเพล็กซ์</label>
													</div>
												</div>
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
														<input type="radio" name="xd_bank_id" id="xd_3" onclick="set_bank('011');set_branch_code('0211');"><label for="xd_3"> ธ.ทหารไทย จำกัด สาขาเอนเนอร์ยี่คอมเพล็กซ์ </label>
													</div>
												</div>
											</div>
											<div class="row transfer">
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
														<input type="radio" name="xd_bank_id" id="xd_4" onclick="set_bank('');set_branch_code('');"><label for="xd_4"> บัญชีเงินฝาก </label>
														<select class="form-control" name="transfer_bank_account_name" id="transfer_bank_account_name" style="display: initial !important;width: 200px !important;">
															<option value="">เลือกบัญชี</option>
															<?php
																foreach ($maco_account as $key => $value) {
																	echo '<option value="'.$value['account_id'].'">'.$value['account_id'].' '.$value['account_name'].'</option>';
																}
															?>
														</select>
													</div>
												</div>
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
														<input type="radio" name="xd_bank_id" id="xd_5" onclick="set_bank('');set_branch_code('');"><label for="xd_5"> อื่นๆ </label>
														<input type="text" name="transfer_other" id="transfer_other" class="form-control" style="display: initial !important;width: 200px !important;">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row m-b-1" id="che_sec" style="display:none;">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"></label>
								<div class="g24-col-sm-17">
									<div id="cheque_deposit">
										<div class="cheque_content">
											<div class="row cheque">
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
														<input type="radio" name="che_bank_id" id="che_1" onclick="set_bank('006');set_branch_code('0071');"><label for="che_1"> ธ.กรุงไทย จำกัด สาขาการปิโตรเลียม</label>
													</div>
												</div>
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
														<input type="radio" name="che_bank_id" id="che_2" onclick="set_bank('002');set_branch_code('1082');"><label for="che_2"> ธ.กรุงเทพ จำกัด สาขาเอนเนอร์ยี่ คอมเพล็กซ์</label>
													</div>
												</div>
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
														<input type="radio" name="che_bank_id" id="che_3" onclick="set_bank('011');set_branch_code('0211');"><label for="che_3"> ธ.ทหารไทย จำกัด สาขาเอนเนอร์ยี่คอมเพล็กซ์ </label>
													</div>
												</div>
											</div>
											<div class="row cheque">
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-4" for="cheque_number">&nbsp;&nbsp;&nbsp;หมายเลขเช็ค :</label>
														<input class="form-control g24-col-sm-10" name="cheque_number" id="cheque_number" placeholder="ระบุบัญชีเงินฝาก"/>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row m-b-1" id="other_sec" style="display:none;">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"></label>
								<div class="g24-col-sm-17">
									<div id="cheque_deposit">
										<div class="cheque_content">
											<div class="row cheque">
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-4" for="other">&nbsp;&nbsp;&nbsp;อื่นๆ :</label>
														<input class="form-control g24-col-sm-10" name="other" id="other" placeholder="ระบุ"/>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> ระบุวันที่ปิดบัญชี </label>
								<div class="g24-col-sm-11">
									<div class="input-with-icon">
										<div class="form-group">
											<?php
												$opn_date = date('d/m/').(date('Y')+543);
											?>
											<div id="form_acc_id" class="form-group">
												<input id="close_date" name="close_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" data-date-language="th-th" value="<?=$opn_date?>" >
												<span class="icon icon-calendar input-icon m-f-1"></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> ระบุเวลาปิดบัญชี </label>
								<div class="g24-col-sm-11">
									<div class="input-with-icon">
										<div class="form-group">
											<div id="form_acc_id" class="form-group">
												<input id="close_time" name="close_time" class="form-control m-b-1 timepicker" type="text" value="<?php echo date('H:i:s'); ?>">
												<span class="icon icon-clock-o input-icon"></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						
						<div class="row m-b-1">
							<div class="form-group">
								<div class="g24-col-sm-24" style="text-align:center">
									<input type="hidden" name="bank_id" id="bank_id">
									<input type="hidden" name="branch_code" id="branch_code">
									<button type="submit" class="btn btn-primary min-width-100">ยืนยัน</button>
									<button class="btn btn-danger min-width-100" type="button"
											onclick="close_modal('close_account');"> ยกเลิก
									</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				&nbsp;
			</div>
		</div>
	</div>
</div>
<script>
	function removeCommas(str) {
		return(str.replace(/,/g,''));
	}
	
	function add_account(account_id, member_id) {
		$.ajax({
			url: base_url + "/save_money/add_save_money",
			method: "post",
			data: {account_id: account_id, member_id: member_id},
			dataType: "text",
			success: function (data) {
				$('#add_account_space').html(data);
				if ($('#sequester_status_2').is(':checked')) {
					$('.show_sequester_amount').show();
				}
				$('#add_account').modal('show');
				change_account_type();
			}
		});

	}

	function change_type() {
		if ($('#sequester_status_2').is(':checked')) {
			$('.show_sequester_amount').show();
		} else {
			$('#sequester_amount').val('0');
			$('.show_sequester_amount').hide();
		}
		check_remark();
	}

	function check_remark(){
		var sequester_status = $('input[name=sequester_status]:checked', '#frm1').val();
		var sequester_status_atm = $('input[name=sequester_status_atm]:checked', '#frm1').val();
		if((sequester_status != 0 || sequester_status_atm != 0) && !$("input[name='sequester_status_atm']").is(':disabled')){
			$('#div_remark').show();
		}else{
			$('#div_remark').hide();
		}
	}

	function get_data(member_id, member_name) {
		$('#member_id_add').val(member_id);
		$('#member_name_add').val(member_name);
		$('#acc_name_add').val(member_name);
		$('#acc_name_add').removeAttr('readonly');
		$('#account_name_eng').val("");
		$.post(base_url + "ajax/get_member",
			{
				member_id: member_id
			}
			, function (result) {
				obj = JSON.parse(result);
				create_option_account_transfer(obj.account_list_transfer);
				console.log(obj);
				if (obj.firstname_en) {
					$('#account_name_eng').val(obj.firstname_en + ' ' + obj.lastname_en);
				} else {
					$('#account_name_eng').val("");
				}

				$.post(base_url + "Save_money/get_account_saving", {
					member_id: member_id
				}
				, function (result) {
					obj = JSON.parse(result);
					$('#transfer_bank_account_name')
						.empty()
						.append('<option selected="selected" value="">เลือกบัญชี</option>');
					$.each(obj, function (i, item) {
						$('#transfer_bank_account_name').append($('<option>', { 
							value: item.account_id,
							text : item.account_id+' '+item.account_name
						}));
					});
				});
				

			});
		$('#account_name_eng').removeAttr('readonly');
		$('#type_id').removeAttr('readonly');
		$('#search_member_add_modal').modal('hide');
	}

	function delete_account(account_id) {
		swal({
				title: "ท่านต้องการลบบัญชีใช่หรือไม่?",
				text: "",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'ยืนยัน',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: false,
				closeOnCancel: true
			},
			function (isConfirm) {
				if (isConfirm) {
					$.ajax({
						type: "POST",
						url: base_url + "/save_money/check_account_delete",
						data: {account_id: account_id},
						success: function (msg) {
							if (msg == 'success') {
								document.location.href = base_url + '/save_money/delete_account/' + account_id;
							} else {
								swal('ไม่สามารถลบข้อมูลบัญชีได้', 'เนื่องจากมียอดเงินคงเหลือในบัญชี', 'warning');
							}
						}
					});
				} else {

				}
			});
	}

	function close_account(account_id, member_id) {
		/*swal({
            title: "ท่านต้องการปิดบัญชีใช่หรือไม่?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                document.location.href = base_url+'/save_money/close_account/'+account_id;
            } else {

            }
        });*/
		blockUI();
		$.ajax({
			type: "POST",
			url: base_url + "save_money/close_account_calculate",
			data: {account_id: account_id},
			success: function (msg) {
				var obj = JSON.parse(msg);
				if (obj.text_alert != '') {
					swal('', obj.text_alert, 'warning');
				} else {
					$('#close_account_interest').val(obj.interest);
					$('#close_account_interest_return').val(obj.interest_return);
					$('#close_account_tax_return').val(obj.tax_return);
					$('#tmp_close_account_interest_return').val(obj.interest_return);
					$('#close_account_principal').val(obj.principal);
					$('#close_account_id').val(account_id);
					$("#close_account_interest").trigger("keyup");
					$('#close_account').modal('show');
					$('#close_member_id').html(member_id);
					$('#close_account_id_c').html(obj.prefix_account_id);
					$('#close_account_name').html(obj.account_name);

					$.post(base_url + "Save_money/get_account_saving", {
						member_id: member_id
					}
					, function (result) {
						obj = JSON.parse(result);
						$('#transfer_bank_account_name')
							.empty()
							.append('<option selected="selected" value="">เลือกบัญชี</option>');
						$.each(obj, function (i, item) {
							$('#transfer_bank_account_name').append($('<option>', { 
								value: item.account_id,
								text : item.account_id+' '+item.account_name
							}));
							unblockUI(1);
						});
					});
				}
			}
		});

	}

	$(function () {
		$("#search_member_add").keyup(function () {
			$.ajax({
				type: "POST",
				url: base_url + "/ajax/search_member_jquery",
				data: {search: $("#search_member_add").val()},
				success: function (msg) {
					$("#result_add").html(msg);
				}
			});
		});

		
        $("#search_text").keyup(function(e) {
			var code = e.which; // recommended to use e.which, it's normalized across browsers
			if(code==13)e.preventDefault();
			if(code==32||code==13||code==188||code==186){
				check_search();
				// $.ajax({
                //     type: "POST",
                //     url: base_url+"/ajax/search_account",
                //     data: "search_text=" + $("#search_text").val(),
                //     success: function(msg) {
                //         $("#tb_wrap").html(msg);
                //         $("#page_wrap").css("display", $("#search_text").val() == "" ? "block" : "none");
                //     }
                // });
			} 
                // $.ajax({
                //     type: "POST",
                //     url: base_url+"/ajax/search_account",
                //     data: "search_text=" + $("#search_text").val(),
                //     success: function(msg) {
                //         $("#tb_wrap").html(msg);
                //         $("#page_wrap").css("display", $("#search_text").val() == "" ? "block" : "none");
                //     }
                // });
            });
        
		$("#is_ignore_interest_return").click(function() {
			if($(this).prop("checked")) {
				$("#close_account_interest_return").val("");
				$("#close_account_interest_return").prop("readonly", true);
			}
			else {
				$("#close_account_interest_return").val($("#tmp_close_account_interest_return").val());
				$("#close_account_interest_return").prop("readonly", false);
			}
			
			$(".cal_close_account_total").trigger("keyup");
		});
		
		$(".cal_close_account_total").keyup(function(e) {
			var close_account_principal = isNaN(parseFloat($("#close_account_principal").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_principal").val().replace(/,/g, ""));
			var close_account_interest = isNaN(parseFloat($("#close_account_interest").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_interest").val().replace(/,/g, ""));
			var close_account_interest_return = isNaN(parseFloat($("#close_account_interest_return").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_interest_return").val().replace(/,/g, ""));
			var close_account_principal_return = isNaN(parseFloat($("#close_account_principal_return").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_principal_return").val().replace(/,/g, ""));
			$("#close_account_total").html($().number_format(close_account_principal + close_account_interest - close_account_interest_return - close_account_principal_return, { numberOfDecimals: 2, decimalSeparator: '.', thousandSeparator: ',' }));
		});
	});

	function check_submit() {		
		var chk_staus = true;
		var text_alert = '';
		if ($('#member_id_add').val() == '') {
			text_alert += '- รหัสสมาชิก\n';
			chk_staus = false;
		}
		if ($('#acc_name_add').val() == '') {
			text_alert += '- ชื่อบัญชี\n';
			chk_staus = false;
		}
		if ($('#type_id').val() == '') {
			text_alert += '- ประเภทบัญชี\n';
			chk_staus = false;
		}

		if($('#min_first_deposit').val()==''){
			if($('#min_first_deposit').is('[readonly]')==false){
				text_alert += '- ระบุยอดเงินเปิดบัญชี\n';
				chk_staus = false;
			}	
		}
		var sequester_status = $('input[name=sequester_status]:checked', '#frm1').val();
		var sequester_status_atm = $('input[name=sequester_status_atm]:checked', '#frm1').val();
		var old_account_no = $("#old_account_no").val();
		console.log("11 ",$("#remark").val());
		console.log($("input[name='sequester_status_atm']").is(':disabled'));
		if((sequester_status != 0 || sequester_status_atm != 0) 
			&& $("#remark").val()==""
			&& !$("input[name='sequester_status_atm']").is(':disabled')
		){
			text_alert += '- ระบุสาเหตุการอายัดบัญชีด้วย\n';
			chk_staus = false;
		}

		if($('#acc_id').val()!=undefined){
			var tmp = $('#acc_id').val();
			acc_id = tmp.replace(/-/g, '');
		}else{
			var tmp = $('#acc_id_yourself').val();
			acc_id = tmp.replace(/-/g, '');
		}
		
		$.ajax({
			type: "POST",
			url: base_url + "/save_money/check_account_save",
			data: {
				atm_number: $('#atm_number').val(),
				member_id: $('#member_id_add').val(),
				account_id: acc_id,
				old_account_no: $("#old_account_no").val(),
				type_id: $('#type_id').val(),
				unique_account: $('#type_id :selected').attr('unique_account'),
				min_first_deposit: removeCommas($('#min_first_deposit').val())
			},
			success: function (msg) {
				var obj = JSON.parse(msg);
				if (obj.acc_number == 'dupplicate_account_no' && ($("#acc_id").val()=="" || $("#acc_id").val()==undefined) ) {
					text_alert += '- มีเลขที่บัญชี ซ้ำในระบบ\n';
					chk_staus = false;
				}
				if (obj.atm_number == 'dupplicate') {
					text_alert += '- มีเลขบัตร ATM ซ้ำในระบบ\n';
					chk_staus = false;
				}
				if (obj.unique_account == 'dupplicate') {
					if(obj.account_status == '0'){
						text_alert += '- ประเภทบัญชีที่ท่านเลือกมีได้เพียงบัญชีเดียว\n';
						chk_staus = false;
					}
				}
				if (obj.error != '' && old_account_no=="") {
					text_alert += '- ' + obj.error + '\n';
					chk_staus = false;
				}

				if (text_alert != '') {
					swal('กรุณากรอกข้อมูลต่อไปนี้', text_alert, 'warning');
				} else {
					if($('#acc_id_yourself').val()!=undefined){
						var tmp = $('#acc_id_yourself').val();
						acc_id = tmp.replace(/-/g, '');
						$('#acc_id_yourself').val(acc_id);
					}
					
					if(chk_staus == true){
						$("#btn_save_add").attr('disabled','disabled');						
						blockUI();
						$('#frm1').submit();
					}else{
						$("#btn_save_add").attr('disabled','');
					}
				}
			}
		});
	}

	function change_account_type() {
		if ($('#type_id :selected').attr('type_code') == '21') {
			$('#atm_space').show();
		} else {
			$('#atm_number').val('');
			$('#atm_space').hide();
		}
	}

	function check_search() {
		if ($('#search_list').val() == '') {
			swal('กรุณาเลือกรูปแบบค้นหา', '', 'warning');
		} else if ($('#search_text').val() == '') {
			swal('กรุณากรอกข้อมูลที่ต้องการค้นหา', '', 'warning');
		} else {
			var tmp = $('#search_text').val().split('-');
			var search_text = tmp.join('');
			$.ajax({
				type: "POST",
				url: base_url + "/ajax/search_account",
				data: {
					search_text: search_text,
					search_list: $('#search_list').val()
				},
				success: function (msg) {
					$("#tb_wrap").html(msg);
					$("#page_wrap").css("display", $("#search_text").val() == "" ? "block" : "none");
				}
			});
		}
	}

	function check_member_id() {
		var member_id = $('#member_id_add').val();
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if (keycode == '13') {
			$.post(base_url + "ajax/get_member",
				{
					member_id: member_id
				}
				, function (result) {
					obj = JSON.parse(result);
					if (obj.member_id && obj.member_name) {
						get_data(obj.member_id, obj.member_name)
						if (obj.firstname_en) {
							$('#account_name_eng').val(obj.firstname_en + ' ' + obj.lastname_en);
						} else {
							$('#account_name_eng').val("");
						}
					} else {
						swal('ไม่พบรหัสสมาชิกที่ท่านเลือก', '', 'warning');
					}
				});
		}
	}

	$('#member_search').click(function () {
		if ($('#member_search_list').val() == '') {
			swal('กรุณาเลือกรูปแบบค้นหา', '', 'warning');
		} else if ($('#member_search_text').val() == '') {
			swal('กรุณากรอกข้อมูลที่ต้องการค้นหา', '', 'warning');
		} else {
			$.ajax({
				url: base_url + "ajax/search_member_by_type_jquery",
				method: "post",
				data: {
					search_text: $('#member_search_text').val(),
					search_list: $('#member_search_list').val()
				},
				dataType: "text",
				success: function (data) {
					$('#result_add').html(data);
				},
				error: function (xhr) {
					console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
				}
			});
		}
	});

	function close_modal(id) {
		$('#' + id).modal('hide');
	}

	function click_bt_yourself() {
		$('.show_acc_id_yourself').show();
	}

	function create_option_account_transfer(data){
		console.log(data);
		$('#account_transfer')
			.find('option')
			.remove()
			.end()
			.append('<option value="">เลือกบัญชีคู่โอน</option>')
			.val('')
		;
		// account_transfer
		$.each(data, function(key, value) {   
			$('#account_transfer')
				.append($("<option></option>")
							.attr("value",value.id)
							.text(value.text)); 
		});
	}

	function set_bank(val){
		$("#bank_id").val(val);
	}

	function set_branch_code(val){
		$("#branch_code").val(val);
	}

	function show(val){
		if(val=='xd_sec'){
			$("#xd_sec").show();
			$("#che_sec").hide();
			$("#other_sec").hide();
		}else if(val=='che_sec'){
			$("#xd_sec").hide();
			$("#che_sec").show();
			$("#other_sec").hide();
		}else if(val=='other_sec'){
			$("#xd_sec").hide();
			$("#che_sec").hide();
			$("#other_sec").show();
		}else{
			$("#xd_sec").hide();
			$("#che_sec").hide();
			$("#other_sec").hide();
		}
		
	}

	$( document ).ready(function() {
		$(".mydate").datepicker({
			prevText : "ก่อนหน้า",
			nextText: "ถัดไป",
			currentText: "Today",
			changeMonth: true,
			changeYear: true,
			isBuddhist: true,
			monthNamesShort: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
			dayNamesMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
			constrainInput: true,
			dateFormat: "dd/mm/yy",
			yearRange: "c-50:c+10",
			autoclose: true,
		});
	});

	$(document).on('change', '#close_date', function(){

		var close_date = $("#close_date").val();
		var account_id = $('#close_account_id').val();
		var member_id = $('#member_Id').val();


		if(typeof account_id !== "undefined" && account_id !== ""){
			blockUI();
			$.ajax({
				type: "POST",
				url: base_url + "save_money/close_account_calculate",
				data: {account_id: account_id, fixed_date: close_date},
				success: function (msg) {
					var obj = JSON.parse(msg);
					if (obj.text_alert != '') {
						swal('', obj.text_alert, 'warning');
					} else {
						$('#close_account_interest').val(obj.interest);
						$('#close_account_interest_return').val(obj.interest_return);
						$('#tmp_close_account_interest_return').val(obj.interest_return);
						$('#close_account_tax_return').val(obj.tax_return);
						$('#close_account_principal').val(obj.principal);
						$('#close_account_id').val(account_id);
						$("#close_account_interest").trigger("keyup");
						$('#close_account').modal('show');
						unblockUI(1);
					}
				}
			});
		}
	});

</script>
