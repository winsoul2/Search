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
		
		.modal-dialog {
			width: 500px;
		}
	</style>
	<?php
	$act = @$_GET['act'];
	$id  = @$_GET['id'];
	$detail_id  = @$_GET['detail_id'];
	?> 
<?php if (empty($act)) { ?>
		<h1 style="margin-bottom: 0">ประกันชีวิต</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">	
			   <button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_type();"><span class="icon icon-plus-circle"></span> เพิ่มปี</button> 
			</div>
		</div>	
		  <div class="row gutter-xs">
			  <div class="col-xs-12 col-md-12">
					<div class="panel panel-body">
						<div class="col-xs-1 col-md-1"></div> 
						<div class="bs-example col-xs-10 col-md-10" data-example-id="striped-table">
							<table class="table table-bordered table-striped table-center"> 
								<thead> 
									 <tr class="bg-primary">
										<th class = "font-normal" style="width: 30%">ประจำปี</th>
										<th class = "font-normal"> วันที่แก้ไข </th>
										<th class = "font-normal" style="width: 30%"> จัดการ </th>
									</tr> 
								</thead>
								<tbody>
						 <?php  
							if(!empty($rs)){
								foreach(@$rs as $key => $row){ 				?>
								<tr> 
								  <td class="text-center"><?php echo @$row['s_insurance_year']; ?></td> 
								  <td class="text-center"><?php echo @$row['updatetime']==''?'ไม่ระบุ':$this->center_function->ConvertToThaiDate(@$row['updatetime']); ?></td> 
								  <td class="text-center">
									  <a style="cursor:pointer;" onclick="edit_type('<?php echo @$row['s_insurance_id']; ?>','<?php echo @$row['s_insurance_year']; ?>','<?php echo @$row['s_insurance_life_premium']; ?>','<?php echo @$row['s_insurance_accident_premium']; ?>','<?php echo @$row['s_insurance_defective_premium']; ?>');">แก้ไข</a> |
									  <a href="#" onclick="del_coop_data('<?php echo @$row['s_insurance_id']; ?>')" class="text-del"> ลบ </a> 
								  </td> 
								</tr>
						<?php 
								}
							} 
						?>
								</tbody> 
							</table> 
						</div>
						<div class="col-xs-1 col-md-1"></div> 
				</div>
					<?php echo @$paging ?>
				 </div>
		  </div>

<?php } ?>
	</div>
</div>
<div id="insurance_type_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title"><span id="title_1">เพิ่มปี</span></h2>
			</div>
			<div class="modal-body">
				<div class="form-group" style="padding-bottom: 50px;">
				<form id='form1' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_life_insurance_data/coop_life_insurance_save'); ?>" method="post">	
					<input type="hidden" class="form-control" id="s_insurance_id" name="s_insurance_id" value="">
					<div class="row">
						<label class="col-sm-5 control-label text-right" for="s_insurance_year">ปี</label>
						<div class="col-sm-5">
							<div class="form-group">
								<select id="s_insurance_year" name="s_insurance_year" class="form-control m-b-1">
									<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
										<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<label class="col-sm-2 control-label">&nbsp;</label>
					</div>
					<div class="row">
						<label class="col-sm-5 control-label text-right" for="s_insurance_life_premium">อัตราเบี้ยประกันชีวิต</label>
						<div class="col-sm-5">
							<div class="form-group">
								<input id="s_insurance_life_premium" name="s_insurance_life_premium" class="form-control m-b-1" type="text" value="" required title="กรุณากรอก อัตราเบี้ยประกันชีวิต" onkeyup="format_the_number_decimal(this);">
							</div>
						</div>
						<label class="col-sm-2 control-label">&nbsp;</label>
					</div>
					<div class="row">
						<label class="col-sm-5 control-label text-right" for="s_insurance_accident_premium">อัตราเบี้ยประกันอุบัติเหตุ</label>
						<div class="col-sm-5">
							<div class="form-group">
								<input id="s_insurance_accident_premium" name="s_insurance_accident_premium" class="form-control m-b-1" type="text" value="" required title="กรุณากรอก อัตราเบี้ยประกันอุบัติเหตุ" onkeyup="format_the_number_decimal(this);">
							</div>
						</div>
						<label class="col-sm-2 control-label">&nbsp;</label>
					</div>
					<div class="row">
						<label class="col-sm-5 control-label text-right" for="s_insurance_defective_premium">อัตราเบี้ยประกันทุพลลภาพ</label>
						<div class="col-sm-5">
							<div class="form-group">
								<input id="s_insurance_defective_premium" name="s_insurance_defective_premium" class="form-control m-b-1" type="text" value="" required title="กรุณากรอก อัตราเบี้ยประกันทุพลลภาพ" onkeyup="format_the_number_decimal(this);">
							</div>
						</div>
						<label class="col-sm-2 control-label">&nbsp;</label>
					</div>
					
					<div class="form-group">
						<div class="col-sm-12" style="text-align:center;margin-top:20px;margin-bottom:20px;">
							<button type="button" class="btn btn-primary" onclick="save_type()">บันทึก</button>
						</div>
					</div>				
				</form>					
				</div>				
			</div>
		</div>
	</div>
