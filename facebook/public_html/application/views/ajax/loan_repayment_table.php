<table class="table table-bordered table-striped table-center">
    <thead> 
		<tr class="bg-primary">
			<th>ครั้งที่รับ</th>
			<th>วงเงินที่รับ</th>
			<th>วันที่รับเงิน</th>
			<th width="30%">การรับเงิน</th>
            <th>สถานะ</th>
			<th>ผู้ทำรายการ</th>
            <th width='100'>ลบ</th>
		</tr>
	</thead>
	<tbody>
        <?php
            $transfer_type = ["เงินสด", "เงินโอนเข้าบัญชีสหกรณ์", "เงินโอนเข้าบัญชีธนาคารอื่นๆ"];
            // var_dump($loan_repayment);
            if($loan_repayment){
                foreach ($loan_repayment as $key => $value) {
                    ?>
                        <tr>
                            <td><?=$value->seq?></td>
                            <td class="text-right"><?=number_format($value->loan_request,2 )?></td>
                            <td><?=$this->center_function->ConvertToThaiDate($value->transaction_time,1,1)?></td>
                            <td class="text-left">
                            <strong><?= $transfer_type[$value->transfer_type-1]?></strong>
                            &emsp;
                            <?php
                                if($value->transfer_type==2){
                                    echo $value->account_id;
                                }else if($value->transfer_type==3){
                                    echo "<br>ธนาคาร ".$value->bank_name . "&emsp; สาขา ". $value->branch_code . "&emsp; <br>เลขบัญชี " . $value->account_no;
                                }
                            ?>
                            </td>
                            <?php
                                $status = ["รอจ่ายเงิน", "จ่ายเงินแล้ว"];
                            ?>
                            <td><?=$status[$value->status]?></td>
                            <td><?=$value->user_name?></td>
                            <td>
                                <?php
                                    if($value->status==0){
                                        ?>
                                            <a href="#" onclick="remove_loan_repayment(<?=$value->id?>)">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        <?php
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php
                }
            }else{
                ?>
                    <tr><td colspan="9">ไม่พบข้อมูล</td></tr>
                <?php
            }
            


        ?>
		
	</tbody> 
</table>

<script>
 function remove_loan_repayment(id){
     console.log(id);
     swal({
			title: "ท่านต้องการลบข้อมูลใช่หรือไม่",
			text: "",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ลบ',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: false,
			closeOnCancel: true
		},
		function(isConfirm) {
			if (isConfirm) {			
				$.ajax({
					url: base_url+'/loan/remove_loan_repayment',
					method: 'POST',
					data: {
						'id': id,
					},
					success: function(msg){
                        console.log(msg);
						if(msg=="success"){
                            // swal("ลบสำเร็จ", "ลบรายการนี้เรียบร้อย"+window.location.origin + window.location.pathname + "/success/"+$("#loan_id").val(), "success");

                            window.location = window.location.origin + window.location.pathname + "/success/"+$("#loan_id").val()
                        }else{
                            swal('เกิดข้อผิดพลาด', 'ไม่สามารถลบรายการนี้ได้', 'warning');
                        }
					}
				});
			} else {
				
			}
		});		
 }
</script>