<?php

class ControllerExtensionModuleRestApi extends Controller
{

	private $allowed_methods = ['GET', 'POST', 'PUT', 'DELETE'];
	private $errors          = [];
	private $json            = [];
	private $filters         = [];
	private $methods         = ['getOrders' => 'GET', 'getProducts' => 'GET', 'getOrderStatus' => 'GET', 'update' => 'POST', 'updateInventory' => 'POST', 'updatePrice' => 'POST', 'validate' => 'GET', 'getProduct' => 'GET'];

	public function __construct($registry)
	{
		parent::__construct($registry);
	}

	public function index()
	{

		$return = $this->authorize();

		if ($return) {

			$this->load->model('extension/module/rest_api');

			$this->dispatchAPI();

			if (count($this->errors) > 0) {
				$this->sendErrorResponse();
			} else {
				$this->sendJsonResponse();
			}
		}
		else
		{
			if (count($this->errors) > 0) {
				$this->sendErrorResponse();
			} else {
				$this->sendJsonResponse();
			}
		}
	}

	private function dispatchAPI()
	{
		$method = $this->request->server['REQUEST_METHOD'];

		if (!in_array($method, $this->allowed_methods)) {
			$this->response->addHeader('HTTP/1.1 404 Not Found');
			$this->response->setOutput("Request Method Not Allowed !");
		} else {
			$action = $this->request->get['action'];

			if (!isset($action) || empty($action)) {

				$this->response->addHeader('HTTP/1.1 400 Bad Request');
				$this->errors[] = "API action not specified";
			} else {

				$action_method = (array_key_exists($action, $this->methods)) ? $this->methods[$action] : '';

				if (!method_exists($this, $action) && !is_callable([$this, $action])) {
					$this->response->addHeader('HTTP/1.1 400 Bad Request');
					$this->errors[] = "Invalid Function called..";
				} elseif ($action_method != $method) {

					$this->response->addHeader('HTTP/1.1 405 Method Not Allowed');
					$this->errors[] = "Method Not Allowed";
				} else {

					if (isset($this->request->get['filters'])) {

						if (is_array($this->request->get['filters'])) {
							$this->filters = $this->request->get['filters'];
						} elseif (is_array(explode(",", $this->request->get['filters']))) {
							$this->filters = explode(",", $this->request->get['filters']);
						}
					}

					$this->{$action}();
				}
			}
		}
	}

	private function getOrders()
	{
		$this->load->model('account/order');

		$orders = $this->model_extension_module_rest_api->setFilters($this->filters)
														->getOrders();
		foreach ($orders as $key => $order) {

			$products = $this->model_extension_module_rest_api->getOrderProducts($order['order_id']);
			$totals   = $this->model_account_order->getOrderTotals($order['order_id']);

			$orders[$key]['gift']     = 0;
			$orders[$key]['discount'] = 0;

			foreach ($totals as $total) {
				if ($total['code'] == 'shipping') {
					$orders[$key]['shipping_charges'] = (float) $total['value'];
				}

				if ($total['code'] == 'sub_total') {
					$orders[$key]['sub_total'] = (float) $total['value'];
				}

				if ($total['code'] == 'coupon') {
					$orders[$key]['discount'] = (float) abs($total['value']);
				}

				if ($total['code'] == 'voucher') {
					$orders[$key]['gift'] = (float) abs($total['value']);
				}
			}

			foreach ($products as $k => $product) {
				$products[$k]['options'] = $this->model_account_order->getOrderOptions($order['order_id'], $product['order_product_id']);
			}

			$attrib = $this->getProductAttribute($products);

			$orders[$key]['dimension'] = $attrib['dimensions'];
			$orders[$key]['weight']    = $attrib['weight'];
			$orders[$key]['products']  = $products;
		}

		$this->json = $orders;
	}

	private function getOrderStatus()
	{
		$this->loadAdminmodel('localisation/order_status');

		$status = $this->model_localisation_order_status->getOrderStatuses();

		$this->json['order_status'] = $status;
	}

