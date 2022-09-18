<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
		</style>

		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">ตั้งค่าการพิมพ์ปกสมุดบัญชีเงินฝาก</h1>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
                    <br>
                    <div class="row">
                        <div class="col-md-offset-2 col-md-6">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal_save_book_bank"><span class="icon icon-add"></span> เพิ่ม</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-offset-1 col-md-10">
                            <table class="table table-border">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>ชื่อรูปแบบ</td>
                                        <td>ตั้งค่าสมุด</td>
                                        <td>ตั้งค่าหน้าปก</td>
                                        <td>ตั้งค่ารายการ</td>
                                        <td>จัดการ</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($items as $key => $value) {
                                            ?>
                                                <tr>
                                                    <td width="5%"><?=$key+1?></td>
                                                    <td width="20%"><?=$value['style_name']?></td>
                                                    <td width="15%">
                                                        <a href="#" onclick="get_style(<?=$value['style_id']?>)" data-toggle="modal" data-id="<?=$value['style_id']?>" data-target="#modal_update_book_bank"><i class="fa fa-arrows-h" aria-hidden="true"></i> กำหนดขนาด</a>
                                                    </td>
                                                    <td width="15%">
                                                        <a href="<?=base_url('Setting_book_bank/coop_book_bank_style_setting?style_id='.$value['style_id'])?>" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i> แก้ไขข้อมูล</a>
                                                    </td>
                                                    <td width="15%">
                                                        <a href="<?=base_url('Setting_book_bank/coop_book_bank_stagement_row?style_id='.$value['style_id'])?>" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i> แก้ไขข้อมูล</a>
                                                    </td>
                                                    <td width="5%">
                                                            <a href="<?=base_url('Setting_book_bank/delete_coop_book_bank_style?style_id='.$value['style_id'])?>" onclick="return confirm('ลบรายการนี้');"><i class="fa fa-times" aria-hidden="true"></i> ลบ</a>
                                                    </td>
                                                </tr>
                                            <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_save_book_bank" role="dialog">
	<input type="hidden" name="line_start" id="line_start" value=""/>
    <div class="modal-dialog modal-md">
        <form action="<?=base_url('Setting_book_bank/save_coop_book_bank_style')?>" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">เพิ่มรูปแบบ</h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <span>ชื่อรูปแบบ</span>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="style_name" id="">
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <span>ความกว้าง</span>
                        </div>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" name="width_page" id="">
                        </div>
                        <div class="col-sm-2">
                            <span>มิลลิเมตร</span>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <span>ความยาว</span>
                        </div>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" name="height_page" id="">
                        </div>
                        <div class="col-sm-2">
                            <span>มิลลิเมตร</span>
                        </div>
                    </div>
                </div>


                <div class="modal-footer text-center">
                    <button class="btn btn-info" type="submit" id="submit_select_line">ตกลง</button>
                    <button class="btn btn-default" id="modal_line_start_close_btn">ยกเลิก</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_update_book_bank" role="dialog">
	<input type="hidden" name="line_start" id="line_start" value=""/>
    <div class="modal-dialog modal-md">
        <form action="<?=base_url('Setting_book_bank/update_coop_book_bank_style')?>" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">แก้ไขรูปแบบ</h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <span>ชื่อรูปแบบ</span>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="style_name" id="style_name">
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <span>ความกว้าง</span>
                        </div>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" name="width_page" id="width_page">
                        </div>
                        <div class="col-sm-2">
                            <span>มิลลิเมตร</span>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <span>ความยาว</span>
                        </div>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" name="height_page" id="height_page">
                        </div>
                        <div class="col-sm-2">
                            <span>มิลลิเมตร</span>
                        </div>
                    </div>
                </div>


                <div class="modal-footer text-center">
                <input type="hidden" name="style_id" id="style_id" value="">
                    <button class="btn btn-info" type="submit" id="submit_select_line">ตกลง</button>
                    <button class="btn btn-default" id="modal_line_start_close_btn">ยกเลิก</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
 function submit_form(){
	 $('#form1').submit();
 }

 function get_style(style_id){
    $.ajax({
		method: 'POST',
		url: base_url+'Setting_book_bank/get_style',
		data: {
			style_id : style_id,
		},
		dataType: 'json',
		success: function(data){
			console.log(data);
            $('#style_name').val(data.result.style_name);
            $('#width_page').val(data.result.width_page);
            $('#height_page').val(data.result.height_page);
            $("#style_id").val(data.result.style_id);
		}
	});
 }
</script>

