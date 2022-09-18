var temp = new Array();

//seq_no
function inline_transaction_seq_no(transaction_id) {
    temp['inline_transaction_seq_no'] = $('.inline_transaction_seq_no[data-inline_transaction_id="' + transaction_id + '"]').html();
	
    var html = `<div class="g24-col-sm-24">
                    <div class="form-group">
                        <input id="seq_no_`+transaction_id+`" name="seq_no" class="form-control m-b-1" type="number" autocorrect="off" spellcheck="false" autocomplete="off"  autocomplete="false">
                    </div>
                </div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="save_inline_seq_no(`+transaction_id+`)"><i class="fa fa-check" aria-hidden="true"></i></a></div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="dimiss_inline_seq_no(`+transaction_id+`)"><i class="fa fa-times" aria-hidden="true" style="color: red !important;"></i></a></div>
        `;
    $('.inline_transaction_seq_no[data-inline_transaction_id="' + transaction_id + '"]').html(html);
	//$('#seq_no_'+transaction_id).attr('autocomplete','off');
	$("#seq_no_"+transaction_id).focus();
	//$('input').attr('autocomplete', 'off');
	$('html, body').animate({
      scrollTop: $(".row_id_"+transaction_id).offset().top
    }, 1000)
}

function save_inline_seq_no(transaction_id){
    var seq_no = $("#seq_no_"+transaction_id).val();
    var token = $('.inline_transaction_seq_no[data-inline_transaction_id="' + transaction_id + '"]').data("token");
    $.ajax({
        url: base_url+'/Save_money/inline_update',
        method: 'POST',
        data: {
            method  : "seq_no",
            transaction_id : transaction_id,
            seq_no : seq_no,
            token : token
        },
        async:false,
        success: function(res){
            if(res.result){
				blockUI();
                //$('.inline_transaction_seq_no[data-inline_transaction_id="' + transaction_id + '"]').html(temp['inline_transaction_seq_no']);
                //$('.inline_transaction_seq_no[data-inline_transaction_id="' + transaction_id + '"]').html(res.message);
				window.location.reload();
            }else{
                swal("ไม่สามารถบันทึกข้อมูลได้", "", "warning");
            }
        }
    });
}

function dimiss_inline_seq_no(transaction_id){
    $('.inline_transaction_seq_no[data-inline_transaction_id="' + transaction_id + '"]').html(temp['inline_transaction_seq_no']);
}