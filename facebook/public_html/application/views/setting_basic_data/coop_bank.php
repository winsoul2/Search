<div class="layout-content">
    <div class="layout-content-body">
<?php
$act = @$_GET['act'];
$id = @$_GET['id'];
?>

<?php if (@$act != "add") { ?>
<h1 style="margin-bottom: 0">ธนาคาร</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
<?php $this->load->view('breadcrumb'); ?>
</div>
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
<a class="link-line-none" href="?act=add">
<button class="btn btn-primary btn-lg bt-add" type="button">
<span class="icon icon-plus-circle"></span>
เพิ่มธนาคาร
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
							 	<th>รหัสธนาคาร</th>
							   	<th>ชื่อธนาคาร</th>
							    <th>ตัวย่อ</th>
							    <th class="text-center">จำนวนสาขา</th> 
							    <th></th> 
							  </tr> 
						 </thead>

					      <tbody>
				   <?php  
					if(!empty($rs)){
						foreach(@$rs as $key => $row){ ?>
					        <tr> 
					        <th scope="row"><?php echo @$row['bank_id']; ?></th>
					        <td><?php echo @$row['bank_name']; ?></td> 
					        <td><?php echo @$row['bank_code']; ?></td> 
					        <td class="text-center"><?php echo number_format(@$row['total']); ?></td> 
					        <td>
								<a class="fancybox fancybox.iframe" href="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_bank_branch?bank_id='.@$row["bank_id"].'&bank_name='.@$row["bank_name"]) ?>">จัดการสาขา</a> | 
								<a href="?act=add&id=<?php echo @$row["bank_id"] ?>">แก้ไข</a> | 
								<span class="text-del del"  onclick="del_coop_basic_data('<?php echo @$row['bank_id'] ?>')">ลบ</span>
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

			<div class="col-md-6 col-md-offset-3">

				<h1 class="text-center m-t-1 m-b-2"><?php echo  (!empty($id)) ? "แก้ไขธนาคาร" : "เพิ่มธนาคาร" ; ?></h1>

			<form id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_bank_save'); ?>" method="post">	
			<?php if (!empty($id)) { ?>
	       <input name="type_add"  type="hidden" value="edit" required>
	       <input name="id"  type="hidden" value="<?php echo $id; ?>" required>
	      <?php }else{ ?>
	       <input name="type_add"  type="hidden" value="add" required>
	      <?php } ?>

                  <div class="row">
                    <label class="col-sm-3 control-label" for="form-control-2">รหัสธนาคาร</label>
                    <div class="col-sm-9">
                      <input id="bank_id" name="bank_id" class="form-control m-b-1" type="text" value="<?php echo @$row['bank_id'] ?>" required maxlength="3">
                    </div>
                  </div>

                  <div class="row">
                    <label class="col-sm-3 control-label" for="form-control-2">ชื่อธนาคาร</label>
                    <div class="col-sm-9">
                      <input id="bank_name" name="bank_name" class="form-control m-b-1" type="text" value="<?php echo @$row['bank_name'] ?>" required>
                    </div>
                  </div>


                  <div class="row">
                   <label class="col-sm-3 control-label" for="form-control-2">ตัวย่อ</label>
                    <div class="col-sm-9">
                      <input id="bank_code" name="bank_code" class="form-control m-b-1" type="text" value="<?php echo @$row['bank_code'] ?>" required maxlength="10">
                    </div>
                  </div>

                  <div class="row text-center m-t-1">
                    <button type="button"  onclick="check_form()" class="btn btn-primary min-width-100">ตกลง</button>
                    <a href="?"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
                  </div>

                  </form>

			</div>


<?php } ?>

<div id="Del" tabindex="-1" role="dialog" class="modal fade">
<div class="modal-dialog modal-dialog-delete">
  <div class="modal-content">
    <div class="modal-header modal-header-delete">
      <h2 class="modal-title">ยืนยันการลบข้อมูล</h2>
    </div>
    <div class="modal-body">
    <form action="?" method="POST">
        <input type="hidden" name="do" value="del">
        <input type="hidden" name="id" value="" id="id">
        <div class="form-group">
          <p style="font-size:14px;"> ท่านต้องการลบข้อมูลนี้ใช่หรือไม่ ! <p>
        </div>
    </div>
    <div class="modal-footer center">
      <button class="btn btn-danger" type="submit"> คกลง </button>
      <button class="btn btn-default" data-dismiss="modal" type="button">ยกเลิก</button>
    </div>
    </form>
  </div>
</div>
</div>

	</div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_bank.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
    