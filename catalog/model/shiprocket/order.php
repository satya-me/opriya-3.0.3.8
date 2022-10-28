<?php
class ModelShiprocketOrder extends Model
{
    public function OrderJoin($id)
    {
        // return $sql = "SELECT * FROM " . DB_PREFIX . "order_product LEFT JOIN " . DB_PREFIX . "purpletree_vendor_orders
        // ON " . DB_PREFIX . "order_product.product_id = " . DB_PREFIX . "purpletree_vendor_orders.product_id AND
        // " . DB_PREFIX . "order_product.order_id = " . DB_PREFIX . "purpletree_vendor_orders.order_id
        // WHERE " . DB_PREFIX . "order_product.order_id = $id";

        // $sql = "SELECT oc_order_product.order_product_id AS order_product_id,oc_order_product.order_id AS order_id,
        // oc_order_product.product_id AS product_id,oc_order_product.name AS name,
        // oc_order_product.quantity AS quantity,oc_order_product.price AS price,
        // oc_order_product.total AS total,oc_purpletree_vendor_orders.seller_id AS seller_id
        // FROM oc_order_product
        // LEFT JOIN oc_purpletree_vendor_orders ON oc_order_product.product_id = oc_purpletree_vendor_orders.product_id AND
        //  oc_order_product.order_id = oc_purpletree_vendor_orders.order_id
        // WHERE oc_order_product.order_id = $id";

        // $sql = "SELECT oc_order_product.order_product_id AS order_product_id,oc_order_product.order_id AS order_id,
        // oc_order_product.product_id AS product_id,oc_order_product.name AS name,
        // oc_order_product.quantity AS quantity,oc_order_product.price AS price,
        // oc_order_product.total AS total,oc_purpletree_vendor_orders.seller_id AS seller_id,
        // oc_product.weight AS weight,
        // oc_product.length AS length,oc_product.width AS width,oc_product.height AS height

        // FROM oc_order_product
        // LEFT JOIN oc_purpletree_vendor_orders ON oc_order_product.product_id = oc_purpletree_vendor_orders.product_id AND
        //  oc_order_product.order_id = oc_purpletree_vendor_orders.order_id

        // LEFT JOIN oc_product ON oc_order_product.product_id = oc_product.product_id
        // WHERE oc_order_product.order_id = $id";

        $sql = "SELECT oc_order_product.order_product_id AS order_product_id,oc_order_product.order_id AS order_id,
        oc_order_product.product_id AS product_id,oc_order_product.name AS name,
        oc_order_product.quantity AS quantity,oc_order_product.price AS price,
        oc_order_product.total AS total,oc_purpletree_vendor_orders.seller_id AS seller_id,
        oc_product.weight AS weight,
        oc_product.length AS length,oc_product.width AS width,oc_product.height AS height,
        oc_order.firstname AS firstname,oc_order.lastname AS lastname,oc_order.email AS email,
        oc_order.telephone AS telephone,oc_order.payment_address_1 AS payment_address_1,
        oc_order.payment_address_2 AS payment_address_2,oc_order.payment_city AS payment_city,
        oc_order.payment_country AS payment_country,oc_order.payment_postcode AS payment_postcode,
        oc_order.payment_method AS payment_method,oc_order.payment_code AS payment_code
        FROM oc_order_product
        LEFT JOIN oc_purpletree_vendor_orders ON oc_order_product.product_id = oc_purpletree_vendor_orders.product_id AND
         oc_order_product.order_id = oc_purpletree_vendor_orders.order_id
        LEFT JOIN oc_product ON oc_order_product.product_id = oc_product.product_id
        LEFT JOIN oc_order ON oc_order_product.order_id = oc_order.order_id
        WHERE oc_order_product.order_id = $id";

        $result = $this->db->query($sql);

        return $result->rows;
    }

    public function getDate($id)
    {
        $sql = "SELECT * FROM oc_order WHERE order_id = $id";
        $result = $this->db->query($sql);
        return $result->row['date_added'];
    }
}
