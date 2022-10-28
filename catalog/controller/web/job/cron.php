<?php

class ControllerWebJobCron extends Controller
{
    public function index(): string
    {
        $this->load->model('shiprocket/auth');
        $token = ShipLoginAuth($this->model_shiprocket_auth->shipGetAuthCredential());

        $data['auth'] = $this->load->controller('web/shiprocket/auth', $token);
        print_r($data['auth']);
        exit;
    }
}
