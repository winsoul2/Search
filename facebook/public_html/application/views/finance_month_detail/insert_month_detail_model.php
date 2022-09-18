<style>
@media (min-width: 768px) {
    .modal-dialog {
        width: 700px;
    }
}

</style>
<div class="modal fade" id="insert_model" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">ข้อมูลสมาชิก</h4>
        </div>
        <div class="modal-body">
       		<div class="input-with-icon">
              <div class="row">
                <label class="col-sm-3 control-label">ทะเบียนสมาชิก</label>
                <div class="col-sm-9">
                  <div class="form-group">
                    <input id="member_id" name="member_id" class="form-control" style="text-align:left;" type="text" value="<?=$_GET['member_id']?>" disabled>
                  </div>
                </div>	
              </div>
              <div class="row">
                <label class="col-sm-3 control-label">ประเภท </label>
                <div class="col-sm-9">
                  <div class="form-group">
                    <select id="search_type_list" name="search_type_list" class="form-control m-b-1" onchange="select_loan_type(<?=$profile_id?>)">
                        <option value="">กรุณาเลือกประเภท</option>
                        <option value="1">ชำระเงินกู้</option>
                        <option value="2">เงินฝาก</option>
                        <option value="3">หุ้น</option>
                        <option value="OTHER">อื่นๆ</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
              <span id="deduct_list">
              </span>
              </div>
              <div class="row">
                <label class="col-sm-3 control-label">จำนวนเงิน	</label>
                <div class="col-sm-8">
                  <div class="form-group">
                    <input id="pay_amount" name="pay_amount" class="form-control" style="text-align:left;" type="number">
                  </div>
                </div>	
                <div class="col-sm-1 control-label">บาท</div>
              </div>
              <div class="row">
                <label class="col-sm-3 control-label">จำนวนจ่ายจริง</label>
                <div class="col-sm-8">
                  <div class="form-group">
                    <input id="real_pay_amount" name="real_pay_amount" class="form-control" style="text-align:left;" type="number">
                  </div>
                </div>	
                <div class="col-sm-1 control-label">บาท</div>
            </div>
					</div>

			<div class="bs-example" data-example-id="striped-table">
					  <table class="table table-striped">
              <tbody id="result_member">
              </tbody>
					  </table>
					</div>

        </div>
        <div class="modal-footer">
          <button id="sudmit_insert_data" type="button" class="btn btn-primary min-width-100" style="display: none" onclick="insert_row('<?=$_GET['member_id']?>','<?php echo $profile_id;?>')">บันทึก</button>
          <button type="button" id="show_confirm_user" class="btn btn-primary min-width-100" onClick='show_confirm_user("sudmit_insert_data")'>บันทึก</button>
          <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
        </div>
      </div>
    </div>
  </div>

<script>
function insert_row(member_id,profile_id){
  var data = null;
  var member_id = document.getElementById("member_id").value;
  var search_type = document.getElementById("search_type_list").value;
  var pay_amount = document.getElementById("pay_amount").value;
  var real_pay_amount = document.getElementById("real_pay_amount").value;
  var loan_id = null;
  var search_deduct = null;
  var deposit_account_id = null;
  var user_id = get_user_id();
  console.log("search_type", search_type);
  if (search_type == 1){
    var loan_id = document.getElementById("search_loan_list").value;
    var search_deduct = document.getElementById("search_deduct").value;
    console.log ('insert_row search_type =>',search_type);
    if (search_type == '',pay_amount == '',real_pay_amount == '', loan_id == '', search_deduct ==''){
      swal('กรุณากรอกข้อมูลให้ครบ','','warning');
      return false;
    }
  }else if(search_type == 2){
    var deposit_account_id = document.getElementById("search_account_list").value;
    console.log ('insert_row account_id =>',deposit_account_id);
    if (search_type == '',pay_amount == '',real_pay_amount == '',deposit_account_id ==''){
      swal('กรุณากรอกข้อมูลให้ครบ','','warning');
      return false;
    }
  }else if(search_type == 3){
    if (search_type == '',pay_amount == '',real_pay_amount == ''){
      swal('กรุณากรอกข้อมูลให้ครบ','','warning');
      return false;
    }
  }else if(search_type == "OTHER" ){
    if (pay_amount == '',real_pay_amount == ''){
      swal('กรุณากรอกข้อมูลให้ครบ','','warning');
      return false;
    }
  }else{
    swal('กรุณากรอกข้อมูลให้ครบ','','warning');
    return false;
  }
	$.ajax({
        type: "POST",
        url: base_url+'finance_month_detail/update_finance_month_detail',
        data: {
			    data: data,
          member_id: member_id,
			    profile_id: profile_id,
          pay_amount: pay_amount,
          real_pay_amount: real_pay_amount,
          search_type: search_type,
          loan_id: loan_id,
          search_deduct: search_deduct,
          deposit_account_id: deposit_account_id,
          user_id: user_id,
			    form_target : 'insert'
        },
        success: function(msg) {
          console.log(msg);
          if (msg == 'true'){
            swal('เพิ่มข้อมูลเรียบร้อย','','success');
            location.reload();
          }else{
            swal('แจ้งเตือน',msg,'warning');
          }
        }
    });
}

function select_loan_type(profile_id){
  var member_id = document.getElementById("member_id").value;
  var search_type = document.getElementById("search_type_list").value;
    $.ajax({
        method: 'POST',
        url: base_url+'finance_month_detail/get_contract_list',
        data: {
          member_id: member_id,
          search_type: search_type,
          profile_id: profile_id,
        },
        success: function(msg){
            $('#deduct_list').html(msg);
        }
    });
}
</script>