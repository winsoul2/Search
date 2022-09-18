var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
    $(".mydate").datepicker({
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
});

function change_loan_type(id){
    // var link_to = '';
    // link_to =  base_url+'/report_loan_data/coop_report_loan_emergent_preview';
    // $('#form'+id).attr('action', link_to);

    var changeon = $("#loan_type"+id).val();
    console.log("changeon", id+" = "+changeon);


    $("input[type='checkbox']").attr('checked', false);
    if(id==1){
        if(changeon==7){
            $(".box_loan_name_1_8").addClass("hide");
            $(".box_loan_name_1_9").addClass("hide");
            $(".box_loan_name_1_10").addClass("hide");
        }else if(changeon==8){
            $(".box_loan_name_1_7").addClass("hide");
            $(".box_loan_name_1_9").addClass("hide");
            $(".box_loan_name_1_10").addClass("hide");
        }else if(changeon==9){
            $(".box_loan_name_1_7").addClass("hide");
            $(".box_loan_name_1_8").addClass("hide");
            $(".box_loan_name_1_10").addClass("hide");
        }else if(changeon==10){
			$(".box_loan_name_1_7").addClass("hide");
			$(".box_loan_name_1_8").addClass("hide");
            $(".box_loan_name_1_9").addClass("hide");
		}
        $(".box_loan_name_1_"+changeon).removeClass("hide");
    }else if(id==2){
        if(changeon==7){
            $(".box_loan_name_2_8").addClass("hide");
            $(".box_loan_name_2_9").addClass("hide");
            $(".box_loan_name_2_10").addClass("hide");
        }else if(changeon==8){
            $(".box_loan_name_2_7").addClass("hide");
            $(".box_loan_name_2_9").addClass("hide");
            $(".box_loan_name_2_10").addClass("hide");
        }else if(changeon==9){
            $(".box_loan_name_2_7").addClass("hide");
            $(".box_loan_name_2_8").addClass("hide");
            $(".box_loan_name_2_10").addClass("hide");
        }else if(changeon==10){
            $(".box_loan_name_2_7").addClass("hide");
            $(".box_loan_name_2_8").addClass("hide");
            $(".box_loan_name_2_9").addClass("hide");
        }
        $(".box_loan_name_2_"+changeon).removeClass("hide");
    }else if(id==3){
        if(changeon==7){
            $(".box_loan_name_3_8").addClass("hide");
            $(".box_loan_name_3_9").addClass("hide");
            $(".box_loan_name_3_10").addClass("hide");
        }else if(changeon==8){
            $(".box_loan_name_3_7").addClass("hide");
            $(".box_loan_name_3_9").addClass("hide");
            $(".box_loan_name_3_10").addClass("hide");
        }else if(changeon==9){
            $(".box_loan_name_3_7").addClass("hide");
            $(".box_loan_name_3_8").addClass("hide");
            $(".box_loan_name_3_10").addClass("hide");
        }else if(changeon==10){
            $(".box_loan_name_3_7").addClass("hide");
            $(".box_loan_name_3_8").addClass("hide");
            $(".box_loan_name_3_9").addClass("hide");
        }
        $(".box_loan_name_3_"+changeon).removeClass("hide");
    }
}

function check_empty(type){
    var report_date = '';
    var month = '';
    var year = '';
    var loan_type = $('#loan_type'+type).val();
    var sum=0;
    var loan_name=[];
    for (var i = 0; i < form1.elements.length; i++) {
        var chk = form1.elements[i];
        if ( chk.type == 'checkbox' && chk.checked )
        {
            console.log(chk.value);
            loan_name.push(chk.value);
        };
    }
    console.log('sum', loan_name)

    if(type == '1'){
        report_date = $('#report_date').val();
    }else if(type == '2'){
        month = $('#report_month').val();
        year = $('#report_year').val();
    }else{
        year = $('#report_only_year').val();
    }
    $.ajax({
        url: base_url+'/report_loan_data/check_report_loan',
        method:"post",
        data:{
            report_date: report_date,
            month: month,
            year: year,
            loan_type: loan_type,
            loan_name: loan_name
        },
        dataType:"text",
        success:function(data){
            //console.log(data); return false;
            if(data == 'success'){
                if(month!='' || report_date!=''){
                    console.log("SIBMIT", $("#form"+type).attr('action'));
                    $('#form'+type).submit();
                }else{
                    console.log("SIBMIT", $("#form"+type).attr('action'));
                    $('#form'+type).submit();
                    // console.log("ERR");
                    // console.log('coop_report_loan_emergent_preview?report='+type+'&loan_type='+loan_type+'&year='+year);
                    // window.open('coop_report_loan_emergent_preview?report='+type+'&loan_type='+loan_type+'&year='+year,'_blank');
                    //window.open('coop_report_loan_normal_preview?loan_type='+loan_type+'&year='+year,'_blank');
                    //window.open('coop_report_loan_normal_preview?loan_type='+loan_type+'&year='+year+'&second_half=1','_blank');

                    //window.open('coop_report_loan_normal_excel?loan_type='+loan_type+'&year='+year,'_blank');
                    //window.open('coop_report_loan_normal_excel?loan_type='+loan_type+'&year='+year+'&second_half=1','_blank');
                }
            }else{
                $('#alertNotFindModal').appendTo("body").modal('show');
            }
        }
    });

}

function check_loan_person_empty(){
    var month = '';
    var year = '';
    var loan_type = $('#loan_type').val();
    var member_id = $('#member_id').val();

    if($('#check_loan_debt').is(':checked')){
        var check_loan_debt = '1';
    }else{
        var check_loan_debt = '';
    }

    month = $('#report_month').val();
    year = $('#report_year').val();


    if(member_id.trim() == ''){
        swal("กรุณากรอกรหัสสมาชิก");
    }else if(month !='' && year ==''){
        swal("กรุณาเลือกปี");
    }else{
        $.ajax({
            url: base_url+'/report_loan_data/check_report_loan_person',
            method:"post",
            data:{
                member_id: member_id,
                check_loan_debt: check_loan_debt,
                month: month,
                year: year,
                loan_type: loan_type
            },
            dataType:"text",
            success:function(data){
                //console.log(data); return false;
                if(data == 'success'){
                    $('#form1').submit();
                }else{
                    $('#alertNotFindModal').appendTo("body").modal('show');
                }
            }
        });
    }
}
