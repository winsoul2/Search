<div class="layout-content">
    <div class="layout-content-body">
<style>
    .form-group { margin-bottom: 0; }
    .form-horizontal .control-label { text-align: left; }

    .permission_list { margin: 0 0 20px 0; padding: 0; list-style: none; }
    .permission_list ul { margin: 0 0 0 20px; padding: 0; list-style: none; }
	.mem_pic { float: right; width: 150px; }
    .mem_pic img { width: 100%; border: solid 1px #ccc; }
    .mem_pic button { display: block; width: 100%; }
</style>
<?php
$btitle = "แก้ไขข้อมูลส่วนตัว" ;
?>
<h1 class="text-center m-t-1 m-b-1"><?php echo $btitle; ?></h1>
    <div class="col-md-6 col-md-offset-2">

        

        <form data-toggle="validator" method="post" action="?" class="form form-horizontal">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="username">Username</label>
                <div class="col-sm-9">
                    <p class="form-control-static m-b-1"><?php echo htmlspecialchars(@$user["username"]); ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="password">Password</label>
                <div class="col-sm-9">
                    <input type="password" id="password" name="password" class="form-control m-b-1" value="<?php echo htmlspecialchars(@$user["password"]); ?>" required title="กรุณาป้อน Password">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="password">Re Password</label>
                <div class="col-sm-9">
                    <input type="password" id="re_password" name="re_password" class="form-control m-b-1" value="<?php echo htmlspecialchars(@$user["password"]); ?>" required title="Re Password ไม่เหมือนกับ Password" equalTo="#password">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="user_name">ชื่อสกุล</label>
                <div class="col-sm-9">
                    <input type="text" id="user_name" name="user_name" class="form-control m-b-1" value="<?php echo htmlspecialchars(@$user["user_name"]); ?>" required title="กรุณาป้อน ชื่อสกุล">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="user_department">แผนก</label>
                <div class="col-sm-9">
                    <input type="text" id="user_department" name="user_department" class="form-control m-b-1" value="<?php echo htmlspecialchars(@$user["user_department"]); ?>">
                </div>
            </div>

            <div class="form-group text-center p-y-lg">
                <button type="submit" class="btn btn-primary min-width-100">ตกลง</button>
                <a href="?"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
            </div>
			<input type="hidden" name="user_pic" id="user_pic">
        </form>

    </div>
	<div class="g24-col-sm-4">
		<div class="g24-col-sm-24 m-b-1 text-right">
			<div class="mem_pic" style="margin-bottom:20px;display: block;margin: 0 auto;">
				<?php $user_pic = empty($user['user_pic']) ? "default.png" : $user['user_pic'];?>
				<img id="member_pic" src="<?php echo base_url(PROJECTPATH.'/assets/uploads/user_pic/'.$user_pic); ?>" alt="" />
				
				<button type="button" id="btn_member_pic" class="btn btn-info">รูปภาพสมาชิก</button>
			</div>
		</div>
	</div>
    </div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/jquery.cookies.2.2.0.min.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
<script>
$( document ).ready(function() {
	$("#btn_member_pic").click(function() {
        $.fancybox({
            'href' : base_url+'main_menu/member_lb_upload'
            , 'padding' : '10'
            , 'width': 520
            , 'modal' : true
            , 'type' : 'iframe'
            , 'autoScale' : false
            , 'transitionIn' : 'none'
            , 'transitionOut' : 'none'
            , afterClose : function() {
                console.log($.cookies);
                if($.cookies.get('is_upload')){
					 get_image();
				}
            }
        });

        return false;
    });
});

function get_image() {
    $.ajax({
        type: "POST"
        , url: base_url+'main_menu/get_image'
        , data: {
            "do" : "get_image"
            , _time : Math.random()
        }
        , success: function(data) {
            $("#member_pic").attr("src", data);
			$("#user_pic").val(data);
        }
    });
}
</script>