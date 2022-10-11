<?php
class ControllerWebCheckoutSuccess extends Controller {
	public function index() {
		$this->load->language('checkout/success');

		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('web/common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('web/checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('web/checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('web/checkout/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('web/account/account', '', true), $this->url->link('web/account/order', '', true), $this->url->link('web/account/download', '', true), $this->url->link('web/information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('web/information/contact'));
		}

		$data['continue'] = $this->url->link('web/common/home');

		$data['column_left'] = $this->load->controller('web/common/column_left');
		$data['column_right'] = $this->load->controller('web/common/column_right');
		$data['content_top'] = $this->load->controller('web/common/content_top');
		$data['content_bottom'] = $this->load->controller('web/common/content_bottom');
		$data['footer'] = $this->load->controller('web/common/footer');
		$data['header'] = $this->load->controller('web/common/header');

		$this->response->setOutput($this->load->view('web/common/success', $data));
	}
}