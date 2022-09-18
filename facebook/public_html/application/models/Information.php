<?php



class Information extends CI_Model {

	var $_member = null;
	var $_setting_share = null;
	var $_setting_retire = null;

	public function __construct()
	{
		parent::__construct();
		self::getShareSetting();
		self::getRetireAgeSetting();
	}

	public function  member($member_id = ""){

		if($member_id == ""){
			return null;
		}
		$this->_member = $this->db->get_where('coop_mem_apply', "member_id='{$member_id}'")->row();

		return $this;
	}

	public function getMemberInfo(){
		if($this->_member) {
			$share = self::getShareCollect($this->_member->member_id);
			$result['row_member'] = self::getInfoArray();
			$result['count_share']  = $share->share_collect;
			$result['cal_share'] = $share->share_collect_value;
			$result['share_period'] = $this->_member->share_month;
			$result['share_value'] = $this->_setting_share;
			$result['retire_age'] = $this->_setting_retire;
			$result['row_member']['mem_group_name'] = self::getGroup('all');
			$result['mem_type_list'] = self::getMemberTypeList();
			$row_gain_detail = self::getGainDetail();
			if(!empty($row_gain_detail)){
				$testament = 'กำหนดผู้รับพินัยกรรมแล้ว';
				$style_testament = '';
			}else{
				$testament = 'รอพินัยกรรม';
				$style_testament = 'style="color: red"';
			}

			$result['testament'] = $testament;
			$result['style_testament'] = $style_testament;
			$result['refrain_share_txt'] = $this->share->findRefrainShare($this->_member->member_id);
			$result['mem_apply_type'] = self::getMemApplyTypeList();
			return $result;
		}
		return null;
	}

	private function getRetireAgeSetting(){
		$this->db->select(array('retire_age'));
		$this->db->from('coop_profile');
		$this->_setting_retire = $this->db->get()->row()->retire_age;
	}

	//ประเภทสมาชิก
	private function getMemberTypeList(){
		$this->db->select('mem_type_id, mem_type_name');
		$this->db->from('coop_mem_type');
		$rs_mem_type = $this->db->get()->result_array();
		$mem_type_list = array();
		foreach($rs_mem_type AS $key=>$row_mem_type){
			$mem_type_list[$row_mem_type['mem_type_id']] = $row_mem_type['mem_type_name'];
		}
		return $mem_type_list;
	}

	private function getMemApplyTypeList(){
		//ประเภทสมัคร
		$this->db->select('apply_type_id, apply_type_name');
		$this->db->from('coop_mem_apply_type');
		$rs_mem_type = $this->db->get()->result_array();
		$mem_type_list = array();
		foreach($rs_mem_type AS $key=>$row_mem_type){
			$mem_apply_type[$row_mem_type['apply_type_id']] = $row_mem_type['apply_type_name'];
		}
		return $mem_apply_type;
	}

	private function getShareCollect($member_id){
		$this->db->select(array('share_collect','share_collect_value'));
		$this->db->from('coop_mem_share');
		$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2','5')");
		$this->db->order_by('share_date DESC,share_id DESC');
		$this->db->limit(1);
		return $this->db->get()->row();
	}

	private function getShareSetting(){
		$this->db->select('*');
		$this->db->from('coop_share_setting');
		$this->db->order_by('setting_id DESC');
		$row = $this->db->get()->row();
		$this->_setting_share = $row->setting_value;
	}

	private function exceptionMember(){
		try{
			if($this->_member == null){
				throw new Exception("Must be call member() function");
			}
		}catch (Exception $e){
			show_error($e->getMessage(), $e->getCode(), $e->getMessage());
		}
	}

	public function getInfoArray(){
		return (array) $this->_member;
	}

	public function getInfo(){
		return $this->_member;
	}

	public function getRetire($max_age_retire = 60){

		if ($this->_member->birthday == "") {
			return null;
		}
		return date("Y-m-d", strtotime($this->_member->birthday)." + ".$max_age_retire." YEAR");
	}

	public function getFullName($type = 'short'){
		$prename = "";
		if($this->_member->prename_id != ""){
			$prename = $this->db->get_where('coop_prename', "prename_id='{$this->_member->prename_id}' ")->row_array()['prename_'.$type];
		}
		return $prename.$this->_member->firstname_th." ".$this->_member->lastname_th;
	}

