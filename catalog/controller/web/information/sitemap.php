<?php
class ControllerWebInformationSitemap extends Controller {
	public function index() {
		$this->load->language('information/sitemap');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('web/common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('web/information/sitemap')
		);

		$this->load->model('catalog/category');

		$data['categories'] = array();

		$categories_1 = $this->model_catalog_category->getCategories(0);

		foreach ($categories_1 as $category_1) {
			$level_2_data = array();

			$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);

			foreach ($categories_2 as $category_2) {
				$level_3_data = array();

				$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

				foreach ($categories_3 as $category_3) {
					$level_3_data[] = array(
						'name' => $category_3['name'],
						'href' => $this->url->link('web/product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'] . '_' . $category_3['category_id'])
					);
				}

				$level_2_data[] = array(
					'name'     => $category_2['name'],
					'children' => $level_3_data,
					'href'     => $this->url->link('web/product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'])
				);
			}

			$data['categories'][] = array(
				'name'     => $category_1['name'],
				'children' => $level_2_data,
				'href'     => $this->url->link('web/product/category', 'path=' . $category_1['category_id'])
			);
		}

		$data['special'] = $this->url->link('web/product/special');
		$data['account'] = $this->url->link('web/account/account', '', true);
		$data['edit'] = $this->url->link('web/account/edit', '', true);
		$data['password'] = $this->url->link('web/account/password', '', true);
		$data['address'] = $this->url->link('web/account/address', '', true);
		$data['history'] = $this->url->link('web/account/order', '', true);
		$data['download'] = $this->url->link('web/account/download', '', true);
		$data['cart'] = $this->url->link('web/checkout/cart');
		$data['checkout'] = $this->url->link('web/checkout/checkout', '', true);
		$data['search'] = $this->url->link('web/product/search');
		$data['contact'] = $this->url->link('web/information/contact');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			$data['informations'][] = array(
				'title' => $result['title'],
				'href'  => $this->url->link('web/information/information', 'information_id=' . $result['information_id'])
			);
		}

		$data['column_left'] = $this->load->controller('web/common/column_left');
		$data['column_right'] = $this->load->controller('web/common/column_right');
		$data['content_top'] = $this->load->controller('web/common/content_top');
		$data['content_bottom'] = $this->load->controller('web/common/content_bottom');
		$data['footer'] = $this->load->controller('web/common/footer');
		$data['header'] = $this->load->controller('web/common/header');

		$this->response->setOutput($this->load->view('web/information/sitemap', $data));
	}
}