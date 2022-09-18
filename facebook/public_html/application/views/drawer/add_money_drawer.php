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
<link rel="stylesheet" href="<?=base_url('assets/css/select2.min.css')?>">
<script src="<?=base_url('assets/js/select2.min.js')?>"></script>
<h1 style="margin-bottom: 0"> เพิ่มเงินลิ้นซักรายวัน</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0" id="breadcrumb">
		<?php $this->load->view('breadcrumb'); ?>
</div>
</div>
	<div class="panel panel-body col-xs-12 col-sm-12 col-md-12 col-lg-12 " >
        <div class="row m-t-1">
			<div class="g24-col-sm-24">
				<div class="form-group">
					<div class=" g24-col-sm-24">			
							<label class="g24-col-sm-3 control-label font-normal" for="form-control-2">วันที่</label>			
							<div class="g24-col-sm-4 m-b-1">
								<input type="text" class="form-control"  name="date" id="date" value="<?=date('d/m/Y') ?>" disabled readonly>
							</div>
											
					</div>
				</div>
            </div>
        </div>
		<div class="bs-example" data-example-id="striped-table">
			<table class="table table-bordered table-striped">	
				<thead> 
					<tr class="bg-primary">
						<th class = "font-normal" style="width: 15%">รหัสสมาชิก</th>
						<th class = "font-normal" style="width: 25%;">ชื่อ-สกุล</th> 
						<th class = "font-normal" style="width: 10%;">จำนวนเงิน</th> 
						<th class = "font-normal" style="width: 5%;"></th> 
					</tr> 
				</thead>
				<tbody id="table_data">
					<?php foreach ($data as $key => $value) { ?>
                        <tr>
                            <td><?=$value['employee_id']?></td>
                            <td><?=$value['user_name']?></td>
                            <td><input class='form-control amount' name="amount" id="amount" value="<?=$value['budget']?>" <?php echo (!empty($value['budget'])) ? 'disabled' : '' ; ?>></td>
                            <td>
                                <center>
                                    <?php if (empty($value['budget'])) { ?>
                                        <button class="btn btn-primary add-money" data-id="<?=$value['user_id']?>">เพิ่มเงิน</button>
                                    <?php } ?>
                                </center>
                            </td>
                        </tr>
                    <?php } ?>
					
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
		</div>
	</div>
</div>

<script>
function validateNumber(event) {
    var key = window.event ? event.keyCode : event.which;
    if (event.keyCode === 8 || event.keyCode === 46) {
        return true;
    } else if ( key < 48 || key > 57 ) {
        return false;
    } else {
        return true;
    }
};
$('.amount').keypress(validateNumber);

$(document).on('click' , '.add-money' ,function(){
    var dataID = $(this).attr('data-id'),
        amount = $(this).parents('tr').find('.amount').val(),
        checkSubmit = true
    if (amount == "") {
        checkSubmit = false
        swal('กรุณากรอกจำนวนเงิน','','warning');
        
    }
    if(checkSubmit) {
        $.ajax({
            type: 'POST',
            url: base_url + 'drawer/save_add_money',
            dataType: "json",
            data: {
                    'amount': amount,
                    'dataID' : dataID,
            },
            success: function (msg) {
              
                
                if (msg.message) {
                    swal({
                        title: "แจ้งเตือน",
                        text: 'ทำรายการสำเร็จ',
                        type: "success",
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: 'ตกลง',
                        closeOnConfirm: false,
                    },
                    function(isConfirm) {
                        if (isConfirm) {			
                            window.location.reload();
                        } else {
                            
                        }
                    });	
                    $(this).attr('disabled' ,  false)
                } else {
                    swal(msg.message,'','warning');
                    

                }
                
            }
        });
    } 
    
    
})
</script>