	public function getGroup($type = ''){

		self::exceptionMember();

		if($type == ""){
			return null;
		}
		$res = null;
		if($type == 'level') {
			$res = $this->db->get_where('coop_mem_group', "id ='{$this->_member->level}' ")->row();
		}else if($type == 'faction'){
			$res = $this->db->get_where('coop_mem_group', "id ='{$this->_member->faction}' ")->row();
		}else if($type == 'depart'){
			$res = $this->db->get_where('coop_mem_group', "id ='{$this->_member->department}' ")->row();
		}else{
			$this->db->select("concat( `t2`.`mem_group_name`, ' ', `t3`.`mem_group_name`, ' ', `t4`.`mem_group_name` ) as `mem_group_name`");
			$this->db->from('coop_mem_apply as t1');
			$this->db->join("coop_mem_group AS t2","t1.department = t2.id","left");
			$this->db->join("coop_mem_group AS t3","t1.faction = t3.id","left");
			$this->db->join("coop_mem_group AS t4","t1.level = t4.id","left");
			$this->db->join("coop_prename AS t5","t1.prename_id = t5.prename_id","left");
			$this->db->where("t1.member_id = '".$this->_member->member_id."'");
			$res = $this->db->get()->row();
		}

		//echo $this->db->last_query(); exit;
		return $res->mem_group_name;
	}

	public function getMemberType() {

		self::exceptionMember();
		$result = $this->db->get_where('coop_mem_type', array('mem_type_id' => $this->_member->mem_tyep_id))->row();
		return $result;

	}

	public function getPosition(){
		return 'ไม่ระบุ';
	}

	public function getStStatus(){
		self::exceptionMember();
		return $this->db->get_where("coop_mem_req_resign", array("member_id" => $this->_member->member_id, "req_resign_status" => 1))->result_array()[0]['approve_date'];
	}

	public function get_province() {
		return $this->db->select('*')->from('coop_province')->get()->result_array();
	}

	public function get_province_by_id($id) {
		return $this->db->select('*')->from('coop_province')->where("province_id = '".$id."'")->get()->row();
	}

	public function getIncoming(){
		//รายได้
		$sql = "SELECT
			*, 
			IFNULL((select coop_income_member.income_value from coop_income_member where coop_income_member.member_id = '".$this->_member->member_id."' and coop_income_member.income_id = coop_income.id), 0) as income_value
		FROM
			`coop_income`
		ORDER BY
			`seq` ASC";
		return $this->db->query($sql)->result_array();
	}

	public function saveInComing($data){
		$member_id 			= $data['member_id'];
		$income_id 			= $data['income_id'];
		$income_value 		= $data['income_value'];
		if($member_id!="" && $income_id!="" && $income_value!=""){
			$this->db->where("member_id", $member_id);
			$this->db->where("income_id", $income_id);
			$this->db->delete("coop_income_member");
			//insert
			$this->db->set("income_id", $income_id);
			$this->db->set("member_id", $member_id);
			$this->db->set("income_value", $income_value);
			$this->db->insert("coop_income_member");

		}
	}


	private function getGainDetail(){
		$this->db->select(array('*'));
		$this->db->from('coop_mem_gain_detail');
		$this->db->where("member_id = '".$this->_member->member_id ."'");
		$this->db->join('coop_prename', 'coop_prename.prename_id = coop_mem_gain_detail.g_prename_id', 'left');
		$this->db->join('coop_district', 'coop_district.district_id = coop_mem_gain_detail.g_district_id', 'left');
		$this->db->join('coop_amphur', 'coop_amphur.amphur_id = coop_mem_gain_detail.g_amphur_id', 'left');
		$this->db->join('coop_province', 'coop_province.province_id = coop_mem_gain_detail.g_province_id', 'left');
		$this->db->join('coop_mem_relation', 'coop_mem_relation.relation_id = coop_mem_gain_detail.g_relation_id', 'left');
		$this->db->join('coop_user', 'coop_user.user_id = coop_mem_gain_detail.admin_id', 'left');
		return $this->db->get()->result_array();
	}
}


