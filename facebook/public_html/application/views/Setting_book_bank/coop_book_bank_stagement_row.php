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
		<h1 style="margin-bottom: 0">แก้ไขข้อมูลหน้ารายการ</h1>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">

                    <br>
                    <form action="<?=base_url('Setting_book_bank/save_coop_book_bank_stagement_row?style_id='.$_GET['style_id'])?>" method="POST">
                    <div class="row">
                        <div class="col-md-offset-1 col-md-5">
							<a href="#" data-toggle="modal" data-target="#how_to"><i class="fa fa-info-circle" aria-hidden="true"></i> วิธีใช้งาน</a>
						</div>
						<div class="col-md-offset-1 col-md-5 text-right">
							<a href="javascript:" onclick="add_detail(1, <?=@$_GET['style_id']?>)" class="add_detail btn btn-primary">
								<i class="fa fa-plus" aria-hidden="true" id="add_detail"></i> ตั้งค่าข้อมูล
							</a>
						</div>
					</div>
					<div class="row">	
                        <div class="col-md-offset-1 col-md-10">                        
                            <table class="table" id="table_row_setting">
                                <thead>
                                    <tr>
                                        <td width="10%" class="col">บรรทัดที่</td>
                                        <td width="20%" class="col">ค่า Y (mm)</td>
                                        <td width="30%" class="col">เพิ่ม/ลบ</td>
                                        <!--<td width="40%" class="col">จัดการ</td>-->
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php
                                        // $key = 0;
                                        foreach ($items as $key => $value) {
                                            ?>
                                                <tr>
                                                    <td width="10%" class="col"><input type="text" class="form-control" name="no[]" value="<?=$value['no']?>"></td>
                                                    <td width="20%" class="col"><input type="text" class="form-control" name="y[]" value="<?=$value['y']?>"></td>
                                                    <td width="30%" class="col">
                                                        <?php
                                                            if(sizeof($items)-1 == $key){
                                                                ?> 
                                                                    <a href="javascript:" onclick="add_line()" class="add_line">
                                                                        <i class="fa fa-plus" aria-hidden="true" id="add_line"></i> เพิ่มบรรทัด
                                                                    </a>
                                                                    <a href="javascript:" onclick="remove_line()" class="remove_line">
                                                                        <i class="fa fa-minus" aria-hidden="true" id="remove_line"></i> ลบบรรทัด
                                                                    </a>
                                                                <?php
                                                            }else{

                                                            }
                                                        ?>
                                                    </td>
                                                    <!--<td width="40%" class="col">
                                                        <a href="javascript:" onclick="add_detail(<?=$value['row_id']?>, <?=@$_GET['style_id']?>)" class="add_detail">
                                                            <i class="fa fa-plus" aria-hidden="true" id="add_detail"></i> เพิ่มข้อมูลในแถว
                                                        </a>
                                                    </td>-->
                                                </tr>
                                            <?php
                                        }

                                        if($key==0){
                                            $key = 1;
                                        }else{
                                            $key += 2;
                                        }
                                        for ($i=$key; $i <= 20; $i++) { 
                                            ?>
                                                <tr>
                                                    <td width="10%" class="col"><input type="text" class="form-control" name="no[]"></td>
                                                    <td width="20%" class="col"><input type="text" class="form-control" name="y[]"></td>
                                                    <td width="70%" class="col">
                                                        <a href="javascript:" onclick="add_line()" class="add_line">
                                                            <i class="fa fa-plus" aria-hidden="true" id="add_line"></i> เพิ่มบรรทัด
                                                        </a>
                                                        <a href="javascript:" onclick="remove_line()" class="remove_line">
                                                            <i class="fa fa-minus" aria-hidden="true" id="remove_line"></i> ลบบรรทัด
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php
                                        }
                                    ?>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>


                    
                    <br><br>
                    <div class="row">
                        <div class="col-md-offset-2 col-md-6 text-center">
                            <button class="btn btn-primary" type="submit"><span class="icon icon-add"></span> บันทึก</button>
                        </div>
                    </div>
                    </form>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="how_to" role="dialog">
	<input type="hidden" name="line_start" id="line_start" value=""/>
    <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">แก้ไขรูปแบบ</h4>
                </div>

                <div class="modal-body">
                <p>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;กำหนดค่าที่ช่องข้อมูลตัวอักษรที่ต้องการตาม ตารางคีย์เวิร์ด กำหนดค่า X และ Y โดย X วัดจากแนวนอนของหน้าปกสมุดบัญชี Y วัดจากแนวตั้งของหน้าปกสมุดบัญชี หน่วยเป็นมิลลิเมตร และกำหนดขนาดของตัวอักษร
                </p>
                <p>
                * หากต้องการลบ ให้เว้นว่างช่องข้อมูลตัวอักษร แล้วกดบันทึก
                </p>
                <div class="row">
                        <div class="col-md-offset-1 col-md-10 text-center">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <td colspan=2>ตารางคีย์เวิร์ด</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            คีย์เวิร์ด
                                        </td>
                                        <td>
                                            ผลลัพท์ที่จะได้
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>[no]</td>
                                        <td>ลำดับ</td>
                                    </tr>
                                    <tr>
                                        <td>[date]</td>
                                        <td>วัน เดือน ปี</td>
                                    </tr>
                                    <tr>
                                        <td>[code]</td>
                                        <td>รายการ</td>
                                    </tr>
                                    <tr>
                                        <td>[withdrawal]</td>
                                        <td>ถอน</td>
                                    </tr>
                                    <tr>
                                        <td>[deposit]</td>
                                        <td>ฝาก</td>
                                    </tr>
                                    <tr>
                                        <td>[balance]</td>
                                        <td>คงเหลือ</td>
                                    </tr>
                                    <tr>
                                        <td>[staff]</td>
                                        <td>เจ้าหน้าที่</td>
                                    </tr>
                                    <tr>
                                        <td>ข้อมูลตัวอักษรแบบคงที่</td>
                                        <td>เช่น สำนักงานใหญ่, บัญชีออมทรัพย์, บัญชีประจำ ,1,2 เป็นต้น</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

