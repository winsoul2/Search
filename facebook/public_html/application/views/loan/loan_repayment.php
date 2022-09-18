<div class="layout-content">
    <div class="layout-content-body">
<style>
    .form-group { margin-bottom: 0; }
    .border1 { border: solid 1px #ccc; padding: 0 15px; }
    .mem_pic { float: right; width: 150px; }
    .mem_pic img { width: 100%; border: solid 1px #ccc; }
    .mem_pic button { display: block; width: 100%; }

    .hide_error{color : inherit;border-color : inherit;}

    .has-error{color : #d50000;border-color : #d50000;}

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .alert-danger {
        background-color: #F2DEDE;
        border-color: #e0b1b8;
        color: #B94A48;
    }
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
    .modal.fade {
        z-index: 10000000 !important;
    }
</style>
<h1 style="margin-bottom: 0">จ่ายเงินกู้ (คืนวงเงิน)</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>

    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        
    </div>

</div>
<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12">
        <div class="panel panel-body">


            <form action="<?=base_url('loan/save_loan_repayment')?>" method="post" name="myForm" id="myForm" onsubmit="event.preventDefault(); validateMyForm();">
                <div class="g24-col-sm-24" style="margin-bottom: 15px;">
                    <div class="form-group g24-col-sm-8">
                        <label class="g24-col-sm-10 control-label" for="form-control-2">เลขที่สัญญา</label>
                        <div class="g24-col-sm-14">
                            <div class="input-group">
                                <input id="contract_number" class="form-control" type="text" required onkeypress="return runScript(event)" autocomplete="off">
                                <span class="input-group-btn">
                                    <a data-toggle="modal" data-target="#search_loan_modal" id="test" class="fancybox_share fancybox.iframe" href="#">
                                        <button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                                    </a>
                                </span>	
                            </div>
                        </div>
                    </div>

                    <div class="form-group g24-col-sm-16">
                        <label class="g24-col-sm-4 control-label " for="form-control-2">ชื่อ - สกุล</label>
                        <div class="g24-col-sm-14">
                        <input class="form-control " type="text" value="" readonly="" id="name">
                        </div>
                    </div>			  		
                </div>  

                <div class="g24-col-sm-24" style="margin-bottom: 15px;">
                    <div class="form-group g24-col-sm-8">
                        <label class="g24-col-sm-10 control-label" for="form-control-2">วงเงินกู้</label>
                        <div class="g24-col-sm-14">
                            <input  class="form-control " type="text" value="" readonly="" id="loan_amount">
                        </div>
                    </div>

                    <div class="form-group g24-col-sm-16">
                        <label class="g24-col-sm-4 control-label " for="form-control-2">หนี้คงค้าง</label>
                        <div class="g24-col-sm-5">
                        <input class="form-control " type="text" value="" readonly="" id="loan_debt">
                        </div>

                        <label class="g24-col-sm-4 control-label " for="form-control-2">สามารถกู้เงินได้</label>
                        <div class="g24-col-sm-5">
                        <input class="form-control " type="text" value="" readonly="" id="loan_full_request">
                        </div>
                    </div>
                </div>

                <div class="g24-col-sm-24" style="margin-bottom: 15px;">
                    <div class="form-group g24-col-sm-8">
                        <label class="g24-col-sm-10 control-label" for="form-control-2">ต้องการขอรับเงิน</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control " type="text" value="" id="loan_request" name="loan_request" autocomplete="off" required >
                        </div>
                    </div>
                </div>

                <div class="g24-col-sm-24" style="margin-bottom: 15px;">
                    <div class="form-group g24-col-sm-19">
                        <label class="g24-col-sm-4 control-label" for="form-control-2">รูปแบบการรับเงิน</label>
                        <div class="form-group g24-col-sm-19">
                            <div class="g24-col-sm-20">
                                <label class="g24-col-sm-5 control-label text-left">
                                    <input type="radio" name="transfer_type" class="transfer_type" id="transfer_type_1" value="1" onclick="choose_transfer_type(1)" checked> เงินสด
                                </label>
                                <label class="g24-col-sm-9 control-label text-left">
                                    <input type="radio" name="transfer_type" class="transfer_type" id="transfer_type_2" value="2" onclick="choose_transfer_type(2)"> เงินโอนเข้าบัญชีสหกรณ์
                                </label>
                                <label class="g24-col-sm-10 control-label text-left">
                                    <input type="radio" name="transfer_type" class="transfer_type" id="transfer_type_3" value="3" onclick="choose_transfer_type(3)"> เงินโอนเข้าบัญชีธนาคารอื่นๆ
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="g24-col-sm-24" style="margin-bottom: 15px;" id="type_2" style="display: none;">
                    <div class="form-group g24-col-sm-19">
                        <label class="g24-col-sm-4 control-label" for="form-control-2">บัญชีสหกรณ์</label>
                        <div class="g24-col-sm-9">
                            <select name="account_id" id="account_id" class="form-control">
                                <option value="">เลือกบัญชีสหกรณ์</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="g24-col-sm-24" style="margin-bottom: 15px;"  id="type_3" style="display: none;">
                    <div class="form-group g24-col-sm-8">
                        <label class="g24-col-sm-10 control-label" for="form-control-2">ธนาคาร</label>
                        <div class="g24-col-sm-14">
                            <select name="bank_id" id="bank_id" class="form-control">
                                <option value="">เลือกธนาคาร</option>
                                <?php
                                    foreach ($bank as $key => $value) {
                                        echo "<option value='".$value->bank_id."'>".$value->bank_name."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group g24-col-sm-16">
                        <label class="g24-col-sm-4 control-label " for="form-control-2">สาขา</label>
                        <div class="g24-col-sm-5">
                            <input type="text" name="branch_code" id="branch_code" class="form-control">
                        </div>

                        <label class="g24-col-sm-4 control-label " for="form-control-2">เลขบัญชี</label>
                        <div class="g24-col-sm-5">
                        <input id="account_no" class="form-control" type="text" value="" name="account_no" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 15px;margin-bottom: 15px;">
                    <div class="col-md-offset-4 col-md-4 text-center" >
                        <input type="hidden" id="loan_id" name="loan_id">
                        <button class="btn btn-primary" id="submit_button" type="submit">บันทึก</button>
                    </div>
                </div>

                <div id="table_repayment">
                
                </div>


            </form>


            

        </div>

    </div>
</div>
    </div>
</div>






<div class="modal fade" id="search_loan_modal" role="dialog"> 
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">ข้อมูลสมาชิก</h4>
        </div>
        <div class="modal-body">
       		<div class="">
				<div class="row">
					<div class="col">
						<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
						<div class="col-sm-4">
							<div class="form-group">
								<select id="member_search_list" name="member_search_list" class="form-control m-b-1">
									<option value="">เลือกรูปแบบค้นหา</option>
                                    <option value="loan_prefix">เลขสัญญา</option>
									<option value="member_id">รหัสสมาชิก</option>
									<option value="id_card">หมายเลขบัตรประชาชน</option>
									<option value="firstname_th">ชื่อสมาชิก</option>
									<option value="lastname_th">นามสกุล</option>
								</select>
							</div>
						</div>
						<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
						<div class="col-sm-5">
							<div class="form-group">
								<div class="input-group">
								<input id="member_search_text" name="member_search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>" autocomplete="off">
								<span class="input-group-btn">
									<button type="button" id="member_loan_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
								</span>	
								</div>
							</div>
						</div>
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
    var base_url = $('#base_url').attr('class');

    
    $( "#loan_request" ).change(function() {
        var max_loan = numeral($("#loan_full_request").val()).value();
        var amount = numeral($("#loan_request").val()).value();
        if(amount>max_loan){
            swal('ไม่สามารถขอรับเงินเกิน '+numeral(max_loan).format('0,0.00')+' บาท','','warning');
            $("#loan_request").val(numeral(max_loan).format('0,0.00'));
        }else{
            $("#loan_request").val(numeral(amount).format('0,0.00'));
        }

        
    });

    // $( "#bank_id" ).change(function() {
    //     var val = $(this).val();
    //     $.ajax({
    //             url: base_url+"ajax/get_bank_branch",
    //             method:"post",  
    //             data: {
    //                 bank : val
    //             },  
    //             dataType:"text",  
    //             success:function(data) {
    //                 $('#branch_code').html(data);  
    //             }  ,
    //             error: function(xhr){
    //                 console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
    //             }
    //         });
    // });

    function get_data(member_id, name, loan_amount, loan_amount_balance, loan_id, contract_number, age, show_modal = true){
        
        if(show_modal){
            $('#search_loan_modal').modal('toggle');
        }
        loan_amount = parseInt(loan_amount);
        loan_amount_balance = parseInt(loan_amount_balance);
        var debt = loan_amount - loan_amount_balance
        
        if(age>=60){
            swal('ไม่สามารถทำรายการได้ อายุเกิน 60 ปี','','warning');
            return false;
        }else{
            $("#name").val(name);
            $("#loan_amount").val( numeral(loan_amount).format('0,0.00') );
            $("#loan_debt").val( numeral(loan_amount_balance).format('0,0.00') );
            $("#loan_full_request").val( numeral(debt).format('0,0.00') );
            $("#loan_request").val(0);
            $("#contract_number").val(contract_number);
            $("#loan_id").val(loan_id);
            
            //loan marco 
            $.ajax({
                    url: base_url+"ajax/get_coop_maco",
                    method:"post",  
                    data: {
                        member_id : member_id
                    },  
                    dataType:"text",  
                    success:function(data) {
                        $('#account_id').html(data);  
                    }  ,
                    error: function(xhr){
                        console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                    }
                });
            //loan repayment 
            $.ajax({
                    url: base_url+"ajax/get_loan_repayment",
                    method:"post",  
                    data: {
                        loan_id : $("#loan_id").val()
                    },  
                    dataType:"text",  
                    success:function(data) {
                        $('#table_repayment').html(data);  
                    }  ,
                    error: function(xhr){
                        console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                    }
                });
            }
        
    }

    $('#member_loan_search').click(function(){
        if($('#member_search_list').val() == '') {
            swal('กรุณาเลือกรูปแบบค้นหา','','warning');
        } else if ($('#member_search_text').val() == ''){
            swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
        } else {
            $.ajax({
                url: base_url+"ajax/search_loan_repayment",
                method:"post",  
                data: {
                    search_text : $('#member_search_text').val(), 
                    search_list : $('#member_search_list').val()
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

    function validateMyForm(){

        var contract_number = $("#contract_number").val();
        var loan_id = $("#contract_number").val();
        var loan_request = numeral($("#loan_request").val()).value();
        if(contract_number=="" || loan_id=="" || loan_request <= 0)
        { 
            swal('ตรวจสอบข้อมูลให้ถูกต้อง','','warning');
            return false;
        }

        var transfer_type = $('input[name=transfer_type]:checked', '#myForm').val();

        if(transfer_type=="3"){
            var bank_id = $("#bank_id").val();
            var branch_code = $("#branch_code").val();
            var account_no = $("#account_no").val();

            if(bank_id=="" || branch_code=="" || account_no==""){
                swal('ตรวจสอบข้อมูลให้ถูกต้อง','','warning');
                return false;
            }
        }

        document.getElementById("myForm").submit();
        return true;
    }

    function runScript(e) {
        //See notes about 'which' and 'key'
        if (e.keyCode == 13) {
            var search = $("#contract_number").val();
            if(search!=""){
                $.ajax({
                    url: base_url+"ajax/search_loan_repayment_by_contract_number",
                    method:"post",  
                    data: {
                        search_text : search
                    },  
                    dataType:"text",  
                    success:function(data) {
                        // $('#result_member_search').html(data); 
                        if(data=="FALSE"){
                            swal('ไม่พบข้อมูลนี้','','warning');
                        }else{
                            var row = JSON.parse(data);
                            console.log(row);
                            console.log(row['member_id']);
                            var member_id = row['member_id'];
                            var name = row['firstname_th']+" "+row['lastname_th'];
                            var loan_amount = row['loan_amount'];
                            var loan_amount_balance = row['loan_amount_balance'];
                            var loan_id = row['l_id'];
                            var contract_number = row['contract_number'];
                            var age = row['age'];
                            get_data(member_id, name, loan_amount, loan_amount_balance, loan_id, contract_number, age, false);
                        }
                        
                    }  ,
                    error: function(xhr){
                        console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                    }
                });

                return false;
            }
            
        }
    }

    function choose_transfer_type(type_id){
        if(type_id == 1){
            $("#type_2").hide();
            $("#type_3").hide();
        }else if(type_id == 2){
            $("#type_2").show();
            $("#type_3").hide();
        }else{
            $("#type_2").hide();
            $("#type_3").show();
        }
    }

    function init(search){
            if(search!=""){
                $.ajax({
                    url: base_url+"ajax/search_loan_repayment_by_contract_number",
                    method:"post",  
                    data: {
                        search_text : search
                    },  
                    dataType:"text",  
                    success:function(data) {
                        // $('#result_member_search').html(data); 
                        if(data=="FALSE"){
                            swal('ไม่พบข้อมูลนี้','','warning');
                        }else{
                            var row = JSON.parse(data);
                            console.log(row);
                            console.log(row['member_id']);
                            var member_id = row['member_id'];
                            var name = row['firstname_th']+" "+row['lastname_th'];
                            var loan_amount = row['loan_amount'];
                            var loan_amount_balance = row['loan_amount_balance'];
                            var loan_id = row['l_id'];
                            var contract_number = row['contract_number'];
                            var age = row['age'];
                            get_data(member_id, name, loan_amount, loan_amount_balance, loan_id, contract_number, age, false);
                        }
                        
                    }  ,
                    error: function(xhr){
                        console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                    }
                });

                return false;
            }
    }

    $("form").keypress(function(e) {
        //Enter key
        if (e.which == 13) {
            return false;
        }
    });

    jQuery("#type_2").hide();
    jQuery("#type_3").hide();

    <?php
        if($save_status==1){
            ?>
                $( document ).ready(function() {
                    init('<?=$contract_number?>');
                    swal("บันทึก!", "ทำรายการสำเร็จแล้ว", "success");
                });
            <?php
            CI_Input::set_cookie("save_status", '0', 10);
        }
    ?>



</script>


<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>