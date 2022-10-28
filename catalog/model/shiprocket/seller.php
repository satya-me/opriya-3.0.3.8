<?php
class ModelShiprocketSeller extends Model
{
    public function GetSeller($id)
    {
        // $sql = "SELECT * FROM " . DB_PREFIX . "purpletree_vendor_stores WHERE seller_id = $id";
        $sql = "SELECT * FROM " . DB_PREFIX . "purpletree_vendor_stores
        LEFT JOIN " . DB_PREFIX . "ship_pickup_tetails ON " . DB_PREFIX . "purpletree_vendor_stores.seller_id = " . DB_PREFIX . "ship_pickup_tetails.customer_id
        WHERE " . DB_PREFIX . "purpletree_vendor_stores.seller_id = $id";

        $result = $this->db->query($sql);

        return $result;
    }
}
