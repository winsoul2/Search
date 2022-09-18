<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			.modal-dialog-data {
				width:50% !important;
				margin:auto;
				margin-top:5%;
				margin-bottom:1%;
			}
			.modal_data_input{
				margin-bottom: 5px;
			}
		</style> 
<h1 class="title_top">ระบบปันผลเฉลี่ยคืน</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<a class="btn btn-primary btn-lg bt-add" onclick="open_modal('dividend_modal')">
			<span class="icon icon-plus-circle"></span>
			เพิ่มปันผลและเฉลี่ยคืน
		</a>
		<a class="btn btn-primary btn-lg bt-add" onclick="open_modal('expect_dividend_modal')" style="margin-right:20px;">
			<span class="icon icon-plus-circle"></span>
			ประมาณการณ์ปันผลและเฉลี่ยคืน
		</a>
	</div>
</div>
<div class="row gutter-xs">
	<div class="col-xs-12 col-md-12">
		<div class="panel panel-body">
			<div class="bs-example" data-example-id="striped-table">
				<table class="table table-bordered table-striped table-center">
					<thead> 
						<tr class="bg-primary">
							<th width="10%">ปี</th>
							<th width="10%">ปันผล</th>
							<th width="10%">เฉลี่ยคืน</th>
							<th width="10%">เงินของขวัญ</th>
							<th width="10%">สถานะ</th>
							<th></th>
						</tr> 
					</thead>
					<tbody>
					<?php
						$i=1;
						$status = array('รอการตรวจสอบ', 'อนุมัติ', 'ไม่อนุมัติ');
						foreach($data as $key => $row){
					?>
						<tr id="master-id-<?php echo $row['id']; ?>">
							<td><?php echo $row['year']; ?></td>
							<td><?php echo $row['dividend_percent']."%"; ?></td>
							<td><?php echo $row['average_percent']."%"; ?></td>
							<td><?php echo number_format($row['gift_varchar'], '2', '.', ","); ?></td>
							<td><?php echo $status[$row['status']]; ?></td>
							<td>
<!--                                <a target="_blank" href="--><?php //echo base_url(PROJECTPATH."/average_dividend/average_dividend_excel?master_id=".$row['id']."&year=".$row['year']); ?><!--">Export to Excel</a>-->
                               <?php if($row['status'] == 1){ ?>
                                   <a href="<?php echo base_url(PROJECTPATH . "/average_dividend/average_dividend_excel?master_id=" . $row['id'] . "&year=" . $row['year']); ?>">ทั้งหมด </a> | <a href="<?php echo base_url(PROJECTPATH . "/average_dividend/average_dividend_excel?type=transfer&master_id=" . $row['id'] . "&year=" . $row['year']); ?>">โอนสำเร็จ</a> | <a href="<?php echo base_url(PROJECTPATH . "/average_dividend/average_dividend_excel?type=no_transfer&master_id=" . $row['id'] . "&year=" . $row['year']); ?>">โอนเข้าต่างธนาคาร</a><?php if(isset($report_insure[$row['id']]) && $report_insure[$row['id']] === true){?>
                                   | <a href="<?php echo base_url(PROJECTPATH."/average_dividend/insure_transfer?id=".$row['id'])?>">รายงานการโอนเงินหลักประกัน</a>
                               <?php }else{ ?>
                                   | <a style="cursor: pointer" onclick="confirmInsure(<?php echo $row['id']; ?>)">โอนเงินหลักประกัน</a>
                               <?php } ?>

                                   | <a style="cursor: pointer" href="<?php echo base_url(PROJECTPATH."/average_dividend/receipt?id=".$row['id']);?>">ใบเสร็จ</a>
                                   | <a style="cursor: pointer" href="<?php echo base_url(PROJECTPATH."/average_dividend/receipt?type=copy&id=".$row['id']);?>">ใบเสร็จ (สำเนา)</a>

                                <?php }else{ ?>
                                 <a href="<?php echo base_url(PROJECTPATH."/average_dividend/management?id=".$row['id'])?>">จัดการ</a> | <a href="<?php echo base_url(PROJECTPATH . "/average_dividend/average_dividend_excel?master_id=" . $row['id'] . "&year=" . $row['year']); ?>">ทั้งหมด </a> | <a style="color: red; cursor: pointer" onclick="deleteAverate(<?php echo $row['id']; ?>)">ลบ</a>
                                <?php } ?>
                            </td>
						</tr>
					<?php } ?>
					</tbody> 
				</table>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<div class="modal fade" id="expect_dividend_modal" role="dialog">
    <div class="modal-dialog modal-dialog-data">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" onclick="close_modal('expect_dividend_modal')">&times;</button>
          <h2 class="modal-title" >ประมาณการณ์ปันผลและเฉลี่ยคืน</h2>
        </div>
        <div class="modal-body">
		<!--<form action="<?php echo base_url(PROJECTPATH.'/average_dividend/average_dividend_expect')?>" method="POST" target="_blank">
			<h2>กรณีที่ 1 </h2>
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-6 control-label">ปันผล</label>
					<div class="g24-col-sm-5" >
						<input class="form-control" name="dividend_percent[1]" onKeyPress="return chkNumber(this)" type="text" value="">
					</div>
					<label class="g24-col-sm-1 control-label">%</label>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-6 control-label">เฉลี่ยคืน</label>
					<div class="g24-col-sm-5" >
						<input class="form-control" name="average_percent[1]" onKeyPress="return chkNumber(this)" type="text" value="">
					</div>
					<label class="g24-col-sm-1 control-label">%</label>
				</div>
			</div>
			<h2>กรณีที่ 2 </h2>
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-6 control-label">ปันผล</label>
					<div class="g24-col-sm-5" >
						<input class="form-control" name="dividend_percent[2]" onKeyPress="return chkNumber(this)" type="text" value="">
					</div>
					<label class="g24-col-sm-1 control-label">%</label>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-6 control-label">เฉลี่ยคืน</label>
					<div class="g24-col-sm-5" >
						<input class="form-control" name="average_percent[2]" onKeyPress="return chkNumber(this)" type="text" value="">
					</div>
					<label class="g24-col-sm-1 control-label">%</label>
				</div>
			</div>
			<h2>กรณีที่ 3 </h2>
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-6 control-label">ปันผล</label>
					<div class="g24-col-sm-5" >
						<input class="form-control" name="dividend_percent[3]" onKeyPress="return chkNumber(this)" type="text" value="">
					</div>
					<label class="g24-col-sm-1 control-label">%</label>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-6 control-label">เฉลี่ยคืน</label>
					<div class="g24-col-sm-5" >
						<input class="form-control" name="average_percent[3]" onKeyPress="return chkNumber(this)" type="text" value="">
					</div>
					<label class="g24-col-sm-1 control-label">%</label>
				</div>
			</div>
			<div class="g24-col-sm-24" style="padding-top:20px">
				<div class="form-group g24-col-sm-24">
					<div class="g24-col-sm-24" style="text-align:center;">
						<input class="btn btn-info btn-width-auto" type="submit" value="แสดงข้อมูลประมาณการณ์">
					</div>
				</div>
			</div>
		</form>-->
		
		<form action="<?php echo base_url(PROJECTPATH.'/average_dividend/calculate_data')?>" id="caldata" method="POST" target="_blank">
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-12 control-label">ประมาณการณ์ ปันผล</label>
					<div class="g24-col-sm-5" >
						<input class="form-control" name="dividend_percent" onKeyPress="return chkNumber(this)" type="text" value="">
					</div>
					<label class="g24-col-sm-1 control-label">%</label>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-12 control-label">ประมาณการณ์ เฉลี่ยคืน</label>
					<div class="g24-col-sm-5" >
						<input class="form-control" name="average_percent" onKeyPress="return chkNumber(this)" type="text" value="">
					</div>
					<label class="g24-col-sm-1 control-label">%</label>
				</div>
			</div>
            <div class="g24-col-sm-24">
                <div class="form-group g24-col-sm-24">
                    <label class="g24-col-sm-12 control-label">เงินของขวัญ</label>
                    <div class="g24-col-sm-5" >
                        <input class="form-control" name="money_gift" onKeyPress="return chkNumber(this)" type="text" value="" title="กรุณากรอก % เงินของขวัญ">
                    </div>
                    <label class="g24-col-sm-1 control-label">บาท</label>
                </div>
            </div>
			<div class="g24-col-sm-24" style="padding-top:20px">
				<div class="form-group g24-col-sm-24">
					<div class="g24-col-sm-24" style="text-align:center;">
					<!--<a target="_blank" href="<?php echo base_url(PROJECTPATH."/average_dividend/average_dividend_excel?master_id=".$row['id']."&year=".$row['year']); ?>"><input class="btn btn-info btn-width-auto" type="submit" value="แสดงข้อมูลประมาณการณ์"></a>-->
					<input class="btn btn-info btn-width-auto" type="button"  onclick="tests()" value="แสดงข้อมูลประมาณการณ์">
					</div>
				</div>
			</div>
	</form>

			<table><tr><td>&nbsp;</td></tr></table>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="dividend_modal" role="dialog">
    <div class="modal-dialog modal-dialog-data">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" onclick="close_modal('dividend_modal')">&times;</button>
          <h2 class="modal-title" >เพิ่มข้อมูลปันผลและเฉลี่ยคืน</h2>
        </div>
        <div class="modal-body">
		<form data-toggle="validator" id="myForm" action="<?php echo base_url(PROJECTPATH.'/average_dividend/save_data')?>" method="POST">
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-9 control-label">ปันผล</label>
					<div class="g24-col-sm-5" >
						<input class="form-control" name="dividend_percent1" onKeyPress="return chkNumber(this)" type="text" value="" required title="กรุณากรอก  % ปันผล">
					</div>
					<label class="g24-col-sm-1 control-label">%</label>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-9 control-label">เฉลี่ยคืน</label>
					<div class="g24-col-sm-5" >
						<input class="form-control" name="average_percent2" onKeyPress="return chkNumber(this)" type="text" value="" required title="กรุณากรอก % เฉลี่ยคืน">
					</div>
					<label class="g24-col-sm-1 control-label">%</label>
				</div>
			</div>
            <div class="g24-col-sm-24">
                <div class="form-group g24-col-sm-24">
                    <label class="g24-col-sm-9 control-label">เงินของขวัญ</label>
                    <div class="g24-col-sm-5" >
                        <input class="form-control" name="money_gift" onKeyPress="return chkNumber(this)" type="text" value="" required title="กรุณากรอก % เงินของขวัญ">
                    </div>
                    <label class="g24-col-sm-1 control-label">บาท</label>
                </div>
            </div>
			<div class="g24-col-sm-24" style="padding-top:20px">
				<div class="form-group g24-col-sm-24">
					<div class="g24-col-sm-24" style="text-align:center;">
						<input class="btn btn-info btn-width-auto" type="button"  onclick="check_form()" value="บันทึกข้อมูลปันผลเฉลี่ยคืน">
					</div>
				</div>
			</div>
		</form>
			<table><tr><td>&nbsp;</td></tr></table>
        </div>
      </div>
    </div>
