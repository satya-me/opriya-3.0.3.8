<?php
class ControllerWebCheckoutSuccess extends Controller
{
    public function index()
    {
        // echo "<pre>";
        // print_r($this->session);
        // exit;

        // echo "<pre>";
        // print_r($order);
        // exit;

        if (isset($this->session->data['gateway_flag']) == 'online') {
            //
            // echo "<pre>";
            // print_r('online');
            // exit;
        } else {
            // exit;
            $this->load->model('shiprocket/product');
            $this->load->model('shiprocket/order');
            $this->load->model('shiprocket/seller');

            $this->load->model('shiprocket/shipment');
            $P = $this->model_shiprocket_product->VendorProductJoin();

            $order = $this->model_shiprocket_order->OrderJoin($this->session->data['order_id']);

            $order_date = $this->model_shiprocket_order->getDate($this->session->data['order_id']);

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
                    $payloads1['data']['shipping_address'] = $this->session->data['shipping_address'];
                    $payloads1['data']['payment_method'] = $this->session->data['payment_method'];
                    $payloads1['data']['pickup_location'] = $S->row['pickup_location'];
                    $payloads1['order_date'] = $order_date;
                    $payloads1['data']['comment'] = $S->row['store_name'];
                    $payloads1['data']['order_items']['order_product_id'] = $order[$i]['order_product_id'];
                    $payloads1['data']['order_id_inv'] = $this->session->data['order_id'];
                    $payloads1['data']['order_items'] = $order[$i];
                    $ty = $this->load->controller('web/shiprocket/create_order', $payloads1);
                    // $ty = json_decode($ty);

                    // Save shipment details in DB
                    $ship = $this->model_shiprocket_shipment->AddShipment($ty);
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
                    $payloads3['data']['shipping_address'] = $this->session->data['shipping_address'];
                    $payloads3['data']['payment_method'] = $this->session->data['payment_method'];
                    $payloads3['data']['pickup_location'] = $pickup_location['pickup_location'];
                    $payloads3['order_date'] = $order_date;
                    $payloads3['data']['comment'] = $S['config_name'];
                    $payloads3['data']['order_items']['order_product_id'] = $order[$i]['order_product_id'];
                    $payloads3['data']['order_id_inv'] = $this->session->data['order_id'];
                    $payloads3['data']['order_items'] = $order[$i];
                    $ty = $this->load->controller('web/shiprocket/create_order', $payloads3);

                    // Save shipment details in DB
                    $ship = $this->model_shiprocket_shipment->AddShipment($ty);

                    // echo "Admin ";
                    // print_r($ty);
                    // echo "<br>";
                }
                // echo "---------------------------------------------------------------------------------<br>";
            }

        }
        // print_r($order);
        // exit;

        $this->load->language('checkout/success');

        if (isset($this->session->data['order_id'])) {
            $this->cart->clear();

            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['guest']);
            unset($this->session->data['comment']);
            unset($this->session->data['order_id']);
            unset($this->session->data['coupon']);
            unset($this->session->data['reward']);
            unset($this->session->data['voucher']);
            unset($this->session->data['vouchers']);
            unset($this->session->data['totals']);
        }
		unset($this->session->data['gateway_flag']);

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('web/common/home'),
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_basket'),
            'href' => $this->url->link('web/checkout/cart'),
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_checkout'),
            'href' => $this->url->link('web/checkout/checkout', '', true),
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_success'),
            'href' => $this->url->link('web/checkout/success'),
        );

        if ($this->customer->isLogged()) {
            $data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('web/account/account', '', true), $this->url->link('web/account/order', '', true), $this->url->link('web/account/download', '', true), $this->url->link('web/information/contact'));
        } else {
            $data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('web/information/contact'));
        }

        $data['continue'] = $this->url->link('web/common/home');

        $data['column_left'] = $this->load->controller('web/common/column_left');
        $data['column_right'] = $this->load->controller('web/common/column_right');
        $data['content_top'] = $this->load->controller('web/common/content_top');
        $data['content_bottom'] = $this->load->controller('web/common/content_bottom');
        $data['footer'] = $this->load->controller('web/common/footer');
        $data['header'] = $this->load->controller('web/common/header');

        $this->response->setOutput($this->load->view('web/common/success', $data));
    }
}
