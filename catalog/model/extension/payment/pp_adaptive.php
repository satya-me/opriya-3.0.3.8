<?php
class ModelExtensionPaymentPPAdaptive extends Model {
		public function getMethod($address, $total) {
			$this->load->language('extension/payment/pp_adaptive');
			$cartData=array();
			$cartData= $this->cart->getProducts();
			$code='pp_adaptive';
			if(!empty($cartData)){
				foreach($cartData as $k=>$v){
					$sellerPayplaEmail=$this->getSellerPayPalId($v['product_id'],$v['cart_id']);
					if(isset($sellerPayplaEmail) && $sellerPayplaEmail == ''){
						$code='';
						break;
					}
				}
			}
			//echo $code;
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_pp_adaptive_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
			
			if ($this->config->get('payment_pp_adaptive_total') > $total) {
				$status = false;
				} elseif (!$this->config->get('payment_pp_adaptive_geo_zone_id')) {
				$status = true;
				} elseif ($query->num_rows) {
				$status = true;
				} else {
				$status = false;
			}
			
			$currencies = array(
			'AUD',
			'CAD',
			'EUR',
			'GBP',
			'JPY',
			'USD',
			'NZD',
			'CHF',
			'HKD',
			'SGD',
			'SEK',
			'DKK',
			'PLN',
			'NOK',
			'HUF',
			'CZK',
			'ILS',
			'MXN',
			'MYR',
			'BRL',
			'PHP',
			'TWD',
			'THB',
			'TRY',
			'RUB',
			'INR'
			);
			
			if (!in_array(strtoupper($this->session->data['currency']), $currencies)) {
				$status = false;
			}
			
			$method_data = array();
			if ($status) {
			if($code != '') {
				$method_data = array(
				'code'       => $code,
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('payment_pp_adaptive_sort_order')
				);
			}
			}
			
			return $method_data;
		}
		public function getSellerDetail($seller_id) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_stores where seller_id = '".(int)$seller_id."'");
			if($query->num_rows>0){
				return $query->row;
				} else {
				return NULL;
			}		
		}
		public function getorderCurrency($order_id) {
			$query = $this->db->query("SELECT currency_code FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$order_id . "'");
			
			if($query->num_rows) {
				return $query->row['currency_code'];
			}
		}
		public function getOrderProducts($order_id) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
			
			return $query->rows;
		}
		public function savelinkid($total_price,$total_commission,$total_pay_amount){
			$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_commission_invoice SET total_amount='".(float)$total_price."',total_commission='".(float)$total_commission."', total_pay_amount='".(float)$total_pay_amount."', created_at ='".date('Y-m-d')."'");
			return $this->db->getLastId();
		}
		public function saveCommisionInvoice($data=array(),$link_id = NULL){
			$total_commission=0;
			$total_commission=(float)$data['commission_fixed']+(float)$data['commission_shipping']+(float)$data['commission'];
			$this->db->query( "INSERT INTO " . DB_PREFIX . "purpletree_vendor_commission_invoice_items SET order_id ='".(int)$data['order_id']."', product_id='".(int)$data['product_id']."', seller_id='".(int)$data['seller_id']."', commission_fixed='".(float)$data['commission_fixed']."', commission_percent='".(float)$data['commission_percent']."', commission_shipping='".(float)$data['commission_shipping']."', total_commission ='".(float)$total_commission."', link_id ='".(int)$link_id."'");
			$this->db->query("UPDATE `" . DB_PREFIX . "purpletree_vendor_commissions` SET invoice_status=1 WHERE id='".(int)$data['id']."'"); 		
		} 
		
		
		public function totalPrice($seller_id,$order_id){
			
			$query= $this->db->query("SELECT SUM(total_price) AS total_price FROM " . DB_PREFIX . "purpletree_vendor_orders WHERE order_id='".(int)$order_id."' AND seller_id='".(int)$seller_id."'");
			if($query->num_rows>0){
				return $query->row;
				} else {
				return NULL;
			}
		}
		public function getSellerId($paypal_email){
			$query=$this->db->query("SELECT seller_id FROM " . DB_PREFIX . "purpletree_vendor_stores WHERE seller_paypal_id='".$paypal_email."'");
			if($query->num_rows>0){
				return $query->row['seller_id'];
				} else {
				return NULL;
			} 	 
		} 	 
		
		public function saveTranDetail($data=array()){
			$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_payments SET invoice_id ='".(int)$data['invoice_id']."', seller_id='".(int)$data['seller_id']."', transaction_id='".$this->db->escape($data['transaction_id'])."', amount='".(float)$data['amount']."', payment_mode='".$this->db->escape($data['payment_mode'])."', status='".$this->db->escape($data['status'])."',created_at=NOW(),updated_at=NOW() ");
		} 
		public function saveTranHistory($data=array()){
			$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_payment_settlement_history SET invoice_id ='".(int)$data['invoice_id']."', transaction_id='".$this->db->escape($data['transaction_id'])."', comment='".$this->db->escape($data['comment'])."', payment_mode='".$this->db->escape($data['payment_mode'])."', status_id='".$this->db->escape($data['status_id'])."',created_date=NOW(),modified_date=NOW()");
		} 		
		
		public function getSellerPayPalId($product_id,$cart_id){
			//$query= $this->db->query("SELECT seller_paypal_id FROM " . DB_PREFIX . "purpletree_vendor_stores WHERE seller_id IN (SELECT seller_id FROM " . DB_PREFIX . "purpletree_vendor_products WHERE product_id='".(int)$product_id."')");
			//
			$seller_id = $this->db->query("SELECT pvp.seller_id,pvs.seller_paypal_id FROM " . DB_PREFIX . "purpletree_vendor_products pvp JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON(pvs.seller_id=pvp.seller_id) JOIN " . DB_PREFIX . "product p ON(p.product_id=pvp.product_id) WHERE pvp.product_id='".(int)$product_id."' AND pvp.is_approved=1")->row;
					if($this->config->get('module_purpletree_multivendor_seller_product_template')){
					if(empty($seller_id['seller_id'])) {
						$sseller_id = $this->cart->getvendorcart($cart_id);
						$seller_id = $this->db->query("SELECT pvs.seller_paypal_id FROM " . DB_PREFIX . "purpletree_vendor_template_products pvtp JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON(pvs.seller_id=pvtp.seller_id) JOIN " . DB_PREFIX . "purpletree_vendor_template pvt ON(pvt.id=pvtp.template_id) JOIN " . DB_PREFIX . "product p ON(p.product_id=pvt.product_id) WHERE pvt.product_id='".(int)$product_id."' AND pvs.seller_id='".$sseller_id."'")->row;
					}
					}
			//
		/* 	echo "q<pre>";
			print_r($seller_id);
			echo "r</pre>"; */
			//die;
			if(!empty($seller_id['seller_id'])) {
				return $seller_id['seller_paypal_id'];
				}else {
				return NULL;
			}
		} 
	public function getSellerStoreName($seller_id){
			$query= $this->db->query("SELECT store_name FROM " . DB_PREFIX . "purpletree_vendor_stores WHERE seller_id ='".(int)$seller_id."')");
			
			if($query->num_rows>0){
				return $query->row['store_name'];
				}else {
				return NULL;
			}
		} 
	public function totalCommission($seller_id,$order_id){
			$query= $this->db->query("SELECT SUM(commission) AS commission FROM " . DB_PREFIX . "purpletree_vendor_commissions WHERE order_id='".(int)$order_id."' AND seller_id='".(int)$seller_id."'");
			if($query->num_rows>0){
				return $query->row;
				} else {
				return NULL;
			}
		}
	
	
	public function getCommissionData($order_id){
			
			$query= $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_commissions WHERE order_id='".(int)$order_id."'");
			if($query->num_rows>0){
				return $query->rows;
				}else {
				return NULL;
			}
		}
	public function getPayout_batch_id($order_id){
		$query = $this->db->query("SELECT payment_key FROM " . DB_PREFIX . "purpletree_vendor_adaptive_paykey WHERE order_id = '" . (int)$order_id . "'");
			if($query->num_rows>0){
				return $query->row['payment_key'];
			}
		}		
}