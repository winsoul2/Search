<?php
    if(@$_GET['start_date']){
		$start_date_arr = explode('/',@$_GET['start_date']);
		$start_day = $start_date_arr[0];
		$start_month = $start_date_arr[1];
		$start_year = $start_date_arr[2];
		$start_year -= 543;
		$start_date = $start_year.'-'.$start_month.'-'.$start_day;
	}
	
	if(@$_GET['end_date']){
		$end_date_arr = explode('/',@$_GET['end_date']);
		$end_day = $end_date_arr[0];
		$end_month = $end_date_arr[1];
		$end_year = $end_date_arr[2];
		$end_year -= 543;
		$end_date = $end_year.'-'.$end_month.'-'.$end_day;
	}
    foreach($datas as $page=>$data) {
?>
		<style>
			.table {
				color: #000;
			}
		</style>
		<div style="width: 1000px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;height: 1420px;">
                <?php if($page == '1'){?>
                    <table style="width: 100%;">
                        <tr>
                            <td style="width:100px;vertical-align: top;">
                                <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
                            </td>
                            <td class="text-center">
                                <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                                <h3 class="title_view">รายงานการรับสมัครสมาชิก<?php echo !empty($member_status_text) ? $member_status_text : "";?></h3>
                                <h3 class="title_view">
                                    <?php 
                                        echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"ตั้งแต่";
                                        echo "วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
                                        echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึงวันที่  ".$this->center_function->ConvertToThaiDate($end_date);
                                    ?>
                                </h3>
                                <p>&nbsp;</p>	
                            </td>
                            <td style="width:100px;vertical-align: top;" class="text-right">
                                <?php
                                    $get_param = '?';
                                    foreach(@$_GET as $key => $value){
                                            $get_param .= $key.'='.$value.'&';
                                    }
                                    $get_param = substr($get_param,0,-1);
                                ?>
                                
                                    <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                                    <a href="<?php echo base_url(PROJECTPATH.'/report_member_data/coop_report_wait_for_approval_member_excel'.$get_param); ?>" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>	
                                
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <h3 class="title_view">
                                <?php
                                    if(!empty($_GET["apply_type_id"])) {
                                        echo "ประเภท ";
                                        echo $_GET["apply_type_id"] == '1' ? "สามัญ " : "สมทบ ";
                                    }
                                    echo "มีพนักงานสมัครสมาชิกสหกรณ์  จำนวน  ".$member_count." ราย  ดังนี้";								
                                ?>	
                                </h3>
                            </td>
                        </tr> 
                    </table>
                <?php } ?>
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th style="width: 40px;vertical-align: middle;">ลำดับที่</th>
							<th style="width: 80px;vertical-align: middle;">เลขทะเบียนสมาชิก</th>
                            <th style="width: 80px;vertical-align: middle;">เลขที่คำร้อง</th>
                            <th style="width: 100px;vertical-align: middle;">วันที่อนุมัติ</th>
							<th style="width: 205px;vertical-align: middle;">ชื่อ - สกุล</th> 
							<th style="width: 205px;vertical-align: middle;">หน่วยงาน</th> 
							<th style="width: 70px;vertical-align: middle;">ค่าหุ้น(บาท)</th> 
							<th style="vertical-align: middle;">หมายเหตุ</th> 
						</tr> 
					</thead>
					<tbody>
					    <?php
                            foreach($data as $row) {
						?>
                        <tr> 
                            <td style="text-align: center;"><?php echo ++$j;?></td>
                            <td style="text-align: center;"><?php echo $row['member_id']; ?></td>
                            <td style="text-align: center;"><?php echo $row['mem_apply_id']; ?></td>
                            <td style="text-align: center;"><?php echo $this->center_function->ConvertToThaiDate($row["member_date"]); ?></td>
                            <td style="text-align: left;"><?php echo $row['prename_full'].$row['firstname_th'].'  '.$row['lastname_th']; ?></td>						 
                            <td style="text-align: left;"><?php echo $row["mem_group_name"]; ?></td> 							 
                            <td style="text-align: right;"><?php echo number_format($row['share_month'],2); ?></td> 						 
                            <td style="text-align: left;"><?php echo $row["register_note"]; ?></td> 							 
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