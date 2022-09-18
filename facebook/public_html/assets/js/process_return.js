$(function() {
  // Init. Element
  $(".datepick").datepicker({
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

  if(('#table_process_return').length > 0) get_process_return();
  $('body').on('click', '#btn_get_data', function() {
    get_process_return();
  });

  function get_process_return() {
    var $date_s = $('#date_s').datepicker('getDate');
    var $date_e = $('#date_e').datepicker('getDate');
    var $is_error = false;
    var $error_msg = '';
    if( (($date_s.getMonth() +1) != ($date_e.getMonth() +1)) || ($date_s.getYear() != $date_e.getYear()) ) {
      $is_error = true;
      $error_msg = 'ไม่สามรถเลือกข้ามเดือนได้';
    }
    var $month = $date_e.getMonth() + 1;
    var $year = $date_e.getYear();
    if( $is_error == true ) {
      swal($error_msg,'','warning');
    } else {
      $('#table_process_return tbody').empty().append('<tr><td colspan="7"><h4 class="text-center"><i class="fa fa-refresh fa-spin"></i>&nbsp;กรุณารอสักครู่</h4></td></tr>');
      $.ajax({
        url: base_url+"ajax/process_return",
        method:"post",
        data: {
          date_s : $('#date_s').val(),
          date_e : $('#date_e').val()
        },
        dataType:"json",
        success:function($json) {
          //console.log($json);
          $('#table_process_return tbody').empty();
          $json.data.forEach(function($row) {
            if( $row.type == 1 ) {
              $('#table_process_return tbody').append('\
              <tr>\
                <td>' + $row.title + '</td>\
                <td><a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=total&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.total + '</a></td>\
                <td class="text-center">-</td>\
                <td><a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=return&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.return + '</a></td>\
                <td><a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=surcharge&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.surcharge + '</a></td>\
                <td>\
                  <a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=remain&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.remain + '</a>\
                  <a href="/finance_process/process_return_edit?type=' + $row.type + '&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank"><i class="icon icon-pencil"></i></a>\
                </td>\
                <td><button type="button" class="btn btn-primary center-block btn-return-process" data-type="' + $row.type + '">คืนเงิน</button></td>\
              </tr>\
              ');
            } else if( $row.type == 3 ) {
              $('#table_process_return tbody').append('\
              <tr>\
                <td>' + $row.title + '</td>\
                <td><a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=total&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.total + '</a></td>\
                <td class="text-center">-</td>\
                <td><a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=return&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.return + '</a></td>\
                <td>-</td>\
                <td>\
                  <a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=remain&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.remain + '</a>\
                  <a href="/finance_process/process_return_edit?type=' + $row.type + '&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank"><i class="icon icon-pencil"></i></a>\
                </td>\
                <td><button type="button" class="btn btn-primary center-block btn-return-process" data-type="' + $row.type + '">คืนเงิน</button></td>\
              </tr>\
              ');
            } else if( $row.type == 4 ) {
              $('#table_process_return tbody').append('\
              <tr>\
                <td>' + $row.title + '</td>\
                <td><a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=total&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.total + '</a></td>\
                <td class="text-center">-</td>\
                <td><a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=return&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.return + '</a></td>\
                <td class="text-center">-</td>\
                <td>\
                  <a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=remain&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.remain + '</a>\
                  <a href="/finance_process/process_return_edit?type=' + $row.type + '&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank"><i class="icon icon-pencil"></i></a>\
                </td>\
                <td><button type="button" class="btn btn-primary center-block btn-return-process" data-type="' + $row.type + '">คืนเงิน</button></td>\
              </tr>\
              ');
            } else {
              $('#table_process_return tbody').append('\
              <tr>\
                <td>' + $row.title + '</td>\
                <td><a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=total&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.total + '</a></td>\
                <td><a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=no_return&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.no_return + '</a></td>\
                <td><a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=return&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.return + '</a></td>\
                <td class="text-center">-</td>\
                <td>\
                  <a href="/finance_process/process_return_excel?type=' + $row.type + '&sec=remain&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank">' + $row.remain + '</a>\
                  <a href="/finance_process/process_return_edit?type=' + $row.type + '&ds=' + $('#date_s').val() + '&de=' + $('#date_e').val() + '" target="_blank"><i class="icon icon-pencil"></i></a>\
                </td>\
                <td><button type="button" class="btn btn-primary center-block btn-return-process" data-type="' + $row.type + '">คืนเงิน</button></td>\
              </tr>\
              ');
            }

          });
        },
        error: function(xhr){
          console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
        }
      });
    }

  }

  var $return_type = '';

  $('body').on('click', '.btn-return-process', function() {
    $return_type = $(this).data('type');
    // swal(,'','info');
    
    swal({
      title: "ข้อมูลจากระบบ",
      text: 'ท่านยังไม่ได้เลือกรายการคืนเงิน \r\n กรุณาเลือกก่อนทำการคืนเงิน',
      type: "info",
      showCancelButton: true,
      confirmButtonColor: '#DD6B55',
      confirmButtonText: 'เลือกรายการคืนเงิน',
      cancelButtonText: "ปิด",
      closeOnConfirm: false,
      closeOnCancel: false
   },
   function(isConfirm){
  
     if (isConfirm){
      window.location.replace(base_url+"finance_process/process_return_edit?type="+$return_type+"&ds="+$("#date_s").val()+"&de="+$("#date_e").val());
  
      } else {
        swal.close();
      }
   });
    return;
    // if( $('#modal-confirm-return').length == 0 ) {
    //   $('body').append('\
    //   <div class="modal fade" id="modal-confirm-return" role="dialog">\
    //     <div class="modal-dialog" style="width: 400px;">\
    //       <div class="modal-content">\
    //         <div class="modal-body">\
    //           <div class="text-center">\
    //             <div class="m-t-lg">\
    //               <button type="button" class="btn btn-primary" id="btn-confirm-return">ยืนยันคืนเงิน</button>\
    //               <button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>\
    //             </div>\
    //           </div>\
    //         </div>\
    //       </div>\
    //     </div>\
    //   </div>\
    // ');
    // }
    // $('#modal-confirm-return').modal();
  });

  $('body').on('click', '#btn-confirm-return', function()  {
    $('.btn-return-process').addClass('disabled');
    $(this).html('<i class="fa fa-refresh fa-spin"></i>&nbsp;กรุณารอสักครู่');
    process_return_exec( $return_type );
  });
  function process_return_exec($type_id) {
      //$('#alertNotFindModal .modal-body').html('<h3 class="text-center"><i class="fa fa-refresh fa-spin m-r-xxs"></i>กรุณารอสักครู่</h3>');
      //$('#alertNotFindModal').modal();
      //swal('<h3 class="text-center"><i class="fa fa-refresh fa-spin m-r-xxs"></i>กรุณารอสักครู่</h3>','','info');
      $.ajax({
        url: base_url+"ajax/process_return_exec",
        method:"post",
        data: {
          ret_type : $type_id,
          date_s : $('#date_s').val(),
          date_e : $('#date_e').val()
        },
        dataType:"text",
        success:function($data) {
          //$('#alertNotFindModal').modal('toggle');
          get_process_return();
        },
        error: function(xhr){
          console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
        }
      });
  }

  /****************************************************************
   * คืนเงินด้วยตัวเอง
   */
  $('body').on('click', '#btn-show-return-manual', function()  {
    show_form_return_manual();
  });

  $('body').on('click', '#fmr-member_id-search', function()  {
      show_enter_member_manual();
  }).on('keypress', "#fmr-member_id", function(e){
    if(e.which === 13) {
      show_enter_member_manual();
    }
  }).on("change", "#fmr-date", function(){
      show_enter_member_manual();
  });

  const show_enter_member_manual = () => {
    if( $.trim($('#fmr-member_id').val()).length > 0 ) {
      $('#fmr-member_name').html('<i class="fa fa-refresh fa-spin"></i>');
      $.ajax({
        url: base_url+"ajax/fmr_get_member_desc",
        method:"post",
        data: {
          member_id : $.trim($('#fmr-member_id').val()),
          date: $.trim($('#fmr-date').val())
        },
        dataType:"json",
        success:function($json) {
          if( $json.is_found == 1 ) {
            $('#fmr-member_id').val($json.member.member_id)
            $('#fmr-member_name').html($json.member.member_name);
            $('#fmr-btn-save').removeClass('disabled');
            $('#fmr-loan_id').empty().append('<option value="0">-- เลือกเลขที่สัญญา --</option>');
            $('#fmr-loan_id').append('<optgroup label="หุ้น">');
            $('#fmr-loan_id').append('<option value="share#' + $json.member.member_id + '"> หุ้นสมาชิก ' + $json.member.member_id + '</option>');
            $('#fmr-loan_id').append('</optgroup>');
            if($json.loan.length > 0) {
              $('#fmr-loan_id').append('<optgroup label="กู้เงิน">');
              $json.loan.forEach(function($row) {
                $('#fmr-loan_id').append('<option value="loan#' + $row.loan_id + '">' + $row.contract_number + '</option>');
              });
              $('#fmr-loan_id').append('</optgroup>');
            }
            if($json.loan_atm.length > 0) {
              $('#fmr-loan_id').append('<optgroup label="กู้เงินฉุกเฉิน ATM">');
              $json.loan_atm.forEach(function($row) {
                $('#fmr-loan_id').append('<option value="loan_atm#' + $row.loan_atm_id + '">' + $row.contract_number + '</option>');
              });
              $('#fmr-loan_id').append('</optgroup>');
            }
            if($json.deposit.length > 0) {
              $('#fmr-loan_id').append('<optgroup label="บัญชีเงินฝาก">');
              $json.deposit.forEach(function($row) {
                $('#fmr-loan_id').append('<option value="deposit#' + $row.account_id + '">' + $row.account_id_show + '</option>');
              });
              $('#fmr-loan_id').append('</optgroup>');
            }
            
			
			//เช็ค default ช่องทางชำระเงิน
			if($json.account.account_id == null){
				$('#pay_type_cash').attr('checked',true);				
			}else{
				$('#pay_type_transfer').attr('checked',true);
			}
			

          } else {
            $('#fmr-member_name').html('-');
            $('#fmr-btn-save').addClass('disabled');
            $('#fmr-loan_id').empty().append('<option value="0">-- เลือกเลขที่สัญญา --</option>');
          }
        },
        error: function(xhr){
          console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
        }
      });
    }
  };
  $(document).on("change", "#fmr-date, #fmr-loan_id ", () => {
    if($("#fmr-loan_id").val() !== '0' && $("#fmr-date").val() !== ''){
      const id = $("#fmr-loan_id").val();
      const date = $("#fmr-date").val();
      const member_id = $("#fmr-member_id").val();
      getReceiptList(member_id, id, date)
    }
  });

  const getReceiptList = (member_id, id, date) => {
  const data = { id:id , date: date, member_id: member_id};
  new Promise((resolve, reject) => {
    $.post(base_url+"ajax/receipt_list", data, (res, status, xhr) => {
      if(res.status === 200){
        resolve(res);
      }else{
        reject(res);
      }
    })
  }).then((res) => {
    $("#fmr-receipt-list").empty().append('<option value="0">-- เลือกรูปใบเสร็จ --</option>');
    res.receipt.forEach((item) => {
      $("#fmr-receipt-list").append(`<option value="${item.receipt_id}" data-principal="${item.principal}" data-interest="${item.interest}" data-total="${item.total}">${item.receipt_id}</option>`);
    });
    $("#fmr-receipt-list").trigger("change");
  }).catch((err) => {
      $("#fmr-receipt-list").empty().append('<option value="0">-- เลือกรูปใบเสร็จ --</option>');
      $("#fmr-receipt-list").trigger("change");
  });
}

$(document).on("change", "#fmr-receipt-list", () => {
  const interest = $("#fmr-receipt-list").find("option:selected").data('interest');
  const principal = $("#fmr-receipt-list").find("option:selected").data('principal');
  $("#fmr-return_principal").val(principal).trigger("change");
  $("#fmr-return_interest").val(interest).trigger("change");
  });

  $('body').on('click', '#fmr-btn-save', function()  {
    $('#modal-form-return-manual .form-group').removeClass('has-error');
    var $is_error = false;

    if( $.isNumeric($('#fmr-return_principal').val()) === false ) {
      $is_error = true;
      $('#fmr-return_principal').parents('.form-group').addClass('has-error');
    }
    if( $.isNumeric($('#fmr-return_interest').val()) === false ) {
      $is_error = true;
      $('#fmr-return_interest').parents('.form-group').addClass('has-error');
    }
    if( $('#fmr-loan_id').val() == 0 ) {
      $is_error = true;
      $('#fmr-loan_id').parents('.form-group').addClass('has-error');
    }
    if( $('#fmr-return_type').val() == 0 ) {
      $is_error = true;
      $('#fmr-return_type').parents('.form-group').addClass('has-error');
    }
	
	var pay_type = $('input[name=pay_type]:checked').val();

    if( $is_error == false ) {
      $('#fmr-btn-save').addClass('disabled');

      $.ajax({
        url: base_url+"ajax/fmr_exec",
        method:"post",
        data: {
          member_id : $('#fmr-member_id').val(),
          loan_id : $('#fmr-loan_id').val(),
          return_type : $('#fmr-return_type').val(),
          return_desc : $('#fmr-return_desc').val(),
          return_principal : $('#fmr-return_principal').val(),
          return_interest : $('#fmr-return_interest').val(),
          pay_type : pay_type,
          _t: Math.random()
        },
        dataType:"json",
        success:function($json) { console.log($json);
		
		var link_account = '';
		if(pay_type == '0'){
			link_account = '';
		}else{
			link_account = '<a class="btn btn-primary" href="' + $json.link_account + '"target="_blank" style="width: unset;">รายละเอียดบัญชีเงินฝาก</a>';
		}
		
         if( $json.success == 1 ) {
          $('#modal-form-return-manual .modal-body').html('\
          <h2 class="text-success" style="text-align: center;">บันทึกข้อมูลสำเร็จค่ะ</h2>\
          <div class="text-center">\
            <div class="m-t-lg">\
			  '+link_account+'\
              <a class="btn btn-primary" href="' + $json.receipt_return_self + '" target="_blank" style="width: unset;">ใบเสร็จ</a>\
              <a class="btn btn-primary" href="' + $json.link_loan + '" target="_blank" style="width: unset;">รายละเอียดเงินกู้</a>\
              <button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>\
            </div>\
          </div>\
          ');
         } else {
          $('#modal-form-return-manual .modal-body').html('\
          <h2 class="text-danger" style="text-align: center;">เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง' + ($json.error_msg.length > 0 ? '<br />เนื่องจาก' + $json.error_msg : '' ) + '</h2>\
          <div class="text-center">\
            <div class="m-t-lg">\
              <button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>\
            </div>\
          </div>\
          ');
         }
        },
        error: function(xhr){
          console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
        }
      });
    }

  });

  $('body').on('change', '#fmr-loan_id', function() {
    $('#fmr-return_type').empty().append('<option value="0">-- เลือกรูปแบบการคืน --</option>');
    if( $('#fmr-loan_id').val().indexOf('loan_atm#') != -1 ) {
      $('#fmr-return_type')
      .append('<option value="3">คืนเงิน ฉATM</option>')
      .append('<option value="4">คืนเงิน ATM หลังผ่านรายการ</option>');
    } else {
      $('#fmr-return_type')
      .append('<option value="1">คืนเงินผ่านรายการเรียกเก็บ</option>')
      .append('<option value="2">คืนเงินหักกลบ</option>');
    }
  });

  function show_form_return_manual() {
    $('#modal-form-return-manual').remove();

    $('body').append('\
      <div class="modal fade" id="modal-form-return-manual" role="dialog">\
        <div class="modal-dialog modal-lg" role="document">\
          <div class="modal-content">\
            <div class="modal-header modal-header-info"><h4 class="modal-title">คืนเงินด้วยตัวเอง</h4></div>\
            <div class="modal-body">\
              <div class="form-horizontal">\
                <div class="form-group">\
                  <label class="control-label col-xs-6 col-sm-5 text-right">รหัสสมาชิก</label>\
                  <div class="col-xs-6 col-sm-3">\
                    <div class="input-group">\
                      <input type="text" class="form-control" id="fmr-member_id" />\
                      <div class="input-group-btn">\
                        <button type="button" class="btn btn-primary" id="fmr-member_id-search" style="width: unset;"><i class="icon icon-search"></i></button>\
                      </div>\
                    </div>\
                  </div>\
                </div>\
                <div class="form-group">\
                  <label class="control-label col-xs-6 col-sm-5 text-right">ชื่อสมาชิก</label>\
                  <div class="col-xs-6 col-sm-3">\
                    <p class="form-control-static" id="fmr-member_name">-</p>\
                  </div>\
                </div>\
                <div class="form-group">\
                <label class="control-label col-xs-6 col-sm-5 text-right">วันที่</label>\
                <div class="col-xs-6 col-sm-3">\
                <div class="input-with-icon col-sm-12" style="padding: 0px !important;margin-left: 0px !important;margin-right: 0px !important;">\
                  <div class="form-group" style="padding: 0px !important;margin-left: 0px !important;margin-right: 0px !important;">\
                    <input id="fmr-date" name="fmr-date" class="form-control" style="padding-left: 50px;" type="text" value="" data-date-language="th-th" autocomplete="off">\
                    <span class="icon icon-calendar input-icon m-f-1"></span>\
                  </div>\
                </div>\
                </div>\
              </div>\
                <div class="form-group">\
                  <label class="control-label col-xs-6 col-sm-5 text-right">เลขที่สัญญา</label>\
                  <div class="col-xs-6 col-sm-3">\
                    <select class="form-control" id="fmr-loan_id">\
                      <option value="0">-- เลือกเลขที่สัญญา --</option>\
                    </select>\
                  </div>\
                </div>\
                <div class="form-group">\
                  <label class="control-label col-xs-6 col-sm-5 text-right">รูปแบบการคืน</label>\
                  <div class="col-xs-6 col-sm-3">\
                    <select class="form-control" id="fmr-return_type">\
                      <option value="0">-- เลือกรูปแบบการคืน --</option>\
                    </select>\
                  </div>\
                </div>\
                <div class="form-group">\
                <label class="control-label col-xs-6 col-sm-5 text-right">ใบเสร็จ</label>\
                <div class="col-xs-6 col-sm-3">\
                  <select class="form-control" id="fmr-receipt-list">\
                    <option value="0">-- เลือกใบเสร็จ --</option>\
                  </select>\
                </div>\
              </div>\
				<div class="form-group">\
                  <label class="control-label col-xs-6 col-sm-5 text-right">ช่องทางชำระเงิน</label>\
                  <div class="col-xs-6 col-sm-3">\
					<input type="radio" id="pay_type_cash" name="pay_type" checked="" value="0"> เงินสด\
					<input type="radio" id="pay_type_transfer" name="pay_type" value="1"> เงินโอน\
                  </div>\
                </div>\
                <div class="form-group">\
                  <label class="control-label col-xs-6 col-sm-5 text-right">รายละเอียด</label>\
                  <div class="col-xs-6 col-sm-3">\
                    <input type="text" class="form-control" id="fmr-return_desc" />\
                  </div>\
                </div>\
                <div class="form-group">\
                  <label class="control-label col-xs-6 col-sm-5 text-right">เงินต้น</label>\
                  <div class="col-xs-6 col-sm-3">\
                    <div class="input-group">\
                      <input type="text" class="form-control" id="fmr-return_principal" />\
                      <div class="input-group-addon">บาท</div>\
                    </div>\
                  </div>\
                </div>\
                <div class="form-group">\
                  <label class="control-label col-xs-6 col-sm-5 text-right">ดอกเบี้ย</label>\
                  <div class="col-xs-6 col-sm-3">\
                    <div class="input-group">\
                      <input type="text" class="form-control" id="fmr-return_interest" />\
                      <div class="input-group-addon">บาท</div>\
                    </div>\
                  </div>\
                </div>\
              </div>\
              <div class="text-center">\
                <div class="m-t-lg">\
                  <button type="button" class="btn btn-primary disabled" id="fmr-btn-save">บันทึก</button>\
                  <button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>\
                </div>\
              </div>\
            </div>\
          </div>\
        </div>\
      </div>\
    ');
    $("#fmr-date").datepicker({
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
    /*
    $("#fmr-return_date").datepicker({
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
      autoclose: true
    }).datepicker("setDate", new Date());

    */

    $('#modal-form-return-manual').modal({
      'backdrop': 'static',
      'keyboard': 'false'
    });
  }

     /****************************************************************
   * แก้ไข Statement
   */
  $('body').on('click', '#btn-show-statement-edit', function()  {
    show_form_statement_edit();
  });

  $('body').on('keypress', '#fse-member_id', function(e)  {
    if(e.which == 13) {
      fse_member_search();
    }
  });

  $('body').on('click', '#fse-member_id-search', function()  {
    fse_member_search();
  });

  $('body').on('change', '#fse-loan_id', function() {
    $('#fse-return_type').empty().append('<option value="0">-- เลือกรูปแบบการคืน --</option>');
    if( $('#fse-loan_id').val().indexOf('loan_atm#') != -1 ) {
      $('#fse-return_type')
      .append('<option value="3">คืนเงิน ฉATM</option>')
      .append('<option value="4">คืนเงิน ATM หลังผ่านรายการ</option>');
    } else {
      $('#fse-return_type')
      .append('<option value="1">คืนเงินผ่านรายการเรียกเก็บ</option>')
      .append('<option value="2">คืนเงินหักกลบ</option>');
    }
  });

  $('body').on('change', '#fse-return_type', function() {
    show_statement_list();
  });

  $('body').on('click', '.fse-statement-del', function() {
    $('#modal-statement-del-confirm').remove();

    $('body').append('\
    <div class="modal fade" id="modal-statement-del-confirm" role="dialog">\
      <div class="modal-dialog modal-sm" role="document">\
        <div class="modal-content">\
          <div class="modal-header modal-header-alert"><h4 class="modal-title">ยืนยันแก้ไข Statement</h4></div>\
          <div class="modal-body">\
            <div class="text-center">\
              <div class="m-t-lg">\
                <button type="button" class="btn btn-primary btn-statement-del-confirm" data-ret_id="' + $(this).data('ret_id') + '" data-return_amount="' + $(this).data('return_amount') + '" data-account_id="' + $(this).data('account_id') + '">ยืนยัน</button>\
                <button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>\
              </div>\
            </div>\
          </div>\
        </div>\
      </div>\
    </div>\
  ');

    $('#modal-statement-del-confirm').modal({
    'backdrop': 'static',
    'keyboard': 'false'
  });
  });

  $('body').on('click', '.btn-statement-del-confirm', function() {

    $.ajax({
      url: base_url+"ajax/fse_confirm_del",
      method:"post",
      data: {
        ret_id : $(this).data('ret_id'),
        return_amount : $(this).data('return_amount'),
        account_id : $(this).data('account_id')
      },
      dataType:"json",
      success:function($json) {
        $('#modal-statement-del-confirm').modal('hide');
        show_statement_list();
      },
      error: function(xhr){
        console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
      }
    });
  });

  function fse_member_search() {
    if( $.trim($('#fse-member_id').val()).length > 0 ) {
      $('#fse-member_name').html('<i class="fa fa-refresh fa-spin"></i>');
      $.ajax({
        url: base_url+"ajax/fmr_get_member_desc",
        method:"post",
        data: {
          member_id : $.trim($('#fse-member_id').val())
        },
        dataType:"json",
        success:function($json) {
          if( $json.is_found == 1 ) {
            $('#fse-member_id').val($json.member.member_id)
            $('#fse-member_name').html($json.member.member_name);
            $('#fse-loan_id').empty().append('<option value="0">-- เลือกเลขที่สัญญา --</option>');
            if($json.loan.length > 0) {
              $('#fse-loan_id').append('<optgroup label="กู้เงิน">');
              $json.loan.forEach(function($row) {
                $('#fse-loan_id').append('<option value="loan#' + $row.loan_id + '">' + $row.contract_number + '</option>');
              });
              $('#fse-loan_id').append('</optgroup>');
            }
            if($json.loan_atm.length > 0) {
              $('#fse-loan_id').append('<optgroup label="กู้เงินฉุกเฉิน ATM">');
              $json.loan_atm.forEach(function($row) {
                $('#fse-loan_id').append('<option value="loan_atm#' + $row.loan_atm_id + '">' + $row.contract_number + '</option>');
              });
              $('#fse-loan_id').append('</optgroup>');
            }

          } else {
            $('#fse-member_name').html('-');
            $('#fse-loan_id').empty().append('<option value="0">-- เลือกเลขที่สัญญา --</option>');
          }
        },
        error: function(xhr){
          console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
        }
      });
    }
  }

  function show_statement_list() {
    $('#fse-statement-data tbody').empty().append('<tr><td colspan="8"><h4style="text-align: center;"><i class="fa fa-refresh fa-spin"></i> กรุณารอสักครู่</h4></td></tr>');
    $.ajax({
      url: base_url+"ajax/fse_get_return_statement",
      method:"post",
      data: {
        member_id : $.trim($('#fse-member_id').val()),
        loan_id : $.trim($('#fse-loan_id').val()),
        return_type : $.trim($('#fse-return_type').val())
      },
      dataType:"json",
      success:function($json) {
        if( $json.is_found ) {
          $('#fse-statement-data tbody').empty();
          var $index = 1;
          $json.statement.forEach(function($row) {
            $('#fse-statement-data tbody').append('\
            <tr>\
              <td>' + $index++ +  '</td>\
              <td>' + $row.return_date + '</td>\
              <td>' + $row.bill_id + '</td>\
              <td>' + $row.return_desc + '</td>\
              <td>' + $row.return_principal + '</td>\
              <td>' + $row.return_interest + '</td>\
              <td>' + $row.return_amount + '</td>\
              <td><i class="fa fa-trash text-danger fse-statement-del" data-ret_id="' + $row.ret_id + '" data-return_amount="' + $row.return_amount + '" data-account_id="' + $row.account_id + '" style="cursor: pointer;"></i></td>\
            </tr>\
            ');
          });
        } else {
          $('#fse-statement-data tbody').empty().append('<tr><td colspan="8"><h4style="text-align: center;">ไม่พบข้อมูลการคืนเงินค่ะ</td></tr>');
        }
      },
      error: function(xhr){
        console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
      }
    });
  }

  function show_form_statement_edit() {
    $('#modal-form-statement-edit').remove();

    $('body').append('\
      <div class="modal fade" id="modal-form-statement-edit" role="dialog">\
        <div class="modal-dialog modal-lg" role="document">\
          <div class="modal-content">\
            <div class="modal-header modal-header-info"><h4 class="modal-title">แก้ไข Statement</h4></div>\
            <div class="modal-body">\
              <div class="form-horizontal">\
                <div class="form-group">\
                  <label class="control-label col-xs-6 col-sm-5 text-right">รหัสสมาชิก</label>\
                  <div class="col-xs-6 col-sm-3">\
                    <div class="input-group">\
                      <input type="text" class="form-control" id="fse-member_id" />\
                      <div class="input-group-btn">\
                        <button type="button" class="btn btn-primary" id="fse-member_id-search" style="width: unset;"><i class="icon icon-search"></i></button>\
                      </div>\
                    </div>\
                  </div>\
                </div>\
                <div class="form-group">\
                  <label class="control-label col-xs-6 col-sm-5 text-right">ชื่อสมาชิก</label>\
                  <div class="col-xs-6 col-sm-3">\
                    <p class="form-control-static" id="fse-member_name">-</p>\
                  </div>\
                </div>\
                <div class="form-group">\
                  <label class="control-label col-xs-6 col-sm-5 text-right">เลขที่สัญญา</label>\
                  <div class="col-xs-6 col-sm-3">\
                    <select class="form-control" id="fse-loan_id">\
                      <option value="0">-- เลือกเลขที่สัญญา --</option>\
                    </select>\
                  </div>\
                </div>\
                <div class="form-group">\
                  <label class="control-label col-xs-6 col-sm-5 text-right">รูปแบบการคืน</label>\
                  <div class="col-xs-6 col-sm-3">\
                    <select class="form-control" id="fse-return_type">\
                      <option value="0">-- เลือกรูปแบบการคืน --</option>\
                    </select>\
                  </div>\
                </div>\
              </div>\
              <table class="table table-bordered table-striped table-center" style="margin: 15px 0px;" id="fse-statement-data">\
                <thead>\
                  <tr class="bg-primary">\
                    <th width="45">ลำดับ</th>\
                    <th width="100">วันที่</th>\
                    <th width="100">เลขที่ใบเสร็จ</th>\
                    <th>รายการ</th>\
                    <th width="80">เงินต้น</th>\
                    <th width="80">ดอกเบี้ย</th>\
                    <th width="80">รวม</th>\
                    <th width="60">&nbsp;</th>\
                  </tr>\
                </thead>\
                <tbody>\
                </tbody>\
              </table>\
              <div class="text-center">\
                <div class="m-t-lg">\
                  <button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>\
                </div>\
              </div>\
            </div>\
          </div>\
        </div>\
      </div>\
    ');

      $('#modal-form-statement-edit').modal({
      'backdrop': 'static',
      'keyboard': 'false'
    });
  }

});