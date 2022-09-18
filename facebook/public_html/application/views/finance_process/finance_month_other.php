<div class="layout-content">
    <div class="layout-content-body">
<style>
  .border1 { border: solid 1px #ccc; padding: 0 15px; }
  .mem_pic { margin-top: -1em;float: right; width: 150px; }
  .mem_pic img { width: 100%; border: solid 1px #ccc; }
  .mem_pic button { display: block; width: 100%; }
  .modal-backdrop.in{
    opacity: 0;
  }
  .modal-backdrop {
    position: relative;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1040;
    background-color: #000;
  }
  .font-normal{
	font-weight:normal;
  }
  .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
    border: 1px solid #fff;
  }
  th {
      text-align: center;
  }
  
  .modal-dialog-search {
		width: 700px;
	}
</style>

<h1 style="margin-bottom: 0"> เรียกเก็บเพิ่มเติม </h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0" id="breadcrumb">
		<?php $this->load->view('breadcrumb'); ?>
</div>
</div>
	<div class="panel panel-body col-xs-12 col-sm-12 col-md-12 col-lg-12 " >
		<form action="<?php echo base_url(PROJECTPATH.'/finance_process/finance_month_other_save'); ?>" method="POST" id="form2">
		<div class="row m-t-1">
			<div class="g24-col-sm-24">
				<div class="form-group">
					<div class=" g24-col-sm-24">			
							<label class="g24-col-sm-3 control-label font-normal" for="form-control-2">ปี</label>			
							<div class="g24-col-sm-6 m-b-1">
								<select id="year_select" name="year" class="form-control" onchange="change_data()">
								<?php for($i=$year;$i<=($year+5);$i++){ ?>
									<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
								<?php } ?>
								</select>
							</div>
							
							<label class="g24-col-sm-3 control-label font-normal" for="form-control-2">เดือน</label>
							<div class="g24-col-sm-4">
								<select id="month_select" name="month" class="form-control" onchange="change_data()">
								<?php foreach($month_arr as $key => $value){ ?>
									<option value="<?php echo $key; ?>" <?php echo $month==$key?'selected':''; ?>><?php echo $value; ?></option>
								<?php } ?>
								</select>
							</div>					
					</div>
				</div>
				<div class="form-group">
					<div class=" g24-col-sm-24">
					<label class="g24-col-sm-3 control-label font-normal" for="form-control-2">รายการหักเพิ่มเติม</label>
						<div class="g24-col-sm-6">
							<select id="deduct_id" class="form-control m-b-1" name="deduct_id" onchange="change_deduct_id()">
								<option value="">เลือกรายการ</option> 
								<?php
								foreach($coop_deduct as $key => $value){
								?>
									<option 
										value="<?php echo $value['deduct_id']; ?>" 
										deduct_type="<?php echo $value['deduct_type']; ?>" 
										deduct_format="<?php echo $value['deduct_format']?>"
										deduct_code="<?php echo $value['deduct_code']?>"
									>
										<?php echo $value['deduct_detail']; ?>
									</option> 
								<?php } ?>
							</select>
						</div>	
					</div>						
				</div>
				<div class="form-group" id="btn_space" style="display:none;">					
					<div class=" g24-col-sm-12">
						<button type="button" onclick="add_row()" class="btn btn-primary min-width-100">
						<span class="icon icon-plus"></span>
						เพิ่มสมาชิก					
						</button>
					</div>	
					<div class=" g24-col-sm-12" style="text-align:right;">
						<button type="button" onclick="check_form()" class="btn btn-primary min-width-100">
						<span class="icon icon-save"></span>
						บันทึก					
						</button>
					</div>					
				</div>
			</div>
		</div>
		<div class="bs-example" data-example-id="striped-table">
			<table class="table table-bordered table-striped">	
				<thead> 
					<tr class="bg-primary">
						<th class = "font-normal" style="width: 5%">ลำดับ</th>
						<th class = "font-normal" style="width: 15%">รหัสสมาชิก</th>
						<th class = "font-normal" style="width: 25%;">ชื่อ-สกุล</th> 
						<th class = "font-normal" style="width: 20%;">เลขที่สัญญา/เลขที่บัญชี</th> 
						<th class = "font-normal" style="width: 10%;">จำนวนเงิน</th> 
						<th class = "font-normal" style="width: 5%;"></th> 
					</tr> 
				</thead>
				<tbody id="table_data">
					<tr id="value_null" style=<?php echo $value_null_display; ?>>
						<td colspan='6' align='center'> ยังไม่มีรายการใดๆ </td>
					</tr>
				</tbody>
			</table>
			
		</div>
			<div class="row m-t-1 table_footer" style="display:none;">	
				<center>
					<button class="btn btn-primary" type="button" id="save" style="width:auto;" onclick="submit_form();">
						<span class="icon icon-print"></span>
						บันทึก				
					</button>
				</center>
			</div>
		</form>
		</div>
	</div>
</div>

<div class="modal fade" id="search_member_modal" role="dialog"> 
    <div class="modal-dialog modal-dialog-search">
      <div class="modal-content">
        <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">ข้อมูลสมาชิก</h4>
        </div>
        <div class="modal-body">
       		<div class="input-with-icon">
				<div class="row">
					<div class="col">
						<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
						<div class="col-sm-4">
							<div class="form-group">
								<select id="member_search_list" name="member_search_list" class="form-control m-b-1">
									<option value="">เลือกรูปแบบค้นหา</option>
									<option value="member_id">รหัสสมาชิก</option>
									<option value="id_card">หมายเลขบัตรประชาชน</option>
									<option value="firstname_th">ชื่อสมาชิก</option>
									<option value="lastname_th">นามสกุล</option>
								</select>
							</div>
						</div>
						<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
						<div class="col-sm-4">
							<div class="form-group">
								<div class="input-group">
								<input id="member_search_text" name="member_search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>">
								<span class="input-group-btn">
									<button type="button" id="member_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
								</span>	
								</div>
							</div>
						</div>
						<input id="data_row" name="data_row" class="form-control m-b-1" type="hidden" value="">		
					</div>
				</div>
			</div>

			<div class="bs-example" data-example-id="striped-table">
				 <table class="table table-striped">
					<tbody id="result_member_search">
					</tbody>
				</table>
			</div>
        </div>
        <div class="modal-footer">
			<input type="hidden" id="input_id">
			<button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
        </div>
      </div>
    </div>
</div>

<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/finance_month_other.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
