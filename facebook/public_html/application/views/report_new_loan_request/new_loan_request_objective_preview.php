<?php
if(@$_GET['download']!=""){
    header("Content-type: application/vnd.ms-excel;charset=utf-8;");
    header("Content-Disposition: attachment; filename=export.xls");
    date_default_timezone_set('Asia/Bangkok');
}
?>
<style>
    .table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
        font-size: 16px;
    }
    .table {
        color: #000;
    }
    .num {
        mso-number-format:General;
    }
    .text{
        mso-number-format:"\@";/*force text*/
    }
    .text-center{
        text-align: center;
    }
    .text-left{
        text-align: left;
    }
    .table_title{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 22px;
        font-weight: bold;
        text-align:center;
    }
    .table_header_top{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 19px;
        font-weight: bold;
        text-align:center;
        border-top: thin solid black;
        border-left: thin solid black;
        border-right: thin solid black;
    }
    .table_header_mid{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 19px;
        font-weight: bold;
        text-align:center;
        border-left: thin solid black;
        border-right: thin solid black;
    }
    .table_header_bot{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 19px;
        font-weight: bold;
        text-align:center;
        border-bottom: thin solid black;
        border-left: thin solid black;
        border-right: thin solid black;
    }
    .table_header_bot2{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 19px;
        font-weight: bold;
        text-align:center;
        border: thin solid black;
    }
    .table_body{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 21px;
        border: thin solid black;
    }
    .table_body_center{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 21px;
        border: thin solid black;
        text-align:center;
    }
    .table_body_right{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 21px;
        border: thin solid black;
        text-align:right;
    }
    @page { size: landscape;margin: 20px;}
</style>
<?php
$param = '';
if(!empty($_GET)){
    foreach(@$_GET as $key => $value){

        if(is_array($value[$_GET['report']])){
            $param .= $key.'=';
            foreach ($value[$_GET['report']] as $k => $v) {
                $param .= $v;
                if($k < (sizeof($value[$_GET['report']])-1)){
                    $param .= ',';
                }
            }
            $param .= '&';
        }else{
            $param .= $key.'='.$value.'&';
        }
    }
}
$i = 1;
$loan_type = $loan_type['loan_type'];
$loan_type = str_replace("?????????????????????","",$loan_type);

$date_start_txt = $start_date;
$date_end_txt = $end_date;
    ?>

    <div style="width: 1500px;" class="page-break">
        <div class="panel panel-body" style="padding-top:20px !important;height: 100%;min-height: 1000px;">
            <table style="width: 100%;">
                <tr>
                    <?php if(@$_GET['download']==""){ ?>
                    <td style="width:100px;vertical-align: top;">
                        <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
                    </td>
                    <?php } ?>
                    <td class="text-center" style="text-align: center;" <?php echo @$_GET['download']!=""? "colspan='4'":"colspan='2'"?>>
                        <h3 class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                        <h3 class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>"><?php echo "9.3 ???????????????????????????????????????".$loan_type."???????????? ???????????????????????????????????????????????????????????????????????????";?></h3>
                        <h3 class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>"><?php echo "??????????????????????????????????????? ".$date_start_txt." - ".$date_end_txt;?></h3>
                    </td>
                    <?php if(@$_GET['download']==""){ ?>
                    <td style="width:100px;vertical-align: top;" class="text-right">
                        <?php if($i == '1'){?>
                            <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
<!--                            <a href="--><?php //echo base_url(PROJECTPATH.'/report_new_loan_request/coop_report_loan_emergent_excel?'.$param); ?><!--" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>-->
                            <a class="no_print" target="_blank" onclick="goto()">
                                <button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
                            </a>
                        <?php } ?>
                    </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td colspan="3">
                        <h3 class="title_view">
                        </h3>
                    </td>
                </tr>
            </table>
            <table class="table table-view table-center">
                <thead>
                <tr>
                    <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" colspan="2" style="vertical-align: middle;">??????????????????</th>
                    <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" colspan="2" style="vertical-align: middle;">??????????????????????????????</th>
                    <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="1" style="vertical-align: middle;">????????????????????????????????????</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $all_num_total = 0;
                $all_loan_amount_total = 0;
                foreach($data as $date => $loan_reason){
                    $num_total = 0;
                    $loan_amount_total = 0;
                ?>
                <tr>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="text-align: left;text-decoration:underline; " colspan = '5'>????????????????????????????????? : <?php echo $this->center_function->mydate2date($date);?></td>
                </tr>
                <?php
                    foreach($loan_reason as $key => $value){
                        $num_total += $value['num'];
                        $loan_amount_total += $value['loan_amount'];
                        $all_num_total += $value['num'];
                        $all_loan_amount_total += $value['loan_amount'];
                ?>
                        <tr>
                            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="text-align: left;" colspan="2"><?php echo $value['loan_reason_name'];?></td>
                            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="text-align: center;" colspan="2"><?php echo $value['num']?></td>
                            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="text-align: right;"><?php echo number_format($value['loan_amount'], 2)?></td>
                        </tr>
                <?php } ?>
                <tr>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="text-align: right;" colspan="2">????????????????????????????????? : <?php echo $date;?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="text-align: center;" colspan="2"><?php echo $num_total;?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="text-align: right;"><?php echo number_format($loan_amount_total, 2);?></td>
                </tr>
                <?php } ?>

                </tbody>
            </table>

            <table style="width: 100%;" class="m-t-2">
                <tr>
<!--                    <td style="width: 350px;"></td>-->
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="width: 500px; text-align: right;"><h3 class="title_view"><?php echo "??????????????????????????????";?></h3></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="width: 100px;    text-align: center;"><h3 class="title_view"><?php echo number_format($all_num_total);?></h3></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="width: 150px;"><h3 class="title_view"><?php echo "??????????????? ";?></h3></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="width: 110px;"><h3 class="title_view"><?php echo "??????????????????????????????????????? " ;?></h3></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="width: 150px;    text-align: center;"><h3 class="title_view"><?php echo number_format($all_loan_amount_total,2) .' ?????????';?></h3></td>
<!--                    <td style="width: 50px;"><h3 class="title_view">--><?php //echo "????????? " ;?><!--</h3></td>-->
                    <td></td>
                </tr>
            </table>
        </div>
    </div>

<?php if(@$_GET['download']==""){ ?>
    <script>
        function goto(){
            // console.log(window.location.href );
            window.open(window.location.href+'&download=1');
        }
    </script>
<?php } ?>