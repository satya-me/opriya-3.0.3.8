<?php

class ControllerWebCommonHeader extends Controller
{
    public function index(): string
    {

        $data['assets'] = ASSET_URL;
        $this->load->model('setting/extension');

        $data['analytics'] = array();

        $analytics = $this->model_setting_extension->getExtensions('analytics');

        foreach ($analytics as $analytic) {
            if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
                $data['analytics'][] = $this->load->controller('web/extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
            }
        }

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
            $this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
        }

        $data['title'] = $this->document->getTitle();

        $data['base'] = $server;
        $data['description'] = $this->document->getDescription();
        $data['keywords'] = $this->document->getKeywords();
        $data['links'] = $this->document->getLinks();
        $data['styles'] = $this->document->getStyles();
        $data['scripts'] = $this->document->getScripts('header');
        $data['lang'] = $this->language->get('code');
        $data['direction'] = $this->language->get('direction');

        $data['name'] = $this->config->get('config_name');

        if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
            $data['logo'] = $server . 'image/' . $this->config->get('config_logo');
        } else {
            $data['logo'] = '';
        }

        $this->load->language('common/header');

        // Wishlist
        if ($this->customer->isLogged()) {
            $this->load->model('account/wishlist');

            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
        } else {
            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
        }

        $data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('web/account/account', '', true), $this->customer->getFirstName(), $this->url->link('web/account/logout', '', true));

        $data['home'] = $this->url->link('web/common/home');
        $data['wishlist'] = $this->url->link('web/account/wishlist', '', true);
        $data['logged'] = $this->customer->isLogged();
        $data['account'] = $this->url->link('web/account/account', '', true);
        $data['register'] = $this->url->link('web/account/register', '', true);
        $data['login'] = $this->url->link('web/account/login', '', true);
        $data['order'] = $this->url->link('web/account/order', '', true);
        $data['transaction'] = $this->url->link('web/account/transaction', '', true);
        $data['download'] = $this->url->link('web/account/download', '', true);
        $data['logout'] = $this->url->link('web/account/logout', '', true);
        $data['shopping_cart'] = $this->url->link('web/checkout/cart');
        $data['checkout'] = $this->url->link('web/checkout/checkout', '', true);
        $data['contact'] = $this->url->link('web/information/contact');
        $data['telephone'] = $this->config->get('config_telephone');

        $data['language'] = $this->load->controller('web/common/language');
        $data['currency'] = $this->load->controller('web/common/currency');
        $data['search'] = $this->load->controller('web/common/search');
        $data['cart'] = $this->load->controller('web/common/cart');

        $menu = $this->load->controller('web/common/menu');
        $data['menu'] = $menu['categories'];




        return $this->load->view('web/common/header', $data);
    }
}
