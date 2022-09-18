<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ãÊèÃÙ»ÀÒ¾</title>
<meta http-equiv="Content-Type" content="text/html; charset=tis-620" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<script type="text/javascript" src="/scripts/ckeditor/jquery.js"></script>
<script type="text/javascript" src="/scripts/jquery.fileupload.js"></script>
<script type="text/javascript" src="/scripts/jquery.cookies.2.2.0.min.js"></script>
<script type="text/javascript">
		$(document).ready(function(){
		$("#btnSubmit").click(function() {
			$("#div_upload").css("display","block");
			$.ajaxFileUpload({
					url:'upload.file.ajax.php',
					secureuri:false,
					fileElementId:'file',
					dataType: 'json',
					success: function (data, status) {
						console.log("data => " + data);
						console.log("status => " + status);
						if(typeof(data.error) != 'undefined') {
							if(data.error != '') {
								console.log(data.error);
							} else {
								$.cookies.set("return", data.msg);
							}
							parent.jQuery.fancybox.close();
						}
						$("#div_upload").css("display","none");
					},
					error: function (data, status, e) {
						console.log(data);
						console.log(status);
						console.log(e);
						alert('กรุณาเลือกไฟล์');
						$("#div_upload").css("display","none");
					}
				});
			});
		});
</script>
<style type="text/css">
	* {
		font-family: "Microsoft Sans sarif";
		font-size: 13px;
	}
	body {
		margin: 0px;
		overflow: hidden;
	}
</style>

</head>
<body>
  <form id="frmUpload" name="frmUpload" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" enctype="multipart/form-data">
          <div style="text-align: center;margin: 10px auto;width: 450px;border: 1px solid #CCCCCC;background-color: #F1F1F1;padding: 10px 5px 10px 5px;">
          	<div><b>เลือกไฟล์ : </b> <input id="file" name="file" type="file" style="width:200px" /></div>
          	<hr size="1" />
				<div id="div_upload" style="display: none;text-align: center;">
					<table border="0" width="150" cellpadding="3" cellspacing="0" style="margin: 0 auto 0 auto;">
						<tr>
							<td width="32"><img id="img_upload" src="/images/wait.gif" /></td>
							<td valign="middle" align="left"><b>กรุณารอสักครู่</b></td>
						</tr>
					</table>
				</div>
            <div>
				<input id="btnSubmit" name="btnSubmit" type="button" title="Send" value="ตกลง" style="width: 75px;" />
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input id="btnClose" name="btnClose" type="button" title="Close" onclick="parent.jQuery.fancybox.close();" value="ปิดหน้าต่าง" style="width: 75px;" />
            </div>
          </div>
  </form>
</body>
</html>