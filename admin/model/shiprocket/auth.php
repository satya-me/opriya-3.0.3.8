<?php
class ModelShiprocketAuth extends Model
{
    public function shipGetAuthCredential()
    {
        
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ship_credential`");

		return $query->row;
    }

    public function shipAuthCredential($data)
    {
        // echo "<pre>";
        // print_r($data['email']);
        // exit;
        $this->db->query("UPDATE `" . DB_PREFIX . "ship_credential` SET email = '" . $this->db->escape($data['email']) . "', `password` = '" . $this->db->escape($data['password']) . "' WHERE id = '" . 1 . "'");
    }
}
