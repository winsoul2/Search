<style>
    @media print {
        .pagination {
            display: none;
        }
        @page { margin: 0; }
    }
    center {
        width: 90%;
    }

    html {
        height: 210mm;
        width: 297mm;
        margin: auto;
    }
    table,tr ,{
        padding: 5px;
    }
    .b {
        padding: 5px 8px;
        border: 1px solid black;
        font-size: 16px;
        color: black;
    }
    td {
        text-align: center;
        font-size: 16px;
    }
    th {
        text-align: center;
        background-color: #efefef;
    }

    .landscape {
        min-height: 210mm; !important;
        min-width: 297mm; !important;
    }

</style>
<div style="width: 1000px;" class="page-break">
    <div class="panel panel-body landscape">
        <table style="width: 100%;">
            <tr>
                <td style="width:100px;vertical-align: top;">

                </td>
                <td class="text-center">
                    <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                    <h3 class="title_view">รายงานอายุสมาชิก</h3>
                </td>
                <td style="width:100px;vertical-align: top;" class="text-right">
                    <a class="no_print" onclick="no_url_print()"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                    </a>
                    <a class="no_print" target="_blank"
                       href="<?php echo base_url(PROJECTPATH . '/report_member_data/coop_report_member_age_excel'); ?>">
                        <button class="btn btn-perview btn-after-input" type="button"><span
                                class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
                    </a>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left;">
                    <span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>
                    <span class="title_view">   เวลา <?php echo date('H:i:s');?></span>
                </td>
            </tr>
        </table>
        <table style="width: 95%;">
            <thead>
            <tr style="text-align: center;">
                <th class="b" >ลำดับ</th>
                <th class="b" >รหัสสมาชิก</th>
                <th class="b" >ชื่อสกุล</th>
                <th class="b" >วันเดือนปีเกิด</th>
                <th class="b" >อายุ</th>
                <th class="b" >วันเข้าเป็นสมาชิก</th>
                <th class="b" >อายุการเป็นสมาชิก</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $count = 0;
            foreach ($datas as $data){
                $count++;
                $diff = date_diff(date_create($data['member_date'] ),date_create(date('Y-m-d')));
                $day = floor($diff->format("%a")%365);
                ?>
                <tr>
                    <td class="b"> <?php echo $count ?> </td>
                    <td class="b"> <?php echo $data['member_id']?> </td>
                    <td class="b" style="text-align: left"> <?php echo $data['prename_full'].$data['firstname_th']." ".$data['lastname_th']?> </td>
                    <td class="b"> <?php echo $this->center_function->ConvertToThaiDate($data['birthday'])?> </td>
                    <td class="b"> <?php echo $this->center_function->diff_year($data['birthday'],date('Y-m-d'))." ปี"?> </td>
                    <td class="b"> <?php echo $this->center_function->ConvertToThaiDate($data['member_date'])?> </td>
                    <td class="b"> <?php echo $this->center_function->diff_year($data['member_date'],date('Y-m-d'))." ปี ". $day . " วัน"?></td>
                </tr>
            <? }?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function no_url_print(){
        document.title = "รายงานอายุสมาชิก";
        window.print()
    }
</script>
