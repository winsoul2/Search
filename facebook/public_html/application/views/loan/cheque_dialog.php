<style type="text/css">
	.btn-small{
		width: 64px;
	}
	.card-item{
		margin: 5px auto;
	}
</style>
<div class="g24-col-sm-24 multi-cheque-contain horizontal-scroll">
		<div class="g24-col-sm-24 card-container">
			<?php
			if(isset($cheque_list) && sizeof(@$cheque_list)){
			foreach ($cheque_list as $index => $cheque) { ?>
			<div class="card-item g24-col-sm-24">
				<div class="g24-col-sm-2 control-label">
					ลำดับที่ <?php echo $index+1; ?>
				</div>
				<div class="g24-col-sm-6">
					<input class="form-control cheque-receiver" name="receiver" placeholder="ระบุชื่อผู้รับเงิน/สถาบันการเงิน" value="<?php echo @$row_member['firstname_th'].' '.@$row_member['lastname_th'] ?>">
				</div>
				<div class="g24-col-sm-5">
					<input class="form-control cheque-amount" placeholder="จำนวนเงิน" onblur="format_the_number_decimal(this)" name="cheque[<?php echo $index; ?>][amount]" value="<?php echo number_format($cheque['amount'], 2)?>">
				</div>
				<div class="g24-col-sm-4">
					<button class="btn btn-primary btn-small add-btn-cheque" type="button" onclick="addCheque()">
						<span class="fa fa-plus">
					</button>
				</div>
			</div>
			<?php }
			} else { ?>
				<div class="card-item g24-col-sm-24">
					<div class="g24-col-sm-2 control-label">
						ลำดับที่ 1
					</div>
					<div class="g24-col-sm-6">
						<input class="form-control cheque-receiver" name="cheque[0][receiver]" placeholder="ระบุชื่อผู้รับเงิน/สถาบันการเงิน" value="<?php echo @$row_member['firstname_th'].' '.@$row_member['lastname_th'] ?>">
					</div>
					<div class="g24-col-sm-5">
						<input class="form-control cheque-amount" placeholder="จำนวนเงิน" onblur="format_the_number_decimal(this)" name="cheque[0][amount]" readonly>
					</div>
					<div class="g24-col-sm-4">
						<button class="btn btn-primary btn-small add-btn-cheque" type="button" onclick="addCheque()">
						<span class="fa fa-plus">
						</button>
					</div>
				</div>
			<?php } ?>
		</div>
