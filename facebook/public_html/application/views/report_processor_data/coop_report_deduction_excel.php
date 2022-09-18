<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานการหักเงิน.xls"); 
date_default_timezone_set('Asia/Bangkok');
?>
<pre>
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<style>
				.num {
				  mso-number-format:General;
				}
				.text{
				  mso-number-format:"\@";/*force text*/ 
				}
				.text-center{
					text-align: center;
				}
				.text-left{
					text-align: left;
				}
				.table_title{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 22px;
					font-weight: bold;
					text-align:center;
				}
				.table_title_right{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 16px;
					font-weight: bold;
					text-align:right;
				}
				.table_header_top{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border-top: thin solid black;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_mid{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_bot{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border-bottom: thin solid black;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_bot2{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border: thin solid black;
				}
				.table_body{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 21px;
					border: thin solid black;
				}
				.table_body_right{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 21px;
					border: thin solid black;
					text-align:right;
				}
			</style>
		</head>
		<body>
<?php
	if(@$_GET['month']!='' && @$_GET['year']!=''){
		$day = '';
		$month = @$_GET['month'];
		$year = (@$_GET['year']);
		$title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year);
	}else{
		$day = '';
		$month = '';
		$year = (@$_GET['year']);
		$title_date = " ปี ".(@$year);
	}
	$where = " AND t4.profile_month = '".$month."' AND t4.profile_year = '".$year."'";

	?>
			<table class="table table-bordered">
				<tr>
					<tr>
						<th class="table_title" colspan="6"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="6">รายงานการหักเงิน</th>
					</tr>
					<tr>
						<th class="table_title" colspan="6"><?php echo " ประจำ ".$title_date;?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="6">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="6">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>

			<table class="table table-bordered">
				<thead>
					<th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
					<th class="table_header_top" style="vertical-align: middle;">เลขที่สมาชิก</th>
					<th class="table_header_top" style="vertical-align: middle;">ชื่อ-นามสกุล</th>
					<th class="table_header_top" style="width:500px; vertical-align: middle;">เลขบัตรประชาชน</th>
					<th class="table_header_top" style="vertical-align: middle;">จำนวนเงิน</th>
					<th class="table_header_top" style="vertical-align: middle;">รวม</th>
					<th class="table_header_top" style="vertical-align: middle;">สังกัด</th>
					<th class="table_header_top" style="vertical-align: middle;">คำนำหน้าชื่อ</th>
					<th class="table_header_top" style="vertical-align: middle;">ชื่อ</th>
					<th class="table_header_top" style="vertical-align: middle;">นามสกุล</th>
				</thead>
				<tbody>
					<?php

						$where_mem_type = "";
						if (!empty($_GET["mem_type"])){
							if (is_array($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
								$where_mem_type .= " AND t1.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
							} else if(!is_array($_GET["mem_type"]) && strpos($_GET["mem_type"], "all") === false){
								$where_mem_type .= " AND t1.mem_type_id IN ".str_replace(']',')',str_replace('[','(',$_GET["mem_type"]));
							}
						}
						$rs = $this->db->select(array(
												't1.member_id',
												't1.id_card',
												't1.prename_id',
												't1.firstname_th',
												't1.lastname_th',
												't2.prename_short',
												't2.prename_full',
												't1.level',
											))
										->from('coop_mem_apply as t1')
										->join("coop_prename as t2","t1.prename_id = t2.prename_id","left")
										->join("coop_mem_group AS t6","t1.`level` = t6.id","inner")
										->where("1=1 {$where_mem_type}")
										->group_by('t1.member_id')
										->order_by('t6.mem_group_id ASC,t1.level ASC,t1.member_id ASC')
										->get()->result_array();

						$finance_profile = $this->db->select("*")
													->from("coop_finance_month_profile")
													->where("profile_month = '".$month."' AND profile_year = '".$year."'")
													->get()->row();
						$runno = 0;
						$pay_amount = 0;
						$index = 0;
						$level_total = 0;
						if(!empty($rs)){
							for($index=0; $index < count($rs); $index++) {
								// if ($rs[$index]['level'] != $rs[$index-1]['level']) {
								// 	$level_total = 0;
								// }
								$row = $rs[$index];
								$finances = $this->db->select(array(
																'SUM(t3.pay_amount) as pay_amount',
															))
														->from("(SELECT * FROM coop_finance_month_detail WHERE member_id = '".$row['member_id']."' AND profile_id = '".$finance_profile->profile_id."') as t3")
														// ->join("(SELECT * FROM coop_receipt WHERE finance_month_profile_id = '".$finance_profile->profile_id."' AND member_id = '".$row['member_id']."') as t5","t3.profile_id = t5.finance_month_profile_id AND t3.member_id = t5.member_id","inner")
														->where("t3.member_id = '".$row['member_id']."'")
														->group_by('t3.member_id')
														->get()->result_array();
								$finance = $finances[0];
								if (!empty($finance)) {
									$runno++;
									$full_name = @$row['prename_full'].@$row['firstname_th'].'  '.@$row['lastname_th'];
									$level_total += $finance['pay_amount'];
					?>
							<tr>
							  <td class="table_body" style="text-align: center;"><?php echo @$runno; ?></td>
							  <td class="table_body" style="text-align: center;"><?php echo @$row['member_id']; ?></td>
							  <td class="table_body" style="text-align: left;"><?php echo @$full_name; ?></td>
							  <td class="table_body" style="text-align: center;"><?php echo @$row['id_card']; ?></td>
							  <td class="table_body" style="text-align: right;"><?php echo number_format($finance['pay_amount'],2);?></td>
							  <td class="table_body" style="text-align: right;">
								  	<?php
									  	if ($rs[$index]['level'] != $rs[$index+1]['level']) {
											echo number_format($level_total,2);
											$level_total = 0;
										}
									?>
							  </td>
							  <!-- <td class="table_body" style="text-align: right;">
								  	<?php
									  	echo $rs[$index]['level']."::".$rs[$index+1]['level'];
									?>
								</td> -->
							  <td class="table_body" style="text-align: left;"><?php echo @$mem_group_arr[@$row['level']];?></td>
							  <td class="table_body" style="text-align: left;"><?php echo @$row['prename_full'];?></td>
							  <td class="table_body" style="text-align: left;"><?php echo @$row['firstname_th'];?></td>
							  <td class="table_body" style="text-align: left;"><?php echo @$row['lastname_th'];?></td>
						  </tr>
					<?php
									$count_member++;
									$pay_amount += $finance['pay_amount'];
								}
							}
						}

						if($page == $page_all){
					?>
						   <tr>
							  <td class="table_body" style="text-align: center;" colspan="4">รวมทั้งสิ้น</td>
							  <td class="table_body" style="text-align: right;"><?php echo number_format($pay_amount,2);?></td>
						  </tr>
					<?php
						}
					?>
				</tbody>
			</table>
		</body>
	</html>
</pre>