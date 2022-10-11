<?php
class ControllerExtensionModulePurpletreeLatestsellerProducts extends Controller {
		public function index($setting) {
			$this->load->language('extension/module/purpletree_latestseller_products');
			
			$data['heading_title'] = $this->language->get('heading_title');
			$this->document->addStylepts('catalog/view/javascript/purpletree/jquery/owl-carousel/owl.carousel.css');
			$this->document->addScriptpts('catalog/view/javascript/purpletree/jquery/owl-carousel/owl.carousel.min.js');
			$data['text_tax'] = $this->language->get('text_tax');
			
			$data['button_cart'] = $this->language->get('button_cart');
			$data['button_wishlist'] = $this->language->get('button_wishlist');
			$data['button_compare'] = $this->language->get('button_compare');
			
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
			
			$data['products'] = array();
			$this->load->model('setting/extension');
			$installed_modules = $this->model_setting_extension->getExtensions('module');
			
			// if (!$setting['limit']) {
			//$setting['limit'] = 4;
			// }
			
			//if (!empty($setting['product'])) {
			
			$this->load->model('extension/module/latestseller_products');
			
			
			$products = $this->model_extension_module_latestseller_products->getLatest();
			
			/* get active skin */
		    
			if (strpos($this->config->get('config_template'), 'journal2') === 0){				
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "journal2_config WHERE store_id = '0' AND `key` = 'active_skin'");
				$theme['journal2_config']['active_skin'] = $query->num_rows ? $query->row['value'] : 1;		
				if($theme['journal2_config']['active_skin']==1 || $theme['journal2_config']['active_skin']==15){
					$data['no_Of_product'] = 4;
					}elseif($theme['journal2_config']['active_skin']==2 || $theme['journal2_config']['active_skin']==3 || $theme['journal2_config']['active_skin']==4 || $theme['journal2_config']['active_skin']==5 || $theme['journal2_config']['active_skin']==6 || $theme['journal2_config']['active_skin']==7 || $theme['journal2_config']['active_skin']==8 || $theme['journal2_config']['active_skin']==9 ||$theme['journal2_config']['active_skin']==12){
					$data['no_Of_product'] = 5;
					}elseif($theme['journal2_config']['active_skin']==10 || $theme['journal2_config']['active_skin']==11 || $theme['journal2_config']['active_skin']==13 || $theme['journal2_config']['active_skin']==14){
					$data['no_Of_product'] = 6;
				}
				}else{
				$data['no_Of_product'] = 4;
			}
			
