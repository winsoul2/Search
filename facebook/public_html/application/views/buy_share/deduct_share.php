<?php
function chkBrowser($nameBroser){
    return preg_match("/".$nameBroser."/",$_SERVER['HTTP_USER_AGENT']);
}
?>
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
		
		input[type=checkbox], input[type=radio] {
			margin: 11px 0 0;
		}
	</style>
<h1 style="margin-bottom: 0">ถอนหุ้น</h1>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>
</div>
    <div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
            <div class="panel panel-body" style="padding-top:0px !important;">
                <form id="form1" method="POST" action="<?php echo base_url(PROJECTPATH.'/buy_share/deduct_share_save'); ?>">
                    <input type="hidden" id="member_id" name="member_id" value="<?php echo $member_id; ?>">
                    <?php $this->load->view('search_member_new'); ?>
                    <div class="" style="padding-top:0;">
                        <h3 >ข้อมูลหุ้น</h3>
                        <div class="g24-col-sm-24">
                            <div class="form-group g24-col-sm-8">
                                <label class="g24-col-sm-10 control-label ">จำนวนหุ้นสะสม</label>
                                <div class="g24-col-sm-14">
                                    <input class="form-control" type="text" name="share_payable" value="<?php echo $count_share; ?>"  readonly>
                                </div>
                            </div>
                            <div class="form-group g24-col-sm-8">
                                <label class="g24-col-sm-10 control-label ">คิดเป็นมูลค่า</label>
                                <div class="g24-col-sm-14">
                                    <input class="form-control" name="share_payable_value" type="text" value="<?php echo $cal_share; ?>"  readonly>
                                </div>
                            </div>
                        </div>
                        <div class="g24-col-sm-24">
                            <div class="form-group g24-col-sm-8">
                                <label class="g24-col-sm-10 control-label ">ต้องการถอนหุ้น/บาท</label>
                                <div class="g24-col-sm-14">
                                    <input id="share_early_value" name="share_early_value" class="form-control " type="text" value="" onkeypress="return chkNumber(this)" onChange="convert_to_share()">
                                </div>
                            </div>
                            <div class="form-group g24-col-sm-8">
                                <label class="g24-col-sm-10 control-label ">คิดเป็นมูลจำนวนหุ้น</label>
                                <div class="g24-col-sm-14">
                                    <input id="share_early" class="form-control" name="share_early"  type="text" value=""  readonly>
                                    <input type="hidden" name="share_value" id="share_value" value="<?php echo $share_value; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="g24-col-sm-24">
                            <div class="form-group g24-col-sm-8">
                                <label class="g24-col-sm-10 control-label ">ระบุวันที่</label>
								<div class="input-with-icon g24-col-sm-14">
									<div class="form-group">
									<input id="fix_date_select" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?=@$_GET['fix_date']=="" ? date("d/m/").(date("Y")+543) : $_GET['fix_date']?>" data-date-language="th-th" autocomplete="off" <?=@$_GET['member_id']=="" ? "" : ""?>>
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>										
                        </div>
                        <div class="g24-col-sm-24">
                            <div class="form-group g24-col-sm-8">
                            <label class="g24-col-sm-10 control-label "></label>
                                <div class="g24-col-sm-10">
                                    <button class="btn btn-primary btn-after-input"  type="button" onclick="return check_form()" style="width: 110px;"><span class="icon icon-save" style="margin-top: 1px;"></span><span> บันทึก</span></button>
                                </div>
                            </div>												
                        </div>

                    </div>
                    <span style="display:none;"><a class="link-line-none" data-toggle="modal" data-target="#confirmSave" id="confirmSaveModal" class="fancybox_share fancybox.iframe" href="#"></a></span>

                    <span style="display:none;"><a class="link-line-none" data-toggle="modal" data-target="#alert" id="alertModal" class="fancybox_share fancybox.iframe" href="#"></a></span>
                    <input type="hidden" id="delete" name="delete" value="0">
                    <input type="hidden" id="share_id" name="share_id" value="">
                    <input type="hidden" name="bank_id" id="bank_id">
                    <input type="hidden" name="branch_code" id="branch_code">
                    <input type="hidden" name="fix_date" id="fix_date">
                </form>
                <div class="g24-col-sm-24 m-t-1">
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-bordered table-striped table-center">
                            <thead>
                            <tr class="bg-primary">
                                <th>วันที่ทำรายการ</th>
                                <th>รายการ</th>
                                <th >จำนวนหุ้น</th>
                                <th>ยอดเงิน</th>
                                <th>สถานะ</th>
                                <th width="20%">ผู้ทำรายการ</th>
                                <th>จัดการ</th>
                            </tr>
                            </thead>

                            <tbody id="result">
                            </tbody>

                            <tbody id="table_first">

                            <?php
                            $share_collect = 0;
                            $share_status = array('0'=>'รอชำระเงิน', '1'=>'ชำระเงินแล้ว', '2'=>'รออนุมัติยกเลิกใบเสร็จ', '3'=>'ยกเลิกใบเสร็จ');
                            foreach($data as $key => $row){
                                $share_collect += $row['share_early'];
                                $share_date = explode('.',$row['share_date']);
                                $share_date = explode(' ',$share_date[0]);
                                $date = explode('-',$share_date[0]);
                                $time = explode(':',$share_date[1]);
                                ?>
                                <tr>
                                    <td><?php echo $date[2]."/".$date[1]."/".($date[0]+543)." ".$time[0].":".$time[1]." น."; ?></td>
                                    <td align="left">ถอนหุ้น</td>
                                    <td align="right"><?php echo number_format($row['share_early']); ?></td>
                                    <td align="right"><?php echo number_format($row['share_early_value']); ?></td>
                                    <!--td align="right"><?php echo $share_collect; ?></td-->
                                    <td align="center"><span id="share_status_<?php echo $row['share_id']; ?>"><?php echo $share_status[$row['share_status']]; ?></span></td>
                                    <td align="center"><?php echo $row['user_name']; ?></td>
                                    <td>
                                    <?php if(!empty($row['voucher_id'])) { ?>
                                        <a title="ออกใบเสร็จ" alt="ออกใบเสร็จ" style="cursor:pointer;font-size: 17px;" onclick="print_voucher('<?php echo base64_encode("id=".$row['voucher_id']); ?>');"><span class="icon icon-print" aria-hidden="true"></span></a>
                                    <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <?php echo $paging ?>
        </div>
    </div>

    </div>
</div>
<?php $this->load->view('search_member_new_modal'); ?>
<div class="modal fade" id="confirmSave"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">ยืนยันข้อมูล</h2>
            </div>
            <div class="modal-body center">
                <p><span class="icon icon-arrow-circle-o-down" style="font-size:75px;"></span></p>
                <p style="font-size:18px;">ถอนหุ้นจำนวน <span id="num_share"></span> หุ้น เป็นเงิน <span id="price_share"></span>  บาท</p>
            </div>
            <div class="modal-footer center">
                <button class="btn btn-info" onclick="submit_form()">ยืนยันการถอนหุ้น</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>
<?php
$v = date('YmdHis');
echo script_tag($link);
?>
<script>
    function cal_share_result(){
        var share_early = $('#share_early').val();
        var share_value = $('#share_value').val();
        if(share_early!=''){
            $('#share_early_value').val(parseFloat(share_early)*parseFloat(share_value));
            $('#num_share').html(share_early);
            $('#price_share').html($('#share_early_value').val());
        }else{
            $('#num_share').html('');
            $('#price_share').html('');
        }
    }

    function convert_to_share(){
        var share_early_value = removeCommas($('#share_early_value').val());
        var share_value = $('#share_value').val();
        var share_early = $('#share_early');
        if(share_value.length > 0){
            share_early.val(numeral(parseFloat(share_early_value)/parseFloat(share_value)).format('0,0'));
            $('#num_share').html(share_early.val());
            $('#price_share').html($('#share_early_value').val());
            $("#share_early_value").val(numeral(share_early_value).format('0,0.00'));
        }else{
            $('#num_share').html('');
            $('#price_share').html('');
        }
    }

    function check_form(){
        var alert_text = '';
        if($('#member_id').val()==''){
            alert_text += '- ข้อมูลสมาชิก\n';
        }
        if($('#share_early').val()==''){
            alert_text += '- จำนวนหุ้นที่ต้องการซื้อเพิ่ม\n';
        }
        if(alert_text == ''){
            $("#confirmSaveModal").trigger("click");
        }else{
			swal('กรุณากรอกข้อมูลต่อไปนี้',alert_text,'warning');
        }
        return false;
    }

    function submit_form(){
        $('#form1').submit();
    }

    function get_share_id(share_id){
        $('#share_id').val(share_id);
    }

    function del_share(){
        $('#delete').val('1');
        $('#form1').submit();
    }

    function chkNumber(ele){
        var vchar = String.fromCharCode(event.keyCode);
        if ((vchar<'0' || vchar>'9') && (vchar != '.')) return false;
        ele.onKeyPress=vchar;
    }

    function print_voucher(parameter) {
        window.open(base_url + 'voucher?'+parameter,'_blank')
    }

    function set_bank(val) {
		$("#bank_id").val(val);
	}

	function set_branch_code(val) {
		$("#branch_code").val(val);
	}

	function show(val) {
		if (val == 'xd_sec') {
			$("#xd_sec").show();
            $("#che_sec").hide();
            $('#other_sec').hide();
		} else if (val == 'che_sec') {
			$("#xd_sec").hide();
            $("#che_sec").show();
            $('#other_sec').hide();
		} else if(val == 'other_sec') {
            $("#xd_sec").hide();
            $("#che_sec").hide();
            $('#other_sec').show();
        }else {
			$("#xd_sec").hide();
            $("#che_sec").hide();
            $('#other_sec').hide();
		}
    }
    function removeCommas(str) {
        return(str.replace(/,/g,''));
    }
</script>
