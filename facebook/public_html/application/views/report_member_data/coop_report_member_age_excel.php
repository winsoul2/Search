<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานอายุสมาชิก.xls");
date_default_timezone_set('Asia/Bangkok');
?>
<pre>
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<style>
                .table_header_top {
                    font-family: AngsanaUPC, MS Sans Serif;
                    font-size: 19px;
                    font-weight: bold;
                    text-align: center;
                    border-top: thin solid black;
                    border-left: thin solid black;
                    border-right: thin solid black;
                }

                .table_body {
                    font-family: AngsanaUPC, MS Sans Serif;
                    font-size: 21px;
                    border: thin solid black;
                }
			</style>
    		</head>
		<body>
				<table class="table table-bordered">
					<thead>
						<tr>
                            <th class="table_header_top">ลำดับ</th>
                            <th class="table_header_top">รหัสสมาชิก</th>
                            <th class="table_header_top">ชื่อสกุล</th>
                            <th class="table_header_top">วันเดือนปีเกิด</th>
                            <th class="table_header_top">อายุ</th>
                            <th class="table_header_top">วันเข้าเป็นสมาชิก</th>
                            <th class="table_header_top">อายุการเป็นสมาชิก</th>
						</tr>
					</thead>
					<tbody>
                                <tbody>
            <?php
            $count = 0;
            foreach ($datas as $data){
                $count++;
                $diff = date_diff(date_create($data['member_date'] ),date_create(date('Y-m-d')));
                $day = floor($diff->format("%a")%365);
                ?>
                <tr>
                    <td class="table_body"> <?php echo $count ?> </td>
                    <td class="table_body"> <?php echo $data['member_id']?> </td>
                    <td class="table_body" style="text-align: left"> <?php echo $data['prename_full'].$data['firstname_th']." ".$data['lastname_th']?> </td>
                    <td class="table_body"> <?php echo $this->center_function->ConvertToThaiDate($data['birthday'])?> </td>
                    <td class="table_body"> <?php echo $this->center_function->diff_year($data['birthday'],date('Y-m-d'))." ปี"?> </td>
                    <td class="table_body"> <?php echo $this->center_function->ConvertToThaiDate($data['member_date'])?> </td>
                    <td class="table_body"> <?php echo $this->center_function->diff_year($data['member_date'],date('Y-m-d'))." ปี ". $day . " วัน"?></td>
                </tr>
            <? }?>

            </tbody>
					</tbody>
				</table>
		</body>
	</html>
</pre>