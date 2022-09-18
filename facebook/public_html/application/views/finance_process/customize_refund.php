<style>
	.datepick {
		text-align: center;
		width: 150px !important;
	}
	#table_process_return {
		margin-top: 30px;
	}
	#table_process_return thead tr th {
		text-align: center !important;
	}
	#table_process_return tbody tr td {
		text-align: center;
	}
	#table_process_return tbody tr td:first-child {
		text-align: left;
	}
</style>
<div class="layout-content">
	<div class="layout-content-body">
        <h1 style="margin-bottom: 0"> เงินคืนดอกเบี้ย ฉ.ATM (CUSTOMIZE) </h1>
        <form action="<?=base_url('finance_process/save_customize_refund');?>" method="POST" id="form_list">
		<div class="panel panel-body col-xs-12 col-sm-12 col-md-12 col-lg-12 " >
            <div class="row"><div class="col-md-12"><br></div></div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0" id="breadcrumb">
                
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                    <a class="btn btn-primary btn-lg bt-add" style="margin-left: 15px" href="<?=base_url('finance_process/customize_refund_excel')?>">
                        <span class="fa fa-file-excel-o"></span> Excel
                    </a>

                    <a class="btn btn-primary btn-lg bt-add" id="btn-show-return-manual" onclick="submit()">
                        <span class="icon icon-hand-paper-o"></span> คืนเงิน
                    </a>
                </div>
            </div>
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-sm-offset-2">
					<table class="table" id="table_process_return">
						<thead>
							<tr>
                                <th>#</th>
                                <th><a href="#" id="select_all">เลือกทั้งหมด</a></th>
								<th>รหัสมาชิก</th>
								<th>สัญญาเงินกู้</th>
								<th>ชื่อ</th>
								<th>สกุล</th>
								<!-- <th>รวมดอกเบี้ยที่จ่ายจริง</th>
								<th>รวมดอกเบี้ยที่คำนวณ</th>
                                <th>คืน/เก็บเพิ่ม</th> -->
                                <th>แก้ไขคืนเงิน</th>
							</tr>
						</thead>
						<tbody>
                            
                            <?php
                            $total = 0;
                            $c = 1;
                            
                                foreach ($list as $key => $value) {
                                    $member_id = sprintf('%06d', $value['A']);
                                    $contract_number = $value['B'];
                                    $loan_atm_id = @$this->db->get_where("coop_loan_atm", array(
                                        "contract_number like " => $contract_number,
                                        "member_id" => $member_id
                                    ))->result_array()[0]['loan_atm_id'];
                                    $sql = "SELECT ret_id
                                            FROM coop_process_return
                                            WHERE return_type = 4
                                                AND member_id = '{$member_id}'
                                                AND loan_atm_id = '{$loan_atm_id}'
                                                AND return_year = 2019
                                                AND return_month = 4
                                                ";
                                    $rs_chk = $this->db->query($sql)->result_array();
                                    if($rs_chk){
                                        continue;
                                    }
                    
                                    $total += $value['H'];

                                    ?>
                                        <tr>
                                            <td><?=$c++?></td>
                                            <td>
                                                
                                                <input type="checkbox" name="member_id[]" class="form-control member_list" style="height: 20px !important;" value="<?=sprintf('%06d', $value['A'])?>">
                                                <input type="hidden" value="<?=$value['B']?>" name="contract_number[<?=sprintf('%06d', $value['A'])?>]">
                                                <input type="hidden" value="<?=$value['H']?>" name="return_interest[<?=sprintf('%06d', $value['A'])?>]">
                                            </td>
                                            <td><?=sprintf('%06d', $value['A'])?></td>
                                            <td><?=$value['B']?></td>
                                            <td><?=$value['C']?></td>
                                            <td><?=$value['D']?></td>
                                            <!-- <td><?=number_format($value['E'],2)?></td>
                                            <td><?=number_format($value['F'],2)?></td>
                                            <td><?=number_format($value['G'],2)?></td> -->
                                            <td style="background-color: #43a0473b;text-align: right;"><?=number_format($value['H'],2)?></td>
                                        </tr>
                                    <?php
                                }

                                if($total==0){
                                    ?>
                                        <tr>
                                            <td colspan="7">ไม่มีรายการคืน</td>
                                        </tr>
                                    <?php
                                }
                            ?>
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" style="color: #FFF;color: #FFF;font-size: 20px;">รวม</td>
                                <td style="color: #FFF;text-align: right;font-size: 20px;"><?=number_format($total,2)?></td>
                            </tr>
                        </tfoot>
					</table>
				</div>
			</div>
            
            <div class="row"><div class="col-md-12"><br></div></div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0" id="breadcrumb">
                
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                    <a class="btn btn-primary btn-lg bt-add" id="btn-show-return-manual" onclick="submit()">
                        <span class="icon icon-hand-paper-o"></span> คืนเงิน
                    </a>
                </div>
            </div>
        </div>
        </form>
	</div>
</div>
<?php
	$link = [
		'src' => PROJECTJSPATH.'assets/js/process_return.js?v='.date("Ymdhi"),
		'type' => 'text/javascript'
	];
    echo script_tag($link);
    
?>

<script>
var select = false;
$( "#select_all" ).click(function() {
    select = !select;
    $('.member_list').prop('checked', select);
    
});

function submit(){
    var r = confirm("ยืนยัน");
    if (r == true) {
        $("#form_list").submit();
    } else {
    }
    
}
</script>