	<div class="text-primary">
		<div class="text-center">
			<img src="assets/images/coop_profile/<?php echo $profile["coop_img"]; ?>" alt="Logo" style="height: 280px;" />
		</div>
		<h1 class="text-center" style="font-size: 30px;"><?php echo $profile["coop_name_en"]; ?></h1>
		<h2 class="text-center" style="font-family: thaisans_neueregular;">ระบบบริหารงาน<?php echo $profile["coop_name_th"]; ?></h2>
	</div>

	<div class="login">
		<div class="login-body">
			<div style="padding-bottom: 20px;"><img class="img-responsive" src="assets/images/logo/logo_web2.gif" alt="UpbeanCoop"></div>
			<div class="login-form">
				<form method="post" action="">
					<input type="hidden" name="do" value="login" />
					<div class="form-group">
						<label for="email">ชื่อผู้ใช้</label>
						<input id="email" class="form-control" type="text" name="username" spellcheck="false" autocomplete="off" data-msg-required="กรุณาระบุชื่อผู้ใช้" required>
					</div>
					<div class="form-group">
						<label for="password">รหัสผ่าน</label>
						<input id="password" class="form-control" type="password" name="password" autocomplete="off" data-msg-required="กรุณาระบุรหัสผ่าน" required>
					</div>
					<button class="btn btn-primary btn-block" type="submit">เข้าสู่ระบบ</button>
					<?php if(!empty($err_msg)) { ?>
						<div class="text-center" style="padding-top: 15px;"><span class="label label-outline-danger"><?php echo @$err_msg; ?></span></div>
					<?php } ?>
				</form>
			</div>
		</div>
		<?php /*<div class="login-footer">
	  Don't have an account? <a href="signup-2.html">Sign Up</a>
	</div>*/ ?>
	</div>

	<div class="text-center" style="position: absolute; bottom: 15px; left: 0; right: 0;">Engine by UpbeanCOOP</div>