</div>

<script>
var base_url = $('#base_url').attr('class');	

function add_type(){
	$('#insurance_type_modal').modal('show');
}

function save_type(){
	var s_insurance_year = $('#s_insurance_year').val();
	var s_insurance_id = $('#s_insurance_id').val();
	$.ajax({
		url: base_url+'/setting_life_insurance_data/check_year_life_insurance',
		method: 'POST',
		data: {
			's_insurance_year': s_insurance_year,
			's_insurance_id': s_insurance_id
		},
		success: function(msg){
		    //console.log(msg);
			if(msg != 0){
				$('#form1').submit();
			}else{
				swal("ปี "+s_insurance_year+" กำหนดค่าในระบบแล้ว");
			}
		}
	});
}

function edit_type(s_insurance_id,s_insurance_year,s_insurance_life_premium,s_insurance_accident_premium,s_insurance_defective_premium){
	$('#s_insurance_id').val(s_insurance_id);
	$('#s_insurance_year').val(s_insurance_year);
	$('#s_insurance_life_premium').val(s_insurance_life_premium);
	$('#s_insurance_accident_premium').val(s_insurance_accident_premium);
	$('#s_insurance_defective_premium').val(s_insurance_defective_premium);
	$('#insurance_type_modal').modal('show');
}

function del_coop_data(id){	
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
				url: base_url+'/setting_life_insurance_data/del_coop_data',
				method: 'POST',
				data: {
					'table': 'coop_setting_life_insurance',
					'id': id,
					'field': 's_insurance_id'
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_life_insurance_data/coop_life_insurance';
					}else{
						swal("ไม่สามารถลบข้อมูลนี้ได้");
					}
				}
			});
        } else {
			swal("ไม่สามารถลบข้อมูลนี้ได้");
        }
    });
}

function format_the_number_decimal(ele){
	var value = $('#'+ele.id).val();
	value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
	var num = value.split(".");
	var decimal = '';	
	var num_decimal = '';	
	if(typeof num[1] !== 'undefined'){
		if(num[1].length > 2){
			num_decimal = num[1].substring(0, 2);
		}else{
			num_decimal =  num[1];
		}
		decimal =  "."+num_decimal;
		
	}
	
	if(value!=''){
		if(value == 'NaN'){
			$('#'+ele.id).val('');
		}else{		
			value = (num[0] == '')?0:parseInt(num[0]);
			value = value.toLocaleString()+decimal;
			$('#'+ele.id).val(value);
		}			
	}else{
		$('#'+ele.id).val('');
	}
}

$('#insurance_type_modal').on('hidden.bs.modal', function() {
	$( "div" ).removeClass('has-error'); 
});
</script>