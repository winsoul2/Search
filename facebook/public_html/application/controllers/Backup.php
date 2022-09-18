<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Backup extends CI_Controller {
	private $dbBackup;
	private $backup_types = [
		0 => "อัตโนมัติ",
		1 => "ด้วยตนเอง"
	];
	private $file_types = [
		0 => "ฐานข้อมูล",
		1 => "ไฟล์"
	];
	
	function __construct() {
		parent::__construct();
		
		$this->dbBackup = $this->load->database('backup', true);
	}
	
	function human_filesize($bytes, $decimals = 0) {
		$sz = ' KMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . " " . trim(@$sz[$factor]) . "B";
	}
	
	private function _backup_db($type) {
		$tmp_path = $this->dbBackup->backuppath."/tmp";
		$backup_date = date("Y-m-d H:i:s");
		$backup_date2 = date("YmdHis", strtotime($backup_date));
		
		file_put_contents($tmp_path."/backup_info.json", json_encode([
			"backup_date" => $backup_date,
			"file_type" => 0
		]));
		
		exec("mysqldump -u ".$this->db->username." --password=".$this->db->password." ".$this->db->database." > ".$tmp_path."/backup_db.sql");
		exec("tar zcvf ".$tmp_path."/backup_db.tgz -C ".$tmp_path." .");
		exec("rm -f ".$tmp_path."/backup_info.json");
		exec("rm -f ".$tmp_path."/backup_db.sql");
		exec("mv ".$tmp_path."/backup_db.tgz ".$this->dbBackup->backuppath."/backup_db_".$backup_date2.".tgz ");
		
		$this->dbBackup->insert("coop_backup", [
			"domain" => $_SERVER["HTTP_HOST"],
			"file_type" => 0,
			"backup_date" => $backup_date,
			"backup_file" => "backup_db_".$backup_date2.".tgz",
			"backup_type" => $type,
			"ip" => $_SERVER["REMOTE_ADDR"]
		]);
	}
	
	private function _restore_db($backup_file) {
		$tmp_path = $this->dbBackup->backuppath."/tmp";
		
		exec("tar zxvf ".$backup_file." -C ".$tmp_path);
		exec("mysql -u ".$this->db->username." --password=".$this->db->password." -D ".$this->db->database." < ".$tmp_path."/backup_db.sql");
		
		$backup_info = json_decode(file_get_contents($tmp_path."/backup_info.json"), true);
		
		$this->dbBackup->select("*");
		$this->dbBackup->from("coop_backup");
		$this->dbBackup->where("domain = '".$_SERVER["HTTP_HOST"]."' AND file_type = 0 AND backup_date = '".$backup_info["backup_date"]."'");
		$row_backup = $this->dbBackup->get()->row_array();
		if(empty($row_backup)) {
			$this->dbBackup->insert("coop_backup", [
				"domain" => $_SERVER["HTTP_HOST"],
				"file_type" => 0,
				"backup_date" => $backup_info["backup_date"],
				"backup_file" => "",
				"backup_type" => "1",
				"ip" => $_SERVER["REMOTE_ADDR"]
			]);
			$backup_id = $this->dbBackup->insert_id();
		}
		else {
			$backup_id = $row_backup["backup_id"];
		}
		
		$this->dbBackup->insert("coop_restore", [
			"backup_id" => $backup_id,
			"domain" => $_SERVER["HTTP_HOST"],
			"file_type" => 0,
			"restore_date" => date("Y-m-d H:i:s"),
			"restore_user" => empty($_SESSION["USER_NAME"]) ? "" : $_SESSION["USER_NAME"],
			"ip" => $_SERVER["REMOTE_ADDR"]
		]);
		
		exec("rm -f ".$tmp_path."/backup_info.json");
		exec("rm -f ".$tmp_path."/backup_db.sql");
		exec("rm -f ".$backup_file);
	}
	
	private function _backup_file($type) {
		$file_path = $_SERVER["DOCUMENT_ROOT"]."/assets/uploads";
		$tmp_path = $this->dbBackup->backuppath."/tmp";
		$backup_date = date("Y-m-d H:i:s");
		$backup_date2 = date("YmdHis", strtotime($backup_date));
		
		file_put_contents($file_path."/backup_info.json", json_encode([
			"backup_date" => $backup_date,
			"file_type" => 1
		]));
		
		exec("tar zcvf ".$tmp_path."/backup_file.tgz -C ".$file_path." .");
		exec("rm -f ".$file_path."/backup_info.json");
		exec("mv ".$tmp_path."/backup_file.tgz ".$this->dbBackup->backuppath."/backup_file_".$backup_date2.".tgz ");
		
		$this->dbBackup->insert("coop_backup", [
			"domain" => $_SERVER["HTTP_HOST"],
			"file_type" => 1,
			"backup_date" => $backup_date,
			"backup_file" => "backup_file_".$backup_date2.".tgz",
			"backup_type" => $type,
			"ip" => $_SERVER["REMOTE_ADDR"]
		]);
	}
	
	private function _restore_file($backup_file) {
		$file_path = $_SERVER["DOCUMENT_ROOT"]."/assets/uploads";
		
		exec("tar zxvf ".$backup_file." -C ".$file_path);
		
		$backup_info = json_decode(file_get_contents($file_path."/backup_info.json"), true);
		
		$this->dbBackup->select("*");
		$this->dbBackup->from("coop_backup");
		$this->dbBackup->where("domain = '".$_SERVER["HTTP_HOST"]."' AND file_type = 1 AND backup_date = '".$backup_info["backup_date"]."'");
		$row_backup = $this->dbBackup->get()->row_array();
		if(empty($row_backup)) {
			$this->dbBackup->insert("coop_backup", [
				"domain" => $_SERVER["HTTP_HOST"],
				"file_type" => 1,
				"backup_date" => $backup_info["backup_date"],
				"backup_file" => "",
				"backup_type" => "1",
				"ip" => $_SERVER["REMOTE_ADDR"]
			]);
			$backup_id = $this->dbBackup->insert_id();
		}
		else {
			$backup_id = $row_backup["backup_id"];
		}
		
		$this->dbBackup->insert("coop_restore", [
			"backup_id" => $backup_id,
			"domain" => $_SERVER["HTTP_HOST"],
			"file_type" => 1,
			"restore_date" => date("Y-m-d H:i:s"),
			"restore_user" => empty($_SESSION["USER_NAME"]) ? "" : $_SESSION["USER_NAME"],
			"ip" => $_SERVER["REMOTE_ADDR"]
		]);
		
		exec("rm -f ".$file_path."/backup_info.json");
		exec("rm -f ".$backup_file);
	}
	
	public function _delete_old_backup() {
		$this->dbBackup->select("*");
		$this->dbBackup->from("coop_backup");
		$this->dbBackup->where("domain = '".$_SERVER["HTTP_HOST"]."' AND backup_date < DATE_ADD(NOW(), INTERVAL -7 DAY)");
		$rs = $this->dbBackup->get()->result_array();
		foreach($rs as $key => $row) {
			$this->dbBackup->delete("coop_backup", [ "backup_id" => $row["backup_id"] ]);
			exec("rm -f ".$this->dbBackup->backuppath."/".$row["backup_file"]);
		}
	}
	
	public function index() {
		$arr_data['file_types'] = $this->file_types;
		
		$row_restores = [];
		foreach($this->file_types as $key => $file_type) {
			$this->dbBackup->select("*");
			$this->dbBackup->from("coop_restore");
			$this->dbBackup->where("domain = '".$_SERVER["HTTP_HOST"]."' AND file_type = ".$key);
			$this->dbBackup->order_by("restore_date DESC, restore_id DESC");
			$this->dbBackup->limit(1);
			$row_restores[$key] = $this->dbBackup->get()->row_array();
		}
		
		$this->dbBackup->select("*");
		$this->dbBackup->from("coop_backup");
		$this->dbBackup->where("domain = '".$_SERVER["HTTP_HOST"]."'");
		$this->dbBackup->order_by("backup_date DESC, backup_id DESC");
		$rs = $this->dbBackup->get()->result_array();
		foreach($rs as $key => $row) {
			$rs[$key]["backup_type_name"] = $this->backup_types[$row["backup_type"]];
			$rs[$key]["file_type_name"] = $this->file_types[$row["file_type"]];
			$rs[$key]["file_size"] = empty($row["backup_file"]) ? "" : $this->human_filesize(@filesize($this->dbBackup->backuppath."/".$row["backup_file"]));
			$rs[$key]["restore_date"] = $row_restores[$row["file_type"]]["backup_id"] == $row["backup_id"] ? $row_restores[$row["file_type"]]["restore_date"] : "";
			$rs[$key]["restore_user"] = $row_restores[$row["file_type"]]["backup_id"] == $row["backup_id"] ? $row_restores[$row["file_type"]]["restore_user"] : "";
			
			$this->dbBackup->select("*");
			$this->dbBackup->from("coop_backup_download");
			$this->dbBackup->where("backup_id = '".$row["backup_id"]."'");
			$this->dbBackup->order_by("download_date DESC, backup_download_id DESC");
			$this->dbBackup->limit(1);
			$row_download = $this->dbBackup->get()->row_array();
			$rs[$key]["download_date"] = empty($row_download) ? "" : $row_download["download_date"];
			$rs[$key]["download_user"] = empty($row_download) ? "" : $row_download["download_user"];
		}
		
		$arr_data['rs'] = $rs;
		
		$this->libraries->template('backup/index',$arr_data);
	}
	
	public function auto_backup() {
		if($_GET["file_type"] == "0") { $this->_backup_db(0); }
		elseif($_GET["file_type"] == "1") { $this->_backup_file(0); }
		
		$this->_delete_old_backup();
		
		echo "OK";
		exit;
	}
	
	public function backup_process() {
		if($_POST["file_type"] == "0") { $this->_backup_db(1); }
		elseif($_POST["file_type"] == "1") { $this->_backup_file(1); }
		
		echo "OK";
		exit;
	}
	
	public function download() {
		if(empty($_SESSION["USER_ID"])) {
			header("location: ".base_url("backup"));
			exit;
		}
		
		$filepath = $this->dbBackup->backuppath."/".$_GET["f"];
		
		if(file_exists($filepath)) {
			$this->dbBackup->insert("coop_backup_download", [
				"backup_id" => $_GET["id"],
				"download_date" => date("Y-m-d H:i:s"),
				"download_user" => empty($_SESSION["USER_NAME"]) ? "" : $_SESSION["USER_NAME"],
				"ip" => $_SERVER["REMOTE_ADDR"]
			]);
			
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
			header('Content-Length: '.filesize($filepath));
			flush(); // Flush system output buffer
			readfile($filepath);
		}
		
		exit;
	}
	
	public function restore_process() {
		if(!empty($_FILES["backup_file"]["tmp_name"]) && !empty($_SESSION["USER_ID"])) {
			if($_POST["file_type"] == "0") { $this->_restore_db($_FILES["backup_file"]["tmp_name"]); }
			elseif($_POST["file_type"] == "1") { $this->_restore_file($_FILES["backup_file"]["tmp_name"]); }
		}
		
		echo '<script>window.top.window.restore_completed();</script>';
		exit;
	}
	
	public function testbackup() {
		//$this->_backup_db(1);
		//$this->_backup_file(1);
		echo "Backup Completed.";
		exit;
	}
	
	public function testrestore() {
		//$this->_restore_db("/home/spkttest/coop_backup/tmp/backup_db_20190422143959.tgz");
		//$this->_restore_file("/home/spkttest/coop_backup/tmp/backup_file_20190424095207.tgz");
		echo "Restore Completed.";
		exit;
	}
	
}