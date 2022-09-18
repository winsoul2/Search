<div class="layout-content">
    <div class="layout-content-body">
		<?php 
			$mysqli_upbean = new mysqli("report.upbean.co.th", "upbean_report", "aPY9rD3wL");
			$mysqli_upbean->select_db("upbean_report");
			$mysqli_upbean->set_charset("utf8");
		?>
		<h1 style="margin-bottom: 0">แจ้งปัญหาและข้อเสนอแนะ</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<a class="link-line-none" href="<?php echo base_url(PROJECTPATH.'/report_problem_data/report_problem_add');?>">
					<button class="btn btn-primary btn-lg bt-add" type="button">
						<span class="icon icon-plus-circle"></span>
						เพิ่มรายการ
					</button>
				</a>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<div class="bs-example" data-example-id="striped-table">
						<table class="table table-striped"> 
							<thead> 
								<tr>
									<th>ลำดับ</th>
									<th>วันที่แจ้ง</th>
									<th>หัวข้อ</th>
									<th>ความเร่งด่วน</th>
									<th>วันที่ต้องการ</th>
									<th>ผู้แจ้ง</th>
									<th>สถานะ</th>
									<th></th> 
								</tr> 
							</thead>
							<tbody>
							<?php 
							$i=1;
							$problem_priority = array('1'=>'ปกติ','2'=>'เร่งด่วน','3'=>'เร่งด่วนมาก');
							$problem_status = array('0'=>'เจ้าหน้าที่กำลังแก้ไข','1'=>'แก้ไขเสร็จสิ้น');
							if(!empty($rs)){
								foreach(@$rs as $key => $row){ 
								
								$sql_upbean = "SELECT problem_status FROM report_problem WHERE problem_id = '".$row['problem_id']."' AND coop_name = 'freetradecoop'";
								$rs_upbean = $mysqli_upbean->query($sql_upbean);
								$row_upbean = $rs_upbean->fetch_assoc();
								
							?>
								<tr> 
									<td><?php echo $i++; ?></td>
									<td><?php echo @$this->center_function->ConvertToThaiDate(@$row['create_date']); ?></td> 
									<td><?php echo @$row['problem_title']; ?></td>
									<td><?php echo @$problem_priority[@$row['problem_priority']]; ?></td>
									<td><?php echo @$this->center_function->ConvertToThaiDate(@$row['finish_date'],'1','0'); ?></td>
									<td><?php echo @$row['user_name']; ?></td>
									<td><?php echo @$problem_status[@$row_upbean['problem_status']]; ?></td>
									<td><a href="<?php echo base_url(PROJECTPATH.'/report_problem_data/report_problem_detail?problem_id='.@$row['problem_id']); ?>">ดูรายละเอียด</a> | 
									<?php if(@$row_upbean['problem_status']=='0'){ ?>
									<a href="<?php echo base_url(PROJECTPATH.'/report_problem_data/report_problem_add?problem_id='.@$row['problem_id']); ?>">แก้ไข</a> | 
									<?php } ?>
									<span onclick="delete_problem('<?php echo @$row['problem_id']?>');" class="text-del del">ลบ</span></td> 
								</tr>
							<?php 
								}
							} 
							?>
							</tbody> 
						</table> 
					</div>

				</div>
			<?php echo @$paging ?>
			</div>
		</div>
	</div>
</div>

<script>
	function delete_problem(problem_id){
		swal({
			title: "",
			text: "ท่านต้องการลบไฟล์ใช่หรือไม่?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ยืนยัน',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: true,
			closeOnCancel: true
		},
		function(isConfirm) {
			if (isConfirm) {			
				$.ajax({
					url: base_url+'/report_problem_data/delete_problem',
					method: 'POST',
					data: {
						'table': 'report_problem_file',
						'id': problem_id,
						'field': 'problem_id'
					},
					success: function(msg){
					   //console.log(msg); return false;
						if(msg == 1){
						  document.location.href = base_url+'report_problem_data/report_problem';
						}else{

						}
					}
				});
			} else {
				
			}
		});
	}
</script>