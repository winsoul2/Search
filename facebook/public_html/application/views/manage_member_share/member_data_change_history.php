<div class="layout-content">
    <div class="layout-content-body">
<style>
    .form-group { margin-bottom: 0; }
    .border1 { border: solid 1px #ccc; padding: 0 15px; }
    .mem_pic { float: right; width: 150px; }
    .mem_pic img { width: 100%; border: solid 1px #ccc; }
    .mem_pic button { display: block; width: 100%; }

    .hide_error{color : inherit;border-color : inherit;}

    .has-error{color : #d50000;border-color : #d50000;}

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .alert-danger {
        background-color: #F2DEDE;
        border-color: #e0b1b8;
        color: #B94A48;
    }
    .modal-backdrop.in{
        opacity: 0;
    }
    .modal-backdrop {
        position: relative;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1040;
        background-color: #000;
    }
	.modal-dialog-idcard{
		width : 80%;
	}
	th{
		text-align: center;
	}
	.btn_idcard{
		font-weight:lighter;
		font-size:16px;
		padding: 3px 12px;
	}
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
		vertical-align: middle;
		padding: 4px;
	}
</style>
<h1 style="margin-bottom: 0">การเปลี่ยนแปลงข้อมูลสมาชิก</h1>
<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12">
        <div class="panel panel-body">
            <div class="m-t-1">

                <div class="g24-col-sm-20">
                    <div class="row">
                        <div class="g24-col-sm-20">
                            <h3>ประวัติการแก้ไขข้อมูล</h3><br>
                        </div>
                        <div class="g24-col-sm-24">
                            <label class="g24-col-sm-2 control-label"></label>
                            <div class=" g24-col-sm-22">
                                <table class="table table-bordered table-striped table-center">
                                    <thead> 
                                        <tr class="bg-primary">
                                            <th>วันที่เวลา</th>
                                            <th>รายการแก้ไข</th>
                                            <th>ผู้ทำรายการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if(!empty($data)) {
                                            foreach($data as $change) {
                                    ?>
                                            <tr> 
                                                <td><?php echo $change["created_at"]?></td>
                                                <td>
                                                <?php
                                                    $total = count($change["change_list"]);
                                                    foreach($change["change_list"] as $key => $change_info) {
                                                ?>
                                                    <a href="#" data-toggle="modal" data-change-name="<?php echo $change_info["name"];?>" data-change-id="<?php echo $change_info["id"];?>" data-target="#changeModal" class="change_link_pop_up"><?php echo $change_info["name"];?></a>
                                                <?php
                                                        if(($key+1) != $total) {
                                                            echo ", ";
                                                        }
                                                    }
                                                ?>
                                                </td>
                                                <td><?php echo $change["user"]; ?></td> 
                                            </tr>
                                    <?php
                                            }
                                        } else {
                                    ?>
                                        <tr><td colspan="9">ไม่พบข้อมูล</td></tr>
                                        <?php } ?>
                                    </tbody> 
                                </table>
                                
                            </div>
                        </div>
                        <div class="g24-col-sm-24 text-center">
                            <?php echo $paging?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<div class="modal fade" id="changeModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">ข้อมูลการแก้ไข</h4>
            </div>
            <div class="modal-body">
                <div class="">
                    <table class="table table-striped">
						<thead>
							<th></th>
							<th class="text-center">ข้อมูลเดิม</th>
							<th class="text-center">ข้อมูลใหม่</th>
						</thead>
                        <tbody id="changeModal_tbody">
                            <td id="changeModal_td_name" class="text-center"></td>
                            <td id="changeModal_td_old_val" class="text-center"></td>
                            <td id="changeModal_td_new_val" class="text-center"></td>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer text-center">
                <button type="button" id="changeModal_close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery( document ).ready(function() {
        $(".change_link_pop_up").click(function() {
            id = $(this).attr("data-change-id")
            name = $(this).attr("data-change-name")
            $.ajax({
                type: "POST",
                url: base_url+'manage_member_share/get_change_detail',
                data: {
                    id : id
                },
                success: function(result) {
                    data = JSON.parse(result)
                    $("#changeModal_td_name").html(name)
                    $("#changeModal_td_old_val").html(data.old_value)
                    $("#changeModal_td_new_val").html(data.new_value)
                }
            })
        });
    });
</script>