<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.bt-add{
				float:none;
			}
			.input-with-icon .form-control{
				padding-left: 40px;
			}
			input[type=file]{
				margin-left: -8px;
			}
			.input-with-icon {
				margin-bottom: 5px;
			}
			.input-with-icon .form-control{
				padding-left: 40px;
			}
			.modal_data_input{
				margin-left:-5px;
			}
			.scrollbar {
				height: 360px;
			}
		</style>

		<h1 style="margin-bottom: 0">ชำระหนี้คงค้าง</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">

		</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<form data-toggle="validator" method="get" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="myForm">
						<div class="m-t-1">
							<div class="g24-col-sm-20">
								<div class="form-group">
									<label class="g24-col-sm-4 control-label">เลขฌาปนกิจสงเคราะห์ <span id="naja"></span> </label>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<div class="input-group">
												<input id="member_cremation_id" name="member_cremation_id" class="form-control" style="text-align:left;" type="number" value="<?php echo empty($_GET['member_cremation_id']) ? '': $_GET['member_cremation_id']; ?>" title="" />
												<span class="input-group-btn">
													<a data-toggle="modal" data-target="#cremation-search-modal" id="modal-search" class="fancybox_share fancybox.iframe" href="#">
														<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span>
														</button>
													</a>
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="g24-col-sm-20">
								<div class="form-group">
									<label class="g24-col-sm-4 control-label"> เดือน </label>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<select id="month" name="month" class="form-control">
												<option value=""></option>
												<?php foreach($this->month_arr as $key => $value){ ?>
													<option value="<?php echo $key; ?>" <?php echo !empty($_GET["month"]) && $_GET["month"] == $key ? 'selected' : ''; ?>><?php echo $value; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<label class="g24-col-sm-4 control-label right"> ปี </label>
									<div class="g24-col-sm-6">
										<select id="year" name="year" class="form-control">
											<option value=""></option>
											<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
												<option value="<?php echo $i; ?>" <?php echo !empty($_GET["year"]) && $_GET["year"] == $i ?'selected':''; ?>><?php echo $i; ?></option>
											<?php } ?>
									</select>
									</div>
								</div>
							</div>
							<div class="g24-col-sm-20">
								<label class="g24-col-sm-4 control-label right"> สถานะ </label>
								<div class="g24-col-sm-20">
									<label class="radio-inline">
										<input type="radio" name="type" value="" id="select_all" <?php echo empty($_GET["type"]) ? 'checked' : '';?>> เลือกทั้งหมด
									</label>
									<label class="radio-inline">
										<input type="radio" name="type" value="paid" id="select_paid" <?php echo $_GET["type"] == "paid" ? 'checked' : '';?>> ชำระแล้ว
									</label>
									<label class="radio-inline">
										<input type="radio" name="type" value="non_pay" id="select_non_paid" <?php echo $_GET["type"] == "non_pay" ? 'checked' : '';?>> ค้างชำระ
									</label>
								</div>
							</div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20">
								<label class="g24-col-sm-4 control-label right"></label>
								<div class="g24-col-sm-20">
									<input type="submit" class="btn btn-primary" id="find-btn" value="ค้นหา"/>
								</div>
							</div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
							<div class="g24-col-sm-20"></div>
						</div>
					</form>
					<div class="bs-example" data-example-id="striped-table">
						<div id="tb_wrap">
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th>ลำดับ</th>
										<th>ปี/เดือน</th>
										<th>รหัสสมาชิก</th>
										<th>ชื่อสกุล</th>
										<th>ยอดเรียกเก็บ</th>
										<th>ค้างชำระ</th>
										<th>วันที่ทำรายการ</th>
										<th>ผู้ทำรายการ</th>
										<th style="width: 130px;">เลขที่ใบเสร็จ</th>
										<th style="width: 130px;"></th>
									</tr>
								</thead>
								<tbody>
								<?php
									$i=1;
									if(!empty($row)){
										foreach(@$row as $key => $row_debt){

									?>
										<tr>
											<td><?php echo $i++;?></td>
											<td><?php echo !empty($row_debt["profile_id"]) ? $row_debt["year"]."/".$this->month_arr[(int) $row_debt["month"]] : $row_debt["debt_year"];?></td>
											<td><?php echo $row_debt['member_cremation_id'];?></td>
											<td class="text-left"><?php echo $row_debt['firstname_th'].'  '.$row_debt['lastname_th'];?></td>
											<td class="text-right"><?php echo number_format($row_debt["pay_amount"],2);?></td>
											<td class="text-right"><?php echo number_format($row_debt["pay_amount"] - $row_debt["real_pay_amount"],2);?></td>
											<td><?php echo $this->center_function->ConvertToThaiDate($row_debt['receipts'][0]["created_at"],1,0);?></td>
											<td><?php echo $row_debt['receipts'][0]["user_name"];?></td>
											<td>
												<?php
                                                    foreach($row_debt['receipts'] as $receipt) {
												?>
												<a href="<?php echo base_url(PROJECTPATH.'/cremation/receipt_form_pdf/'.$receipt['receipt_id']); ?>" target="_blank"><?php echo $receipt['receipt_id'];?></a>
												<?php
                                                    }
												?>
											</td>
											<td>
												<?php
												if($row_debt["pay_amount"] == $row_debt["real_pay_amount"]){
														echo 'ชำระแล้ว';
												} else {
												?>
												<button id="bt_add_<?php echo $row_debt["id"];?>" type="button" class="btn btn-primary bt_add" data-id="<?php echo $row_debt["id"];?>">
													<span>ชำระหนี้คงค้าง</span>
												</button>
												<?php }?>
											</td>
										</tr>
									<?php
										}
									}else{ ?>
										<tr><td colspan="10">ไม่พบข้อมูล</td></tr>
									<?php } ?>
								</tbody> 
							</table>
						</div>
					</div>
					<?php echo @$paging ?>
				</div>
			</div>
		</div>
    </div>
