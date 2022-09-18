<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permission_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        # Load libraries
        //$this->load->library('parser');
        $this->load->helper(array('html', 'url'));
    }

    public  function permission_url($old_url,$request_url){
        $request_url_qc = explode("?", $request_url);
        $request_url_qcf = explode("/", $request_url_qc[0]);

        $this->db->select(array('*'));
        $this->db->from('coop_menu');
        $this->db->where(" menu_url = '{$request_url_qc[0]}' " );
        $row_detail = $this->db->get()->result_array();
        $row['permission_url'] = $row_detail;
        $permission_id = $row_detail[0]['menu_id'];

            if(!empty($permission_id)){
                $permission_id =$permission_id;
            }else  {
                $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
                $actual_link_qc =$actual_link.'://'.$_SERVER['HTTP_HOST'];
                $old_url = str_replace($actual_link_qc,"",$old_url);

                $old_url_qc = explode("?", $old_url);
                $old_url_qcf = explode("/", $old_url_qc[0]);

                $this->db->select(array('*'));
                $this->db->from('coop_menu');
                $this->db->where(" menu_url = '{$old_url_qc[0]}' " );
                $row_detail = $this->db->get()->result_array();
                $row['permission_url'] = $row_detail;
                $permission_id = $row_detail[0]['menu_id'];

                if(!empty($permission_id)){
                    $permission_id = $permission_id;

                }else {

                    $count_url = count($old_url_qcf);
                    foreach ($old_url_qcf as $key => $value_url) {
                        $group_text = '';
                        for ($ic = 0; $ic < $count_url; $ic++) {
                            if (!empty($old_url_qcf[$ic])) {
                                $group_text .= '/' . $old_url_qcf[$ic];
                            }
                        }

                        $this->db->select(array('*'));
                        $this->db->from('coop_menu');
                        $this->db->where(" menu_url = '{$group_text}' ");
                        $row_detail = $this->db->get()->result_array();
                        $row['permission_url'] = $row_detail;
                        $permission_id = $row_detail[0]['menu_id'];

                        if (!empty($permission_id)) {
                            //echo $permission_id . '<br>';
                            break;
                        }
                        $count_url--;
                    }
                }
            }
        return  $permission_id;
    }

}
