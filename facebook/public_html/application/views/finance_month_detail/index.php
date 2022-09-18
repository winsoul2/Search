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
			td {
				font-size: 12px;
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
			.scroll_x{
				overflow-x: scroll;
			}
		</style>
		<h1 style="margin-bottom: 0">แก้ไขข้อมูลเรียกเก็บ</h1>
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
                                <label class="g24-col-sm-2 control-label"> รหัสสมาชิก </label>
                                <div class="g24-col-sm-3">
                                <div class="form-group">
									<div class="input-group">											
										<input id="member_id" name="member_id" class="form-control" style="text-align:left;" type="text" value="<?php echo @$_GET['member_id'];?>" title="กรุณาป้อน เลขสมาชิก" onkeypress="check_member_id();" />
										<span class="input-group-btn">
											<a data-toggle="modal" data-target="#myModal" id="test" class="fancybox_share fancybox.iframe" href="#">
												<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
											</a>
										</span>	
									</div>
								</div>
								</div>
								<label class="g24-col-sm-1 control-label"> ปี </label>
								<div class="g24-col-sm-3">
									<select name="year" id="year" class="form-control m-b-1">
										<?php 	
										foreach($coop_finance_month_profile_arr as $key => $value){ 
											if(@$_GET['year']!=''){
												if(@$_GET['year'] == $value['profile_year']){
													$selected = 'selected';
												}else{
													$selected = '';
												}
											}else{
												if( (date('Y')+543) == $value['profile_year']){
													$selected = 'selected';
												}else{
													$selected = '';
												}
											}
										?>
											<option value="<?php echo $value['profile_year']; ?>" <?php echo $selected; ?>><?php echo $value['profile_year'];?></option>
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
							</div>
						</div>
					</form>
					<form>
						<input type="hidden" name="non_pay_year" id="non_pay_year" class="form-control" value="<?php echo (@$_GET['year'])?@$_GET['year']:(date('Y')+543);?>">
						<input type="hidden" name="non_pay_month" id="non_pay_month" class="form-control" value="<?php echo ($_GET['month'])?@$_GET['month']:(int)date('m');?>">
						<input type="hidden" name="non_pay_month_th" id="non_pay_month_th" class="form-control" value="<?php echo ($_GET['month'])?@$month_arr[$_GET['month']]:$month_arr[(int)date('m')];?>">
						<div class="form-group" id="btn_space">	
							<div class=" g24-col-sm-24" style="text-align:right;">
							<?php if($_GET['member_id']!=''&&$_GET['year']!=''&&$_GET['month']!=''){ ?>
								<button id="botton_insert" type="button" data-toggle="modal" data-target="#insert_model" class="btn btn-primary min-width-100">
								<span class="icon icon-cloud-upload"></span> เพิ่ม </button>
								<button type="button" onclick="update_finance_month_detail()" class="btn btn-primary min-width-100">
								<span class="icon icon-save"></span> บันทึก	</button>
							<?php }else{ ?>
<!--								<button type="button" onclick="popup_error()" class="btn btn-secondary min-width-100">-->
<!--								<span class="icon icon-cloud-upload"></span> เพิ่ม	</button>-->
<!--								<button type="button" onclick="popup_error()" class="btn btn-secondary min-width-100">-->
<!--								<span class="icon icon-save"></span> บันทึก	</button>-->
							<?php } ?>
							</div>					
						</div>
						<div class="g24-col-sm-24 m-t-1 scroll_x">
                            <?php
                            $edit_finance_month_detail_arr = $data;
                            ?>
								<table class="table table-bordered table-striped">
									<thead> 
										<tr class="bg-primary">
											<th style="width: 10%;">ลำดับ</th>
<!--                                            <th style="width: 6.25%;">ชื่อนามสกุล</th>-->
											<th style="width: 10%;">ทะเบียนสมาชิก</th>
                                            <th style="width: 30%;">ประเภท</th>
                                            <th style="width: 20%;">จำนวนเงิน</th>
                                            <th style="width: 20%;">จำนวนจ่ายจริง</th>
											<th id="delete" style=";width: 10%;">ลบ</th>
										</tr> 

									</thead>
									<tbody id="table_Finance">
									<?php
                                    $sum_pay_amount = 0;
                                    $sum_real_pay_amount = 0;
									if(isset($data) && sizeof($data) > 0){
									foreach($data as $key => $value){
                                        $profile_id = $value['profile_id'];
                                        $total++;
                                        $sum_pay_amount += $value['pay_amount'];
                                        $sum_real_pay_amount += $value['real_pay_amount'];
                                        $disabled = $value['run_status'] == 1 ? ' disabled="disabled" ' : "";
                                        $js_link = $value['run_status'] == 1 ? "#" : "javascript:show_confirm_user('delete_data(".$value['run_id'].")')";
										$ctrl_btn = $value['run_status'] == 1 ? '' : '<a href="{$js_link}">ลบ</a>';
										$text_deduct_detail = '';
										if($value['deduct_code'] == 'ATM'){
                                            if($value['pay_type'] == 'principal' ){
                                                $text_deduct_detail = 'ต้นเงินสัญญากู้ฉุกเฉิน ATM'." ".$value['contract_number'];
                                            }else{
                                                $text_deduct_detail = 'ดอกเบี้ยสัญญากู้ฉุกเฉิน ATM'." ".$value['contract_number'];
                                            }
                                        }else{
                                            $text_deduct_detail = $value['deduct_detail']." ". $value['contract_number'];
                                        }
                                        ?>
										 <tr>
											<td class="center"><?php echo $total?></td>
<!--                                            <td>--><?php //echo $value['fullname_th']?><!--</td>-->
                                            <td class="center"><?php echo $value['member_id']?></td>
                                            <td><?php echo $text_deduct_detail;?></td>
                                            <td><input id="pay_amount<?php echo $key?>" <?php echo $disabled; ?> name="pay_amount<?php echo $key?>" <?php echo !empty($_GET['member_id'])? '': 'disabled'; ?> class="form-control" style="text-align:left;" type="text"  onkeyup="inputeditFunction('<?php echo $key?>','<?php echo $value['run_id']?>','pay_amount','<?php echo $value['member_id']?>','<?php echo $value['fullname_th']?>','<?php echo $value['deduct_detail']?>','<?php echo $value['deduct_id']?>','<?php echo $value['profile_id']?>')" value="<?php echo number_format($value['pay_amount'], 2, '.', ',');?>"/></td>
                                            <td><input id="real_pay_amount<?php echo $key?>" <?php echo 'disabled'; ?> name="real_pay_amount<?php echo $key?>" class="form-control" type="text" onkeyup="inputeditFunction('<?php echo $key?>','<?php echo $value['run_id']?>','real_pay_amount','<?php echo $value['member_id']?>','<?php echo $value['fullname_th']?>','<?php echo $value['deduct_detail']?>','<?php echo $value['deduct_id']?>','<?php echo $value['profile_id']?>')" style="text-align:left;" value="<?php echo number_format($value['real_pay_amount'], 2, '.', ','); ?>"/></td>
<!--											<td class="center" id="delete">--><?php //echo $ctrl_btn; ?><!--</td>-->

                                            <?php if(!empty($_GET['member_id'])) { ?>
                                                <td class="center" id="delete"><a href="javascript:show_confirm_user('delete_data(<?php echo $value['run_id'];?>)')">ลบ</a></td>
                                            <?php }else{ ?>
                                                <td class="center"></td>
                                            <?php } ?>
										</tr> 
										<button id = "delete_data(<?php echo $value['run_id'];?>)" type="button" style="display: none" onClick="delete_row(<?php echo $value['run_id']; ?>)">ลบ</button>
									<?php }}?>
									<?php if($total == 0){ ?>
										<tr>
											<td colspan="17" class="center" > ไม่มีข้อมูล </td>
										</tr>
									<?php } ?>
									</tbody>
                                    <tfoot>
                                        <td colspan=3 class="center" style="color: #FFF">รวม</td>
                                        <td><input id="sum_pay_amount" name="sum_pay_amount" class="form-control" type="text"  style="text-align:left;" value="<?php echo number_format($sum_pay_amount, 2, '.', ',');?>" disabled/></td>
                                        <td><input id="sum_real_pay_amount" name="sum_real_pay_amount" class="form-control" type="text"  style="text-align:left;" value="<?php echo number_format($sum_real_pay_amount, 2, '.', ',');;?>" disabled/></td>
                                        <td class="center"></td>
                                    </tfoot>
								</table> 
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

<?php 
// load view
$this->load->view('finance_month_detail/search_member_add_modal');
$this->load->view('finance_month_detail/insert_month_detail_model'); 
?>

<!-- MODAL EDIT MONTH DETAIL-->
<div class="modal fade" id="submitmyModal" role="dialog">
    <div class="modal-dialog modal-lg" style='width: 90%;'>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">บันทึกข้อมูล</h4>
            </div>
            <div class="modal-body">
                <div class="g24-col-sm-24 scroll_x">
                <!-- *สีแดงคือข้อมูลที่แก้ไขหรือเพิ่มใหม่ -->
                    
                    <div class="bs-example" data-example-id="striped-table">
                    <table class="table table-striped">

                        <tbody id="table_data2">

                        </tbody>

                    </table>
                </div>
                </div>
                
            </div>
            <div class="modal-footer">
				<button type="button" id="show_confirm_user" class="btn btn-default" onClick='show_confirm_user("sudmit_check_form")'>บันทึก</button>
                <button type="button" id="sudmit_check_form" class="btn btn-default" style="display: none" onClick='check_form(<?php echo  $finance_month_detail_arr?>)'>บันทึก</button>
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL EDIT MONTH DETAIL-->

<!-- MODAL CONFIRM USER-->
<div class="modal fade" id="modal_confirm_user" role="dialog">
    <div class="modal-dialog modal-sm" style='width: 300px;'>
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">ยืนยันสิทธิ์การใช้งาน</h4>
        </div>
        <div class="modal-body">
          	<p>ชื่อผู้มีสิทธิ์อนุมัติ</p>
		  	<input type="text" class="form-control" id="confirm_user">
		  	<p>รหัสผ่าน</p>
		  	<input type="password" class="form-control" id="confirm_pwd">
			  <br>
			<!--<input type="hidden" id="transaction_id_err">-->
			<div class="row">
				<div class="col-sm-12 text-center">
					<button class="btn btn-info bt_check_submit" id="submit_confirm_user">บันทึก</button>
				</div>
			</div>
        </div>
        <div class="modal-footer">
        </div>
      </div>
    </div>
</div>
<!-- MODAL CONFIRM USER-->

<?php
	echo "<script>";
	echo 'var finance_month_detail_json = '.json_encode($finance_month_detail_arr).';';
    echo 'var edit_finance_month_detail_json = '.json_encode($edit_finance_month_detail_arr).';';
    echo 'var year = '.$_GET['year'].';';
    echo 'var month = '.$_GET['month'].';';
    echo 'var get_member_id = "'.$_GET['member_id'].'";';
    echo "</script>";
?>
<script>
// console.log('-----------------------',get_member_id);
console.log(finance_month_detail_json);
console.log(edit_finance_month_detail_json);
var save_edit = [];
var botton_id = '';
var user_id = '';
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


function check_form(finance_month_detail_arr){
	var text_alert = '';
	console.log('this.finance_month_detail_json', this.finance_month_detail_json);
	console.log('this.edit_finance_month_detail_json', this.edit_finance_month_detail_json);
	// this.finance_month_detail_json.__proto__ = null;
	// this.edit_finance_month_detail_json.__proto__ = null;
	var status = false;
	for(var i in edit_finance_month_detail_json) {
		for(var j in edit_finance_month_detail_json[i]) {
			// console.log(finance_month_detail_json[i][j], this.edit_finance_month_detail_json[i][j]);
			// if	(finance_month_detail_json[i][j] != this.edit_finance_month_detail_json[i][j]){
			// 	status = false;
			// }
		}
	}
	// return false;
	var user_id = get_user_id();
	
	if(status){
		swal('ไม่มีการแก้ไขข้อมูล',text_alert,'warning');
	}else{
		$.ajax({
            type: "POST",
            url: base_url+'finance_month_detail/save_finance_month_detail',
            data: {
                save_edit : this.save_edit,
				user_id : user_id,
				form_target : 'update'
            },
            success: function(msg) {
                console.log (msg);
				swal('บันทึกข้อมูลเรียบร้อย',text_alert,'success');
				location.reload();
            }
        });
	}
}



function open_modal(id,data_row){
	$('#'+id).modal('show');
	$('#data_row').val(data_row);
}
 
$(document).ready(function(){
	$("#bt_attach_file").click(function(){
		$("#import-modal").modal('toggle');
	});
});

function get_search_member(){
        $.ajax({
            type: "POST",
            url: base_url+'finance_month_detail/get_search_member',
            data: {
                search_text : $("#search_text").val(),
				form_target : 'add'
            },
            success: function(msg) {
                $("#table_data").html(msg);
            }
        });
    }

function inputeditFunction(key,run_id,value,member_id,full_name,deduct_detail,deduct_id=null,profile_id=null){
    // console.log('member_id', member_id);
	// console.log(edit_finance_month_detail_json,key);
    var pay_amount = document.getElementById("pay_amount"+key).value;
    var newStr = pay_amount.replace(/,/g, '');
    // pay_amount = numeral(newStr).format('00.00');
    pay_amount.replace(",", "");
	// var real_pay_amount = document.getElementById("real_pay_amount"+key).value;
    var real_pay_amount = pay_amount;
	var edit_data = {run_id: run_id, 
					member_id: member_id,
					full_name: full_name,
					deduct_id: deduct_id,
					deduct_detail: deduct_detail, 
					pay_amount: pay_amount, 
					real_pay_amount: real_pay_amount,
					profile_id: profile_id};


    // $(this).val(numeral(pay_amount).format('0,0.00'));


    // console.log ('pay_amount', numeral(pay_amount).format('0,0.00'));


	this.save_edit[key] = edit_data;
    document.getElementById("real_pay_amount"+key).value = numeral(pay_amount).format('0,0.00');
    document.getElementById("pay_amount"+key).value = pay_amount;
    document.getElementById("sum_pay_amount").value =  numeral(update_sum_pay_amount()).format('0,0.00');
    document.getElementById("sum_real_pay_amount").value =  numeral(update_sum_real_pay_amount()).format('0,0.00');
	var num = 0;
	for(var key in this.save_edit) {
		num++;
		// console.log('this.save_edit', key, this.save_edit[key]);
		// console.log('num', num);
	}
	// this.save_edit.length = num;
	// console.log('save_edit', this.save_edit);
	
}

function update_sum_pay_amount() {
    var sum_pay_amount = 0;
    this.edit_finance_month_detail_json.forEach(function(item, index, rows){
        var new_save_edit = this.save_edit;
        var data = new_save_edit.filter(function(edit_item){
            return edit_item.run_id === item.run_id;
        });

        if (data.length > 0){
            sum_pay_amount = sum_pay_amount + Number(data[0].pay_amount);
        }else{
            sum_pay_amount = sum_pay_amount + Number(item.pay_amount);
        }
    });
    // console.log(sum_pay_amount);
    return sum_pay_amount;
}

function update_sum_real_pay_amount() {
    var sum_real_pay_amount = 0;
    this.edit_finance_month_detail_json.forEach(function(item, index, rows){
        var new_save_edit = this.save_edit;
        var data = new_save_edit.filter(function(edit_item){
            return edit_item.run_id === item.run_id;
        });

        if (data.length > 0){
            sum_real_pay_amount = sum_real_pay_amount + Number(data[0].real_pay_amount);
        }else{
            sum_real_pay_amount = sum_real_pay_amount + Number(item.real_pay_amount);
        }
    });
    // console.log(sum_pay_amount);
    return sum_real_pay_amount;
}

function update_finance_month_detail(){
	var test = [];
	console.log('save_edit', this.save_edit);
	if (this.save_edit.length != 0){
		$('#submitmyModal').modal('show');
		$.ajax({
			type: "POST",
			url: base_url+'finance_month_detail/update_finance_month_detail',
			data: {
				save_edit : this.save_edit,
				year : year,
				month : month,
				member_id : get_member_id,
				form_target : 'checkupdate'
			},
			success: function(msg) {
				$("#table_data2").html(msg);
			}
		});
	}else{
		swal('ไม่มีการแก้ไขข้อมูล','','warning');
	}
}

function delete_row(run_id){
	var user_id = get_user_id();
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
			$.ajax({
				type: "POST",
				url: base_url+'finance_month_detail/update_finance_month_detail',
				data: {
					run_id : run_id,
					user_id : user_id,
					form_target : 'delete'
				},
				success: function(msg) {
					console.log(msg);
					swal('ลบข้อมูลเรียบร้อย','','success');
					location.reload();
				}
			});
        } else {
			
        }
    });
}

