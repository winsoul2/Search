var base_url = $('#base_url').attr('class');
$( document ).ready(function() {

});
function add_benefit(gain_detail_id,member_id){
    $.ajax({
        url:base_url+"beneficiary/add_beneficiary",
        method:"post",
        data:{gain_detail_id:gain_detail_id, member_id:member_id},
        dataType:"text",
        success:function(data)
        {
            $('#add_benefit_space').html(data);
            $('#add_benefit').modal('show');
        }
    });

}

function show_detail(gain_detail_id){
    $.ajax({
        url:base_url+"beneficiary/show_beneficiary",
        method:"post",
        data:{gain_detail_id:gain_detail_id},
        dataType:"text",
        success:function(data)
        {
            $('#show_detail_space').html(data);
            $('#show_detail').modal('show');
        }
    });
}

function change_province(id, id_to, id_input_amphur, district_space, id_input_district){
    var province_id = $('#'+id).val();
    $.ajax({
        method: 'POST',
        url: base_url+'manage_member_share/get_amphur_list',
        data: {
            province_id : province_id,
            id_input_amphur : id_input_amphur,
            district_space : district_space,
            id_input_district : id_input_district
        },
        success: function(msg){
            $('#'+id_to).html(msg);
            $('#'+id_input_amphur).removeClass('m-b-1');
        }
    });
}

function change_amphur(id, id_to, id_input_district){
    var amphur_id = $('#'+id).val();
    $.ajax({
        method: 'POST',
        url: base_url+'manage_member_share/get_district_list',
        data: {
            amphur_id : amphur_id,
            id_input_district : id_input_district
        },
        success: function(msg){
            $('#'+id_to).html(msg);
            $('#'+id_input_district).removeClass('m-b-1');
        }
    });
}
function check_form(){
    var text_alert = '';
    var id_to_focus = [];
    var i=0;
    if($('#g_prename_id').val() == ''){
        // text_alert += 'คำนำหน้าชื่อ\n';
        // id_to_focus[i] = 'g_prename_id';
        // i++;
    }
    if($('#g_firstname').val() == ''){
        // text_alert += 'ชื่อ\n';
        // id_to_focus[i] = 'g_firstname';
        // i++;
    }
    if($('#g_lastname').val() == ''){
        // text_alert += 'สกุล\n';
        // id_to_focus[i] = 'g_lastname';
        // i++;
    }
    if($('#g_relation_id').val() == ''){
        // text_alert += 'ความสัมพันธ์\n';
        // id_to_focus[i] = 'g_relation_id';
        // i++;
    }
    if($('#g_id_card').val() == ''){
        // text_alert += 'เลขบัตรประชาชน\n';
        // id_to_focus[i] = 'g_id_card';
        // i++;
    }
    if($('#g_share_rate').val() == ''){
        // text_alert += 'ส่วนแบ่ง\n';
        // id_to_focus[i] = 'g_share_rate';
        // i++;
    }
    if($('#g_mobile').val() == ''){
        // text_alert += 'หมายเลขโทรศัพท์มือถือ\n';
        // id_to_focus[i] = 'g_mobile';
        // i++;
    }
    /*if($('#g_email').val() == ''){
        text_alert += 'อีเมล์\n';
        id_to_focus[i] = 'g_email';
        i++;
    }*/
    if(text_alert != ''){
        $('#'+ id_to_focus[0]).focus();
        swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');

    }else{
        $('#form1').submit();
    }
}

function delete_benefit(gain_detail_id,member_id){
    swal({
            title: "ท่านต้องการลบข้อมูลใช่หรือไม่?",
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
                document.location.href = base_url+"beneficiary/delete_beneficiary/"+gain_detail_id+"/"+member_id;
            } else {

            }
        });
}