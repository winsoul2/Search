<div class="layout-content">
    <div class="layout-content-body">
<?php
$act = @$_GET['act'];
$id  = @$_GET['id'];

?> 
	<style>
	  input[type=number]::-webkit-inner-spin-button, 
	  input[type=number]::-webkit-outer-spin-button { 
		-webkit-appearance: none; 
		margin: 0; 
	  }
	  th, td {
		  text-align: center;
	  }
	  .modal-dialog-delete {
			margin:0 auto;
			width: 350px;
			margin-top: 8%;
		}
	  .modal-header-delete {
			padding:9px 15px;
			border:1px solid #d50000;
			background-color: #d50000;
			color: #fff;
			-webkit-border-top-left-radius: 5px;
			-webkit-border-top-right-radius: 5px;
			-moz-border-radius-topleft: 5px;
			-moz-border-radius-topright: 5px;
			border-top-left-radius: 5px;
			border-top-right-radius: 5px;
		}
	</style>
	<?php if ($act != "add") { ?>
		<h1 style="margin-bottom: 0">รายการซื้อ</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		   <!-- <h4 style="font-size:20.5px;margin-top:0px;text-align:right;" >มูลค่าหุ้นปัจจุบัน 50 บาท </h4> -->
		   <a class="link-line-none" href="?act=add">
		   <button class="btn btn-primary btn-lg bt-add" type="button">
		   <span class="icon icon-plus-circle"></span>
		   เพิ่มรายการ
		   </button>
		   </a>
		</div>
		</div>
	<?php } ?>

<?php if ($act != "add") { ?>

		  <div class="row gutter-xs">
			  <div class="col-xs-12 col-md-12">
					  <div class="panel panel-body">
				<div class="bs-example" data-example-id="striped-table">
				<table class="table table-striped"> 
					<thead> 
						 <tr>
							<th class = "font-normal" width="5%">#</th>
							<th class = "font-normal" style="width: 40%"> รายการ </th>
							<th class = "font-normal" style="width: 20%"> จำนวน </th>
							<th class = "font-normal" style="width: 15%"> ผังบัญชี </th>
							<th class = "font-normal" style="width: 15%"> จัดการ </th>
						</tr> 
					</thead>
					<tbody>
				 <?php  
					if(!empty($rs)){
						foreach(@$rs as $key => $row){ 
				?>
						<tr> 
						  <td scope="row"><?php echo $i++; ?></td>
						  <td style="text-align:left"><?php echo @$row['account_list']; ?></td> 
						  <td><?php echo number_format(@$row['amount']); ?></td> 
						  <td><?php echo @$row['account_chart_id']; ?></td> 
						  <td>
						  <?php if(@$row['is_fix']=='0'){ ?>
						  <a href="?act=add&id=<?php echo @$row["account_id"] ?>">แก้ไข</a> |
						  <a href="#" onclick="del_coop_account_data('<?php echo @$row['account_id']; ?>')" class="text-del"> ลบ </a> 
						  <?php } ?>
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

<?php } else { ?>

		<div class="col-md-6 col-md-offset-3">
			<h1 class="text-center m-t-1 m-b-1"> <?php echo(!empty($id)) ? "แก้ไขรายการซื้อ" : "เพิ่มรายการซื้อ " ; ?></h1>

			<form id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_account_data/coop_account_buy_save'); ?>" method="post">	
			<?php if (!empty($id)) { ?>
				<input name="type_add"  type="hidden" value="edit" required>
				<input name="id"  type="hidden" value="<?php echo $id; ?>" required>
			<?php }else{ ?>
				<input name="type_add"  type="hidden" value="add" required>
			<?php } ?>
			<input type="hidden" name="return_url" value="<?php echo @$_GET['return_url']; ?>">
			<div class="form-group">
				<label class="col-sm-3 control-label" >รหัสผังบัญชี</label>
				<div class="col-sm-9">
					<select class="form-control m-b-1" name="account_chart_id" id="account_chart_id">
						<option value="">เลือกผังบัญชี</option>
						<?php  
							if(!empty($rs_account_chart)){
								foreach(@$rs_account_chart as $key => $row_account_chart){ 
						?>	
							<option value="<?php echo @$row_account_chart['account_chart_id']; ?>" <?php echo @$row['account_chart_id']==@$row_account_chart['account_chart_id']?'selected':''; ?>><?php echo @$row_account_chart['account_chart_id']." : ".@$row_account_chart['account_chart']; ?></option>	
						<?php 
								}
							} 
						?>
					</select>
				</div>
				
				<label class="col-sm-3 control-label" >รายการชำระเงิน</label>
				<div class="col-sm-9">
					<input  name="account_list" id="account_list" class="form-control m-b-1" type="text" value="<?php echo @$row['account_list']; ?>" required>
				</div>

				<label class="col-sm-3 control-label" >จำนวนเงิน</label>
				<div class="col-sm-9">
					<input name="amount" id="amount" class="form-control m-b-1" type="number" value="<?php echo @$row['amount']; ?>" required>
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
    'src' => PROJECTJSPATH.'assets/js/coop_account_buy.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
