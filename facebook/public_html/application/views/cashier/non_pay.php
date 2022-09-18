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
			th {
				text-align: center;
			}
			.modal-dialog-cal {
				width:80% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			
			.modal-dialog-search {
				width: 700px;
			}
		</style>
		<h1 style="margin-bottom: 0">รายการชำระเงินไม่ครบ</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">

			</div>

		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<h3></h3>
					<form action="" method="GET">
						<div class="g24-col-sm-24 m-t-1">
							<div class="form-group">
								<label class="g24-col-sm-1 control-label"> ปี </label>
								<div class="g24-col-sm-3">
									<select name="year" id="year" class="form-control m-b-1">
										<?php for($j=(date('Y')+542);$j<=(date('Y')+544);$j++){ 
										if(@$_GET['year']!=''){
											if(@$_GET['year'] == $j){
												$selected = 'selected';
											}else{
												$selected = '';
											}
										}else{
											if( (date('Y')+543) == $j){
												$selected = 'selected';
											}else{
												$selected = '';
											}
										}
										?>
											<option value="<?php echo $j; ?>" <?php echo $selected; ?>><?php echo $j; ?></option>
										<?php } ?>
									</select>
								</div>
								<label class="g24-col-sm-1 control-label"> เดือน </label>
								<div class="g24-col-sm-3">
									<select name="month" id="month" class="form-control m-b-1">
										<?php foreach($month_arr as $key => $value){ 
										if(@$_GET['month']!=''){
											if(@$_GET['month'] == $key){
												$selected = 'selected';
											}else{
												$selected = '';
											}
										}else{
											if(date('m') == $key){
												$selected = 'selected';
											}else{
												$selected = '';
											}
										}
										?>
											<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="g24-col-sm-2"><input type="submit" class="btn btn-primary" value="ค้นหา"> </div>
								<div class="g24-col-sm-1"></div>
								<div class="g24-col-sm-2"> <a href="<?php echo base_url(PROJECTPATH.'/cashier/non_pay');?>"><button type="button" class="btn btn-primary">แสดงทั้งหมด</button></a></div>
							</div>
						</div>
					</form>
					<form action="<?php echo base_url(PROJECTPATH.'/cashier/non_pay_save'); ?>" id="form1" method="POST">
						<input type="hidden" name="non_pay_year" id="non_pay_year" class="form-control" value="<?php echo (@$_GET['year'])?@$_GET['year']:(date('Y')+543);?>">
						<input type="hidden" name="non_pay_month" id="non_pay_month" class="form-control" value="<?php echo ($_GET['month'])?@$_GET['month']:(int)date('m');?>">
						<input type="hidden" name="non_pay_month_th" id="non_pay_month_th" class="form-control" value="<?php echo ($_GET['month'])?@$month_arr[$_GET['month']]:$month_arr[(int)date('m')];?>">
						<div class="form-group" id="btn_space">
							<div class=" g24-col-sm-12">
								<button type="button" onclick="add_row()" class="btn btn-primary min-width-100">
								<span class="icon icon-plus"></span>
								เพิ่มสมาชิก					
								</button>
							</div>	
							<div class=" g24-col-sm-12" style="text-align:right;">
								<button type="button" onclick="check_form()" class="btn btn-primary min-width-100">
								<span class="icon icon-save"></span>
								บันทึก					
								</button>
							</div>					
						</div>
						<div class="g24-col-sm-24 m-t-1">
							<div class="bs-example" data-example-id="striped-table">
								<table class="table table-bordered table-striped">
									<thead> 
										<tr class="bg-primary">
											<th style="width: 5%;">ลำดับ</th>
											<th style="width: 20%;">รหัสสมาชิก</th>
											<th>ชื่อ - สกุล</th>
											<th style="width: 10%;">จำนวนเงินทั้งหมด</th>
											<th style="width: 10%;">คงค้าง</th>
											<th style="width: 10%;">ปี</th>
											<th style="width: 10%;">เดือน</th>
											<th style="width: 10%;">จัดการ</th>
										</tr> 
									</thead>
									<tbody id="table_data">
										<tr id="value_null">
										</tr>
									</tbody>
									<tbody>	
									<?php 
									$total=0;
									foreach($data as $key => $value){ 										
									?>
										<tr> 
											<td align="center" class="old_num_row"  id="old_num_row_<?php echo $i;?>"><?php echo $i; ?></td>
											<td align="center"><?php echo $value['member_id']; ?></td>
											<td><?php echo $value['prename_short'].$value['firstname_th']." ".$value['lastname_th']; ?></td>											
											<td align="right"><?php echo number_format($value['pay_amount'],2); ?></td>
											<td align="right"><?php echo number_format($value['non_pay_amount'],2); ?></td>
											<td align="center"><?php echo @$value['non_pay_year']; ?></td>
											<td align="center"><?php echo @$month_arr[$value['non_pay_month']]; ?></td>
											<td align="center"><a title="ลบ" class="icon icon-trash-o" style="cursor:pointer;" onclick="delete_non_pay('<?php echo $value['non_pay_id']; ?>')"></a></td>
										</tr>
									<?php 
										$i++;
									} 
									?>
									</tbody> 
								</table> 
							</div>
						</div>
					</form>	
				</div>
				 <div id="page_wrap">
					<?php echo $paging ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="search_member_modal" role="dialog"> 
    <div class="modal-dialog modal-dialog-search">
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
								<select id="member_search_list" name="member_search_list" class="form-control m-b-1">
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
								<input id="member_search_text" name="member_search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>">
								<span class="input-group-btn">
									<button type="button" id="member_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
								</span>	
								</div>
							</div>
						</div>
						<input id="data_row" name="data_row" class="form-control m-b-1" type="hidden" value="">
					</div>
				</div>
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

<script>
function open_modal(id){
	$('#'+id).modal('show');
}
$( document ).ready(function() {
	$('#search_member_modal').on('shown.bs.modal', function() {
		$('#search_member').focus();
	});
	$('#search_member').keyup(function(){
	   var txt = $(this).val();
	   if(txt != ''){
			$.ajax({
				 url:base_url+"/ajax/search_member_jquery",
				 method:"post",
				 data:{search:txt},
				 dataType:"text",
				 success:function(data)
				 {
				 //console.log(data);
				  $('#result_member_search').html(data);
				 }
			});
	   }else{
		   
	   }
	});
});
function delete_non_pay(non_pay_id){
	swal({
        title: 'ท่านต้องการลบข้อมูลใช่หรือไม่',
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
			document.location.href = base_url+'cashier/non_pay_delete/'+non_pay_id;
        } else {
			
        }
    });
}

function check_member_id() {
   var member_id = $('#member_id').val();
   var keycode = (event.keyCode ? event.keyCode : event.which);
   if(keycode == '13'){
     $.post(base_url+"ajax/get_member", 
     {	
       member_id: member_id
     }
     , function(result){
        obj = JSON.parse(result);
		console.log(obj)
        if(obj.member_id  && obj.member_name){
			get_data(obj.member_id, obj.member_name, obj.member_group)
        }else{					
          swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning'); 
        }
      });		
    }
}

$('#member_search').click(function(){
	if($('#member_search_list').val() == '') {
		swal('กรุณาเลือกรูปแบบค้นหา','','warning');
	} else if ($('#member_search_text').val() == ''){
		swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
	} else {
		$.ajax({
			url: base_url+"ajax/search_member_by_type_jquery",
			method:"post",  
			data: {
				search_text : $('#member_search_text').val(), 
				search_list : $('#member_search_list').val(),
				data_row : $('#data_row').val()
			},  
			dataType:"text",  
			success:function(data) {
				$('#result_member_search').html(data);  
			}  ,
			error: function(xhr){
				console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
			}
		});  
	}
});

$("#form1-submit").click(function(){
	$("#form1").submit();
});

var i=0;
function add_row(){
	$('#value_null').hide();
	var non_pay_year = $("#non_pay_year").val();
	var non_pay_month = $("#non_pay_month").val();
	var non_pay_month_th = $("#non_pay_month_th").val();
	var new_row = "";
	new_row += "<tr class='new_row' id='new_row_"+i+"'>\n";
	new_row += "<td class='num_row' align='center'></td>";
	new_row += "<td>";
		new_row += "<div class='input-group'>";
		new_row += "<input type='text' id='member_id_"+i+"' class='form-control center member_id' name='data[list_data]["+i+"][member_id]' onchange=\"keypress_search_member('"+i+"')\">";
		new_row += "<span class='input-group-btn'>";
		new_row += "<a data-toggle='modal' class='fancybox_share fancybox.iframe' href='#' onclick=\"open_modal('search_member_modal','"+i+"')\">";
		new_row += "<button id='' type='button' class='btn btn-info btn-search'><span class='icon icon-search'></span></button>";
		new_row += "</a>";
		new_row += "</span>";		
		new_row += "</div>";		
	new_row += "</td>";
	new_row += "<td><input type='text' id='member_name_"+i+"' name='data[list_data]["+i+"][member_name]' class='form-control member_name' readonly></td>";
	//new_row += "<td align='right'><span id='non_pay_amount_balance_"+i+"'></span></td>";
	new_row += "<td align='right'><span id='total_pay_amount_"+i+"'></span></td>";
	new_row += "<td><input type='text' class='form-control non_pay_amount right' id='non_pay_amount_"+i+"' name='data[list_data]["+i+"][non_pay_amount]' onkeyup=\"format_the_number(this);keypress_non_pay_amount('"+i+"')\" maxlength='10'></td>";
	new_row += "<td align='center'>"+non_pay_year+"</td>";
	new_row += "<td align='center'>"+non_pay_month_th+"</td>";
	new_row += "<td align='center'><a style='cursor:pointer;' class='icon icon-trash-o' titla='ลบ' onclick=\"del_list('"+i+"')\"></a></td>";
	new_row += "</tr>\n";
	$('#table_data').append(new_row);
	add_num_row();
	i++;
}
function add_num_row(){
	var num_row=1;
	$('.num_row').each(function(){
		$(this).html(num_row);
		num_row++;
	});
	
	$('.old_num_row').each(function(){
		var id = this.id;
		var arr_id = id.split("old_num_row_");
		var val_id = arr_id[1];
		var old_row = parseInt(val_id)+parseInt(num_row-1);
		$('#'+id).html(old_row);
	});	
}

function del_list(id){
	$('#new_row_'+id).remove();
	add_num_row();
}

function keypress_search_member(id){
	var member_id = $('#member_id_'+id).val();
	var deduct_id = $('#deduct_id').val();
	var non_pay_year = $("#year").val();
	var non_pay_month = $("#month").val();
	$.post(base_url+"/cashier/keypress_search_member", 
	{	
		member_id: member_id,
		deduct_id: deduct_id,
		id:id,
		non_pay_year:non_pay_year,
		non_pay_month:non_pay_month
	}
	, function(result){
		if(result!='error'){
			var obj = JSON.parse(result);
			console.log(obj);
			var member_name = obj.row_member['prename_short']+obj.row_member['firstname_th']+" "+obj.row_member['lastname_th']
			$('#member_id_'+id).val(obj.row_member['member_id']);
			$('#member_name_'+id).val(member_name);
			$('#ref_space_'+id).html(obj.ref_data);
			$('#total_pay_amount_'+id).html(obj.total_pay_amount);
			check_member_id(obj.row_member['member_id'],id);
		}else{
			$('#member_name_'+id).val('');
			$('#ref_space_'+id).html('');
		}
	});
}

function check_form(){
	var text_alert = '';
	
	var new_row = 0;
	$('.new_row').each(function(){
			new_row++;
	});
	if(new_row == 0){
		text_alert += '- กรุณาเพิ่มรายการ\n';
	}
	
	var empty_pay_amount = 0;
	$('.non_pay_amount').each(function(){
		if($(this).val() == ''){
			empty_pay_amount++;
		}
	});
	if(empty_pay_amount>0){
		text_alert += '- จำนวนเงิน\n';
	}
	
	if(text_alert!=''){
		swal('กรุณากรอกข้อมูลต่อไปนี้ให้ครบถ้วน',text_alert,'warning');
	}else{
		$('#form1').submit();
	}
}

function format_the_number(ele){
	var value = $('#'+ele.id).val();
	value = value.replace(/[^0-9]/g, '');	
	if(value!=''){
		if(value == 'NaN'){
			$('#'+ele.id).val('');
		}else{		
			value = parseInt(value);
			value = value.toLocaleString();
			$('#'+ele.id).val(value);
		}			
	}else{
		$('#'+ele.id).val('');
	}
}

function keypress_non_pay_amount(id){
	var non_pay_amount = $('#non_pay_amount_'+id).val();
	$('#non_pay_amount_balance_'+id).html(non_pay_amount);
}

function get_data(member_id,firstname_th,data_row){
	$('#member_id_'+data_row).val(member_id);
	$('#member_name_'+data_row).val(firstname_th);	
	
	$('#search_member_modal').modal('hide');
	$('#member_search_text').val('');
	$('#member_search_list').val('');
	$('#result_member_search').html('');
	
	check_member_id(member_id,data_row);
}

function check_member_id(member_id,data_row){
	var i=0;
	$('.member_id').each(function(){
		
		if(member_id == $(this).val()){	
			i++;
			if(i>1){
				$('#member_id_'+data_row).val('');
				$('#member_name_'+data_row).val('');
				swal('กรุณาเลือกรหัสสมาชิกใหม่ \nเนื่องจากมีรหัสสมาชิกนี้แล้ว','','warning');
			}			
		}
	});
}

function open_modal(id,data_row){
	$('#'+id).modal('show');
	$('#data_row').val(data_row);
}
 
</script>