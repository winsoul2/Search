<div class="layout-content">
    <div class="layout-content-body">
	<style>
		label{
			padding-top:7px;
		}
		.control-label{
			padding-top:7px;
			text-align:right;
		}
		.indent{
			text-indent: 40px;
			.modal-dialog-data {
				width:90% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
		}
		.bt-add{
			float:none;
		}
		.modal-dialog{
			width:80%;
		}
		small{
			display: none !important;
		}
		.cke_contents{
			height: 500px !important;
		}
		th{
			text-align:center;
		}
	</style>
		<h1 style="margin-bottom: 0">ประเภทเงินฝาก</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">	
				<a href="<?php echo base_url(PROJECTPATH.'/setting_deposit_data/coop_deposit_type_setting_detail_add?type_id='.@$_GET['type_id']); ?>">
					<button class="btn btn-primary btn-lg bt-add" type="button"><span class="icon icon-plus-circle"></span> เพิ่มรายการ</button> 
				</a>
				<a href="<?php echo base_url(PROJECTPATH.'/setting_deposit_data/coop_deposit_type_setting'); ?>">
					<button class="btn btn-primary btn-lg bt-add" type="button"><span class="icon icon-chevron-left"></span> กลับหน้าหลัก </button> 
				</a>
			</div>
		</div>	
		<div class="row gutter-xs">
			  <div class="col-xs-12 col-md-12">
					<div class="panel panel-body">
						<h1 class="text-left m-t-1 m-b-1"><?php echo @$row['type_code'].'  '.@$row['type_name']; ?></h1>
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-striped"> 
								<thead> 
									 <tr>
										<th class = "font-normal" width="5%">ลำดับ</th>
										<th class = "font-normal"> วันที่เพิ่ม </th>
										<th class = "font-normal"> วันที่มีผล </th>
										<th class = "font-normal"> สถานะ </th>
										<th class = "font-normal" style="width: 150px;"> จัดการ </th>
									</tr> 
								</thead>
								<tbody>
							 <?php  
								$i = 1;
								if(!empty($rs_detail)){
									foreach(@$rs_detail as $key => $row_detail){ 
							?>
									<tr> 
									  <td scope="row" align="center"><?php echo $i++; ?></td>
									  <td align="center"><?php echo $this->center_function->ConvertToThaiDate(@$row_detail['createdatetime']); ?></td> 
									  <td align="center"><?php echo $this->center_function->ConvertToThaiDate(@$row_detail['start_date'],1,0); ?></td> 
									  <td align="center"><?php echo @$row_status['type_detail_id']==@$row_detail['type_detail_id']?'<span style="color:green">ใช้งาน</span>':'ไม่ใช้งาน'; ?></td> 
									  <td align="center">
										  <a href="<?php echo base_url(PROJECTPATH.'/setting_deposit_data/coop_deposit_type_setting_detail_add?type_id='.@$row_detail['type_id']."&type_detail_id=".@$row_detail['type_detail_id']."&act=copy"); ?>">คัดลอก</a> |
										  <a href="<?php echo base_url(PROJECTPATH.'/setting_deposit_data/coop_deposit_type_setting_detail_add?type_id='.@$row_detail['type_id']."&type_detail_id=".@$row_detail['type_detail_id']); ?>">แก้ไข</a> |
										  <a href="#" onclick="del_coop_detail_data('<?php echo @$row_detail["type_id"]; ?>','<?php echo @$row_detail["type_detail_id"]; ?>')" class="text-del"> ลบ </a> 
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
					<?php echo @$paging ?>
				 </div>
		  </div>
	</div>
</div>  
<script>
	var base_url = $('#base_url').attr('class');
	function del_coop_detail_data(type_id,type_detail_id){
		swal({
			title: "",
			text: "ท่านต้องการลบข้อมูลใช่หรือไม่?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ยืนยัน',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: false,
			closeOnCancel: true
		 },
		 function(isConfirm){
		   if (isConfirm){
				document.location.href = base_url+'setting_deposit_data/coop_deposit_type_setting_detail_delete?type_id='+type_id+'&type_detail_id='+type_detail_id;
			}
		 });
	}
</script> 