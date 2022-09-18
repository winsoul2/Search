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
</style>
<h1 style="margin-bottom: 0">ลงทะเบียนพัสดุ</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>

    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <a class="btn btn-primary btn-lg bt-add" href="facility/add">
            <span class="icon icon-plus-circle"></span>
			เพิ่มรายการ
        </a>
    </div>

</div>
<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12">
        <div class="panel panel-body">


            <div class="row">
                <div class="col-sm-6">
                    <div class="input-with-icon">
                        <input class="form-control input-thick pill m-b-2" type="text" placeholder="ค้นหา" name="search_text" id="search_text" onkeyup="get_search_store()">
                        <span class="icon icon-search input-icon"></span>
                    </div>
                </div>

                <div class="col-sm-6 text-right">
                    
                </div>
            </div>

			<form method="post" action="<?php echo base_url(PROJECTPATH.'/facility/del_all'); ?>" class="g24 form form-horizontal" id="form_del_all">               
				<div class="bs-example" data-example-id="striped-table">
					<div id="tb_wrap">
						<table class="table table-striped">
							<thead>
							<tr>
								<th style="width:40px;"></th>
								<th >ลำดับ</th>
								<th>รหัสพัสดุ</th>
								<th>รายการ</th>
								<th class="text-right">ราคา</th>
								<th class="text-right">ราคาหลังหักค่าเสื่อม</th>
								<th style="width:8%;"></th>
								<th>หน่วยงาน</th>
								<th>สถานะ</th>
								<th style="width:80px;"></th>
							</tr>
							</thead>
							<tbody id="table_data">
							<?php foreach($row as $key => $value){ ?>
								<tr>
									<td><input type="checkbox" id="store_id[<?php echo @$value['store_id'];?>]" name="store_id[<?php echo @$value['store_id'];?>]" value="<?php echo @$value['store_id'];?>"></td>
									<td><?php echo $i++; ?></td>
									<td><?php echo @$value['store_code']; ?></td>
									<td><?php echo @$value['store_name']; ?></td>
									<td class="text-right"><?php echo number_format(@$value['store_price'],2); ?></td>
									<td class="text-right"><?php echo number_format(@$value['depreciation_price'],2); ?></td>
									<th></th>
									<td><?php echo @$value['department_name']; ?></td>
									<td><?php echo @$value['facility_status_name']; ?></td>
									<td>
										<a href="<?php echo base_url(PROJECTPATH.'/facility/add?s_id='.@$value['store_id']);?>">แก้ไข</a> 
										|
										<span class="text-del del"  onclick="del_coop_data('<?php echo @$value['store_id'] ?>')">ลบ</span>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="m-t-1">
					<div class="g24-col-sm-24">
						<div class="form-group p-y-lg">						
							<button type="button" onclick="del_all()" class="btn btn-primary">
								ลบ
							</button>
						</div>
					</div>
				</div>
			</form>
        </div>
        <div id="page_wrap">
            <?php echo @$paging ?>
        </div>
    </div>
</div>
    </div>
</div>
<script>
    var base_url = $('#base_url').attr('class');
    function get_search_store(){
        $.ajax({
            type: "POST",
            url: base_url+'facility/get_search_store',
            data: {
                search_text : $("#search_text").val(),
				form_target : 'index'
            },
            success: function(msg) {
                $("#table_data").html(msg);
            }
        });
    }
	
	function del_coop_data(id){	
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
					url: base_url+'/facility/del_coop_data',
					method: 'POST',
					data: {
						'table': 'coop_facility_store',
						'id': id,
						'field': 'store_id'
					},
					success: function(msg){
					   //console.log(msg); return false;
						if(msg == 1){
						  document.location.href = base_url+'facility';
						}else{

						}
					}
				});
			} else {
				
			}
		});		
	}
	
	function del_all(){
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
				$('#form_del_all').submit();
			} else {
				
			}
		});			
	}
</script>