	private function getProducts()
	{
		$products = $this->model_extension_module_rest_api->setFilters($this->filters)
														  ->getProducts();
		$currency = $this->config->get('config_currency');

		$this->loadAdminmodel('catalog/product');

		foreach ($products as $key => $product) {
			$products[$key]['options'] = $this->model_catalog_product->getProductOptions($product['product_id']);

			if ($this->request->server['HTTPS']) {
				$products[$key]['image'] = $this->config->get('config_ssl') . 'image/' . $product['image'];
			} else {
				$products[$key]['image'] = $this->config->get('config_url') . 'image/' . $product['image'];
			}

			$products[$key]['currency'] = $currency;
			$products[$key]['url']      = $this->url->link('product/product', 'product_id=' . (int) $product['product_id']);
		}

		$this->json = $products;
	}

	private function getProduct()
	{
		if ($this->has('product_id')) {

			$currency = $this->config->get('config_currency');

			$this->loadAdminmodel('catalog/product');

			$product = $this->model_extension_module_rest_api->getProduct($this->get('product_id'));

			$product['options'] = $this->model_catalog_product->getProductOptions($product['product_id']);

			if ($this->request->server['HTTPS']) {
				$product['image'] = $this->config->get('config_ssl') . 'image/' . $product['image'];
			} else {
				$product['image'] = $this->config->get('config_url') . 'image/' . $product['image'];
			}

			$product['currency'] = $currency;
			$product['url']      = $this->url->link('product/product', 'product_id=' . (int) $product['product_id']);

			$this->json = $product;
		} else {
			$this->errors[] = "Product ID Missing..!";
		}
	}

	public function update()
	{
		$function = ($this->has('function')) ? $this->get('function', true) : '';

		switch ($function) {
			case 'status':
				$this->updateStatus();
				break;

			case 'inventory':
				$this->updateInventory();
				break;

			case 'price':
				$this->updatePrice();
				break;

			default:
				$this->errors[] = 'Update Method Not defined..';
		}
	}

	public function updateStatus()
	{
		$this->load->model('checkout/order');
		if ($this->request()
				 ->has('order_id') && $this->request()
										   ->has('status_id')
		) {
			$comment = $this->getComment($this->get('status_id'));

			$this->model_checkout_order->addOrderHistory($this->request()
															  ->get('order_id'), $this->request()
																					  ->get('status_id'), $comment, true, true);
		} else {
			$this->errors[] = "Order ID or Status Missing..";
		}
	}

	public function updateInventory()
	{
		$data = print_r($this->filters, 1);

		if (isset($this->filter->product_id) && isset($this->filter->quantity)) {
			if (isset($this->request->get['type'])) {
				$this->changeProduct($this->filter->quantity, $this->request->get['type']);
			} else {
				$this->changeProduct($this->filter->quantity, 'update');
			}

			$this->json[] = ['Product Quantity Updated'];
		} else {
			$this->errors[] = 'Error: Mandatory fields missing';
		}
	}

	public function updatePrice()
	{
		if (isset($this->filter->sku) && isset($this->request->get['price'])) {
			$this->model_extension_module_rest_api->updateProductPrice($this->filter->sku, $this->request->get['price']);

			$this->json[] = ['Product Price Updated'];
		} else {
			$this->errors[] = 'Error: Mandatory Product fields missing';
		}
	}

