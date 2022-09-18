<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
    }
    .none-border td {
        border: unset !important;
    }
    .table {
		color: #000;
	}
</style>
<?php
    $transfer_total = 0;
    $cash_total  = 0;
    $total = 0;
    // $all_total = 0;
    if (!empty($data)) {
        foreach (@$data AS $page=>$data_row) {
?>

<div style="width: 1000px;"  class="page-break">
    <div class="panel panel-body" style="padding-top:10px !important;min-height: 1420px;">
        <table style="width: 100%;">
            <?php 
                if(@$page == 1){
            ?>
                <tr>
                    <td style="width:100px;vertical-align: top;">
                        
                    </td>
                    <td class="text-center">
                            <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
                            <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                            <h3 class="title_view">รายงานเงินคืนสมาชิกลาออก</h3>
                            <h3 class="title_view"><?php echo @$title_date;?></h3>
                            <!-- <h3 class="title_view">วันที่  <?php echo @$date?></h3> -->
                    </td>
                    <td style="width:100px;vertical-align: top;" class="text-right">
                        <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
						<?php
							$get_param = '?';
							foreach(@$_GET as $key => $value){
								if($key != 'mem_type'){
									$get_param .= $key.'='.$value.'&';
								}
								
								if($key == 'mem_type'){
									foreach($value as $key2 => $value2){
										$get_param .= $key.'[]='.$value2.'&';
									}
								}	
							}
							$get_param = substr($get_param,0,-1);
							
						?>
						<a class="no_print"  target="_blank" href="<?php echo base_url('/report_processor_resign_data/coop_report_refund_resign_month_excel'.$get_param); ?>">
							<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
						</a>
					</td>
                </tr>
            <?php 
                }
            ?>
                <!--<tr>
                    <td colspan="3" style="text-align: right;">
                        <span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>
                    </td>
                </tr>-->
                <tr>
                    <td colspan="3" style="text-align: right;">
                        <span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;">
                        <span class="title_view">เวลา <?php echo date('H:i:s');?></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;">
                        <span class="title_view">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
                    </td>
                </tr>

        </table>

        <table class="table table-view table-center">
            <thead>
                <tr>
                    <th style="width: 10px;vertical-align: middle;">ลำดับ</th>
                    <th style="width: 10px;vertical-align: middle;">รหัสสมาชิก</th>
                    <th style="width: 100px;vertical-align: middle;">ชื่อ-นามสกุล</th>
                    <th style="width: 20px;vertical-align: middle;">เลขที่ใบเสร็จ</th>
                    <th style="width: 20px;vertical-align: middle;">หุ้น</th>
                    <th style="width: 20px;vertical-align: middle;">เลขที่สัญญา</th>
                    <th style="width: 20px;vertical-align: middle;">เงินต้น</th>
                    <th style="width: 20px;vertical-align: middle;">ดอกเบี้ย</th>
                    <th style="width: 20px;vertical-align: middle;">จำนวนเงินฝาก</th>
                    <th style="width: 20px;vertical-align: middle;">เลขบัญชีกรุงไทย</th>
                    <th style="width: 20px;vertical-align: middle;">เบอร์โทร </th>
                </tr> 
            </thead>
            <tbody>
            <?php
                $run_no = $last_run_no;
                if(!empty($data_row)){
                    foreach(@$data_row as $key => $row){
                        $run_no++;
						$run_loan = 0;
						if(@$row['loan']){
							foreach(@$row['loan'] AS $key_loan=> $row_loan){
								if(@$run_loan == 0){
									$key_loan_f = $key_loan;									
									$run_loan++;
								}
							}
						}	
            ?>
                <tr>
                    <td style="text-align: center;"><?php echo $run_no;?></td>
                    <td style="text-align: center;"><?php echo @$row['member_id']; ?></td>
                    <td style="text-align: left;"><?php echo @$row['prename_full'].@$row['firstname_th']." ".@$row['lastname_th'];?></td>
                    <td style="text-align: left;"><?php echo @$row['receipt_id'];?></td>
                    <td style="text-align: right;"><?php echo ($row['share'] != '')?number_format($row['share'],2,'.',','):'';?></td>
                    <td style="text-align: left;"><?php echo @$row['loan'][$key_loan_f]['contract_number'];?></td>
                    <td style="text-align: right;"><?php echo (@$row['loan'][$key_loan_f]['principal'] != '')?number_format(@$row['loan'][$key_loan_f]['principal'],2,'.',','):'';?></td>
                    <td style="text-align: right;"><?php echo (@$row['loan'][$key_loan_f]['interest'] != '')?number_format(@$row['loan'][$key_loan_f]['interest'],2,'.',','):'';?></td>
                    <td style="text-align: right;"><?php echo (@$row['deposit'] != '')?number_format(@$row['deposit'],2,'.',','):'';?></td>
                    <td style="text-align: left;"><?php echo  @$row['bank_account'];?></td>
                    <td style="text-align: center;"><?php echo @$row['mobile'];?></td>
                </tr>
				<?php 
					$run_loan_n = 0;
					if(@$row['loan']){
						foreach(@$row['loan'] AS $key_loan_n=> $row_loan){
							if(@$run_loan_n > 0){
				?>
							<tr>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: left;"></td>
								<td style="text-align: left;"></td>
								<td style="text-align: right;"></td>
								<td style="text-align: left;"><?php echo @$row['loan'][$key_loan_n]['contract_number'];?></td>
								<td style="text-align: right;"><?php echo (@$row['loan'][$key_loan_n]['principal'] != '')?number_format(@$row['loan'][$key_loan_n]['principal'],2,'.',','):'';?></td>
								<td style="text-align: right;"><?php echo (@$row['loan'][$key_loan_n]['interest'] != '')?number_format(@$row['loan'][$key_loan_n]['interest'],2,'.',','):'';?></td>
								<td style="text-align: right;"></td>
								<td style="text-align: left;"></td>
								<td style="text-align: center;"></td>
							</tr>
				<?php 
							}
							$run_loan_n++;	
							$total_principal += @$row['loan'][$key_loan_n]['principal'];
							$total_interest += @$row['loan'][$key_loan_n]['interest'];
						} 
					}
				?>
				
            <?php
						
						$total_share += @$row['share'];
						$total_deposit += @$row['deposit'];
					}
                        
                }
                $last_run_no = $run_no;

                if($page == $page_all){
            ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">รวม</td>
                            <td style="text-align: right;"><?php echo number_format($total_share,2,'.',',');?></td>
                            <td style="text-align: right;"> </td>
                            <td style="text-align: right;"><?php echo number_format($total_principal,2,'.',',');?></td>
                            <td style="text-align: right;"><?php echo number_format($total_interest,2,'.',',');?></td>
                            <td style="text-align: right;"><?php echo number_format($total_deposit,2,'.',',');?></td>
                            <td style="text-align: right;"> </td>
                            <td style="text-align: right;"> </td>
                        </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php echo $paging;?>
    </div>
</div>
<?php   
        }
    }
?>
<script>
    function PrintDiv(data) {
        console.log("=========here===========")
        var mywindow = window.open();
        var is_chrome = Boolean(mywindow.chrome);
        mywindow.document.write(data);
        if (is_chrome) {
            setTimeout(function() { // wait until all resources loaded 
                mywindow.document.close(); // necessary for IE >= 10
                mywindow.focus(); // necessary for IE >= 10
                mywindow.print(); // change window to winPrint
                mywindow.close(); // change window to winPrint
            }, 500);
        } else {
            mywindow.document.close(); // necessary for IE >= 10
            mywindow.focus(); // necessary for IE >= 10
            mywindow.print();
            mywindow.close();
        }
        return true;
    }
</script>