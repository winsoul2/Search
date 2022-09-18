<div class="layout-content">
    <div class="layout-content-body">
		<style>
		  .border1 { border: solid 1px #ccc; padding: 0 15px; }
		  .mem_pic { margin-top: -1em;float: right; width: 150px; }
		  .mem_pic img { width: 100%; border: solid 1px #ccc; }
		  .mem_pic button { display: block; width: 100%; }
		  .modal-backdrop.in{
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
		  .font-normal{
			font-weight:normal;
		  }
		  .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
			border: 1px solid #fff;
		  }
		  th, td {
			  text-align: center;
		  }
		</style>

		<h1 style="margin-bottom: 0"> รายการรับ - จ่าย  </h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0" id="breadcrumb">
				<?php $this->load->view('breadcrumb'); ?>
		</div>
		</div>
		<div class="panel panel-body col-xs-12 col-sm-12 col-md-12 col-lg-12 " >
			<form data-toggle="validator" id='form1' novalidate="novalidate" method="post" class="g24 form form-horizontal">
				<div class="row">
					<div class="form-group">
						<div class="g24-col-sm-24">
							<label class="g24-col-sm-3 control-label">
								วันที่
							</label>
							<div class="g24-col-sm-5">
								<div class="input-group">
									<div class="input-with-icon">
										<input id="buy_date" name="buy_date" class="form-control" type="text" value="<?php echo $this->center_function->mydate2date(empty($row) ? date("Y-m-d") : @$row['buy_date']); ?>" data-date-language="th-th" style="text-align:center;">
										<span class="icon icon-calendar input-icon"></span>
									</div>
									<span class="input-group-btn">
										<a data-toggle="modal" data-target="#search_account_buy" id="test" class="" href="#" onclick="search_buy('')">
											<button id="" type="button" class="btn btn-info btn-search">
												<span class="icon icon-search"></span>
											</button>
										</a>
									</span>	
								</div>
								
							</div>
							<?php if(@$_GET['account_buy_id']!=''){ ?>
							<div class="g24-col-sm-16 text-right">
								<a class="" href="<?php echo base_url(PROJECTPATH.'/coop_buy')?>">
									<button id="" type="button" class="btn btn-info btn-width-auto">
										<span class="icon icon-plus-circle"></span> เพิ่มรายการใหม่
									</button>
								</a>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<div class="g24-col-sm-24">
							<label class="g24-col-sm-3 control-label">
								รับ - จ่ายให้
							</label>
							<div class="g24-col-sm-11">
								<input type="text" class="form-control" id="pay_for" value="<?php echo @$row['pay_for']!=''?$row['pay_for']:''; ?>">
							</div>
						</div>
					</div>
				</div>
                <div class="row">
                    <div class="form-group">
                        <div class="g24-col-sm-24">
                            <label class="g24-col-sm-3 control-label">
                                รายการ รับ / จ่าย
                            </label>
                            <div class="g24-col-sm-11">
                                <select class="form-control" name="cashpay" id="cashpay">
                                        <option value="receipt">รายการรับ</option>
                                        <option value="payment">รายการจ่าย</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="row">
					<div class="form-group">
						<div class="g24-col-sm-24">
							<label class="g24-col-sm-3 control-label">
								โดย
							</label>
							<label class="g24-col-sm-20 control-label" style="text-align:left;">
								<input type="radio" name="pay_type" id="pay_type" value="cash" onclick="change_pay_type('1')" <?php echo @$row['pay_type']=='cash'?'checked':''; ?>>&nbsp;&nbsp;เงินสด 
								&nbsp;&nbsp;&nbsp;&nbsp;
								<!--input type="radio" name="pay_type" id="pay_type2" value="cheque" onclick="change_pay_type('2')" <?php echo @$row['pay_type']=='cheque'?'checked':''; ?>>&nbsp;&nbsp;เช็คธนาคาร -->
								<input type="radio" name="pay_type" id="pay_type3" value="transfer" onclick="change_pay_type('3')" <?php echo @$row['pay_type']=='transfer'?'checked':''; ?>>&nbsp;&nbsp; เงินโอน
								<input type="radio" name="pay_type" id="pay_type8" value="railway" onclick="change_pay_type('8')" <?php echo @$row['pay_type']=='railway'?'checked':''; ?>>&nbsp;&nbsp; รับเงินจากการรถไฟ
							</label>
							<?php
								if( @$row['pay_type']=='cash' || @$row['pay_type']==''){
									$cheque_display = 'display:none;';
									$transfer_display = 'display:none;';
								}else if(@$row['pay_type']=='transfer'){
									$cheque_display = 'display:none;';
									$transfer_display = '';
								}else{
									$cheque_display = '';
									$transfer_display = 'display:none;';
								}
							?>
							<label class="g24-col-sm-3 control-label cheque_space" style="<?php echo $cheque_display; ?>">
								เลขที่เช็ค
							</label>
							<div class="g24-col-sm-4 cheque_space" style="<?php echo $cheque_display; ?>">
								<input type="text" id="cheque_number" class="form-control" value="<?php echo @$row['cheque_number']!=''?$row['cheque_number']:''; ?>">
							</div>
							<label class="g24-col-sm-3 control-label cheque_space" style="<?php echo $cheque_display; ?>">
								ลงวันที่
							</label>
							<div class="g24-col-sm-4 cheque_space" style="<?php echo $cheque_display; ?>">
								<div class="input-with-icon">
									<input id="cheque_date" name="cheque_date" class="form-control" type="text" value="<?php echo (@$row['account_buy_id']!='' && @$row['pay_type']=='cheque')?date("d/m/Y",strtotime(@$row['cheque_date'])):date("d/m/Y",strtotime('+543 year')); ?>" data-date-language="th-th" style="text-align:center;">
									<span class="icon icon-calendar input-icon"></span>
								</div>
							</div>
							<label class="g24-col-sm-3 control-label transfer_space" style="<?php echo $transfer_display; ?>">
								บัญชีธนาคาร
							</label>
							<div class="g24-col-sm-4 transfer_space" style="<?php echo $transfer_display; ?>">
								<select name="account_bank_id" id="account_bank_id" class="form-control">
									<option value="">เลือกบัญชีธนาคาร</option>
									<?php
										foreach($rs_bank as $key => $row_bank){ ?>
											<option value="<?php echo $row_bank['account_bank_id']; ?>" <?php echo @$row['account_bank_id'] == $row_bank['account_bank_id']?'selected':''; ?>><?php echo $row_bank['account_bank_name']; ?></option>
										<?php }
									?>
								</select>
							</div>
							<label class="g24-col-sm-2 control-label transfer_space" style="text-align:left;<?php echo $transfer_display; ?>">
								<!--a href="/admin/coop_buy_setting.php?act=add&return_url=coop_buy.php"> ตั้งค่า </a-->
								<a href="<?php echo PROJECTPATH."/setting_account_data2/bank";?>"> ตั้งค่า </a>
							</label>
						</div>
					</div>
				</div>
				<div class="row" style="margin-left:3px">
						<h3>เพิ่มรายการ</h3>
				</div>
				<div class="row">
					<div class="form-group">
						<div class="g24-col-sm-24">
							<label class="g24-col-sm-3 control-label">
								รายการ
							</label>
							<div class="g24-col-sm-11">
								<select class="form-control" name="account_id" id="account_id" onchange="change_account_list()">
									<option value="">เลือกรายการ</option>
									<?php foreach($account_buy_list as $key => $row_account_buy_list){ ?>
										<option value="<?php echo $row_account_buy_list['account_id']?>" account_list="<?php echo $row_account_buy_list['account_list']; ?>" amount="<?php echo $row_account_buy_list['amount']; ?>"><?php echo $row_account_buy_list['account_list']; ?></option>		
									<?php } ?>
								</select>
							</div>
							<label class="g24-col-sm-2 control-label" style="text-align:left;">
								<!--a href="/admin/coop_buy_setting.php?act=add&return_url=coop_buy.php"> ตั้งค่า </a-->
								<a href="<?php echo PROJECTPATH."/setting_account_data2/coop_account_buy";?>"> ตั้งค่า </a>
							</label>
							
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<div class="g24-col-sm-24">
							<label class="g24-col-sm-3 control-label">
								เพื่อจ่าย
							</label>
							<div class="g24-col-sm-11">
								<input type="text" id="pay_description" class="form-control">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<div class="g24-col-sm-24">
							<label class="g24-col-sm-3 control-label">
								จำนวนเงิน
							</label>
							<div class="g24-col-sm-3">
								<input type="text" id="pay_amount" class="form-control" onkeypress="return chkNumber(this)">
							</div>
							<label class="g24-col-sm-1 control-label">
								บาท
							</label>
							<label class="g24-col-sm-3 control-label">
								เลขที่บิล
							</label>
							<div class="g24-col-sm-4">
								<input type="text" id="bill_number" class="form-control">
							</div>
							<label class="g24-col-sm-4">
								<?php if(@$data_get['account_buy_id'] != ''){ 
									if(@$row['account_buy_status'] == '1'){ ?>
										<button type="button" onclick="cancel_account_buy('0','<?php echo $row['account_buy_id']; ?>')" class="btn btn-warning min-width-100 btn-width-auto">
											<span class="icon icon-close"></span>
											ยกเลิกการยกเลิกรายการซื้อ					
										</button>
									<?php }else if(@$row['account_buy_status'] == '0'){ ?>
										<button type="button" onclick="cancel_account_buy('1','<?php echo $row['account_buy_id']; ?>')" class="btn btn-danger min-width-100 btn-width-auto">
											<span class="icon icon-close"></span>
											ยกเลิกรายการซื้อ					
										</button>
									<?php }else{ ?>
											<span style="color:red;">รายการถูกยกเลิกแล้ว</span>
									<?php } ?>
								<?php }else{ ?>
								<button type="button" onclick="check_form()" class="btn btn-primary min-width-100">
									<span class="icon icon-save"></span>
									เพิ่มรายการ					
								</button>
								<?php } ?>
							</label>
						</div>
					</div>
				</div>
			</form>

		<input type="hidden" id="number_input" value="0">
		<form id="form2" action="<?php echo base_url(PROJECTPATH.'/coop_buy/save_buy'); ?>" target="_blank" method="POST">
			<input type="hidden" class="type_input" name="data[coop_account_buy][buy_date]" id="buy_date_input">
			<input type="hidden" class="type_input" name="data[coop_account_buy][pay_for]" id="pay_for_input">
            <input type="hidden" class="type_input" name="data[coop_account_buy][cashpay_input]" id="cashpay_input">
            <input type="hidden" class="type_input" name="data[coop_account_buy][pay_type]" id="pay_type_input">
			<input type="hidden" class="type_input" name="data[coop_account_buy][cheque_number]" id="cheque_number_input">
			<input type="hidden" class="type_input" name="data[coop_account_buy][cheque_date]" id="cheque_date_input">
			<input type="hidden" class="type_input" name="data[coop_account_buy][account_bank_id]" id="account_bank_id_input">
			<div id="hidden_space">
			</div>
		</form>
		<!-- For preview -->
		<form id="form3" action="<?php echo base_url(PROJECTPATH.'/coop_buy/coop_buy_preview_pdf'); ?>" target="_blank" method="POST">
			<input type="hidden" class="type_input" name="buy_date" id="buy_date_preview">
			<input type="hidden" class="type_input" name="pay_for" id="pay_for_preview">
            <input type="hidden" class="type_input" name="cashpay_input" id="cashpay_preview">
            <input type="hidden" class="type_input" name="pay_type" id="pay_type_preview">
			<input type="hidden" class="type_input" name="cheque_number" id="cheque_number_preview">
			<input type="hidden" class="type_input" name="cheque_date" id="cheque_date_preview">
			<input type="hidden" class="type_input" name="account_bank_id" id="account_bank_id_preview">
			<div id="hidden_space_preview">
			</div>
		</form>
				<div class="bs-example" data-example-id="striped-table">
					<table class="table table-bordered table-striped table-center">	
						<thead> 
							<tr class="bg-primary">
								<th class="font-normal" style="width: 15%">เลขที่บิล</th>
								<th class="font-normal" style="width: 40%">รายการ</th>
								<th class="font-normal" style="width: 15%;">จำนวนเงิน</th> 
								<th class="font-normal" style="width: 5%;"></th> 
							</tr> 
						</thead>
						<tbody id="table_space">
							<?php
							if(!empty($rs_detail)){
								foreach(@$rs_detail as $key => $row_detail){
							?>
							<tr>
								<td><?php echo $row_detail['bill_number']; ?></td>
								<td><?php echo $row_detail['pay_description']; ?></td>
								<td><?php echo $row_detail['pay_amount']; ?></td>
								<td></td>
							</tr>
							<?php }
							} ?>
						</tbody>
						<tfoot>
						<?php if(@$data_get['account_buy_id'] != ''){ ?>
						<tr class="bg-primary table_footer">
							<td align='right' colspan='2'>ยอดรวมสุทธิ</td>
							<td align='right'> <?php echo number_format($row['total_amount'],2); ?> บาท</td>
							<td></td>
						</tr>
						<?php }else{ ?>
						<tr class="bg-primary table_footer" style="display:none;">
							<td align='right' colspan='2'>ยอดรวมสุทธิ</td>
							<td align='right'> <span id="total_space"></span> บาท</td>
							<td></td>
						</tr>
						<?php } ?>
						</tfoot>
					</table>
				</div>
				<?php if(@$data_get['account_buy_id'] != ''){ ?>
				<div class="row m-t-1 table_footer">	
					<center>
						<a href="<?php echo base_url(PROJECTPATH.'/coop_buy/coop_buy_pdf?account_buy_id='.$data_get['account_buy_id']); ?>" target="_blank">
							<button class="btn btn-primary btn-width-auto" type="button">
								<span class="icon icon-print"></span>
								พิมพ์ใบสำคัญจ่าย
							</button>
						</a>
					</center>
				</div>
				<?php }else{ ?>
				<div class="row m-t-1 table_footer" style="display:none;">	
					<center>
						<button class="btn btn-primary btn-width-auto" type="button" onclick="submit_form()">
							<span class="icon icon-print"></span>
							บันทึกและพิมพ์ใบสำคัญจ่าย
						</button>
						<button class="btn btn-primary btn-width-auto" type="button" onclick="preview()">
							<span class="icon icon-print"></span>
							Preview
						</button>
					</center>
				</div>
				<?php } ?>
			</div>
	</div>
</div>
<div class="modal fade" id="search_account_buy" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">ข้อมูลรายการซื้อ</h4>
		</div>
			<div class="modal-body">
			<div class="input-with-icon">
				<input class="form-control input-thick pill m-b-2" type="text" placeholder="กรอกเลขที่ใบสำคัญจ่าย" name="search_text" id="search_buy">
				<span class="icon icon-search input-icon"></span>
			</div>

				<div class="bs-example" data-example-id="striped-table">
					<table class="table table-striped">
						<thead> 
							<tr>
								<th class="font-normal" style="width: 15%">เลขที่ใบสำคัญ</th>
								<th class="font-normal" style="width: 15%">วันที่</th>
								<th class="font-normal" style="width: 40%">จ่ายให้</th> 
								<th class="font-normal" style="width: 5%"></th> 
							</tr> 
						</thead>
						<tbody id="result_account_buy">
							<?php
								   if(!empty($rs_search)){  
										$i= 1; 
										foreach($rs_search as $key => $row){ ?>
												<tr> 
													<th scope="row"><?php echo $row['account_buy_number']; ?></th>
													<td><?php echo $this->center_function->ConvertToThaiDate($row['buy_date'],'1','0'); ?></td> 
													<td><?php echo $row['pay_for']; ?></td> 
													<td align="right">
														<a href="?account_buy_id=<?php echo $row['account_buy_id']; ?>">
															<button style="padding: 2px 12px;"  id="<?php echo $row['account_buy_id']; ?>" type="button" class="btn btn-info">เลือก</button>
														</a>
													</td>
												</tr>
									  <?php $i++; 
									   }
								   }
							?>
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
<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_buy.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
