<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends CI_Controller
{
	public function index ()
	{
		if($this->session->userdata('user_id') == NULL)
		{
			$this->login();
		}
	}

	public function login ()
	{
		$data['title'] = "User Login";

		if($this->input->post())
		{
			$username = $this->input->post('username');
			$password = $this->input->post('password');

			//Check for username and password validness
			if($userdata = $this->db->get_where('users', array('username' => $username, 'password' => $password))->row())
			{
				//Set The Session with no one logged
				if($this->session->userdata('user_id') == NULL)
				{
					$session = array(

						'user_id'					=> $userdata->id,
						'username'					=> $userdata->username,
						'fullname'					=> $userdata->fullname

						);

				$this->session->set_userdata($session);
				$this->session->set_flashdata('success', "You have successfully logged in!");
				redirect('main');
				}
			}
			//Username Is not found
			else
			{
				$this->session->set_flashdata('failed', "Username or Password doesn't match!");
				redirect('accounts/login');
			}
		}

		//If not post
		if($this->session->userdata('user_id') == NULL)
		{
			//If there is no one logged in
			$this->template->load('default_login', 'accounts/login_form', $data);
		}else
		{
			redirect('main');
		}
	}

	public function logout ()
	{
		$this->session->sess_destroy();
		redirect('main');
	}
}

?>