<!-- add_detail modal -->
<div class="modal fade" id="add_detail_modal" role="dialog">
    <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">แก้ไขข้อมูลในแถว </h4>
                </div>

                <div class="modal-body">
                <p>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;กำหนดค่าที่ช่องข้อมูลตัวอักษรที่ต้องการตาม ตารางคีย์เวิร์ด กำหนดค่า X และ Y โดย X วัดจากแนวนอนของหน้าปกสมุดบัญชี Y วัดจากแนวตั้งของหน้าปกสมุดบัญชี หน่วยเป็นมิลลิเมตร และกำหนดขนาดของตัวอักษร
                </p>
                <p>
                * หากต้องการลบ ให้เว้นว่างช่องข้อมูลตัวอักษร แล้วกดบันทึก
                </p>
                <form action="" id="frm" method="post">
                <div class="row">
                        <div class="col-sm-offset-1 col-sm-2 text-center">
                            <b>ข้อมูลตัวอักษร</b>
                        </div>
                        <div class="col-sm-2 text-center">
                            <b>ค่า X (mm)</b>
                        </div>
                        <div class="col-sm-2 text-center">
                            <b>ความยาว (mm)</b>
                        </div>
                        <div class="col-sm-2 text-center">
                            <b>ขนาดตัวอักษร</b>
                        </div>
                        <div class="col-sm-2 text-center">
                            <b>จัดเรียง</b>
                        </div>
                </div>
                <div class="row">
                        <div class="col-sm-offset-1 col-sm-2 text-center">
                            <input type="text" class="form-control" name="style_value[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="x[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="width[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="font_size[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <select name="align[]" class="form-control">
                                <option value="L">ชิดซ้าย</option>
                                <option value="C">กึ่งกลาง</option>
                                <option value="R">ชิดขวา</option>
                            </select>
                        </div>
                </div><br>
                <div class="row">
                        <div class="col-sm-offset-1 col-sm-2 text-center">
                            <input type="text" class="form-control" name="style_value[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="x[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="width[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="font_size[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <select name="align[]" class="form-control">
                                <option value="L">ชิดซ้าย</option>
                                <option value="C">กึ่งกลาง</option>
                                <option value="R">ชิดขวา</option>
                            </select>
                        </div>
                </div><br>
                <div class="row">
                        <div class="col-sm-offset-1 col-sm-2 text-center">
                            <input type="text" class="form-control" name="style_value[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="x[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="width[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="font_size[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <select name="align[]" class="form-control">
                                <option value="L">ชิดซ้าย</option>
                                <option value="C">กึ่งกลาง</option>
                                <option value="R">ชิดขวา</option>
                            </select>
                        </div>
                </div><br>
                <div class="row">
                        <div class="col-sm-offset-1 col-sm-2 text-center">
                            <input type="text" class="form-control" name="style_value[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="x[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="width[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="font_size[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <select name="align[]" class="form-control">
                                <option value="L">ชิดซ้าย</option>
                                <option value="C">กึ่งกลาง</option>
                                <option value="R">ชิดขวา</option>
                            </select>
                        </div>
                </div><br>
                <div class="row">
                        <div class="col-sm-offset-1 col-sm-2 text-center">
                            <input type="text" class="form-control" name="style_value[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="x[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="width[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="font_size[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <select name="align[]" class="form-control">
                                <option value="L">ชิดซ้าย</option>
                                <option value="C">กึ่งกลาง</option>
                                <option value="R">ชิดขวา</option>
                            </select>
                        </div>
                </div><br>
                <div class="row">
                        <div class="col-sm-offset-1 col-sm-2 text-center">
                            <input type="text" class="form-control" name="style_value[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="x[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="width[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="font_size[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <select name="align[]" class="form-control">
                                <option value="L">ชิดซ้าย</option>
                                <option value="C">กึ่งกลาง</option>
                                <option value="R">ชิดขวา</option>
                            </select>
                        </div>
                </div>
                <br>
                <div class="row">
                        <div class="col-sm-offset-1 col-sm-2 text-center">
                            <input type="text" class="form-control" name="style_value[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="x[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="width[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <input type="number" class="form-control" name="font_size[]">
                        </div>
                        <div class="col-sm-2 text-center">
                            <select name="align[]" class="form-control">
                                <option value="L">ชิดซ้าย</option>
                                <option value="C">กึ่งกลาง</option>
                                <option value="R">ชิดขวา</option>
                            </select>
                        </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12 text-center">
						<input type="hidden" class="form-control" name="type_save" id="type_save" value="">
                        <!--<button type="button" class="btn btn-primary" id="bt_save_row" style="width: auto;">บันทึกเฉพาะบรรทัดนี้</button>-->
                        <button type="button" class="btn btn-success" id="bt_save_all">บันทึก</button>
                    </div>
                </div>
                </form>
                
                
            </div>
    </div>
</div>

<style>
    .col{
        padding: 10px;
    }
</style>

<script>
    function add_line(){
        console.log("add_line ");
        $(".add_line").remove();
        $(".remove_line").remove();
        
        var last_row = parseInt($('#table_row_setting').find('input[name="no[]"]').last().val()) + 1;
        var last_y = parseInt($('#table_row_setting').find('input[name="y[]"]').last().val());
        var sub_last_y = parseInt($('#table_row_setting').find('input[name="y[]"][value!='+last_y+']').last().val());
        var diff_y = last_y - sub_last_y;
        var new_y = last_y + diff_y;
        if(isNaN(new_y))
            new_y = "";

        if(new_y==last_y)
            new_y = "";

        var new_row = '<tr>\
                        <td width="10%" class="col"><input type="text" class="form-control" name="no[]" value="'+last_row+'"></td>\
                        <td width="20%" class="col"><input type="text" class="form-control" name="y[]" value="'+new_y+'"></td>\
                        <td width="70%" class="col">\
                            <a href="javascript:" onclick="add_line()" class="add_line">\
                                <i class="fa fa-plus" aria-hidden="true" id="add_line"></i> เพิ่มบรรทัด\
                            </a>\
                            <a href="javascript:" onclick="remove_line()" class="remove_line">\
                                <i class="fa fa-minus" aria-hidden="true" id="remove_line"></i> ลบบรรทัด\
                            </a>\
                        </td>\
                        </tr>';

        $('#table_row_setting tr:last').after(new_row);
    }

    function remove_line(){
        console.log("remove_line ");
        var last_row = parseInt($('#table_row_setting').find('input[name="no[]"]').last().val());
        if(last_row==1){
            return;
        }
        $('#table_row_setting tr:last').remove();
        $('#table_row_setting tr:last').remove();

        last_row = parseInt($('#table_row_setting').find('input[name="no[]"]').last().val()) + 1;
        var last_y = parseInt($('#table_row_setting').find('input[name="y[]"]').last().val());
        var sub_last_y = parseInt($('#table_row_setting').find('input[name="y[]"][value!='+last_y+']').last().val());
        var diff_y = last_y - sub_last_y;
        var new_y = last_y + diff_y;
        if(isNaN(new_y))
            new_y = "0";
        if(isNaN(last_row))
            last_row = "1";
            

        console.log(last_row)
        

        var new_row = '<tr>\
                        <td width="10%" class="col"><input type="text" class="form-control" name="no[]" value="'+last_row+'"></td>\
                        <td width="20%" class="col"><input type="text" class="form-control" name="y[]" value="'+new_y+'"></td>\
                        <td width="70%" class="col">\
                            <a href="javascript:" onclick="add_line()" class="add_line">\
                                <i class="fa fa-plus" aria-hidden="true" id="add_line"></i> เพิ่มบรรทัด\
                            </a>\
                            <a href="javascript:" onclick="remove_line()" class="remove_line">\
                                <i class="fa fa-minus" aria-hidden="true" id="remove_line"></i> ลบบรรทัด\
                            </a>\
                        </td>\
                        </tr>';
        console.log($('#table_row_setting tr:last'));
        $('#table_row_setting tr:last').after(new_row);

    }

    function add_detail(row_id, style_id){
        console.log(row_id, style_id);
        $.ajax({
			type: "POST",
			url: base_url + "setting_book_bank/get_detail_in_row",
			data: {
                row_id: row_id,
				style_id: style_id,
			},
			success: function (msg) {
                // console.log(msg);
				var obj = msg.result;
                console.log(obj);
                $.each( $('#frm').find('input[name="style_value[]"]') , function( key, value ) {
                    if( typeof obj[key] !== 'undefined'){
                        $(value).val(obj[key].style_value);
                    }
                });
                $.each( $('#frm').find('input[name="x[]"]') , function( key, value ) {
                    if( typeof obj[key] !== 'undefined'){
                        $(value).val(obj[key].x);
                    }
                });
                $.each( $('#frm').find('input[name="width[]"]') , function( key, value ) {
                    if( typeof obj[key] !== 'undefined'){
                        $(value).val(obj[key].width);
                    }
                });
                $.each( $('#frm').find('input[name="font_size[]"]') , function( key, value ) {
                    if( typeof obj[key] !== 'undefined'){
                        $(value).val(obj[key].font_size);
                    }
                });
                $.each( $('#frm').find('select[name="align[]"]') , function( key, value ) {
                    if( typeof obj[key] !== 'undefined'){
                        $(value).val(obj[key].align);
                    }
                });
			}
		});

        $('#add_detail_modal').modal('show');
        
        $('#frm').attr('action', base_url+'Setting_book_bank/save_coop_book_bank_stagement_row_setting?row_id='+row_id+"&style_id="+style_id);
    }
	
	$('#bt_save_row').click(function(){
		$('#type_save').val('row');
		$('#frm').submit();
	})	
	
	$('#bt_save_all').click(function(){
		$('#type_save').val('all');
		$('#frm').submit();
	})
</script>