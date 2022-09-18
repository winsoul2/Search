<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Interests extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('html', 'url'));
    }

    public function averageReturn($date_start, $date_end, $multiplier = 100, $member_id = false){

        if(empty($date_start) || empty($date_end) || empty($multiplier)){
            return [];
        }
        $interest = $this->interestCalculation($date_start, $date_end, $member_id);

        $multiplier /= 100;

        if($member_id){

            $res['average_return'] = $interest->return_amount * $multiplier;
            $res['interest'] = $interest->return_amount;
            $res['multiplier'] = $multiplier;

            return (object) $res;

        }else{

            $res = [];

            foreach ($interest as $key => $value){
                $res[$value['member_id']]['average_return'] = $value['return_amount'] * $multiplier;
                $res[$value['member_id']]['interest'] = $value['return_amount'];
                $res[$value['member_id']]['multiplier'] = $multiplier;
            }

            return $res;

        }

    }

    private function interestCalculation($date_start, $date_end, $member_id = false){

        $cond = "";

        if(empty($date_start) || empty($date_end)){
            return [];
        }

        if($member_id){
            $cond = " WHERE member_id = '{$member_id}' ";
        }

        $stmt  = "SELECT member_id,SUM(return_amount) AS return_amount FROM (SELECT member_id,interest AS `return_amount`,payment_date AS `date` FROM coop_finance_transaction WHERE payment_date BETWEEN '{$date_start}' AND '{$date_end}' AND account_list_id IN ('15','31') AND interest<> 0 UNION ALL 
SELECT member_id,(return_principal-return_amount) AS return_amount,return_time AS `date` FROM coop_process_return WHERE return_time BETWEEN '{$date_start}' AND '{$date_end}') T {$cond} GROUP BY member_id";
        $query = $this->db->query($stmt);
        if($member_id){
            return $query->row();
        }else{
            return $query->result_array();
        }
    }

}