<div class="layout-content">
    <div class="layout-content-body">
		<?php
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		?>
		<style>
			.modal-header-alert {
				padding:9px 15px;
				border:1px solid #FF0033;
				background-color: #FF0033;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}
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
		</style>

		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">หนังสือรับรองหุ้นหนี้</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
                    <form action="<?php echo base_url(PROJECTPATH.'/certificate/cert_share_loan'); ?>" id="form1" method="GET">
                        <input type="hidden" name="do_search" value="Y"/>
                        <input id="member_id" type="hidden" name="member_id" value="<?php echo $member_id; ?>"/>
                        <h3></h3>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ข้อมูล ณ วันที่ </label>
                            <div class="g24-col-sm-4">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="date" name="date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date($date); ?>" data-date-language="th-th">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ประเภท</label>
                            <div class="g24-col-sm-18">
                                <span id="span-type" style="">
                                    <input type="radio" name="type" id="type_0" value="" checked="checked"> ทั้งหมด &nbsp;&nbsp;
                                    <input type="radio" name="type" id="type_1" value="share"> หุ้น &nbsp;&nbsp;
                                    <input type="radio" name="type" id="type_2" value="loan"> หนี้ &nbsp;&nbsp;
                                    <input type="radio" name="type" id="type_3" value="deposit"> เงินฝาก &nbsp;&nbsp;
                                </span>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ค้นหาจาก</label>
                            <div class="g24-col-sm-18">
                                <span id="show_pay_type2" style="">
                                    <input type="radio" name="search_type" id="search_type_0" value="0" checked="checked"> ค้นหาจากเลขสมาชิก &nbsp;&nbsp;
                                    <input type="radio" name="search_type" id="search_type_1" value="1"> ค้นหาตามหน่วยงาน &nbsp;&nbsp;
                                </span>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24 member_search_div">
                            <label class="g24-col-sm-6 control-label right">เลขสมาชิก</label>
                            <div class="g24-col-sm-4">
                                <div class="input-group">
                                    <input id="form-control-2" name="member_search"  class="form-control member_id" type="text" value="<?php echo $member_id; ?>" onkeypress="check_member_id();">
                                    <span class="input-group-btn">
                                        <a data-toggle="modal" data-target="#myModal" id="test" class="fancybox_share fancybox.iframe" href="#">
                                            <button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                                        </a>
                                    </span>
                                </div>
                            </div>
                            <label class="g24-col-sm-2 control-label right">ชื่อสกุล</label>
                            <div class="g24-col-sm-6">
                                <input id="form-control-2" class="form-control" style="width:100%" type="text" value="<?php echo $member_name; ?>" readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24 department_search_div">
                            <label class="g24-col-sm-6 control-label right show_department"> สังกัดหน่วยงาน </label>
                            <div class="g24-col-sm-4 show_department">
                                <select name="department" id="department" onchange="change_mem_group('department', 'faction')" class="form-control">
                                    <option value="">เลือกข้อมูล</option>
                                    <?php 
                                        foreach($row_mem_group as $key => $value){
                                        ?>
                                        <option value="<?php echo $value['id']; ?>" <?php if(!empty($_GET['department']) && $_GET['department'] == $value['id']) echo "selected"?>><?php echo $value['mem_group_name']; ?></option>
                                    <?php 
                                    } ?>
                                </select>
                            </div>
                            <label class="g24-col-sm-1 control-label right show_level"> อำเภอ </label>
                            <div class="g24-col-sm-4 show_level">
                                <select name="faction" id="faction" onchange="change_mem_group('faction','level')" class="form-control">
                                    <option value="">เลือกข้อมูล</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24 show_level department_search_div">
                            <label class="g24-col-sm-6 control-label right"> หน่วยงานย่อย </label>
                            <div class="g24-col-sm-4">
                                <select name="level" id="level" class="form-control">
                                    <option value="">เลือกข้อมูล</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"></label>
                            <div class="g24-col-sm-4">
                                <input type="button" class="btn btn-primary" style="width:100%" value="ดึงข้อมูล" onclick="check_empty()">
                            </div>
                        </div>
                    </form>
                    <?php
                        if(!empty($datas)) {
                    ?>
                    <div class="g24-col-sm-24 m-t-1">
                        <div class="bs-example" data-example-id="striped-table">
                            <table class="table table-bordered table-striped table-center" id="table">	
                                <thead>
                                    <tr class="bg-primary">
                                        <th class="font-normal" style="width: 5%">ลำดับ</th>
                                        <th class="font-normal" >เลขที่สมาชิก</th>
                                        <th class="font-normal" >ชื่อ-นามสกุล</th> 
                                        <th class="font-normal r_hidden" >
                                        <?php
                                            if (!empty($_GET["department"])) {
                                        ?>
                                            <a class="" onclick="print_pdf('all')" style="color: #fff !important;">
                                                <span class="icon icon-print"></span>
                                                พิมพ์ทั้งหมด
                                            </a>
                                        <?php
                                            }
                                        ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if (!empty($_GET["page"])) {
                                            $i= ((intval($_GET["page"]) - 1) * 20);
                                        } else {
                                            $i=0;
                                        }
                                        foreach($datas as $data) {
                                            $i++;
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $data["member_id"]; ?></td>
                                        <td><?php echo $data["prename_full"].$data["firstname_th"]." ".$data["lastname_th"]; ?></td>
                                        <td>
                                            <a class="" onclick="print_pdf('<?php echo $data['member_id']; ?>')">
                                                <span class="icon icon-print"></span>
                                                พิมพ์ PDF
                                            </a>
                                        </td>
                                    </tr>
                                    <?php      
                                        }
                                    ?>
                                </tbody>
                            </table>
                            <div class="text-center">
                                <?php
                                    echo $paging;
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
				</div>
			</div>
		</div>
	</div>
    
</div>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
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
                                <select id="search_list" name="search_list" class="form-control m-b-1">
                                    <option value="">เลือกรูปแบบค้นหา</option>
                                    <option value="member_id">รหัสสมาชิก</option>
									<option value="employee_id">รหัสพนักงาน</option>
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
                                    <input id="search_text" name="search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>">
                                    <span class="input-group-btn">
                                        <button type="button" id="member_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div class="bs-example" data-example-id="striped-table">
				<table class="table table-striped">
                    <tbody id="result_member">
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
<script>	
	$( document ).ready(function() {
        $(".department_search_div").hide();
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

        $('#member_search').click(function(){
            if($('#search_list').val() == '') {
                swal('กรุณาเลือกรูปแบบค้นหา','','warning');
            } else if ($('#search_text').val() == ''){
                swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
            } else {
                $.ajax({
                    url: base_url+"ajax/search_member_by_type",
                    method:"post",
                    data: {
                        search_text : $('#search_text').val(),
                        search_list : $('#search_list').val()
                    },  
                    dataType:"text",  
                    success:function(data) {
                        $('#result_member').html(data);
                    }  ,
                    error: function(xhr){
                        console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                    }
                });  
            }
        });
        $('input[type=radio][name=search_type]').change(function() {
            if (this.value == '0') {
                $(".member_search_div").show();
                $(".department_search_div").hide();
            } else if (this.value == '1') {
                $(".department_search_div").show();
                $(".member_search_div").hide();
            }
        });

        if('<?php echo $_GET['search_type'];?>' == '1') {
            $("#search_type_1").prop("checked", true);
            $(".department_search_div").show();
            $(".member_search_div").hide();
        }

        if('<?php echo $_GET['department'];?>' != '') {
            change_mem_group_first_access('department', 'faction');
            if ('<?php echo $_GET['faction'];?>' != '') {
                $('#faction  option[value="<?php echo $_GET['faction'];?>"]').prop("selected", true);
            }
        }
	});	

    function change_mem_group_first_access(id, id_to){
		var mem_group_id = $('#'+id).val();
		$('#level').html('<option value="">เลือกข้อมูล</option>');
		$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/get_mem_group_list',
			data: {
				mem_group_id : mem_group_id
			},
			success: function(msg){
				$('#'+id_to).html(msg);
                if (id_to == "faction" && '<?php echo $_GET['faction'];?>' != '') {
                    $('#faction  option[value="<?php echo $_GET['faction'];?>"]').prop("selected", true);
                    change_mem_group_first_access("faction", "level")
                } else if  (id_to == "level" && '<?php echo $_GET['level'];?>' != '') {
                    $('#level  option[value="<?php echo $_GET['level'];?>"]').prop("selected", true);
                }
			}
		});
	}

    function check_member_id() {
        var member_id = $('.member_id').first().val();
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            $.post(base_url+"save_money/check_member_id",
            {
                member_id: member_id
            }
            , function(result){
                obj = JSON.parse(result);
                mem_id = obj.member_id;
                if(mem_id != undefined){
                    document.location.href = '<?php echo base_url(uri_string())?>?member_id='+mem_id
                }else{
                    swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning');
                }
            });
        }
    }

    function change_mem_group(id, id_to){
		var mem_group_id = $('#'+id).val();
		$('#level').html('<option value="">เลือกข้อมูล</option>');
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

	function check_empty(){
        if(!$('#department').val()) {
			swal('กรุณาเลือกสังกัดหน่วยงาน','','warning');
		} else {
            $('#form1').submit();
        }
	}

    function print_pdf(id) {
        $.blockUI({
            message: 'กรุณารอสักครู่...',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .5,
                color: '#fff'
            },
            baseZ: 2000,
            bindEvents: false
        });
        type = $('input[name=type]:checked').val();
        if(id == 'all') {
            $.ajax({
                url: base_url+'/certificate/check_cert_confirm_share_loan?department=<?php echo $_GET['department']?>&faction=<?php echo $_GET['faction']?>&level=<?php echo $_GET['level']?>&date='+$("#date").val()+'&type='+type,	
                method:"get",
                dataType:"text",
                success:function(data){
                    $.unblockUI();
                    if(data == 'success'){
                        window.open(base_url+"certificate/cert_share_loan_pdf?department=<?php echo $_GET['department']?>&faction=<?php echo $_GET['faction']?>&level=<?php echo $_GET['level']?>&date="+$("#date").val()+'&type='+type, "_blank");
                    }else{
                        $('#alertNotFindModal').appendTo("body").modal('show');
                    }
                }
            });
        } else {
            $.ajax({
                url: base_url+"/certificate/check_cert_confirm_share_loan?member_id="+id+"&date="+$("#date").val()+'&type='+type,	
                method:"get",
                dataType:"text",
                success:function(data){
                    console.log(data);
                    $.unblockUI();
                    if(data == 'success'){
                        window.open(base_url+"certificate/cert_share_loan_pdf?member_id="+id+"&date="+$("#date").val()+'&type='+type, "_blank");
                    }else{
                        $('#alertNotFindModal').appendTo("body").modal('show');
                    }
                }
            });
        }
    }
</script>
