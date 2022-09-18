<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title></title>
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
  <meta name="theme-color" content="#ffffff">
<?php
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
 
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/custom.css',
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
?>
		<style tyle="text/css">
			body {  
				  background-color: #CCCCCC;
				  font-size: 12px;
			}
			
			table{font-size: 12px;}
			
			@media print {	
				@font-face {
					font-family: 'upbean';
					src: url('/assets/css/fonts/upbean/UpBean Regular Ver 1.00.ttf');
				}
				h3 {
					font-family: upbean;
				}
				.table>thead>tr>th{
					font-family: upbean;
					font-size: 18px;
					padding: 6px;
				}
			}
			
			/*border-bottom: 1px dotted #75758a;*/
		</style>
		<center>
		
		<div style="width: 1000px;">
			<div class="panel panel-body" style="padding-top:0px !important;min-height: 940px;">
				 <h3><?php echo @$cremation_type_name;?> </h3>
				 <div style="text-align: left;display: -webkit-inline-box;">
					<h3>รหัสสมาชิก </h3>
					<h3 style="border-bottom: 1px dotted #75758a;width: 150px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo @$member_id;?></h3>
					<h3>ชื่อสกุล </h3>
					<h3 style="border-bottom: 1px dotted #75758a;width: 680px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo @$member_full_name;?></h3> 						
				 </div>
				 <table class="table table-bordered table-striped table-center">
				 <thead> 
					<tr class="bg-primary">
						<th>ลำดับ</th>
						<th>วันที่ชำระ</th>
						<th>จำนวนเงิน</th>
						<th>หมายเหตุ</th> 
					</tr> 
				 </thead>
				 <tbody id="table_first">
				  <?php 
					$i =1;
					foreach($data as $key => $row ){ 
					?>
					  <tr> 
						  <td><?php echo $i++;?></td>
						  <td><?php echo @$this->center_function->ConvertToThaiDate(@$row['receipt_datetime']); ?></td>
						  <td style="text-align: right;"><?php echo number_format(@$row['total_amount'],2); ?></td> 
						  <td style="text-align: left;"><?php echo @$row['account_list']; ?></td> 							 
					  </tr>
				  <?php } ?>
				  </tbody> 
				  </table> 
			</div>
		</div>
		</center>
    </body>
</html>