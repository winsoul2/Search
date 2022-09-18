<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
		  .form-group{
			margin-bottom: 5px;
		  }
        .btn-position-top{
            margin-top:  -1em;
        }
		</style> 
		<h1 style="margin-bottom: 0">นำเข้า ฌาปนกิจ สสอค</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
                <button name="btn_approve" id="btn_approve" type="button" class="btn btn-primary btn-lg btn-position-top">
                    <span>ลบรายการ</span>
                </button>
                <button name="btn_delete" id="btn_delete" type="button" class="btn btn-danger btn-lg btn-position-top">
                    <span>เพิ่มการนำเข้า</span>
                </button>
            </div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<h3 ></h3>
					 <table class="table table-bordered table-striped table-center">
					 <thead> 
						<tr class="bg-primary">
                            <th style="width: 45px;">
                                <div class="form-check">
                                    <input type="checkbox" name="chk-approve" value="" id="chk-all" onclick="check_all_page()">
                                </div>
                            </th>
							<th style="width:150px;">วันที่ทำรายการ</th>
							<th>เลขฌาปนกิจ</th>
							<th>ชื่อสมาชิก</th>
							<th>ประเภทสมาชิก</th>
							<th>ผู้ทำรายการ</th>
							<th>สถานะ</th>
							<th style="width:150px;"></th> 
						</tr> 
					 </thead>
					 <tbody id="table_first">
					  <?php 
						$cremation_status = array('0'=>'รอการอนุมัติ', '1'=>'อนุมัติ', '5'=>'ไม่อนุมัติ');
						
						foreach($data as $key => $row ){
						?>
						  <tr>
                              <td>
                                  <?php
                                  if(@$row['cremation_status']=='0'){
                                  ?>
                                      <div class="form-check">
                                          <input class="chk-approve" type="checkbox" name="member[<?php echo $row['member_id']; ?>]" value="<?php echo $row['cremation_request_id']; ?>" data-type="<?php echo $row['cremation_status']; ?>" id="chk-mem-id">
                                          <label class="form-check-label" for="chk-approve">
                                          </label>
                                      </div>
                                  <?php }else if(@$row['cremation_status']=='5'){ ?>
                                      <div class="form-check">
                                          <input class="chk-approve" type="checkbox" name="member[<?php echo $row['member_id']; ?>]" value="<?php echo $row['cremation_request_id']; ?>" data-type="<?php echo $row['cremation_status']; ?>" id="chk-mem-id">
                                          <label class="form-check-label" for="chk-approve">
                                          </label>
                                      </div>
                                  <?php } ?>
                              </td>
							  <td><?php echo $this->center_function->ConvertToThaiDate(@$row['createdatetime']); ?></td>
							  <td><?php echo @$row['member_cremation_id']; ?></td>
							  <td class="text-left"><?php echo @$row['assoc_firstname']." ".@$row['assoc_lastname']; ?></td>
							  <td class="text-left"><?php echo @$row['mem_type_id'] == '1' ? 'สามัญ' : 'สมทบ'; ?></td>
							  <td class="text-left"><?php echo @$row['user_name']; ?></td> 
							  <td><span id="cremation_status_<?php echo @$row['cremation_request_id']; ?>" ><?php echo @$cremation_status[$row['cremation_status']]; ?></span></td>
							  <td>
								<?php 
									if(@$row['cremation_status']=='0'){
								?>
									<a class="btn-radius btn-info" id="approve_<?php echo @$row['cremation_request_id']; ?>_1" title="อนุมัติ" onclick="approve_cremation('<?php echo @$row['cremation_request_id']; ?>','1')">
										อนุมัติ
									</a>
									<a class="btn-radius btn-danger" id="approve_<?php echo @$row['cremation_request_id']; ?>_1" title="ไม่อนุมัติ" onclick="approve_cremation('<?php echo @$row['cremation_request_id']; ?>','5')">
										ไม่อนุมัติ
									</a>
								<?php } ?>
							  </td>
						  </tr>
					  <?php } ?>
					  </tbody> 
					  </table> 
					</div>
			  </div>
		</div>
		<?php echo @$paging ?>
	</div>
</div>
<script>

</script>