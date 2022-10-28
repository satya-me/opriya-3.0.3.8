<?php
class ModelShiprocketToken extends Model
{
    public function GetToken()
    {

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ship_credential`");

        return $query->row['token'];
    }

}
