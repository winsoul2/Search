<div class="layout-content">
    <div class="layout-content-body">
	<?php if (@$act != "add") { ?>
		<h1 style="margin-bottom: 0">ขอบเขตการซื้อหุ้น</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
	<?php } ?>

		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
                    <form id='form1' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_share_data/coop_share_limit_setting'); ?>" method="post">
                        <h3></h3>
                        <?php
                            foreach($mem_types as $mem_type) {
                        ?>
                        <div class="g24-col-sm-24 m-b-1">
                            <div class="form-group">
                                <label class="g24-col-sm-6 control-label right"><?php echo $mem_type["mem_type_name"]?> :</label>
                                <label class="g24-col-sm-2 control-label text-left">ซื้อได้ไม่เกิน</label>
                                <div class="g24-col-sm-2">
                                    <input class="form-control m-b-1" type="number" name="limit_per_time[<?php echo $mem_type["mem_type_id"]?>]" id="limit_per_time_<?php echo $mem_type["mem_type_id"]?>" value="<?php echo !empty($mem_type['limit_per_time']) ? $mem_type['limit_per_time'] : ""?>">
                                </div>
                                <label class="g24-col-sm-2 control-label text-left">หุ้น ต่อครั้ง</label>
                                <div class="g24-col-sm-2">
                                    <input class="form-control m-b-1" type="number" name="limit_per_year[<?php echo $mem_type["mem_type_id"]?>]" id="limit_per_year<?php echo $mem_type["mem_type_id"]?>" value="<?php echo !empty($mem_type['limit_per_year']) ? $mem_type['limit_per_year'] : ""?>">
                                </div>
                                <label class="g24-col-sm-2 control-label text-left">หุ้น ต่อปี</label>
                            </div>
                        </div>
                        <?php
                            }
                        ?>
                        <div class="g24-col-sm-24">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label "></label>
                                <div class="g24-col-sm-4" style="text-align:center;">
                                    <button class="btn btn-primary" type="button" onclick="submit_form()"><span class="icon icon-save"></span> บันทึก</button>
                                </div>
                            </div>
                        </div>
                    </form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
    function submit_form(){
        $('#form1').submit();
    }
</script>
