

$.fn.remove_comma = function(){
	this.val(this.val().split(',').join(''));
	return this;
}

$.fn.format_number = function (){
	this.val(parseFloat(this.val()).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,"));
	return this;
}

$.fn.float = function(){
	return parseFloat(this.val().split(",").join(""));
}

function format_number(n) {
    return parseFloat(n).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
}

function blockUI(){
    let block = '<div class="display-block"><div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>';
    if($('body .display-block').hasClass('display-block') === false) {
        $('body').prepend(block);
        $('.display-block').css({
            'width': '100%',
            'height': '100%',
            'position': 'fixed',
            'background-color': 'rgba(0, 0, 0, 0.4)',
            'top': 0,
            'display': 'flex',
            'justify-content': 'center',
            'align-items': 'center'
        }).addClass('zIndex');
    }
}

function unblockUI(sleep){
    sleep = typeof sleep === "undefined" ? 1000: sleep;
    setTimeout(function(){
        $('.display-block').remove();
    }, sleep);
}

$(".date_th").datepicker({
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

$('.timepicker').datetimepicker({
	format: 'HH:mm:ss',
	icons: {
        up: "icon icon-chevron-up",
        down: "icon icon-chevron-down",
    }
	//,debug:true
});
