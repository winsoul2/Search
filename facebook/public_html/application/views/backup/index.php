<div class="layout-content">
    <div class="layout-content-body">
		<?php
		$act = @$_GET['act'];
		$id = @$_GET['id'];
		?>

		<?php if (@$act != "add") { ?>
		<h1 style="margin-bottom: 0">สำรองข้อมูลและเรียกคืนข้อมูล</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<button class="btn btn-primary btn-lg bt-add btn_restore" type="button">
				<span class="icon icon-refresh"></span> เรียกคืนข้อมูล
			</button>
			
			<button class="btn btn-primary btn-lg bt-add btn_backup" type="button" style="margin-right: 10px;">
				<span class="icon icon-cloud-download"></span> สำรองข้อมูลด้วยตนเอง
			</button>
		</div>
		</div>
		<?php } ?>
		
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
			  <div class="panel panel-body">
				
				<div class="bs-example" data-example-id="striped-table">
				 <table class="table table-striped"> 
					 <thead> 
						  <tr>
							<th class="text-center" style="width: 180px;">วันที่สำรองข้อมูล</th>
							<th class="text-center">ประเภท</th>
							<th class="text-center" style="width: 180px;">ไฟล์สำรองข้อมูล</th>
							<th class="text-center" style="width: 180px;">ขนาดข้อมูล</th>
							<th class="text-center" style="width: 320px;">ดาวน์โหลด</th>
							<th class="text-center" style="width: 320px;">เรียกคืนข้อมูล</th>
						  </tr> 
					 </thead>
					  <tbody>
			   <?php  
				if(!empty($rs)){
					foreach(@$rs as $key => $row){ ?>
						<tr> 
							<td class="text-center"><?php echo $this->center_function->ConvertToThaiDate(@$row['backup_date']); ?></td>
							<td class="text-center"><?php echo @$row['backup_type_name']; ?></td>
							<td class="text-center"><?php echo @$row['file_type_name']; ?></td>
							<td class="text-center"><?php echo @$row['file_size']; ?></td>
							<td class="text-center">
								<?php if(empty($row['backup_file'])) { ?>
									-
								<?php } else { ?>
									<?php echo empty($row["download_date"]) ? "" : $this->center_function->ConvertToThaiDate(@$row['download_date'])." โดย ".$row['download_user']; ?>
									<a href="<?php echo base_url("backup/download?id=".@$row['backup_id']."&f=".@$row['backup_file']); ?>"><span class="icon icon-download"></span><?php if(empty($row["download_date"])) { ?> ดาวน์โหลด<?php } ?></a>
								<?php } ?>
							</td>
							<td class="text-center"><?php echo empty($row['restore_date']) ? "" : $this->center_function->ConvertToThaiDate(@$row['restore_date'])." โดย ".$row['restore_user']; ?></td>
						</tr>
				<?php 
						}
					} 
				?>

						</tbody> 
					</table> 
				</div>
			  </div>
			  
			</div>
		</div>

	</div>
</div>

