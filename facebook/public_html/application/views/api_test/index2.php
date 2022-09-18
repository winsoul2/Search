<div class="layout-content">
    <div class="layout-content-body">
		<div class="row gutter-xs"> 
			<div class="row">
				<div  class="col-md-9 col-md-offset-1">
                   <br>
                   <br>
	                <br>
					<?php 
					
					foreach($tran_type_atm AS $key=>$value){					
					?>
					<a class="link-line-none" href="?type=<?php echo $value['tran_type_code'];?>">
						<button class="btn btn-primary" style="margin-right:5px;width: auto;"><?php echo $value['tran_type_description'];?></button>
					</a>
					<?php					
					}
						
					?>
					
					<?php
						if(@$_GET['type'] != ''){
					?>
					<br>
					<br>
					<form class="form form-horizontal" action="<?php echo base_url(PROJECTPATH.'/api_2/atm_request'); ?>" method="post">
						<input name="" type="hidden" value="">
						<?php 
						//echo '<pre>'; print_r($tran_type[0]); echo '</pre>';
						if(!empty($tran_type[@$_GET['type']])){
						foreach(@$tran_type[@$_GET['type']] AS $key2=>$value2){
						?>
						<div class="row">
							<label class="col-sm-5 control-label" for="form-control-1"><?php echo @$key2;?></label>
							<div class="col-sm-2">
							  <input id="form-control-1" name="<?php echo $key2;?>" id="<?php echo @$key2;?>" class="form-control m-b-1" type="text" value="<?php echo $value2;?>" style="text-align: center;">
							</div>
						</div>
						<?php } ?>
					  <div class="form-group m-t-1">
						<label class="col-sm-5 control-label" for="form-control-1"></label>
						<div class="col-sm-5">
							<button type="submit" class="btn btn-primary min-width-100">ตกลง</button>
						</div>
					  </div>  
					</form>
					<?php 
							}
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>


