<div class="modal fade" id="process_filter_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">ประมวลผลผ่านรายการ</h2>
			</div>
			<?php //echo '<pre>'; print_r($row); echo '</pre>';
				$month = (int)date('m');
				$year = (date('Y')+543);
			?>
			<div class="modal-body">
				<form action="<?php echo base_url(PROJECTPATH."/finance/finance_month_process"); ?>" method="POST" data-toggle="validator" enctype="multipart/form-data" id="form_process_filter_modal">
				<div class="g24-col-sm-24 modal_data_input">
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label">ปี</label>
						<div class="g24-col-sm-6 m-b-1">
							<select class="form-control" name="year">
								<?php for($y=(date('Y')+540);$y<=(date('Y')+546);$y++){ ?>
									<option value="<?php echo $y; ?>" <?php echo $y==$year?'selected':''; ?>><?php echo $y; ?></option>
								<?php } ?>
							</select>
						</div>
						<label class="g24-col-sm-5 control-label">เดือน</label>
						<div class="g24-col-sm-6 m-b-1">
							<select class="form-control" name="month">
								<?php foreach($month_arr as $key => $value){ ?>
									<option value="<?php echo $key; ?>" <?php echo $key==$month?'selected':''; ?>><?php echo $value; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label">หน่วยงานหลัก</label>
						<div class="g24-col-sm-6 m-b-1">
							<div class="form-group">
								<select class="form-control" name="department" id="department_menu" onchange="change_mem_group_menu('department_menu', 'faction_menu')" title=" " required>
									<option value="">เลือกข้อมูล</option>
									<?php foreach($mem_group as $key => $value){ ?>
										<option value="<?php echo $value['id']; ?>" <?php echo @$_GET['department']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<label class="g24-col-sm-5 control-label right"> อำเภอ </label>
						<div class="g24-col-sm-6">
							<div class="form-group">
								<select name="faction" id="faction_menu" onchange="change_mem_group_menu('faction_menu','level_menu')" class="form-control" title=" " required>
									<option value="">เลือกข้อมูล</option>
									<?php foreach($faction as $key => $value){ ?>
										<option value="<?php echo $value['id']; ?>" <?php echo @$_GET['faction']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> หน่วยงานย่อย </label>
						<div class="g24-col-sm-6">
							<div class="form-group">
								<select name="level" id="level_menu" class="form-control" title=" " required>
									<option value="">เลือกข้อมูล</option>
									<?php foreach($level as $key => $value){ ?>
										<option value="<?php echo $value['id']; ?>" <?php echo @$_GET['level']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<label class="g24-col-sm-5 control-label">ประเภทสมาชิก</label>
						<div class="g24-col-sm-6 m-b-1">
							<select class="form-control" name="mem_type_id" id="mem_type_id">
								<option value="">เลือกข้อมูล</option>
								<?php foreach($mem_type as $key => $value){ ?>
									<option value="<?php echo $value['mem_type_id']; ?>" <?php echo @$_GET['mem_type_id']==$value['mem_type_id']?'selected':''; ?>><?php echo $value['mem_type_name']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> จำนวนแสดงรายการ </label>
						<div class="g24-col-sm-6">
							<select name="show_row" id="show_row" class="form-control">
								<option value="100">100</option>
								<option value="500">500</option>
								<option value="1000">1000</option>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24 m-t-1" style="text-align:center;">
						<div class="g24-col-sm-24">
							<input type="submit" class="btn btn-primary" value="ค้นหา">
						</div>
					</div>
				</div>
				</form>
				&nbsp;
			</div>
		</div>
	</div>
</div>
<script>
	function change_mem_group_menu(id, id_to){
		var mem_group_id = $('#'+id).val();
		$('#level_menu').html('<option value="">เลือกข้อมูล</option>');
		$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/get_mem_group_list',
			data: {
				mem_group_id : mem_group_id
			},
			success: function(msg){
				$('#'+id_to).html(msg);
			}
		});
	}
</script>
<?php
	
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/elephant.min.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
	$link = array(
          'src' => PROJECTJSPATH.'assets/js/application.min.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
	  $link = array(
          'src' => PROJECTJSPATH.'assets/js/jquery-migrate-1.4.1.min.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/bootstrap-datepicker/bootstrap-datepicker-thai.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/bootstrap-datepicker/locales/bootstrap-datepicker.th.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);

      $link = array(
          'src' => PROJECTJSPATH.'assets/js/fancybox/jquery.fancybox.js?v=2.1.5',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/fancybox/jquery.mousewheel-3.0.6.pack.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/fancybox/helpers/jquery.fancybox-media.js?v=1.0.6',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/fancybox/helpers/jquery.fancybox-thumbs.js?v=1.0.7',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);

      $link = array(
          'src' => PROJECTJSPATH.'assets/js/toast.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);

      $link = array(
          'src' => PROJECTJSPATH.'assets/js/jquery.blockUI.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/sweetalert.min.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/moment.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/bootstrap-datetimepicker.min.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);

		$link = array(
          'src' => PROJECTJSPATH.'assets/js/center_js.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
		$link = array(
			'src' => PROJECTJSPATH.'assets/js/jquery.number_format.js',
			'type' => 'text/javascript'
		);
		echo script_tag($link);
?>
    <div class="layout-footer" style="z-index:-1 !important;">
        <div class="layout-footer-body">
            <small class="version">Version 1.0.0</small>
            <small class="copyright">UpbeanCOOP by <a href="http://upbean.co.th/">Upbean Co.,Ltd</a></small>
        </div>
    </div>
</div>

<div class="theme">
    <div class="theme-panel theme-panel-collapsed">
        <div class="theme-panel-body">
            <div class="custom-scrollbar">
                <div class="custom-scrollbar-inner">
                    <ul class="theme-settings">
                        <li class="theme-settings-heading">
                            <div class="divider">
                                <div class="divider-content">Theme Settings</div>
                            </div>
                        </li>
                        <li class="theme-settings-item">
                            <div class="theme-settings-label">Header fixed</div>
                            <div class="theme-settings-switch">
                                <label class="switch switch-primary">
                                    <input class="switch-input" type="checkbox" name="layout-header-fixed" data-sync="true">
                                    <span class="switch-track"></span>
                                    <span class="switch-thumb"></span>
                                </label>
                            </div>
                        </li>
                        <li class="theme-settings-item">
                            <div class="theme-settings-label">Sidebar fixed</div>
                            <div class="theme-settings-switch">
                                <label class="switch switch-primary">
                                    <input class="switch-input" type="checkbox" name="layout-sidebar-fixed" data-sync="true">
                                    <span class="switch-track"></span>
                                    <span class="switch-thumb"></span>
                                </label>
                            </div>
                        </li>
                        <li class="theme-settings-item">
                            <div class="theme-settings-label">Sidebar sticky*</div>
                            <div class="theme-settings-switch">
                                <label class="switch switch-primary">
                                    <input class="switch-input" type="checkbox" name="layout-sidebar-sticky" data-sync="true">
                                    <span class="switch-track"></span>
                                    <span class="switch-thumb"></span>
                                </label>
                            </div>
                        </li>
                        <li class="theme-settings-item">
                            <div class="theme-settings-label">Sidebar collapsed</div>
                            <div class="theme-settings-switch">
                                <label class="switch switch-primary">
                                    <input class="switch-input" type="checkbox" name="layout-sidebar-collapsed" data-sync="false">
                                    <span class="switch-track"></span>
                                    <span class="switch-thumb"></span>
                                </label>
                            </div>
                        </li>
                        <li class="theme-settings-item">
                            <div class="theme-settings-label">Footer fixed</div>
                            <div class="theme-settings-switch">
                                <label class="switch switch-primary">
                                    <input class="switch-input" type="checkbox" name="layout-footer-fixed" data-sync="true">
                                    <span class="switch-track"></span>
                                    <span class="switch-thumb"></span>
                                </label>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    $( document ).ready(function() {
        $('#myModal').on('shown.bs.modal', function() {
            $('#search_mem').focus();
        });
        $('.modal').on('shown.bs.modal', function() {
            $.blockUI({
                message: '',
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff'
                },
                baseZ: 2000,
                bindEvents: false
            });
        });
        var prev_id;
        $('.modal').on('hide.bs.modal', function () {
            if(this.id != 'cal_period_normal_loan' && this.id != 'show_file_attach' && this.id != 'search_member_loan_modal'){
                $.unblockUI();
            }

        });

        // Toast
        var toast = "<?php echo isset($_COOKIE['toast']) ? $_COOKIE['toast'] : "" ?>";
        if(toast) {
            toastNotifications(toast);
        }
        // Toast Danger
        var toast_e = "<?php echo isset($_COOKIE['toast_e']) ? $_COOKIE['toast_e'] : "" ?>";
        if(toast_e) {
            toastDanger(toast_e);
        }
		bodyOnload();
    });
function bodyOnload(){
	call_notification();
	setTimeout("doLoop();",10000);
}
function doLoop(){
	bodyOnload();
}
function call_notification(){
	$.ajax({
        type: "POST"
        , url: base_url+'notification/call_notification'
        , data: {
            "do" : "call_notification"
        }
        , success: function(data) {
			var obj = jQuery.parseJSON( data );
			$('.notification_count').html(obj.notification_count);
            $('#notification_space').html(obj.notification_body);
        }
    });
}
function open_menu_modal(id){
	$('#'+id).modal('show');
}
</script>
<!-- search_member -->
<?php
    $link = array(
        'src' => PROJECTJSPATH.'assets/js/search_member.js',
        'language' => 'javascript',
        'type' => 'text/javascript'
    );
    echo script_tag($link);
?>
<!-- search_member -->

<!-- check_bt_submit -->
<?php
	$v = date('YmdHis');
    $link = array(
        'src' => PROJECTJSPATH.'assets/js/check_bt_submit.js?v='.$v,
        'language' => 'javascript',
        'type' => 'text/javascript'
    );
    echo script_tag($link);
?>
<!-- check_bt_submit -->
            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                    <strong></strong>
                </div>
                <div></div>
            </footer>
    </body>
</html>
