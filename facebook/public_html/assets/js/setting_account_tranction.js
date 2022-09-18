var base_url = $('#base_url').attr('class');
function open_modal(id){
    $('#'+id).modal('show');
}
function close_modal(id){
    $('.type_input').val('');
    $('#'+id).modal('hide');
}
function add_account_chart(){
    $('#modal_title').html('เพิ่มผังบัญชี');
    open_modal('add_account_chart');
}

function form_submit(){
    var text_alert = '';
    if($('#account_chart_id').val()==''){
        text_alert += '- รหัสผังบัญชี\n';
    }
    if($('#account_chart').val()==''){
        text_alert += '- ผังบัญชี\n';
    }
    if(text_alert!=''){
        swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
    }else{

            $('#form1').submit();

    }
}
function edit_account_chart(account_chart_id, setting_name_list,description, process, ref_type, match_type){
    $('#modal_title').html('แก้ไขผังบัญชี');
    $('#old_account_chart_id').val(account_chart_id);
    $('#setting_name_list').val(setting_name_list);
    $('#description').val(description);
    $('#process').val(process);
    $('#ref_type').val(ref_type);
    $('#match_type').val(match_type);


    open_modal('add_account_chart');
}
function sub_edit_account_chart(account_match_id, match_id_description,match_type, account_chart_id, match_id,bankcharge){
    $('#modal_title').html('แก้ไขผังบัญชี');
    $('#old_account_match_id').val(account_match_id);
    $('#match_id_description').val(match_id_description);
    $('#match_type').val(match_type);
    $('#bankcharge').val(bankcharge);

    change_type2(match_id);

    if($('#match_type').val() == 'bank'){
        document.getElementById("bankcharge_la").style.display = "block";
        document.getElementById("bankcharge_div").style.display = "block";
    }else{
        document.getElementById("bankcharge_la").style.display = "none";
        document.getElementById("bankcharge_div").style.display = "none";
    }


    // $('#match_id').val(match_id);
    // console.log(match_id);
    // $('#match_id option[value=match_id]').attr('selected','selected');
    $('#account_chart_id').val(account_chart_id);

    open_modal('add_account_chart');
}
function del_account_tranction (id){
    swal({
        title: "ท่านต้องการลบข้อมูลการตั้งค่าบันทึกบัญชีใช่หรือไม่",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: "ยกเลิก",
        closeOnConfirm: false,
        closeOnCancel: true
    },
        function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: base_url+'/setting_account_tranction/del_setting_account_tranction',
                    method: 'POST',
                    data: {
                        'table': 'setting_account_tranction',
                        'id': id,
                        'field': 'setting_id'
                    },
                    success: function(msg){
                        // console.log(msg); return false;
                        if(msg == 1){
                            document.location.href = base_url+'setting_account_tranction/index_setting_account_tranction';
                        }else{

                        }
                    }
                });
            } else {

            }
        });
}
function del_sub_account_tranction (id){
    swal({
            title: "ท่านต้องการลบข้อมูลการตั้งค่าบันทึกบัญชีใช่หรือไม่",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: base_url+'/setting_account_tranction/sub_del_setting_account_tranction',
                    method: 'POST',
                    data: {
                        'table': 'coop_account_match',
                        'id': id,
                        'field': 'account_match_id'
                    },
                    success: function(msg){
                        // console.log(msg); return false;
                        if(msg == 1){
                            document.location.href = base_url+'setting_account_tranction/sub_index_setting_account_match';
                        }else{

                        }
                    }
                });
            } else {

            }
        });
}
$( document ).ready(function() {
    $('#add_account_chart').on('hide.bs.modal', function () {
        $('.type_input').val('');
    });
});

function change_type(){

    if($('#match_type').val() == 'bank'){
        document.getElementById("bankcharge_la").style.display = "block";
        document.getElementById("bankcharge_div").style.display = "block";
    }else{
        document.getElementById("bankcharge_la").style.display = "none";
        document.getElementById("bankcharge_div").style.display = "none";
    }

    $.ajax({
        url: base_url+'setting_account_tranction/change_loan_type',
        method: 'POST',
        data: {
            'type_id': $('#match_type').val()
        },
        success: function(msg){
            $('#match_id').html(msg);

        }
    });
}
function change_type2(match_id1){
    // console.log(match_id1);
    $.ajax({
        url: base_url+'setting_account_tranction/change_loan_type',
        method: 'POST',
        data: {
            'type_id': $('#match_type').val()
        },
        success: function(msg){
            $('#match_id').html(msg);
            $("#match_id").val(match_id1);
        }

    });
}