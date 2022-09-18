
<?php
if(@$_GET['dev'] == "on") {
	echo "<pre>";
	print_r($datas);
	exit;
}
?>
<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}		
	.table {
		color: #000;
	}
	@page {
        /*size: landscape;*/
        size: 356mm 216mm ; /* F14 แนวนอน */
    }
</style>		
	<?php
		//echo '<pre>'; print_r($_GET); echo '</pre>';
		if(@$_GET['month']!='' && @$_GET['year']!=''){
			$day = '';
			$month = $_GET['month'];
			$year = $_GET['year'];
			$title_date = " เดือน ".$month_arr[$month]." ปี ".($year);
		}else{
			$day = '';
			$month = '';
			$year = (@$_GET['year']);
			$title_date = " ปี ".(@$year);
		}
		$runno = 0;
		$index = 0;
		$data_count = count($datas);
		$all_index = 1;
        $tmp_group = "";
        $tmp_lv = 0;
        $counter = 0;
		$sum_gua_prin = 0;
		$sum_gua_int = 0;
		$save_mem_group_id = NULL;
		foreach($datas as $member_id => $data){
			$counter++;

			if (!empty($data['total'])) {
				$runno++;
				$depositCount = !empty($data['DEPOSIT']) ? count($data['DEPOSIT']) : 1;
				$normalCount = !empty($data['normal']) ? count($data['normal']) : 1;
				$normalProjectCount = !empty($data['normal_project']) ? count($data['normal_project']) : 1;
				$emergentCount = !empty($data['emergent']) ? count($data['emergent']) : 1;
				$specialCount = !empty($data['special']) ? count($data['special']) : 1;
				$max_index = max(array($depositCount, $normalCount, $normalProjectCount, $emergentCount, $specialCount));
				for($i = 0; $i < $max_index; $i++) {
					if (($index == 0 || $index == 18 || ( $index > 18 && (($index-18) % 24) == 0 )) && ($tmp_group == $data['mem_group_name'] || $index == 0)) { // ใช้สำหรับ F14
//					if ($index == 0 || $index == 24 || ( $index > 24 && (($index-24) % 30) == 0 )) { // ใช้สำหรับ A4
	?>
		<div style="min-width: 1500px;"  class="page-break">
			<div class="panel panel-body " style="padding-top:10px !important;min-height: 950px;display: table;">
				<table style="width: 100%;">
				<?php 
					if($index == 0){
				?>	
					<tr>
						<td style="width:100px;vertical-align: top;">
							
						</td>
						<td class="text-center">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">สรุปรายการเรียกเก็บ รายบุคคล</h3>
							 <h3 class="title_view">
								<?php echo " ประจำ ".@$title_date;?>
							</h3>
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
							<a class="no_print"  target="_blank" href="<?php echo base_url('/report_processor_data/coop_report_charged_person_excel?'.$_SERVER['QUERY_STRING']); ?>">
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
				<?php
					}
				?>
					<!-- <tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>						
						</td>
					</tr>  -->
				</table>
			
				<table class="table table-view table-center">
					<thead>
						<tr>
							<th rowspan="2" style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th rowspan="2" style="width: 262px; text-align: left; vertical-align: middle;">หน่วยงานหลัก</th>
							<th rowspan="2" style="width: 66px;vertical-align: middle;">เลขพนักงาน</th>
							<th rowspan="2" style="width: 200px;vertical-align: middle;">ชื่อ-นามสกุล</th>
							<th rowspan="2" style="width: 64px;vertical-align: middle;">หุ้น</th>
							<?php 
								foreach($loan_type AS $key=>$row_loan_type){
							?>
							<th colspan="3" style="width: 200px;vertical-align: middle;"><?php echo str_replace('เงินกู้','',$row_loan_type['loan_type']);?></th>
							<?php
								}
							?>
							<th colspan="3" style="width: 200px;vertical-align: middle;">สัญญาค้ำประกัน</th>
							<th colspan="2" style="width: 160px;vertical-align: middle;">เงินฝาก</th>
							<th rowspan="2" style="width: 100px;vertical-align: middle;">ชำระหนี้ค้ำประกัน</th>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">รวม</th>
						</tr>
						<tr>
							<?php foreach ($loan_type AS $key=>$row_loan_type){ ?>
								<th style="vertical-align: middle;">เลขที่สัญญา</th>
								<th style="vertical-align: middle;">เงินต้น</th>
								<th style="vertical-align: middle;">ดอกเบี้ย</th>
							<?php } ?>
							<th style="vertical-align: middle;">เลขที่สัญญา</th>
							<th style="vertical-align: middle;">เงินต้น</th>
							<th style="vertical-align: middle;">ดอกเบี้ย</th>
							<th style="vertical-align: middle;">เลขบัญชี</th>
							<th style="vertical-align: middle;">จำนวนเงิน</th>
						</tr>
					</thead>
					<tbody>
					<?php
					}

					if($tmp_group == ""){
					    $tmp_group = $data['mem_group_name'];
                        $tmp_lv = $data['lv'];
                    }else if($tmp_group != "" && $tmp_group != $data['mem_group_name']){
                        if(($index-18) % 24 != 0){
					    ?>
                        <tr>
                            <td style="text-align: center;" colspan="4">รวม</td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_SHARE']) ? number_format($group_total_data[$tmp_lv.'_SHARE'], 2) : '';?></td>
                            <?php foreach ($loan_type AS $key=>$row_loan_type ){ ?>
                                <!-- ฉุกเฉิน -->
                                <td style="text-align: center;"></td>
                                <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_'.$row_loan_type['loan_type_code'].'_principal']) ? number_format($group_total_data[$tmp_lv.'_'.$row_loan_type['loan_type_code'].'_principal'],2) : '';?></td>
                                <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_'.$row_loan_type['loan_type_code'].'_interest']) ? number_format($group_total_data[$tmp_lv.'_'.$row_loan_type['loan_type_code'].'_interest'],2) : '';?></td>
                            <?php } ?>
                            <td style="text-align: center;"></td>
                            <td style="text-align: right;"><?php echo !empty($sum_prin)?number_format($sum_prin,2):'';?></td>
                            <td style="text-align: right;"><?php echo !empty($sum_int)?number_format($sum_int,2):'';?></td>
                            <td style="text-align: right;" ></td>
                            <td style="text-align: right;" ><?php echo !empty($group_total_data[$tmp_lv.'_DEPOSIT']) ? number_format($group_total_data[$tmp_lv.'_DEPOSIT'],2) : '';?></td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_GUARANTEE_AMOUNT']) ? number_format($group_total_data[$tmp_lv.'_GUARANTEE_AMOUNT'],2) : '';?></td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_total_amount']) ? number_format($group_total_data[$tmp_lv.'_total_amount'],2) : '';?></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                        </table>
                        </div>
                        </div>
                        <div style="min-width: 1500px;"  class="page-break">
                        <div class="panel panel-body " style="padding-top:10px !important;min-height: 950px;display: table;">
                        <table style="width: 100%;">
                        </table>
                        <table class="table table-view table-center">
                        <thead>
                        <tr>
                            <th rowspan="2" style="width: 40px;vertical-align: middle;">ลำดับ</th>
                            <th rowspan="2" style="width: 262px; text-align: left; vertical-align: middle;">หน่วยงานหลัก</th>
                            <th rowspan="2" style="width: 66px;vertical-align: middle;">เลขพนักงาน</th>
                            <th rowspan="2" style="width: 200px;vertical-align: middle;">ชื่อ-นามสกุล</th>
                            <th rowspan="2" style="width: 64px;vertical-align: middle;">หุ้น</th>
                            <?php
                            foreach($loan_type AS $key=>$row_loan_type){
                                ?>
                                <th colspan="3" style="width: 200px;vertical-align: middle;"><?php echo str_replace('เงินกู้','',$row_loan_type['loan_type']);?></th>
                                <?php
                            }
                            ?>
                            <th colspan="3" style="width: 200px;vertical-align: middle;">สัญญาค้ำประกัน</th>
                            <th colspan="2" style="width: 160px;vertical-align: middle;">เงินฝาก</th>
                            <th rowspan="2" style="width: 100px;vertical-align: middle;">ชำระหนี้ค้ำประกัน</th>
                            <th rowspan="2" style="width: 80px;vertical-align: middle;">รวม</th>
                        </tr>
                        <tr>
                            <?php foreach ($loan_type AS $key=>$row_loan_type){ ?>
                                <th style="vertical-align: middle;">เลขที่สัญญา</th>
                                <th style="vertical-align: middle;">เงินต้น</th>
                                <th style="vertical-align: middle;">ดอกเบี้ย</th>
                            <?php } ?>
                            <th style="vertical-align: middle;">เลขที่สัญญา</th>
                            <th style="vertical-align: middle;">เงินต้น</th>
                            <th style="vertical-align: middle;">ดอกเบี้ย</th>
                            <th style="vertical-align: middle;">เลขบัญชี</th>
                            <th style="vertical-align: middle;">จำนวนเงิน</th>
                        </tr>
                        </thead>
                        <tbody>
                    <?php
                        $tmp_lv = $data['lv'];
                        $tmp_group = $data['mem_group_name'];
						$sum_prin = 0;
						$sum_int = 0;
                        $index += 24 - ((($index-18) % 24)); // ลด $index
                    }

					?>
							<tr> 
								<td style="text-align: center;"><?php echo $runno;?></td>
								<td style="width: 262px; text-align: left;white-space: nowrap"><?php echo @$data['mem_group_id']; ?></td>
								<td style="text-align: center;"><?php echo $data['employee_id'];?></td>
								<td style="text-align: left; white-space: nowrap"><?php echo $data['member_name'];?></td>
								<td style="text-align: right;"><?php echo $i == 0 && !empty($data['SHARE']) ? number_format($data['SHARE'],2) : ""; ?></td>
								<?php

								foreach ($loan_type AS $key=>$row_loan_type){
									?>
									<!-- ฉุกเฉิน -->
									<?php
									if($loan_type[$key]['id'] == 8 && $data[$row_loan_type['loan_type_code']][$data[$row_loan_type['loan_type_code'].'_ids'][$i]]['chk_guarantee'] == 1) {
										$contract_guarantee =$data[$row_loan_type['loan_type_code']][$data[$row_loan_type['loan_type_code'].'_ids'][$i]]['contract_number'];
										$gua_principle= $data[$row_loan_type['loan_type_code']][$data[$row_loan_type['loan_type_code'].'_ids'][$i]]['principal'];
										$gua_interest= $data[$row_loan_type['loan_type_code']][$data[$row_loan_type['loan_type_code'].'_ids'][$i]]['interest'];
									?>
										<td style="width: 262px; text-align: left;white-space: nowrap"></td>
										<td style="text-align: center;"></td>
										<td style="text-align: left; white-space: nowrap"></td>
									<?php
									}else{
                                        $loan_name_short = $data[$row_loan_type['loan_type_code']][$data[$row_loan_type['loan_type_code'].'_ids'][$i]]['loan_name_short'];
									    ?>
									<td style="text-align: center;"><?php echo $loan_name_short.$data[$row_loan_type['loan_type_code']][$data[$row_loan_type['loan_type_code'].'_ids'][$i]]['contract_number'];?></td>
									<td style="text-align: right;"><?php echo !empty($data[$row_loan_type['loan_type_code']][$data[$row_loan_type['loan_type_code'].'_ids'][$i]]['principal']) ? number_format($data[$row_loan_type['loan_type_code']][$data[$row_loan_type['loan_type_code'].'_ids'][$i]]['principal'],2) : '';?></td>
									<td style="text-align: right;"><?php echo !empty($data[$row_loan_type['loan_type_code']][$data[$row_loan_type['loan_type_code'].'_ids'][$i]]['interest']) ? number_format($data[$row_loan_type['loan_type_code']][$data[$row_loan_type['loan_type_code'].'_ids'][$i]]['interest'],2) : '';?></td>

									<?php }
								}
								 ?>
								<td style="text-align: center;"><?php echo $contract_guarantee; ?></td>
								<td style="text-align: right;"><?php echo !empty($gua_principle)?number_format($gua_principle, 2):''; ?></td>
								<td style="text-align: right;"><?php echo !empty($gua_interest)?number_format($gua_interest, 2):''; ?></td>
								<td style="text-align: right;"><?php echo $data['deposit_account_id'][$i];?></td>
								<td style="text-align: right;"><?php echo !empty($data['DEPOSIT'][$i]) ? number_format($data['DEPOSIT'][$i],2) : '';?></td>
								<td style="text-align: right;"><?php echo $i == 0 && !empty($data['GUARANTEE_AMOUNT']) ? number_format($data['GUARANTEE_AMOUNT'],2): '';?></td>
								<td style="text-align: right;"><?php echo $i == 0 ? number_format($data['total'],2) : "";?></td> 							 
						  	</tr>						
					
					<?php
					$sum_gua_prin = $gua_principle + $sum_gua_prin;
					$sum_gua_int = $gua_interest + $sum_gua_int;
					$sum_prin = $sum_prin+$gua_principle;
					$sum_int = $sum_int+$gua_interest;
						if($data_count == $counter){
					?>
							<tr>
								<td style="text-align: center;" colspan="4">รวม</td>
								<td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_SHARE']) ? number_format($group_total_data[$tmp_lv.'_SHARE'], 2) : '';?></td>
								<?php foreach ($loan_type AS $key=>$row_loan_type ){ ?>
									<!-- ฉุกเฉิน -->
									<td style="text-align: center;"></td>
									<td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_'.$row_loan_type['loan_type_code'].'_principal']) ? number_format($group_total_data[$tmp_lv.'_'.$row_loan_type['loan_type_code'].'_principal'],2) : '';?></td>
									<td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_'.$row_loan_type['loan_type_code'].'_interest']) ? number_format($group_total_data[$tmp_lv.'_'.$row_loan_type['loan_type_code'].'_interest'],2) : '';?></td>
								<?php } ?>
								<td style="text-align: center;"></td>
								<td style="text-align: right;"><?php echo !empty($sum_prin) ? number_format($sum_prin,2) : '';?></td>
								<td style="text-align: right;"><?php echo !empty($sum_int) ? number_format($sum_int,2) : '';?></td>

								<td style="text-align: right;" ></td>
								<td style="text-align: right;" ><?php echo !empty($group_total_data[$tmp_lv.'_DEPOSIT']) ? number_format($group_total_data[$tmp_lv.'_DEPOSIT'],2) : '';?></td>
								<td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_GUARANTEE_AMOUNT']) ? number_format($group_total_data[$tmp_lv.'_GUARANTEE_AMOUNT'],2) : '';?></td>
								<td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_total_amount']) ? number_format($group_total_data[$tmp_lv.'_total_amount'],2) : '';?></td>
							</tr>
					<?php
						}
						// if($page == $page_all){
					$contract_guarantee="";
					$gua_principle=0;
					$gua_interest=0;
					?>

					<?php
						// }
						if($data_count == $all_index) {
					?>

				   			<tr>
								<td style="text-align: center;" colspan="4">รวมทั้งสิ้น</td>				 
								<td style="text-align: right;"><?php echo !empty($total_data['SHARE']) ? number_format($total_data['SHARE']) : '';?></td>
								<?php foreach ($loan_type AS $key=>$row_loan_type){ ?>
								<!-- ฉุกเฉิน -->
								<td style="text-align: center;"></td>
								<td style="text-align: right;"><?php echo !empty($total_data[$row_loan_type['loan_type_code'].'_principal']) ? number_format($total_data[$row_loan_type['loan_type_code'].'_principal'],2) : '';?></td>
								<td style="text-align: right;"><?php echo !empty($total_data[$row_loan_type['loan_type_code'].'_interest']) ? number_format($total_data[$row_loan_type['loan_type_code'].'_interest'],2) : '';?></td>
								<?php } ?>
								<td style="text-align: center;"></td>
								<td style="text-align: right;"><?php echo !empty($sum_gua_prin) ? number_format($sum_gua_prin,2) : '';?></td>
								<td style="text-align: right;"><?php echo !empty($sum_gua_int) ? number_format($sum_gua_int,2) : '';?></td>
								<td style="text-align: right;" ></td>
								<td style="text-align: right;" ><?php echo !empty($total_data['DEPOSIT']) ? number_format($total_data['DEPOSIT'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo !empty($total_data['GUARANTEE_AMOUNT']) ? number_format($total_data['GUARANTEE_AMOUNT'],2) : '';?></td>
								<td style="text-align: right;"><?php echo !empty($total_data['total_amount']) ? number_format($total_data['total_amount'],2) : '';?></td> 	 						 
						  	</tr>

					<?php
						}
						if ($data_count == $all_index || $index == 17 || ( $index > 18 && (($index-18) % 24) == 23 )) {  // ใช้สำหรับ F14
//						if ($data_count == $all_index || $index == 23 || ( $index > 24 && (($index-24) % 30) == 29 )) {
					?>
					</tbody>    
				</table>
			</div>
		</div>
<?php
						}
						$index++;
				}
			}
			$all_index++;
		}
?>
