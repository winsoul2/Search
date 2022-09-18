<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_cremation extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    //conditions must be array : array() for null
    public function get_members($cremation_id, $conditions) {
        $result = array();

        $where = "t1.status IS NOT NULL";
        if(!empty($conditions["status"])) {
            $where .= " AND t1.status IN (".implode(',',$conditions["status"]).")";
        }

        $cremations = $this->db->select("t1.id as cremation_member_id,
                                            t1.status,
                                            t1.member_id,
                                            t1.cremation_member_id as cremation_no,
                                            t1.member_date,
                                            t3.name as period_name,
                                            t4.firstname_th,
                                            t4.lastname_th,
                                            t5.prename_full
                                        ")
                                ->from("coop_sp_cremation_member as t1")
                                ->join("coop_sp_cremation_registration as t2", "t1.id = t2.cremation_member_id", "INNER")
                                ->join("coop_sp_cremation_registration_period as t3", "t2.period_id = t3.id", "LEFT")
                                ->join("coop_mem_apply as t4", 't1.member_id = t4.member_id', "INNER")
                                ->join("coop_prename as t5", "t4.prename_id = t5.prename_id", "LEFT")
                                ->where($where)
                                ->order_by("t1.member_date, t1.id")
                                ->get()->result_array();

        if(!empty($cremations)) {
            $result["datas"] = $cremations;
            $result["status"] = "success";
        } else {
            $result["status"] = "no_mem";
        }
        return $result;
    }

    //conditions must be array : array() for null
    public function get_registers($cremation_id, $conditions) {
        $result = array();

        $where = "t1.status IS NOT NULL";
        if(!empty($conditions)) {
            if(!empty($conditions["status"])) {
                $where .= " AND t1.status IN (".implode(',',$conditions["status"]).")";
            }
            if(!empty($conditions["register_from_date"])) {
                $where .= " AND t1.request_date >= '".$this->center_function->ConvertToSQLDate($_POST['register_from_date'])."'";
            }
            if(!empty($conditions["register_thru_date"])) {
                $where .= " AND t1.request_date <= '".$this->center_function->ConvertToSQLDate($_POST['register_thru_date'])."'";
            }
            if(!empty($conditions["payment_from_date"])) {
                $where .= " AND t1.payment_date >= '".$this->center_function->ConvertToSQLDate($_POST['payment_from_date'])."'";
            }
            if(!empty($conditions["payment_thru_date"])) {
                $where .= " AND t1.payment_date <= '".$this->center_function->ConvertToSQLDate($_POST['payment_thru_date'])."'";
            }
            if(!empty($conditions["approve_from_date"])) {
                $where .= " AND t1.approve_date >= '".$this->center_function->ConvertToSQLDate($_POST['approve_from_date'])."'";
            }
            if(!empty($conditions["approve_thru_date"])) {
                $where .= " AND t1.approve_date <= '".$this->center_function->ConvertToSQLDate($_POST['approve_thru_date'])."'";
            }
        }

        $cremations = $this->db->select("t1.status,
                                            t1.receipt_id,
                                            t1.request_date,
                                            t2.id as cremation_member_id,
                                            t2.member_id,
                                            t2.cremation_member_id as cremation_no,
                                            t3.name as period_name,
                                            t4.firstname_th,
                                            t4.lastname_th,
                                            t5.prename_full
                                        ")
                                ->from("coop_sp_cremation_registration as t1")
                                ->join("coop_sp_cremation_member as t2", "t2.id = t1.cremation_member_id", "INNER")
                                ->join("coop_sp_cremation_registration_period as t3", "t1.period_id = t3.id", "LEFT")
                                ->join("coop_mem_apply as t4", 't2.member_id = t4.member_id', "INNER")
                                ->join("coop_prename as t5", "t4.prename_id = t5.prename_id", "LEFT")
                                ->where($where)
                                ->get()->result_array();

        if(!empty($cremations)) {
            $result["datas"] = $cremations;
            $result["status"] = "success";
        } else {
            $result["status"] = "no_mem";
        }
        return $result;
    }

    //conditions must be array : array() for null
    public function get_resigns($cremation_id, $conditions) {
        $result = array();

        $where = "t1.status IS NOT NULL";
        if(!empty($conditions)) {
            if(!empty($conditions["status"])) {
                $where .= " AND t1.status IN (".implode(',',$conditions["status"]).")";
            }
            if(!empty($conditions["register_from_date"])) {
                $where .= " AND t1.request_date >= '".$this->center_function->ConvertToSQLDate($_POST['register_from_date'])."'";
            }
            if(!empty($conditions["register_thru_date"])) {
                $where .= " AND t1.request_date <= '".$this->center_function->ConvertToSQLDate($_POST['register_thru_date'])."'";
            }
            if(!empty($conditions["approve_from_date"])) {
                $where .= " AND t1.approve_date >= '".$this->center_function->ConvertToSQLDate($_POST['approve_from_date'])."'";
            }
            if(!empty($conditions["approve_thru_date"])) {
                $where .= " AND t1.approve_date <= '".$this->center_function->ConvertToSQLDate($_POST['approve_thru_date'])."'";
            }
        }

        $cremations = $this->db->select("t1.status,
                                            t1.receipt_id,
                                            t1.request_date,
                                            t1.reason,
                                            t2.id as cremation_member_id,
                                            t2.member_id,
                                            t2.cremation_member_id as cremation_no,
                                            t4.firstname_th,
                                            t4.lastname_th,
                                            t5.prename_full
                                        ")
                                ->from("coop_sp_cremation_resign as t1")
                                ->join("coop_sp_cremation_member as t2", "t2.id = t1.cremation_member_id", "INNER")
                                ->join("coop_mem_apply as t4", 't2.member_id = t4.member_id', "INNER")
                                ->join("coop_prename as t5", "t4.prename_id = t5.prename_id", "LEFT")
                                ->where($where)
                                ->order_by("t1.request_date, t1.id")
                                ->get()->result_array();

        if(!empty($cremations)) {
            $result["datas"] = $cremations;
            $result["status"] = "success";
        } else {
            $result["status"] = "no_mem";
        }
        return $result;
    }

    //conditions must be array : array() for null
    public function get_request_moneys($cremation_id, $conditions) {
        $result = array();

        $where = "t1.status IS NOT NULL";
        if(!empty($conditions)) {
            if(!empty($conditions["status"])) {
                $where .= " AND t1.status IN (".implode(',',$conditions["status"]).")";
            }
            if(!empty($conditions["register_from_date"])) {
                $where .= " AND t1.request_date >= '".$this->center_function->ConvertToSQLDate($_POST['register_from_date'])."'";
            }
            if(!empty($conditions["register_thru_date"])) {
                $where .= " AND t1.request_date <= '".$this->center_function->ConvertToSQLDate($_POST['register_thru_date'])."'";
            }
            if(!empty($conditions["approve_from_date"])) {
                $where .= " AND t1.approve_date >= '".$this->center_function->ConvertToSQLDate($_POST['approve_from_date'])."'";
            }
            if(!empty($conditions["approve_thru_date"])) {
                $where .= " AND t1.approve_date <= '".$this->center_function->ConvertToSQLDate($_POST['approve_thru_date'])."'";
            }
        }

        $cremations = $this->db->select("t1.status,
                                            t1.receipt_id,
                                            t1.request_date,
                                            t1.reason,
                                            t2.id as cremation_member_id,
                                            t2.member_id,
                                            t2.cremation_member_id as cremation_no,
                                            t4.firstname_th,
                                            t4.lastname_th,
                                            t5.prename_full
                                        ")
                                ->from("coop_sp_cremation_request_receive as t1")
                                ->join("coop_sp_cremation_member as t2", "t2.id = t1.cremation_member_id", "INNER")
                                ->join("coop_mem_apply as t4", 't2.member_id = t4.member_id', "INNER")
                                ->join("coop_prename as t5", "t4.prename_id = t5.prename_id", "LEFT")
                                ->where($where)
                                ->order_by("t1.request_date, t1.id")
                                ->get()->result_array();

        if(!empty($cremations)) {
            $result["datas"] = $cremations;
            $result["status"] = "success";
        } else {
            $result["status"] = "no_mem";
        }
        return $result;
    }

    //conditions must be array : array() for null
    public function get_registration_periods($cremation_id, $conditions) {
        $result = array();

        $where = "t1.status = 1";
        if(!empty($conditions)) {
            if(!empty($conditions["start_date"])) {
                $where .= " AND t1.end_date >= '".$this->center_function->ConvertToSQLDate($conditions['start_date'])."'";
            }
            if(!empty($conditions["end_date"])) {
                $where .= " AND t1.start_date <= '".$this->center_function->ConvertToSQLDate($conditions['end_date'])."'";
            }
        }

        $periods = $this->db->select("*")
                            ->from("coop_sp_cremation_registration_period as t1")
                            ->where($where)
                            ->order_by("t1.start_date, t1.end_date, t1.id")
                            ->get()->result_array();

        if(!empty($periods)) {
            $result["datas"] = $periods;
            $result["status"] = "success";
        } else {
            $result["status"] = "no_mem";
        }
        return $result;
    }

    public function check_fee_charge_report($cremation_id, $conditions) {
        $result = array();

        $where = "t1.status != 3";
        if(!empty($conditions["year"])) $where .= " AND t1.year = '".$conditions["year"]."'";
        if(!empty($conditions["period_id"])) $where .= " AND t2.period_id = '".$conditions["period_id"]."'";
        $data = $this->db->select("t1.id")
                            ->from("coop_sp_cremation_debt as t1")
                            ->join("coop_sp_cremation_fee as t2", "t1.fee_id = t2.id", "inner")
                            ->where($where)
                            ->get()->row();
        if(!empty($data)) {
            $result["status"] = "success";
        } else {
            $result["status"] = "no_data";
        }

        return $result;
    }
}
