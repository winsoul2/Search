var base_url = $('#base_url').attr('class');

function check_empty(){
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    $.get(base_url+'/report_cremation_data/check_report_pay',
        {
            start_date: start_date,
            end_date: end_date
        },function(data){
            if(data.status === true){
                $('#form1').submit();
            }else{
                swal('ไม่มีข้อมูลในวันที่เลือก', '', 'warning');
            }
        });
}

$( document ).ready(function() {
    var firstPicker = $("#start_date").datepicker({
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
        endDate: new Date(),
        autoclose: true
    });

    var secondPicker = $('#end_date').datepicker({
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
        endDate: new Date(),
        autoclose: true
    });


    firstPicker.on('changeDate', function(ev, i){
        secondPicker.datepicker('setStartDate',  moment(ev.date).format('DD-MM-YYYY'));
    });

    secondPicker.on('changeDate', function(ev, i){
        firstPicker.datepicker('setEndDate',  moment(ev.date).format('DD-MM-YYYY'));
    })
});
