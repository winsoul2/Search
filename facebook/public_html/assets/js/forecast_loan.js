const period_type = $('#period_type');
const period_in = $("#period");
const period_amount_bath = $("#period_amount_bath");
const loan_amount  = $("#loan");
const interest  = $("#interest");
const pay_type  = $("#pay_type");
const estimate_receive_money  = $("#estimate_receive_money");
const estimate_interest  = $("#estimate_interest");
const estimate_principle  = $("#estimate_principle");

$(document).ready(function(e){
    if(period_type.val() === "1"){
        console.log("yedd!");
        period_amount_bath.hide();
        period_in.show();
    }else{
        period_amount_bath.show();
        period_in.hide();
    }
});

function format_the_number_decimal(ele){
    var value = $(ele).val();
    value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    var num = value.split(".");
    var decimal = '';
    var num_decimal = '';
    if(typeof num[1] !== 'undefined'){
        if(num[1].length > 2){
            num_decimal = num[1].substring(0, 2);
        }else{
            num_decimal =  num[1];
        }
        decimal =  "."+num_decimal;

    }

    if(value!=''){
        if(value == 'NaN'){
            $('#'+ele.id).val('');
        }else{
            value = (num[0] == '')?0:parseInt(num[0]);
            value = value.toLocaleString()+decimal;
            $('#'+ele.id).val(value);
        }
    }else{
        $('#'+ele.id).val('');
    }
}

$(document).on("change", "#period_type", function(){
    if($(this).val() === "1"){
        period_amount_bath.hide();
        period_in.show();
    }else{
        period_amount_bath.show();
        period_in.hide();
    }
});

function calc_loan(){
    const dayOfYear = new Date().getFullYear() % 4 === 0 ? 366 : 365;

    let loan_amt = parseFloat(loan_amount.val().split(",").join(""));
    let int_amt = parseFloat(interest.val().split(",").join(""));
    let money_period = parseFloat(period_amount_bath.val().split(",").join(""));
    let period =  parseInt(period_in.val().split(",").join(""));
    let calc_int = loan_amt*int_amt/100;
    let loan_all  = loan_amt+calc_int;
    let period_amt = 0;
    let int = 0;
    let principle = 0;

    int = Math.round(calc_int*31/dayOfYear);

    if(period_type.val() === "1"){
        if(pay_type.val() === "2") {
            //period_amt = Math.ceil((loan_amt/period)/10)*10;

            period_amt = (loan_amt * ( (int_amt/100) / 12 ))/( 1-Math.pow(1/(1+( (int_amt/100) /12)),period));
            period_amt = Math.round(period_amt);

            //principle
            principle = period_amt-int;
        }else{
            principle = Math.ceil(loan_amt/period/10)*10;
            period_amt = principle + int;
        }

    }else{
        period_amt = money_period;
        let _period = Math.ceil((loan_amt/money_period)/ 10)*10;
        let _principle = Math.ceil((loan_amt/_period)/ 10)*10;
        if(pay_type.val() === "2"){
            principle = _principle - int;
        }else{
            principle = _principle;
            period_amt += int;
        }
    }

    period_amt = period_amt.toFixed(2);
    estimate_receive_money.val(period_amt.toLocaleString('en', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }));

    int = int.toFixed(2)
    estimate_interest.val(int.toLocaleString('en', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }));

    principle = principle.toFixed(2);
    estimate_principle.val(principle.toLocaleString('en', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }));

}
