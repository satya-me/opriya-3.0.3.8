<?php

class ControllerWebExtensionPaymentCashFree extends Controller
{
    /**
     * Initiate checkout page
     *
     * @return void
     */
    public function index()
    {
        // echo "<pre>";
        // echo "SATYAJIT";
        // print_r($this->session);
        // exit;
        $this->language->load('extension/payment/cashfree');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['text_loading'] = $this->language->get('text_loading');
        $data['text_redirect'] = $this->language->get('text_redirect');
        $this->session->data['order_id'];
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/cashfree')) {
            return $this->load->view($this->config->get('config_template') . '/template/extension/payment/cashfree', $data);
        } else {
            return $this->load->view('web/extension/payment/cashfree', $data);
        }
    }

    /**
     * Redirect to payment page for executing checkout page
     *
     * @return void
     */
    public function confirm()
    {

        if ($this->session->data['payment_method']['code'] == 'cashfree') {
            $this->language->load('extension/payment/cashfree');

            $appId = trim($this->config->get('payment_cashfree_app_id'));
            $secretKey = trim($this->config->get('payment_cashfree_secret_key'));

            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $cf_request = array();
            $cf_request["appId"] = $appId;
            $cf_request["secretKey"] = $secretKey;
            $cf_request["orderId"] = $this->session->data['order_id'];
            $cf_request["orderNote"] = $order_info['store_name'] . " - #" . $cf_request["orderId"];
            $cf_request["orderAmount"] = round($order_info['total'], 2);
            $cf_request["orderCurrency"] = $order_info['currency_code'];
            $cf_request["customerPhone"] = $order_info['telephone'];
            $cf_request["customerName"] = html_entity_decode($order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
            $cf_request["customerEmail"] = $order_info['email'];
            $cf_request["returnUrl"] = $this->url->link('web/extension/payment/cashfree/thankyou', '', 'SSL');
            $cf_request["notifyUrl"] = $this->url->link('web/extension/payment/cashfree/callback', '', 'SSL');
            $cf_request["source"] = "opencart";

            $jsonResponse = $this->getOrderLink($appId, $secretKey, $this->session->data['order_id']);
            $response['data'] = $this->session->data;

            if ($jsonResponse->{'status'} == "OK") {
                $response["status"] = 1;
                $response["redirect"] = $jsonResponse->{"paymentLink"};
            } else {
                $jsonResponse = $this->createOrder($cf_request);
                if ($jsonResponse->{'status'} == "OK") {
                    $response["status"] = 1;
                    $response["redirect"] = $jsonResponse->{"paymentLink"};
                } else {
                    $response["status"] = 0;
                    $response["message"] = $this->language->get('cashfree_api_error') . $jsonResponse->{"reason"};
                }
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($response));
        }
    }

    /**
     * Create order using cashfree order api's
     *
     * @param  mixed $cf_request
     * @return void
     */
    public function createOrder($cf_request)
    {
        $apiEndpoint = trim($this->config->get('payment_cashfree_api_url'));
        $timeout = 10;

        $request_string = "";
        foreach ($cf_request as $key => $value) {
            $request_string .= $key . '=' . rawurlencode($value) . '&';
        }

        $apiEndpoint = rtrim($apiEndpoint, "/");
        $apiEndpoint = $apiEndpoint . "/api/v1/order/create";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$apiEndpoint?");
        curl_setopt($ch, CURLOPT_POST, count($cf_request));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $curl_result = curl_exec($ch);
        curl_close($ch);
        $jsonResponse = json_decode($curl_result);
        return $jsonResponse;
    }

    /**
     * If order is already generate with same order id then get payment links
     *
     * @param  mixed $appId
     * @param  mixed $secretKey
     * @param  mixed $orderId
     * @return void
     */
    public function getOrderLink($appId, $secretKey, $orderId)
    {
        $url = trim($this->config->get('payment_cashfree_api_url'));
        $url = rtrim($url, "/");
        $url = $url . "/api/v1/order/info/link";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "appId=$appId&secretKey=$secretKey&orderId=$orderId",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
            ),
        ));

        $err = curl_error($curl);
        $curl_result = curl_exec($curl);

        curl_close($curl);
        $jsonResponse = json_decode($curl_result);
        return $jsonResponse;
    }

    /**
     * Process payment on client after cashfree response
     *
     * @param  mixed $postData
     * @return void
     */
    private function processResponse($postData)
    {
        $this->load->model('checkout/order');
        $this->language->load('extension/payment/cashfree');

        $order_info = $this->model_checkout_order->getOrder($postData["orderId"]);

        if ($order_info) {
            $cashfree_response = array();
            $cashfree_response["orderId"] = $postData["orderId"];
            $cashfree_response["orderAmount"] = $postData["orderAmount"];
            $cashfree_response["txStatus"] = $postData["txStatus"];
            $cashfree_response["referenceId"] = $postData["referenceId"];
            $cashfree_response["txTime"] = $postData["txTime"];
            $cashfree_response["txMsg"] = $postData["txMsg"];
            $cashfree_response["paymentMode"] = $postData["paymentMode"];
            $cashfree_response["signature"] = $postData["signature"];

            $secret_key = trim($this->config->get('payment_cashfree_secret_key'));
            $data = "{$cashfree_response['orderId']}{$cashfree_response['orderAmount']}{$cashfree_response['referenceId']}{$cashfree_response['txStatus']}{$cashfree_response['paymentMode']}{$cashfree_response['txMsg']}{$cashfree_response['txTime']}";

            $hash_hmac = hash_hmac('sha256', $data, $secret_key, true);
            $computedSignature = base64_encode($hash_hmac);
            if ($cashfree_response["signature"] != $computedSignature) {
                $this->model_checkout_order->addOrderHistory($cashfree_response['orderId'], 10, $cashfree_response["txMsg"] . ' Signature missmatch! Check Cashfree dashboard for details of Reference Id:' . $data['referenceId']);
                $redirectUrl = $this->url->link('web/checkout/failure', '', true);
                return array("status" => 0, "message" => $this->language->get('cashfree_signature_mismatch'), "redirectUrl" => $redirectUrl);
            }

            if ($cashfree_response["txStatus"] == 'SUCCESS') {
                if ($order_info["order_status_id"] != $this->config->get('payment_cashfree_order_status_id')) {
                    // only updated if it has been updated it
                    $this->model_checkout_order->addOrderHistory($cashfree_response['orderId'], $this->config->get('payment_cashfree_order_status_id'), "Payment Received");
                }

                // Call order API shiprocket.
                $ship_rsp = $this->shipRocket($postData["orderId"]);

                return array("status" => 1, 'ship_rsp' => $ship_rsp);
            } else if ($cashfree_response["txStatus"] == "CANCELLED") {
                $this->model_checkout_order->addOrderHistory($cashfree_response['orderId'], 7, $cashfree_response["txMsg"] . ' Payment Cancelled! Check Cashfree dashboard for details of Reference Id:' . $cashfree_response['referenceId']);
                $redirectUrl = $this->url->link('web/checkout/checkout', '', true);
                return array("status" => 0, "message" => $this->language->get('cashfree_payment_cancelled'), "redirectUrl" => $redirectUrl);
            } else {
                $this->model_checkout_order->addOrderHistory($cashfree_response['orderId'], 10, $cashfree_response["txMsg"] . ' Payment Failed! Check Cashfree dashboard for details of Reference Id:' . $cashfree_response['referenceId']);
                $redirectUrl = $this->url->link('web/checkout/failure', '', true);
                return array("status" => 0, "message" => $this->language->get('cashfree_payment_failed'), "redirectUrl" => $redirectUrl);
            }

        }
        return array("status" => 0, "message" => "");
    }

    /**
     * Redirect to thank you page after process payment on client
     *
     * @return void
     */
    public function thankyou()
    {
        // echo "<pre>";
        // print_r($this->session->data);
        // exit;
        $this->session->data['gateway_flag'] = "online";
        // $this->session->data['gateway_post'] = $_POST;

        if (!isset($_POST["orderId"])) {
            $this->response->redirect($this->url->link('web/checkout/failure'));
        }

        $response = $this->processResponse($_POST);
        if ($response["status"] == 1) {
            $this->response->redirect($this->url->link('web/checkout/success', '', true));
        } else {
            $this->session->data['error_warning'] = $response["message"];
            $this->response->redirect($response['redirectUrl']);
        }
    }

    /**
     * Checking for notify url
     *
     * @return void
     */
    public function callback()
    {
        if (!isset($_POST["orderId"])) {
            die();
        }
        sleep(20);
        $response = $this->processResponse($_POST);
        die();
        //do nothing
    }

    public function shipRocket($orderId)
    {
        $this->session->data['gateway_flag_order'] = $orderId;
        $this->load->model('shiprocket/product');
        $this->load->model('shiprocket/order');
        $this->load->model('shiprocket/seller');
        
        $this->load->model('shiprocket/shipment');
        $P = $this->model_shiprocket_product->VendorProductJoin();
        
        $order = $this->model_shiprocket_order->OrderJoin($orderId);
        
        $order_date = $this->model_shiprocket_order->getDate($orderId);
        
        
        $this->load->model('shiprocket/token');
        $token = $this->model_shiprocket_token->GetToken();
        // echo "<pre>";
        // print_r($order);
        // exit;
        for ($i = 0; $i < count($order); $i++) {

            // Seller pickup_location
            if (!empty($order[$i]['seller_id'])) {

                $S = $this->model_shiprocket_seller->GetSeller($order[$i]['seller_id']);

                // Process Order call shiprocket api;
                $payloads1['token'] = $token;
                // $payloads1['data']['shipping_address'] = $this->session->data['shipping_address'];
                $payloads1['data']['shipping_address']['firstname'] = $order[$i]['firstname'];
                $payloads1['data']['shipping_address']['lastname'] = $order[$i]['lastname'];
                $payloads1['data']['shipping_address']['address_1'] = $order[$i]['payment_address_1'];
                // $payloads1['data']['shipping_address']['address_2'] = $order[$i]['address_2'];
                $payloads1['data']['shipping_address']['city'] = $order[$i]['payment_city'];
                $payloads1['data']['shipping_address']['postcode'] = $order[$i]['payment_postcode'];
                $payloads1['data']['shipping_address']['zone'] = $order[$i]['payment_city'];
                $payloads1['data']['shipping_address']['country'] = $order[$i]['payment_country'];

                // $payloads1['data']['payment_method'] = $this->session->data['payment_method'];
                // $payloads1['data']['payment_method']['title'] = $order[$i]['payment_method'];
                $payloads1['data']['payment_method']['title'] = 'Prepaid';

                $payloads1['data']['order_items']['length'] = $order[$i]['length'];

                $payloads1['data']['pickup_location'] = $S->row['pickup_location'];
                $payloads1['order_date'] = $order_date;
                $payloads1['data']['comment'] = $S->row['store_name'];
                $payloads1['data']['order_items']['order_product_id'] = $order[$i]['order_product_id'];
                $payloads1['data']['order_id_inv'] = $orderId;
                $payloads1['data']['order_items'] = $order[$i];
                $ty[] = $this->load->controller('web/shiprocket/create_order', $payloads1);
                // $ty = json_decode($ty);

                // Save shipment details in DB
                $ship[] = $this->model_shiprocket_shipment->AddShipment($ty);
                // echo "Seller ";
                // print_r($ship);
                // echo "<br>";

            } else {

                // Admin pickup_location
                $this->load->model('shiprocket/admin');
                $S = $this->model_shiprocket_admin->getAdminSetting();
                $pickup_location = $this->model_shiprocket_admin->getPickup();
                // print_r($pickup_location['pickup_location']);
                // exit;
                $this->load->model('shiprocket/pickup');
                $payloads2['token'] = $token;
                $payloads2['data']['store_phone'] = $S['config_telephone'];
                $payloads2['data']['store_name'] = $S['config_name'];
                $payloads2['data']['email'] = $S['config_email'];
                // $payloads2['data']['store_phone'] = $S['config_telephone'];
                $payloads2['data']['store_address'] = $S['config_address'];
                $payloads2['data']['store_city'] = $this->model_shiprocket_admin->getState($S['config_zone_id']);
                // $payloads2['data']['store_name'] = $S['config_name'];
                $payloads2['data']['store_zipcode'] = $S['config_zipcode'];

                $PK['customer_id'] = 'admin';
                $PK['pickup_location'] = $payloads2['data']['store_phone'] . '-' . $payloads2['data']['store_name'];
                $pickup = $this->model_shiprocket_pickup->AddShipAdmin($PK);

                if ($pickup == "true") {
                    # code...
                    $this->load->controller('web/shiprocket/create_pickup', $payloads2);
                }

                // Process Order call shiprocket order api;
                $payloads3['token'] = $token;
                // $payloads3['data']['shipping_address'] = $this->session->data['shipping_address']; 
                $payloads3['data']['shipping_address']['firstname'] = $order[$i]['firstname'];
                $payloads3['data']['shipping_address']['lastname'] = $order[$i]['lastname'];
                $payloads3['data']['shipping_address']['address_1'] = $order[$i]['payment_address_1'];
                // $payloads3['data']['shipping_address']['address_2'] = $order[$i]['address_2'];
                $payloads3['data']['shipping_address']['city'] = $order[$i]['payment_city'];
                $payloads3['data']['shipping_address']['postcode'] = $order[$i]['payment_postcode'];
                $payloads3['data']['shipping_address']['zone'] = $order[$i]['payment_city'];
                $payloads3['data']['shipping_address']['country'] = $order[$i]['payment_country'];

                // $payloads3['data']['payment_method'] = $this->session->data['payment_method'];
                // $payloads3['data']['payment_method']['title'] = $order[$i]['payment_method'];
                $payloads3['data']['payment_method']['title'] = 'Prepaid';
                $payloads3['data']['order_items']['length'] = $order[$i]['length'];


                $payloads3['data']['pickup_location'] = $pickup_location['pickup_location'];
                $payloads3['order_date'] = $order_date;
                $payloads3['data']['comment'] = $S['config_name'];
                $payloads3['data']['order_items']['order_product_id'] = $order[$i]['order_product_id'];
                $payloads3['data']['order_id_inv'] = $orderId;
                $payloads3['data']['order_items'] = $order[$i];
                $ty[] = $this->load->controller('web/shiprocket/create_order', $payloads3);

                // Save shipment details in DB
                $ship[] = $this->model_shiprocket_shipment->AddShipment($ty);

                // echo "Admin ";
                // print_r($ty);
                // echo "<br>";
            }
            // echo "---------------------------------------------------------------------------------<br>";
        }
        $this->session->data['gateway_flag_shipping_address'] = $order;
        $this->session->data['gateway_flag_details'] = $ty;
        return $ty;
    }
}
