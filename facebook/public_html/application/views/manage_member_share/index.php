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
    .modal.fade {
        z-index: 10000000 !important;
    }
	.control-label{
		text-align: right;
		margin-bottom: 0;
		padding-top: 7px;
	}
	th{
		text-align: center;
	}
</style>
<h1 style="margin-bottom: 0">ข้อมูลสมาชิก</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>

    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <a class="btn btn-primary btn-lg bt-add" href="manage_member_share/add">
            <span class="icon icon-plus-circle"></span>
            เพิ่มคำร้อง
        </a>
    </div>

</div>
<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12">
        <div class="panel panel-body">


            <div class="row">
                <div class="col-sm-6">
                    <div class="input-with-icon">
                        <input class="form-control input-thick pill m-b-2" type="text" placeholder="ค้นหา" name="search_text" id="search_text" onkeyup="get_search_member()">
                        <span class="icon icon-search input-icon"></span>
                    </div>
                </div>
				<div class="form-group col-sm-3">
                    <label class="control-label" style="float:left;">สถานะ : </label>
                    <select style="float:right;width:80%" name="member_status" id="member_status" class="form-control input-thick pill m-b-2" onchange="change_member_status(this);">
						<option value="">ทั้งหมด</option>
						<?php foreach($member_status as $key => $value){ ?>
						<option value="<?php echo $key; ?>" <?php echo $key==@$_GET['member_status']?'selected':''; ?>><?php echo $value; ?></option>
						<?php } ?>
					</select>
                </div>
                <div class="col-sm-3 text-right">
                    <p>จำนวนสมาชิกขณะนี้ <?php echo number_format($num_rows); ?> คน</p>
                </div>
            </div>

            <div class="bs-example" data-example-id="striped-table">
                <div id="tb_wrap">
					<form action="<?php echo base_url(PROJECTPATH.'/manage_member_share/del_member'); ?>" method="POST" id="form1">
						<table class="table table-striped">
							<thead>
							<tr>
								<th>ลำดับ</th>
								<th>เลขที่คำร้อง</th>
								<th>รหัสสมาชิก</th>
								<th>ชื่อสกุล</th>
								<th>วันที่สมัคร</th>
								<th>สถานะ</th>
								<th></th>
								<th><input type="checkbox" id="check_all" onclick="checked_all()"> <a onclick="del_member()" style="cursor:pointer" title="ลบข้อมูลที่เลือก" class="icon icon-trash-o"></a></th>
							</tr>
							</thead>
							<tbody id="table_data">
							<?php foreach($row as $key => $value){ ?>
								<tr>
									<td align="center"><?php echo $i++; ?></td>
									<td align="center"><?php echo $value['mem_apply_id']; ?></td>
									<td align="center"><?php echo $value['member_id']; ?></td>
									<td width="30%"><?php echo $value['prename_short'].$value['firstname_th']." ".$value['lastname_th']; ?></td>
									<td align="center"><?php echo $this->center_function->mydate2date($value['apply_date']); ?></td>
									<td align="center"><?php echo @$member_status[$value['member_status']]; ?></td>
									<td  align="center">
										<a href="<?php echo base_url(PROJECTPATH.'/manage_member_share/add/'.$value['id']);?>">แก้ไข</a> 
										<!--a data-toggle="modal" data-target="#Del" data-id="<?php echo $value['mem_apply_id']  ?>" class="text-del">ลบ</a-->
									</td>
									<td align="center" width="5%">
										<?php if(in_array($value['member_status'],array('3','4'))){ ?>
										<input type="checkbox" class="check_member" name="del_member[]" value="<?php echo $value['id']; ?>">
										<?php } ?>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</form>
                </div>
            </div>
        </div>
        <div id="page_wrap">
            <?php echo $paging ?>
        </div>
    </div>
</div>
    </div>
</div>
<script>
    var base_url = $('#base_url').attr('class');
    function get_search_member(){
        $.ajax({
            type: "POST",
            url: base_url+'manage_member_share/get_search_member',
            data: {
                search_text : $("#search_text").val(),
				form_target : 'index'
            },
            success: function(msg) {
                $("#table_data").html(msg);
            }
        });
    }
	function change_member_status(ele){
		document.location.href = base_url+'manage_member_share/index?member_status='+$('#'+ele.id).val();
	}
	function checked_all(){
		if($('#check_all').is(':checked')){
			$('.check_member').prop('checked','checked');
		}else{
			$('.check_member').prop('checked','');
		}
	}
	function del_member(){
		swal({
			title: "",
			text: "ท่านต้องการลบข้อมูลที่เลือกไว้ใช่หรือไม่",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ยืนยัน',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: false,
			closeOnCancel: true
		 },
		 function(isConfirm){
		   if (isConfirm){
				$('#form1').submit();
			} else {
				
			}
		 });
	}
</script>