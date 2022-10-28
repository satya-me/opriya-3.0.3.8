<?php
class ModelShiprocketAdmin extends Model
{
    public function getAdminSetting()
    {
        $data = array();

        $sql = "SELECT * FROM " . DB_PREFIX . "setting WHERE code = 'config'";
        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            if (!$result['serialized']) {
                if ($result['key'] == 'config_file_ext_allowed' || $result['key'] == 'config_file_mime_allowed' || $result['key'] == 'config_robots') {

                } else {
                    $data[$result['key']] = $result['value'];
                }
            } else {
                $data[$result['key']] = json_decode($result['value'], true);
            }
        }

        return $data;
    }

    public function getState($id)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "zone WHERE zone_id = $id";
        $query = $this->db->query($sql)->row;
        return $query['name'];
    }

    public function getCountry()
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "zone WHERE zone_id = $id";
    }

    public function getPickup()
    {
        $sql = "SELECT * FROM oc_ship_pickup_admin_tetails";
        $query = $this->db->query($sql);

        return $query->row;

    }
}
