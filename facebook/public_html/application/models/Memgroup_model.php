<?php
class Memgroup_model extends CI_Model {
    
    public function get_department_all()
    {
        $query = $this->db->select('id, mem_group_name')
        ->from('coop_mem_group')
        ->where('mem_group_type = 1')
        ->get();
        return $query->result_array();
    }

}