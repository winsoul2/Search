<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>����ٻ�Ҿ</title>
<meta http-equiv="Content-Type" content="text/html; charset=tis-620" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<script type="text/javascript" src="/scripts/ckeditor/jquery.js"></script>
<script type="text/javascript" src="/scripts/jquery.fileupload.js"></script>
<script type="text/javascript" src="/scripts/jquery.cookies.2.2.0.min.js"></script>
<link href="/html/frontend/font/font-awesome/4.3.0/css/font-awesome.css" />

<script type="text/javascript">
		$(document).ready(function(){
		$("#btnSubmit").click(function() {
			$("#div_upload").css("display","block");
			$.ajaxFileUpload({
					url:'upload.ajax.php',
					secureuri:false,
					fileElementId:'file',
					dataType: 'json',
					success: function (data, status) {
						if(typeof(data.error) != 'undefined')
						{
							if(data.error != '') {
								alert(data.error);
							} else {
								$.cookies.set("return", data.msg);
								//alert(data.msg);
							}
							parent.jQuery.fancybox.close();
						}
						$("#div_upload").css("display","none");
					},
					error: function (data, status, e) {
						alert(e);
						//window.close();
						alert('�����ѧ��������͡����ٻ');
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
          	<div><b>���͡����ٻ:</b> <input id="file" name="file" type="file" style="width:200px" /></div>
          	<hr size="1" />
				<div id="div_upload" style="display: none;text-align: center;">
					<table border="0" width="150" cellpadding="3" cellspacing="0" style="margin: 0 auto 0 auto;">
						<tr>
							<td width="32"><span><i class="fa fa-spinner fa-spin fa-3x" ></i></span><!-- <img id="img_upload" src="/images/wait.gif" /> --></td>
							<td valign="middle" align="left"><b>��س����ѡ����</b></td>
						</tr>
					</table>
				</div>
            <div>
				<input id="btnSubmit" name="btnSubmit" type="button" title="Send" value="��ŧ" style="width: 75px;" />
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input id="btnClose" name="btnClose" type="button" title="Close" onclick="parent.jQuery.fancybox.close();" value="�Դ˹�ҵ�ҧ" style="width: 75px;" />
            </div>
          </div>
          <div style="text-align: left;padding: 10px 5px 10px 5px;width: 450px;margin: 20px auto;border: 1px solid #CCCCCC;background-color: #FFFFFF;">
			<b><font color="#FF0000">��͡�˹�</font></b><br />
			<font color="#FF0000"><b>*</b></font> ��Ҵ����ٻ����Թ 5MB<br />
            <font color="#FF0000"><b>*</b></font> ��ͧ����� .jpg, .jpeg, .gif, .png ��ҹ��<br />
          </div>
  </form>
</body>
</html>