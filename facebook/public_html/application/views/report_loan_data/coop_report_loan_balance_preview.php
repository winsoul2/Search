<?php
$i = '1';
$h_border = "";
$b_border = "";
if(isset($_GET['excel'])){
    $i = '0';
    $file_name = " รายงาน".($_GET['loan_type'] ? $loan_type[$_GET['loan_type']] : 'เงินกู้')."คงเหลือ ".$date_title;
	//echo '<pre>'; print_r($_GET); echo '</pre>'; exit;
    header("Content-Disposition: attachment; filename=".$file_name.".xls");
    header("Content-type: application/vnd.ms-excel; charset=UTF-8");

    $h_border = ' border: thin solid black; ';
    $b_border = ' border-left: thin solid black;';
}
?>
<style>

	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 16px;
	}		
	@page { 
		size: landscape; 			
	}
	.table {
		color: #000;
	}
    <?php if(isset($_GET['excel'])) {?>

    * {
        all: unset;
    }

    body{
        background: #fff !important;
    }

    table {
        background: white !important;
    }

    table tbody tr td{
        border-collapse: collapse;
        border-top: none;
        border-bottom: none;
        border-left: thin solid black;
        border-right: thin solid black;
    }

    <?php } ?>
	</style>
		<div style="width: 1500px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1000px;">
				<table style="width: 100%;">
					<tr>
                        <?php if(!isset($_GET['excel'])){ ?>
						<td style="width:100px;vertical-align: top;">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
						</td>
                        <?php } ?>
						<td colspan="11" class="text-center">
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view"><?php echo "รายงาน".($_GET['loan_type'] ? $loan_type[$_GET['loan_type']] : 'เงินกู้')."คงเหลือ";?></h3>
							 <h3 class="title_view"><?php echo $date_title?></h3>
						 </td>
                        <?php if($i == '1'){?>
                        <td style="width:100px;vertical-align: top;" class="text-right">
								<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
								<a class="no_print" onclick="export_excel('<?php echo @$_GET['loan_type']?>', '<?php echo @$_GET['loan_name']?>','<?php echo @$_GET['start_date']?>', '<?php echo @$_GET['type_date']?>');"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>
						 </td>
                        <?php } ?>
					</tr>
				</table>
				<table class="table table-view table-center">
					<thead> 
						<tr>
                            <th  style="width: 80px;vertical-align: middle; <?php echo $h_border?>">สมาชิก<br>เลขทะเบียน</th>
							<th  style="width: 100px;vertical-align: middle; <?php echo $h_border?>">เลขที่สัญญา</th>
							<th  style="width: 80px;vertical-align: middle; <?php echo $h_border?>">ประเภทหลัก</th>
							<th  style="width: 200px;vertical-align: middle; <?php echo $h_border?>">ประเภทย่อย</th>
							<th  style="width: 80px;vertical-align: middle; <?php echo $h_border?>">วันที่ทำสัญญา</th>
							<th  style="width: 200px;vertical-align: middle; <?php echo $h_border?>">วัตถุประสงค์</th>
							<th  style="width: 85px;vertical-align: middle; <?php echo $h_border?>">ประเภทสัญญา</th>
							<th  style="width: 85px;vertical-align: middle; <?php echo $h_border?>">จำนวนงวด</th>
							<th  style="width: 85px;vertical-align: middle; <?php echo $h_border?>">สถานะสัญญา</th>
							<th  style="width: 85px;vertical-align: middle; <?php echo $h_border?>">วงเงินกู้</th>
							<th  style="width: 85px;vertical-align: middle; <?php echo $h_border?>">เงินต้นคงเหลือ</th>
						</tr>
					</thead>
					<tbody>
					  <?php 
						$count_loan = 0;
						$loan_amount=0;
						//echo '<pre>'; print_r(@$data); echo '</pre>';
						if(!empty($data)){
							foreach($data as $key => $value){
								$i+=1;
								$loan_amount += @$value['loan_amount'];
								$status = "";
								if(@$value['loan_status'] == '4' || $value['loan_amount_balance'] == 0 ){
                                    $status = $value['loan_amount_balance'] > 0 ? 'ปกติ' : 'ชำระเงินครบถ้วน';
                                }else{
                                    $status = 'ปกติ';
                                }
						?>
						  <tr>
                              <td style="text-align: center;<?php echo $border; ?>;"><?php echo @$value['member_id']; ?></td>
                              <td style="text-align: center;<?php echo $border; ?>"><?php echo @$value['contract_number']?></td>
                              <td style="text-align: left;<?php echo $border; ?>"><?php echo @$value['loan_type']?></td>
                              <td style="text-align: left;<?php echo $border; ?>"><?php echo @$value['loan_name']?></td>
                              <td style="text-align: center;<?php echo $border; ?>"><?php echo $this->center_function->mydate2date(@$value['createdatetime']); ?></td>
                              <td style="text-align: left;<?php echo $border; ?>"><?php echo @$value['loan_reason'] ? $value['loan_reason'] : 'ไม่ระบุ'?></td>
                              <td style="text-align: left;<?php echo $border; ?>"><?php echo @$value['period_type'] == '1' ? 'แบบคงต้น': 'แบบคงยอด';?></td>
                              <td style="text-align: right;<?php echo $border; ?>"><?php echo @$value['period_amount']; ?></td>
							  <td style="text-align: left;<?php echo $border; ?>"><?php echo $status;?></td>
                              <td style="text-align: right;<?php echo $border; ?>"><?php echo number_format(@$value['loan_amount'],2);?></td>
							  <td style="text-align: right;<?php echo $border; ?>"><?php echo number_format(@$value['loan_amount_balance'], 2); ?></td>
						  </tr>
					<?php 
							$count_loan++;
							}
						} 
					?>							
					</tbody>  
				</table>
				
<!--				<table style="width: 100%;" class="m-t-2">-->
<!--					<tr>-->
<!--						<td style="width: 200px;"></td>-->
<!--						<td style="width: 150px;"><h3 class="title_view">--><?php //echo "เดือน ".$month_arr[$m];?><!--</h3></td>-->
<!--						<td style="width: 40px;"><h3 class="title_view">--><?php //echo "รวม " ;?><!--</h3></td>-->
<!--						<td style="width: 50px;    text-align: center;"><h3 class="title_view">--><?php //echo number_format($count_loan);?><!--</h3></td>-->
<!--						<td style="width: 150px;"><h3 class="title_view">--><?php //echo "สัญญา ";?><!--</h3></td>-->
<!--						<td style="width: 110px;"><h3 class="title_view">--><?php //echo "เป็นเงินจำนวน " ;?><!--</h3></td>-->
<!--						<td style="width: 150px;    text-align: center;"><h3 class="title_view">--><?php //echo number_format($loan_amount) ;?><!--</h3></td>-->
<!--						<td style="width: 50px;"><h3 class="title_view">--><?php //echo "บาท " ;?><!--</h3></td>-->
<!--						<td></td>-->
<!--					</tr>-->
<!--				</table>-->
			</div>
		</div>
<?php if(!isset($_GET['excel'])) { ?>
<script>
    function export_excel(loan_type, loan_name, start_date, type_date) {
        window.open('coop_report_loan_balance_preview?excel=&start_date=' + start_date + '&type_date=' + type_date + '&loan_type=' + loan_type + '&loan_name=' + loan_name, '_blank');
        //window.open('coop_report_loan_normal_excel?loan_type='+loan_type+'&year='+year+'&second_half=1','_blank');
    }
</script>
<?php } ?>