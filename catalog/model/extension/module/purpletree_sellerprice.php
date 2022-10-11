<?php
class ModelExtensionModulePurpletreeSellerprice extends Model {
		public function getTemplatePrice($product_id) {
			
			$template_price = array();
			$query = $this->db->query("SELECT pvtp.*,pvs.id AS storeidd, pvs.store_name,pvs.vacation,pvs.store_logo,p.minimum,p.tax_class_id FROM " . DB_PREFIX . "purpletree_vendor_template pvt LEFT JOIN " . DB_PREFIX . "purpletree_vendor_template_products pvtp ON (pvtp.template_id = pvt.id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON (pvs.seller_id  = pvtp.seller_id)  LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id  = pvt.product_id ) WHERE pvt.product_id ='". (int)$product_id ."' AND pvtp.quantity != 0 AND pvs.store_status = 1 AND pvtp.status = 1 AND pvtp.quantity > 0 ORDER BY price");
			if($query->num_rows) {
				$template_price = $query->rows;
			}
			return $template_price;
			
		}
		public function getTemplateProductfromproandseller($product_id,$seller_id) {
			
			$template_price = array();
			$query = $this->db->query("SELECT pvtp.*,pvs.id AS storeidd, pvs.store_name,pvs.store_logo,p.minimum FROM " . DB_PREFIX . "purpletree_vendor_template pvt LEFT JOIN " . DB_PREFIX . "purpletree_vendor_template_products pvtp ON (pvtp.template_id = pvt.id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON (pvs.seller_id  = pvtp.seller_id)  LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id  = pvt.product_id ) WHERE pvt.product_id ='". (int)$product_id ."' AND pvtp.quantity != 0 AND pvs.store_status = 1 AND pvtp.status = 1 AND pvtp.quantity > 0 AND pvs.seller_id = ".(int)$seller_id." ORDER BY price");
			if($query->num_rows) {
				$template_price = $query->rows;
			}
			return $template_price;
			
		}
		public function getStoreRating($seller_id){
			$query = $this->db->query("SELECT AVG(rating) as rating FROM " . DB_PREFIX . "purpletree_vendor_reviews where seller_id='".(int)$seller_id."' AND status=1");
			return $query->row['rating'];
		}		
		public function getTemplateProduct($store_id){
			$query = $this->db->query("SELECT p.*,(SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating,pd.*,pvt.product_id,pvtp.seller_id,pvtp.price AS t_price,pvtp.t_description,pvtp.stock_status_id as t_stock_status_id FROM " . DB_PREFIX . "purpletree_vendor_template pvt INNER JOIN " . DB_PREFIX . "purpletree_vendor_template_products pvtp ON (pvt.id = pvtp.template_id) INNER JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON (pvs.seller_id = pvtp.seller_id) INNER JOIN " . DB_PREFIX . "product p ON (p.product_id = pvt.product_id) INNER JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = pvt.product_id) WHERE pvs.id='".(int)$store_id."' AND pvt.status=1 AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pvtp.status = 1");			
			if($query->num_rows) {
				return $query->rows;
			}
		}	
		public function getTemlateDescription($product_id,$seller_id){
			$query = $this->db->query("SELECT pvtp.t_description AS t_description FROM " . DB_PREFIX . "purpletree_vendor_template_products pvtp INNER JOIN " . DB_PREFIX . "purpletree_vendor_template pvt ON (pvt.id = pvtp.template_id) WHERE pvt.product_id='".(int)$product_id."' AND pvtp.seller_id = '".(int)$seller_id."'");
			if($query->num_rows) {
				return $query->row['t_description'];
			}
		}
}