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
				height: 200px;
			}
			
			.text-success{color:#5cb85c;}
			
			.pointer{cursor: pointer;}
		</style>
		<?php
			$param = '';
			if(!empty($_GET)){
				foreach($_GET AS $key=>$val){
					$param .= $key.'='.$val.'&';
				}
			}
		?>
		<h1 style="margin-bottom: 0">จดหมายแจ้งเตือน</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">			   
			<button name="bt_view" id="bt_view" type="button" class="btn btn-primary btn-lg bt-add" onclick="print_preview();">
				<span class="icon icon-file-text-o"></span>
				<span>ดูตัวอย่างก่อนพิมพ์</span>
			</button>
			
			<button name="bt_add" id="bt_add" type="button" class="btn btn-primary btn-lg bt-add" onclick="modal_print();">
				<span class="icon icon-print"></span>
				<span>พิมพ์จดหมาย</span>
			</button>		   
		</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">	
					
					<form data-toggle="validator" method="get" action="<?php echo base_url(PROJECTPATH.'/debt/letter'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="form_search">											
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
								<label class="g24-col-sm-2 control-label" for="faction">อำเภอ</label>
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

								<label class="g24-col-sm-3 control-label" for="level">หน่วยงานย่อย</label>
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
							<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/debt/save_debt_letter?'.$param); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="form_print_item">
								<input type="hidden" id="action" name="action" value="save">
								<table class="table table-bordered table-striped table-center">
									<thead> 
										<tr class="bg-primary">
											<th><input type="checkbox" id="check_all" onclick="checked_all()"> <!--<a onclick="del_member()" style="cursor:pointer" title="เลือก" class="icon icon-trash-o"></a>--></th>
											<th>ลำดับ</th>
											<th>ปี</th>
											<th>เดือน</th>
											<th>รหัสสมาชิก</th>
											<th>ชื่อสกุล</th>
											<th>ยอดค้างชำระ</th>
											<?php for($i=1;$i<=$num_letter;$i++){ ?>
											<th style="width: 105px;">ครั้งที่ <?php echo $i;?></th>
											<?php } ?>
										</tr>
									</thead>
									<tbody>
									<?php
										
										$runno=1;
										if(!empty($row)){
											foreach(@$row as $key => $row_debt){
												$icon_ckeck = '';
												$runno_max = 0;
												$this->db->select(array('MAX(runno) AS runno_max'));
												$this->db->from('coop_debt_letter');
												$this->db->where("coop_debt_letter.non_pay_id = '{$row_debt['non_pay_id']}' AND letter_status = '1'");
												$this->db->limit(1);
												$rs_max = $this->db->get()->result_array();
												$runno_max= @$rs_max[0]['runno_max']+1;
												
												$this->db->select(array('*'));
												$this->db->from('coop_debt_letter');
												$this->db->where("coop_debt_letter.non_pay_id = '{$row_debt['non_pay_id']}' AND letter_status = '1'");
												$rs_letter = $this->db->get()->result_array();
												//echo $this->db->last_query();
												$row_letter = @$rs_letter;
												$arr_icon_ckeck = array();
												$letter_runno = 0;
												if(!empty($row_letter)){
													foreach($row_letter AS $key=>$value){			
														$icon_ckeck = '<span class="icon icon-check text-success pointer" onclick="letter_print('.$value['letter_id'].');"  title="แสดงจดหมาย"></span>'; //เขียว														
														$icon_ckeck .= $this->center_function->ConvertToThaiDate($value['print_date'],1,0);	
														$arr_icon_ckeck[$value['runno']] = @$icon_ckeck;
														$letter_runno = @$value['runno'];
													}
												}

												$total_non_pay = $this->db->select("sum(coop_non_pay_detail.non_pay_amount_balance) as sum")
																			->from("coop_non_pay")
																			->join("(SELECT * FROM coop_non_pay_detail WHERE deduct_code != 'DEPOSIT' AND deduct_code != 'CREMATION') as coop_non_pay_detail", "coop_non_pay.non_pay_id = coop_non_pay_detail.non_pay_id", "inner")
																			->where("coop_non_pay.non_pay_status != '0' AND coop_non_pay.non_pay_status != '2' AND cast(coop_non_pay.non_pay_year as int) <= ".$row_debt['non_pay_year']."
																						 AND cast(coop_non_pay.non_pay_month as int) <= ".$row_debt['non_pay_month']." AND coop_non_pay.member_id = '".$row_debt["member_id"]."'")
																			->get()->row();

												$arr_icon_ckeck[$runno_max] = '<span class="icon icon-check text-danger pointer" onclick="modal_print('.$row_debt['non_pay_id'].');" title="พิมพ์จดหมาย"></span>'; //แดง
																						
										?>
											<tr> 											 
												<td>
													<input type="checkbox" class="check_item" name="print_non_pay[]" id="print_non_pay[]" value="<?php echo @$row_debt['non_pay_id']; ?>">
												</td>
												<td><?php echo @$runno;?></td>
												<td><?php echo $row_debt['non_pay_year'];?></td>
												<td><?php echo $month_arr[$row_debt['non_pay_month']];?></td>
												<td><?php echo $row_debt['member_id'];?></td>
												<td class="text-left"><?php echo $row_debt['firstname_th'].'  '.$row_debt['lastname_th'];?></td>
												<td class="text-right"><?php echo number_format($total_non_pay->sum,2);?></td>
												<?php for($i=1;$i<=$num_letter;$i++){ ?>
													<td><?php echo @$arr_icon_ckeck[$i];?></td>	
												<?php } ?>										
											</tr>
										<?php 
												$runno++; 
											}
										}else{ ?>
											<tr><td colspan="10">ไม่พบข้อมูล</td></tr>
										<?php } ?>
									</tbody> 
								</table>
							</form>
						</div>
					</div>
					<?php echo @$paging ?>
				</div>
			</div>
		</div>
    </div>
</div>

<div class="modal fade" id="modalPrint"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:50%">
        <div class="modal-content">
            <div class="modal-body" style="height: 200px;">
				<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/debt/save_debt_letter?'.$param); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="form_print">
					<input type="hidden" id="do" name="do" value="print_all">
					<input type="hidden" id="non_pay_id" name="non_pay_id" value="">
					<input type="hidden" id="action_single_mem" name="action" value="save">
					<div class="g24-col-sm-24 text-center" style="padding-top: 30px;">
						<div class="form-group">
							<h3>เมื่อทำการพิมพ์ระบบจะอัปเดตสถานะเป็นดำเนินการแล้ว</h3>
						</div>					
					</div>
					<div class="g24-col-sm-24 text-center">
						<div class="form-group">						
							<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
							<button type="button" class="btn btn-info" onclick="submit_form()">ตกลง</button>							
						</div>					
					</div>					
				</form>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<script>
var base_url = $('#base_url').attr('class');
function modal_print(non_pay_id = ''){	
	$("#action").val("save");

	if(non_pay_id == ''){
		if(!$('.check_item').is(':checked')){
			swal("กรุณาเลือกรายการที่ต้องการพิมพ์จดหมาย");
			return false;
		}else{
			can_print = true;
			$('.check_item').each(function() {
				if(this.checked) {
					$.ajax({
						method: 'POST',
						url: base_url+'debt/check_month_letter',
						data: {
							non_pay_id : $(this).val()
						},
						async:false,
						success: function(msg){
							if(msg == "no"){
								swal("ไม่สามารถพิมพ์จดหมายได้ ");
								can_print = false;
							}
						}
					});
				}
			});
			if(can_print) {
				$("#non_pay_id").val(non_pay_id);
				$('#modalPrint').modal('show');
			}
		}
	}else{
		 $.ajax({
			method: 'POST',
			url: base_url+'debt/check_month_letter',
			data: {
				non_pay_id : non_pay_id
			},
			success: function(msg){
				console.log(msg);
				if(msg == "no"){
					//swal("ไม่สามารถพิมพ์จดหมายได้ \nเนื่องจากเดือนนี้ได้พิมพ์จดหมายแล้ว");
					swal("ไม่สามารถพิมพ์จดหมายได้ ");
					return false;
				}else{
					$("#non_pay_id").val(non_pay_id);
					$('#modalPrint').modal('show');
				}
			}
		});
	}
}

function print_preview(){
	$("#action").val("view");
	if(!$('.check_item').is(':checked')){
		swal("กรุณาเลือกรายการที่ต้องการดูตัวอย่างก่อนพิมพ์");
		return false;
	}else{
		$('#form_print_item').submit();
	}
}	

function letter_print(letter_id){
	window.open(base_url+'debt/letter_perview?id='+letter_id,'_blank');
}	

function submit_form(){
	var non_pay_id = $("#non_pay_id").val();
	console.log(non_pay_id);
	if(non_pay_id != ''){
		$('#form_print').submit();
	}else{
		$('#form_print_item').submit();
	}
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

function checked_all(){
	if($('#check_all').is(':checked')){
		$('.check_item').prop('checked','checked');
	}else{
		$('.check_item').prop('checked','');
	}
}
</script>