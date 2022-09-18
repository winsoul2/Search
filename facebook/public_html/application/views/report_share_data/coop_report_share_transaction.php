<div class="layout-content">
    <div class="layout-content-body">
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

            @media (min-width: 768px) {
                .modal-dialog {
                    width: 700px;
                }
            }
            .form-group{
                margin-bottom: 5px;
            }
		</style>

		<h1 style="margin-bottom: 0">รายงานความเคลื่อนไหวหุ้น</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<h3></h3>
					<div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-5 control-label right">รหัสสมาชิก</label>
                        <div class="g24-col-sm-3">
                            <div class="input-group">
                                <input id="form-control-2"  class="form-control member_id" type="text" value="<?php echo $member_id; ?>" onkeypress="check_member_id();">
                                <span class="input-group-btn">
                                    <a data-toggle="modal" data-target="#myModal" id="test" class="fancybox_share fancybox.iframe" href="#">
                                        <button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                                    </a>
                                </span>
                            </div>
                        </div>
					</div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-5 control-label right">ชื่อสกุล</label>
                        <div class="g24-col-sm-7">
                                <input id="form-control-2" class="form-control " style="width:100%" type="text" value="<?php echo $member_name; ?>"  readonly>
                        </div>
					</div>
                    <form action="<?php echo base_url(PROJECTPATH.'/report_share_data/coop_report_share_transaction_preview'); ?>" id="form1" method="GET" target="_blank">
                        <input id="member_id" type="hidden" name="member_id" value="<?php echo $member_id; ?>"/>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-5 control-label right"> รูปแบบ </label>
                            <div class="g24-col-sm-4">
                                <select name="type" id="type" onchange="" class="form-control">
                                    <option value="1">ทั้งหมดถึงวันที่เลือก</option>
                                    <option value="2">ช่วงเวลาที่กำหนด</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-5 control-label right"> วันที่ </label>
                            <div class="g24-col-sm-4 type_op">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                            <label class="g24-col-sm-1 control-label right type_op"> ถึง </label>
                            <div class="g24-col-sm-4">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="end_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-5 control-label right"></label>
                            <div class="g24-col-sm-3">
                                <input type="button" class="btn btn-primary" style="width:100%" value="แสดงรายงาน" onclick="change_type()">
                            </div>
                            <div class="g24-col-sm-3">
                                <input type="button" class="btn btn-default" style="width:100%" value="Export Excel" onclick="export_excel()">
                            </div>
                        </div>
                    </form>
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

        $(".type_op").hide();

        $("#type").change(function() {
            if ($(this).val() == 1) {
                $(".type_op").hide();
            } else {
                $(".type_op").show();
            }
        });
    });

	function change_type(){
        if($('#member_id').val() == ''){
			swal("กรุณาเลือกหมายเลขสมาชิก");
			return false;
		}

        $.ajax({
			url: base_url+'/report_share_data/check_coop_report_share_transaction',	
			method:"get",
			data: $("#form1").serializeArray(),
			dataType:"text",
			success:function(data){
                if(data == 'success'){
                    link_to =  base_url+'report_share_data/coop_report_share_transaction_preview';
                    $('#form1').attr('action', link_to);
                    $('#form1').submit();
                }else{
                    $('#alertNotFindModal').appendTo("body").modal('show');
                }
			}
		});
	}

	function export_excel() {
        if($('#member_id').val() == ''){
			swal("กรุณาเลือกหมายเลขสมาชิก");
			return false;
		}

        $.ajax({
			url: base_url+'/report_share_data/check_coop_report_share_transaction',
			method:"get",
			data: $("#form1").serializeArray(),
			dataType:"text",
			success:function(data){
                if(data == 'success'){
                    link_to =  base_url+'report_share_data/coop_report_share_transaction_excel';
                    $('#form1').attr('action', link_to);
                    $('#form1').submit();
                }else{
                    $('#alertNotFindModal').appendTo("body").modal('show');
                }
			}
		});
	}

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
</script>


