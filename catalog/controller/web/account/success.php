<?php
class ControllerWebAccountSuccess extends Controller {
	public function index() {
		$this->load->language('account/success');

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

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('web/account/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_message'), $this->url->link('web/information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_approval'), $this->config->get('config_name'), $this->url->link('web/information/contact'));
		}

		if ($this->cart->hasProducts()) {
			$data['continue'] = $this->url->link('web/checkout/cart');
		} else {
			$data['continue'] = $this->url->link('web/account/account', '', true);
		}

		$data['column_left'] = $this->load->controller('web/common/column_left');
		$data['column_right'] = $this->load->controller('web/common/column_right');
		$data['content_top'] = $this->load->controller('web/common/content_top');
		$data['content_bottom'] = $this->load->controller('web/common/content_bottom');
		$data['footer'] = $this->load->controller('web/common/footer');
		$data['header'] = $this->load->controller('web/common/header');

		$this->response->setOutput($this->load->view('web/common/success', $data));
	}
}