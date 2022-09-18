<?php
$param = '';
if(!empty($_GET)){
    foreach($_GET AS $key=>$val){
        $param .= $key.'='.$val.'&';
    }
}
$i=0;
foreach($datas as $year=>$months) {
    foreach($months as $m => $data) {
        $i++;
        ?>
        <style>
            .table {
                color: #000;
            }
            .page-break{
                page-break-after: always;
            }
        </style>
        <div style="width: 1000px;" class="page-break">
            <div class="panel panel-body" style="padding-top:10px !important;min-height: 1420px;">
                <table style="width: 100%;">
                    <tr>
                        <td colspan='2'>
                            <h3 class="title_view" style="text-align: left;">ระเบียบวาระที่ 5 เรื่องเพื่อพิจารณา</h3>
                            <h3 class="title_view" style="text-align: left;">5.1 เรื่องการรับสมัครสมาชิกใหม่</h3>
                            <p>&nbsp;</p>
                        </td>
                        <td style="width:100px;vertical-align: top;" class="text-right">
                            <?php if($i == '1'){?>
                                <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                                <a href="<?php echo base_url(PROJECTPATH.'/report_member_data/coop_report_member_in_out_excel?'.$param); ?>" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100px;vertical-align: top;">
                            <!--							<img src="--><?php //echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?><!--" alt="Logo" style="height: 80px;" />-->
                        </td>
                        <td class="text-center">
                            <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                            <h3 class="title_view">รายชื่อผู้ประสงค์เป็นสมาชิกสหกรณ์</h3>
                            <h3 class="title_view">
                                <?php
                                    if($m != '12'){
                                        $next_month = sprintf("%02d",$m+1);
                                        $next_year = $year;
                                    }else{
                                        $next_month = '01';
                                        $next_year = $year+1;
                                    }
                                ?>
                                ประจำเดือน <?php echo $month_arr[$m].' '.($year+543);?> เริ่มหักเดือน <?php echo $month_arr[$next_month].' '.($next_year+543);?>
                            </h3>
                            <p>&nbsp;</p>
                        </td>
                        <td style="width:100px;vertical-align: top;" class="text-right">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <h3 class="title_view">
                                <?php
                                $members = array();
                                $count_register = 0;
                                if(!empty($data['member_in'])) {
                                    $members = $data['member_in']['members'];
                                    $count_register = count($members);
                                }
                                //								echo "ในระหว่างเดือน ".$month_arr[$m]."  ".($year+543)." มีพนักงานสมัครสมาชิกสหกรณ์  จำนวน  ".$count_register." ราย  ดังนี้";
                                ?>
                            </h3>
                        </td>
                    </tr>
                </table>
                <table class="table table-view table-center">
                    <thead>
                    <tr>
                        <th style="width: 40px;vertical-align: middle;">ลำดับที่</th>
                        <th style="width: 230px;vertical-align: middle;">ชื่อ - สกุล</th>
                        <th style="width: 50px;vertical-align: middle;">เลขประจำตัว</th>
                        <th style="width: 80px;vertical-align: middle;">ทะเบียนสมาชิก</th>
                        <th style="width: 230px;vertical-align: middle;">สังกัด/ผ่าย</th>
                        <th style="width: 70px;vertical-align: middle;">ค่าหุ้นรายเดือน</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $j = 1;
                    $share = 0;
                    if(!empty($data['member_in'])){
                        $member_ids = array_column($members, 'member_id');
                        array_multisort($member_ids, SORT_ASC, $members);
                        foreach($members as $key => $row){
                            $comment_txt = "";
                            if(!empty($row['re_register_check'])){
                                $comment_txt = "สมัครครั้งที่ 2";
                            }
                            $share += @$row['share_month'];

                            ?>
                            <tr>
                                <td style="text-align: center;"><?php echo @$j++;?></td>
                                <td style="text-align: left;"><?php echo @$row['prename_short'].@$row['firstname_th'].'  '.@$row['lastname_th']; ?></td>
                                <td style="text-align: center;"><?php echo @$row['employee_id']; ?></td>
                                <td style="text-align: center;"><?php echo @$row['member_id']; ?></td>
                                <td style="text-align: left;"><?php echo @$mem_group_arr[@$row['department']].' / '.@$mem_group_arr[@$row['faction']]; ?></td>
                                <td style="text-align: right;"><?php echo number_format(@$row['share_month'],2); ?></td>

                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td style="vertical-align: middle;border: 1px solid #000;" colspan="5"> รวม </td>
                        <td style="text-align: right; border: 1px solid #000;text-decoration:underline;"><?php echo number_format($share,2); ?></td>
                    </tr>
                    </tfoot>
                </table>
                <?php
                $prev_month = $m-1;
                $prev_year = $year;

                $this->db->select(array('t1.member_id','t2.resign_date','t2.req_resign_id'));
                $this->db->from("coop_mem_apply as t1");
                $this->db->join("coop_mem_req_resign as t2","t1.member_id = t2.member_id AND t2.req_resign_status = '1' AND t2.resign_date < '".date('Y-m-t',strtotime($prev_year."-".sprintf("%02d",($prev_month))."-01"))."'","left");
                $this->db->where("t1.apply_date < '".date('Y-m-t',strtotime($prev_year."-".sprintf("%02d",($prev_month))."-01"))."'");
                $rs_all_member = $this->db->get()->result_array();
                $count_all_member = 0;
                if(!empty($rs_all_member)){
                    foreach($rs_all_member as $key => $row_all_member){
                        if(@$row_all_member['req_resign_id']==''){
                            $count_all_member++;
                        }
                    }
                }
                ?>
                <table style="width: 100%;">
                    <tr>
                        <td><h3 class="title_view m-t-2"><?php echo "รวมสมาชิกสมัครใหม่เดือน ".$month_arr[$m]."  ".($year+543)." จำนวน  ".$count_register." ราย";?></h3></td>
                    </tr>
                </table>
            </div>
        </div>
        <!--
        <div style="width: 1000px;"  class="page-break">
            <div class="panel panel-body" style="padding-top:10px !important;min-height: 1420px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width:100px;vertical-align: top;">
                            <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
                        </td>
                        <td class="text-center">
                            <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                            <h3 class="title_view">เรื่องการรับสมัครสมาชิกใหม่และสมาชิกออกจากสหกรณ์ฯ</h3>
                            <h3 class="title_view">
                                <?php echo @$title_date;?>
                            </h3>
                            <p>&nbsp;</p>
                        </td>
                        <td style="width:100px;vertical-align: top;" class="text-right">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <h3 class="title_view">
                                <?php
                                $members = array();
                                $count_retire = 0;
                                if(!empty($data['member_out'])) {
                                    $members = $data['member_out']['members'];
                                    $count_retire = count($members);
                                }
                                echo "ในระหว่างเดือน ".@$month_arr[$m]."  ".($year+543)."  มีพนักงานลาออกจากการเป็นสมาชิกสหกรณ์  จำนวน  ".$count_retire." ราย  ดังนี้";
                                ?>
                            </h3>
                        </td>
                    </tr>
                </table>
                <table class="table table-view table-center">
                    <thead>
                    <tr>
                        <th style="width: 40px;vertical-align: middle;">ลำดับที่</th>
                        <th style="width: 80px;vertical-align: middle;">เลขทะเบียนสมาชิก</th>
                        <th style="width: 50px;vertical-align: middle;">รหัสพนักงาน</th>
                        <th style="width: 230px;vertical-align: middle;">ชื่อ - สกุล</th>
                        <th style="width: 230px;vertical-align: middle;">หน่วยงาน</th>
                        <th style="width: 80px;vertical-align: middle;">เงินค่าหุ้น สะสม(บาท)</th>
                        <th style="width: 80px;vertical-align: middle;">เงินค้างชำระ</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $j = 1;
                    $share = 0;
                    $loan = 0;
                    if(!empty($data['member_out'])){
                        $member_ids = array_column($members, 'member_id');
                        array_multisort($member_ids, SORT_ASC, $members);
                        foreach($members as $key => $row){
                            $share += $row['sum_share'];
                            $loan += $row['sum_loan'];

                            ?>
                            <tr>
                                <td style="text-align: center;"><?php echo @$j++;?></td>
                                <td style="text-align: center;"><?php echo @$row['member_id']; ?></td>
                                <td style="text-align: center;"><?php echo @$row['employee_id']; ?></td>
                                <td style="text-align: left;"><?php echo @$row['prename_short'].@$row['firstname_th'].'  '.@$row['lastname_th']; ?></td>
                                <td style="text-align: left;"><?php echo @$mem_group_arr[@$row['faction']]; ?></td>
                                <td style="text-align: right;"><?php echo number_format(@$row['sum_share'],2); ?></td>
                                <td style="text-align: right;"><?php echo number_format(@$row['sum_loan'],2); ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right;border-bottom: 3px double #000 !important;"><?php echo number_format(@$share,2); ?></td>
                        <td style="text-align: right;border-bottom: 3px double #000 !important;"><?php echo number_format(@$loan,2); ?></td>
                    </tr>
                    </tfoot>
                </table>
                <?php
                $prev_month = $m-1;
                $prev_year = $year;

                $this->db->select(array('t1.member_id','t2.resign_date','t2.req_resign_id'));
                $this->db->from("coop_mem_apply as t1");
                $this->db->join("coop_mem_req_resign as t2","t1.member_id = t2.member_id AND t2.req_resign_status = '1' AND t2.resign_date < '".date('Y-m-t',strtotime($prev_year."-".sprintf("%02d",($prev_month))."-01"))."'","left");
                $this->db->where("t1.apply_date < '".date('Y-m-t',strtotime($prev_year."-".sprintf("%02d",($prev_month))."-01"))."'");
                $rs_all_member = $this->db->get()->result_array();
                $count_all_member = 0;
                if(!empty($rs_all_member)){
                    foreach($rs_all_member as $key => $row_all_member){
                        if(@$row_all_member['req_resign_id']==''){
                            $count_all_member++;
                        }
                    }
                }
                ?>
                <table style="width: 100%;">
                    <tr>
                        <td><h3 class="title_view m-t-2"><?php echo 'สมาชิกคงเหลือ ณ.วันที่  '.date('t',strtotime($year."-".sprintf("%02d",$m)."-01")).' '.$month_arr[$m].'  '.($year+543);?></h3></td>
                        <td></td>
                        <td></td>
                        <td style="width: 50%;"></td>
                    </tr>
                    <tr>
                        <td><h3 class="title_view"><?php echo 'ยอดยกมา ('.date('t',strtotime($prev_year."-".sprintf("%02d",($prev_month))."-01")).' '.$month_arr[$prev_month].'  '.($prev_year+543).')';?></h3></td>
                        <td class="text-right"><h3 class="title_view"><?php echo number_format($count_all_member);?></h3></td>
                        <td><h3 class="title_view m-f-1"> ราย</h3></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><h3 class="title_view"><?php echo 'สมาชิกสมัครใหม่';?></h3></td>
                        <td class="text-right"><h3 class="title_view"><?php echo number_format($count_register);?></h3></td>
                        <td><h3 class="title_view m-f-1"> ราย</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><h3 class="title_view"><?php echo 'สมาชิกลาออก';?></h3></td>
                        <td class="text-right"><h3 class="title_view"><?php echo number_format($count_retire);?></h3></td>
                        <td><h3 class="title_view m-f-1"> ราย</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><h3 class="title_view"><?php echo 'จำนวนสมาชิกคงเหลือทั้งสิ้น';?></h3></td>
                        <td class="text-right"><h3 class="title_view"><?php echo number_format((($count_all_member+$count_register)-$count_retire));?></h3></td>
                        <td><h3 class="title_view m-f-1"> ราย</h3></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
        -->

        <?php
    }
}
?>
