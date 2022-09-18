<?php
$path = '../common/';
$tableId = 'exSummary';

if(!($_POST['title'] || $_GET['title']) ) {
	$title = 'กรุณาใส่หัวข้อของหน้ารายงาน';
}
else {
	if($_POST['title']) {
		$title = $_POST['title'];
	}
	else {
		$title = $_GET['title'];
	}
}

if(!$_POST['effect']) {
	$effect = 'fadeIn';
}
else {
	$effect = $_POST['effect'];
}

$data = $_POST['data'];
$reportfile = $_POST['reportfile'];

if($_POST['dataType']) {
	$dataType = $_POST['dataType'];
}
else {
	$dataType = $_GET['dataType'];
}
if(!$dataType) {
	$dataType = false;
}
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $title; ?></title>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<link href="<?php echo $path; ?>font/stylesheet.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo $path; ?>css/reset.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo $path; ?>css/exsum.css?t=<?php echo date("Ymdhis"); ?>" rel="stylesheet" type="text/css" />
	<!--[if gte IE 9]>
	<style type="text/css">
		.gradient {
			filter: none;
		}
	</style>
	<![endif]-->
    <script src="<?php echo $path; ?>jquery.js"></script>

	<script src="<?php echo $path; ?>jquery.fullscreen.js" type="text/javascript"></script>
</head>
<body>

<script type="text/javascript">

	$(".fullscreen-supported").toggle($(document).fullScreen() != null);
	$(".fullscreen-not-supported").toggle($(document).fullScreen() == null);

	var rows = 0;
	var cols = 0;
	var el_table;





	$(document).ready(function() {

		$('.head_text').addClass('hidden');
		//var tmp;
		el_table = $('#<?php echo $tableId; ?>');

		$('#<?php echo $tableId; ?> td').children().map(function (index) {
			$(this).replaceWith($(this).contents());
		});

		$('#<?php echo $tableId; ?> td').removeAttr('class').removeAttr('width').removeAttr('bgcolor');

		rows = $('#<?php echo $tableId; ?> tr').length;
		cols = $('#<?php echo $tableId; ?> > tbody > tr')[0].cells.length;

		$('#<?php echo $tableId; ?> td').addClass('ptb10');


		/* Fix padding of td */
		switch(rows) {
			case 5: $('#<?php echo $tableId; ?> td').addClass('ptb26'); break;
			case 6: $('#<?php echo $tableId; ?> td').addClass('ptb20'); break;
			case 7: $('#<?php echo $tableId; ?> td').addClass('ptb17'); break;
			case 8: $('#<?php echo $tableId; ?> td').addClass('ptb15'); break;
			case 9: $('#<?php echo $tableId; ?> td').addClass('ptb13'); break;
			case 10: $('#<?php echo $tableId; ?> td').addClass('ptb11'); break;
			case 11: $('#<?php echo $tableId; ?> td').addClass('ptb8'); break;
			default: $('#<?php echo $tableId; ?> td').addClass('ptb15'); break;
		}

		/* Fix font size of content */
		switch(cols) {
			case 3: $('#<?php echo $tableId; ?> td').addClass('f42'); break;
			case 4: $('#<?php echo $tableId; ?> td').addClass('f40'); break;
			case 5: $('#<?php echo $tableId; ?> td').addClass('f38'); break;
			case 6: $('#<?php echo $tableId; ?> td').addClass('f36'); break;
			case 7: $('#<?php echo $tableId; ?> td').addClass('f34'); break;
			case 8: $('#<?php echo $tableId; ?> td').addClass('f32'); break;
			case 9: $('#<?php echo $tableId; ?> td').addClass('f30'); break;
			case 10: $('#<?php echo $tableId; ?> td').addClass('f28'); break;
			case 11: $('#<?php echo $tableId; ?> td').addClass('f26'); break;
			default: $('#<?php echo $tableId; ?> td').addClass('f30'); break;
		}



		/* Hide The Content */
		el_table.addClass('hidden');

		/* Show The Titel */
		$('.head_text').fadeIn(3000,function(){

			/* Show The Content */
			el_table.<?php echo $effect;?>(3000).removeClass('hidden');
		}).removeClass('hidden');

	});

</script>
<div id="header">
	<div class="head_text">
		<?php echo $title; ?>
	</div>
</div>
<div class="bgGradiant">
	<?php
		//echo "<table id=\"$tableId\">{$data}</table>";
	if($dataType == "file") {
		include("{$reportfile}.php");
	}
	else {
		echo "<table id=\"$tableId\">{$data}</table>";
	}
	?>
	<div class="clear"></div>
</div>
<div class="footer">
	<div class="foot_text"><button onclick="$(document).fadeIn(3000).toggleFullScreen();">Full Screen</button>| | Executive Summary Report</div>
</div>
<script type="text/javascript">
	//$(document).delay(5000).toggleFullScreen();
</script>
<p class="fullscreen-supported">

</p>
<p class="fullscreen-not-supported">

</p>
</body>
</html>