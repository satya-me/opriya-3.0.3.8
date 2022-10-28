<?php
class ModelShiprocketPickup extends Model
{
    public function AddShip($data)
    {
        $sql = "INSERT INTO " . DB_PREFIX . "ship_pickup_tetails
        (customer_id, pickup_location) VALUES (" . $data['customer_id'] . ", '" . $data['pickup_location'] . "')";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function AddShipAdmin($data){
        // return $data;
        
        $sql2 = "SELECT * FROM " . DB_PREFIX . "ship_pickup_admin_tetails";
        
        $query2 = $this->db->query($sql2);

        if ($query2->num_rows >= 1) {
            // do nothing
            return "false";
        } else {
            // do insert
            $sql = "INSERT INTO " . DB_PREFIX . "ship_pickup_admin_tetails (user_id, pickup_location)
            VALUES ('" . $data['customer_id'] . "', '" . $data['pickup_location'] . "')";
            $query = $this->db->query($sql);

            return "true;";
        }
        
    }

}
