
    function account_excel_tranction_voucher(user_officer_id,payment_date){
        $('#user_officer_id').val(user_officer_id);
        $('#payment_date').val(payment_date);
        $('#from_excel_day').submit();
    }

    function account_excel_tranction_voucher_result(user_officer_id,payment_date){
        $('#user_officer_id_result').val(user_officer_id);
        $('#payment_date_result').val(payment_date);
        $('#from_excel_day_result').submit();
    }