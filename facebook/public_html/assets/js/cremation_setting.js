$( document ).ready(function() {
    $("#start_date").datepicker({
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
        //startDate: '+1d', //ใสวันที่เริ่มต้นแสดงในปฎิทิน
        autoclose: true,
    });

    finance_collect_type_change()
    $(".finance_collect_type").change(function() {
        finance_collect_type_change()
    });

    finance_period_type_change()
    $(".finance_period_type").change(function() {
        finance_period_type_change()
    });

});

function submit_form(){
    $('#form1').submit();
}

function go_back(){
    location.href = 'cremation_data_detail?cremation_id=2';
}

$(document).on('input', '.check_number', function () {
    this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
});

function finance_collect_type_change() {
    if($('input[name=finance_collect_type]:checked').val() == 1) {
        $(".advance_pay_div").show();
        $(".finance_amount_div").hide();
    } else {
        $(".advance_pay_div").hide();
        $(".finance_amount_div").show();
    }
}

function finance_period_type_change() {
    if($('input[name=finance_period_type]:checked').val() == 1) {
        $(".dividend_deduction_div").hide();
        $('#dividend_deduction').attr('checked', false);
    } else {
        $(".dividend_deduction_div").show();
    }
}