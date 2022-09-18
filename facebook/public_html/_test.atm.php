<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php
set_time_limit(600);
/*
	define("HOSTNAME","localhost") ;
	define("DBNAME","coop_spktcsys_2");
	define("USERNAME","root");
	define("PASSWORD",'');
	*/
	define("HOSTNAME","103.233.192.68") ;
	define("DBNAME","coop_spktcsys");
	define("USERNAME","admin_spktcsys");
	define("PASSWORD",'Qzij45#0');

	$mysqli = new mysqli( HOSTNAME , USERNAME , PASSWORD );
	$mysqli->select_db(DBNAME);
	$mysqli->query("SET NAMES utf8");
	$month = 10;
	$p_month = 41;
	echo '<table border="1" cellspacing="0" cellpadding="3">
			<tbody>';
	$sql = " SELECT tb1.*, DAYOFYEAR(DATE_FORMAT(NOW(), '%Y-12-31')) num_day_of_year
			, DATE(tb1.approve_date) date_begin
			, LAST_DAY(tb1.approve_date) date_end
			, DATEDIFF(LAST_DAY(tb1.approve_date), DATE(tb1.approve_date)) num_datediff
			, CONCAT(tb2.firstname_th, ' ', tb2.lastname_th) member_name
			, tb3.account_id
            FROM coop_loan tb1
			INNER JOIN coop_mem_apply tb2 ON tb1.member_id = tb2.member_id
			INNER JOIN ( SELECT * FROM coop_maco_account WHERE type_id = '2' AND account_status = '0' ) tb3 ON tb1.member_id = tb3.mem_id
            WHERE tb1.approve_date >= '2018-10-01 00:00:00'
				AND tb1.loan_status = 1
			ORDER BY tb1.member_id 			";
	$sql = "SELECT
			 t2.*
			 , DAYOFYEAR(DATE_FORMAT(NOW(), '%Y-12-31')) num_day_of_year
			 , DATEDIFF(LAST_DAY(t2.payment_date), DATE(t2.payment_date)) num_datediff
			 , CONCAT(tb2.firstname_th, ' ', tb2.lastname_th) member_name
			, tb3.account_id
			 , tb5.contract_number
				FROM coop_receipt AS t1
				INNER JOIN coop_finance_transaction AS t2 ON t1.receipt_id = t2.receipt_id
				INNER JOIN coop_mem_apply tb2 ON t1.member_id = tb2.member_id
				INNER JOIN ( SELECT * FROM coop_maco_account WHERE type_id = '2' AND account_status = '0' ) tb3 ON t1.member_id = tb3.mem_id
				INNER JOIN coop_loan tb5 ON t2.loan_id = tb5.id
				WHERE month_receipt IS NOT NULL
					AND t2.principal_payment > 0
					AND (YEAR(payment_date) = 2018 AND MONTH(payment_date) = 10)
					AND DATEDIFF(LAST_DAY(t2.payment_date), DATE(t2.payment_date)) > 0
				ORDER BY member_id";
	$sql = "SELECT
 t2.*
 , DAYOFYEAR(DATE_FORMAT(NOW(), '%Y-12-31')) num_day_of_year
 , ABS(DATEDIFF(DATE_ADD(DATE_FORMAT(t2.payment_date, '%Y-%m-01'), INTERVAL -1 DAY), DATE(t2.payment_date))) num_datediff
 , CONCAT(tb2.firstname_th, ' ', tb2.lastname_th) member_name
, tb3.account_id
 , tb5.contract_number
FROM coop_receipt AS t1
INNER JOIN coop_finance_transaction AS t2 ON t1.receipt_id = t2.receipt_id
INNER JOIN coop_mem_apply tb2 ON t1.member_id = tb2.member_id
INNER JOIN ( SELECT * FROM coop_maco_account WHERE type_id = '2' AND account_status = '0' ) tb3 ON t1.member_id = tb3.mem_id
INNER JOIN coop_loan_atm tb5 ON t2.loan_atm_id = tb5.loan_atm_id
WHERE month_receipt IS NULL
AND (YEAR(payment_date) = 2018 AND MONTH(payment_date) = {$month})
AND t2.principal_payment > 0
AND t2.loan_amount_balance = 0
ORDER BY member_id";
	$rs_loan = $mysqli->query($sql);
	while(( $row_loan = $rs_loan->fetch_assoc() )) {


			$return = 0;
			$interest = ROUND( ($row_loan['principal_payment']) * ( 6 / 100 ) * ( $row_loan['num_datediff'] / $row_loan['num_day_of_year'] ) );
			$diff = $row_loan['interest'] - $interest;

			$sql = "SELECT
					 t2.*
					 , DAYOFYEAR(DATE_FORMAT(NOW(), '%Y-12-31')) num_day_of_year
					 , ABS(DATEDIFF(DATE_ADD(DATE_FORMAT(t2.payment_date, '%Y-%m-01'), INTERVAL -1 DAY), DATE(t2.payment_date))) num_datediff
					 , CONCAT(tb2.firstname_th, ' ', tb2.lastname_th) member_name
					, tb3.account_id
					 , tb5.contract_number
					FROM coop_receipt AS t1
					INNER JOIN coop_finance_transaction AS t2 ON t1.receipt_id = t2.receipt_id
					INNER JOIN coop_mem_apply tb2 ON t1.member_id = tb2.member_id
					INNER JOIN ( SELECT * FROM coop_maco_account WHERE type_id = '2' AND account_status = '0' ) tb3 ON t1.member_id = tb3.mem_id
					INNER JOIN coop_loan_atm tb5 ON t2.loan_atm_id = tb5.loan_atm_id
					WHERE month_receipt IS NOT NULL
					AND (YEAR(payment_date) = 2018 AND MONTH(payment_date) = {$month})
					AND t2.member_id = '{$row_loan['member_id']}'
					ORDER BY member_id";

			$rs_finance = $mysqli->query($sql);

			while(( $row_finance = $rs_finance->fetch_assoc() )) {

				$return += $row_finance['principal_payment'] + $row_finance['interest'];
			}
			echo "
			<tr>
				<td>{$row_loan['account_id']}</td>
				<td>{$row_loan['member_id']}</td>
				<td>{$row_loan['member_name']}</td>
				<td>{$row_loan['contract_number']}</td>
				<td>{$row_loan['principal_payment']}</td>
				<td>{$row_loan['interest']}</td>
				<td>{$return}</td>
			</tr>
			<div style='display: none;'>
				{$row_loan['member_id']} - {$row_loan['date_begin']} - {$row_loan['date_end']} - {$row_loan['num_datediff']} - {$row_before['loan_id']} - {$row_before['contract_number']} - {$row_before['loan_amount_balance']} - {$row_principal['pay_amount']} := {$interest}
			</div>";
			}
	echo '
		</tbody>
	</table>
	';