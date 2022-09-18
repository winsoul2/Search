<?php
		$arr_data[] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

?>
<div class="layout-content">
    <div class="layout-content-body">
		<h1>รอบบัญชี</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 m-b-2">
		<?php $this->load->view('breadcrumb'); ?>
		</div>

			<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">  
                <div class="bs-example" data-example-id="striped-table">
                <form id="form1" data-toggle="validator" novalidate="novalidate" action="#" method="post">
						<h3></h3>
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label right"> รอบบัญชีสหกรณ์ </label>
									<div class="g24-col-sm-4">
                                    <select id="month" name="month" class="form-control">
                                                        <?php foreach($arr_data[0] as $index => $val){
                                                                $chk = $row['accm_month_ini'] == $index ? ' selected="selected" ' : "";
                                                                echo "<option ".$chk." value='".$index."|".$val."'>".$val."</option>";

                                                        } ?>
                                    </select>
									</div>
									
								</div>
							</div>
							<div class="g24-col-sm-24">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label "></label>
									<div class="g24-col-sm-4" style="text-align:center;">
										<button class="btn btn-primary" type="submit"><span class="icon icon-save"></span> บันทึก</button>
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
  var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
  };

  var loadFile1 = function(event) {
    var output = document.getElementById('output1');
    output.src = URL.createObjectURL(event.target.files[0]);
  };

  var loadFile2 = function(event) {
    var output = document.getElementById('output2');
    output.src = URL.createObjectURL(event.target.files[0]);
  };
</script>