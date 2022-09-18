<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php	
echo (int)substr('R25611000253', 7, 5);
exit();
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
	$rs_loan = $mysqli->query($sql);
	while(( $row_loan = $rs_loan->fetch_assoc() )) {
		
		$sql = "SELECT tb1.contract_number, tb2.*
				FROM coop_loan tb1
				INNER JOIN coop_loan_transaction tb2 ON tb1.id = tb2.loan_id
				WHERE tb1.member_id = '{$row_loan['member_id']}'
				AND (tb1.approve_date < '2018-10-01 00:00:00' OR tb1.approve_date IS NULL)
				AND tb2.transaction_datetime < '{$row_loan['approve_date']}'
				ORDER BY tb2.transaction_datetime DESC
				LIMIT 1";
		$rs_before = $mysqli->query($sql);
		$row_before = $rs_before->fetch_assoc();
		if( $row_before['loan_amount_balance'] > 0 ) {
			
			$sql = "SELECT pay_amount 
					FROM coop_finance_month_detail 
					WHERE loan_id = '{$row_before['loan_id']}' 
					AND profile_id = '{$p_month}' 
					AND pay_type = 'principal'";
			$rs_principal = $mysqli->query($sql);
			$row_principal = $rs_principal->fetch_assoc();
			$interest = ROUND( ($row_before['loan_amount_balance'] - $row_principal['pay_amount']) * ( 6 / 100 ) * ( $row_loan['num_datediff'] / $row_loan['num_day_of_year'] ) );
			
			$sql = "SELECT
					 t2.*
					FROM
					 coop_receipt AS t1
					INNER JOIN coop_finance_transaction AS t2 ON t1.receipt_id = t2.receipt_id
					WHERE month_receipt IS NULL
					AND (YEAR(payment_date) = 2018 AND MONTH(payment_date) = 10)
					AND t1.member_id = '011437'
					ORDER BY member_id";
			
			$sql = "SELECT 
						SUM(CASE WHEN pay_type = 'principal' THEN real_pay_amount ELSE 0 END) principal,
						SUM(CASE WHEN pay_type = 'interest' THEN real_pay_amount ELSE 0 END) interest
					FROM coop_finance_month_detail 
					WHERE loan_id = '{$row_before['loan_id']}' 
					AND profile_id = '41'
					GROUP BY loan_id";
			$rs_finance = $mysqli->query($sql);
			$row_finance = $rs_finance->fetch_assoc();
			echo "
			<tr>
				<td>{$row_loan['account_id']}</td>
				<td>{$row_loan['member_id']}</td>
				<td>{$row_loan['member_name']}</td>
				<td>{$row_before['contract_number']}</td>
				<td>{$row_finance['principal']}</td>
				<td>{$row_finance['interest']}</td>
				<td>{$interest}</td>
				<td>{$row_before['loan_amount_balance']}</td>
				<td>{$row_principal['pay_amount']}</td>
				<td>{$row_before['loan_id']}</td>
			</tr>
			<div style='display: none;'>
				{$row_loan['member_id']} - {$row_loan['date_begin']} - {$row_loan['date_end']} - {$row_loan['num_datediff']} - {$row_before['loan_id']} - {$row_before['contract_number']} - {$row_before['loan_amount_balance']} - {$row_principal['pay_amount']} := {$interest}
			</div>";
		}
	}
	echo '
		</tbody>
	</table>
	';