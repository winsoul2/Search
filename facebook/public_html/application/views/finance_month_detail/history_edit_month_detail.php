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
<div class="layout-content">
    <div class="layout-content-body">
        <h1 style="margin-bottom: 0">ประวัติการแก้ไขข้อมูลเรียกเก็บ</h1>
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
                                    <label class="g24-col-sm-1 control-label"> วันที่ </label>
                                    <div class="g24-col-sm-3">
                                        <input id='date_start' name="date_start" class="form-control m-b-1" type="date" value="<?=$date_start?>">
                                    </div>
                                    <label class="g24-col-sm-1 control-label"> ถึงวันที่ </label>
                                    <div class="g24-col-sm-3">
                                        <input id="date_end" name="date_end" class="form-control m-b-1" type="date" value="<?=$date_end?>">
                                    </div>
                                    <div class="g24-col-sm-2">
                                        <input type="submit" class="btn btn-primary" value="ค้นหา"> 
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form>
                            <div class="g24-col-sm-24 m-t-1 scroll_x">
                                <?php 
                                $edit_finance_month_detail_arr = $finance_month_detail_arr;
                                ?>
                                    <table class="table table-bordered table-striped">
                                        <thead> 
                                            <tr class="bg-primary">
                                                <th style="width: 5%;">ลำดับ</th>
                                                <th style="width: 6.25%;">เดือน/ปี เรียกเก็บ</th>
                                                <th style="width: 6.25%;">ชื่อนามสกุล</th>
                                                <th style="width: 6.25%;">ทะเบียนสมาชิก</th>
                                                <th style="width: 6.25%;">สัญญา</th>
                                                <th style="width: 6.25%;">ประเภท</th>
                                                <th style="width: 6.25%;">จำนวนเงิน</th>
                                                <th style="width: 6.25%;">จำนวนจ่ายจริง</th>
                                                <th style="width: 6.25%;">การกระทำ</th>
                                                <th style="width: 6.25%;">วันที่ทำรายการ</th>
                                                <th style="width: 6.25%;">สมาชิกที่ทำรายการ</th>
                                            </tr> 

                                        </thead>
                                        <?php 
                                        $total = 0;
                                        foreach($log_month_detail_arr as $key => $value){ 	
                                        $total++;
                                        $profile_month = $value['profile_month'];
									    ?>
                                        <tbody>
                                            <tr>
                                                <td class="center"><?=$total?></td>
                                                <td class="center"><?=$month_short_arr[$profile_month].' '.$value['profile_year']?> </td>
                                                <td><?=$value['fullname_th']?></td>
                                                <td class="center"><?=$value['member_id']?></td>
                                                <td class="center"><?php
                                                if ($value['loan_id'] != ''){
                                                    echo $value['loan_name'].' '.$value['contract_number'];
                                                }else if ($value['deposit_account_id'] != ''){
                                                    echo 'เงินฝาก '.$value['deposit_account_id'];
                                                }
                                                ?></td>
                                                <td class="center"><?=$value['deduct_detail']?></td>
                                                <td class="center"><?=$value['pay_amount']?></td>
                                                <td class="center"><?=$value['real_pay_amount']?></td>
                                                <td class="center">
                                                    <?php if($value['status'] == 1){ 
                                                        echo 'แก้ไขข้อมูล';
                                                    }else if($value['status'] == 2){
                                                        echo 'ลบข้อมูล';
                                                    }else if($value['status'] == 3){
                                                        echo 'เพิ่มข้อมูล';
                                                    } ?>
                                                </td>
                                                <td class="center"><?=$value['create_datetime']?></td>
                                                <td class="center"><?=$value['user_name']?></td>
                                            </tr>
                                        </tbody>
                                        <?php } ?>
                                        <?php if($total == 0){ ?>
                                            <tr>
                                                <td colspan="17" class="center" > ไม่มีข้อมูล </td>
                                            </tr>
                                        <?php } ?>
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
</div>