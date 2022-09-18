<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}	
	.table {
		color: #000;
	}	
</style>		
<?php
    $balance = 0;
    if (!empty($data)) {
        foreach (@$data AS $page=>$data_row) {
?>

		<div style="width: 1000px;"  class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1420px;">
				<table style="width: 100%;">
				<?php
					if($page == 1){
				?>	
					<tr>
						<td style="width:100px;vertical-align: top;">

						</td>
						<td class="text-center">
                            <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
							<h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							<h3 class="title_view">รายงานคืนใบเสร็จยืนยันการประมวลผล</h3>
							<h3 class="title_view">ประจำ<?php echo $title_date?></h3>
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
							<a href="<?php echo base_url(PROJECTPATH.'/report_processor_data/coop_report_return_receipt_excel'.$get_param); ?>" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>	
						</td>
					</tr>
                <?php
                    }
                    ?>
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>						
						</td>
					</tr> 
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
                            <th style="width: 40px;vertical-align: middle;">ลำดับ</th>
                            <th style="width: 100px;vertical-align: middle;">หน่วยงานย่อย</th>
							<th style="width: 40px;vertical-align: middle;">เลขที่สมาชิก</th>
							<th style="width: 200px;vertical-align: middle;">ชื่อ-นามสกุล</th>
							<th style="width: 80px;vertical-align: middle;">รวม</th>
						</tr>
					</thead>
					<tbody>
					<?php	
                        $run_no = $last_run_no;
                        if(!empty($data_row)){
                            foreach(@$data_row as $key => $row){
                                $run_no++;
					?>
						<tr> 
							<td style="text-align: center;"><?php echo $run_no; ?></td>
							<td style="text-align: center;"><?php echo $row['mem_group_name'] ?></td>
							<td style="text-align: center;"><?php echo $row['member_id']; ?></td>
							<td style="text-align: left;"><?php echo $row['prename_full'].$row['firstname_th']." ".$row['lastname_th'];?></td>
							<td style="text-align: right;"><?php echo number_format($row['non_pay_amount_balance'],2);?></td>
						</tr>
					<?php
                            $balance += $row['id'];
                            }
                            $last_run_no = $run_no;
                        }

						// if($page == $page_all){
					?>
						   <!-- <tr>
							  <td style="text-align: center;" colspan="4"><?php echo "รวมทั้งสิ้น ".number_format($num_rows)." รายการ";?></td>
							  <td style="text-align: right;"><?php echo number_format($balance,2);?></td>
						  </tr> -->
					<?php
						// }
					?>	  
					</tbody>    
				</table>
			</div>
		</div>
<?php
        }
    } 
?>