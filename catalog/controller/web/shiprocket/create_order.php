<?php

class ControllerWebShiprocketCreateOrder extends Controller
{
    public function index($data)
    {
        // return $data['data'];
        // return rand(10,1000).'-224-447-'.$data['data']['order_items'][0]['order_id'];
        // return '{
        //     "order_id": "000-'.$data['data']['order_id_inv'].'-'.$data['data']['order_items']['order_product_id'].'",
        //     "order_date": "'.$data['order_date'].'",
        //     "pickup_location": "'.$data['data']['pickup_location'].'",
        //     "channel_id": "",
        //     "comment": "'.$data['data']['comment'].'",
        //     "billing_customer_name": "'.$data['data']['shipping_address']['firstname'].'",
        //     "billing_last_name": "'.$data['data']['shipping_address']['lastname'].'",
        //     "billing_address": "'.$data['data']['shipping_address']['address_1'].'",
        //     "billing_address_2": "'.$data['data']['shipping_address']['address_2'].'",
        //     "billing_city": "'.$data['data']['shipping_address']['city'].'",
        //     "billing_pincode": "'.$data['data']['shipping_address']['postcode'].'",
        //     "billing_state": "'.$data['data']['shipping_address']['zone'].'",
        //     "billing_country": "'.$data['data']['shipping_address']['country'].'",
        //     "billing_email": "naruto@uzumaki.com",
        //     "billing_phone": "9876543210",
        //     "shipping_is_billing": true,
        //     "shipping_customer_name": "",
        //     "shipping_last_name": "",
        //     "shipping_address": "",
        //     "shipping_address_2": "",
        //     "shipping_city": "",
        //     "shipping_pincode": "",
        //     "shipping_country": "",
        //     "shipping_state": "",
        //     "shipping_email": "",
        //     "shipping_phone": "",
        //     "order_items": [
        //         {
        //         "name": "'.$data['data']['order_items']['name'].'",
        //         "sku": "ewrwr123",
        //         "units": '.$data['data']['order_items']['quantity'].',
        //         "selling_price": "'.$data['data']['order_items']['price'].'",
        //         "discount": "",
        //         "tax": "",
        //         "hsn": ""
        //         }
        //     ],
        //     "payment_method": "'.$data['data']['payment_method']['title'].'",
        //     "shipping_charges": 0,
        //     "giftwrap_charges": 0,
        //     "transaction_charges": 0,
        //     "total_discount": 0,
        //     "sub_total": '.$data['data']['order_items']['total'].',
        //     "length": '.$data['data']['order_items']['length'].',
        //     "breadth": '.$data['data']['order_items']['width'].',
        //     "height": '.$data['data']['order_items']['height'].',
        //     "weight": '.$data['data']['order_items']['weight'].'
        // }';
        // return $data['data']['order_items'][0]['order_product_id'];
        // return $data['data']['shipping_address'];
        // return $data['data']['payment_method'];
        // return $data['data']['order_items'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/create/adhoc',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>
            '{
                "order_id": "000-' . $data['data']['order_id_inv'] . '-' . $data['data']['order_items']['order_product_id'] . '",
                "order_date": "' . $data['order_date'] . '",
                "pickup_location": "' . $data['data']['pickup_location'] . '",
                "channel_id": "",
                "comment": "' . $data['data']['comment'] . '",
                "billing_customer_name": "' . $data['data']['shipping_address']['firstname'] . '",
                "billing_last_name": "' . $data['data']['shipping_address']['lastname'] . '",
                "billing_address": "' . $data['data']['shipping_address']['address_1'] . '",
                "billing_address_2": "' . $data['data']['shipping_address']['address_2'] . '",
                "billing_city": "' . $data['data']['shipping_address']['city'] . '",
                "billing_pincode": "' . $data['data']['shipping_address']['postcode'] . '",
                "billing_state": "' . $data['data']['shipping_address']['zone'] . '",
                "billing_country": "' . $data['data']['shipping_address']['country'] . '",
                "billing_email": "naruto@uzumaki.com",
                "billing_phone": "9876543210",
                "shipping_is_billing": true,
                "shipping_customer_name": "",
                "shipping_last_name": "",
                "shipping_address": "",
                "shipping_address_2": "",
                "shipping_city": "",
                "shipping_pincode": "",
                "shipping_country": "",
                "shipping_state": "",
                "shipping_email": "",
                "shipping_phone": "",
                "order_items": [
                    {
                    "name": "' . $data['data']['order_items']['name'] . '",
                    "sku": "ewrwr123",
                    "units": ' . $data['data']['order_items']['quantity'] . ',
                    "selling_price": "' . $data['data']['order_items']['price'] . '",
                    "discount": "",
                    "tax": "",
                    "hsn": ""
                    }
                ],
                "payment_method": "' . $data['data']['payment_method']['title'] . '",
                "shipping_charges": 0,
                "giftwrap_charges": 0,
                "transaction_charges": 0,
                "total_discount": 0,
                "sub_total": ' . $data['data']['order_items']['total'] . ',
                "length": ' . $data['data']['order_items']['length'] . ',
                "breadth": ' . $data['data']['order_items']['width'] . ',
                "height": ' . $data['data']['order_items']['height'] . ',
                "weight": ' . $data['data']['order_items']['weight'] . '
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $data['token'] . '',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}