function popup_error(){
	swal('กรุณาค้นหาสมาชิก','','warning');
}

function show_confirm_user(botton_id){
	this.botton_id = botton_id;
	$('#modal_confirm_user').modal('show');
}

function get_botton_id (){
	return this.botton_id;
}

function set_user_id (user_id){
	this.user_id = user_id;
}

function get_user_id (){
	return this.user_id;
}

//CONFIRM USER
$("#submit_confirm_user").on('click', function (){
		var confirm_user = $('#confirm_user').val();
		var confirm_pwd = $('#confirm_pwd').val();	
		var permission_id = '<?php echo $_SESSION['permission_id'];?>';	
		var botton_id = get_botton_id();
		console.log('botton_id', botton_id)
		$.ajax({
				method: 'POST',
				url: base_url+'auth/authen_confirm_user',
				data: {
					confirm_user : confirm_user,
					confirm_pwd : confirm_pwd,
					permission_id : permission_id
				},
				dataType: 'json',
				success: function(data){
					if(data.result=="true"){

						if(data.permission=="true"){
							var tagButton = document.getElementById ( botton_id );
							set_user_id(data.user_id);
							tagButton.click();
							$('#modal_confirm_user').modal('toggle');
							document.getElementById("submit_confirm_user").disabled = false;
						}else{
							swal("ไม่มีสิทธิ์ทำรายการ");
							document.getElementById("submit_confirm_user").disabled = false;
						}
					}else{
						swal("ตรวจสอบข้อมูลให้ถูกต้อง");
						document.getElementById("submit_confirm_user").disabled = false;
					}
				}
		});

	});
	//CONFIRM USER
</script>
