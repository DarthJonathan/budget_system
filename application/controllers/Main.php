<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	function __construct ()
	{
		parent::__construct();

		if($this->session->userdata('user_id') == NULL)
		{
			redirect('accounts');
		}
	}

	public function index()
	{
		$data['title'] = "Home";
		$data['month'] = date('F');

		//Get Data
		$data['gold']		= $this->budget_model->getMonthlyLimit('gold', date('F'))->limit_transaction;
		$data['diamond']	= $this->budget_model->getMonthlyLimit('diamond', date('F'))->limit_transaction;

		$data['trans_gold']		= (!$this->budget_model->getTotalTrans('gold', date('F')))? 0 : $this->budget_model->getTotalTrans('gold', date('F'));
		$data['trans_diamond']	= (!$this->budget_model->getTotalTrans('diamond', date('F')))? 0 : $this->budget_model->getTotalTrans('diamond', date('F'));

		$data['ratio_gold'] 	= $data['trans_gold']/$data['gold'];
		$data['ratio_diamond'] 	= $data['trans_diamond']/$data['diamond'];

		$this->template->load('default', 'home', $data);
	}

	public function month($month)
	{
		$data['title'] = $month . "'s Budget";
		$data['month'] = $month;

		//Get Data
		$data['gold']		= $this->budget_model->getMonthlyLimit('gold', $month)->limit_transaction;
		$data['diamond']	= $this->budget_model->getMonthlyLimit('diamond', $month)->limit_transaction;

		$data['trans_gold']		= $this->budget_model->getTotalTrans('gold', $month);
		$data['trans_diamond']	= $this->budget_model->getTotalTrans('diamond', $month);

		$data['ratio_gold'] 	= $data['trans_gold']/$data['gold'];
		$data['ratio_diamond'] 	= $data['trans_diamond']/$data['diamond'];

		$this->template->load('default', 'home', $data);
	}

	public function year_overview ()
	{
		$data['title'] = "Year Overview";

		$data['gold']		= $this->budget_model->getYearly('gold');
		$data['diamond']	= $this->budget_model->getYearly('diamond');

		$this->template->load('default', 'year_overview', $data);
	}

	public function all_transactions ($month = '')
	{
		$data['title'] = "Transactions";

		$data['gold'] 	 = $this->budget_model->getTransMonth(date('F'), 'gold');
		$data['diamond'] = $this->budget_model->getTransMonth(date('F'), 'diamond');

		$this->template->load('default', 'all_transactions', $data);
	}

	public function delete ($id)
	{
		//Get Data From Transactions
		$data = $this->db->get_where('transactions', array('id' => $id))->row();

		//Delete From Transaction
		$this->db->where('id', $id);
		$this->db->delete('transactions');

		//Delete From Monthly Limit
		$voong = $this->db->get_where('monthly_limit', array('month' => $data->month, 'year' => $data->year, 'type' => $data->type))->row()->transaction_id;
		$voong = str_replace('#' . $id, '', $voong);

		$this->db->where(array('month' => $data->month, 'year' => $data->year, 'type' => $data->type));
		$this->db->set('transaction_id', $voong);
		$this->db->update('monthly_limit');

		//Redirect
		$this->session->set_flashdata('success', 'You Have Deleted A Transaction!');
		redirect('main/all_transactions');
	}
}
