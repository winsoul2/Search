<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.left {
				text-align: left;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			.modal-dialog-data {
				width:90% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal-dialog-cal {
				width:80% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal-dialog-file {
				width:50% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal_data_input{
				margin-bottom: 5px;
			}
			.form-group{
				margin-bottom: 5px;
			}
			.red{
				color: red;
			}
			.green{
				color: green;
			}
			.green_blue{
				background-color: #c7f5c7;
				color: blue;
			}
			.title-tab{
				margin-top: 0px;
				margin-bottom: 0px;
			}.blue{
			background-color: #4ef7ff;
			opacity: 1;
			border-radius: 4px;
			display: block;
			width: 100%;
			height: 34px;
			padding: 6px 12px;
			font-size: 13px;
			line-height: 1.538462;
			color: #3e65d6;
			background-image: none;
			border: 1px solid #e0e0e0;
			 }
			.red_reason{
				background-color: #fa0b01;
				opacity: 1;
				border-radius: 4px;
				display: block;
				width: 100%;
				height: 34px;
				padding: 6px 12px;
				font-size: 13px;
				line-height: 1.538462;
				color: white;
				background-image: none;
				border: 1px solid #e0e0e0;
			}
			.yell_gua{
				color: #0b46ab;
				background-color: #ffffad;
			}

		</style> 
		<div class="row">
			<div class="form-group">
				<div class="col-sm-6">
					<h1 class="title_top">ข้อมูลสมาชิก</h1>
					<?php $this->load->view('breadcrumb'); ?>
				</div>
				<div class="col-sm-6">
				<br>
					<!--<div class="g24-col-sm-24">
					<?php if($member_id!=''){ ?>
						<div class="g24-col-sm-10">
							<select id="loan_type_choose" class="form-control" onchange="change_type()">
								<option value="">เลือกประเภทการกู้เงิน</option>
								<?php foreach($rs_loan_type as $key => $value){ ?>
									<option value="<?php echo $value['id']; ?>" ><?php echo $value['loan_type']; ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="g24-col-sm-9">
							<select id="loan_type_select" class="form-control">
								<option value="">เลือกชื่อเงินกู้</option>
							</select>
						</div>
						<div class="g24-col-sm-1">
							<a class="link-line-none" id="normal_loan_btn" onclick="change_modal()">
								<button class="btn btn-primary" style="margin-right:5px;">เพิ่มคำร้อง</button>
							</a>
						</div>
					<?php } ?>
					</div>-->
				</div>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
						<?php $this->load->view('search_member_new'); ?>
						<div class="g24-col-sm-24">
							<!--<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">ตำแหน่ง</label>
								<div class="g24-col-sm-14" >
									<input id="position_name"  class="form-control " type="text" value="<?php echo @$row_member['position']; ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">ประเภท</label>
								<div class="g24-col-sm-14" >
									<input id="mem_type_id"  class="form-control " type="text" value="<?php echo @$mem_type_list[@$row_member['mem_type_id']]; ?>"  readonly>
								</div>
							</div>
							-->
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label " for="form-control-2">ประเภทสมัคร</label>
								 <div class="g24-col-sm-14">
									<input id="form-control-2"  class="form-control " type="text" value="<?php echo $mem_apply_type[$row_member['apply_type_id']];?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">เงินเดือน</label>
								<div class="g24-col-sm-14" >
									<input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$row_member['salary']); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">รายได้อื่นๆ</label>
								<div class="g24-col-sm-14" >
									<input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$row_member['other_income']); ?>"  readonly>
								</div>
							</div>	
						</div>
						<div class="g24-col-sm-24">								
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">รวมรายได้</label>
								<div class="g24-col-sm-14" >
									<input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$row_member['salary']+@$row_member['other_income']); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">พินัยกรรม</label>
								<div class="g24-col-sm-14" >
									
									<input id="form-control-2"  class="form-control " type="text" <?php echo @$style_testament;?> value="<?php echo @$testament; ?>"  readonly>
								</div>
							</div>							
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">ทุนเรือนหุ้นสะสม</label>
								<div class="g24-col-sm-14">
									<input class="blue" type="text" value="<?php echo number_format(@$cal_share,0); ?>" readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">ส่งหุ้นงวดละ</label>
								<div class="g24-col-sm-14">
									<input class="blue" type="text" value="<?php echo number_format(@$row_member['share_month']); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">หมายเหตุ</label>
								<div class="g24-col-sm-14" >

									<div class="input-group">
										<input id="note_remark" class="red_reason" type="text" value="<?php echo strip_tags(@$note); ?>" readonly>
										<span class="input-group-btn">
											<a id="test_remark" class="fancybox_share fancybox.iframe" href="#" onclick="read_more()">
												<button id="" type="button" class="btn btn-info btn-search"><span class="fa fa-plus-square"></span></button>
											</a>
										</span>	
									</div>
								</div>
							</div>
                            <div class="form=group g24-col-sm-8">
                                <label class="g24-col-sm-10 control-label" for="form-control-2">หมายศาล</label>
                                <div class="g24-col-sm-14" >
                                    <input id="court_writ_note_in" class="red_reason" type="text" value="<?php echo(@$row_member['court_writ_'] == '1') ? "มีหมายศาล" : "ไม่มีหมายศาล"; ?>" readonly>
                                </div>
                            </div>
						</div>
				</div>
			</div>
		</div>
		
		<?php if(@$member_id != ''){?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">		
					<div class="g24-col-sm-24 m-t-3">
						<div class="panel m-b-lg">
							<div class="tabs-top">
							  <ul class="nav nav-tabs">
								<li class="" id="t1"><a href="#tab1" data-toggle="tab" aria-expanded="true"><h4 class="title-tab">หุ้น</h4></a></li>
								<li class="" id="t2"><a href="#tab2" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">เงินปันผล/เฉลี่ยคืน</h4></a></li>
								<li class="" id="t3"><a href="#tab3" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">เงินฝาก</h4></a></li>
								<li class="" id="t4"><a href="#tab4" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">การกู้เงิน</h4></a></li>
								<li class="" id="t5"><a href="#tab5" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">ภาระค้ำประกัน</h4></a></li>
								<li class="" id="t6"><a href="#tab6" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">รายการเรียกเก็บ</h4></a></li>
								<li class="" id="t7"><a href="#tab7" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">ประวัติการผิดนัดชำระ</h4></a></li>
								<li class="" id="t8"><a href="#tab8" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">ประวัติประกันชีวิต</h4></a></li>
								<li class="" id="t9"><a href="#tab9" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">คืนเงิน</h4></a></li>
								<li class="" id="t10"><a href="#tab10" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">รายได้-รายจ่าย</h4></a></li>
								<li class="" id="t11"><a href="#tab11" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">ฌาปณกิจ</h4></a></li>
								<li class="" id="t12"><a href="#tab12" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">หมายเหตุ</h4></a></li>
                                <li class="" id="t13"><a href="#tab13" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">หมายศาล</h4></a></li>
                                <li class="" id="t14"><a href="#tab14" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">ที่อยู่</h4></a></li>
                                <li class="" id="t14"><a href="#tab15" data-toggle="tab" aria-expanded="false"><h4 class="title-tab">ที่อยู่จัดส่ง</h4></a></li>
							  </ul>
							  <div class="tab-content m-t-3">
								<div class="tab-pane fade" id="tab1">
									<div class="" style="padding-top:0;">
										<!--<h3 >หุ้น</h3>-->
										<div class="g24-col-sm-24">
											<div class="form-group g24-col-sm-8">
												<label class="g24-col-sm-10 control-label ">ทุนเรือนหุ้นสะสม</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format(@$cal_share,0); ?>" readonly>
												</div>
											</div>
											<div class="form-group g24-col-sm-8">
												<label class="g24-col-sm-10 control-label ">ส่งหุ้นงวดละ</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format(@$row_member['share_month']); ?>"  readonly>
												</div>
											</div>
											<div class="form-group g24-col-sm-8">
												<label class="g24-col-sm-10 control-label ">งวดที่</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format(@$share_period); ?>"  readonly>
												</div>
											</div>
										</div>
									</div>
									<?php if($permission[332]==true){?>
									<div class="g24-col-sm-24 text-right">
										<button type="button" class="btn btn-info btn_deposit" data-toggle="modal" data-target="#update_transaction" data-account="<?php echo $row_memberall['account_id'] ?>"> <span class="icon icon-arrow-circle-down"></span> อัพเดท ST </button>
									</div>
									<?php }?>
									<div class="g24-col-sm-24 m-t-1 hidden_table" id="">
										<div class="bs-example" data-example-id="striped-table">
											<table class="table table-bordered table-striped table-center">
											<thead>
												<tr class="bg-primary">
													<th>ลำดับ</th>
													<th>วันที่</th>
													<th>ประเภท</th>
													<th>จำนวนเงิน</th>
													<th>จำนวนเงินหุ้นคงเหลือ</th>
													<th>หุ้นคงเหลือ</th>
													<th>เลขที่เอกสาร</th>
													<th>สถานะ</th>
													<th width="20%">ผู้ทำรายการ</th>
												</tr>
											</thead>

											<tbody id="table_first">
											<?php
											$run_no_share = 0;
											if(!empty($rs_mem_share)){
												foreach($rs_mem_share as $key => $row_mem_share){
													$run_no_share++;
											?>
													<tr>
														<td><?php echo @$run_no_share;?></td>
														<td><?php echo @$this->center_function->ConvertToThaiDate(@$row_mem_share['share_date']); ?></td>
														<td><?php echo @$row_mem_share['share_type']; ?></td>
														<td class="text-right"><?php echo number_format(@$row_mem_share['share_early_value'],0); ?></td>
														<td class="text-right"><?php echo number_format(@$row_mem_share['share_collect_value'],0); ?></td>
														<td class="text-right"><?php echo number_format(@$row_mem_share['share_collect'],0); ?></td>
														<td class="text-center"><?php echo @$row_mem_share['share_bill']; ?></td>
														<td class="text-center"><?php echo @$share_type[@$row_mem_share['share_type']]; ?></td>
														<td class="text-center"><?php echo @$row_mem_share['user_name'];?></td>
													</tr>
											<?php 
												}
											}else{
											?>	
												<tr><td colspan="8">ไม่พบข้อมูล</td></tr>
											<?php											
											} 
											?>
											</tbody>
										</table>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="tab2">
                                    <div class="bs-example" data-example-id="striped-table">
                                        <table class="table table-bordered table-striped table-center">
                                            <thead>
                                            <tr class="bg-primary">
                                                <th style="width: 50%;">ปี</th>
                                                <th style="width: 50%;">เงินปันผลเฉลี่ยคืน</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            if(!empty($dividend_average)){
                                                foreach($dividend_average as $key => $average_return){
                                                    ?>
                                                    <tr>
                                                        <td><?php echo @$average_return['year'];?></td>
                                                        <td><?php echo number_format(@$average_return['average_return'], 2, ".", ","); ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }else{
                                                ?>
                                                <tr><td colspan="2">ไม่พบข้อมูล</td></tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
								</div>
								<div class="tab-pane fade" id="tab3">
									<div class="" style="padding-top:0;">
										<!--<h3 >เงินฝาก</h3>-->
										<div class="g24-col-sm-24">
											<div class="form-group g24-col-sm-8">
												<label class="g24-col-sm-10 control-label ">จำนวนบัญชี</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format(@$count_account); ?>"  readonly>
												</div>
											</div>
											<div class="form-group g24-col-sm-8">
												<label class="g24-col-sm-10 control-label ">ยอดรวมทั้งสิ้น</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format(@$cal_account,2); ?>"  readonly>
												</div>
											</div>
											<div class="form-group g24-col-sm-8">
												
											</div>
										</div>
									</div>
									<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_1">
										<div class="bs-example" data-example-id="striped-table">
											<table class="table table-bordered table-striped table-center">
												<thead> 
													<tr class="bg-primary">
														<th style="width: 5%;">#</th>
														<th style="width: 15%;">เลขที่บัญชี</th>
														<th>ชื่อบัญชี</th>
														<th style="width: 15%;">ยอดเงิน</th>
														<th style="width: 15%;">วงเงินคงเหลือ</th>
														<th style="width: 10%;">สถานะการใช้งาน</th>
													</tr> 
												</thead>
												<tbody>
												<?php
													$i=1;
													if(!empty($data_account)){
													foreach(@$data_account as $key => $row_account){
												?>
													<tr> 
														<td><?php echo $i++; ?></td>
														<td><?php echo $row_account['account_id']; ?></td>
														<td class="text-left"><?php echo $row_account['account_name']; ?></td> 
														<td class="text-right"><?php echo number_format($row_account['transaction_balance'],2); ?></td> 
														<td class="text-right"><?php echo number_format(@$row_account['transaction_balance']-@$row_account['sequester_amount'],2); ?></td> 
														<td>
															<?php 
																if(@$row_account['sequester_status'] == '1' || @$row_account['sequester_status']=='2'){
																	echo 'อายัดบัญชี';
																}else{
																	echo 'ปกติ';
																}
															?>
														</td> 
													</tr>
												<?php }
													}else{ ?>
													<tr><td colspan="6">ไม่พบข้อมูล</td></tr>
													<?php } ?>							
												</tbody> 
											</table> 
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="tab4">
									<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_3" >
										<h3>การกู้เงิน</h3>
									</div>
									<div class="" style="padding-top:0;">										
										<div class="g24-col-sm-24">
											<div class="form-group g24-col-sm-6">
												<label class="g24-col-sm-10 control-label ">จำนวนสัญญา</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format(@$count_loan); ?>"  readonly>
												</div>
											</div>
											<div class="form-group g24-col-sm-6">
												<label class="g24-col-sm-10 control-label ">เงินต้นคงเหลือ</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format(@$sum_loan_balance,2); ?>"  readonly>
												</div>
											</div>
											<div class="form-group g24-col-sm-6">
												<label class="g24-col-sm-10 control-label ">ดอกคงค้าง</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format($interest_debt_balance,2); ?>"  readonly>
												</div>
											</div>
											<div class="form-group g24-col-sm-6">
												<label class="g24-col-sm-10 control-label ">อื่น</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format($other_debt,2); ?>"  readonly>
												</div>
											</div>
										</div>
									</div>
									<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_3" >
										<div class="bs-example" data-example-id="striped-table">
											<table class="table table-bordered table-striped table-center">
												<thead> 
													<tr class="bg-primary">
														<th rowspan="2" style="vertical-align: middle;width: 15%;">ประเภทของสัญญา<br>เงินกู้</th>
														<th rowspan="2" style="vertical-align: middle;width: 8%;">เลขที่<br>สัญญา</th>
														<th rowspan="2" style="vertical-align: middle;width: 7%;">วันที่สั่ง<br>จ่าย</th>
														<th rowspan="2" style="vertical-align: middle;">วงเงินอนุมัติ</th>
														<th rowspan="2" style="vertical-align: middle;">ยอดเงิน<br>คงเหลือ</th>
														<th rowspan="2" style="vertical-align: middle;">รูปแบบการ<br>ผ่อนชำระ</th>
														<th rowspan="2" style="vertical-align: middle;width: 7%;">วันที่ชำระ<br>งวดแรก</th>
														<th rowspan="2" style="vertical-align: middle;">ชำระ<br>ต่องวด</th>
														<th rowspan="2" style="vertical-align: middle;width: 7%;">วันที่ชำระ<br>ล่าสุด</th>
														<th rowspan="2" style="vertical-align: middle;">ยอดเงินหลัง<br>ประมวลผล</th>
														<th rowspan="2" style="vertical-align: middle;">งวดที่</th>
														<th rowspan="2" style="vertical-align: middle;">จัดการ</th>
														<th colspan="2" style="vertical-align: middle;">ผู้ค้ำประกัน</th>
													</tr>
													<tr class="bg-primary">
														<th style="vertical-align: middle;width: 80px;">เลขสมาชิก</th>
														<th style="vertical-align: middle;">ชื่อ-สกุล</th>
													</tr>	
												</thead>
												<tbody>
												<?php
													$i=1;
													$loan_status = array('0'=>'รออนุมัติ', '1'=>'อนุมัติ' , '2'=>'ยื่นขอยกเลิกรายการ', '3'=>'<span style="color:red;">ยกเลิก</span>', '4'=>'ชำระเงินครบถ้วน', '5'=>'ไม่อนุมัติ','6'=>'เบี้ยวหนี้');
													$compromise_type = array('3'=>'หลังมีคำพิพากษา', '4'=>'ก่อนมีคำพิพากษา' , '5'=>'ก่อนมีคำพิพากษา', '6'=>'หลังมีคำพิพากษา');
													if(!empty($rs_loan)){
													foreach(@$rs_loan as $key => $row_loan){
														$this->db->select(array('t1.*','t3.prename_full','t2.firstname_th','t2.lastname_th'));
														$this->db->from('coop_loan_guarantee_person as t1');
														$this->db->join("coop_mem_apply AS t2","t1.guarantee_person_id = t2.member_id","left");
														$this->db->join("coop_prename AS t3","t3.prename_id = t2.prename_id","left");
														$this->db->where("t1.loan_id = '".$row_loan['id']."' AND t1.guarantee_person_id != ''");
														$this->db->order_by("t1.id ASC");
														$rs_guarantee_loan = $this->db->get()->result_array();
														//echo '<pre>'; print_r($rs_guarantee_loan); echo '<pre>';	
														
												?>
													<tr> 
														<td class="text-left"><?php echo @$row_loan['loan_type_detail']." ".@$row_loan['loan_name_description']; ?></td>
														<td>
														<?php 
															$petition_number = "<a href='".base_url(PROJECTPATH.'/loan/'.@$row_loan['petition_file'].'/'.$row_loan['id'])."' target='_blank'>".$row_loan['petition_number']."</a>";
															if(@$row_loan['contract_number']!=''){
																echo @$petition_number."/".@$row_loan['contract_number'];
															}else{
																echo @$petition_number;
															}
															if (array_key_exists($row_loan['compromise_type'], $compromise_type)) echo "(".$compromise_type[$row_loan['compromise_type']].")";
														?>
														</td>
														<td><?php echo @$row_loan['date_transfer']!=''?@$this->center_function->ConvertToThaiDate(@$row_loan['date_transfer']):''; ?></td>
														<td class="text-right green_blue"><?php echo number_format(@$row_loan['loan_amount'],2); ?></td>
														<td class="text-right"><?php echo number_format(@$row_loan['loan_amount_balance'],2); ?></td>  
														<td><?php echo @$pay_type_name[@$row_loan['pay_type']];?></td> 
														<td><?php echo $this->center_function->ConvertToThaiDate(@$row_loan['date_start_period']);?></td>
														<td class="text-right"><?php echo number_format(@$row_loan['total_paid_per_month'],2);?></td>
														<td><?php echo $this->center_function->ConvertToThaiDate(@$row_loan['date_transaction']); ?></td> 
														<td class="text-right green_blue"><?php echo number_format(@$row_loan['process_balance'],2);?></td>
														<td><?php echo @$row_loan['period_count'].'/'.@$row_loan['period_amount'];?></td> 
														<td>
															<div class="dropdown">
																<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" style="width:90%">จัดการ
																<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	<li><a title="ตารางจ่ายเงิน" style="cursor: pointer;padding-left:2px;padding-right:2px" onclick="show_period_table('<?php echo $row_loan['id']?>')"><span class="icon icon-table"></span> ตารางจ่ายเงิน</a></li>
																	<li><a title="รายการหัก" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo PROJECTPATH."/report_loan_data/coop_report_loan_deduct/".$row_loan['id']; ?>" target="_blank"><span class="icon icon-list-alt"></span> รายการหัก</a></li>
																</ul>
															</div>
														</td>
														<td style="vertical-align: TOP;" class="yell_gua" colspan="2">
															<table>								
														<?php
															foreach($rs_guarantee_loan AS $key_guarantee=>$row_guarantee_loan){
														?>
															<tr>
																<td style="vertical-align: TOP;width:75px;">
																	<?php echo @$row_guarantee_loan['guarantee_person_id'];?>
																</td> 
																<td class="text-left " style="vertical-align: TOP;">
																	<?php echo @$row_guarantee_loan['prename_full'].@$row_guarantee_loan['firstname_th'].'  '.@$row_guarantee_loan['lastname_th'];?>
																</td>
															</tr>
														<?php		
															}	
														?>
															</table>
														</td>													
													</tr>
													
													<?php
														$i++; 
														}
													}else{ ?>
													<tr><td colspan="14">ไม่พบข้อมูล</td></tr>
													<?php } ?>
												</tbody> 
											</table>
											
										</div>
									</div>									
									
																		
									<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_3" style="display: none" >
										<h3>การกู้เงินฉุกเฉิน ATM</h3>
										<div class="bs-example" data-example-id="striped-table">
											<table class="table table-bordered table-striped table-center">
												<thead> 
													<tr class="bg-primary">
														<th width="5%">#</th>
														<th width="15%">วันที่ทำรายการ</th>
														<th width="15%">เลขที่คำร้อง/เลขที่สัญญา</th>
														<th width="15%">วงเงินกู้</th>
														<th width="15%">วงกู้คงเหลือ</th>
														<th width="15%">ยอดหนี้คงเหลือ</th>
														<th width="10%">สถานะ</th>
														<th style="width: 10%;">สถานะการใช้งาน</th>
													</tr>
												</thead>
												<tbody>
												<?php if(!empty($row_loan_atm_all)){ $i=1; ?>
													<?php foreach($row_loan_atm_all as $key => $value){ ?>
														<tr>
															<td><?php echo $i++; ?></td>
															<td><?php echo $this->center_function->ConvertToThaiDate(@$value['createdatetime']); ?></td>
															<td>
																<a href="<?php echo base_url(PROJECTPATH.'/loan_atm/petition_emergent_atm_pdf/'.@$value['loan_atm_id']); ?>" target="_blank"><?php echo @$value['petition_number']; ?></a>
																<?php echo (@$value['contract_number']!='')?"/".@$value['contract_number']:''; ?>
															</td>
															<td class="text-right">
																<?php 
																	$total_amount_approve = (@$value['total_amount_approve']>0)?@$value['total_amount_approve']:@$value['total_amount'];
																	echo number_format(@$total_amount_approve,2); 
																?>
															</td>
															<td class="text-right"><?php echo (@$value['total_amount_balance']>@$value['total_amount_approve'])?number_format(@$value['total_amount_approve'],2):number_format(@$value['total_amount_balance'],2); ?></td>
															<td class="text-right"><?php echo number_format(@$total_amount_approve -@$value['total_amount_balance'],2); ?></td>
															<td><?php echo @$loan_atm_status[@$value['loan_atm_status']]; ?></td>
															<td>
															<?php 
																if(@$value['activate_status'] == '1'){
																	echo 'อายัดบัญชี';
																}else{
																	echo 'ปกติ';
																}
															?>
															</td> 
														</tr>
													<?php } ?>
												<?php }else{ ?>
													<tr><td colspan="8" align="center">ไม่พบข้อมูล</td></tr>
												<?php } ?>
												</tbody> 
											</table> 
										</div>
									</div>
								</div>								
								<div class="tab-pane fade" id="tab5">
									<div class="" style="padding-top:0;">
										<!--<h3 >ภาระค้ำประกัน</h3>-->
										<div class="g24-col-sm-24">
											<div class="form-group g24-col-sm-8">
												<label class="g24-col-sm-10 control-label ">จำนวนสัญญา</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format(@$count_contract); ?>"  readonly>
												</div>
											</div>
											<div class="form-group g24-col-sm-8">
												<label class="g24-col-sm-10 control-label ">เงินต้นคงเหลือ</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format(@$sum_guarantee_balance,2); ?>"  readonly>
												</div>
											</div>
											<div class="form-group g24-col-sm-8">
												
											</div>
										</div>
									</div>
									
									<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_2">
										<div class="bs-example" data-example-id="striped-table">
											<table class="table table-bordered table-striped table-center">
												<thead> 
													<tr class="bg-primary">
														<th>#</th>
														<th>ประเภทของสัญญาเงินกู้</th>
														<th>เลขที่สัญญา</th>
														<th>รหัสสมาชิก</th>
														<th>ชื่อสมาชิก</th>
														<th>ภาระค้ำประกัน</th>
														<th>คงเหลือ</th>
													</tr>
												</thead>
												<tbody>
												<?php
											
													$i=1;
													if(!empty($rs_guarantee)){
													foreach(@$rs_guarantee as $key => $row_guarantee){
												?>
													<tr> 
														<td><?php echo $i++; ?></td>
														<td><?php echo @$row_guarantee['loan_type_detail']." ".@$row_guarantee['loan_name_description']; ?></td>
														<td><?php echo $this->center_function->convert_loan_short(@$row_guarantee['loan_type']).$row_guarantee['contract_number']; ?></td>
														<td><?php echo $row_guarantee['member_id']; ?></td> 
														<td><?php echo $row_guarantee['firstname_th']." ".$row_guarantee['lastname_th']; ?></td> 
														<td><?php echo number_format($row_guarantee['loan_amount'],2); ?></td>
														<td><?php echo number_format($row_guarantee['loan_amount_balance'],2); ?></td>
													</tr>
													<?php }
													}else{ ?>
													<tr><td colspan="9">ไม่พบข้อมูล</td></tr>
													<?php } ?>
												</tbody> 
											</table> 
										</div>
									</div>
								</div>								
								<div class="tab-pane fade" id="tab6">
									<div class="" style="padding-top:0;">
										<!--<h3 >รายการเรียกเก็บ</h3>-->
										<div class="g24-col-sm-24">
											<form action="<?php echo base_url(PROJECTPATH.'/manage_member_share/member_loan'); ?>" id="form2" method="GET">
												<input type="hidden" name="member_id" value="<?php echo @$member_id; ?>">
												<input type="hidden" name="tab" value="6">
												<div class="form-group g24-col-sm-8">
													<label class="g24-col-sm-10 control-label ">เดือน</label>
													<div class="g24-col-sm-14">
														<select id="report_month" name="month" class="form-control">
															<option value="">เลือกเดือน</option>
															<?php 
																foreach($month_arr as $key => $value){ 
																//$month = (@$_GET['month'] == '')?date('m'):@$_GET['month']; 
																$month = @$_GET['month']; 
																$selected_month = ($key==$month)?'selected':'';
															?>
																<option value="<?php echo $key; ?>" <?php echo $selected_month; ?>><?php echo $value; ?></option>
															<?php } ?>
														</select>
													</div>
												</div>
												<div class="form-group g24-col-sm-8">
													<label class="g24-col-sm-10 control-label "> ปี</label>
													<div class="g24-col-sm-14">
														<select id="report_year" name="year" class="form-control">
															<?php 
																for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ 
																$year = (@$_GET['year'] == '')?(date('Y')+543):@$_GET['year']; 
																$selected_year = ($i==$year)?'selected':'';
															?>
																<option value="<?php echo $i; ?>" <?php echo $selected_year; ?>><?php echo $i; ?></option>
															<?php } ?>
														</select>
													</div>
												</div>
												<div class="form-group g24-col-sm-8">
													<button class="btn btn-primary btn-after-input" type="submit"><span> แสดงผล</span></button>
												</div>
											</form>	
										</div>										
										
										<div class="g24-col-sm-24">
											<div class="form-group g24-col-sm-8">
												<label class="g24-col-sm-10 control-label ">เดือน/ปี</label>
												<div class="g24-col-sm-14">
													<?php
														$mm_yy = "";
														$mm_yy .= (@$finance_month != '')?@$month_arr[@$finance_month]."/":"";
														$mm_yy .= (@$finance_year != '')?@$finance_year:"";
													?>
													<input class="form-control" type="text" value="<?php echo $mm_yy; ?>"  readonly>
												</div>
											</div>
											<div class="form-group g24-col-sm-8">
												<label class="g24-col-sm-10 control-label ">จำนวนเงิน</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format(@$pay_amount_all,2); ?>"  readonly>
												</div>
											</div>
											<div class="form-group g24-col-sm-8">
												
											</div>
										</div>
									</div>
									<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_5">
										<div class="bs-example" data-example-id="striped-table">
											<table class="table table-bordered table-striped table-center">
												<thead> 
													<tr class="bg-primary">
														<th style="width:100px;">ลำดับ</th>
														<th>เดือน/ปี</th>
														<th>ยอดเรียกเก็บ</th>
														<th>ค้างชำระ</th>
														<th>เลขที่เอกสาร</th>
														<th>สถานะ</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
												<?php
													$i=1;
													if(!empty($rs_finance_detail)){
													foreach(@$rs_finance_detail as $key => $row_finance_detail){
														
												?>
													<tr> 
														<td><?php echo $i++; ?></td>										
														<td><?php echo @$month_arr[@$row_finance_detail['profile_month']]."/".@$row_finance_detail['profile_year']; ?></td> 
														<td><?php echo number_format(@$row_finance_detail['pay_amount'],2); ?></td>
														<td><?php echo number_format(@$row_finance_detail['non_pay_amount_balance'],2); ?></td>
														<td>
															<?php
																$is_resign = 0;
																if(!empty($row_finance_detail['receipt_id'])){
																	foreach($row_finance_detail['receipt_id'] AS $receipt){
																		$is_void = ($receipt['receipt_status']==1) ? "style='color: red;'" : "";
																		
																		if(!empty($receipt["req_resign_id"])) {
																			$is_resign = 1;
																		}
																?>
																	<a href="<?php echo base_url(PROJECTPATH.'/admin/receipt_form_pdf/'.@$receipt['receipt_id']); ?>" target="_blank" <?=$is_void?> title="<?=$title ?>"><?php echo @$receipt['receipt_id'];?></a>
																	<br>
																<?php 
																	}
																}
															?>
														</td>
														<td>
															<?php 
																$text_receipt_status = '';
																if(!empty($row_finance_detail['receipt_id'])){	
																	if(@$row_finance_detail['non_pay_id'] != ''){
																		$pay_amount_balance = @$row_finance_detail['pay_amount']-@$row_finance_detail['non_pay_amount_balance'];
																		if(@$row_finance_detail['pay_amount'] == @$row_finance_detail['non_pay_amount_balance']){
																			$text_receipt_status = 'ยังไม่ได้ชำระ';
																		}elseif(@$row_finance_detail['non_pay_amount_balance'] == 0){
																			$text_receipt_status = 'ชำระแล้ว';
																		}else{
																			$text_receipt_status = 'ชำระบางส่วน';
																		}	
																	}else{
																		$text_receipt_status = 'ชำระแล้ว';
																	}
																}else{
																	$text_receipt_status = 'ยังไม่ได้ชำระ';
																}
																echo $text_receipt_status;
															?>
														</td>
														<td>
															<a title="ดูรายการ" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="view_finance_detail('<?php echo @$member_id;?>','<?php echo @$row_finance_detail['profile_month'];?>','<?php echo @$row_finance_detail['profile_year']; ?>','<?php echo @$month_arr[@$row_finance_detail['profile_month']]; ?>')"> ดูรายการ</a></li>
														</td>
													</tr>
													<?php }
													}else{ ?>
													<tr><td colspan="6">ไม่พบข้อมูล</td></tr>
													<?php } ?>
												</tbody> 
											</table> 
										</div>
									</div>	
								</div>								
								<div class="tab-pane fade" id="tab7">
									<div class="" style="padding-top:0;">
										<!--<h3 >ประวัติการผิดนัดชำระ</h3>-->
										<div class="g24-col-sm-24">
											<div class="form-group g24-col-sm-8">
												<label class="g24-col-sm-10 control-label ">จำนวนงวด</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format(@$count_debt); ?>"  readonly>
												</div>
											</div>
											<div class="form-group g24-col-sm-8">
												<label class="g24-col-sm-10 control-label ">ยอดรวมหนี้</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format(@$sum_debt_balance,2); ?>"  readonly>
												</div>
											</div>
											<div class="form-group g24-col-sm-8">
												<label class="g24-col-sm-10 control-label ">รวมยอดค้างชำระ</label>
												<div class="g24-col-sm-14">
													<input class="form-control" type="text" value="<?php echo number_format(@$sum_debt_all,2); ?>"  readonly>
												</div>
											</div>
										</div>
									</div>
									<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_4">
										<div class="bs-example" data-example-id="striped-table">
											<table class="table table-bordered table-striped table-center">
												<thead> 
													<tr class="bg-primary">
														<th>ครั้งที่</th>
														<th>งวด</th>
														<th>สัญญาเลขที่</th>
														<th>ยอดเรียกเก็บ</th>
														<th>ค้างชำระ</th>
														<th>สถานะ</th>
													</tr>
												</thead>
												<tbody>
												<?php
													$arr_status = array(
																	'1'=>'ยังไม่ได้ชำระ',
																	'2'=>'ชำระแล้ว',
																	'3'=>'ออกจดหมายเตือน',
																	'6'=>'ให้ออกจากสมาชิก',
																	);
						
													$i=1;
													//echo '<pre>'; print_r($rs_debt); echo '</pre>';
													if(!empty($rs_debt)){
													foreach(@$rs_debt as $key => $row_debt){
															$non_pay_year_month = @$row_debt['non_pay_year'].'/'.@$month_arr[$row_debt['non_pay_month']];															
												?>
														<tr> 
															<td><?php echo @$i++; ?></td>
															<td><?php echo @$non_pay_year_month; ?></td>
															<td><?php echo @$row_debt['contract_number']; ?></td> 
															<td><?php echo number_format(@$row_debt['pay_amount'],2); ?></td> 
															<td><?php echo number_format(@$row_debt['non_pay_amount_balance'],2).@$row_debt['non_pay_detail'] ; ?></td>
															<td>
																<?php 
																	$text_time = (@$row_debt['non_pay_status'] == "3")?" ครั้งที่ ".@$letter_runno:"";
																	echo @$arr_status[$row_debt['non_pay_status']].@$text_time;
																?>
															</td>
														</tr>
													<?php 
														}
													}else{ ?>
													<tr><td colspan="9">ไม่พบข้อมูล</td></tr>
													<?php } ?>
												</tbody> 
											</table> 
										</div>
									</div>
								</div>								
								<div class="tab-pane fade" id="tab8">
									<?php $this->load->view('manage_member_share/member_life_insurance'); ?>									
								</div>								
								<div class="tab-pane fade" id="tab9">
								
								</div>							
								<div class="tab-pane fade" id="tab10">
									<?php $this->load->view('manage_member_share/member_outgoing'); ?>
								</div>							
								<div class="tab-pane fade" id="tab11">
									<table class="table table-bordered table-striped table-center">
										<thead>
											<tr class="bg-primary">
												<th style="width:100px;">ลำดับ</th>
												<th>วันที่</th>
												<th>เงินสงเคราะห์คงเหลือ</th>
												<th>เรียกเก็บ</th>
												<th>รวม</th>
												<th>เลขที่ใบเสร็จ</th>
												<th>ผู้ทำรายการ</th>
											</tr>
										</thead>
										<tbody>
										<?php
											$i=1;
											if(!empty($cremation_month_transactions)){
											foreach(@$cremation_month_transactions as $key => $transaction){
												$cremation_balance = ($transaction["advance_pay"] * $transaction["member_count"]) - $transaction["pay_amount"];
										?>
											<tr>
												<td><?php echo $i++; ?></td>
												<td><?php echo $this->center_function->ConvertToThaiDate(@$transaction['create_datetime']); ?></td>
												<td><?php echo number_format($cremation_balance,2); ?></td>
												<td><?php echo number_format($transaction["pay_amount"],2); ?></td>
												<td><?php echo number_format(($transaction["advance_pay"] * $transaction["member_count"]),2); ?></td>
												<td>
													<?php if(!empty($transaction["receipt_id"])) { ?>
													<a href="<?php echo base_url(PROJECTPATH.'/admin/receipt_form_pdf/'.$transaction['receipt_id']); ?>" target="_blank"><?php echo $transaction['receipt_id'];?></a>
													<?php } ?>
												</td>
												<td><?php echo $transaction["user_name"];?></td>
											</tr>
											<?php }
											}else{ ?>
											<tr><td colspan="6">ไม่พบข้อมูล</td></tr>
											<?php } ?>
										</tbody>
									</table>
								</div>						
								<div class="tab-pane fade" id="tab12">
									<div class="" style="padding-top:0;">
										<!--<h3>หมายเหตุ</h3>-->
										<form class="form-horizontal" id="frm_problem" action="<?php echo base_url(PROJECTPATH.'/manage_member_share/note_save');?>" method="post"  enctype="multipart/form-data" >
											<input type="hidden" name="member_id" id="member_id" value="<?php echo @$member_id; ?>">
											<input type="hidden" name="tab" value="12">
											<div class="g24-col-sm-24">
												<textarea id="note" name="note" ><?php echo (@$note != '')?@$note:''; ?></textarea>
											</div>
											<div class="g24-col-sm-24 m-t-1 text-right">
												<button type="submit" class="btn btn-primary" style="margin-left:5px;" value="save">บันทึกข้อมูล</button>
											</div>
										</form>
									</div>									
								</div>
                                  <div class="tab-pane fade" id="tab13">
                                      <div class="" style="padding-top:0;">
                                          <!--<h3>หมายเหตุ</h3>-->
                                          <form class="form-horizontal" id="frm_court_writ" action="<?php echo base_url(PROJECTPATH.'/manage_member_share/court_writ_note_save');?>" method="post"  enctype="multipart/form-data" >
                                              <input type="hidden" name="member_id" id="member_id" value="<?php echo @$member_id; ?>">
                                              <input type="hidden" name="tab" value="13">
                                              <div class="g24-col-sm-24">
                                                  <textarea id="court_writ_note" name="court_writ_note" ><?php echo (@$row_member['court_writ_note'] != '')?@$row_member['court_writ_note']:''; ?></textarea>
                                              </div>
                                              <div class="g24-col-sm-24 m-t-1 text-right">
                                                  <button type="submit" class="btn btn-primary" style="margin-left:5px;" value="save">บันทึกข้อมูล</button>
                                              </div>
                                          </form>
                                      </div>
                                  </div>
								  <div class="tab-pane fade" id="tab14">
									  <div style="padding-top: 0">
									  	<form class="form-horizontal" id="frm_addr_note" action="<?php echo base_url(PROJECTPATH.'/manage_member_share/address_note_save');?>" method="post" enctype="multipart/form-data">
											<input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
											<input type="hidden" name="tab" value="14">
											<div class="g24-col-sm-24">
												<textarea id="note_address" name="note_address"><?php echo (@$row_member['note_address'] != '')?@$row_member['note_address']:''; ?></textarea>
											</div>
											<div class="g24-col-sm-24 m-t-1 text-right">
												<button type="submit" class="btn btn-primary" style="margin-left:5px;" value="save">บันทึกข้อมูล</button>
											</div>
										</form>
									  </div>
								  </div>
                                  <div class="tab-pane fade" id="tab15">
                                      <div style="padding-top: 0">
                                          <form class="form-horizontal" id="frm_addr_note" action="<?php echo base_url(PROJECTPATH.'/manage_member_share/address_send_doc_save');?>" method="post" enctype="multipart/form-data">
                                              <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
                                              <input type="hidden" name="tab" value="14">
                                              <div class="g24-col-sm-24">
                                                  <textarea id="note_address_send_doc" name="note_address_send_doc"><?php echo (@$row_member['address_send_doc'] != '')?@$row_member['address_send_doc']:''; ?></textarea>
                                              </div>
                                              <div class="g24-col-sm-24 m-t-1 text-right">
                                                  <button type="submit" class="btn btn-primary" style="margin-left:5px;" value="save">บันทึกข้อมูล</button>
                                              </div>
                                          </form>
                                      </div>
                                  </div>
							  </div>
							</div>
						</div>
					</div>
					
					
					<input type="hidden" id="show_status_1" value="">
					<input type="hidden" id="show_status_2" value="">
					<input type="hidden" id="show_status_3" value="1">
					<input type="hidden" id="show_status_4" value="">
					<input type="hidden" id="show_status_5" value="">
					
					<!--div class="g24-col-sm-24">
					<?php if($member_id!=''){ ?>
						<div class="g24-col-sm-5">
							<select id="loan_type_choose" class="form-control" onchange="change_type()">
								<option value="">เลือกประเภทการกู้เงิน</option>
								<?php foreach($rs_loan_type as $key => $value){ ?>
									<option value="<?php echo $value['id']; ?>" ><?php echo $value['loan_type']; ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="g24-col-sm-5">
							<select id="loan_type_select" class="form-control">
								<option value="">เลือกชื่อเงินกู้</option>
							</select>
						</div>
						<div class="g24-col-sm-12">
							<a class="link-line-none" id="normal_loan_btn" onclick="change_modal()">
								<button class="btn btn-primary" style="margin-right:5px;">เพิ่มคำร้อง</button>
							</a>
						</div>
					<?php } ?>
					</div-->
				</div>
			</div>
		</div>
		<?php } ?>
		<?php 
			if(!empty($rs_rule)){
				foreach($rs_rule as $key => $row_rule){ ?>
					<input 
						type="hidden" 
						id = "loan_rule_<?php echo $row_rule['type_id']; ?>" 
						type_id = "<?php echo $row_rule['type_id']; ?>" 
						type_name = "<?php echo $row_rule['type_name']; ?>" 
						credit_limit = "<?php echo $row_rule['credit_limit']; ?>" 
						less_than_multiple_salary = "<?php echo $row_rule['less_than_multiple_salary']; ?>" 
						num_guarantee = "<?php echo $row_rule['num_guarantee']; ?>" 
						percent_share_guarantee = "<?php echo $row_rule['percent_share_guarantee']; ?>" 
						percent_fund_quarantee = "<?php echo $row_rule['percent_fund_quarantee']; ?>" 
						interest_rate = "<?php echo $row_rule['interest_rate']; ?>" 
					>
			<?php 
				} 
			}
			?>
		<input type="hidden" id="share_value" value="<?php echo $share_value; ?>">
	</div>
</div>
<?php $this->load->view('search_member_new_modal'); ?>
<form action="<?php echo base_url(PROJECTPATH.'/loan/coop_loan_save?member='.@$member_id); ?>" method="POST" id="form_normal_loan" enctype="multipart/form-data">
	<div class="modal fade" id="normal_loan" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
		<div class="modal-dialog modal-dialog-data">
		  <div class="modal-content data_modal">
				<div class="modal-header modal-header-confirmSave">
				  <button type="button" class="close" data-dismiss="modal">x</button>
				  <h2 class="modal-title" id="type_name">กู้เงินสามัญ</h2>
				</div>
				<div class="modal-body">
					<?php $this->load->view('loan/normal_loan_modal'); ?>
			</div>
		  </div>
		</div>
	</div>
	<div class="modal fade cal_period" id="cal_period_normal_loan" role="dialog">
		<div class="modal-dialog modal-dialog-cal">
		  <div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
			  <button type="button" class="close" onclick="close_modal('cal_period_normal_loan')">&times;</button>
			  <h2 class="modal-title">คำนวณการส่งค่างวด</h2>
			</div>
			<div class="modal-body">
				<?php $this->load->view('loan/calculate_loan'); ?>
			</div>
		  </div>
		</div>
	</div>
	<div class="modal fade" id="show_file_attach" role="dialog">
		<div class="modal-dialog modal-dialog-file">
		  <div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
			  <button type="button" class="close" onclick="close_modal('show_file_attach')">&times;</button>
			  <h2 class="modal-title">แสดงไฟล์แนบ</h2>
			</div>
			<div class="modal-body" id="show_file_space">
			</div>
		  </div>
		</div>
	</div>
</form>
<div class="modal fade" id="search_member_loan_modal" role="dialog"> 
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">ข้อมูลสมาชิก</h4>
        </div>
        <div class="modal-body">
       		<div class="input-with-icon">
					  <input class="form-control input-thick pill m-b-2" type="text" placeholder="กรอกเลขทะเบียนหรือชื่อ-สกุล" name="search_text" id="search_member_loan">
					  <span class="icon icon-search input-icon"></span>
					</div>

			<div class="bs-example" data-example-id="striped-table">
				 <table class="table table-striped">
					<tbody id="result_member_search">
					</tbody>
				</table>
			</div>
        </div>
        <div class="modal-footer">
			<input type="hidden" id="input_id">
			<button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="guarantee_person_data_modal" role="dialog"> 
    <div class="modal-dialog modal-dialog-file">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3 class="modal-title">ข้อมูลผู้ค้ำประกัน</h3>
        </div>
        <div class="modal-body">
			<div class="bs-example" data-example-id="striped-table" id="guarantee_person_data">
			
			</div>
        </div>
        <div class="modal-footer">
			<button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="period_table" role="dialog">
    <div class="modal-dialog modal-dialog-data">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" onclick="close_modal('period_table')">&times;</button>
          <h2 class="modal-title" id="type_name">ตารางคำนวณการชำระเงิน</h2>
        </div>
        <div class="modal-body period_table">
			
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="check_term_of_loan_result_modal" role="dialog">
    <div class="modal-dialog modal-dialog-file">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" onclick="close_modal('check_term_of_loan_result_modal')">&times;</button>
          <h2 class="modal-title" id="type_name">ตรวจสอบเงื่อนไขการกู้เงิน</h2>
        </div>
        <div class="modal-body">
		<div id="check_term_of_loan_result"></div>
		<div class="center"> 
			<button type="button" class="btn btn-primary" style="width:100px" onclick="submit_form()">ทำรายการต่อ</button> 
			<button type="button" style="width:100px" class="btn btn-danger" onclick="close_modal('check_term_of_loan_result_modal')">ยกเลิก</button>
		</div>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="check_term_of_loan_before_result_modal" role="dialog">
    <div class="modal-dialog modal-dialog-file">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" onclick="close_modal('check_term_of_loan_before_result_modal')">&times;</button>
          <h2 class="modal-title" id="type_name">ตรวจสอบเงื่อนไขการกู้เงิน</h2>
        </div>
        <div class="modal-body">
		<div id="check_term_of_loan_before_result"></div>
		<div class="center"> 
			<button type="button" class="btn btn-primary" style="width:100px" onclick="open_modal('normal_loan')">ทำรายการต่อ</button> 
			<button type="button" style="width:100px" class="btn btn-danger" onclick="close_modal('check_term_of_loan_before_result_modal')">ยกเลิก</button>
		</div>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="update_salary_modal" role="dialog"> 
    <div class="modal-dialog modal-dialog-file">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3 class="modal-title">อัพเดทรายได้สมาชิก</h3>
        </div>
        <div class="modal-body">
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label col-sm-4">เงินเดือน</label>
						<div class="col-sm-4">
							<input type="text" id="update_salary" class="form-control" value="<?php echo number_format($row_member['salary']); ?>" onkeyup="format_the_number(this)">
						</div>
					</div>
				</div>
			</div>
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label col-sm-4">รายได้อื่นๆ</label>
						<div class="col-sm-4">
							<input type="text" id="update_other_income" class="form-control" value="<?php echo number_format($row_member['other_income']); ?>" onkeyup="format_the_number(this)">
						</div>
					</div>
				</div>
			</div>
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group" style="text-align:center">
						<input type="button" class="btn btn-primary" value="บันทึก" onclick="update_salary();">
					</div>
				</div>
			</div>
        </div>
      </div>
    </div>
</div>

<div class="modal fade" id="finance_detail_modal" role="dialog"> 
    <div class="modal-dialog modal-dialog-file">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3 class="modal-title">รายการเรียกเก็บ</h3>
        </div>
        <div class="modal-body">
			<div class="row m-b-1">
				<div class="col-sm-12">
				<h4 class="modal-title">รหัสสมาชิก <span id="member_id_show"></span> เดือน <span id="month_show"></span> ปี พ.ศ.<span id="year_show"></span></h4>
					<table class="table table-bordered table-striped table-center">
						<thead> 
							<tr class="bg-primary">
								<th width="80">ลำดับ</th>
								<th>รายการเรียกเก็บ</th>
								<th width="150">ยอดเงิน</th>
							</tr>
						</thead>
						<tbody id="table_data_debt">

						</tbody>
					</table>
				</div>
			</div>			
        </div>
      </div>
    </div>
</div>

<!-- update_transaction -->
<div id="update_transaction" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-account">
		<div class="modal-content">
			<div class="modal-header modal-header-deposit">
				<h2 class="modal-title">อัพเดทยอดคงเหลือ</h2>
			</div>
			<div class="modal-body">
				<form action="?" method="POST">
					<input type="hidden" name="update_account_id"  value="<?=@$row_memberall['account_id']?>" id="update_account_id">
					<div class="g24-col-sm-24">
						<div class="form-group">
							<label for="money" class="control-label g24-col-sm-7">เลือกวันที่เริ่มการอัพเดท</label>
							<div class="g24-col-sm-5">
								<select name="update_day" id="update_day" class="form-control" required>
								<option value="">เลือกวันที่</option>
									<?php
										for ($i=1; $i <= 31; $i++) { 
											echo "<option value='".sprintf('%02d', $i)."'>".sprintf('%02d', $i)."</option>";
										}
									?>
								</select>
							</div>
							<div class="g24-col-sm-5">
								<select name="update_day" id="update_month" class="form-control" required>
								<option value="">เลือกเดือน</option>
									<?php
										for ($i=1; $i <= 12; $i++) { 
											echo "<option value='".sprintf('%02d', $i)."'>".sprintf('%02d', $i)."</option>";
										}
									?>
								</select>
							</div>
							<div class="g24-col-sm-5">
								<select name="update_day" id="update_year" class="form-control" required>
								<option value="">เลือกปี</option>
									<?php
										for ($i=(date('Y')+543); $i >= (date('Y')+543-10); $i--) { 
											echo "<option value='$i'>$i</option>";
										}
									?>
								</select>
							</div>
							<label class="control-label g24-col-sm-4">&nbsp;</label>
							
						</div>

						<label class="g24-col-sm-24"><i class="fa fa-info"></i> วิธีอัพเดท ให้เลือกวันที่ก่อนหน้า รายการที่ยอดคงเหลือผิด 1 รายการ</label>

						<div class="form-group">
							<div class="g24-col-sm-24 text-center m-t-2">
								<button class="btn btn-primary"  type="button" id="update_confirm">อัพเดท</button>
								<button class="btn btn-default bt_close" data-dismiss="modal" type="button">ยกเลิก </button>								
							</div>
						</div>
					</div>
				</form>
				<div>&nbsp;</div>
			</div>
		</div>
	</div>
</div>

<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/loan.js',
    'type' => 'text/javascript'
);
echo script_tag($link);

$link = array(
    'src' => PROJECTJSPATH.'assets/ckeditor/ckeditor.js',
    'type' => 'text/javascript'
);
echo script_tag($link);

$link = array(
    'src' => PROJECTJSPATH.'assets/ckeditor/adapters/jquery.js',
    'type' => 'text/javascript'
);
echo script_tag($link);

?>

<script>
$(document).ready(function() {		
	if($("#note").length) {
		$("#note").ckeditor({ height : 400 , customConfig : '<?php echo PROJECTPATH; ?>/assets/ckeditor/config-admin.js'   });
	}

    if($("#court_writ_note").length) {
        $("#court_writ_note").ckeditor({ height : 400 , customConfig : '<?php echo PROJECTPATH; ?>/assets/ckeditor/config-admin.js'   });
    }

	if($("#note_address").length) {
		$("#note_address").ckeditor({ height : 400 , customConfig : '<?php echo PROJECTPATH; ?>/assets/ckeditor/config-admin.js'   });
	}

    if($("#note_address_send_doc").length) {
        $("#note_address_send_doc").ckeditor({ height : 400 , customConfig : '<?php echo PROJECTPATH; ?>/assets/ckeditor/config-admin.js'   });
    }

    var t_tab = '<?php echo @$_GET['tab']?>';
	if(t_tab == ''){
		t_tab = '1';
	}
	$("#t"+t_tab).toggleClass("active");
	$("#tab"+t_tab).toggleClass(" active in");
});		

function view_finance_detail(member_id,profile_month,profile_year,text_month){
	$.ajax({
		 url:base_url+"/manage_member_share/get_finance_month_detail",
		 method:"post",
		 data:{ 
			member_id: member_id,	
			profile_month: profile_month,	
			profile_year: profile_year,	
		 },
		 dataType:"text",
		 success:function(data)
		 {
			console.log(data);
			$('#table_data_debt').html(data);
			$('#member_id_show').html(member_id);
            $('#year_show').html(profile_year);
            $('#month_show').html(text_month);
			$('#finance_detail_modal').modal('show');
		 }
	});		
}

function read_more(){
	var note = $("#note_remark").val();
	if(note!=''){
		swal('หมายเหตุ', note);
	}
	
}

$("#update_confirm" ).click(function(){
	var d = $("#update_day").val();
	var m = $("#update_month").val();
	var y = $("#update_year").val();

	if(d=="" || m=="" || y==""){
		swal("เลือกวันที่ถูกต้อง", "warming");
		return;
	}

	$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/update_transaction_share',
			data: {
				date : (y-543) + '-' + m + '-' + d,
				member_id : $(".member_id").val()
			},
			success: function(data){
				console.log(data);
				if(data=="success"){
					
					swal("อัพเดทสำเร็จ", "อัพเดทข้อมูลเรียบร้อย", "success");
					setTimeout(() => {
						location.reload();
					}, 500);
					
				}else if(data=="fail"){
					swal("ไม่สามารถอัพเดทได้ ตรวจสอบวันที่ให้ถูกต้อง");
				}else{
					swal("ไม่สามารถอัพเดทได้ ตรวจสอบวันที่ให้ถูกต้อง");
				}

			}
	});	


});
</script>
