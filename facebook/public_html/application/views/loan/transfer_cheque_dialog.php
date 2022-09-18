<style type="text/css">
	.btn-small{
		width: 64px;
	}
	.card-item{
		margin: 5px auto;
	}
</style>
<div class="g24-col-sm-24 multi-cheque-contain horizontal-scroll">
		<div class="g24-col-sm-offset-4 g24-col-sm-20 card-container">
		</div>
</div>
<script type="application/javascript">

	$.fn.remove_comma = function() {
		$(this).split(',').join('');
	}

	var _interval = null;
	var _cheque_editor = null;

	$("#transfer_modal").on('shown.bs.modal',function(){
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
		if (_cheque_editor !== null && $.isEmptyObject(_editor_cheque) === false) clearInterval(_cheque_editor);
		prepare_cheque_editor();
	}

	//must be call in interval
	function prepare_cheque_editor(){
		if($.isEmptyObject(_editor_cheque) === false){
			const object = $.isEmptyObject(_editor_cheque.coop_loan_cheque) ? {} : _editor_cheque.coop_loan_cheque;
			if($.isEmptyObject(object) === false) {
				console.log('coop_loan_cheque', object);
				$('.card-item').remove();
				$('#che_sec').remove();
				$.each(object, function(key, value){
					console.log(key, value);
					addCheque();
					const row = $(`.card-item:eq(${key})`);
					row.find('.cheque-id').val(value.cheque_id);
					row.find('.cheque-number').val(value.cheque_number);
					row.find('.cheque-amount').val(format_number(value.amount));
					row.find(`input[name="cheque[${key}][id]"]`).val(value.id);
				});
				if(_cheque_editor !== null){
					clearInterval(_cheque_editor);
					_cheque_editor = null;
				}
			}
		}
	}

	function find_estimate_receive_money(){
		const estimate = parseFloat(removeCommas($('#transfer_real_amount').val()));
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

		let btnDel = `\t\t\t\t\t<button class="btn btn-danger btn-small" type="button" onclick="deleteCheque(this)">
						\t\t\t\t\t\t<span class="fa fa-trash">
						\t\t\t\t\t</button>`;

		let template = `\t\t\t<div class="card-item g24-col-sm-24">
						\t\t\t\t<div class="g24-col-sm-3 checkbox-inline">
						\t\t\t\t\t\t<label class="control-label">
						\t\t\t\t\t\tลำดับที่ ${number}</label>
						\t\t\t\t</div>
						\t\t\t\t<div class="g24-col-sm-9">
						\t\t\t\t\t<select class="form-control cheque-id" name="cheque[${i}][cheque_id]">
						\t\t\t\t\t\t<option value="">ระบุเช็คธนาคาร</option>
						<?php foreach (@$cheque_bank_title as $key => $item) {?>
						\t\t\t\t\t\t<option value="<?php echo $item['cheque_id']; ?>"><?php echo $item['cheque_title']?></option>
						<?php } ?>
						\t\t\t\t\t</select>
						\t\t\t\t</div>
						\t\t\t\t<div class="g24-col-sm-6">
						\t\t\t\t\t<input class="form-control cheque-number" placeholder="หมายเลขเช็ค" name="cheque[${i}][cheque_number]">
						\t\t\t\t</div>
						\t\t\t\t<div class="g24-col-sm-6">
						\t\t\t\t\t<input class="form-control cheque-amount" placeholder="จำนวนเงิน" onblur="format_the_number_decimal(this)" name="cheque[${i}][amount]">
						\t\t\t\t\t<input type="hidden" name="cheque[${i}][id]">
						\t\t\t\t</div>
						\t\t\t</div>`;

		if(i >= 4) {
			swal(`แจ้งเตือน!`,`ไม่สามารถแยกจ่ายเช็คได้มากกว่า ${i} ใบ`, `warning`);
		}else {
			$(document).find('.add-btn-cheque').replaceWith(btnDel);
			$(document).find(`.card-container`).append(template);
		}
	}

	function deleteCheque(ele) {

		const index = $(ele).closest('.card-item').index();
		$(`.card-item:eq(${index})`).remove();
		$(`.card-item`).each(function (index) {
			$(this).find('.control-label').text(`ลำดับที่ ${index + 1}`);
			$(this).find('.cheque-id').attr("name", `cheque[${index}][cheque_id]`);
			$(this).find('.cheque-number').attr("name", `cheque[${index}][cheque_number]`);
			$(this).find('.cheque-amount').attr("name", `cheque[${index}][cheque_amount]`);
		});

	}

	function format_the_number_decimal(ele){
		var value = $(ele).val();
		if(value === "undefined" || value === "" || value === "NaN"){
			$(ele).val(format_number(0));
			return;
		}
		console.log(value);
		value = value.split(",").join("");
		$(ele).val(format_number(value));
	}

	function open_modal_transfer_cheque() {

	}
</script>
