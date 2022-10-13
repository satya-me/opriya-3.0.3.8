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
        $d = json_decode($data);
        // return $d;
        
        $this->db->query("UPDATE `" . DB_PREFIX . "ship_credential` SET token = '" . $this->db->escape($d->token) . "' WHERE id = '" . 1 . "'");
    }
}
