<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}	
	.table {
		color: #000;
	}
	@media print {
		.pagination {
			display: none;
		}
	}
</style>		
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
		$where_mem_type = "";
		if (!empty($_GET["mem_type"])){
			if (is_array($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
				$where_mem_type .= " AND t1.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
			} else if(!is_array($_GET["mem_type"]) && strpos($_GET["mem_type"], "all") === false){
				$where_mem_type .= " AND t1.mem_type_id IN ".str_replace(']',')',str_replace('[','(',$_GET["mem_type"]));
			}
		}

		$finance_profile = $this->db->select("*")
									->from("coop_finance_month_profile")
									->where("profile_month = '".$month."' AND profile_year = '".$year."'")
									->get()->row();

		$this->db->select(array(
			"t1.member_id", "t1.firstname_th", "t1.lastname_th", "t1.id_card", "t1.level", "t1.prename_id"
		));
		$rs_count = $this->db->from('coop_mem_apply as t1')
								->join("(SELECT profile_id, member_id FROM coop_finance_month_detail GROUP BY member_id) as t3","t1.member_id = t3.member_id","inner")
								->join("coop_finance_month_profile as t4","t3.profile_id = t4.profile_id","inner")
								->join("(SELECT * FROM coop_receipt WHERE finance_month_profile_id = '".$finance_profile->profile_id."') as t5","t4.profile_id = t5.finance_month_profile_id AND t3.member_id = t5.member_id","inner")
								->join("coop_mem_group AS t6","t1.`level` = t6.id","inner")
								->where("1=1 {$where} {$where_mem_type}")
								->group_by('t1.member_id')
								->order_by('t6.mem_group_id ASC,t1.level ASC,t1.member_id ASC')
								->get()->result_array();
		//echo $this->db->last_query(); echo '<hr>'; exit;
		$num_rows = count($rs_count);
		$page_limit = 100;
		$page_all = @ceil($num_rows/$page_limit);
		$arr_total = array();
		$count_member = 0;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;

		// for($page = 1;$page<=$page_all;$page++){
		$page_start = (($page-1)*$page_limit);
		$per_page = $page*$page_limit;
	?>

		<div style="width: 1000px;"  class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1420px;">
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
							 <h3 class="title_view">รายงานการหักเงิน</h3>
							 <h3 class="title_view">
								<?php echo " ประจำ ".@$title_date;?>
							</h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							<?php
								$get_param = '?';
								foreach(@$_GET as $key => $value){
									$get_param .= $key.'='.str_replace('"','',json_encode($value)).'&';
								}
								$get_param = substr($get_param,0,-1);
							?>
							<a href="<?php echo base_url(PROJECTPATH.'/report_processor_data/coop_report_deduction_excel'.$get_param); ?>" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>	
						</td>
					</tr>
				<?php
					// }
				?>
				</table>
			
				<table class="table table-view table-center">
					<thead>
						<tr>
							<th style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th style="width: 40px;vertical-align: middle;">เลขที่สมาชิก</th>
							<th style="width: 200px;vertical-align: middle;">ชื่อ-นามสกุล</th>
							<th style="width: 100px;vertical-align: middle;">เลขบัตรประชาชน</th>
							<th style="width: 80px;vertical-align: middle;">จำนวนเงิน</th>
							<th style="width: 200px;vertical-align: middle;">สังกัด</th>
							<th style="width: 200px;vertical-align: middle;">คำนำหน้าชื่อ</th>
							<th style="width: 200px;vertical-align: middle;">ชื่อ</th>
							<th style="width: 200px;vertical-align: middle;">นามสกุล</th>

						</tr>
					</thead>
					<tbody>
					<?php
							for($index=0; $index < count($rs_count); $index++) {
								if($index >= $page_start && $index < ($page_start + $page_limit)) {
									$row = $rs_count[$index];

									$prename = $this->db->select(array('prename_full'))
														->from("coop_prename")
														->where("prename_id = '".$row["prename_id"]."'")
														->get()->row();
									$finances = $this->db->select(array(
																'SUM(t3.pay_amount) as pay_amount',
																't5.receipt_datetime'
															))
														->from('coop_finance_month_detail as t3')
														->join("coop_finance_month_profile as t4","t3.profile_id = t4.profile_id","inner")
														->join("coop_receipt as t5","t4.profile_id = t5.finance_month_profile_id AND t3.member_id = t5.member_id","inner")
														->where("t3.member_id = '".$row['member_id']."'". $where)
														->group_by('t3.member_id')
														->get()->result_array();
									$finance = $finances[0];
									$runno++;
									
									$full_name =$prename->prename_full.@$row['firstname_th'].'  '.@$row['lastname_th'];
					?>
							<tr>
							  <td style="text-align: center;"><?php echo @$runno + $page_start; ?></td>
							  <td style="text-align: center;"><?php echo @$row['member_id']; ?></td>
							  <td style="text-align: left;"><?php echo @$full_name; ?></td>
							  <td style="text-align: center;"><?php echo @$row['id_card']; ?></td>
							  <td style="text-align: right;"><?php echo number_format($finance['pay_amount'],2);?></td>
							  <td style="text-align: left;"><?php echo @$mem_group_arr[@$row['level']];?></td>
							  <td style="text-align: left;"><?php echo $prename->prename_full;?></td>
							  <td style="text-align: left;"><?php echo $row['firstname_th'];?></td>
							  <td style="text-align: left;"><?php echo $row['lastname_th'];?></td>
						  </tr>
					<?php
										$count_member++;
										$pay_amount += $row['pay_amount'];
								}
							}

						if($page == $page_all){
					?>
						   <!-- <tr> 
							  <td style="text-align: center;" colspan="5">รวมทั้งสิ้น</td>
							  <td style="text-align: right;"><?php echo number_format($pay_amount,2);?></td>
						  </tr> -->
					<?php
						}
					?>
					</tbody>
				</table>
				<?php
					$paging = $this->pagination_center->paginating(intval($page), $page_all, 1, 20,@$_GET);
					echo $paging;
				?>
			</div>
		</div>
		<?php
			// }
		?>