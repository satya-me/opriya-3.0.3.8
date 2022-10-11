<?php

class ControllerWebCommonFooter extends Controller 
{
    public function index(): string
    {
        $data['assets'] = ASSET_URL;
        $data['base_url'] = HTTP_SERVER;
        return $this->load->view('web/common/footer', $data);
    }
}