</div>
<script>
	function open_modal(id){
		$('#'+id).modal('show');
	}

	function close_modal(id){
		$('#'+id).modal('hide');
	}

	function open_averate(){
	    $('#delaverate_modal').modal('show');
    }

	function chkNumber(ele){
		var vchar = String.fromCharCode(event.keyCode);
		if ((vchar<'0' || vchar>'9') && (vchar != '.')) return false;
		ele.onKeyPress=vchar;
	}
	
	function check_form(){
		$('#myForm').submit();
	}
	function calret()
	{
		$('#caldata').submit();
	}

	function deleteAverate(master){
        swal({
            title: "ลบข้อมูล",
            text: "ท่านกำลังลบข้อมูลแน่ใจหรือไม่",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function () {
            $.post("/average_dividend/delete", {master_id : master}, function(res){
                if(res.status === true) {
                    swal("ข้อมูลของท่านถูกลบแล้ว! ");
                    $("#master-id-" + master).remove();
                }else{
                    swal("ไม่สามารถลบข้อมูลได้!", "เนื่องจากเกิดข้อผิดพลาดบางอย่าง", "error");
                }
            });
        });
    }

    function confirmInsure(master){
        swal({
            title: "โอนเงินหลักประกัน",
            confirmButtonText: "ยืนยัน",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function (isConfirm) {
            if(isConfirm) {
                $.post("/average_dividend/deposit", {id: master}, function (res) {
                    if (res.status === true) {
                        swal("โอนเงินหลักประกัน สำเร็จแล้ว");
                        setTimeout(function () {
                            location.reload();
                        }, 500);
                    } else {
                        if (typeof res.msg !== "undefined" && res.msg === 'pending') {
                            swal("ไม่สามารถโอนได้!", "เนื่องจากมีการทำรายการนี้ไปแล้ว", "warning");
                        } else {
                            swal("ไม่สามารถโอนเงินหลักประกันได้!", "เนื่องจากเกิดข้อผิดพลาดบางอย่าง", "error");
                        }
                    }
                });
            }
        });
    }

function tests()
{
	var divi = $('input[name=dividend_percent]').val();
	var avg = $('input[name=average_percent]').val();
	var gift = $("#caldata input[name=money_gift]").val();

	
	if(divi && avg)
	{
        console.log("process...");

        location.href = "/average_dividend/calculate_data?dividend_percent="+divi+"&average_percent="+avg+"&money_gift="+gift;

//	$.ajax({
//		method: "POST",
//		url: "/average_dividend/calculate_data",
//		data: { "dividend_percent":divi,"average_percent":avg }
//		})
//		.done(function( msg ) {
//            var wnd = window.open("about:blank", "", "_blank");
//            wnd.document.write(msg);
//		if(!msg)
//		{
//			console.log("excel creating...");
//			$('#expect_dividend_modal').modal('hide');
//			location.href="/average_dividend/average_dividend_excel";
//		}
//		});

	}else{

		alert("กรุณากรอกข้อมูล");
	}
}

</script>