</div>
<div class="modal fade" id="cremation-search-modal" role="dialog">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">ฌาปนกิจสงเคราะห์</h4>
			</div>
			<div class="modal-body">
				<div class="input-with-icon">
					<div class="row">
						<div class="col">
							<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select id="cre_search_list" name="search_list" class="form-control m-b-1">
										<option value="">เลือกรูปแบบค้นหา</option>
										<option value="cremation_no">เลขที่คำร้อง</option>
										<option value="member_cremation_id">เลขฌาปนกิจสงเคราะห์</option>
										<option value="member_id">รหัสสมาชิก</option>
										<option value="id_card">หมายเลขบัตรประชาชน</option>
										<option value="firstname_th">ชื่อสมาชิก</option>
									</select>
								</div>
							</div>
							<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
							<div class="col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="cre_search_text" name="search_text" class="form-control m-b-1" type="text" value="">
										<span class="input-group-btn">
											<button type="button" id="cremation_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="bs-example" data-example-id="striped-table">
					<table class="table table-striped">
						<thead>
							<th class="text-center">เลขที่คำร้อง</th>
							<th class="text-center">เลขฌาปนกิจสงเคราะห์</th>
							<th class="text-center">รหัสสมาชิก</th>
							<th class="text-center">ชื่อสมาชิก</th>
							<th></th>
						</thead>
						<tbody id="cre-table_data">
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
<form method="post" action="<?php echo base_url(PROJECTPATH.'/cremation/save_debt_payment'); ?>" id="payment_form">
    <input type="hidden" name="id" id="form-id" value=""/>
</form>
<script>
    $(document).ready(function() {
        $(".bt_add").click(function(){
            id = $(this).attr("data-id");
			swal({
				title: 'ชำระหนี้คงค้าง',
				text: "",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#0288d1',
				confirmButtonText: 'ยืนยัน',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: false,
				closeOnCancel: true
			},
			function(isConfirm) {
				if (isConfirm) {
					$("#form-id").val(id);
           			$("#payment_form").submit();
				}
			});
        });

        $("#cremation_search").click(function() {
			if($('#cre_search_list').val() == '') {
				swal('กรุณาเลือกรูปแบบค้นหา','','warning');
			} else if ($('#cre_search_text').val() == ''){
				swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
			} else {
				is_member = $("#modal-type").val() == '2' ? 1 : 0;
				$.ajax({
					url: base_url+"cremation/search_cremation_by_type_jquery",
					method:"post",
					data: {
						search_text : $('#cre_search_text').val(), 
						search_list : $('#cre_search_list').val(),
						is_member : is_member
					},
					dataType:"text",
					success:function(data) {
						$('#cre-table_data').html(data);
					},
					error: function(xhr){
						console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
					}
				});
			}
		});

		$(document).on('click','.cre-modal-btn',function(){
			$("#member_cremation_id").val($(this).attr("data-member-cremation-id"));
			$("#cremation-search-modal").modal('hide');
		});
    });
</script>
