<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $title;?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
  <meta name="theme-color" content="#ffffff">
      <?php
      $link = array(
          'href' => PROJECTJSPATH.'assets/images/logo/favicon.png',
          'rel' => 'shortcut icon'
      );
      echo link_tag($link);
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/vendor.min.css',
          'rel' => 'stylesheet',
          'type' => 'text/css'
      );
      echo link_tag($link);
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/elephant.min.css',
          'rel' => 'stylesheet',
          'type' => 'text/css'
      );
      echo link_tag($link);
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/application.min.css',
          'rel' => 'stylesheet',
          'type' => 'text/css'
      );
      echo link_tag($link);
      //fancybox
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/fancybox/jquery.fancybox.css?v=2.1.5',
          'rel' => 'stylesheet',
          'type' => 'text/css',
          'media'=>'screen'
      );
      echo link_tag($link);
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5',
          'rel' => 'stylesheet',
          'type' => 'text/css'
      );
      echo link_tag($link);
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7',
          'rel' => 'stylesheet',
          'type' => 'text/css'
      );
      echo link_tag($link);
      //fancybox
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/custom.css',
          'rel' => 'stylesheet',
          'type' => 'text/css',
          'media'=>'screen'
      );
      echo link_tag($link);
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/sweetalert.css',
          'rel' => 'stylesheet',
          'type' => 'text/css',
          'media'=>'screen'
      );
      echo link_tag($link);
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/bootstrap-datetimepicker.css',
          'rel' => 'stylesheet',
          'type' => 'text/css',
          'media'=>'screen'
      );
      echo link_tag($link);
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/jquery-ui-timepicker-addon.css',
          'rel' => 'stylesheet',
          'type' => 'text/css',
          'media'=>'screen'
      );
      echo link_tag($link);
      $link = array(
          'href' => PROJECTJSPATH.'assets/jquery-ui/jquery-ui.css',
          'rel' => 'stylesheet',
          'type' => 'text/css',
          'media'=>'screen'
      );
      echo link_tag($link);
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/custom-grid24.css',
          'rel' => 'stylesheet',
          'type' => 'text/css',
          'media'=>'screen'
      );
      echo link_tag($link);
	  $link = array(
          'href' => PROJECTJSPATH.'assets/css/font-awesome/css/font-awesome.min.css',
          'rel' => 'stylesheet',
          'type' => 'text/css',
          'media'=>'screen'
      );
      echo link_tag($link);
	  $link = array(
          'href' => PROJECTJSPATH.'assets/css/fonts/upbean/upbean.css',
          'rel' => 'stylesheet',
          'type' => 'text/css',
          'media'=>'screen'
      );
      echo link_tag($link);
	  $link = array(
          'href' => PROJECTJSPATH.'assets/css/fonts/thsarabunnew/thsarabunnew.css',
          'rel' => 'stylesheet',
          'type' => 'text/css',
          'media'=>'screen'
      );
      echo link_tag($link);

      /*$link = array(
          'href' => PROJECTJSPATH.'assets/css/dataTables.bootstrap.min.css',
          'rel' => 'stylesheet',
          'type' => 'text/css'
      );
      echo link_tag($link);
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/jquery.dataTables.min.js',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/dataTables.bootstrap.min.js',
          'type' => 'text/javascript'
      );
      echo script_tag($link);*/
      
      
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/vendor.min.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
	  
      $link = array(
          'src' => PROJECTJSPATH.'assets/js/reset_time_out.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
      ?>
    </head>
	<style tyle="text/css">
	body {  
		  background-color: #CCCCCC;
		  font-size: 12px;
	}
	
	table{font-size: 12px;}
	.h3, h3 {
		margin-top: 5px;
		margin-bottom: 5px;
	}
	span{font-family: upbean;font-size: 14px;}
	@media print {	
		@font-face {
			font-family: 'upbean';
			src: url('/assets/css/fonts/upbean/UpBean Regular Ver 1.00.ttf');
		}
		@font-face {
			font-family: 'THSarabunNew';
			src: url('/assets/css/fonts/thsarabunnew/thsarabunnew-webfont.eot');
			src: url('/assets/css/fonts/thsarabunnew/thsarabunnew-webfont.eot?#iefix') format('embedded-opentype'),
				 url('/assets/css/fonts/thsarabunnew/thsarabunnew-webfont.woff') format('woff'),
				 url('/assets/css/fonts/thsarabunnew/thsarabunnew-webfont.ttf') format('truetype');
			font-weight: normal;
			font-style: normal;
		}
		h1,h2,h3,h4,h5,h6{
			font-family: 'upbean';
		}
		.table>thead>tr>th{
			font-family: upbean;
			font-size: 18px;
			padding: 6px;
		}
		.no_print{display: none;}
		.table-view>tbody>tr>td, 
		.table-view>tbody>tr>th, 
		.table-view>tfoot>tr>td, 
		.table-view>tfoot>tr>th, 
		.table-view>thead,
		.table-view>thead>tr>td, 
		.table-view>thead>tr>th {
			border: 1px solid #000;
		}
		
		.table-view>tfoot>tr>td{
			background-color: #fff;
			border: 0px;
		} 
				
		.m-f-1{
			margin-left: 1em;
		}
		
		.m-t-2{
			margin-top: 2em;
		}
		
		.page-break { page-break-before: always; }
		span{font-family: upbean;font-size: 14px;}
		
		@page { margin: 0px 0px 0px 0px;}
		
	}
</style>		
    <body>
    <div id="base_url" class="<?php echo base_url().PROJECTPATH; ?>"></div>
	<center>