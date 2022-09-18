<?php 
if(@$_GET['download']!=""){
    // header("Content-type: application/vnd.ms-excel;charset=utf-8;");
    // header("Content-Disposition: attachment; filename=รายงานการถอนเงินสามัญหมุนเวียน.xls"); 
    // date_default_timezone_set('Asia/Bangkok');
}

?>
<style>
@page {
  size: 210mm 297mm; 
  /* Chrome sets own margins, we change these printer settings */
  margin: 15mm 0mm 15mm 0mm; 
}

@media print {
	body {zoom: 50%;}
}
table {
    border-collapse: collapse;
}
span.title_view {
    font-size : 24px !important;
}

th, td {
    font-size : 24px !important;
    font-family: 'upbean';
    /* padding: 10px; */
}
td{
    text-align: left;
    padding: 5px;
}
.head_table{
    text-align: center;
    padding: 5px;
}

td.installment_table > table > tbody > tr > td:nth-child(1), td.installment_table > table > tbody > tr > td:nth-child(2), td.installment_table > table > tbody > tr > td:nth-child(3){
    text-align: center;
}

</style>			

		
		<div style="width: 90%;" class="text-center" >
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
				<table style="width: 100%;">
				<?php 
					
					// if(@$page == 1){
				?>	
					<tr>
						<td style="width:100px;vertical-align: top;">
							
						</td>
						<td class="text-center">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
							<h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							<h3 class="title_view">รายงานการเปลี่ยนแปลงงวดการชำระ <?php echo $type_name;?></h3>
							
							<h3 class="title_view">
                                <br>
								<?php 
									// echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"ตั้งแต่";
									// echo "วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
									// echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึงวันที่  ".$this->center_function->ConvertToThaiDate($end_date);
								?>
							</h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
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
						</td>
					</tr>  					
				<?php 
					// }else{
				?>
					<!-- <tr>
						<td colspan="3" style="text-align: left;">&nbsp;</td>
					</tr> -->
				<?php
					// } 
				?>
					
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>				
							<span class="title_view">   เวลา <?php echo date('H:i:s');?></span>	
						</td>
					</tr> 
					<tr>
						<td colspan="3" style="text-align: left;">
						</td>
					</tr>
                    <tr>
                        <td colspan="3">
                            <table border=0 width='100%'>
                                <tbody>
                                    <tr>
                                        <td width='50%'>
                                            <table width='100%'>
                                                <tr>
                                                    <td>รหัสสมาชิก</td>
                                                    <td><?=$member->member_id?></td>
                                                    <td>ชื่อสกุล</td>
                                                    <td><?=$member->fullname?></td>
                                                </tr>
                                                <tr>
                                                    <td>เลขที่สัญญา</td>
                                                    <td><?=$loan->contract_number;?></td>
                                                    <td>วันที่จ่ายเงินกู้</td>
                                                    <td><?=$this->center_function->ConvertToThaiDate($loan->date_start_period)?></td>
                                                </tr>
                                                <tr>
                                                    <td>วงเงินอนุมัติ</td>
                                                    <td><?=number_format($loan->loan_amount,2)?></td>
                                                    <td>จำนวนเงินคงเหลือ</td>
                                                    <td><?=number_format($loan->loan_amount_balance,2)?></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td>
                                        <table width='100%'>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>ประเภทสัญญา</td>
                                                    <td><?=$loan->type_name?></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>                            
                            </table>
                        </td>
                    </tr>
				</table>
                        
                <?php
                $template = array(
                        'table_open'  => '<table border="1" cellpadding="2" cellspacing="1" width="100%">',
                        'heading_cell_start'    => '<th class="head_table">',
                        'heading_cell_end'      => '</th class="head_table">',
                        'cell_start'            => '<td style="vertical-align: top;">',
                        'cell_end'              => '</td>',
                );
                    if($period_history){
                        foreach ($period_history as $key => $value) {
                            ?>
                            
                            <br><br>
                            <table width='100%'>
                                <thead>
                                    <tr>
                                        <td colspan='2' width='100%'>
                                        <h3><b>ครั้งที่ <?=$value->seq_no?></b> วันที่ <?=$this->center_function->ConvertToThaiDate(@$value->create_date);?> โดย <?=$value->user_name?></h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%">
                                            <h3><b>รูปแบบการชำระเดิม</b></h3>
                                        </td>
                                        <td>
                                            <h3><b>รูปแบบการชำระใหม่</b></h3>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td width="50%" style="vertical-align: text-top;" class="installment_table">
                                            <?php
                                                $fn_table->set_template($template);
                                                $fn_table->set_heading('รูปแบบการชำระ', 'ผ่อนชำระต่อเดือน', 'จำนวนงวด');
                                                $data = array($value->installment_previous);
                                                echo $fn_table->generate($data);
                                            ?>
                                        </td>
                                        <td width="50%" style="vertical-align: text-top;" class="installment_table">
                                            <?php
                                                $fn_table->set_template($template);
                                                $fn_table->set_heading('รูปแบบการชำระ', 'ผ่อนชำระต่อเดือน', 'จำนวนงวด');
                                                $data = array($value->installment_new);
                                                echo $fn_table->generate($data);
                                            ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <?php
                        }
                    }
                ?>

			</div>
		</div>
