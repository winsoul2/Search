<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
            .form-group{
                margin-bottom: 5px;
            }
            .btn-position-top{
                margin-top:  -1em;
                padding-bottom: 2px;
                width: 120px;
            }
            .modal-footer {
                border-top:0;
            }
		</style>
		<h1 style="margin-bottom: 0">นำเข้าฌาปนกิจ สสอค.</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-right:0px;text-align:right;">
                <button id="del-btn" type="button" class="btn btn-primary btn-lg btn-position-top">
                    <span>ลบรายการ</span>
                </button>
                <button id="add-btn" type="button" class="btn btn-primary btn-lg btn-position-top">
                    <span class="icon icon-plus-circle"></span>
                    <span>เพิ่มการนำเข้า</span>
                </button>
            </div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<h3 ></h3>
                    <form action="" id="form1" method="GET" enctype="multipart/form-data">
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 control-label right">ชื่อ-นามสกุล</label>
                            <div class="g24-col-sm-5">
                                <input type="text" class="form-control" name="name" value="<?php echo $_GET["name"]?>"/>
                            </div>
                            <label class="g24-col-sm-3 control-label right">รหัสสมาชิก</label>
                            <div class="g24-col-sm-5">
                                <input type="text" class="form-control" name="member_id" value="<?php echo $_GET["member_id"];?>"/>
                            </div>
                            <label class="g24-col-sm-3 control-label right">เลขฌาปนกิจ</label>
                            <div class="g24-col-sm-5">
                                <input type="text" class="form-control" name="import_cremation_no" value="<?php echo $_GET["import_cremation_no"];?>"/>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-3 control-label right"> ปี </label>
                            <div class="g24-col-sm-5">
                                <select id="year" name="year" class="form-control">
                                    <option value=""></option>
                                    <?php
                                        if(empty($_GET["year"])){$_GET["year"] = date('Y')+543;}
                                        for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){
                                    ?>
                                        <option value="<?php echo $i; ?>" <?php echo !empty($_GET["year"]) &&  $i == $_GET["year"] ?'selected':''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label class="g24-col-sm-3 control-label right"></label>
                            <div class="g24-col-sm-5">
                                <input type="submit" class="btn btn-primary" id="search-btn" name="search_btn" value="ค้นหา"/>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24"></div>
                        <div class="form-group g24-col-sm-24"></div>
                    </form>
                    <div class="form-group g24-col-sm-24"></div>
                    <table class="table table-bordered table-striped table-center">
                        <thead>
                            <tr class="bg-primary">
                                <th style="width: 45px;">
                                    <div class="form-check">
                                        <input type="checkbox" value="" id="chk-all">
                                    </div>
                                </th>
                                <th class="text-center">ลำดับ</th>
                                <th class="text-center">เลขฌาปนกิจ</th>
                                <th class="text-center">รหัสสมาชิก</th>
                                <th class="text-center">ชื่อสกุล</th>
                                <th class="text-center">วันที่เป็นสมาชิก</th>
                                <th class="text-center">วันที่คุ้มครอง</th>
                                <th class="text-center">รวมส่งเงินสงเคราะห์</th>
                            </tr>
                        </thead>
                        <tbody id="table_first">
                        <form action="" id="form2" method="POST">
                        <?php
                            if(!empty($datas)) {
                                foreach($datas as $data) {
                        ?>
                            <tr>
                                <td class="text-center">
                                    <div class="form-check">
                                        <input class="chk-del" type="checkbox" name="ids[]" value="<?php echo $data['import_cremation_id']; ?>" id="chk-id-<?php echo $data['id']; ?>">
                                        <label class="form-check-label" for="chk-approve"></label>
                                    </div>
                                </td>
                                <td class="text-center"><?php echo $page_start++;?></td>
                                <td class="text-center"><?php echo $data["import_cremation_no"];?></td>
                                <td class="text-center"><?php echo $data["member_id"];?></td>
                                <td class="text-left"><?php echo $data["prename_full"].$data["firstname_th"]." ".$data["lastname_th"];?></td>
                                <td class="text-center"><?php echo $this->center_function->ConvertToThaiDate($data['import_start_date'],0,0,0); ?></td>
                                <td class="text-center"><?php echo $this->center_function->ConvertToThaiDate($data['import_protection_date'],0,0,0); ?></td>
                                <td class="text-center"><?php echo number_format($data["import_total_amount_balance"], 2); ?></td>
                            </tr>
                        <?php
                                }
                            } else {
                        ?>
                            <tr>
                                <td class="text-center" colspan="8">ไม่พบข้อมูล</td>
                            </tr>
                        <?php
                            }
                        ?>
                        </form>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
		<?php echo @$paging ?>
	</div>
</div>
<form action="" id="form1" method="POST" enctype="multipart/form-data">
<div id="import-modal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-info">
        <div class="modal-content">
            <div class="modal-header modal-header-info">
                <h2 class="modal-title">นำเข้าฌาปนกิจ สสอค.</h2>
            </div>
            <div class="modal-body" style="height: 200px;">
                <div class="g24-col-xs-24 g24-col-sm-24 g24-col-md-24 g24-col-lg-24 padding-l-r-0">
                    <label class="g24-col-xs-2 g24-col-sm-2 g24-col-md-2 g24-col-lg-2"></label>
                    <label class="g24-col-xs-22 g24-col-sm-22 g24-col-md-22 g24-col-lg-22 padding-l-r-2 control-label text-left">
                        1.ทำการจัดรูปแบบ Excel ดังนี้ <a href="<?php echo base_url(PROJECTPATH."/cremation/download_file?type=thai_ftsc");?>"><span>ดาวน์โหลด Excel ตัวอย่าง</span></a>
                    </label>
	        	</div>
                <div class="g24-col-xs-24 g24-col-sm-24 g24-col-md-24 g24-col-lg-24 padding-l-r-0">&nbsp</div>
                <div class="g24-col-xs-24 g24-col-sm-24 g24-col-md-24 g24-col-lg-24 padding-l-r-0">
                    <label class="g24-col-xs-2 g24-col-sm-2 g24-col-md-2 g24-col-lg-2"></label>
                    <label class="g24-col-sm-3 control-label text-left">
                        2.เลือกปี
                    </label>
                    <div class="g24-col-sm-6">
                        <select id="year" name="year" class="form-control">
                            <option value=""></option>
                            <?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
                                <option value="<?php echo $i; ?>" <?php echo !empty($_GET["year"]) &&  $i == $_GET["year"] ?'selected':''; ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="g24-col-xs-24 g24-col-sm-24 g24-col-md-24 g24-col-lg-24 padding-l-r-0">&nbsp</div>
                <div class="g24-col-xs-24 g24-col-sm-24 g24-col-md-24 g24-col-lg-24 padding-l-r-0">
                    <label class="g24-col-xs-2 g24-col-sm-2 g24-col-md-2 g24-col-lg-2"></label>
                    <label class="g24-col-sm-7 control-label">3.ทำการแนบไฟล์ Excel</label>
                    <div class="g24-col-sm-6 req-file">
                        <div class="form-group">
                            <label class="fileContainer btn btn-info ">
                                <span class="icon icon-paperclip"></span> 
                                แนบไฟล์
                                <input id="file" name="file" class="form-control m-b-1" type="file" value="" style="height: auto;">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center" style="padding-top:0;">
				<button type="submit" id="submit-btn" class="btn btn-primary" >นำเข้า</button>
			</div>
        </div>
    </div>
</div>
</form>
<script>
    $(document).ready(function(){
        $("#add-btn").click(function(){
            $("#import-modal").modal('toggle');
        });
        // $("#year").change(function(){
        //     window.location.href = base_url+"cremation/import_thaiftsc?year=" + $(this).val();
        // });
        $("#del-btn").click(function(){
            $("#form2").submit();
        })
    });
</script>