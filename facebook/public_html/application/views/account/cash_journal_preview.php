<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}	
	.table {
		color: #000;
	}	
</style>		
<?php
    if(@$_GET['from_date']){
		$start_date_arr = explode('/',@$_GET['from_date']);
		$start_day = $start_date_arr[0];
		$start_month = $start_date_arr[1];
		$start_year = $start_date_arr[2];
		$start_year -= 543;
		$start_date = $start_year.'-'.$start_month.'-'.$start_day;
	}

    $runno = 0;
    $total = array();
    $is_pv = 0;
    foreach($datas as $page => $data) {
?>
    <div style="width: 8.3in;"  class="page-break">
        <div class="panel panel-body" style="padding-top:10px !important;height: 10.7in;">
            <table style="width: 100%;">
                <tr>
                    <td style="width:100px;vertical-align: top;">
                    </td>
                    <td class="text-center">
                        <!-- <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	 -->
                            <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                            <h3 class="title_view">สมุดรายวันรับ-สมุดรายวันจ่าย</h3>
                            <h3 class="title_view">
                            <?php 
                                echo "ประจำวันที่ ".$this->center_function->ConvertToThaiDate($start_date, false);
                            ?>
                            </h3>
                            <h3 class="title_view">รายการสด</h3>
                        </td>
                        <td style="width:100px;vertical-align: top;" class="text-right">
                        <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                        <?php
                            $get_param = '?';
                            foreach(@$_GET as $key => $value){
                                    $get_param .= $key.'='.$value.'&';
                            }
                            $get_param = substr($get_param,0,-1);
                        ?>
                        <a class="no_print"  target="_blank" href="<?php echo base_url('/account/cash_journal_excel'.$get_param); ?>">
                            <button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
                        </a>
                    </td>
                </tr> 
                <tr>
                    <td colspan="3" style="text-align: right;">
                        <span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></span>				
                    </td>
                </tr>  
                <tr>
                    <td colspan="3" style="text-align: right;">
                        <span class="title_view">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
                    </td>
                </tr>  
                <tr>
                    <td colspan="3" style="text-align: right;">
                        <span class="title_view">หน้าที่ <?php echo $page.'/'.$page_all;?></span><br>						
                    </td>
                </tr> 
            </table>
        
            <table class="table table-view table-center">
                <thead> 
                    <tr>
                        <th style="vertical-align: middle; width:10%;">เลขที่บัญชี</th>
                        <th style="vertical-align: middle; width:45%;">รายการ</th>
                        <th style="vertical-align: middle; width:15%;">เลขที่อ้างอิง</th>
                        <th style="vertical-align: middle; width:15%;">เดบิต</th>
                        <th style="vertical-align: middle; width:15%;">เครดิต</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach($data as $row) {
                            if($row["journal_type"] == "P" && $is_pv != 2) $is_pv = 1;
                            if(empty($runno)) {
                    ?>
                    <tr>
                        <td colspan="2" style="text-align: left; font-weight: bold;">รายรับ</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                            } else if ($is_pv == 1) {
                    ?>
                    <tr>
                        <td colspan="2" style="text-align: left; font-weight: bold;">รายจ่าย</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                                $is_pv = 2;
                            }
                            $runno++;
                            $journal_ref = substr($row["journal_ref"],0,1)."-".substr($row["journal_ref"],1,7)."-".substr($row["journal_ref"],8,3);
                    ?>
                    <tr>
                        <td><?php echo $row["account_chart_id"];?></td>
                        <td style="text-align: left;"><?php echo $row["account_chart"];?></td>
                        <td><?php echo $journal_ref;?></td>
                        <td style="text-align: right;"><?php echo $row["account_type"] == "debit" ? number_format($row["amount"],2) : "";?></td>
                        <td style="text-align: right;"><?php echo $row["account_type"] == "credit" ? number_format($row["amount"],2) : "";?></td>
                    </tr>
                    <?php
                            $total["debit"] += $row["account_type"] == "debit" ? $row["amount"] : 0;
                            $total["credit"] += $row["account_type"] == "credit" ? $row["amount"] : 0;
                        }

                        if($page == $page_all) {
                            $diff = $total["debit"] - $total["credit"];
                            $rv = $cash_balance + $diff_cash;
                            $pv = $cash_balance - $diff + $diff_cash;
                    ?>
                        <tr>
                            <td style="text-align: left;" colspan="3">รวม : รายรับ - รายจ่าย</td>
                            <td style="text-align: right;"><?php echo number_format($total["debit"],2);?></td>
                            <td style="text-align: right;"><?php echo number_format($total["credit"],2);?></td>
                        </tr>
                        <tr>
                            <td style="text-align: left; font-weight: bold;" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ยอดยกมา</td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"><?php echo number_format($rv,2);?></td>
                        </tr>
                        <tr>
                            <td style="text-align: left; font-weight: bold;" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;รวมเงิน</td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"><?php echo number_format($pv+$total["debit"],2);?></td>
                        </tr>
                        <tr>
                            <td style="text-align: left; font-weight: bold;" colspan="3">หักยอดรวมจ่ายยกไป</td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"><?php echo number_format($total["debit"],2);?></td>
                        </tr>
                        <tr>
                            <td style="text-align: left; font-weight: bold;" colspan="3">ยอดรวมเงินคงเหลือ</td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"><?php echo number_format($pv,2);?></td>
                        </tr>
                    <?php
                        }
                    ?>
                </tbody>    
            </table>
        </div>
    </div>
<?php } ?>