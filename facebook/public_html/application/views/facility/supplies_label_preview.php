<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}
	.table-view-2>thead>tr>th{
	    border-top: 1px solid #000 !important;
		border-bottom: 1px solid #000 !important;
		font-size: 16px;
	}
	.table-view-2>tbody>tr>td{
	    border: 0px !important;
		font-family: Tahoma;
		font-size: 12px;
	}	
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}
	.foot-border{
	    border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}
	.table {
		color: #000;
	}
	@media print {
		.pagination {
			display: none;
		}
	}
</style>
<?php
    foreach($datas as $page=> $data) {
?>
    <div style="width: 11.93in;" class="page-break">
        <div class="panel panel-body" style="padding:50px !important;height: 15.98in;">
        <div class="row">
            <?php
                foreach($data as $store) {
            ?>
            <div class="col-sm-4" style="padding:0px 10px; border-style: solid;border-width: 1px;border-color: black; font-size: 15px;">
                <div class="row">
                    <div class="g24-col-sm-16 text-left" style="padding:0; padding-left:5px;">
                        <label class="g24-col-sm-24 control-label text-left">
                            item : <?php echo $store["store_name"];?>
                        </label>
                        <label class="g24-col-sm-24 control-label text-left">
                            No : <?php echo $store["store_code"];?>
                        </label>
                        <label class="g24-col-sm-24 control-label text-left">
                            Year : <?php echo $store["budget_year"];?>
                        </label>
                    </div>
                    <div class="g24-col-sm-6" style="padding:0;">
                        <img src="<?php echo base_url(PROJECTPATH.'/facility/qrcode_generate?text='.base_url(PROJECTPATH.'/facility/supplies_info?id='.$store["store_id"]))?>" alt="code" height="100" width="100">
                    </div>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
        </div>
    </div>
<?php
    }
?>
