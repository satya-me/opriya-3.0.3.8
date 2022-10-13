<?php
class ControllerWebShiprocketAuth extends Controller
{
    public function index($token)
    {
        return $this->model_shiprocket_auth->shipAuthCredential($token);
    }
}
