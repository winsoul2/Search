<div class="layout-content">
    <div class="layout-content-body">
	<style>
		label{
			padding-top:7px;
		}
		.control-label{
			padding-top:7px;
		}
		.indent{
			text-indent: 40px;
			.modal-dialog-data {
				width:90% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
		}
		.bt-add{
			float:none;
		}
		.modal-dialog{
			width:80%;
		}
		small{
			display: none !important;
		}
		.cke_contents{
			height: 500px !important;
		}
		th{
			text-align:center;
		}
		
		.modal-dialog {
			width: 500px;
		}
	</style>
	<?php
	$act = @$_GET['act'];
	$id  = @$_GET['id'];
	$detail_id  = @$_GET['detail_id'];
	?> 

		<h1 style="margin-bottom: 0">ประมาณการประกันชีวิต</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">	

			</div>
		</div>	
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<form action="" id="form1" method="GET" target="_blank">
					<h3></h3>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-8 control-label right"> ประจำปี </label>
						<div class="g24-col-sm-4">
							<select id="year" name="year" class="form-control">
								<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
									<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-8 control-label right"></label>
						<div class="g24-col-sm-4">
							<input type="button" class="btn btn-primary" style="width:100%" value="แสดงรายการ" onclick="check_empty_excel()">
						</div>
					</div>
				</form>				
				</div>
			</div>
		</div>

	</div>
</div>

<script>
	function check_empty_excel(){				
		link_to =  base_url+'life_insurance_data/life_insurance_year_excel';
		$('#form1').attr('action', link_to);
		$('#form1').submit();

	}

</script>