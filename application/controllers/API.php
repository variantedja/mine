<?php
	require APPPATH.'libraries/REST_Controller.php';
	date_default_timezone_set('Asia/Jakarta');
		 
	class API extends REST_Controller {
		public function __construct() {
		   parent::__construct();
		   $this->load->database();
		}
		
		public function index_get($id = 0, $limit = null, $offset = null) {
			if ($id != 0) {
				if ($dt = $this->__Get_User_By_ID($id))
					$dt['addresses'] = $this->__Get_Addresses_By_User_ID($id);
			} else {
				if (isset($offset))
					$offset = ($offset - 1) * $limit;
				
				if ($dt = $this->db->get('users', $limit, $offset)->result()) {
					foreach ($dt as $d) 
						$d->addresses = $this->__Get_Addresses_By_User_ID($d->id);
				}
			}
			
			if (isset($dt) && !empty($dt))
				echo json_encode($dt);
			else
				echo json_encode(['failed' => 'Data user tidak ditemukan.']);
		}
		
		public function address_get($id = 0, $user_id = 0) {
			if ($id != 0)
				$dt = $this->__Get_Address_By_ID($id);
			elseif ($user_id != 0)
				$dt = $this->__Get_Addresses_By_User_ID($user_id);
			else
				$dt = $this->db->get('addresses')->result();
			
			if (isset($dt) && !empty($dt))
				echo json_encode($dt);
			else
				echo json_encode(['failed' => 'Alamat user tidak ditemukan.']);
		}
		 
		public function index_post() {
			$post = $this->post();
			
			$this->db->insert('users', [
				'username' => trim($post['username'])
				, 'email' => trim($post['email'])
				, 'password' => $post['password']
			]);
			$this->db->insert('addresses', [
				'user_id' => $this->db->insert_id()
				, 'detail' => trim($post['detail'])
				, 'preferred' => 1
			]);
			echo json_encode(['success' => 'Berhasil menambahkan data dan alamat user.']);
		}
		
		public function address_post() {
			$post = $this->post();
			
			if ($post['preferred'] == 1)
				$this->__Set_All_Preferred_Address_To_Zero($post['user_id']);
			
			$this->db->insert('addresses', [
				'user_id' => $post['user_id']
				, 'detail' => $post['detail']
				, 'preferred' => $post['preferred']
			]);
			echo json_encode(['success' => 'Berhasil menambahkan alamat user.']);
		}
		
		public function index_put($id) {
			if ($id != 0) {
				$put = $this->put();
				
				if ($this->db->update('users', $put, ['id' => $id]))
					echo json_encode(['success' => 'Berhasil mengubah data user.']);
				else
					echo json_encode(['failed' => 'Tidak dapat mengubah data user.']);
			} else {
				echo json_encode(['failed' => 'Tidak dapat mengubah data user.']);
			}
		}
		
		public function address_put($id) {
			if ($id != 0) {
				$ad = $this->__Get_Address_By_ID($id);
				$user_id = $ad['user_id'];
				$put = $this->put();
				$put['updated_at'] = date('Y-m-d H:i:s');
				
				if ($put['preferred'] == 1)
					$this->__Set_All_Preferred_Address_To_Zero($user_id);

				$this->db->update('addresses', $put, ['id' => $id]);
				echo json_encode(['success' => 'Berhasil mengubah alamat user.']);
			} else {
				echo json_encode(['failed' => 'Tidak dapat mengubah alamat user.']);
			}
		}
		
		public function index_delete($id) {
			if ($id != 0) {
				$this->db->delete('addresses', ['user_id' => $id]);
				$this->db->delete('users', ['id' => $id]);
				echo json_encode(['success' => 'Berhasil menghapus data dan alamat user.']);
			} else {
				echo json_encode(['failed' => 'Tidak dapat menghapus data dan alamat user.']);
			}
		}
		
		public function address_delete($id) {
			if ($id != 0) {
				$this->db->delete('addresses', ['id' => $id]);
				echo json_encode(['success' => 'Berhasil menghapus alamat user.']);
			} else {
				echo json_encode(['failed' => 'Tidak dapat menghapus alamat user.']);
			}
		}
		
		private function __Get_User_By_ID($id) {
			return $this->db->get_where('users', ['id' => $id])->row_array();
		}
		
		private function __Get_Address_By_ID($id) {
			return $this->db->get_where('addresses', ['id' => $id])->row_array();
		}
		
		private function __Get_Addresses_By_User_ID($user_id) {
			return $this->db->get_where('addresses', ['user_id' => $user_id])->result();
		}
		
		private function __Set_All_Preferred_Address_To_Zero($user_id) {
			$this->db->update('addresses', ['preferred' => 0], ['user_id' => $user_id]);
		}
	}