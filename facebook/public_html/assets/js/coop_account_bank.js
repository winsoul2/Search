$(document).ready(function() {
    $(".edit_btn").click(function() {
        id = $(this).attr('data_id');
        $.ajax({
            url: base_url+'/setting_account_data2/ajax_get_bank_data_by_id',
            method: 'POST',
            data: {
                'id': id,
            },
            success: function(res){
                result = JSON.parse(res);
                data = result.data

                $("#id").val(id);
                $("#bank_code").val(data.account_bank);
                $("#account_bank_number").val(data.account_bank_number);
                $("#account_chart_id").val(data.account_chart_id);
                $("#add_account_modal").modal("show");
            }
        });
    });
    $(".del_btn").click(function() {
        id = $(this).attr('data_id');
        swal({
            title: "คุณต้องการที่จะลบ",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ลบ',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: base_url+'/setting_account_data2/delete_account_bank',
                    method: 'POST',
                    data: {
                        'id': id,
                    },
                    success: function(res){
                        document.location.href = base_url+'/setting_account_data2/bank';
                    }
                });
            } else {
                $("#add_account_modal").modal("close");
            }
        });

    });
    $("#submit_btn").click(function() {
        warning_msg = "";
        if(!$("#account_bank_number").val()) {
            warning_msg += " - เลขที่บัญชี";
        }

        if(warning_msg == "") {
            $("#form1").submit();
        } else {
            swal('กรุณากรอกข้อมูล', warning_msg, 'warning');
        }
    });
    $("#modal_cancel_btn").click(function() {
        $("#add_account_modal").modal("hide");
    });
});

function add_bank_account() {
    $("#id").val("");
    $("#bank_code").val("");
    $("#account_bank_number").val("");
    $("#account_chart_id").val("");
    $("#add_account_modal").modal("show");
}