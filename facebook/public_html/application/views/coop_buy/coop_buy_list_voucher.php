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
	.modal-dialog-account {
		margin:auto;
		margin-top:7%;
	}
</style>
<link rel="stylesheet" href="/html/css/custom-grid24.css">
<style type="text/css">
  .form-group{
    margin-bottom: 5px;
  }
</style>
<h1 class="title_top">รายการซื้อ</h1>
<?php $this->load->view('breadcrumb'); ?>
<div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
			<div class="" style="padding-top:0px !important;">
                <div class="row gutter-xs">
                    <div class="col-xs-12 col-md-12">
                        <div class="panel panel-body" style="padding-top:0px !important;">
                            <h3 >บัญชีรายวัน</h3>
                            <form method="GET" action="">
                                <div class="g24-col-sm-24">
                                    <label class="g24-col-sm-3 control-label datepicker1" for="approve_date">เลือกวันที่บันทึกบัญชี</label>
                                    <div class="input-with-icon g24-col-sm-3">
                                        <div class="form-group">
                                            <input id="approve_date" name="approve_date" class="form-control m-b-1 form_date_picker" type="text" value="<?php echo (@$_GET['approve_date'] != '')?@$_GET['approve_date']:''; ?>" data-date-language="th-th" autocomplete="off">
                                            <span class="icon icon-calendar input-icon m-f-1"></span>
                                        </div>
                                    </div>
                                    <div class="g24-col-sm-1">
                                        <input type="submit" class="btn btn-primary" value="ค้นหา">
                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
                <div class="row gutter-xs">
                    <div class="col-xs-12 col-md-12">
                        <div class="panel panel-body">
                            <div class="bs-example" data-example-id="striped-table">
                              <h3 >รายการซื้อ</h3>
                                 <table class="table table-bordered table-striped table-center">
                                 <thead>
                                    <tr class="bg-primary">
                                        <th>วันที่ทำรายการ</th>
                                        <th>วิธีชำระเงิน</th>
                                        <th>จ่ายให้</th>
                                        <th>จำนวนเงินรวม</th>
                                        <th>ประเภทการจ่าย</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                 </thead>
                                    <tbody id="table_first">
                                      <?php
                                        $pay_type = array('cash'=>'เงินสด', 'transfer'=>'เช็คธนาคาร');
                                        $pay_status = array('0'=>'ปกติ', '1'=>'รออนุมัติยกเลิก', '2'=>'ยกเลิกรายการ');
                                        foreach($data as $key => $row){ ?>
                                          <tr>
                                              <td><?php echo $this->center_function->ConvertToThaiDate($row['buy_date']); ?></td>
                                              <td><?php echo $pay_type[$row['pay_type']]; ?></td>
                                              <td><?php echo $row['pay_for']; ?></td>
                                              <td><?php echo number_format($row['total_amount'],2); ?></td>
                                              <td><?php echo $pay_status[$row['account_buy_status']]; ?></td>
                                              <td><?php echo $row['cashpay_type']=='payment'?'รายจ่าย':'รายรับ'; ?></td>

                                              <td style="font-size: 18px;">
                                                  <button name="bt_add" id="bt_add" type="button" class="btn btn-primary" onclick="account_excel_tranction_voucher('<?php echo $row['account_buy_id'] ?>')" >
                                                      <span>Excel Voucher</span>
                                                  </button>
                                              </td>
                                          </tr>
                                      <?php } ?>
                                      </tbody>
                                      </table>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
          </div>
</div>
<?php echo $paging ?>
	</div>
</div>
<div class="modal-body">
    <form action="<?php echo base_url(PROJECTPATH.'/coop_buy/account_excel_tranction_voucher_coop_buy'); ?>" method="get" id="from_excel_day">
        <input id="account_buy_id" name="account_buy_id" type="hidden" value="">
    </form>
</div>
<script>
    function account_excel_tranction_voucher(account_buy_id){
        $('#account_buy_id').val(account_buy_id);
        $('#from_excel_day').submit();

    }
var base_url = $('#base_url').attr('class');

    $(document).ready(function() {
        $(".form_date_picker").datepicker({
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
    });
    $( document ).ready(function() {
        $("#account_datetime").datepicker({
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
        $('#add_account_chart').on('hide.bs.modal', function () {
            //$('.type_input').val('');
        });
    });
</script>