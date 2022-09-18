<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}
	@page { size: landscape; }
</style>
<?php

    $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

	foreach($datas AS $page => $members) {
	?>

		<div style="width: 950px;" >
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1500px;">
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
							 <h3 class="title_view">รายการส่งยืนยันยอดหุ้น - หนี้ และเงินฝาก</h3>
							 <h3 class="title_view">
								วันที่ <?php echo $this->center_function->ConvertToThaiDate($date,'','0');?>
							</h3>
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
							<a class="no_print"  target="_blank" href="<?php echo base_url('/certificate/coop_confirmation_share_debt_dept_excel'.$get_param); ?>">
								<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
							</a>
						</td>
					</tr>
				<?php } ?>
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>
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
							<th style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th style="width: 200px;vertical-align: middle;">หน่วยคณะ</th>
							<th style="width: 60px;vertical-align: middle;">เลขที่สมาชิก</th>
							<th style="width: 130px;vertical-align: middle;">ชื่อ-นามสกุล</th>
                            <th style="width: 80px;vertical-align: middle;">หุ้น</th>
                            <th style="width: 80px;vertical-align: middle;">หนี้(รวม)</th>
                            <th style="width: 80px;vertical-align: middle;">เงินฝาก(รวม)</th>
						</tr>
					</thead>
					<tbody>
					<?php
                        foreach($members AS $member_id => $member) {
							$runno++;
					?>
                        <tr>
                            <td style="text-align: center;"><?php echo $runno; ?></td>
                            <td style="text-align: left;"><?php echo $member["department_name"].",".$member["faction_name"].",".$member["level_name"]; ?></td>
							<td style="text-align: center;"><?php echo $member['member_id']; ?></td>
							<td style="text-align: left;"><?php echo $member["prename_full"].$member["firstname_th"]." ".$member["lastname_th"]?></td>
                            <td style="text-align: right;"><?php echo number_format($member['share_collect_value'],2); ?></td>
                            <td style="text-align: right;"><?php echo number_format($member['loan'],2); ?></td>
                            <td style="text-align: right;"><?php echo number_format($member['account'],2); ?></td>
                        </tr>
                    <?php
                        }
                    ?>
					</tbody>
				</table>
			</div>
		</div>
<?php
	}
?>