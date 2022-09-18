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
			
			.print{display: none;}
			
			@media print {	
				@font-face {
					font-family: 'upbean';
					src: url('/assets/css/fonts/upbean/UpBean Regular Ver 1.00.ttf');
				}
				@font-face {
					font-family: 'THSarabunNew';
					src: url('/assets/css/fonts/thsarabunnew/thsarabunnew-webfont.eot');
					src: url('/assets/css/fonts/thsarabunnew/thsarabunnew-webfont.eot?#iefix') format('embedded-opentype'),
						 url('/assets/css/fonts/thsarabunnew/thsarabunnew-webfont.woff') format('woff'),
						 url('/assets/css/fonts/thsarabunnew/thsarabunnew-webfont.ttf') format('truetype');
					font-weight: normal;
					font-style: normal;
				}
				h3 {
					font-family: upbean;
				}
				.table>thead>tr>th{
					font-family: upbean;
					font-size: 18px;
					padding: 6px;
				}
				.no_print{display: none;}
				.print{display: initial;}
				.table-view>tbody>tr>td, 
				.table-view>tbody>tr>th, 
				.table-view>tfoot>tr>td, 
				.table-view>tfoot>tr>th, 
				.table-view>thead,
				.table-view>thead>tr>td, 
				.table-view>thead>tr>th {
					border: 1px solid #000;
				}
				
				.table-view>tfoot>tr>td{
					background-color: #fff;
					border: 0px;
				} 
						
				.m-f-1{
					margin-left: 1em;
				}
				
				.m-t-2{
					margin-top: 2em;
				}
				
				.page-break { page-break-before: always; }
				span{font-family: upbean;font-size: 14px;}
				
				@page { margin: 0px 0px 0px 0px;}
				
				.pagination{display: none;}
				.layout-footer{display: none;}
				
			}
		</style>
		<?php
			$param = '';
			if(!empty($_GET)){
				foreach($_GET AS $key=>$val){
					$param .= $key.'='.$val.'&';
				}
			}
		?>
		<div style="text-align:center;" class="print"><h3 style="margin-bottom: 0;">รายชื่อคนติดตามหนี้</h3></div>
		<h1 style="margin-bottom: 0" class="no_print">ติดตามหนี้</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0 no_print">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0 no_print">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_print" style="padding-right:0px;text-align:right;">			   
			<a class="btn btn-primary btn-lg bt-add" href="<?php echo base_url(PROJECTPATH.'/debt/letter'); ?>">
				<span class="icon icon-envelope-o"></span>
				<span>จดหมายแจ้งเตือน</span>
			</a>
			
			<button name="bt_add" id="bt_add" type="button" class="btn btn-primary btn-lg bt-add" onclick="window.print();">
				<span class="icon icon-print"></span>
				<span>พิมพ์</span>
			</button>
		</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">	
					<form data-toggle="validator" method="get" action="<?php echo base_url(PROJECTPATH.'/debt'); ?>" class="g24 form form-horizontal no_print" enctype="multipart/form-data" autocomplete="off" id="form_search">				
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-24">
								<label class="g24-col-sm-2 control-label ">ปี</label>
								<div class="g24-col-sm-3">
									<div class="form-group">
										<select id="report_year" name="year" class="form-control">
												<option value="">เลือกข้อมูล</option>
											<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
												<option value="<?php echo $i; ?>" <?php echo !empty($_GET["year"]) && $_GET["year"] == $i ? "selected": "" ?>><?php echo $i; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<label class="g24-col-sm-1 control-label ">เดือน</label>
								<div class="g24-col-sm-4">
									<div class="form-group">
										<select id="report_month" name="month" class="form-control">
											<option value="">เลือกข้อมูล</option>
											<?php foreach($month_arr as $key => $value){ ?>
												<option value="<?php echo $key; ?>" <?php echo !empty($_GET["month"]) && $_GET["month"] == $key ? "selected": "" ?>><?php echo $value; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							
								<label class="g24-col-sm-3 control-label" for="department">หน่วยงาน</label>
								<div class="g24-col-sm-9">
									<div class="form-group">
										<select class="form-control m-b-1" name="department" id="department" onchange="change_mem_group('department', 'faction')">
											<option value="">เลือกข้อมูล</option>
											<?php
											foreach($department as $key => $value){ ?>
												<option value="<?php echo $value['id']; ?>" <?php echo @$data['department']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>						
						</div>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-24">					
								<label class="g24-col-sm-2 control-label" for="faction">ฝ่าย</label>
								<div class="g24-col-sm-8">
									<div class="form-group" id="faction_space">
										<select class="form-control m-b-1" name="faction" id="faction" onchange="change_mem_group('faction','level')">
											<option value="">เลือกข้อมูล</option>
											<?php foreach($faction as $key => $value){ ?>
													<option value="<?php echo $value['id']; ?>" <?php echo @$data['faction']==$value['id']?'selected':'';?>><?php echo $value['mem_group_name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>

								<label class="g24-col-sm-3 control-label" for="level">สังดัก</label>
								<div class="g24-col-sm-9">
									<div class="form-group" id="level_space">
										<select class="form-control m-b-1" name="level" id="level">
											<option value="">เลือกข้อมูล</option>
											<?php foreach($level as $key => $value){ ?>
												<option value="<?php echo $value['id']; ?>" <?php echo @$data['level']==$value['id']?'selected':'';?>><?php echo $value['mem_group_name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							
								<div class="g24-col-sm-2">
								</div>
							</div>						
						</div>
						
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-24">					
								<label class="g24-col-sm-2 control-label" for="faction">ค้นหา</label>
								<div class="g24-col-sm-8">
									<div class="form-group">
										<input class="form-control" type="text" placeholder="ป้อนชื่อสกุล หรือ รหัสสมาชิก" name="search_member" id="search_member" value="<?php echo !empty($_GET['search_member']) ? $_GET['search_member'] : ""?>">                       
									</div>
								</div>

								<label class="g24-col-sm-3 control-label" for="level"></label>
								<div class="g24-col-sm-11">
									<div class="form-group">
										<button name="bt_view" id="bt_view" type="submit" class="btn btn-primary" style="width: 90px;" onclick="">
											<span>แสดง</span>
										</button>
									</div>
								</div>
							</div>						
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
										<th>สถานะ</th>
										<th class="no_print"></th>
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
									if(!empty($row)){
										foreach(@$row as $key => $row_debt){
											
											$this->db->select(array('SUM(pay_amount) AS pay_amount'));
											$this->db->from('coop_finance_month_detail');
											$this->db->join("coop_finance_month_profile","coop_finance_month_detail.profile_id=coop_finance_month_profile.profile_id","left");
											$this->db->where("coop_finance_month_detail.member_id = '{$row_debt['member_id']}' AND coop_finance_month_profile.profile_month = '{$row_debt['non_pay_month']}' AND coop_finance_month_profile.profile_year = '{$row_debt['non_pay_year']}'");
											
											$rs_receipt = $this->db->get()->result_array();
											$receipt_amount = number_format(@$rs_receipt[0]['pay_amount']-@$row_debt['non_pay_amount_balance'],2); //ยอดที่เก็บได้
											$non_pay_amount_balance = number_format(@$row_debt['non_pay_amount_balance'],2);//ค้างชำระ
											$pay_amount = number_format(@$rs_receipt[0]['pay_amount'],2);//ยอดเรียกเก็บ
											
											$non_pay_id = $row_debt['non_pay_id'];
											$non_pay_year_month = $row_debt['non_pay_year'].'/'.$month_arr[$row_debt['non_pay_month']];
											
											$this->db->select(array('*'));
											$this->db->from('coop_debt_letter');
											$this->db->where("coop_debt_letter.non_pay_id = '{$row_debt['non_pay_id']}'");
											$rs_letter = $this->db->get()->result_array();
											$row_letter = @$rs_letter;
											$arr_icon_ckeck = array();
											$letter_runno = 0;
											if(!empty($row_letter)){
												foreach($row_letter AS $key=>$value){			
													$letter_runno = @$value['runno'];
												}
											}
									?>
										<tr><td><?php echo @$i;?></td>
											<td><?php echo $non_pay_year_month;?></td>
											<td><?php echo $row_debt['member_id'];?></td>
											<td class="text-left"><?php echo $row_debt['firstname_th'].'  '.$row_debt['lastname_th'];?></td>
											<td class="text-right"><?php echo $pay_amount;?></td>
											<td class="text-right"><?php echo $non_pay_amount_balance;?></td>
											<td>
												<?php 
													$text_time = ($row_debt['non_pay_status'] == "3")?" ครั้งที่ ".$letter_runno:"";
													echo $arr_status[$row_debt['non_pay_status']].@$text_time;
												?>
											</td>
											<td class="no_print">
												<div class="dropdown">
													<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" style="width:90%">จัดการ 
													<span class="caret"></span></button>
													<ul class="dropdown-menu">
														<li><a title="รายละเอียด" style="cursor: pointer;padding-left:15px;padding-right:2px" onclick="view_detail('<?php echo $row_debt['non_pay_id'];?>','<?php echo $non_pay_year_month;?>','<?php echo $receipt_amount;?>','<?php echo $non_pay_amount_balance;?>','<?php echo $pay_amount;?>')"> รายละเอียด</a></li>
														<li><a title="ให้ออกจากสมาชิก" style="cursor:pointer;padding-left:15px;padding-right:2px" onclick="view_resignation('<?php echo $row_debt['non_pay_id'];?>','<?php echo $row_debt['member_id'];?>')"> ให้ออกจากสมาชิก</a></li>
													</ul>
												</div>
											</td>
										</tr>
									<?php 
											$i++; 
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

<div class="modal fade" id="viewDetail"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">รายละเอียด</h2>
            </div>
            <div class="modal-body" style="height: 600px;">
				<form data-toggle="validator" method="post" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_view">
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">
							<label class="g24-col-sm-5 control-label">ปี/เดือน </label>
							<div class="g24-col-sm-5">
								<div class="form-group">
									<input type="text" class="form-control" name="non_pay_year_month" id="non_pay_year_month" value=""  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-5 control-label">ยอดเรียกเก็บ </label>
							<div class="g24-col-sm-5">
								<div class="form-group">
									<input type="text" class="form-control" name="pay_amount" id="pay_amount" value=""  readonly="readonly">
								</div>
							</div>
						</div>					
					</div>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">							
							<label class="g24-col-sm-5 control-label">ยอดที่เก็บได้ </label>
							<div class="g24-col-sm-5">
								<div class="form-group">
									<input type="text" class="form-control" name="receipt_amount" id="receipt_amount" value=""  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-5 control-label">ค้างชำระ </label>
							<div class="g24-col-sm-5">
								<div class="form-group">
									<input type="text" class="form-control" name="non_pay_amount_balance" id="non_pay_amount_balance" value=""  readonly="readonly">
								</div>
							</div>
						</div>					
					</div>
					<div class="g24-col-sm-24 m-t-1">&nbsp;</div>
					<div class="bs-example" data-example-id="striped-table">
						<div id="tb_wrap">
							<h3>รายการหักชำระแล้ว</h3>
							<div class="col-sm-10 col-sm-offset-1  scrollbar">
								<table class="table table-bordered table-striped table-center">
									<thead> 
										<tr class="bg-primary">
											<th width="80">ลำดับ</th>
											<th>รายการหัก</th>
											<th width="200">ยอดเงิน</th>
										</tr>
									</thead>
									<tbody id="table_data">

									</tbody>
								</table>
							</div>
						</div>
					</div>
				</form>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewResignation"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">ให้ออกจากการเป็นสมาชิก</h2>
            </div>
            <div class="modal-body" style="height: 150px;">
				<form data-toggle="validator" method="post" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_view">
					<input type="hidden" id="non_pay_id" name="non_pay_id" value="">
					<input type="hidden" id="member_id" name="member_id" value="">
					<div class="g24-col-sm-24 text-center" style="padding-top: 60px;"  id="button_resignation">
						<div class="form-group">
							<button name="bt_save" id="bt_save" type="button" class="btn btn-primary btn-lg bt-add" onclick="save_debt_resignation()">
								<span class="icon icon icon-sign-out"></span>
								<span>ให้ออกจากการเป็นสมาชิก</span>
							</button>
						</div>					
					</div>	
					<div class="g24-col-sm-24 text-center  text-danger" style="padding-top: 40px;" id="text_resignation" >
											
					</div>			
				</form>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<script>
function view_detail(non_pay_id,non_pay_year_month,receipt_amount,non_pay_amount_balance,pay_amount){
	$("#non_pay_year_month").val(non_pay_year_month);
	$("#receipt_amount").val(receipt_amount);
	$("#non_pay_amount_balance").val(non_pay_amount_balance);
	$("#pay_amount").val(pay_amount);
	//$('#table_data').html('');
	$.ajax({
		type: "POST",
		url: base_url+'debt/get_receipt_detail',
		data: {
			non_pay_id : non_pay_id
		},
		success: function(data) {
			console.log(data);	
			$('#table_data').html(data);			
		}
	});
	
	$('#viewDetail').modal('show');
}

function view_resignation(non_pay_id,member_id){
	$("#non_pay_id").val(non_pay_id);
	$("#member_id").val(member_id);

	$.ajax({
		type: "POST",
		url: base_url+'debt/check_debt_resignation',
		data: {
			member_id : member_id
		},
		success: function(data) {			
			if(data != ''){	
				$("#button_resignation").hide();
				$("#text_resignation").html(data);				
			}else{
				$("#button_resignation").show();
				$("#text_resignation").hide();
			}
			$('#viewResignation').modal('show');
		}
	});
	
}

function change_mem_group(id, id_to){
    var mem_group_id = $('#'+id).val();
    $.ajax({
        method: 'POST',
        url: base_url+'manage_member_share/get_mem_group_list',
        data: {
            mem_group_id : mem_group_id
        },
        success: function(msg){
            $('#'+id_to).html(msg);
        }
    });
}
function save_debt_resignation(){
	var non_pay_id = $("#non_pay_id").val();
	var member_id = $("#member_id").val();
	
	$.ajax({
		type: "POST",
		url: base_url+'debt/save_debt_resignation',
		data: {
			non_pay_id : non_pay_id,
			member_id : member_id
		},
		success: function(data) {		
			//console.log(data);	
			if(data != ''){	
				$("#button_resignation").hide();
				$("#text_resignation").show();
				$("#text_resignation").html(data);
			}	
		}
	});
}
		
</script>
