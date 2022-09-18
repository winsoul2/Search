<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
            .form-group{
                margin-bottom: 5px;
            }
            .btn-position-top{
                margin-top:  -1em;
            }
            .text-success{
                color:#5cb85c;
            }
            .pointer{
                cursor: pointer;
            }
		</style>
		<h1 style="margin-bottom: 0">ติดตามการชำระเงิน</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
                <button id="btn_port" type="button" class="btn btn-primary btn-lg btn-position-top">
                    <span>พิมพ์รายงาน</span>
                </button>
                <button name="btn_print" id="btn_print" type="button" class="btn btn-primary btn-lg btn-position-top">
                    <span>พิมพ์จดหมาย</span>
                </button>
            </div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<h3 ></h3>
					<form data-toggle="validator" method="get" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="myForm">
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ปี </label>
                            <div class="g24-col-sm-4">
                                <select id="year" name="year" class="form-control">
                                    <option value=""></option>
                                    <?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
                                        <option value="<?php echo $i; ?>" <?php echo !empty($_GET["year"]) &&  $i == $_GET["year"] ?'selected':''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label class="g24-col-sm-1 control-label right"> เดือน </label>
                            <div class="g24-col-sm-4">
                                <select id="month" name="month" class="form-control">
                                    <option value=""></option>
                                    <?php foreach($month_arr as $key => $value){ ?>
                                        <option value="<?php echo $key; ?>" <?php echo !empty($_GET["month"]) &&  $key == $_GET["month"] ?'selected':''; ?>><?php echo $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ค้นหา </label>
                            <div class="g24-col-sm-9">
                                <input class="form-control" type="text" id="search_text" name="search_text" value="<?php echo !empty($_GET["search_text"]) ? $_GET["search_text"] : "" ;?>" placeholder="ป้อนชื่อ หรือ นามสกุล"/>
                            </div>
                            <div class="g24-col-sm-3">
                                <input type="submit" class="btn btn-primary" style="width:100%" value="แสดงผล">
                            </div>
                        </div>
                    </form>
                    <table class="table table-bordered table-striped table-center">
                        <thead>
                            <tr class="bg-primary">
                                <th style="width: 45px;">
                                    <div class="form-check">
                                        <input type="checkbox" name="chk-approve" value="" id="chk-all" onclick="check_all_page()">
                                    </div>
                                </th>
                                <th style="width: 80px;">ลำดับ</th>
                                <th>ปี</th>
                                <th>เดือน</th>
                                <th>เลขฌาปนกิจ</th>
                                <th>ชื่อสกุล</th>
                                <th>รหัสสมาชิก</th>
                                <th>ค้างชำระ</th>
                                <th>ครั้งที่ 1</th>
                                <th>ครั้งที่ 2</th>
                                <th style="width:150px;"></th> 
                            </tr>
                        </thead>
                        <tbody id="table_first">
                            <form action="<?php echo base_url(PROJECTPATH.'/cremation/save_debt_letter'); ?>" id="form2" method="POST">
                        <?php
                            foreach($datas as $data) {
                        ?>
                            <tr>
                                <td>
                                <?php if(empty($data["second_letter_id"])) { ?>
                                    <input type="checkbox" name="non_pay_ids[]" class="non-pay-checkbox" value="<?php echo $data['non_pay_id'];?>" id="chk-<?php echo $data["non_pay_id"];?>">
                                <?php } ?>
                                </td>
                                <td class="text-center"><?php echo $page_start++?></td>
                                <td><?php echo $month_arr[(int) $data["non_pay_month"]]?></td>
                                <td><?php echo $data["non_pay_year"];?></td>
                                <td>
                                <?php
                                    foreach($data["member_cremation_ids"] as $key => $member_cremation_id) {
                                        echo $key == 0 ? $member_cremation_id["member_cremation_id"] : ", ".$member_cremation_id["member_cremation_id"];
                                    }
                                ?>
                                </td>
                                <td><?php echo $data["prename_full"].$data["firstname_th"]." ".$data["lastname_th"]?></td>
                                <td><?php echo $data["member_id"];?></td>
                                <td><?php echo number_format($data["non_pay_amount_balance"],2);?></td>
                                <td>
                                <?php
                                    if(!empty($data["first_letter_id"])) {
                                ?>
                                    <span id="first-letter-<?php echo $data["non_pay_id"];?>" class="icon icon-check text-success pointer print-letter" data-runno="1" data-non-pay-id="<?php echo $data["non_pay_id"];?>" data-letter-id="<?php echo $data["first_letter_id"];?>" title="พิมพ์จดหมาย"></span>
                                <?php
                                    } else {
                                ?>
                                    <span id="first-letter-<?php echo $data["non_pay_id"];?>" class="icon icon-check text-danger pointer print-letter-new" data-runno="1" data-non-pay-id="<?php echo $data["non_pay_id"];?>" title="พิมพ์จดหมาย"></span>
                                <?php
                                    }
                                ?>
                                </td>
                                <td>
                                <?php
                                    if(!empty($data["first_letter_id"])) {
                                        if(!empty($data["second_letter_id"])) {
                                ?>
                                    <span id="second-letter-<?php echo $data["non_pay_id"];?>" class="icon icon-check text-success pointer print-letter print-letter" data-runno="2" data-non-pay-id="<?php echo $data["non_pay_id"];?>" data-letter-id="<?php echo $data["second_letter_id"];?>" title="พิมพ์จดหมาย"></span>
                                <?php
                                        } else {
                                ?>
                                    <span id="second-letter-<?php echo $data["non_pay_id"];?>"  class="icon icon-check text-danger pointer print-letter print-letter-new" data-runno="2" data-non-pay-id="<?php echo $data["non_pay_id"];?>" title="พิมพ์จดหมาย"></span>
                                <?php
                                        }
                                    }
                                ?>
                                </td>
                                <td>
                                <?php
                                    if(!empty($data["second_letter_id"])) {
                                        if($data["cremation_status"] != 11) {
                                ?>
                                    <input type="button" id="fire-<?php echo $data["non_pay_id"];?>" class="fire-bth btn btn-primary" data-member-id="<?php echo $data["member_id"]?>" value="ให้ออก"/>
                                <?php
                                        } else {
                                ?>
                                    พ้นสภาพ
                                <?php
                                        }
                                    }
                                ?>
                                </td>
                            </tr>
                        <?php
                            }
                        ?>
                            </form>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
		<?php echo @$paging ?>
	</div>
</div>
<!-- Form for create letter -->
<form action="<?php echo base_url(PROJECTPATH.'/cremation/save_debt_letter'); ?>" id="single-letter" method="POST">
    <input type="hidden" id="non_pay_id" name="non_pay_id" value=""/>
</form>
<!-- Form for file member -->
<form action="<?php echo base_url(PROJECTPATH.'/cremation/fire_member'); ?>" id="fire-member" method="POST">
    <input type="hidden" id="fire-member_id" name="member_id" value=""/>
</form>
<script>
$(document).ready(function(){
    $(".non-pay-checkbox").change(function(){
        if(!$(this).is(':checked')) {
            $('#chk-all').prop('checked', false);
        }
    });
    $(".print-letter-new").click(function(){
        non_pay_id = $(this).attr("data-non-pay-id");
        swal({
            title: 'พิมพ์จดหมาย',
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
                $("#non_pay_id").val(non_pay_id);
                $("#single-letter").submit();
            }
        });
    });
    $(".print-letter").click(function(){
        var win = window.open(base_url+"cremation/print_debt_letter?letter_id="+$(this).attr("data-letter-id"), '_blank');
        win.focus();
    });
    $("#btn_port").click(function(){
        var win = window.open(base_url+"report_cremation_data/coop_report_finance_month", '_blank');
        win.focus();
    });
    $("#btn_print").click(function(){
        swal({
            title: 'พิมพ์จดหมาย',
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
                $("#form2").submit();
            }
        });
    });
    $(".fire-bth").click(function(){
        member_id = $(this).attr("data-member-id");
        swal({
            title: 'ให้สมาชิกออกจากระบบฌาปนกิจสงเคราะห์',
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
                $("#fire-member_id").val(member_id);
                $("#fire-member").submit();
            }
        });
    });
});
function check_all_page(){
    if($('#chk-all').is(':checked') === true) {
        $('.non-pay-checkbox').prop('checked', true);
    }else{
        $('.non-pay-checkbox').prop('checked', false);
    }
}
</script>