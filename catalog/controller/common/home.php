<?php
class ControllerCommonHome extends Controller
{
    public function index()
    {
        // $this->document->setTitle($this->config->get('config_meta_title'));
        // $this->document->setDescription($this->config->get('config_meta_description'));
        // $this->document->setKeywords($this->config->get('config_meta_keyword'));

        // if (isset($this->request->get['route'])) {
        //     $this->document->addLink($this->config->get('config_url'), 'canonical');
        // }

        // $data['column_left'] = $this->load->controller('common/column_left');
        // $data['column_right'] = $this->load->controller('common/column_right');
        // $data['content_top'] = $this->load->controller('common/content_top');
        // $data['content_bottom'] = $this->load->controller('common/content_bottom');
        // $data['footer'] = $this->load->controller('common/footer');
        // $data['header'] = $this->load->controller('common/header');

        // $this->response->setOutput($this->load->view('common/home', $data));

        // New code

        $data['assets'] = ASSET_URL;
        $this->document->setTitle($this->config->get('config_meta_title'));
        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setKeywords($this->config->get('config_meta_keyword'));

        if (isset($this->request->get['route'])) {
            $this->document->addLink($this->config->get('config_url'), 'canonical');
        }

        $data['column_left'] = $this->load->controller('web/common/column_left');
        $data['column_right'] = $this->load->controller('web/common/column_right');
        $data['content_top'] = $this->load->controller('web/common/content_top');
        $data['content_bottom'] = $this->load->controller('web/common/content_bottom');
        $data['content_sub_banner'] = $this->load->controller('web/common/content_sub_banner');
        $data['footer'] = $this->load->controller('web/common/footer');
        $data['header'] = $this->load->controller('web/common/header');

        $this->load->model('setting/module');

        $data['modules'] = array();
        $layout_id = 14;
        $modules = $this->model_design_layout->getLayoutModules($layout_id, 'content_top');

        foreach ($modules as $module) {
            $part = explode('.', $module['code']);

            if (isset($part[0]) && $this->config->get('module_' . $part[0] . '_status')) {
                $module_data = $this->load->controller('web/extension/module/' . $part[0]);

                if ($module_data) {
                    if ($part[0] == 'banner') {
                        $data['banner'][] = $module_data;
                    }
                }
            }

            if (isset($part[1])) {
                $setting_info = $this->model_setting_module->getModule($part[1]);

                if ($setting_info && $setting_info['status']) {
                    $output = $this->load->controller('web/extension/module/' . $part[0], $setting_info);

                    if ($output) {
                        if ($part[0] == 'banner') {
                            $data['banner'][] = $output;
                        }
                    }
                }
            }
        }
        // print_r($data['banner']);

        $this->response->setOutput($this->load->view('web/common/home', $data));
    }
}
