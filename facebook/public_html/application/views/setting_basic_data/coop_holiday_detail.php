<div class="layout-content">
    <div class="layout-content-body">
		<link rel="stylesheet" href="/assets/js/fullcalendar/fullcalendar.min.css" />
		<style>
			.indent{
				text-indent: 40px;
				.modal-dialog-data {
					width:90% !important;
					margin:auto;
					margin-top:1%;
					margin-bottom:1%;
				}
			}
			table>thead>tr>th{
				text-align: center;
			}
			table>tbody>tr>td{
				text-align: center;
			}

			label {
				padding-top: 6px;
				text-align: right;
			}
			.text-center{
				text-align:center;
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
			
			.layout { overflow-x: visible !important; }
			.fc-event { font-size: 13px; line-height: 20px; }
			.fc-day { cursor: pointer; }
			.fc-day:hover { background-color: #eee; }
		</style>
		
		<h1 style="margin-bottom: 0">วันหยุดสหกรณ์</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
		
		<div class="row gutter-xs">
			<div class="col-md-12">
				<div class="panel panel-body">
					<form class="form form-horizontal" action="<?php echo base_url(PROJECTPATH.'/setting_basic_data/save_coop_holiday_detail'); ?>" method="post" enctype="multipart/form-data">
						<input type="hidden" name="type" value="1">
						<input type="hidden" name="work_year" value="<?php echo $row["work_year"]; ?>">
						<div class="text-center">
							<h3>วันหยุดประจำปี <?php echo $row["work_year"] + 543; ?></h3>
							<p style="font-weight: bold;">หยุดทุกว้น</p>
							<?php
							$days = [
								1 => "จันทร์",
								2 => "อังคาร",
								3 => "พุธ",
								4 => "พฤหัสบดี",
								5 => "ศุกร์",
								6 => "เสาร์",
								7 => "อาทิตย์"
							];
							
							$holidays = explode(",", $row["holidays"]);
							
							foreach($days as $key => $day) { ?>
								<label>
									<input type="checkbox" name="holidays[]" value="<?php echo $key; ?>"<?php if(in_array($key, $holidays)) { ?> checked="checked"<?php } ?>> <?php echo $day; ?> &nbsp;
								</label>
							<?php } ?>
						</div>
						
						<div class="form-group text-center p-y-lg">
							<button type="submit" class="btn btn-primary min-width-100">ตกลง</button>
							<a href="?" class="btn btn-danger min-width-100">ยกเลิก</a>
						</div>
					</form>
				</div>
			</div>
			
			<div class="col-md-8">
				<div class="panel panel-body" data-toggle="match-height">
					<h3 class="text-center">วันหยุดนักขัตฤกษ์</h3>
					<div id="calendar" style="margin: 0 auto; max-width: 1000px;"></div>
				</div>
			</div>
			
			<div class="col-md-4">
				<div class="panel panel-body" data-toggle="match-height">
					<h3 class="text-center">วันหยุดตลอดปี <?php echo $row["work_year"] + 543; ?></h3>
					<div id="holiday_list_wrap"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="holiday_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title"><span id="title_1">วันหยุด</span></h2>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<div class="row">
						<label class="col-sm-4 control-label" for="type_code">รายละเอียด</label>
						<div class="col-sm-4">
							<input type="text" id="holiday_title" name="holiday_title" value="" class="form-control m-b-1">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 m-t-1" style="text-align:center;">
							<button type="button" id="btn_holiday_save" class="btn btn-primary">บันทึก</button>&nbsp;&nbsp;&nbsp;
							<button type="button" id="btn_holiday_delete" class="btn btn-danger">ลบ</button>&nbsp;&nbsp;&nbsp;
							<button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
						</div>
					</div>
				</div>				
			</div>
		</div>
	</div>
</div>

<script src="/assets/js/fullcalendar/lib/moment.min.js"></script>
<script src="/assets/js/fullcalendar/fullcalendar.min.js"></script>
<script src="/assets/js/fullcalendar/locale/th.js"></script>
<script>
	var base_url = $('#base_url').attr('class');
	
	$(document).ready(function() {
		var cal_id = 1;
		var currentEvent = {
			"id":  "",
			"title":  "",
			"start":  "",
			"end":  ""
		};
		
		var calendar = $("#calendar").fullCalendar({
			defaultDate: "<?php echo $row["work_year"]."-01-01"; ?>",
			showNonCurrentDates: false,
			editable: false,
			events: { url: base_url + "/setting_basic_data/get_coop_holiday" },
			selectable: true,
			select: function(start, end) {
				var status = "new";
				var edit_id = "";
				var objs = calendar.fullCalendar("clientEvents");
				for(x in objs) {
					if(start.format() == objs[x].start.format()) {
						status = "edit";
						edit_id = x;
						break;
					}
				}
				
				if(status == "new") {
					currentEvent["id"] = "i_" + cal_id;
					currentEvent["start"] = start;
					currentEvent["end"] = start;
					
					$("#holiday_title").val("วันหยุด");
					$("#holiday_modal").modal("show");
				}
				else if(status == "edit") {
					var edit_event = objs[edit_id];
					currentEvent["id"] = edit_event.id;
					currentEvent["title"] = edit_event.title;
					currentEvent["start"] = edit_event.start;
					currentEvent["end"] = edit_event.end;
					
					$("#holiday_title").val(currentEvent["title"]);
					$("#holiday_modal").modal("show");
				}
				
				//calendar.fullCalendar("unselect");
			},
			 eventClick: function(calEvent, jsEvent, view) {
				currentEvent["id"] = calEvent.id;
				currentEvent["title"] = calEvent.title;
				currentEvent["start"] = calEvent.start;
				currentEvent["end"] = calEvent.end;
				
				$("#holiday_title").val(currentEvent["title"]);
				$("#holiday_modal").modal("show");
			 }
		});
		
		$("#btn_holiday_save").click(function() {
			currentEvent["title"] = $("#holiday_title").val();
			
			$.ajax({
				url: base_url + "/setting_basic_data/save_coop_holiday_detail",
				method: "POST",
				data: {
					"type": 2,
					"holiday_date": currentEvent["start"].format(),
					"holiday_title": currentEvent["title"]
				},
				success: function(msg){
					calendar.fullCalendar("refetchEvents");
					show_holiday_list(currentEvent["start"].year());
				}
			});
			
			$("#holiday_modal").modal("hide");
		});
		
		$("#btn_holiday_delete").click(function() {
			swal({
				title: "ท่านต้องการลบข้อมูลใช่หรือไม่",
				text: "",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'ลบ',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: true,
				closeOnCancel: true
			},
			function(isConfirm) {
				if(isConfirm) {
					$.ajax({
						url: base_url + "/setting_basic_data/del_coop_holiday_detail",
						method: "POST",
						data: {
							"holiday_date": currentEvent["start"].format()
						},
						success: function(msg){
							calendar.fullCalendar("refetchEvents");
							show_holiday_list(currentEvent["start"].year());
						}
					});
					
					$("#holiday_modal").modal("hide");
				}
			});
		});
		
		function show_holiday_list(y) {
			$.ajax({
				url: base_url + "/setting_basic_data/get_coop_holiday_list",
				method: "POST",
				data: {
					"y": y
				},
				success: function(res){
					data = JSON.parse(res);
					$("#holiday_list_wrap").html(data.html);
				}
			});
		}
		
		show_holiday_list("<?php echo $row["work_year"]; ?>");
	});
</script>