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
<h1 style="margin-bottom: 0">การตรวจสอบภาระค้ำประกัน</h1>
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
                    <div class="form-group g24-col-sm-12">
                        <label class="g24-col-sm-10 control-label" for="form-control-2">เลขที่สมาชิก</label>
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

                    <div class="form-group g24-col-sm-12">
                        <label class="g24-col-sm-4 control-label " for="form-control-2">ชื่อ - สกุล</label>
                        <div class="g24-col-sm-14">
                        <input class="form-control " type="text" value="" readonly="" id="name">
                        </div>
                    </div>			  		
                </div>  

                <div class="g24-col-sm-24" style="margin-bottom: 15px;">
                    <div class="form-group g24-col-sm-12">
                        <label class="g24-col-sm-10 control-label" for="form-control-2">สังกัด</label>
                        <div class="g24-col-sm-14">
                         <input class="form-control " type="text" value="" readonly="" id="department">
                        </div>
                    </div>

                    <div class="form-group g24-col-sm-12">
                        <label class="g24-col-sm-4 control-label " for="form-control-2">กลุ่ม</label>
                        <div class="g24-col-sm-14">
                        <input class="form-control " type="text" value="" readonly="" id="level">
                        </div>
                    </div>			  		
                </div>  



                <!-- <div class="row" style="margin-top: 15px;margin-bottom: 15px;">
                    <div class="col-md-offset-4 col-md-4 text-center" >
                        <br>
                        <input type="hidden" id="loan_id" name="loan_id">
                        <button class="btn btn-primary" id="submit_button" type="submit">บันทึก</button>
                    </div>
                </div> -->

                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#home">สัญญาที่ได้รับการค้ำประกัน</a></li>
                    <li><a data-toggle="tab" href="#menu1">สัญญาที่ค้ำประกันผู้อื่น</a></li>
                </ul>

                <div class="tab-content">
                    <div id="home" class="tab-pane fade in active">
                        <h3>สัญญาที่ได้รับการค้ำประกัน</h3>
                        <div id="guarantee_1"></div>
                    </div>
                    <div id="menu1" class="tab-pane fade">
                        <h3>สัญญาที่ค้ำประกันผู้อื่น</h3>
                        <div id="guarantee_2"></div>
                    </div>
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
									<option value="employee_id">รหัสพนักงาน</option>
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

    function get_data(member_id, name, department, level, show_modal = true){
        console.log(member_id);
        if(show_modal){
            $('#search_loan_modal').modal('toggle');
        }

        
            $("#name").val(name);
            // $("#contract_number").val(contract_number);
            // $("#loan_id").val(loan_id);
            $("#department").val(department);
            $("#level").val(level);

            $.ajax({
                    url: base_url+"ajax/get_loan_guarantee",
                    method:"post",  
                    data: {
                        member_id : member_id
                    },  
                    dataType:"json",  
                    success:function(data) {
                        // $('#account_id').html(data);  
                        setResultHtml(data);
                        
                    }  ,
                    error: function(xhr){
                        console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                    }
                });
        
    }

    $('#member_loan_search').click(function(){
        if($('#member_search_list').val() == '') {
            swal('กรุณาเลือกรูปแบบค้นหา','','warning');
        } else if ($('#member_search_text').val() == ''){
            swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
        } else {
            $.ajax({
                url: base_url+"ajax/search_loan_guarantee",
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
                    url: base_url+"ajax/search_loan_guarantee_by_member_id",
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
                            var department = row['department_name'];
                            var level = row['level_name'];
                            get_data(member_id, name, department, level, false);
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
                    url: base_url+"ajax/search_loan_guarantee_by_member_id",
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
                            var department = row['department_name'];
                            var level = row['level_name'];
                            get_data(member_id, name, department, level, false);
                        }
                        
                    }  ,
                    error: function(xhr){
                        console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                    }
                });

                return false;
            }
    }

    function setResultHtml(data){
        console.log(data);
        var name = $("#name").val();
        var data1 = data.guarantee_1;
        var data2 = data.guarantee_2;
        var data3 = data.guarantee_3;

        var tab1 = "";
        tab1 += "<table class='table table-bordered table-striped table-center'>";
        tab1 += "<thead>";
        tab1 += "<tr><th colspan=5>สัญญาที่ได้รับค้ำประกันของ "+name+"</th></tr>";
        tab1 += "<tr class='bg-primary'>";
		tab1 += "<td>ประเภทของสัญญาเงินกู้</td>";
        tab1 += "<td>สัญญาเลขที่</td>";
        tab1 += "<td>ชื่อผู้ค้ำประกัน</td>";
        tab1 += "<td>เลขสมาชิก</td>";
        tab1 += "<td>วงเงินอนุมัติ</td>";
        tab1 += "<td>สถานะสัญญา</td>";
        tab1 += "</tr>";
        tab1 += "<thead>";
        tab1 += "<tbody>";
        
        data1.forEach(element => {
            // console.log(element);
            tab1 += "<tr>";
			tab1 += "<td>"+element.loan_type_detail+""+element.loan_name_description+"</td>";
            tab1 += "<td>"+element.contract_number+"</td>";
            var list_guarantor = "<u style='line-height: 26px;'>";
            var list_member_id = "<u style='line-height: 26px;'>";
            element.have_guarantee.forEach(element_gua => {
                list_guarantor += "<li>"+element_gua.name+"</li>";
                list_member_id += "<li>"+element_gua.member_id+"</li>";
            });
            
            list_guarantor += "</u>";
            list_member_id += "</u>";
            tab1 += "<td class='text-left'>"+list_guarantor+"</td>";
            tab1 += "<td>"+list_member_id+"</td>";
            tab1 += "<td>"+element.loan_amount+"</td>";
            tab1 += "<td>"+element.status+"</td>";
            tab1 += "</tr>";
        });

        tab1 += "</tbody>";
        tab1 += "</table>";

        var tab2 = "";
        tab2 += "<table class='table table-bordered table-striped table-center'>";
        tab2 += "<thead>";
        tab2 += "<tr><th colspan=9>สัญญาที่ "+name+" ค้ำประกันผู้อื่น</th></tr>";
        tab2 += "<tr class='bg-primary'>";
        tab2 += "<td>ลำดับที่</td>";
		tab2 += "<td>ประเภทของสัญญาเงินกู้</td>";
        tab2 += "<td>สัญญาเลขที่</td>";
        tab2 += "<td>ชื่อ-สกุลผู้กู้ </td>";
        tab2 += "<td>เลข สมาชิก</td>";
        tab2 += "<td>วงเงินอนุมัติ</td>";
        tab2 += "<td>ยอดคงเหลือ</td>";
        tab2 += "<td>วงเงินค้ำประกัน</td>";
        tab2 += "<td>สถานะสัญญา</td>";
        tab2 += "<td>ผู้ค้ำประกันทั้งหมด </td>";
        tab2 += "</tr>";
        tab2 += "<thead>";
        tab2 += "<tbody>";
        
        var no = 1;
        data2.forEach(element_main => {
            console.log(element_main);
            element_main.guarantee.forEach(element => {
                tab2 += "<tr>";
                tab2 += "<td>"+(no++)+"</td>";
				tab2 += "<td>"+element.loan_type_detail+""+element.loan_name_description+"</td>";
                tab2 += "<td>"+element.loan_name_short+""+element.contract_number+"</td>";
                tab2 += "<td class='text-left'>"+element.name+"</td>";
                tab2 += "<td>"+element.member_id+"</td>";
                tab2 += "<td>"+element.loan_amount+"</td>";
                tab2 += "<td>"+element.loan_amount_balance+"</td>";
                tab2 += "<td>"+(element.guarantee_person_amount==null ? 0 : element.guarantee_person_amount)+"</td>";
                tab2 += "<td>"+element.status+"</td>";
                tab2 += "<td>"+(element.count==null ? 0 : element.count)+"</td>";
                tab2 += "</tr>";
            });
            
        });
        tab2 += "<tr>";
        tab2 += "<td colspan=6 class='text-left'>วงเงินค้ำประกันทั้งหมด</td>";
        tab2 += "<td>"+data3.income+"</td>";
        tab2 += "<td></td>";
        tab2 += "<td></td>";
        tab2 += "<td></td>";
        tab2 += "</tr>";
        tab2 += "<tr>";
        tab2 += "<td colspan=6 class='text-left'>วงเงินค้ำประกันใช้ไป </td>";
        tab2 += "<td>"+data3.total_guarantee+"</td>";
        tab2 += "<td></td>";
        tab2 += "<td></td>";
        tab2 += "<td></td>";
        tab2 += "</tr>";
        tab2 += "<tr>";
        tab2 += "<td colspan=6 class='text-left'>วงเงินค้ำประกันคงเหลือ</td>";
        tab2 += "<td>"+data3.amount_total_guarantee+"</td>";
        tab2 += "<td></td>";
        tab2 += "<td></td>";
        tab2 += "<td></td>";
        tab2 += "</tr>";
        tab2 += "</tbody>";
        tab2 += "</table>";

        $("#guarantee_1").html(tab1);
        $("#guarantee_2").html(tab2);
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
