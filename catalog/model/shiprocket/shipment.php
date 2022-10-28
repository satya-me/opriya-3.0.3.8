<?php
class ModelShiprocketShipment extends Model
{
    public function AddShipment($data)
    {
        $data = json_decode($data);
        // return $data->order_id;
        $sql = "INSERT INTO oc_ship_shipment (order_id, shipment_id, `status`, status_code, onboarding_completed_now, awb_code, courier_company_id, courier_name)
                        VALUES ('$data->order_id', '$data->shipment_id', '$data->status', '$data->status_code', '$data->onboarding_completed_now', '$data->awb_code', '$data->courier_company_id', '$data->courier_name')";

        $result = $this->db->query($sql);

        return $result;
    }
}