</div>
<script type="application/javascript">

	var _interval = null;
	var _cheque_editor = null;

	$("#normal_loan").on('shown.bs.modal',function(){
		runInterval();
	}).on('hidden.bs.modal', function(){
		stopInterval();
	});

	function runInterval(){
		if(_interval !== null) clearInterval(_interval);
		_interval = setInterval(find_estimate_receive_money, 3000);
		if(_cheque_editor !== null) clearInterval(_cheque_editor);
		_cheque_editor = setInterval(find_load_data_editor, 1000)
	}

	function stopInterval(){
		if(_interval !== null) clearInterval(_interval);
		if(_cheque_editor !== null) clearInterval(_cheque_editor);
	}

	function find_load_data_editor(){
		if (_cheque_editor !== null && $.isEmptyObject(edit_data) === false) clearInterval(_cheque_editor);
		prepare_cheque_editor();
	}

	//must be call in interval
	function prepare_cheque_editor(){
		if($.isEmptyObject(edit_data) === false){
			const object = $.isEmptyObject(edit_data.coop_loan_cheque) ? {} : edit_data.coop_loan_cheque;
			if($.isEmptyObject(object) === false) {
				$('.card-item').remove();
				$.each(object, function(key, value){
					addCheque();
					const row = $(`.card-item:eq(${key})`);
					if(key === 0){
						row.find('.cheque-amount').prop('readonly', true);
						if(object.length > 1){
							row.find('.add-btn-cheque').remove();
						}
					}
					row.find('.cheque-receiver').val(value.receiver);
					row.find('.cheque-amount').val(format_number(value.amount));
				});
				if(_cheque_editor !== null){
					setTimeout(auto_update_balance, 2500);
					clearInterval(_cheque_editor);
					_cheque_editor = null;
				}
			}
		}
	}

	function find_estimate_receive_money(){
		const estimate = parseFloat(removeCommas($('#estimate_receive_money').val()));
		if(estimate === "undefined" || estimate === "" || estimate === null || isNaN(estimate)) return false;
		if($('.card-item:eq(0)').find('.cheque-amount').val() !== ""){
			if (_interval !== null ) clearInterval(_interval);
		}
		const length = $('.card-item').length;
		const pair = estimate/length;
		let all = estimate;
		$('.card-item').each(function(index){
			if($(this).find('.cheque-amount').val() === "") {
				if (length - 1 === index) {
					$(this).find('.cheque-amount').val(format_number(all));
				} else {
					all = all - pair;
					$(this).find('.cheque-amount').val(format_number(pair));
				}
			}
		});
		if(_interval !== null) clearInterval(_interval);
	}

	function addCheque(){
		const i = $('.card-item').length;
		const number = i+1;

		let btnDel = `\t\t\t\t\t<button class="btn btn-danger btn-small btn-delete" type="button" onclick="deleteCheque(this)">
						\t\t\t\t\t\t<span class="fa fa-trash">
						\t\t\t\t\t</button>`;

		let template = `\t\t\t<div class="card-item g24-col-sm-24">
						\t\t\t\t<div class="g24-col-sm-2 control-label">
						\t\t\t\t\tลำดับที่ ${number}
						\t\t\t\t</div>
						\t\t\t\t<div class="g24-col-sm-6">
						\t\t\t\t\t\t<input class="form-control cheque-receiver" name="cheque[${i}][receiver]" placeholder="ชื่อผู้รับเงิน/สถาบันการเงิน">
						\t\t\t\t</div>
						\t\t\t\t<div class="g24-col-sm-5">
						\t\t\t\t\t<input class="form-control cheque-amount" placeholder="จำนวนเงิน" onblur="format_the_number_decimal(this); auto_update_balance()" name="cheque[${i}][amount]">
						\t\t\t\t</div>
						\t\t\t\t<div class="g24-col-sm-2">
						\t\t\t\t\t<button class="btn btn-primary btn-small add-btn-cheque" type="button" onclick="addCheque()">
						\t\t\t\t\t\t<span class="fa fa-plus">
						\t\t\t\t\t</button>
						\t\t\t\t</div>
						\t\t\t</div>`;

		if(i >= 4) {
			swal(`แจ้งเตือน!`,`ไม่สามารถแยกจ่ายเช็คได้มากกว่า ${i} ใบ`, `warning`);
		}else {
			$(document).find('.add-btn-cheque').replaceWith(btnDel);
			$(document).find(`.card-item:eq(0) .btn-delete`).remove();
			$(document).find(`.card-item:eq(${i-1}) .btn-delete:eq(1)`).remove();
			$(document).find(`.card-container`).append(template);
			$(document).find(`.card-item:eq(${i})`).append(btnDel);
		}
	}

	const addBtn = `\t\t\t\t<div class="g24-col-sm-2">
					\t\t\t\t\t<button class="btn btn-primary btn-small add-btn-cheque" type="button" onclick="addCheque()">
					\t\t\t\t\t\t<span class="fa fa-plus">
					\t\t\t\t\t</button>
					\t\t\t\t</div>`;

	function deleteCheque(ele) {

		const index = $(ele).closest('.card-item').index();
		$(`.card-item:eq(${index})`).remove();
		$(`.card-item`).each(function (index) {
			$(this).find('.control-label').text(`ลำดับที่ ${index + 1}`);
			// $(this).find('.cheque-number').attr("name", `cheque[${index}][cheque_number]`);
			$(this).find('.cheque-receiver').attr("name", `cheque[${index}][receiver]`);
			$(this).find('.cheque-amount').attr("name", `cheque[${index}][amount]`);

			if($(`.card-item`).length-1 === index){
				if($(this).find('.add-btn-cheque').hasClass('add-btn-cheque') === false) {
					$(this).find(".cheque-amount").closest(".g24-col-sm-5").after(addBtn);
				}
			}
		});
		setTimeout(auto_update_balance, 800);
	}

	function auto_update_balance(){
		const contain = $('.card-item');
		const display = $('.display#estimate-money');
		let est = $('#estimate_receive_money').val();
		est = est === "" || est === "undefined" ? "0" : est;
		const estimate_value = parseFloat(removeCommas(est));
		if(contain.length > 1){
			let amount = 0;
			contain.each(function(i){
				if(i !== 0){
					const amt = $(this).find('.cheque-amount').val();
					let val = amt === "" ? "0" : $(this).find('.cheque-amount').val() ;
					amount += parseFloat(removeCommas(val));
				}
				if(contain.length-1 === i){
					if((estimate_value-amount) < 0){
						swal('แจ้งเตือน!', 'กรุณาตรวจสอบยอดเงิน');
						contain.each(function(k){
							if(k === 0){
								$(this).find('.cheque-amount').val(format_number(estimate_value));
							}else{
								$(this).find('.cheque-amount').val(0);
							}
						});
					}else {
						contain.eq(0).find('.cheque-amount').val(format_number(estimate_value - amount));
						display.val(format_number(estimate_value - amount));
					}
				}
			});

		}else{
			contain.eq(0).find('.cheque-amount').val(format_number(estimate_value));
			display.val(format_number(estimate_value));
		}

	}
</script>
