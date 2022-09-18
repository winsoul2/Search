<div class="layout-content">
    <div class="layout-content-body">
        <style>
            .modal-dialog-account {
                margin:auto;
                margin-top:7%;
            }
        </style>
        <style type="text/css">
          .form-group{
            margin-bottom: 5px;
          }
        </style>
        <h1 style="margin-bottom: 0">แก้ไขข้อมูล<?php echo @$style_name;?></h1>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <br>
                    <form action="<?=base_url('setting_report_payment_interest/save_coop_report_payment_interest_style_setting?style_id='.$_GET['style_id'])?>" method="POST">
                    <div class="row">
                        <div class="col-md-offset-1 col-md-10">
                        <a href="#" data-toggle="modal" data-target="#how_to"><i class="fa fa-info-circle" aria-hidden="true"></i> วิธีใช้งาน</a>
                            <table class="">
                                <thead>
                                    <tr>
                                        <td class="col">#</td>
                                        <td class="col">ข้อมูลตัวอักษร</td>
                                        <td class="col">ค่า X (mm)</td>
                                        <td class="col">ค่า Y (mm)</td>
                                        <td class="col">ความยาวของช่อง (mm)</td>
                                        <td class="col">ขนาดตัวอักษร</td>
                                        <td class="col">การจัดเรียง</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($items as $key => $value) {
                                            ?>
                                                <tr>
                                                    <td width="3%" class="col"><?=$key+1?></td>
                                                    <td width="40%" class="col"><input type="text" class="form-control" name="style_value[]" value="<?=$value['style_value']?>"></td>
                                                    <td width="10%" class="col"><input type="text" class="form-control" name="x[]" value="<?=$value['x']?>"></td>
                                                    <td width="10%" class="col"><input type="text" class="form-control" name="y[]" value="<?=$value['y']?>"></td>
                                                    <td width="10%" class="col"><input type="text" class="form-control" name="width[]" value="<?=$value['width']?>"></td>
                                                    <td width="10%" class="col"><input type="text" class="form-control" name="font_size[]" value="<?=$value['font_size']?>"></td>
                                                    <td width="22%" class="col">
                                                        <select name="align[]" class="form-control" id="" style="width: 100px;">
                                                            <option value="L" <?=($value['align']=="L" ? "selected" : "")?>>ชิดซ้าย</option>
                                                            <option value="C" <?=($value['align']=="C" ? "selected" : "")?>>กึ่งกลาง</option>
                                                            <option value="R" <?=($value['align']=="R" ? "selected" : "")?>>ชิดขวา</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            <?php
                                        }

                                        if($key==0){
                                            $key = 1;
                                        }else{
                                            $key += 2;
                                        }
                                        for ($i=$key; $i <= 20; $i++) { 
                                            ?>
                                                <tr>
                                                    <td width="5%" class="col"><?=$i?></td>
                                                    <td width="40%" class="col"><input type="text" class="form-control" name="style_value[]" value=""></td>
                                                    <td width="10%" class="col"><input type="text" class="form-control" name="x[]" value=""></td>
                                                    <td width="10%" class="col"><input type="text" class="form-control" name="y[]" value=""></td>
                                                    <td width="10%" class="col"><input type="text" class="form-control" name="width[]" value=""></td>
                                                    <td width="10%" class="col"><input type="text" class="form-control" name="font_size[]" value=""></td>
                                                    <td width="22%" class="col">
                                                        <select name="align[]" class="form-control" id="" style="width: 100px;">
                                                            <option value="L">ชิดซ้าย</option>
                                                            <option value="L">กึ่งกลาง</option>
                                                            <option value="L">ชิดขวา</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            <?php
                                        }
                                    ?>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br><br>
                    <div class="row">
                        <div class="col-md-offset-2 col-md-6 text-center">
                            <button class="btn btn-primary" type="submit"><span class="icon icon-add"></span> บันทึก</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="how_to" role="dialog">
    <input type="hidden" name="line_start" id="line_start" value=""/>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">แก้ไขรูปแบบ</h4>
            </div>

            <div class="modal-body">
            <p>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;กำหนดค่าที่ช่องข้อมูลตัวอักษรที่ต้องการตาม ตารางคีย์เวิร์ด กำหนดค่า X และ Y โดย X วัดจากแนวนอนของหน้าปกสมุดบัญชี Y วัดจากแนวตั้งของหน้าปกสมุดบัญชี หน่วยเป็นมิลลิเมตร และกำหนดขนาดของตัวอักษร
            </p>
            <p>
            * หากต้องการลบ ให้เว้นว่างช่องข้อมูลตัวอักษร แล้วกดบันทึก
            </p>
            <div class="row">
                    <div class="col-md-offset-1 col-md-10 text-center">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td colspan=2>ตารางคีย์เวิร์ด</td>
                                </tr>
                                <tr>
                                    <td>
                                        คีย์เวิร์ด
                                    </td>
                                    <td>
                                        ผลลัพท์ที่จะได้
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>[account_id]</td>
                                    <td>หมายเลขบัญชี</td>
                                </tr>
                                <tr>
                                    <td>[account_name]</td>
                                    <td>ชื่อบัญชี</td>
                                </tr>
                             
                                <tr>
                                    <td>[transaction_time]</td>
                                    <td>วันที่ทำรายการ</td>
                                </tr>
                                <tr>
                                    <td>[transaction_deposit_interest]</td>
                                    <td>จำนวนเงิน</td>
                                </tr>
                                <tr>
                                    <td>[transaction_deposit_interest_th]</td>
                                    <td>ยอดเงินสดนำฝาก</td>
                                </tr>
                                <tr>
                                    <td>[type_name]</td>
                                    <td>ประเภทเงินฝาก</td>
                                </tr>
                                <tr>
                                    <td>[pay_type]</td>
                                    <td>ตัวเลือกรายการรับเงิน (ตัวกากบาท)</td>
                                </tr>
                                <tr>
                                    <td>[pay_type_th]</td>
                                    <td>รายการรับเงิน</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .col{
        padding: 10px;
    }
</style>