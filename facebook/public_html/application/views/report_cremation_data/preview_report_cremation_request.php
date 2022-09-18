<?php
if(@$_GET['download']!=""){
    header("Content-type: application/vnd.ms-excel;charset=utf-8;");
    header("Content-Disposition: attachment; filename=รายงานผู้สมัคร.xls");
    date_default_timezone_set('Asia/Bangkok');
}

?>
<style>
    @page {
        size: 210mm 297mm;
        /* Chrome sets own margins, we change these printer settings */
        margin: 15mm 0mm 15mm 0mm;
    }

    span.title_view {
        font-size : 24px !important;
    }

    th, td {
        font-size : 16px !important;
    }
    body {
		color: #000;
	}

</style>

<div style="width: 90%;" class="text-center" >
    <div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
        <table style="width: 100%;">
            <?php

            // if(@$page == 1){
            ?>
            <tr>
                <?php
                if(@$_GET['download']==""){
                    ?>
                    <td width=150>

                    </td>
                    <td align="center" colspan="4">
                        <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />

                        <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                        <h3 class="title_view">รายงานผู้สมัคร</h3>

                        <h3 class="title_view">

                            <?php
                            if($start_date == $end_date){
                                ?>
                                ประจำวันที่ <?=@$start_date?>
                                <?php
                            }else{
                                ?>
                                ประจำวันที่ <?=@$start_date?> ถึง <?=@$end_date?>
                                <?php
                            }
                            ?>
                        </h3>
                    </td>
                    <td  width=150>
                        <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                        <?php
                        $get_param = '?';
                        foreach(@$_GET as $key => $value){
                            //if($key != 'month' && $key != 'year' && $value != ''){
                            $get_param .= $key.'='.$value.'&';
                            //}
                        }
                        $get_param = substr($get_param,0,-1);
                        ?>
                        <a class="no_print" target="_blank" onclick="goto()">
                            <!-- <a class="no_print"  target="_blank" href="<?php echo base_url('/report_deposit_data/coop_report_account_transaction_excel'.$get_param); ?>"> -->
                            <button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
                        </a>
                    </td>
                    <?php
                }else{
                    ?>
                    <td align="center" colspan="6">
                        <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                        <h3 class="title_view">รายงานผู้สมัคร</h3>

                        <h3 class="title_view">
                            <?php
                            if($start_date == $end_date){
                                ?>
                                ประจำวันที่ <?=@$start_date?>
                                <?php
                            }else{
                                ?>
                                ประจำวันที่ <?=@$start_date?> ถึง <?=@$end_date?>
                                <?php
                            }
                            ?>
                        </h3>
                    </td>
                    <?php
                }
                ?>
            </tr>

            <tr>
                <td colspan='3' align=left>
                    <span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>
                    <span class="title_view">เวลา <?php echo date('H:i:s');?></span><br>

                </td>
                <td colspan='3' align=right>
                    <span class="title_view">ผู้ทำรายการ <?=$st_by_name?></span>
                </td>
            </tr>



        </table>

        <br>

        <table style="width: 100%;" border=1 class="st">
            <thead>
            <tr class="st">
                <th  class="text-center" style="padding: 7px;" width='70' align='center' >ลำดับ</th>
                <th  class="text-center" style="padding: 7px;" width="80" align='center'>วันที่สมัคร</th>
                <th  class="text-center" style="padding: 7px;" width="80" align='center'>วันที่อนุมัติ</th>
                <th  class="text-center" style="padding: 7px;" width="100" align='center'>เลขฌาปนกิจ</th>
                <th  class="text-center" style="padding: 7px;" width="100" align='center'>รหัสสมาชิก</th>
                <th  class="text-center" style="padding: 7px;" width="100" align='center'>ประเภทสมาชิกฌาปนกิจ</th>
                <th  class="text-center" style="padding: 7px;" width="100" align='center'>ชื่อ-นามสกุล</th>
                <th  class="text-center" style="padding: 7px;" width="100" align='center'>เลขบัตรประชาชน</th>
                <th  class="text-center" style="padding: 7px;" width="70" align='center'>สถานะ</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(isset($data)){
                $c=1;
                foreach ($data as $key => $value) {
                    ?>
                    <tr class="st">
                        <td align="center" style="padding: 7px;"><?php echo  ($c++)?></td>
                        <td align="center" style="padding: 7px;"><?php echo @($value['createdatetime']!="") ? $this->center_function->ConvertToThaiDate(@$value['createdatetime'], 1 ,0) : "-" ;?></td>
                        <td align="center" style="padding: 7px;"><?php echo @($value['approve_date']!="") ? $this->center_function->ConvertToThaiDate(@$value['approve_date'], 1 ,0) : "-" ;?></td>
                        <td align="center" style="padding: 7px;"><?php echo $value['member_cremation_id']; ?></td>
                        <td align="center" style="padding: 7px;"><?php echo $value['member_id']; ?></td>
                        <td align="center" style="padding: 7px;"><?php echo $mem_type_id[$value['mem_type_id']]; ?></td>
                        <td align="left" style="padding: 7px;"><?php echo $value['fullname']; ?></td>
                        <td align="center" style="padding: 7px;"><?php echo $value['id_card']; ?></td>
                        <td align="center" style="padding: 7px;">
                            <?php
                                echo $value['cremation_status'] == 0 || $value['cremation_status'] == 5 || $value['cremation_status'] == 8
                                        || $value['cremation_status'] == 9 || $value['cremation_status'] == 11? $status[$value['cremation_status']] : 'ปกติ';
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<?php
// 	}
// }
?>

<style>
    table.st {
        border-collapse: collapse;
    }
    tr:nth-child(even).st {background-color: #f2f2f2;}
    tr:hover.st {background-color: #f5f5f5;}
</style>

<script>
    function goto(){
        // console.log(window.location.href );
        window.open(window.location.href+'&download=1');
    }
</script>