<div class="modal fade" id="modal_backup"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title"><span class="icon icon-cloud-download"></span> สำรองข้อมูลด้วยตนเอง</h2>
            </div>
            <div class="modal-body">
				<form method="post" id="frm_backup" action="">
					<div class="g24-col-sm-24">
						<div class="form-group">
							<label class="g24-col-sm-4 control-label" style="text-align: left;">สำรองข้อมูล</label>
							<div class="g24-col-sm-8">
								<select id="file_type" name="file_type" class="form-control">
									<?php foreach($file_types as $key => $file_type) { ?>
										<option value="<?php echo $key; ?>"><?php echo $file_type; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					
					<div class="g24-col-sm-24">
						<div class="form-group">
							<div class="g24-col-sm-24" style="padding-top: 15px;">
								<div>
									เนื่องจากไฟล์ข้อมูลมีขนาดใหญ่ กรุณารอจนกว่าระบบดำเนินการเสร็จ ไม่ทำการปิด หรือรีเฟชร เพราะจะทำให้ระบบทำงานผิดพลาดได้
								</div>
							</div>
						</div>
					</div>
					
					<div class="g24-col-sm-24">
						<div class="form-group">
							<div class="g24-col-sm-24" style="padding-top: 15px;">
								<label>
									<input type="checkbox" id="chk_confirm_backup" name="chk_confirm_backup" value="1">
									ฉันได้อ่านและทำความเข้าใจข้อความระวังเป็นอย่างดีแล้ว
								</label>
							</div>
						</div>
					</div>
				</form>
				<div class="clearfix"></div>
				
            </div>
            <div class="text-center m-t-1">
                <button type="button" id="btn_backup_modal" class="btn btn-info" disabled="disabled"><span class="icon icon-cloud-download"></span> สำรองข้อมูล</button>
            </div>
			<div class="text-center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_backup_process"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title"><span class="icon icon-cloud-download"></span> สำรองข้อมูลด้วยตนเอง</h2>
            </div>
            <div class="modal-body">
				<div class="text-center">
					กำลังสำรองข้อมูล กรุณารอสักครู่
					<div style="position: relative; margin-top: 30px;">
						<div class="spinner spinner-default"></div>
					</div>
				</div>
            </div>
			<div class="text-center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_backup_completed"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title"><span class="icon icon-cloud-download"></span> สำรองข้อมูลด้วยตนเอง</h2>
            </div>
            <div class="modal-body">
				<div class="text-center">
					เสร็จสิ้นสำรองข้อมูล
				</div>
            </div>
			<div class="text-center m-t-1">
                <button type="button" class="btn btn-info" onclick="location.reload(true);"><span class="icon icon-close"></span> ปิดหน้าต่าง</button>
            </div>
			<div class="text-center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_restore"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title"><span class="icon icon-refresh"></span> เรียกคืนข้อมูล</h2>
            </div>
            <div class="modal-body">
				<form method="post" id="frm_retore" enctype="multipart/form-data" action="<?php echo base_url("backup/restore_process"); ?>" target="frm_retore_process">
					<div class="g24-col-sm-24">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label" style="text-align: left;">เรียกคืนข้อมูล</label>
							<div class="g24-col-sm-8">
								<select id="file_type" name="file_type" class="form-control">
									<?php foreach($file_types as $key => $file_type) { ?>
										<option value="<?php echo $key; ?>"><?php echo $file_type; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					
					<div class="g24-col-sm-24" style="padding-top: 5px;">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label" style="text-align: left;">ไฟล์สำรองข้อมูล</label>
							<div class="g24-col-sm-18">
								<label class="fileContainer btn btn-info">
									<span class="icon icon-paperclip"></span> เลือกไฟล์
									<input id="backup_file" data-index="1" name="backup_file" class="form-control m-b-1 file_upload" type="file" value="" data-target="#backup_file_info">
								</label>
								<label id="backup_file_info" style="padding-left: 10px;"></label>
							</div>
						</div>
					</div>
					
					<div class="g24-col-sm-24">
						<div class="form-group">
							<div class="g24-col-sm-24">
								<label class="control-label">ข้อความระวัง</label>
								<div style="padding-left: 30px;">
									1. กรุณาสำรองข้อมูลก่อน จะทำการเรียกคืนข้อมูลทุกครั้ง<br>
									2. การเรียกคืนข้อมูลจะทำการลบข้อมูลเดิมทั้งหมด และนำข้อมูลจากไฟล์สำรองข้อมูลที่ท่านเลือก มาใช้เป็นข้อมูลหลักการทำงาน<br>
									3. การเรียกคืนข้อมูลจะทำให้ระบบใช้งานไม่ได้ชั่วขณะ จนกว่าจะดำเนินการเสร็จ<br>
									4. ระหว่างที่เรียกคืนข้อมูล กรุณารอให้ระบบดำเนินการเสร็จ อย่ารีเฟชร หรือปิดหน้า จะทำให้ระบบทำงานผิดพลาดได้<br>
									5. การเรียกคืนข้อมูลมีความเสี่ยง กรุณาทำเมื่อจำเป็นเท่านั้น<br>
									6. ท่านสามารถทดสอบเรียกคืนข้อมูล เพื่อดูข้อมูลได้ที่ sitetest.spktcoop.com<br>
								</div>
							</div>
						</div>
					</div>
					
					<div class="g24-col-sm-24">
						<div class="form-group">
							<div class="g24-col-sm-24" style="padding-top: 15px;">
								<label>
									<input type="checkbox" id="chk_confirm_restore" name="chk_confirm_restore" value="1">
									ฉันได้อ่านและทำความเข้าใจข้อความระวังเป็นอย่างดีแล้ว
								</label>
							</div>
						</div>
					</div>
				</form>
				<div class="clearfix"></div>
				
            </div>
            <div class="text-center m-t-1">
                <button type="button" id="btn_restore_modal" class="btn btn-info" disabled="disabled"><span class="icon icon-refresh"></span> เรียกคืนข้อมูล</button>
            </div>
			<div class="text-center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>
<iframe id="frm_retore_process" name="frm_retore_process" style="width: 0; height: 0;"></iframe>

<div class="modal fade" id="modal_restore_process"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title"><span class="icon icon-refresh"></span> เรียกคืนข้อมูล</h2>
            </div>
            <div class="modal-body">
				<div class="text-center">
					กำลังเรียกคืนข้อมูล กรุณารอสักครู่
					<div style="position: relative; margin-top: 30px;">
						<div class="spinner spinner-default"></div>
					</div>
				</div>
            </div>
			<div class="text-center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_restore_completed"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title"><span class="icon icon-refresh"></span> เรียกคืนข้อมูล</h2>
            </div>
            <div class="modal-body">
				<div class="text-center">
					เสร็จสิ้นเรียกคืนข้อมูล
				</div>
            </div>
			<div class="text-center m-t-1">
                <button type="button" class="btn btn-info" onclick="location.reload(true);"><span class="icon icon-close"></span> ปิดหน้าต่าง</button>
            </div>
			<div class="text-center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<script>
	var base_url = $('#base_url').attr('class');
	
	function open_modal(id){
		$('#'+id).modal('show');
	}
	
	function close_modal(id){
		$('#'+id).modal('hide');
	}
	
	function restore_completed() {
		close_modal("modal_restore_process");
		open_modal("modal_restore_completed");
	}
	
	$( document ).ready(function() {
		$(".btn_backup").click(function() {
			open_modal("modal_backup");
		});
		
		$(".btn_restore").click(function() {
			open_modal("modal_restore");
		});
		
		$("#chk_confirm_backup").click(function() {
			$("#btn_backup_modal").prop("disabled", $(this).prop("checked") ? false : true);
		});
		
		$("#btn_backup_modal").click(function() {
			var file_type = $("#file_type").val();
			
			close_modal("modal_backup");
			open_modal("modal_backup_process");
			
			$.ajax({
				url: base_url + "/backup/backup_process",
				method: "post",
				data: { file_type: file_type },
				dataType: "text",
				success: function (data) {
					close_modal("modal_backup_process");
					open_modal("modal_backup_completed");
				}
			});
		});
		
		$(".file_upload").change(function() {
			var filename = $(this).val().replace(/.*(\/|\\)/, '');
			$($(this).data("target")).html(filename);
		});
		
		$("#chk_confirm_restore").click(function() {
			$("#btn_restore_modal").prop("disabled", $(this).prop("checked") ? false : true);
		});
		
		$("#btn_restore_modal").click(function() {
			$("#frm_retore").submit();
			close_modal("modal_restore");
			open_modal("modal_restore_process");
		});
	});
	
</script>
