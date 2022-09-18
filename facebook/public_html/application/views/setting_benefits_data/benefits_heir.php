<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.cke_contents{
				height: 500px !important;
			}
		</style>
		<?php
		$act = @$_GET['act'];
		$id = @$_GET['id'];
		?>

		<?php if (@$act != "add") { ?>
		<h1 style="margin-bottom: 0">สวัสดิการรับขวัญทายาทใหม่</h1>
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
							<th class="text-center" style="width: 80px;">ลำดับ</th>
							<th>วันที่เพิ่ม</th>
							<th>วันที่มีผล</th>
							<th style="width: 150px;"></th> 
						  </tr> 
					 </thead>
					  <tbody>
			   <?php  
				if(!empty($rs)){
					foreach(@$rs as $key => $row){ ?>
						<tr> 
							<td class="text-center"><?php echo @$i++; ?></d>
							<td><?php echo $this->center_function->ConvertToThaiDate(@$row['createdatetime']); ?></td> 
							<td><?php echo $this->center_function->ConvertToThaiDate(@$row['start_date']); ?></td> 
							<td>
								<a href="?act=add&id=<?php echo @$row["benefits_heir_id"] ?>">ดูรายละเอียด</a> | 
								<span class="text-del del"  onclick="del_coop_data('<?php echo @$row['benefits_heir_id'] ?>')">ลบ</span>
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
			  <?php echo @$paging; ?>
			</div>
		</div>
<?php }else{ ?>

		<div class="col-md-12">
		<h1 class="text-center m-t-1 m-b-1"><?php echo  (!empty($id)) ? "แก้ไขสวัสดิการรับขวัญทายาทใหม่" : "เพิ่มสวัสดิการรับขวัญทายาทใหม่" ; ?></h1>
			<form id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_benefits_data/benefits_heir_save'); ?>" method="post">	
				<?php if (!empty($id)) { ?>
				<input name="type_add"  type="hidden" value="edit" required>
				<input name="id"  type="hidden" value="<?php echo $id; ?>" required>
				<?php }else{ ?>
				<input name="type_add"  type="hidden" value="add" required>
				<?php } ?>
				
				<div class="row">
					<label class="col-sm-2 control-label text-right" for="benefits_heir_detail">รายละเอียด</label>
					<div class="col-sm-9">
						<div class="form-group">
							<textarea id="benefits_heir_detail" name="benefits_heir_detail" required  title="กรุณากรอก รายละเอียด"><?php echo @$row['benefits_heir_detail']; ?></textarea>
						</div>
					</div>
				</div>
				
				<div class="row">
                   <label class="col-sm-2 control-label text-right" for="start_date">มีผลวันที่</label>
                    <div class="col-sm-2">
						<div class="form-group">
						  <input id="start_date" name="start_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(empty($row['start_date']) ? '' : @$row['start_date']); ?>" data-date-language="th-th" required  title="กรุณาเลือก มีผลวันที่">
						  <span class="icon icon-calendar input-icon m-f-1"></span>
						</div>
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
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/ckeditor/ckeditor.js',
    'type' => 'text/javascript'
);
echo script_tag($link);

$link = array(
    'src' => PROJECTJSPATH.'assets/ckeditor/adapters/jquery.js',
    'type' => 'text/javascript'
);
echo script_tag($link);

$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_benefits_heir.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
<script>
	$(document).ready(function() {
		
		if($("#benefits_heir_detail").length) {
			$("#benefits_heir_detail").ckeditor({ height : 146 , customConfig : '<?php echo PROJECTPATH; ?>/assets/ckeditor/config-admin.js'   });
		}
	});	
</script>   