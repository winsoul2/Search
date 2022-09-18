<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fscct_account extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->cremation_group_id = 2;
        $this->account_group_id = 2;
        $this->path = "fscct_account";
        $this->month_arr = array('01'=>'มกราคม','02'=>'กุมภาพันธ์','03'=>'มีนาคม','04'=>'เมษายน','05'=>'พฤษภาคม','06'=>'มิถุนายน','07'=>'กรกฎาคม','08'=>'สิงหาคม','09'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$this->month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
        $this->load->model('Sp_cremation/Cremation', 'cremation');
        $this->load->model('Sp_account/Account', 'account');
        $this->load->model('Sp_account/Report_account', 'report');
	}

    public function index() {
        $page = !empty($_GET["page"]) ? (int) $_GET["page"] : 1;

        $account_dates = $this->db->select("account_datetime")
                                    ->from("coop_sp_account")
                                    ->where("(account_status != 2 OR account_status is null) AND group_id = '".$this->account_group_id."'")
                                    ->order_by("account_datetime desc")
                                    ->group_by("account_datetime")
                                    ->get()->result_array();

        $paging = $this->pagination_center->paginating($page, count($account_dates), 1, 20);

        $conditions = array();
        $conditions["account_datetime"] = $account_dates[$page-1]["account_datetime"];

        $result = $this->account->get_account_transactions($this->account_group_id, $conditions);

        $account_chart = $this->account->get_charts($this->account_group_id, null, 3, 1);

        $arr_data['data_account_detail'] = $result["datas"];
        $arr_data['account_chart'] = $account_chart["charts"];
        $arr_data['space'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $arr_data['paging'] = $paging;
        $arr_data['path'] = $this->path;
        $this->libraries->template('sp_account/account_index',$arr_data);
    }

    public function get_account_detail_by_id() {
        $result = $this->account->get_account_detail_by_id($_GET["account_id"]);
        if(!empty($result)) echo json_encode($result);
        exit;
    }

    public function save() {
        $result = $this->account->save($this->account_group_id, $_POST);
        echo json_encode($result);
    }

    public function cancel_account_transaction() {
        $result = $this->account->cancel_account_transaction($this->account_group_id, $_POST["account_id"]);
        echo json_encode($result);
    }

    function account_day_book(){
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;
        $arr_data['path'] = $this->path;
        $this->libraries->template('sp_account/account_day_book',$arr_data);
    }

    function ajax_check_day_book(){
        $result = $this->report->check_day_book($this->account_group_id, $_GET);
        echo $result;
    }

    public function account_day_book_excel(){
        $arr_data = array();
        $result = $this->report->get_day_book_data($this->account_group_id, $_GET);
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);
        $arr_data['data'] = $result["datas"];
        $this->load->view('sp_account/account_day_book_excel',$arr_data);
    }

    function account_chart_report(){
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;
        $arr_data["account_charts"] = $this->account->get_charts($this->account_group_id, null, 3, 1);
        $arr_data['path'] = $this->path;
        $this->libraries->template('sp_account/account_chart_report',$arr_data);
    }

    
    function ajax_check_account_chart_report() {
        $result = $this->report->check_account_chart($this->account_group_id, $_POST);
        echo $result;
    }

    function account_chart_report_pdf() {
        $arr_data = $this->report->get_account_chart_report($this->account_group_id, $_GET);
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);
        $this->load->view('sp_account/account_chart_report_pdf',$arr_data);
    }

    function account_chart_report_excel() {
        $arr_data = $this->report->get_account_chart_report($this->account_group_id, $_GET);
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);
        $this->load->view('sp_account/account_chart_report_excel',$arr_data);
    }

    function account_experimental_budget() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;
        $arr_data['path'] = $this->path;
        $this->libraries->template('sp_account/account_experimental_budget',$arr_data);
    }

    
    function ajax_check_account_experimental_budget(){
        $result = $this->report->account_experimental_budget($this->account_group_id, $_POST);
        echo $result;
    }

    function coop_account_experimental_budget_excel() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;
        $arr_data['month_short_arr'] = $this->month_short_arr;

        $datas = $this->report->get_account_experimental_budget_data($this->account_group_id, $_GET);

        $arr_data["prev_budgets"] = $datas["prev_budgets"];
        $arr_data["rs"] = $datas["rs"];
        $arr_data["data_chart"] = $datas["data_chart"];
        $arr_data['textTitle'] = $datas['textTitle'];
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_account/account_experimental_budget_excel',$arr_data);
    }

    function coop_account_experimental_budget_pdf() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;
        $arr_data['month_short_arr'] = $this->month_short_arr;

        $datas = $this->report->get_account_experimental_budget_data($this->account_group_id, $_GET);

        $arr_data["prev_budgets"] = $datas["prev_budgets"];
        $arr_data["rs"] = $datas["rs"];
        $arr_data["data_chart"] = $datas["data_chart"];
        $arr_data['textTitle'] = $datas['textTitle'];

        $charts = array();
        $page = 0;
        $first_page_size = 16;
        $page_size = 16;
        foreach($datas["data_chart"] as $index => $chart) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $charts[$page][] = $chart;
        }

        $arr_data["page_all"] = $page;
        $arr_data["data_charts"] = $datas["data_chart"];
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_account/account_experimental_budget_pdf',$arr_data);
    }

    public function coop_account_balance_sheet(){
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;
        $arr_data['path'] = $this->path;
        $this->libraries->template('sp_account/coop_account_balance_sheet',$arr_data);
    }

    public function ajax_check_account_balance_sheet(){
        echo "success";
    }

    public function coop_account_balance_sheet_excel(){
        $arr_data = array();

        $data = $this->report->get_account_balance_data($this->account_group_id, $_GET);

        $arr_data["from_date"] = $data["from_date"];
        $arr_data["thur_date"] = $data["thur_date"];
        $arr_data["prev_date"] = $data["prev_date"];
        $arr_data["from_date_header"] = $data["from_date_header"];
        $arr_data["thur_date_header"] = $data["thur_date_header"];
        $arr_data["prev_date_header"] = $data["prev_date_header"];
        $arr_data["account_charts"] = $data["account_charts"];
        $arr_data["prev_year_budgets"] = $data["prev_year_budgets"];
        $arr_data["year_budgets"] = $data["year_budgets"];
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);
        // echo "<pre>"; print_r($arr_data); exit;

        $this->load->view('sp_account/coop_account_balance_sheet_excel',$arr_data);
    }

    public function coop_account_balance_sheet_pdf(){
        $arr_data = array();

        $data = $this->report->get_account_balance_data($this->account_group_id, $_GET);
    
        $arr_data["from_date"] = $data["from_date"];
        $arr_data["thur_date"] = $data["thur_date"];
        $arr_data["prev_date"] = $data["prev_date"];
        $arr_data["from_date_header"] = $data["from_date_header"];
        $arr_data["thur_date_header"] = $data["thur_date_header"];
        $arr_data["prev_date_header"] = $data["prev_date_header"];
        $arr_data["account_charts"] = $data["account_charts"];
        $arr_data["prev_year_budgets"] = $data["prev_year_budgets"];
        $arr_data["year_budgets"] = $data["year_budgets"];
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_account/coop_account_balance_sheet_pdf',$arr_data);
    }

    
    public function coop_account_profit_lost_statement(){
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;
        $arr_data['path'] = $this->path;
        $this->libraries->template('sp_account/coop_account_profit_lost_statement',$arr_data);
    }

    public function ajax_check_account_profit_lost_statement() {
        echo $this->report->check_account_profit_lost_statement($group_id, $_POST);
    }

    public function coop_account_profit_lost_statement_excel(){
        $arr_data = array();

        $data = $this->report->get_account_profit_lost_data($this->account_group_id, $_GET);

        $arr_data["from_date"] = $data["from_date"];
        $arr_data["thur_date"] = $data["thur_date"];
        $arr_data["prev_date"] = $data["prev_date"];
        $arr_data["from_date_header"] = $data["from_date_header"];
        $arr_data["thur_date_header"] = $data["thur_date_header"];
        $arr_data["prev_date_header"] = $data["prev_date_header"];
        $arr_data["account_charts"] = $data["account_charts"];
        $arr_data["prev_year_budgets"] = $data["prev_year_budgets"];
        $arr_data["year_budgets"] = $data["year_budgets"];
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_account/coop_account_profit_lost_statement_excel',$arr_data);
    }

    public function coop_account_profit_lost_statement_pdf(){
        $arr_data = array();

        $data = $this->report->get_account_profit_lost_data($this->account_group_id, $_GET);

        $arr_data["from_date"] = $data["from_date"];
        $arr_data["thur_date"] = $data["thur_date"];
        $arr_data["prev_date"] = $data["prev_date"];
        $arr_data["from_date_header"] = $data["from_date_header"];
        $arr_data["thur_date_header"] = $data["thur_date_header"];
        $arr_data["prev_date_header"] = $data["prev_date_header"];
        $arr_data["account_charts"] = $data["account_charts"];
        $arr_data["prev_year_budgets"] = $data["prev_year_budgets"];
        $arr_data["year_budgets"] = $data["year_budgets"];
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_account/coop_account_profit_lost_statement_pdf',$arr_data);
    }

    public function tranction_voucher() {
        $result = $this->report->get_account_transaction_vouchers($this->account_group_id, $_GET);
        $arr_data = $result;
        $arr_data['path'] = $this->path;
        $this->libraries->template('sp_account/tranction_voucher',$arr_data);
    }

    public function account_pdf_tranction_voucher() {
        $result = $this->report->get_account_transaction_voucher($this->account_group_id, $_POST);
        $arr_data = $result;
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_account/account_pdf_tranction_voucher',$arr_data);
    }

    public function cash_journal_report() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;
        $arr_data['path'] = $this->path;
        $this->libraries->template('sp_account/cash_journal_report',$arr_data);
    }

    public function check_cash_journal() {
        echo $this->report->check_cash_journal($this->account_group_id, $_GET);
    }

    public function cash_journal_preview() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;

        $result = $this->report->get_cash_journal($this->account_group_id, $_GET);
        $datas = array();
        $page = 0;
        $first_page_size = 20;
        $page_size = 20;
        $index = 0;
        foreach($result["accounts"] as $account) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $account;
            $index++;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $arr_data["cash_debit"] = $result["cash_debit"];
        $arr_data["cash_credit"] = $result["cash_credit"];
        $arr_data["cash_balance"] = $result["cash_balance"];
        $arr_data["diff_cash"] = $result["diff_cash"];
        $arr_data['path'] = $this->path;
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->preview_libraries->template_preview('sp_account/cash_journal_preview', $arr_data);
    }

    public function cash_journal_excel() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;

        $accounts = $this->report->get_cash_journal($this->account_group_id, $_GET);
        $arr_data["datas"] = $accounts["accounts"];

        $arr_data["cash_debit"] = $accounts["cash_debit"];
        $arr_data["cash_credit"] = $accounts["cash_credit"];
        $arr_data["cash_balance"] = $accounts["cash_balance"];
        $arr_data["diff_cash"] = $accounts["diff_cash"];
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_account/cash_journal_excel',$arr_data);
    }

    
    public function general_journal_report() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;
        $arr_data['path'] = $this->path;
        $this->libraries->template('sp_account/general_journal_report',$arr_data);
    }

    public function check_general_journal() {
        echo $this->report->check_general_journal($this->account_group_id, $_GET);
    }

    public function general_journal_preview() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;

        $accounts = $this->report->get_general_journal($this->account_group_id, $_GET);
        $datas = array();
        $page = 0;
        $first_page_size = 20;
        $page_size = 20;
        $index = 0;
        foreach($accounts as $account) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $account;
            $index++;
        }
        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;
        $arr_data['path'] = $this->path;
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->preview_libraries->template_preview('sp_account/general_journal_preview', $arr_data);
    }

    public function general_journal_excel() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;

        $accounts = $this->report->get_general_journal($this->account_group_id, $_GET);
        $arr_data["datas"] = $accounts;
        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_account/general_journal_excel',$arr_data);
    }
}
