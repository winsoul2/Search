<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}
	.table-view>tbody>tr>th{
	    border-top: 1px solid #000 !important;
		border-bottom: 1px solid #000 !important;
		font-size: 12px;
		background-color: #eee;
	}
	.table-view-2>tbody>tr>td{
	    border: 0px !important;
		/*font-family: upbean;
		font-size: 16px;*/
		font-family: Tahoma;
		font-size: 12px;
	}	
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}
	.table-view-2>tbody>tr>td>span{
		font-family: Tahoma;
		font-size: 12px !important;
	}
	.foot-border{
	    border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}
	.table {
		color: #000;
	}
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		padding:6px;
	}
</style>
<?php
	$runno = 0;
	$prev_level = "x";
	foreach($datas AS $page=>$data){
?>
<div style="width: 8.3in;" class="page-break">
	<div class="panel panel-body" style="padding-top:10px !important;height: 11.7in;">
		<table style="width: 100%;">
		<?php 
			if($page == 1 || $datas[$page-1][0]["level"] != $datas[$page][0]["level"] ) {
		?>	
			<tr>
				<td style="width:100px;vertical-align: top;">

				</td>
				<td class="text-center">
					<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
					<h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
					<h3 class="title_view">รายงานรายชื่อสมาชิก</h3>
					<h3 class="title_view">
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
					<a class="no_print"  target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_member_data/coop_report_member_name_excel'.$get_param); ?>">
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
			<tbody>
			<?php
				$first_of_page = 1;
				$max = count($data)-1;
				foreach($data as $key => $row){
					if($prev_level == $row["level"] && !empty($first_of_page)) {
			?>
				<tr>
					<th style="width: 80px;vertical-align: middle;">ลำดับ</th>
					<th style="width: 120px;vertical-align: middle;">เลขสมาชิก</th>
					<th style="vertical-align: middle;">ชื่อ-นามสกุล</th>
					<th style="width: 200px;vertical-align: middle;">ลายเซ็น</th>
				</tr>
			<?php
					}	
					if($prev_level != $row["level"]) {
						if($key != 0) {
			?>
				<tr>
					<td colspan="4" class="text-left">
						รวมทั้งสิ้น <?php echo $runno;?> คน
					</td>
				</tr>
			<?php
						}
			?>
				<tr>
					<th colspan="4" class="text-left">
						หน่วยงาน :: <?php echo !empty($row["level"]) ? $row["mem_group_name"]."(".$row["mem_group_id"].")" : "";?>
					</th>
				</tr>
			<?php
			?>
				<tr>
					<th style="width: 80px;vertical-align: middle;">ลำดับ</th>
					<th style="width: 120px;vertical-align: middle;">เลขสมาชิก</th>
					<th style="vertical-align: middle;">ชื่อ-นามสกุล</th>
					<th style="width: 200px;vertical-align: middle;">ลายเซ็น</th>
				</tr>
			<?php
						$runno = 0;
					}
			?>
				<tr>
					<td style="text-align: center;vertical-align: top;"><?php echo ++$runno; ?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo $row['member_id'];?></td>
					<td style="text-align: left;vertical-align: top;"><?php echo $row['prename_full'].$row["firstname_th"]." ".$row["lastname_th"];?></td>
					<td style="text-align: center;vertical-align: top;"></td>
				</tr>
			<?php
					$first_of_page = 0;
					$prev_level = $row["level"];
					if($max == $key && $datas[$page+1][0]["level"] != $row["level"]) {
			?>
				<tr>
					<td colspan="4" class="table_body">
						รวมทั้งสิ้น <?php echo $runno;?> คน
					</td>
				</tr>
			<?php
					}
				}
			?>
			</tbody>
		</table>
	</div>
</div>
<?php
	}
?>