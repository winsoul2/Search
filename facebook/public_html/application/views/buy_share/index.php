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
<h1 style="margin-bottom: 0">ซื้อหุ้นเพิ่มพิเศษ</h1>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
            <a class="link-line-none" class="fancybox_share fancybox.iframe" href="<?php echo base_url(PROJECTPATH.'/buy_share'); ?>" style="float:right;">
                <button class="btn btn-primary btn-lg bt-add" type="button"><span class="icon icon-plus-circle"></span> เพิ่มรายการใหม่</button>
            </a>
    </div>

</div>
    <div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
            <div class="panel panel-body" style="padding-top:0px !important;">
                <form id="form1" method="POST" action="<?php echo base_url(PROJECTPATH.'/buy_share/save_share'); ?>">
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
                                <label class="g24-col-sm-10 control-label ">ต้องการซื้อเพิ่ม/บาท</label>
                                <div class="g24-col-sm-14">
                                    <input id="share_early_value" name="share_early_value" class="form-control " type="text" value="" onkeypress="return chkNumber(this)" onkeyup="convert_to_share()">
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
                            <div class="form-group g24-col-sm-16">
								<label class="g24-col-sm-5 control-label right"> การชำระเงิน</label>
								<div class="g24-col-sm-14">
									<span id="show_pay_type2" style="">
										<input type="radio" name="pay_type" id="pay_type_0" value="0" onclick="set_bank('');set_branch_code('');show('');"> เงินสด &nbsp;&nbsp;
										<input type="radio" name="pay_type" id="pay_type_1" value="1" onclick="set_bank('');set_branch_code('');show('xd_sec');"> โอนเงิน &nbsp;&nbsp;
                                        <input type="radio" name="pay_type" id="pay_type_2" value="2" onclick="set_bank('');set_branch_code('');show('che_sec');"> เช็คเงินสด &nbsp;&nbsp;
                                        <input type="radio" name="pay_type" id="pay_type_3" value="3" onclick="set_bank('');set_branch_code('');show('other_sec');"> อื่นๆ &nbsp;&nbsp;
									</span>
								</div>
							</div>											
                        </div>

                        <div class="g24-col-sm-24" id="xd_sec" style="display: none;">
                            <div class="form-group g24-col-sm-16">
								<label class="g24-col-sm-5 control-label right"></label>
								<div class="g24-col-sm-10">
                                    <div id="transfer_deposit">
                                        <div class="transfer_content">
                                            <div class="row transfer">
                                                <div class="g24-col-sm-24">
                                                    <div class="form-group">
                                                        <label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
                                                        <input type="radio" name="xd_bank_id" id="xd_1" onclick="set_bank('006');set_branch_code('0071');"><label for="xd_1"> ธ.กรุงไทย จำกัด สาขาการปิโตรเลียม</label>
                                                    </div>
                                                </div>
                                                <div class="g24-col-sm-24">
                                                    <div class="form-group">
                                                        <label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
                                                        <input type="radio" name="xd_bank_id" id="xd_2" onclick="set_bank('002');set_branch_code('1082');"><label for="xd_2"> ธ.กรุงเทพ จำกัด สาขาเอนเนอร์ยี่ คอมเพล็กซ์</label>
                                                    </div>
                                                </div>
                                                <div class="g24-col-sm-24">
                                                    <div class="form-group">
                                                        <label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
                                                        <input type="radio" name="xd_bank_id" id="xd_3" onclick="set_bank('011');set_branch_code('0211');"><label for="xd_3"> ธ.ทหารไทย จำกัด สาขาการปิโตรเลียม </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row transfer">
                                                <div class="g24-col-sm-24">
                                                    <div class="form-group">
                                                        <label class="control-label g24-col-sm-1" for="local_account_id"></label>
                                                        <input type="radio" name="xd_bank_id" id="xd_4" onclick="set_bank('');set_branch_code('');"><label for="xd_4"> บัญชีเงินฝาก </label>
                                                        <select class="form-control" name="local_account_id" id="local_account_id" style="display: initial !important;width: 230px !important;">
                                                            <option value="">เลือกบัญชี</option>
                                                            <?php
                                                                foreach ($maco_account as $key => $value) {
                                                                    echo '<option value="'.$value['account_id'].'">'.$value['account_id'].' '.$value['account_name'].'</option>';
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row transfer">
                                                <div class="g24-col-sm-24">
                                                    <div class="form-group">
                                                        <label class="control-label g24-col-sm-1" for="local_account_id"></label>
                                                        <input type="radio" name="xd_bank_id" id="xd_5" onclick="set_bank('');set_branch_code('');"><label for="xd_5"> อื่นๆ </label>
                                                        <input type="text" name="transfer_other" id="transfer_other" class="form-control" style="display: initial !important;width: 200px !important;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
								</div>
							</div>											
                        </div>
                        <div class="g24-col-sm-24" id="che_sec" style="display: none;">
                            <div class="form-group g24-col-sm-16">
								<label class="g24-col-sm-5 control-label right"></label>
								<div class="g24-col-sm-10">
                                    <div id="cheque_deposit">
                                        <div class="cheque_content">
                                            <div class="row cheque">
                                                <div class="g24-col-sm-24">
                                                    <div class="form-group">
                                                        <label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
                                                        <input type="radio" name="che_bank_id" id="che_1" onclick="set_bank('006');set_branch_code('0071');"><label for="che_1"> ธ.กรุงไทย จำกัด สาขาการปิโตรเลียม</label>
                                                    </div>
                                                </div>
                                                <div class="g24-col-sm-24">
                                                    <div class="form-group">
                                                        <label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
                                                        <input type="radio" name="che_bank_id" id="che_2" onclick="set_bank('002');set_branch_code('1082');"><label for="che_2"> ธ.กรุงเทพ จำกัด สาขาเอนเนอร์ยี่ คอมเพล็กซ์</label>
                                                    </div>
                                                </div>
                                                <div class="g24-col-sm-24">
                                                    <div class="form-group">
                                                        <label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
                                                        <input type="radio" name="che_bank_id" id="che_3" onclick="set_bank('011');set_branch_code('0211');"><label for="che_3"> ธ.ทหารไทย จำกัด สาขาการปิโตรเลียม </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row cheque">
                                                <div class="g24-col-sm-24">
                                                    <div class="form-group">
                                                        <label class="control-label g24-col-sm-8" for="cheque_no">หมายเลขเช็ค :</label>
                                                        <input class="form-control g24-col-sm-14" name="cheque_no" id="cheque_no" placeholder="ระบุบัญชีเงินฝาก" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
								</div>
							</div>											
                        </div>
                        <div class="g24-col-sm-24" id="other_sec" style="display: none;">
                            <div class="form-group g24-col-sm-16">
								<label class="g24-col-sm-5 control-label right"></label>
								<div class="g24-col-sm-10">
                                    <div id="other_deposit">
                                        <div class="other_content">
                                            <div class="row cheque">
                                                <div class="g24-col-sm-24">
                                                    <div class="form-group">
                                                        <label class="control-label g24-col-sm-8" for="other">อื่นๆ :</label>
                                                        <input class="form-control g24-col-sm-14" name="other" id="other" placeholder="ระบุ" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                <!--th>หุ้นสะสม</th-->
                                <th>สถานะ</th>
                                <th>เลขที่ใบเสร็จ</th>
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
                                    <td align="left">ซื้อหุ้นเพิ่ม</td>
                                    <td align="right"><?php echo number_format($row['share_early']); ?></td>
                                    <td align="right"><?php echo number_format($row['share_early_value']); ?></td>
                                    <!--td align="right"><?php echo $share_collect; ?></td-->
                                    <td align="center"><span id="share_status_<?php echo $row['share_id']; ?>"><?php echo $share_status[$row['share_status']]; ?></span></td>
                                    <td align="center"><span id="share_bill_<?php echo $row['share_id']; ?>"><?php echo $row['share_bill']; ?></span></td>
                                    <td align="center"><?php echo $row['user_name']; ?></td>
                                    <td>
                                        <?php if($row['share_status']!='3'){ ?>
                                            <a style="font-size: 16px;" href="<?php echo base_url().PROJECTPATH; ?>/admin/receipt_form_pdf/<?php echo $row['share_bill']; ?>" alt="ตัวอย่างใบเสร็จ" title="ตัวอย่างใบเสร็จ" target="_blank"><span class="icon icon-file-o" aria-hidden="true"></span></a>
                                            &nbsp;
                                            <a title="ออกใบเสร็จ" alt="ออกใบเสร็จ" style="cursor:pointer;font-size: 17px;" onclick="receipt_process('<?php echo $row['share_id']; ?>');"><span class="icon icon-print" aria-hidden="true"></span></a>
                                            &nbsp;
                                        <?php } ?>
                                        <?php if($row['share_status']=='0'){
                                            $display_1 = "";
                                            $display_2 = "display:none;";
                                        }else if($row['share_status']=='1'){
                                            $display_1 = "display:none;";
                                            $display_2 = "";
                                        }else{
                                            $display_1 = "display:none;";
                                            $display_2 = "display:none;";
                                        } ?>
                                        <a class="link-line-none" id="delete_<?php echo $row['share_id']; ?>" style="font-size: 18px;<?php echo $display_1; ?>" data-toggle="modal" data-target="#confirmDelete" id="confirmDeleteModal" class="fancybox_share fancybox.iframe" href="#" onclick="get_share_id('<?php echo $row['share_id']; ?>')" alt="ลบรายการ" title="ลบรายการ">
                                            <span style="cursor: pointer;" class="icon icon-trash-o"></span></a>

                                        <a class="link-line-none" id="cancel_<?php echo $row['share_id']; ?>" style="font-size: 19px;<?php echo $display_2; ?>" data-toggle="modal" data-target="#confirmCancel" id="confirmCancelModal" class="fancybox_share fancybox.iframe" href="#" alt="ยกเลิกใบเสร็จ" title="ยกเลิกใบเสร็จ" onclick="get_share_id('<?php echo $row['share_id']; ?>')">
                                            <span style="cursor: pointer;" class="icon icon-times-circle-o"></span></a>
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
                <p style="font-size:18px;">ซื้อหุ้นเพิ่มจำนวน <span id="num_share"></span> หุ้น เป็นเงิน <span id="price_share"></span>  บาท</p>
            </div>
            <div class="modal-footer center">
                <button class="btn btn-info" onclick="submit_form()">ยืนยันการซื้อหุ้น</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmDelete"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-alert">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">ยืนยันการลบรายการ</h2>
            </div>
            <div class="modal-body center">

                <p style="font-size:18px;">ท่านต้องการลบรายการใช่หรือไม่?</p>
            </div>
            <div class="modal-footer center">
                <button class="btn btn-danger" onclick="del_share()">ยืนยัน</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmCancel"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-alert">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">ยืนยันการยกเลิกใบเสร็จ</h2>
            </div>
            <div class="modal-body center">

                <p style="font-size:18px;">ท่านต้องการยกเลิกใบเสร็จใช่หรือไม่?</p>
            </div>
            <div class="modal-footer center">
                <button class="btn btn-danger" onclick="cancel_share()" data-dismiss="modal">ยืนยัน</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="alert"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-alert">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">กรุณากรอกข้อมูลต่อไปนี้</h2>
            </div>
            <div class="modal-body center">
                <p style="font-size:18px;"><span id="alert_text"></span></p>
            </div>
            <div class="modal-footer center">
                <button type="button" class="btn btn-danger" data-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>
<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/Buy_share.js?v='.$v,
    'type' => 'text/javascript'
);
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
        var share_early_value = $('#share_early_value').val();
        var share_value = $('#share_value').val();
        var share_early = $('#share_early');
        if(share_value.length > 0){

            share_early.val(parseFloat(share_early_value)/parseFloat(share_value));
            $('#num_share').html(share_early.val());
            $('#price_share').html($('#share_early_value').val());

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
        if($('input[name=pay_type]').is(":checked") == false){
            alert_text += '- การชำระเงิน\n';
        }
        if(alert_text == ''){
            $("#confirmSaveModal").trigger("click");
        }else{
            //$('#alert_text').html(alert_text);
            //$("#alertModal").trigger("click");
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

    function receipt_process(share_id){
        $.post(base_url+"buy_share/receipt_process",
            {
                receipt_create: "1",
                share_id: share_id
            }
            , function(result){
                $('#share_status_'+share_id).html('ชำระเงินแล้ว');
                $('#share_bill_'+share_id).html(result);
                $('#delete_'+share_id).hide();
                $('#cancel_'+share_id).show();
                //window.open(base_url+'admin/receipt_pdf?receipt_id='+result, '_blank');
                window.open(base_url+'buy_share/receipt_buy_share?receipt_id='+result, '_blank');
            });
    }
    function cancel_share(){
        var share_id = $('#share_id').val();
        $.post(base_url+"buy_share/save_share",
            {
                cancel_receipt: "1",
                share_id: share_id
            }
            , function(result){
                $('#cancel_'+share_id).hide();
                $('#share_status_'+share_id).html('รออนุมัติยกเลิกใบเสร็จ');
            });
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

</script>
