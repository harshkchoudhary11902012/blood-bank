<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		
			extract($_POST);		
			$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."' ");
			if($qry->num_rows > 0){
				foreach ($qry->fetch_array() as $key => $value) {
					if($key != 'passwors' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
				
					return 1;
			}else{
				return 3;
			}
	}
	function login2(){
		
			extract($_POST);
			if(isset($email))
				$username = $email;
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if($_SESSION['login_alumnus_id'] > 0){
				$bio = $this->db->query("SELECT * FROM alumnus_bio where id = ".$_SESSION['login_alumnus_id']);
				if($bio->num_rows > 0){
					foreach ($bio->fetch_array() as $key => $value) {
						if($key != 'passwors' && !is_numeric($key))
							$_SESSION['bio'][$key] = $value;
					}
				}
			}
			if($_SESSION['bio']['status'] != 1){
					foreach ($_SESSION as $key => $value) {
						unset($_SESSION[$key]);
					}
					return 2 ;
					exit;
				}
				return 1;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		$data .= ", type = '$type' ";
		
		$chk = $this->db->query("Select * from users where username = '$username' and id !='$id' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	function signup(){
		extract($_POST);
		$data = " name = '".$firstname.' '.$lastname."' ";
		$data .= ", username = '$email' ";
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("INSERT INTO users set ".$data);
		if($save){
			$uid = $this->db->insert_id;
			$data = '';
			foreach($_POST as $k => $v){
				if($k =='password')
					continue;
				if(empty($data) && !is_numeric($k) )
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if($_FILES['img']['tmp_name'] != ''){
							$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
							$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
							$data .= ", avatar = '$fname' ";

			}
			$save_alumni = $this->db->query("INSERT INTO alumnus_bio set $data ");
			if($data){
				$aid = $this->db->insert_id;
				$this->db->query("UPDATE users set alumnus_id = $aid where id = $uid ");
				$login = $this->login2();
				if($login)
				return 1;
			}
		}
	}
	function update_account(){
		extract($_POST);
		$data = " name = '".$firstname.' '.$lastname."' ";
		$data .= ", username = '$email' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' and id != '{$_SESSION['login_id']}' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("UPDATE users set $data where id = '{$_SESSION['login_id']}' ");
		if($save){
			$data = '';
			foreach($_POST as $k => $v){
				if($k =='password')
					continue;
				if(empty($data) && !is_numeric($k) )
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if($_FILES['img']['tmp_name'] != ''){
							$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
							$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
							$data .= ", avatar = '$fname' ";

			}
			$save_alumni = $this->db->query("UPDATE alumnus_bio set $data where id = '{$_SESSION['bio']['id']}' ");
			if($data){
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
				$login = $this->login2();
				if($login)
				return 1;
			}
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['system'][$key] = $value;
		}

			return 1;
				}
	}

	
	function save_donor(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", address = '$address' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", blood_group = '$blood_group' ";
			if(empty($id)){
				$save = $this->db->query("INSERT INTO donors set $data");
			}else{
				$save = $this->db->query("UPDATE donors set $data where id = $id");
			}
		if($save)
			return 1;
	}
	function delete_donor(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM donors where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_donation(){
		extract($_POST);
		$data = " donor_id = '$donor_id' ";
		$data .= ", blood_group = '$blood_group' ";
		$data .= ", status = '$status' ";
		$data .= ", volume = '$volume' ";
		$data .= ", date_created = '$date_created' ";
			if(empty($id)){
				$save = $this->db->query("INSERT INTO blood_inventory set $data");
			}else{
				$save = $this->db->query("UPDATE blood_inventory set $data where id = $id");
			}
		if($save)
			return 1;
	}
	function delete_donation(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM blood_inventory where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_request(){
		extract($_POST);
		$data = " patient = '$patient' ";
		$data .= ", blood_group = '$blood_group' ";
		$data .= ", physician_name = '$physician_name' ";
		$data .= ", volume = '".($volume * 1000)."' ";
		if(isset($status))
		$data .= ", status = '$status' ";
		
			if(empty($id)){
				$i = 1;
				while($i == 1){
					$rand = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					$ref_code = substr(str_shuffle($rand), 0,8);
					$chk = $this->db->query("SELECT * FROM requests where ref_code = '$ref_code'")->num_rows;
					if($chk <= 0){
						$i = 0;
						$data .= ", ref_code = '$ref_code' ";
					}
				}
				$save = $this->db->query("INSERT INTO requests set $data");
			}else{
				$save = $this->db->query("UPDATE requests set $data where id = $id");
			}
		if($save)
			return 1;
	}
	function delete_request(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM requests where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function get_available(){
		extract($_POST);
		$where = '';
		if(!empty($id)){
			$where = " and request_id != $id ";
		}
		$inn = $this->db->query("SELECT sum(volume) as total from blood_inventory where blood_group = '$blood_group' and status = 1 ");
		$inn = $inn->num_rows > 0 ? $inn->fetch_array()['total'] : 0;
		$out = $this->db->query("SELECT sum(volume) as total from blood_inventory where blood_group = '$blood_group' and status = 2 $where ");
		$out = $out->num_rows > 0 ? $out->fetch_array()['total'] : 0;

		$available = $inn - $out;
		$available = $available / 1000;
		return $available;
	}
	function chk_request(){
		extract($_POST);
		$qry= $this->db->query("SELECT * FROM requests where ref_code = '$ref_code'");
		$data = array();
		if($qry->num_rows > 0){
			$result = $qry->fetch_array();
			if($result['status'] == 0){
				$data['status'] = 2;
			}else{
				$chk = $this->db->query("SELECT * FROM handedover_request hr inner join requests r on r.id = hr.request_id where r.ref_code = '$ref_code' ".($id > 0 ? " and hr.id != $id " : "")." ")->num_rows;
				if($chk > 0){
				$data['status'] = 3;
				}else{
					$data['status'] = 1;
				foreach($result as $k => $v){
					if(!is_numeric($k)){
						$data['data'][$k] = $v;
					}
				}
				}
			}
		}else{
				$data['status'] = 0;
		}
		if(isset($data['data']['volume']))
		$data['data']['volumeL'] = $data['data']['volume'] / 1000; 	
		return json_encode($data);
	}
	function save_handover(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','ref_code')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO handedover_request set $data");
			$id=$this->db->insert_id;
		}else{
			$save = $this->db->query("UPDATE handedover_request set $data where id = $id");
		}

		if($save){
			$request = $this->db->query("SELECT * FROM requests where ref_code = '$ref_code' ")->fetch_array();
			$data = " blood_group = '{$request['blood_group']}' ";
			$data .= ", volume = '{$request['volume']}' ";
			$data .= ", request_id = '{$request['id']}' ";
			$data .= ", status = '2' ";
			$chk = $this->db->query("SELECT * FROM blood_inventory where request_id= '{$request['id']}' ")->num_rows;
			if($chk> 0){
				$this->db->query("UPDATE blood_inventory set $data where request_id= '{$request['id']}' ");
			}else{
				$this->db->query("INSERT INTO blood_inventory set $data");
			}
			return 1;
		}
	}
	function delete_handover(){
		extract($_POST);
		$request_id = $this->db->query("SELECT * FROM handedover_request where id= '$id' ")->fetch_array()['request_id'];

		$delete = $this->db->query("DELETE FROM handedover_request where id = ".$id);
		if($delete){
				$this->db->query("DELETE FROM blood_inventory where request_id= '$request_id' ");
			return 1;
		}
	}
}