<style>
	.datepick {
		text-align: center;
		width: 150px !important;
	}
	#table_process_return {
		margin-top: 30px;
	}
	#table_process_return thead tr th {
		text-align: center !important;
	}
	#table_process_return tbody tr td {
		text-align: center;
	}
	#table_process_return tbody tr td:nth-child(4) {
		text-align: left;
	}
	#table_process_return tbody tr td:nth-child(7) {
		text-align: right;
	}
</style>
<div class="layout-content">
	<div class="layout-content-body">
		<h1 style="margin-bottom: 0"> ประมวลผลเงินคืน </h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0" id="breadcrumb">
					<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
		<div class="panel panel-body col-xs-12 col-sm-12 col-md-12 col-lg-12 " >

			<div class="row">
				<div class="col-xs-12 col-sm-12 col-sm-offset-0">
          <div class="text-right"><button type="button" class="btn btn-primary" id="btn-save-return">บันทึก</button></div>
					<br>
					<div class="col-xs-4 col-sm-4 col-sm-offset-0">
						<button type="button" class="btn" id="btn-previous">ก่อนหน้า</button>
					</div>
					<div class="col-xs-4 col-sm-4 col-sm-offset-0 text-center">
						<div class="col-md-4 text-right">
							<label for="">แสดงทีละ</label>
						</div>
						<div class="col-md-6">
							<select name="" id="limit" class="form-control">
								<option value="100" <?=(@$_GET['limit']=="100") ? "selected" : "" ?>>100 รายการ</option>
								<option value="200" <?=(@$_GET['limit']=="200") ? "selected" : "" ?>>200 รายการ</option>
								<option value="300" <?=(@$_GET['limit']=="300") ? "selected" : "" ?>>300 รายการ</option>
								<option value="400" <?=(@$_GET['limit']=="400") ? "selected" : "" ?>>400 รายการ</option>
								<option value="500" <?=(@$_GET['limit']=="500" || $_GET['limit']=="" ) ? "selected" : "" ?>>500 รายการ</option>
							</select>
						</div>
					</div>
					<div class="col-xs-4 col-sm-4 col-sm-offset-0 text-right">
						<button type="button" class="btn" id="btn-next">หน้าถัดไป</button>
					</div>

          <form id="frm-return-interest">
						<table class="table" id="table_process_return" data-type="<?php echo $_GET['type']; ?>" data-ds="<?php echo $_GET['ds']; ?>" data-de="<?php echo $_GET['de']; ?>" data-page="<?=(@$_GET['page']=="" ? "1" : @$_GET['page'])?>" data-limit="<?=(@$_GET['limit']=="" ? "100" : @$_GET['limit'] )?>">
              <thead>
                <tr>
                  <th width="30">
                    <label class="custom-control custom-control-primary custom-checkbox">
                      <input type="checkbox" id="check_all" value="1" class="custom-control-input" />
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-label"></span>
                    </label>
                  </th>
                  <th width="60">#</th>
                  <th width="100">รหัสสมาชิก</th>
                  <th>ชื่อสมาชิก</th>
				  <?php if(@$_GET['type'] == 5){?>
					   <th width="120">เลขที่ใบเสร็จ</th>	
					   <th colspan="7">รายละเอียด</th>
				  <?php }else{ ?>
					  <th width="120">เลขที่สัญญา</th>
					  <th width="60">อัตราดอกเบี้ย</th>
					  <th width="120">เลขที่ใบเสร็จ</th>
					  <th width="100">เงินคืนต้น</th>
					  <th width="100">เงินคืนดอก</th>
					  <th width="100">เก็บเพิ่ม</th>
					  <th width="150">สถานะ</th>
				  <?php } ?>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </form>
				</div>
			</div>

		</div>
	</div>
</div>
<?php
	$v = date('YmdHis');
	$link = [
		'src' => PROJECTJSPATH.'assets/js/process_return_edit.js?v='.$v,
		'type' => 'text/javascript'
	];
	echo script_tag($link);
?>