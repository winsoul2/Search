<div class="layout-content">
    <div class="layout-content-body">
	<style>
		label{
			padding-top:7px;
		}
		.control-label{
			padding-top:7px;
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
		<h1 style="margin-bottom: 0">ฌาปนกิจสงเคราะห์</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">	
			   <button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_type();"><span class="icon icon-plus-circle"></span> เพิ่มรายการ</button> 
			</div>
		</div>	
		  <div class="row gutter-xs">
			  <div class="col-xs-12 col-md-12">
					  <div class="panel panel-body">
				<div class="bs-example" data-example-id="striped-table">
				<table class="table table-striped"> 
					<thead> 
						 <tr>
							<th class = "font-normal" width="5%">ลำดับ</th>
							<th class = "font-normal"> ชื่อฌาปนกิจสงเคราะห์ </th>
							<th class = "font-normal" style="width: 20%"> ชื่อย่อ </th>
							<th class = "font-normal" style="width: 150px;"> จัดการ </th>
						</tr> 
					</thead>
					<tbody>
				 <?php  
					if(!empty($rs)){
						foreach(@$rs as $key => $row){ 
				?>
						<tr> 
						  <td scope="row" align="center"><?php echo $i++; ?></td>
						  <td class="text-left"><?php echo @$row['cremation_name']; ?></td> 
						  <td align="center"><?php echo @$row['cremation_name_short']; ?></td> 
						  <td align="center">
								<a href="<?php echo base_url(PROJECTPATH.'/setting_cremation_data/cremation_data_detail?cremation_id='.@$row["cremation_id"]); ?>">จัดการ</a> |
								<a href="#" onclick="edit_type('<?php echo @$row['cremation_id']; ?>','<?php echo @$row['cremation_name']; ?>','<?php echo @$row['cremation_name_short']; ?>','1')">แก้ไข</a> |
								<a href="#" onclick="del_coop_data('<?php echo @$row['cremation_id']; ?>')" class="text-del"> ลบ </a> 
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
<div id="cremation_data_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title"><span id="title_1">เพิ่มฌาปนกิจสงเคราะห์</span></h2>
			</div>
			<div class="modal-body">
				<div class="form-group" style="padding-bottom: 30px;">
				<form id='form1' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_cremation_data/cremation_data_save'); ?>" method="post">	
					<input type="hidden" class="form-control" id="cremation_id" name="cremation_id" value="">
					<div class="row">
						<label class="col-sm-4 control-label text-right" for="cremation_name">ชื่อฌาปนกิจสงเคราะห์</label>
						<div class="col-sm-6">
							<div class="form-group">
								<input id="cremation_name" name="cremation_name" class="form-control m-b-1" type="text" value="" required title="กรุณากรอก ชื่อฌาปนกิจสงเคราะห์">
							</div>
						</div>
						<label class="col-sm-2 control-label">&nbsp;</label>
					</div>
					<div class="row">
						<label class="col-sm-4 control-label text-right" for="cremation_name_short">ชื่อย่อ</label>
						<div class="col-sm-6">
							<div class="form-group">
								<input id="cremation_name_short" name="cremation_name_short" class="form-control m-b-1" type="text" value="" required title="กรุณากรอก ชื่อย่อ">
							</div>
						</div>
						<label class="col-sm-2 control-label">&nbsp;</label>
					</div>
					
					<div class="form-group">
						<div class="col-sm-12" style="text-align:center;margin-top:20px;margin-bottom:20px;">
							<button type="button" class="btn btn-primary" onclick="save_type()">บันทึก</button>&nbsp;&nbsp;&nbsp;
							<button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
						</div>
					</div>
					
					<table id="group_table" class="table table-bordered table-striped table-center">
						<thead> 
							<tr class="bg-primary">
								<th width="80px">ลำดับ</th>
								<th>ชื่อฌาปนกิจสงเคราะห์</th>
								<th>ชื่อย่อ</th>
								<th width="150px"></th>
							</tr>
						</thead>
						<tbody>
						<?php 
							$j = 1;
							if(!empty($rs)){
								foreach(@$rs as $key => $row){ 
						?>
								<tr> 
									<td><?php echo @$j++; ?></d>
									<td style="text-align:left;"><?php echo @$row['cremation_name']; ?></td> 
									<td><?php echo @$row['cremation_name_short']; ?></td> 
									<td>
										<a href="<?php echo base_url(PROJECTPATH.'/setting_cremation_data/cremation_data_detail?cremation_id='.@$row["cremation_id"]); ?>">จัดการ</a> |
										<a style="cursor:pointer;" onclick="edit_type('<?php echo @$row['cremation_id']; ?>','<?php echo @$row['cremation_name']; ?>','<?php echo @$row['cremation_name_short']; ?>','0');">แก้ไข</a> |
										<span class="text-del del"  onclick="del_coop_data('<?php echo @$row['cremation_id'] ?>')">ลบ</span>
									</td> 
								</tr>
						<?php 
								}
							} 
						?>
						</tbody>
					</table> 					
				</form>					
				</div>				
			</div>
		</div>
	</div>
</div>
<script>
function add_type(){
	$('#cremation_id').val('');
	$('#cremation_name').val('');
	$('#cremation_name_short').val('');
	$('#cremation_data_modal').modal('show');
}
function save_type(){
	$('#form1').submit();
}
function edit_type(cremation_id,cremation_name,cremation_name_short, open_modal){
	$('#cremation_id').val(cremation_id);
	$('#cremation_name').val(cremation_name);
	$('#cremation_name_short').val(cremation_name_short);
	if(open_modal == '1'){
		$('#cremation_data_modal').modal('show');
	}
}
function del_coop_data(cremation_id){	
	swal({
        title: "ท่านต้องการลบข้อมูลใช่หรือไม่",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'ลบ',
        cancelButtonText: "ยกเลิก",
        closeOnConfirm: false,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {						
			$.ajax({
				url: base_url+'setting_cremation_data/check_cremation_data_detail',
				method: 'POST',
				data: {
					'cremation_id': cremation_id
				},
				success: function(msg){
				   //console.log(msg); return false;
					if(msg == 'success'){	
						  document.location.href = base_url+'setting_cremation_data/del_cremation_data?cremation_id='+cremation_id;
					}else{
						swal("ไม่สามารถลบข้อมูลได้ \nเนื่องจากข้อมูลได้รับการตั้งค่าแล้ว");
					}
				}
			});
        } else {
			
        }
    });
}
</script>  