	private function authorize()
	{
		try {
			$publicHash = @$this->request->server['HTTP_X_PUBLIC'];

			if (isset($publicHash) && !empty($publicHash)) {

				$contentHash = $this->request->server['HTTP_X_HASH'];

				$sql = "SELECT * FROM " . DB_PREFIX . "rest_api_token WHERE public_key = '" . $publicHash . "'";

				$result = $this->db->query($sql);

				if ($result->num_rows > 0) {
					$privateHash = $result->row['private_key'];

					$hash = hash_hmac('sha256', $publicHash, $privateHash);
				} else {
					$hash = '';
				}


				if ($hash == $contentHash) {
					return true;
				} else {

					$this->response->addHeader('HTTP/1.1 401 Not Authorized');
					$this->errors[] = "UnAuthorized Access.. !";
				}
			} else {
				$this->response->addHeader('HTTP/1.1 301 MOVED');
				$this->errors[] = "UnAuthorized Access.. Public Hash Missing !";
			}
		} catch (Exception $e) {
			$this->errors[] = $e->getMessage();
		}
	}

	public function sendErrorResponse()
	{
		$response = ['status' => 400, 'error' => json_encode($this->errors)];

		$this->errors = [];

		$this->response->setOutput(json_encode($response));
	}

	public function sendJsonResponse()
	{
		$response = ['status' => 200, 'data' => json_encode($this->json)];

		$this->json = [];

		$this->response->setOutput(json_encode($response));
	}

	public function loadAdminmodel($model)
	{

		$admin_dir = DIR_SYSTEM;
		$admin_dir = str_replace('system/', 'admin/', $admin_dir);
		$file      = $admin_dir . 'model/' . $model . '.php';
		//$file  = DIR_APPLICATION . 'model/' . $model . '.php';
		$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);

		if (file_exists($file)) {
			include_once($file);

			$this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
		} else {
			trigger_error('Error: Could not load model ' . $model . '!');
			exit();
		}
	}

	public function __get($name)
	{
		if ($name == 'filter') {
			$object = (object) $this->filters;

			return $object;
		} else {
			return parent::__get($name);
		}
	}

	public function get($name, $value = true)
	{
		if (isset($this->filter->{$name})) {
			if ($value) {
				return $this->filter->{$name};
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	public function request()
	{
		return $this;
	}

	public function has($name)
	{
		return $this->get($name, false);
	}

	public function getParam($name)
	{
		$request = (object) $this->request->get;

		if (isset($request->{$name})) {
			return $request->{$name};
		} else {
			return false;
		}
	}

	public function changeProduct($quantity, $type)
	{
		if (isset($quantity) && isset($type)) {

			unset($this->filters['quantity']);
			unset($this->filters['function']);

			$model = $this->model_extension_module_rest_api;

			switch ($type) {
				case 'add':
					$model->setFilters($this->filters)
						  ->addProductQuantity($quantity);
					break;

				case 'update':
					$model->setFilters($this->filters)
						  ->updateProductQuantity($quantity);
					break;

				case 'remove':
					$model->setFilters($this->filters)
						  ->removeProductQuantity($quantity);
					break;

				default:
					$this->errors[] = 'Update Inventory type is missing';
					break;
			}
		} else {
			$this->errors[] = 'Product required information missing.';
		}
	}

	public function validate()
	{
		return true;
	}

	public function getComment($status_id)
	{
		if ($this->has('comment')) {
			$cmt = " :: " . $this->get('comment');
		} else {
			$cmt = "";
		}

		if (empty($status_id)) {
			return '';
		} else {
			switch ($status_id) {
				case 3:
					$comment = "Order Shipped";
					break;

				case 5:
					$comment = "Order Delivered";
					break;

				case 7:
					$comment = "Order Cancelled";
					break;

				default:
					$comment = '';
			}

			return $comment . $cmt;
		}
	}

	private function getProductAttribute($products)
	{
		$length = 0;
		$width  = 0;
		$height = 0;
		$weight = 0;

		foreach ($products as $product) {
			$product = $this->model_extension_module_rest_api->getProduct($product['product_id']);
			$length += round($product['length'], 2);
			$width += round($product['width'], 2);
			$height += round($product['height'], 2);
			$weight += round($product['weight'], 2);
		}

		$data['dimensions'] = $length . "x" . $width . "x" . $height;
		$data['weight']     = $weight;

		return $data;
	}
}

?>