<div class="layout-content">
    <div class="layout-content-body">
	<h1 style="margin-bottom: 0">การแจ้งเตือน</h1>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		</div>
	</div>
	
	<div class="row gutter-xs">
		<div class="col-xs-12 col-md-12">
		  <div class="panel panel-body">
			
			<div class="bs-example" data-example-id="striped-table">				
				<table class="table table-striped"> 
					<thead> 
						<tr>
							<th style="width: 50px;">#</th>
							<th>หัวข้อ</th>
							<th>ข้อความ</th>
							<th>สถานะ</th>
							<th>รายละเอียด</th>
						</tr> 
					</thead>
					
					<tbody>
					<?php
					if(!empty($rs)){
						foreach(@$rs as $key => $row){ 
					?>
							<tr>
								<td scope="row"><?php echo $i++; ?></td>
								<td><?php echo @$row["notification_title"]; ?></td> 
								<td><?php echo @$row["notification_text"]; ?></td> 
								<td><?php echo empty($row["ref_id"]) ? "ยังไม่ได้อ่าน" : "อ่านแล้ว"; ?></td> 
								<td><a href="<?php echo PROJECTPATH."/notification/update_notification?id=".$row['id']; ?>">รายละเอียด</a></td>
							</tr>
					<?php 
							}
						} 
					?>
					</tbody> 
				</table>
			</div>
	
		  </div>
		  <?php echo $paging ?>
		</div>
	</div>
	</div>
</div>