			if(!empty($products)) {
				$count= 0;
				foreach ($products as $product) {
					if($count < 8) {
						$count++;
						$product_info = $this->model_catalog_product->getProduct($product['product_id']);
						
						if ($product_info) {
							if ($product_info['image']) {
								$image = $this->model_tool_image->resize($product_info['image'], '200' , '200');
								// echo "<pre>"; 
								// print_r($setting);
								// die; 
								} else {
								$image = $this->model_tool_image->resize('placeholder.png', '200' , '200');
							}
							
							if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
								$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
								} else {
								$price = false;
							}
							
							if ((float)$product_info['special']) {
								$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
								} else {
								$special = false;
							}
							
							if ($this->config->get('config_tax')) {
								$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
								} else {
								$tax = false;
							}
							
							if ($this->config->get('config_review_status')) {
								$rating = $product_info['rating'];
								} else {
								$rating = false;
							}
							$imagee2 = false;
							$labelss ='';
							if (strpos($this->config->get('config_template'), 'journal2') === 0){
								$this->load->model('journal2/product');
								$labelss = (array_search('journal2', array_column($installed_modules, 'code')) !== False)?$this->model_journal2_product->getLabels($product['product_id']):'';
								$additional_imagess = $this->model_catalog_product->getProductImages($product['product_id']);               
								if (count($additional_imagess) > 0) {
									$imagee2 = $this->model_tool_image->resize($additional_imagess[0]['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
								}
							}
							$shortdescription = utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length' )) . '..';
							
							if(strlen($shortdescription) > 25){
								$shortdescription =  substr($shortdescription, 0, 25).'...';
							}
							$this->load->model('extension/purpletree_multivendor/sellerproduct');
						$this->load->language('account/ptsregister');
						$data['text_seller_label'] =$this->language->get('text_seller_label');    
						$data['button_deliver'] =$this->language->get('button_deliver');
						$this->load->model('extension/purpletree_multivendor/vendor');
						$data['show_seller_name'] = $this->config->get('module_purpletree_multivendor_show_seller_name');
						$data['multivendor_status'] = $this->config->get('module_purpletree_multivendor_status');
						$data['show_seller_address'] = $this->config->get('module_purpletree_multivendor_show_seller_address');
							$seller_detail = array();
							$pts_quick_status = '';
							$seller_detail = $this->model_extension_purpletree_multivendor_sellerproduct->getSellername($product_info['product_id']);
							$pts_quick_status = $this->model_extension_purpletree_multivendor_sellerproduct->getQucikOrderStatus($product_info['product_id']);
							$seller_detailss = array();
							if($seller_detail){
							  $seller_detailss = $this->model_extension_purpletree_multivendor_vendor->getStoreDetail($seller_detail['seller_id']);
							}
							if(!empty($seller_detailss)){
							   $cuntry_name = $this->model_extension_purpletree_multivendor_vendor->getStoreDetail($seller_detail['seller_id']);
							   $cuntry_name = $this->model_extension_purpletree_multivendor_vendor->getCountryName($seller_detailss['store_country']);
							   $state_name = $this->model_extension_purpletree_multivendor_vendor->getStateName($seller_detailss['store_state'],$seller_detailss['store_country']);
							   $store_address = $seller_detailss['store_address'].','.$seller_detailss['store_city'].','.$state_name.','.$cuntry_name;
							   $seller_link  = $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', 'seller_store_id=' . $seller_detailss['id']);
								$google_map = $seller_detailss['google_map_link'];
							}else{
							$seller_link ='';
							$store_address = '';
							$google_map = '';
							}
							$data['products'][] = array(
							'seller_name'  => isset($seller_detail['seller_name'])?$seller_detail['seller_name']:'',
							'store_address'  => $store_address,
							'google_map'  => $google_map,
							'seller_link'  => $seller_link,
							'pts_quick_status'  => $pts_quick_status,
							'quick_order'       => $this->url->link('extension/account/purpletree_multivendor/quick_order', '&product_id='.$product_info['product_id'], true),
							'product_id'  => $product_info['product_id'],
							'thumb'       => $image,
							'thumb2'       => $imagee2,
							'labels'        =>	$labelss,
							'name'        => $product_info['name'],
							'description' => $shortdescription,
							'price'       => $price,
							'special'     => $special,
							'tax'         => $tax,
							'rating'      => $rating,
							'href'        => $this->url->link('product/product', 'product_id=' .      $product_info['product_id'])
							);
						}
					}
				}
			}
			
			if ($data['products']) {			
				return $this->load->view('extension/module/purpletree_latestseller_products', $data);
			}
		}
	}
	if (! function_exists('array_column')) {
		function array_column(array $input, $columnKey, $indexKey = null) {
			$array = array();
			foreach ($input as $value) {
				if ( !array_key_exists($columnKey, $value)) {
					trigger_error("Key \"$columnKey\" does not exist in array");
					return false;
				}
				if (is_null($indexKey)) {
					$array[] = $value[$columnKey];
				}
				else {
					if ( !array_key_exists($indexKey, $value)) {
						trigger_error("Key \"$indexKey\" does not exist in array");
						return false;
					}
					if ( ! is_scalar($value[$indexKey])) {
						trigger_error("Key \"$indexKey\" does not contain scalar value");
						return false;
					}
					$array[$value[$indexKey]] = $value[$columnKey];
				}
			}
			return $array;
		}
	}