<div class="layout-content">
    <div class="layout-content-body">
<?php
$act = @$_GET['act'];
$id = @$_GET['id'];
?>   

<?php if (@$act != "add") { ?>
<h1 style="margin-bottom: 0">ตำแหน่ง</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
<?php $this->load->view('breadcrumb'); ?>
</div>
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
<a class="link-line-none" href="?act=add">
<button class="btn btn-primary btn-lg bt-add" type="button">
<span class="icon icon-plus-circle"></span>
เพิ่มรายการ
</button>
</a>
</div>
</div>
<?php } ?>

<?php if (@$act != "add") { ?>
	<div class="row gutter-xs">
				<div class="col-xs-12 col-md-12">
	              <div class="panel panel-body">
	                
					<div class="bs-example" data-example-id="striped-table">
					 <table class="table table-striped"> 
						 <thead> 
						 	  <tr>
								<th>#</th>
								<th>ตำแหน่ง</th>
								<th></th> 
							  </tr> 
						 </thead>

					        <tbody>

                <?php  
					if(!empty($rs)){
						foreach(@$rs as $key => $row){ 
				?>
					        <tr> 
					        <th scope="row"><?php echo @$i++; ?></th>
							<td><?php echo @$row['position_name']; ?></td> 
							<td>
							  <a href="?act=add&id=<?php echo @$row["position_id"] ?>">แก้ไข</a> |
							   <span class="text-del del"  onclick="del_coop_member_data('<?php echo @$row['position_id'] ?>')">ลบ</span>
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
<?php }else{ ?>

			<div class="col-md-6 col-md-offset-3">

				<h1 class="text-center m-t-1 m-b-2"> <?php echo  (!empty($id)) ? "แก้ไขรายการ" : "เพิ่มรายการ" ; ?></h1>

			<form id='form_save' data-toggle="validator" novalidate="novalidate" action="" method="post">
				<input name="id"  type="hidden" value="<?php echo @$id; ?>">
					
				<div class="form-group">
                    <label class="col-sm-3 control-label" for="form-control-2">ชื่อตำแหน่ง</label>
                    <div class="col-sm-9">
                      <input id="position_name" name="position_name" class="form-control m-b-1" type="text" value="<?php echo @$row['position_name']; ?>" required>
                    </div>
                  </div>
                  
                  <div class="form-group text-center">
                    <button type="button"  onclick="check_form()" class="btn btn-primary min-width-100">ตกลง</button>
                    <a href="?"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
                  </div>

                  </form>

			</div>


<?php } ?>
	</div>
</div>
<script>
function del_coop_member_data(id){	
	swal({
		title: "ท่านต้องการลบข้อมูลนี้ใช่หรือไม่ ! ",
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
				url: base_url+'/setting_member_data/del_coop_member_data',
				method: 'POST',
				data: {
					'table': 'coop_mem_position',
					'id': id,
					'field': 'position_id'
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_member_data/coop_mem_position';
					}else{

					}
				}
			});
		} else {
			
		}
	});
	
}

function check_form(){
	var text_alert = '';
	if($.trim($('#position_name').val())== ''){
		text_alert += ' - ชื่อตำแหน่ง\n';
	}
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form_save').submit();
	}
}
</script>