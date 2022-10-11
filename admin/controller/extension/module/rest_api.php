<?php

class ControllerExtensionModuleRestApi extends Controller
{
	private $error = [];

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->load->model('extension/module/rest_api');
	}

	public function index()
	{
		$this->load->language('extension/module/rest_api');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->getList();
	}

	public function add()
	{
		$this->load->language('extension/module/rest_api');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_rest_api->addApi($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/rest_api', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit()
	{
		$this->load->language('extension/module/rest_api');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_rest_api->updateApi($this->request->post);

			$this->session->data['success'] = $this->language->get('text_edit_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/rest_api', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete()
	{
		if (empty($this->request->post['selected'])) {
			$this->session->data['success'] = $this->language->get('text_invalid_api_id');

			$this->response->redirect($this->url->link('extension/module/rest_api', 'user_token=' . $this->session->data['user_token'] . $url, true));
		} else {
			$this->model_extension_module_rest_api->deleteApis( $this->request->post['selected'] );

			$this->session->data['success'] = $this->language->get('text_api_deleted');

			$this->response->redirect($this->url->link('extension/module/rest_api', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
	}

	public function install()
	{
		$test = $this->model_extension_module_rest_api->createTokenTable();
	}

	public function uninstall()
	{
		$this->model_extension_module_rest_api->deleteTokenTable();
	}

	protected function getList()
	{
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'title';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => 'Extensions',
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/rest_api', 'user_token=' . $this->session->data['user_token'] . $url, true)
		];

		$data['add']    = $this->url->link('extension/module/rest_api/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/module/rest_api/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['apis'] = [];

		$filter_data = [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		];

		$api_total = $this->model_extension_module_rest_api->getTotalApis();

		$apis = $this->model_extension_module_rest_api->getApis($filter_data);


		foreach ($apis as $api) {
			$data['apis'][] = [
				'id'         => $api['id'],
				'title'      => $api['title'],
				'public'     => $api['public_key'],
				'date_added' => $api['created_at'],
				'edit'       => $this->url->link('extension/module/rest_api/edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $api['id'] . $url, true)
			];
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list']       = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm']    = $this->language->get('text_confirm');

		$data['column_title']      = $this->language->get('column_title');
		$data['column_public']     = $this->language->get('column_public');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action']     = $this->language->get('column_action');

		$data['button_add']    = $this->language->get('button_add');
		$data['button_edit']   = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array) $this->request->post['selected'];
		} else {
			$data['selected'] = [];
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_username']   = $this->url->link('extension/module/rest_api', 'user_token=' . $this->session->data['user_token'] . '&sort=username' . $url, true);
		$data['sort_status']     = $this->url->link('extension/module/rest_api', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/module/rest_api', 'user_token=' . $this->session->data['user_token'] . '&sort=created_at' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination        = new Pagination();
		$pagination->total = $api_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('extension/module/rest_api', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($api_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($api_total - $this->config->get('config_limit_admin'))) ? $api_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $api_total, ceil($api_total / $this->config->get('config_limit_admin')));

		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');


		$this->response->setOutput($this->load->view('extension/module/rest_api', $data));
	}

	protected function getForm()
	{
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form']     = !isset($this->request->get['id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled']  = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_title']   = $this->language->get('entry_title');
		$data['entry_public']  = $this->language->get('entry_public_key');
		$data['entry_private'] = $this->language->get('entry_private_key');

		$data['button_save']   = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = '';
		}

		if (isset($this->error['public_key'])) {
			$data['error_public'] = $this->error['public_key'];
		} else {
			$data['error_public'] = '';
		}

		if (isset($this->error['private_key'])) {
			$data['error_private'] = $this->error['private_key'];
		} else {
			$data['error_private'] = '';
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => 'Extensions',
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/rest_api', 'user_token=' . $this->session->data['user_token'] . $url, true)
		];

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('extension/module/rest_api/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/module/rest_api/edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $this->request->get['id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/module/rest_api', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$api_info = $this->model_extension_module_rest_api->getApi($this->request->get['id']);
		}

		if (isset($this->request->post['title'])) {
			$data['title'] = $this->request->post['title'];
		} elseif (!empty($api_info)) {
			$data['title'] = $api_info['title'];
		} else {
			$data['title'] = '';
		}

		if (isset($this->request->post['public'])) {
			$data['public'] = $this->request->post['public'];
		} elseif (!empty($api_info)) {
			$data['public'] = $api_info['public_key'];
		} else {
			$data['public'] = token(64);
		}

		if (isset($this->request->post['private'])) {
			$data['private'] = $this->request->post['private'];
		} elseif (!empty($api_info)) {
			$data['private'] = $api_info['private_key'];
		} else {
			$data['private'] = token(64);
		}

		if (isset($this->request->post['id'])) {
			$data['public'] = $this->request->post['id'];
		} elseif (!empty($api_info)) {
			$data['id'] = $api_info['id'];
		} else {
			$data['id'] = '';
		}


		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/rest_api_edit', $data));
	}

	protected function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'extension/module/rest_api')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['title']) < 3) || (utf8_strlen($this->request->post['title']) > 20)) {
			$this->error['title'] = $this->language->get('error_title');
		}

		if (empty($this->request->post['public'])) {
			$this->error['public_key'] = $this->language->get('error_public_key');
		}

		if (empty($this->request->post['private'])) {
			$this->error['private_key'] = $this->language->get('error_private_key');
		}

		return !$this->error;
	}
}