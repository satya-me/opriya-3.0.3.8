<?php

class ModelExtensionModuleRestApi extends Model
{
	const CANCELLED = 1;

	private $where;
	private $_orders_table   = "order";

	public function getOrders()
	{
		try {
			$sql = "SELECT * FROM " . DB_PREFIX .$this->_orders_table . $this->where." ORDER BY order_id DESC";

			return $this->db->query($sql)->rows;
		} catch (Exception $E) {
			return $E->getMessage();
		}
	}

	public function getProducts()
	{
		try {
			if (!is_object($this->model_catalog_product)) {
				$this->loadAdminmodel('catalog/product');
			}

			return $this->model_catalog_product->getProducts();
		} catch (Exception $E) {
			return $E->getMessage();
		}
	}

	public function getProduct($product_id)
	{
		try {

			if (!is_object($this->model_catalog_product)) {
				$this->loadAdminmodel('catalog/product');
			}

			return $this->model_catalog_product->getProduct($product_id);
		} catch (Exception $E) {
			return $E->getMessage();
		}
	}

	public function setFilters($filters = [])
	{
		$this->where = $this->getWhereCondition($filters);

		return $this;
	}

	public function getWhereCondition($filters)
	{
		$where = '';
		$index = 0;

		if (!empty($filters) && is_array($filters)) {
			$where = ' WHERE ';

			foreach ($filters as $field => $value) {
				$condition = explode(":", $field);

				if (isset($condition[1])) {
					$field = $condition[0];
				}

				if ($index > 0) {
					if (!isset($condition[1]) || strtolower($condition[1]) != 'or') {
						$where .= ' AND ';
					} else {
						$where .= ' OR ';
					}
				}

				if (isset($condition[1]) && strtolower($condition[1]) == 'in') {
					if (is_array($value)) {
						$value = implode(",", $value);
					}

					$where .= ' ' . $field . ' IN (' . $value . ') ';
				} elseif ($field == 'from') {
					$date = date_create_from_format('U', $value);
					//$date = $date->setTimezone(new DateTimeZone('Asia/Kolkata'));

					$where .= " date_added  >= '" . $date->format('Y-m-d H:i:s') . "'";
				} elseif ($field == 'to') {
					$date = date_create_from_format('U', $value);
					//$date = $date->setTimezone(new DateTimeZone('Asia/Kolkata'));

					$where .= " date_added  <= '" . $date->format('Y-m-d H:i:s') . "'";
				} elseif (isset($condition[1]) && $condition[1] == 'not') {
					$where .= ' ' . $field . ' != ' . $value . ' ';
				} else {
					$where .= ' ' . $field . ' = ' . $value . ' ';
				}

				$index++;
			}
		}

		return $where;
	}

	public function updateProductQuantity($quantity)
	{
		$sql = "UPDATE " . DB_PREFIX . "product SET quantity = " . $quantity . $this->where;
		file_put_contents("sql.txt", $sql);
		$this->db->query($sql);
	}

	public function addProductQuantity($quantity)
	{
		$sql = "UPDATE " . DB_PREFIX . "product SET quantity = (quantity+" . $quantity . ')' . $this->where;

		$this->db->query($sql);
	}

	public function removeProductQuantity($quantity)
	{
		$sql = "UPDATE " . DB_PREFIX . "product SET quantity = (quantity-" . $quantity . ')' . $this->where;

		$this->db->query($sql);
	}


	public function updateProductPrice($price)
	{
		echo $sql = "UPDATE " . DB_PREFIX . "product SET price = " . $price . $this->where;

		$this->db->query($sql);
	}

	private function loadAdminmodel($model)
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

	public function getOrderProducts($order_id)
	{
		$this->load->model('catalog/product');

		$products = $this->model_account_order->getOrderProducts($order_id);

		return $products;

		$sql = "SELECT * FROM " . DB_PREFIX . "order_product op LEFT JOIN " . DB_PREFIX . "product p ON op.product_id = p.product_id LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE op.order_id = '" . (int) $order_id . "'";

		$query = $this->db->query($sql);

		return $query->rows;
	}
}

?>
