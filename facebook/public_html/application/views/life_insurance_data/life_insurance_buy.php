<div class="layout-content">
    <div class="layout-content-body">
<style>
	.modal-header-alert {
		padding:9px 15px;
		border:1px solid #FF0033;
		background-color: #FF0033;
		color: #fff;
		-webkit-border-top-left-radius: 5px;
		-webkit-border-top-right-radius: 5px;
		-moz-border-radius-topleft: 5px;
		-moz-border-radius-topright: 5px;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
	}
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
	
	.text-p{
		font-family: upbean;
	}
</style>
<h1 style="margin-bottom: 0">ซื้อประกันชีวิต</h1>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php 
		if(@$member_id != ''){
		?>
		<a>
			<button type="button" class="btn btn-primary btn-lg bt-add" id="permanent" onclick="refrain_temporary();">ซื้อประกันชีวิตเพิ่ม</button>
		</a>		
		<?php 
		}
		?>
	</div>
</div>
<div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                <?php $this->load->view('search_member_new'); ?>
				<div class="" style="padding-top:0;">
		
				<div class="g24-col-sm-24 m-t-1">
				  <div class="bs-example" data-example-id="striped-table">
					 <table class="table table-bordered table-striped table-center">
					 <thead> 
						<tr class="bg-primary">
							<th>ประจำปี</th>
							<th>วันที่ซื้อ</th>
							<th>เลขที่สัญญา</th>
							<th>ทุนประกัน</th>
							<th>เบี้ยประกัน</th>
							<th>ประเภทการซื้อ</th>
							<th>เลขที่ใบเสร็จ</th>
						</tr> 
					 </thead>
						<tbody id="table_first">
							<?php
							  $i = 0;
							  foreach($data as $key => $row){ 
							?>
							<tr>
								<td><?php echo @$row['insurance_year'];?></td>
								<td><?php echo @$this->center_function->ConvertToThaiDate(@$row['insurance_date'],1,0); ?></td>
								<td><?php echo @$row['contract_number'];?></td>						
								<td class="text-right"><?php echo number_format(@$row['insurance_amount'],2); ?></td>
								<td class="text-right"><?php echo number_format(@$row['insurance_premium'],2); ?></td>
								<td><?php echo @$row['insurance_type_name'];?></td>
								<td><a href="<?php echo base_url(PROJECTPATH.'/admin/receipt_form_pdf/'.@$row['receipt_id']); ?>" target="_blank"><?php echo @$row['receipt_id'];?></a></td>
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

	</div>
</div>

<div class="modal fade" id="refrainTemporaryModal"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account">
      <div class="modal-content">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h2 class="modal-title">ซื้อประกันชีวิตเพิ่ม</h2>
        </div>
        <div class="modal-body center">
			<form action="<?php echo base_url(PROJECTPATH.'/life_insurance_data/life_insurance_buy_save'); ?>" method="POST" id="form_1">
				<input class="form-control" type="hidden" name="member_id"  id="member_id" value="">
				<div class="form-group g24-col-sm-24">							
					<label class="g24-col-sm-4 control-label right">ประจำปี </label>
					<div class="g24-col-sm-6">
						<select id="insurance_year" name="insurance_year" class="form-control">
							<?php for($i=((date('Y')+543)); $i<=((date('Y')+543)+5); $i++){ ?>
								<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="g24-col-sm-2 control-label"></label>
					
					<label class="g24-col-sm-4 control-label right"> วันที่ซื้อ </label>
					<div class="g24-col-sm-6">
						<div class="input-with-icon">
							<div class="form-group">
								<input id="insurance_date" name="insurance_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
								<span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>
					</div>
					<label class="g24-col-sm-2 control-label"></label>
					
					<label class="g24-col-sm-4 control-label right"> ทุนประกัน </label>
					<div class="g24-col-sm-6">
						<input class="form-control" type="text" name="insurance_amount"  id="insurance_amount" onkeyup="format_the_number_decimal(this);check_life_insurance();">
					</div>
					<label class="g24-col-sm-2 control-label"></label>
					
					<label class="g24-col-sm-4 control-label right"> เบี้ยประกัน </label>
					<div class="g24-col-sm-6">
						<input class="form-control" type="text" name="insurance_premium"  id="insurance_premium" onkeyup="format_the_number_decimal(this);" readonly>
					</div>
					<label class="g24-col-sm-2 control-label"></label>
				</div>
			</form>
			<div class="form-group g24-col-sm-24 text-center">
			<br>
			</div>
		</div>
		<div class="modal-footer center" style="border-top:0;">
			<div class="form-group g24-col-sm-24 text-center">
				<button class="btn btn-info" onclick="check_save();">บันทึก</button>
			</div>
		</div>
      </div>
    </div>
</div>

<?php $this->load->view('search_member_new_modal'); ?>
<script>
	$( document ).ready(function() {	
		$(".mydate").datepicker({
			prevText : "ก่อนหน้า",
			nextText: "ถัดไป",
			currentText: "Today",
			changeMonth: true,
			changeYear: true,
			isBuddhist: true,
			monthNamesShort: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
			dayNamesMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
			constrainInput: true,
			dateFormat: "dd/mm/yy",
			yearRange: "c-50:c+10",
			autoclose: true,
		});		
	});			


	function refrain_temporary(){	
		$('#refrainTemporaryModal').modal('show');			
		var member_id = $('.member_id').val();
		$('#member_id').val(member_id);
	}
	
	function check_save(){	
		var alert_text = '';
		if($('#insurance_year').val()==''){
			alert_text += '- ประจำปี\n';
		}
		if($('#insurance_date').val()==''){
			alert_text += '- วันที่ซื้อ\n';
		}
		if($('#insurance_amount').val()==''){
			alert_text += '- ทุนประกัน\n';
		}
		if($('#insurance_premium').val()==''){
			alert_text += '- เบี้ยประกัน\n';
		}
		
		if(alert_text!=''){
			swal('กรุณากรอกข้อมูลต่อไปนี้' , alert_text , 'warning');
		}else{
			$('#form_1').submit();
		}
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
	
	function removeCommas(str) {
		return(str.replace(/,/g,''));
	}
	function addCommas(x){
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	
	//ระบบประกันชีวิต
	function check_life_insurance(excel=''){
			var member_id = $('.member_id').val();
			var insurance_date = $('#insurance_date').val();
			var insurance_amount = removeCommas($('#insurance_amount').val());

			$.post(base_url+"/life_insurance_data/get_life_insurance", 
			{	
				member_id:member_id,
				insurance_date:insurance_date,
				insurance_amount:insurance_amount
			}
			, function(result){
				obj = JSON.parse(result);
				$('#insurance_premium').val(obj.deduct_insurance);
				
				//console.log(obj);				
			});
	}
</script>