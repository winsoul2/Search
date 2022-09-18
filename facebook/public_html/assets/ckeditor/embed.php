<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ใส่รูปภาพ</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<script type="text/javascript" src="/scripts/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="/scripts/jquery-migrate-1.2.1.js"></script>
<script type="text/javascript" src="/scripts/jquery.cookies.2.2.0.min.js"></script>
<script type="text/javascript">
		$(document).ready(function(){
			$("body").on("click", "#btnSubmit", function() { 
				$.cookies.set("return_embed", $("#return").val());
				parent.jQuery.fancybox.close();
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
		background-color: #EEEEEE;
	}
</style>

</head>
<body>
  <form id="frmUpload" name="frmUpload" method="post" enctype="multipart/form-data">
          <div style="text-align: center;margin: 10px auto;width: 720px;border: 1px solid #CCCCCC;background-color: #F1F1F1;padding: 10px 5px 10px 5px;">
						<div style="text-align: left;padding-left: 5px;line-height: 180%;">ใส่ Embed Youtube หรืออื่น ๆ</div>
          	<div><textarea id="return" name="return" style="width: 700px;margin: 0 auto;height: 230px;"></textarea></div>
          	<hr size="1" />
            <div>
							<input type="hidden" name="do" value="return" />
								<input id="btnSubmit" name="btnSubmit" type="button" title="Send" value="ตกลง" style="width: 75px;" />
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input id="btnClose" name="btnClose" type="button" title="Close" onclick="parent.jQuery.fancybox.close();" value="ปิดหน้าต่าง" style="width: 75px;" />
            </div>
          </div>
  </form>
</body>
</html>