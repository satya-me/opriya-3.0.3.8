<?php
class ModelShiprocketProduct extends Model
{
    public function VendorProductJoin()
    {

        // $sql = "SELECT * FROM oc_product
        // LEFT JOIN oc_purpletree_vendor_products ON oc_product.product_id = oc_purpletree_vendor_products.product_id
        // UNION All
        // SELECT * FROM oc_product
        // RIGHT JOIN oc_purpletree_vendor_products ON oc_product.product_id = oc_purpletree_vendor_products.product_id";

        $sql = "SELECT oc_product.product_id, oc_product.quantity, oc_purpletree_vendor_products.product_id AS s_pid, 
        oc_purpletree_vendor_products.seller_id AS s_id
        FROM oc_product
        LEFT JOIN oc_purpletree_vendor_products
        ON oc_product.product_id = oc_purpletree_vendor_products.product_id
        ORDER BY oc_product.product_id";

        return $this->db->query($sql)->rows;
    }
}
