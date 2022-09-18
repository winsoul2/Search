<div id="period_table_space">

    <head>
        <style>
        .new_table {
            font-family: Tahoma;
            width: 100%;
            font-size: 16px;

        }

        img {
            display: none;
        }

        .align_1 {
            text-align: left;
            text-decoration: underline;
        }

        .title {
            text-align: left;
            font-size: 14px;
        }

        td {
            font-size: 14px;
        }

        .guaruntee {
            text-align: left;
            font-size: 14px;
            text-decoration: underline;
        }

        .printer {
            display: none;
        }

        @media print {

            .printer {
                display: 'block';
            }

            .display {
                display: none;
            }

            .new_table {
                font-family: Tahoma;
                width: 100%;
                font-size: 10;
                text-decoration: underline;
            }

            img {
                display: none;
            }

            .align_1 {
                text-align: left;
                text-decoration: underline;
            }

            .title {
                text-align: left;
                font-size: 10px;
            }

            .guaruntee {
                text-align: left;
                font-size: 10px;
                text-decoration: underline;
            }
			#table2 {
				border-collapse: collapse;
			}

			td {
				border-bottom: none;
				border-top: none;

			}

        }
        </style>
        <?php

        ?>
    </head>

    <body>
        <div class="printer" id="printer">
            <?php
            if (!empty($row['data'])) {
            ?>
            <?php
                foreach (@$row['data'] as $key => $row) {
                    ?>
            <?php
                    if ($key == 1) {
                ?>
            <table class="new_table"  >
                <tbody>
                    <tr>
                        <td class="title" style="width: 62%;" colspan=4><span style="font-size : 13px;">สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจรถไฟแห่งประเทศไทย จำกัด</span></td>
						<td class="title" style="width: 30%;" colspan=2><span style="font-size : 13px; text-align : left;"> 9.9ตารางอัตราการส่งชำระเงินกู้ใหม่</span></td>
                        <td class="title" style="width: 5%;"><span style="font-size : 13px; align :right;">หน้าที่ </span></td>
                        <td class="title" style="width: 3;"><span style="font-size : 13px;"><?php echo @$key;    ?></span> </td>
                    </tr>
                    <tr>
						<td class="title" style="width: 20%;" colspan=2><span style="font-size : 13px;">หนังสือสัญญาเงินกู้สามัญ เลขที่</span> </td>
                        <td class="align_1" style="width: 5%;" ><span style="text-decoration : underline; font-size : 13px;"><?php echo @$loan['contract_number']; ?></span></td>
                        <td class="text-center" style="width: 5%;"><span style="font-size : 13px;"> เลขที่สมาชิก</span> </td>
                        <td class="align_1" style="width: 5%;"><span style="text-decoration : underline; font-size : 13px;"><?php echo @$loan['member_id']; ?></span></td>
                        <td style="width: 65%;" colspan="3"><span style="font-size : 13px;">ชื่อผู้กู้ &nbsp; </span><span style="text-decoration : underline; font-size : 13px;"><?php echo @$mem['prename_full'].@$mem['firstname_th']. "&nbsp;". @$mem['lastname_th']; ?></span></td>
                    </tr>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center" style="width: 20%;"><span style="font-size : 13px;">&nbsp;&nbsp;&nbsp;จ่ายเงินกู้วันที่</span> </td>
                        <td class="align_1" style="width: 12%;"><span style="text-decoration : underline; font-size : 13px;">
                            <?php echo $this->center_function->mydate2date(@$loan['approve_date']); ?></span></td>
                        <td class="text-center"  style="width: 17%;"><span style="font-size : 13px;">ชำระเงินต่องวด</span> </td>
                        <td class="align_1" style="width: 13%;"><span style="text-decoration : underline; font-size : 13px;">
                            <?php echo number_format(@$loan['money_period_1'], 2);  //text-align : left; 
                                        ?></span></td>
                        <td class="title" style="width: 10%;"><span style="font-size : 13px;"> บาท</td>
                        <td class="text-center" style="width: 23%;"><span style="font-size : 13px;">จำนวนงวด &nbsp;</span>
							<span style="text-decoration : underline; font-size : 13px;"><?php echo @$loan['period_amount']; ?></span></td>
                        <td class="title" style="width: 5%;"><span style="font-size : 13px;"> งวด</span></td>
                    </tr>
                    <tr>
                        <td class="text-center" style="width: 20%;"><span style="font-size : 13px;">&nbsp;&nbsp;&nbsp;อัตราดอกเบี้ย</span></td>
                        <td class="align_1" style="width: 12%;"><span style="text-decoration : underline; font-size : 13px;"><?php echo @$loan['interest_per_year']; ?></span> % </td>
                        <td class="text-center" style="width: 17%;"><span style="font-size : 13px;">จำนวนเงินกู้ </span></td>
                        <td class="align_1" style="width: 13%;"><span style="text-decoration : underline; font-size : 13px;">
								<?php echo number_format(@$loan['loan_amount'], 2); ?></span>
                        </td>
                        <td class="title" style="width: 10%;"> <span style="font-size : 13px;">บาท</span></td>
                        <td class="text-center" style="width: 18%;"><span style="font-size : 13px;">งวดสุดท้าย &nbsp;</span>
							<span style="text-decoration : underline; font-size : 13px;">
                            <?php echo number_format(@$row[sizeof($row)-1]['principal_payment'], 2);
                                        "&nbps"; ?></span></td>
                        <td class="title" style="width: 10%;"> <span style="font-size : 13px;">บาท </span></td>
                    </tr>
                    <tr>

                        <td class="text-center" style="width:15%;"><span style="font-size : 13px;"> &nbsp;&nbsp;&nbsp;รายชื่อผู้ค้ำประกัน </span></td>
                        <td class="guaruntee" style="width:85%;" colspan=7><span style="text-decoration : underline; font-size : 13px;">
                            <?php echo implode(', ', array_column($guarantee, 'fullname')); ?></span></td>
                    </tr>
                </tbody>
            </table>
            <?php
                    } else { ?>
            <table class="new_table" >
                <tbody>
				<tr>
					<td class="title" style="width: 55%;" colspan=4><span style="font-size : 13px;">สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจรถไฟแห่งประเทศไทย จำกัด</span></td>
					<td class="title" style="width: 35%;" colspan=2><span style="font-size : 13px; text-align : left;"> 9.9ตารางอัตราการส่งชำระเงินกู้ใหม่</span></td>
					<td class="title" style="width: 5%;"><span style="font-size : 13px; text-align :right;">หน้าที่ </span></td>
					<td class="title" style="width: 5%;"><span style="font-size : 13px;"><?php echo @$key;    ?></span> </td>
				</tr>
				<tr>
					<td class="title" style="width: 30%;" colspan=2><span style="font-size : 13px;">หนังสือสัญญาเงินกู้สามัญ เลขที่</span> </td>
					<td class="align_1" style="width: 17%;" ><span style="text-decoration : underline; font-size : 13px;"><?php echo @$loan['contract_number']; ?></span></td>
					<td class="text-center" style="width: 14%;"><span style="font-size : 13px;"> เลขที่สมาชิก</span> </td>
					<td class="align_1" style="width: 13%;"><span style="text-decoration : underline; font-size : 13px;"><?php echo @$loan['member_id']; ?></span></td>
					<td style="width: 27%;" colspan="3"><span style="font-size : 13px;">ชื่อผู้กู้ &nbsp; </span><span style="text-decoration : underline; font-size : 13px;"><?php echo @$mem['prename_full'].@$mem['firstname_th']. "&nbsp;". @$mem['lastname_th']; ?></span></td>
				</tr>
                </tbody>
            </table>
            <?php  }
                    ?>

            <table id="table2" border="1" style="border-collapse: collapse; " >
                <thead >
                    <tr >
						<td class="text-center" style="width: 5%; text-align: center;"><span style="font-size : 13px; ">งวดที่</span></td>
						<td class="text-right" style="width: 9%;  text-align: center;"><span style="font-size : 13px;">วันที่</span></td>
						<td class="text-right" style="width: 15%; text-align: center;"><span style="font-size : 13px;">เงินต้นชำระ</span></td>
						<td class="text-right" style="width: 15%; text-align: center;"><span style="font-size : 13px;">ดอกเบี้ยชำระ</span></td>
						<td class="text-right" style="width: 15%; text-align: center;"><span style="font-size : 13px;">จำนวนเงินคงเหลือ</span></td>
						<td class="text-right" style="width: 15%; text-align: center;"><span style="font-size : 13px;">รวมดอกเบี้ยชำระ</span></td>
						<td class="text-right" style="width: 13%; text-align: center;"><span style="font-size : 13px;">รวมเงินต้นชำระ</span></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
							if(empty($key)){
                            $total_loan_int = 0;
                            $total_loan_pri = 0;
                            $total_loan_pay = 0;}
							else{$total_loan_pri=$total_loan_pri;
								$total_loan_int=$total_loan_int;}
                            if (!empty($row)) {
                                foreach (@$row as $key => $value) {    ?>
                    <tr>
						<td style="border-bottom: none; border-top: none; text-align: right; padding-right: 10px; "><span style="text-align : center; font-size : 12px; "><?php echo @$value['period_count'] ?></span></td>

						<td style="border-bottom: none; border-top: none; text-align: center; " class="text-right"><span style="font-size : 12px;"><?php echo $this->center_function->mydate2date(@$value['date_period']) ?></span>
                        </td>
						<td style="border-bottom: none; border-top: none; text-align: right; padding-right: 10px;" class="text-right"><span style="font-size : 12px;"><?php echo number_format(@$value['principal_payment'], 2) ?> </span></td>
						<td style="border-bottom: none; border-top: none; text-align: right; padding-right: 10px;" class="text-right"><span style="font-size : 12px;"><?php echo number_format(@$value['interest'], 2) ?></span></td>
						<td style="border-bottom: none; border-top: none; text-align: right; padding-right: 10px;" class="text-right"><span style="font-size : 12px;"><?php echo number_format(@$value['outstanding_balance'], 2) ?></span></td>
                        <?php $total_loan_int += @$value['interest'];
                                        $total_loan_pri += @$value['principal_payment'];  ?>
						<td style="border-bottom: none; border-top: none; text-align: right; padding-right: 10px;" ><span style="font-size : 12px; align :left;"><?php echo number_format(@$total_loan_int, 2) ?></span></td>
						<td style="border-bottom: none; border-top: none; text-align: right; padding-right: 10px;" ><span style="font-size : 12px; align :right;"><?php echo number_format(@$total_loan_pri, 2) ?></span></td>
                    </tr>
                    <?php        }
								$total_loan_pri=$total_loan_pri;
								$total_loan_int=$total_loan_int;
                            }
                            ?>
                </tbody>
            </table>
            <?php   }
            }
            ?>
        </div>

        <div class="display">
            <table class="new_table">
                <thead>
                    <tr>
                        <th class="title" style="width: 60%;" colspan=5>
                            สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจรถไฟแห่งประเทศไทย จำกัด</th>
                        <th class="title" style="width: 20%;" colspan=2> 9.9 ตารางอัตราการส่งชำระเงินกู้ใหม่</th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="title" style="width: 20%;" colspan=2>หนังสือสัญญาเงินกู้สามัญ เลขที่ </td>
                        <td class="align_1" style="width: 5%;"><?php echo @$loan['contract_number']; ?></td>
                        <td class="text-center" style="width: 5%;"> เลขที่สมาชิก </td>
                        <td class="align_1" style="width: 5%;"><?php echo @$loan['member_id']; ?></td>
                        <td style="width: 5%;">ชื่อผู้กู้ </td>
                        <td class="align_1" style="width: 60%;" colspan=2>
                            <?php echo @$mem['prename_full'];
                            echo @$mem['firstname_th'];
                            echo "&nbsp;";
                            echo @$mem['lastname_th']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center" style="width: 15%;">จ่ายเงินกู้วันที่ </td>
                        <td class="align_1" style="width: 10%;">
                            <?php echo $this->center_function->mydate2date(@$loan['approve_date']); ?></td>
                        <td class="text-center" style="width: 18%;">ชำระเงินต่องวด </td>
                        <td class="align_1" style="width: 12%;">
                            <?php echo number_format(@$loan['money_period_1'], 2);  //text-align : left; 
                            ?></td>
                        <td class="title" style="width: 10%;"> บาท</td>
                        <td class="text-center" style="width: 15%;">จำนวนงวด</td>
                        <td class="align_1" style="width: 5%"> <?php echo @$loan['period_amount']; ?></td>
                        <td class="title" style="width: 15%;"> งวด</td>
                    </tr>
                    <tr>
                        <td class="text-center" style="width: 15%;">อัตราดอกเบี้ย</td>
                        <td class="align_1" style="width: 10%;"> <?php echo @$loan['interest_per_year']; ?> % </td>
                        <td class="text-center" style="width: 8%;">จำนวนเงินกู้ </td>
                        <td class="align_1" style="width: 15%;"><?php echo number_format(@$loan['loan_amount'], 2); ?>
                        </td>
                        <td class="title" style="width: 5%;"> บาท</td>
                        <td class="text-center" style="width: 14%;">งวดสุดท้าย </td>
                        <td class="align_1" style="width: 10%;">
                            <?php echo number_format(@$rs[sizeof($rs)-1]['principal_payment'], 2);
                            "&nbps"; ?></td>
                        <td class="title" style="width: 23%;"> บาท </td>
                    </tr>
                    <tr>

                        <td class="text-center" style="width:20%;"> รายชื่อผู้ค้ำประกัน </td>
                        <td class="guaruntee" style="width:80%;" colspan=8>
                            <?php echo implode(', ', array_column($guarantee, 'fullname')); ?></td>
                    </tr>
            </table>
            <td style="width:100px;vertical-align: top;"></td>
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">งวดที่</th>
                        <th class="text-right" style="width: 9%;">วันที่</th>
                        <th class="text-right" style="width: 15%;">เงินต้นชำระ</th>
                        <th class="text-right" style="width: 15%;">ดอกเบี้ยชำระ</th>
                        <th class="text-right" style="width: 15%;">จำนวนเงินคงเหลือ</th>
                        <th class="text-right" style="width: 15%;">รวมดอกเบี้ยชำระ</th>
                        <th class="text-right" style="width: 13%;">รวมเงินต้นชำระ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_loan_int = 0;
                    $total_loan_pri = 0;
                    $total_loan_pay = 0;
                    if (!empty($rs)) {
                        foreach (@$rs as $key => $row) {    ?>
                    <tr>
                        <td class="text-center"><?php echo @$row['period_count'] ?></td>
                        <td class="text-right"><?php echo $this->center_function->mydate2date(@$row['date_period']) ?>
                        </td>
                        <td class="text-right"><?php echo number_format(@$row['principal_payment'], 2) ?> </td>
                        <td class="text-right"><?php echo number_format(@$row['interest'], 2) ?></td>
                        <td class="text-right"><?php echo number_format(@$row['outstanding_balance'], 2) ?></td>
                        <?php $total_loan_int += @$row['interest'];
                                $total_loan_pri += @$row['principal_payment'];  ?>
                        <td class="text-right"><?php echo number_format(@$total_loan_int, 2) ?></td>
                        <td class="text-right"><?php echo number_format(@$total_loan_pri, 2) ?></td>
                    </tr>
                    <?php        }
                    }
                    ?>
                </tbody>
            </table>

            <div class="text-center p-v-xxl hidden-print">
                <button type="button" class="btn btn-primary btn-calculate"
                    onclick="printElem('printer');">พิมพ์</button>
            </div>
        </div>
