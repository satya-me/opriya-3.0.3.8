<?php
class ControllerWebAccountAccount extends Controller {
	public function index() {
		$data['assets'] = ASSET_URL;
		// echo "Check";
		// exit;
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('web/account/account', '', true);

			$this->response->redirect($this->url->link('web/account/login', '', true));
		}

		$this->load->language('account/account');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('web/common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('web/account/account', '', true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		} 
		
		$data['edit'] = $this->url->link('web/account/edit', '', true);
		$data['password'] = $this->url->link('web/account/password', '', true);
		$data['address'] = $this->url->link('web/account/address', '', true);
		
		$data['credit_cards'] = array();
		
		$files = glob(DIR_APPLICATION . 'controller/extension/credit_card/*.php');
		
		foreach ($files as $file) {
			$code = basename($file, '.php');
			
			if ($this->config->get('payment_' . $code . '_status') && $this->config->get('payment_' . $code . '_card')) {
				$this->load->language('extension/credit_card/' . $code, 'extension');

				$data['credit_cards'][] = array(
					'name' => $this->language->get('extension')->get('heading_title'),
					'href' => $this->url->link('web/extension/credit_card/' . $code, '', true)
				);
			}
		}
		
		$data['wishlist'] = $this->url->link('web/account/wishlist');
		$data['order'] = $this->url->link('web/account/order', '', true);
		$data['download'] = $this->url->link('web/account/download', '', true);
		
		if ($this->config->get('total_reward_status')) {
			$data['reward'] = $this->url->link('web/account/reward', '', true);
		} else {
			$data['reward'] = '';
		}		
		
		$data['return'] = $this->url->link('web/account/return', '', true);
		$data['transaction'] = $this->url->link('web/account/transaction', '', true);
		$data['newsletter'] = $this->url->link('web/account/newsletter', '', true);
		$data['recurring'] = $this->url->link('web/account/recurring', '', true);
		
		$this->load->model('account/customer');
		
		$affiliate_info = $this->model_account_customer->getAffiliate($this->customer->getId());
		
		if (!$affiliate_info) {	
			$data['affiliate'] = $this->url->link('web/account/affiliate/add', '', true);
		} else {
			$data['affiliate'] = $this->url->link('web/account/affiliate/edit', '', true);
		}
		
		if ($affiliate_info) {		
			$data['tracking'] = $this->url->link('web/account/tracking', '', true);
		} else {
			$data['tracking'] = '';
		}
		
		$data['column_left'] = $this->load->controller('web/common/column_left');
		$data['column_right'] = $this->load->controller('web/common/column_right');
		$data['content_top'] = $this->load->controller('web/common/content_top');
		$data['content_bottom'] = $this->load->controller('web/common/content_bottom');
		$data['footer'] = $this->load->controller('web/common/footer');
        $data['header'] = $this->load->controller('web/common/header');
		
		$this->response->setOutput($this->load->view('web/account/account', $data));
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
