<?php
class ControllerWebAccountIndex extends Controller
{
    public function index()
    {
        $data['assets'] = ASSET_URL;
        $data['token'] = $token = md5(mt_rand());
        $data['login'] = $this->url->link('account/login', '_token=' . $token . '&type=.?', true);
        $data['register'] = $this->url->link('web/account/index/register', '_token=' . $token . '&type=.?', true);
        // account/login
        $this->response->setOutput($this->load->view('web/account/index', $data));
    }

    public function login()
    {

        echo "Login";
        // $data['assets'] = ASSET_URL;

        // $this->response->setOutput($this->load->view('web/account/index', $data));
    }

    public function register()
    {
        echo "Register";
    }
}
