<?php
/**
* @comment ส่งออกไฟล์ doc
* @projectCode 58PTY01
* @tor 3.1.4
* @package core
* @author Kiatisak  Chansawang
* @access public/private
* @created 31/05/2016
*/
# ตัวอย่างไฟล์การส่งออก ms word + จัด format file เข้ารหัสไฟล์แบบ utf-8
# รูปแบบ พรีวิวก่อนทำการกดส่งออก
header('Content-Type: text/html; charset=utf-8');
include('define_config_db.php');
?>
<html>
<head>
<!-- ไม่ต้องระบุ charset -->
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/bootstrap3.min.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/table_style.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/font_style.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/AllFont.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/page_loading.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/sweetalert.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/main.css" />
<!-- ไม่ต้องระบุ charset -->
</head>
<script type="application/javascript" src="common/global/js/jquery-1.10.2.js"></script>
<script>
$(document).ready(function(){
	$('#export').click(function(){
		var headContent = $('head').html();
		var bodyContent = $('#exportData').html();
		$.ajax({
			method: "POST",
			url: "html2doc_setContent.php",
			data: {htmlHead: headContent, htmlBody: bodyContent, exportData : 'on'}
		}) .done(function( msg ) {
			if($.trim(msg) == 'ok'){
				window.open('html2doc.php','_blank');
			}
		});
	});
});
</script>
<style>
	@page Section1 {
		size: 8.5in 11.0in;
		margin: 1.0in 1.25in 1.0in 1.25in ;
		mso-header-margin: .5in;
		mso-footer-margin: .5in;
		mso-paper-source: 0;
	}

	#exportData {
		float:left;
		width:auto;
		height:auto;
	}
</style>
<body>
<input type="button" id="export" value="ส่งออก">
<div id="exportData">
<div style="width:595px; height:842px; margin:0px;">
	<div style="width:100%; height:auto; margin:0px;">
	<div style="width:100%;" align="center">
    	<img src="http://master.cmss-otcsc.com/competency_master/application/raise_salary/order_no/krut.jpg" alt=""  width="29mm" height="33mm"/>
   	</div>
    <div style="width:100%; height:0.4cm;"></div>
    <div style="float:left; width:100%;" align="center">คำสั่งโรงเรียนบ้านทับเดื่อ</div>
    <div style="width:100%; height:0.3cm;"></div>
    <div style="float:left; width:100%;" align="center"><span style="font-family: thsarabun">ที่ ๖๑/๒๕๕๘</span></div>
    <div style="width:100%; height:0.3cm;"></div>
    <div style="float:left; width:100%;" align="center">เรื่อง  คำสั่งเลื่อนขั้นเงินเดือนข้าราชการครูและบุคลากรการทางการศึกษาที่ไม่มีวิทยฐานะ</div>
    <div style="width:100%; height:0.3cm;"></div>
    <div style="float:left; width:100%;" align="center">--------------------------------------------------------------------------</div>
    <div style="width:100%; height:0.3cm;"></div>
    <div style="float:left; width:100%; text-align:justify; text-indent:2.5cm;">อาศัยอำนาจตามความในมาตรา ๕๓ มาตรา ๗๒ มาตรา ๗๓ แห่งพระราชบัญญัติระเบียบข้าราชการครู และบุคลากรทางการศึกษา พ.ศ.๒๕๔๗ และที่แก้ไขเพิ่มเติม กฎ ก.ค.ศ. ว่าด้วยการเลื่อนขั้นเงินเดือนข้าราชการครูและบุคลากรทางการศึกษา พ.ศ. ๒๕๕o ระเบียบกระทรวงการคลังว่าด้วยการเบิกจ่ายค่าตอบแทนพิเศษของข้าราชการและลูกจ้างประจำผู้ได้รับเงินเดือนหรือค่าจ้างถึงขั้นสูงหรือใกล้ถึงขั้นสูงของอันดับหรือตำแหน่ง พ.ศ.๒๕๕๐ (ฉบับที่ ๒) พ.ศ.๒๕๕๑ และโดยอนุมัติ อ.ก.ค.ศ. เขตพื้นที่การศึกษาประถมศึกษาเชียงใหม่ เขต ๒ ในคราวประชุม ครั้งที่ ๑๑/๒๕๕๘ เมื่อวันที่ ๙  ตุลาคม ๒๕๕๘ จึงให้เลื่อนขั้นเงินเดือนข้าราชการครูและบุคลากรทางการศึกษาและให้รับค่าตอบแทนพิเศษ ตามผลการประเมินประสิทธิภาพและประสิทธิผลการปฏิบัติงานครึ่งปีที่แล้วมา (๑ เมษายน ๒๕๕๘ - ๓o กันยายน ๒๕๕๘) จำนวน ๒ ราย ดังบัญชีรายละเอียดแนบท้ายคำสั่งนี้</div>
    <div style="clear:both;"></div>
    <div style="float:left; width:100%; text-align: justify; text-indent:2.5cm; margin-top:0.6cm;">ทั้งนี้ ตั้งแต่วันที่ ๑ ตุลาคม พ.ศ. ๒๕๕๘ เป็นต้นไป</div>
    <div style="clear:both;"></div>
    <div style="float:left; width:100%; text-indent:4cm; margin-top:0.6cm;">สั่ง&nbsp;ณ&nbsp;วันที่&nbsp;๙ เดือนตุลาคม พ.ศ.๒๕๕๘<br><br></div>
	<div style="clear:both;"></div>
    <div style="float:left; text-indent:8cm; margin-top:1.6cm;">
		( นางจตุพร  ปัญจรัง )<br>
    </div>
    <div style="float:left; width:100%; text-indent:7.25cm;">ผู้อำนวยการโรงเรียนบ้านทับเดื่อ</div><div style="float:left; width:100%; height:1.3cm;"></div></div>


</div>
</body>
</html>
