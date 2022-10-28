<?php
class ControllerWebShiprocketCreatePickup extends Controller
{
    public function index($data)
    {

        // return;
        // return gettype((int)$data['data']['store_zipcode']);
        $pickup_location = $data['data']['store_phone'].'-'.$data['data']['store_name'];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/settings/company/addpickup',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "pickup_location": "'.$pickup_location.'",
                "name": "'.$data['data']['store_name'].'",
                "email": "'.$data['data']['email'].'",
                "phone": "'.$data['data']['store_phone'].'",
                "address": "'.$data['data']['store_address'].'",
                "address_2": "",
                "city": "'.$data['data']['store_city'].'",
                "state":"'.$data['data']['store_name'].'",
                "country": "India",
                "pin_code": "'.(int)$data['data']['store_zipcode'].'"

            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $data['token'] . '',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }
}
