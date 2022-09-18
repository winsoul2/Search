<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.left {
				text-align: left;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			.modal-dialog-data {
				width:90% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal-dialog-cal {
				width:80% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal-dialog-file {
				width:50% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal_data_input{
				margin-bottom: 5px;
			}
			.form-group{
				margin-bottom: 5px;
			  }
			  .red{
				color: red;
			  }
			  .green{
				color: green;
			  }
            .warm {
                background-color: antiquewhite !important;
            }
            .normal {
                background-color: unset !important;
            }
            .point{
                cursor: pointer;
            }
		</style> 
		<div class="row">
			<div class="form-group">
				<div class="col-sm-6">
					<h1 class="title_top">แก้ไขรายการเคลื่อนไหวสินเชื่อ</h1>
					<?php $this->load->view('breadcrumb'); ?>
				</div>
				<div class="col-sm-6">
				</div>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" id="panel-body" style="padding-top:0px !important;">
                    <div id="content-panel-body">
                        <!-- info -->
                        <div class="row">
                            <div class="col-md-offset-2 col-md-3">
                                <h3><b>รหัสสมาชิก</b></h3>
                            </div> 
                            <div class="col-md-3">
                                <h3><?=@$member['member_id']?></h3>
                            </div> 
                        </div> 
                        <div class="row">
                            <div class="col-md-offset-2 col-md-3">
                                <h3><b>ชื่อ - สกุล</b></h3>
                            </div> 
                            <div class="col-md-3">
                                <h3><?=@$member['prename_short']." ".@$member['firstname_th']." ".@$member['lastname_th']?></h3>
                            </div> 
                        </div> 
                        <div class="row">
                            <div class="col-md-offset-2 col-md-3">
                                <h3><b>รหัสสัญญาเงินกู้</b></h3>
                            </div> 
                            <div class="col-md-3">
                                <h3><?=@$loan['contract_number']?></h3>
                            </div> 
                        </div> 
                        <div class="row">
                            <div class="col-md-offset-2 col-md-3">
                                <h3><b>วงเงินกู้</b></h3>
                            </div> 
                            <div class="col-md-3">
                                <h3><?=@number_format($loan['loan_amount'], 2);?></h3>
                            </div> 
                            <div class="col-md-1">
                                <h3><b>บาท</b></h3>
                            </div> 
                        </div> 
                        <div class="row">
                            <div class="col-md-offset-2 col-md-3">
                                <h3><b>คงเหลือ</b></h3>
                            </div> 
                            <div class="col-md-3">
                                <h3><?=@number_format($loan['loan_amount_balance'], 2);?></h3>
                            </div> 
                            <div class="col-md-1">
                                <h3><b>บาท</b></h3>
                            </div> 
                        </div> 
                        <!-- loan_transaction -->
                        <hr>
                        <div class="row">
                            <div class="col-md-1 text-center">
                                <h3><b>ลำดับ</b></h3>
                            </div> 
                            <div class="col-md-2 text-center">
                                <h3><b>วันที่ทำรายการ</b></h3>
                            </div>
                            <div class="col-md-2 text-center">
                                <h3><b>เลขใบเสร็จ</b></h3>
                            </div> 
                            <div class="col-md-1 text-center">
                                <h3><b>เงินต้น</b></h3>
                            </div>
                            <div class="col-md-1 text-center">
                                <h3><b>ดอกเบี้ย</b></h3>
                            </div>
                            <div class="col-md-2 text-center">
                                <h3><b>คงเหลือ</b></h3>
                            </div> 
                            <div class="col-md-3 text-center">
                                <h3><b>จัดการ</b></h3>
                            </div> 
                            
                        </div> 
                        <?php
                            $loan_amount = $loan['loan_amount'];
                            foreach($loan_transaction as $key => $value){
                                $loan_amount -= $value['principal'];
                                $class_css = "normal";
                                $invalid = false;
                                $token = sha1(md5($value['loan_transaction_id']));
                                if($loan_amount!=$value['loan_amount_balance']){
                                    $invalid = true;
                                    $class_css = "warm";
                                    $info = '<i class="fa fa-exclamation" aria-hidden="true" style="color: red;" data-toggle="tooltip" data-placement="bottom" title="'.number_format($loan_amount+$value['principal'], 2).' - '.number_format($value['principal'], 2).' = '.number_format($loan_amount, 2).'"></i>';
                                }else{
                                    $info = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                                }

                                ?>
                                    <div class="row <?=$class_css?>" id="row_<?=$value['loan_transaction_id']?>">
                                        <div id="content_row_<?=$value['loan_transaction_id']?>">
                                        <div class="col-md-1">
                                            <h3><?=($key+1)?></h3>
                                        </div> 
                                        <div class="col-md-2">
                                            <h3><?=$value['transaction_datetime']?></h3>
                                        </div>
                                        <div class="col-md-2">
                                            <h3><a href="<?=base_url()?>/admin/receipt_form_pdf/<?=$value['receipt_id']?>" target="_blank"><?=$value['receipt_id']?></a></h3>
                                        </div> 
                                        <div class="col-md-1 text-right">
                                            <h3><?=number_format($value['principal'], 2)?></h3>
                                        </div>
                                        <div class="col-md-1  text-right">
                                            <h3><?=number_format($value['interest'], 2)?></h3>
                                        </div>
                                        <div class="col-md-2" style="padding-top: 20px;">
                                            <input type="text" class="form-control loan_transaction_balance numeral" id="bal_<?=$value['loan_transaction_id']?>" value="<?=number_format($value['loan_amount_balance'], 2)?>" data-default="<?=number_format($value['loan_amount_balance'], 2)?>" data-invalid="<?=$loan_amount?>">
                                        </div> 
                                        <div class="col-md-3">
                                            <h3>
                                                <?=$info?>
                                                <?php
                                                    if($invalid || 1==1){
                                                        ?>
                                                            <i class="fa fa-info-circle" aria-hidden="true"><small class="point" data-toggle="tooltip" data-placement="bottom" title="<?=number_format($loan_amount, 2)?>" onclick="set_val('bal_<?=$value['loan_transaction_id']?>', <?=$loan_amount?>)"> ใช้ค่าแนะนำ</small></i>
                                                            <button type="button" class="btn btn-success btn-xs" onclick="update_this(<?=$value['loan_id']?>, <?=$value['loan_transaction_id']?>, '<?=$token?>');">อัพเดทเฉพาะแถวนี้</button>
                                                            <button type="button" class="btn btn-primary btn-xs" onclick="update_after_this(<?=$value['loan_id']?>, <?=$value['loan_transaction_id']?>, '<?=$token?>');">อัพเดทตั้งแต่แถวนี้</button>
                                                            <button type="button" class="btn btn-danger btn-xs" onclick="delete_trash_transaction(<?=$value['loan_id']?>, <?=$value['loan_transaction_id']?>, '<?=$token?>');">ลบแถวนี้</button>
                                                        <?php
                                                    }
                                                ?>
                                                
                                            </h3>
                                        </div> 
                                        </div> 
                                    </div>
                                <?php
                            }
                        ?>
                    </div>
				</div>
			</div>
		</div>

		
	</div>
</div>

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();

    $("body").on('change', '.numeral', function(){    // 2nd (B)
        var default_val = numeral($(this).data("default")).value();
        console.log("Default", default_val);
        var val = numeral($(this).val()).value();
        $(this).val(numeral(val).format('0,0.00'));
    });

});

function set_val(id, val_to){
    console.log("set to", val);
    var val = numeral(val_to).value();
    $("#"+id).val(numeral(val).format('0,0.00'));
}

function update_this(loan_id, loan_transaction_id, token){
    console.log("update_this", loan_transaction_id);
    var bal = numeral($("#bal_"+loan_transaction_id).val()).value();
    $.ajax({
		url: base_url+'loan/update_loan_transaction_this_row',
		method: 'POST',
		data: {
			loan_id : loan_id,
            loan_transaction_id : loan_transaction_id,
            token : token,
            balance: bal
		},
		success: function(res){
            var obj = JSON.parse(res);
            $("#row_"+loan_transaction_id).load(window.location.href+" #content_row_"+loan_transaction_id, function (response, status, xhr) {
                var invalid = $("#bal_"+loan_transaction_id).data("invalid");
                var val = numeral($("#bal_"+loan_transaction_id).val()).value();
                if(invalid==val){
                    $("#row_"+loan_transaction_id).removeClass("warm");
                    $("#row_"+loan_transaction_id).addClass("normal");
                }
            });
		}
	});
}

function update_after_this(loan_id, loan_transaction_id, token){
    console.log("update_after_this", loan_transaction_id);
    var bal = numeral($("#bal_"+loan_transaction_id).val()).value();
    $.ajax({
		url: base_url+'loan/update_loan_transaction_after_this_row',
		method: 'POST',
		data: {
			loan_id : loan_id,
            loan_transaction_id : loan_transaction_id,
            token : token,
            balance: bal
		},
		success: function(res){
            var obj = JSON.parse(res);
            $("#panel-body").load(window.location.href+" #content-panel-body", function (response, status, xhr) {
                // var invalid = $("#bal_"+loan_transaction_id).data("invalid");
                // var val = numeral($("#bal_"+loan_transaction_id).val()).value();
                // if(invalid==val){
                //     $("#row_"+loan_transaction_id).removeClass("warm");
                //     $("#row_"+loan_transaction_id).addClass("normal");
                // }
            });
		}
	});
}

function delete_trash_transaction(loan_id, loan_transaction_id, token){
	const bal = numeral($("#bal_"+loan_transaction_id).val()).value();
	swal({
		title: "คุณแน่ใจใช่หรือไม่",
		text: "ถ้าลบข้อมูลนี้จะไม่สามารถกู้คืนได้",
		type: "warning",
		showCancelButton: true,
		confirmButtonClass: "btn-danger",
		confirmButtonText: "ใช่",
		cancelButtonText: "ไม่",
		closeOnConfirm: true,
		closeOnCancel: true
	}, function(isConfirm){
		if(isConfirm){
			$.post(base_url+'/loan/delete_trash_transaction', {
				loan_id: loan_id,
				loan_transaction_id: loan_transaction_id,
				token: token,
				balance: bal
			}, function(response, status, xhr){
				console.log('On Deleted: ', response);
				$("#panel-body").load(window.location.href+" #content-panel-body", function (response, status, xhr) {

				});
			});
		}
	});

}

</script>
