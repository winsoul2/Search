<div class="layout-content">
    <div class="layout-content-body">
<style>
	.form-group { margin-bottom: 0; }
	
	.permission_list { margin: 0 0 20px 0; padding: 0; list-style: none; }
	.permission_list ul { margin: 0 0 0 20px; padding: 0; list-style: none; }
	.mem_pic { float: right; width: 150px; }
    .mem_pic img { width: 100%; border: solid 1px #ccc; }
    .mem_pic button { display: block; width: 100%; }
</style>
<?php if(@in_array($_GET["do"] , array("add" , "edit") ) ) {
	if(@in_array($_GET["do"] , array("edit"))){		
		$btitle = "แก้ไขผู้ใช้งาน" ;
	}else{
		$row = array();
		$btitle = "เพิ่มผู้ใช้งาน" ;
	}
	
	$user_id = (int) @$row["user_id"]  ; 
	?>
	<h1 class="text-center m-t-1 m-b-2"><?php echo $btitle; ?></h1>
	<div class="col-md-6 col-md-offset-2">
		
		<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_user_save'); ?>" class="form form-horizontal" autocomplete="off" enctype="multipart/form-data">
			<input type="hidden" name="user_id" value="<?php echo $user_id; ?>"/>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="employee_id">รหัสพนักงาน</label>
				<div class="col-sm-9">
					<input type="text" id="employee_id" name="employee_id" class="form-control m-b-1" value="<?php echo @$row["employee_id"]; ?>" title="กรุณาป้อนรหัสพนักงาน" onchange="search_employee_id()">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="username">Username</label>
				<div class="col-sm-9">
					<input type="text" id="username" name="username" class="form-control m-b-1" value="<?php echo htmlspecialchars(@$row["username"]); ?>" required title="กรุณาป้อน Username" remote="<?php echo base_url(PROJECTPATH.'/setting_basic_data/chk_user?old_username='.@$row["username"]); ?>">
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-3 control-label" for="password">Password</label>
				<div class="col-sm-9">
					<input type="password" id="password" name="password" class="form-control m-b-1" value="<?php echo htmlspecialchars(@$row["password"]); ?>" required title="กรุณาป้อน Password">
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-3 control-label" for="user_name">ชื่อสกุล</label>
				<div class="col-sm-9">
					<input type="text" id="user_name" name="user_name" class="form-control m-b-1" value="<?php echo htmlspecialchars(@$row["user_name"]); ?>" required title="กรุณาป้อน ชื่อสกุล">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="user_email">อีเมล์</label>
				<div class="col-sm-9">
					<input type="text" id="user_email" name="user_email" class="form-control m-b-1" value="<?php echo htmlspecialchars(@$row["user_email"]); ?>" required title="กรุณาป้อน อีเมล์">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="user_tel">โทรศัพท์มือถือ</label>
				<div class="col-sm-9">
					<input type="text" id="user_tel" name="user_tel" class="form-control m-b-1" value="<?php echo htmlspecialchars(@$row["user_tel"]); ?>" required title="กรุณาป้อน หมายเลขโทรศัพท์มือถือ">
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-3 control-label" for="user_department">กำหนดสิทธิ์</label>
				<div class="col-sm-9" style="padding-top: 7px;">
					<ul class="permission_list">
						<?php						
						function get_permission_list($menus,$row,$admin_permissions) {
							$html = "";							
							foreach($menus as $menu) {
								$ckeck_box = '';
								if($admin_permissions){
									$ckeck_box = (chk_permission(@$menu["id"], NULL, NULL, @$row, @$admin_permissions) ? ' checked' : '');
								}
								$html .= '<li>
													<label class="custom-control custom-control-primary custom-checkbox">
														<input type="checkbox" id="user_permissions['.@$menu["id"].']" name="user_permissions['.@$menu["id"].']" value="1" class="custom-control-input permission_item" '.$ckeck_box.'>
														<span class="custom-control-indicator"></span>
														<span class="custom-control-label">'.@$menu["name"].'</span>
													</label>';
								if(!empty($menu["submenus"])) {
									$html .= '<ul>';
									$html .= get_permission_list(@$menu["submenus"],@$row,@$admin_permissions);
									$html .= '</ul>';
								}
								$html .= '</li>';
							}
							
							return $html;
						}
						
						echo get_permission_list(@$menus,@$row,@$admin_permissions);
						
						?>
					</ul>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="user_department">การแจ้งเตือน</label>
				<div class="col-sm-9">
					<ul class="permission_list">
					<?php 
					if(!empty($row_notification)){
						foreach($row_notification as $key => $value){ ?>
						<li>
							<label class="custom-control custom-control-primary custom-checkbox">
								<input type="checkbox" name="user_notification[]" value="<?php echo $value['notification_id']; ?>" class="custom-control-input permission_item" <?php echo @$user_notification[$value['notification_id']]=='1'?'checked':''; ?>>
								<span class="custom-control-indicator"></span>
								<span class="custom-control-label"><?php echo $value['notification']; ?></span>
							</label>
						</li>
					<?php }
					} ?>
					</ul>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-3 control-label" for="user_status">&nbsp;</label>
				<div class="col-sm-9">
					<label class="custom-control custom-control-primary custom-checkbox">
						<input type="checkbox" id="user_status" name="user_status" <?php echo @$row["user_status"] == 1 || @$_GET["do"] == "add" ? "checked" : "" ; ?> value="1" class="custom-control-input">
						<span class="custom-control-indicator"></span>
						<span class="custom-control-label">เปิดการใช้งาน</span>
					</label>
				</div>
			</div>
			
			<div class="form-group text-center p-y-lg">
				<button type="submit" class="btn btn-primary min-width-100">ตกลง</button>
				<a href="?" class="btn btn-danger min-width-100">ยกเลิก</a>
			</div>
			<input type="hidden" name="user_pic" id="user_pic">
		</form>
	
	</div>
	<div class="g24-col-sm-4">
		<div class="g24-col-sm-24 m-b-1 text-right">
			<div class="mem_pic" style="margin-bottom:20px;display: block;margin: 0 auto;">
				<?php $user_pic = empty($row['user_pic']) ? "default.png" : $row['user_pic'];?>
				<img id="member_pic" src="<?php echo base_url(PROJECTPATH.'/assets/uploads/user_pic/'.$user_pic); ?>" alt="" />
				
				<button type="button" id="btn_member_pic" class="btn btn-info">รูปภาพสมาชิก</button>
			</div>
		</div>
	</div>
<?php } else { ?>
	<h1 style="margin-bottom: 0">กำหนดผู้ใช้งาน</h1>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<a class="btn btn-primary btn-lg bt-add" href="?do=add">
				<span class="icon icon-plus-circle"></span>
				เพิ่มผู้ใช้งาน
			</a>
		</div>
	</div>
	
	<div class="row gutter-xs">
		<div class="col-xs-12 col-md-12">
		  <div class="panel panel-body">
			
			<div class="bs-example" data-example-id="striped-table">				
				<table class="table table-striped"> 
					<thead> 
						<tr>
							<th style="width: 50px;">#</th>
							<th>รหัสพนักงาน</th>
							<th>ชื่อสกุล</th>
							<th>Username</th>
							<th style="width: 200px;">Password</th> 
							<th style="width: 200px;"></th> 
						</tr> 
					</thead>
					
					<tbody>
					<?php  
					if(!empty($rs)){
						foreach(@$rs as $key => $row){ 
					?>
							<tr>
								<td scope="row"><?php echo $i++; ?></td>
								<td><?php echo @$row["employee_id"]; ?></td> 
								<td><?php echo @$row["user_name"]; ?></td> 
								<td><?php echo @$row["username"]; ?></td> 
								<td>
									<span class="showpass" data-pass="<?php echo @$row["password"]; ?>" style="cursor: pointer;">คลิกแสดง password</span>
								</td> 
								<td>
									<a href="?do=edit&id=<?php echo @$row["user_id"]; ?>">แก้ไขและกำหนดสิทธิ์</a>
									<?php if(@$row["user_type_id"] != 1) { ?>| <span class="text-del del"  onclick="del_coop_user('<?php echo @$row['user_id'] ?>')">ลบ</span><?php } ?>
								</td>
							</tr>
					<?php 
							}
						} 
					?>
					</tbody> 
				</table>
			</div>
	
		  </div>
		  <?php echo $paging ?>
		</div>
	</div>
<?php 
	}
?>

	</div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/jquery.cookies.2.2.0.min.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_user.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>