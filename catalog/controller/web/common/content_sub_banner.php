<?php
class ControllerWebCommonContentSubBanner extends Controller
{
    public function index()
    {
        $this->load->model('design/layout');

        if (isset($this->request->get['route'])) {
            $route = (string) $this->request->get['route'];
        } else {
            $route = 'common/home';
        }
        // print_r($route);

        $layout_id = 0;

        if ($route == 'product/category' && isset($this->request->get['path'])) {
            $this->load->model('catalog/category');

            $path = explode('_', (string) $this->request->get['path']);

            $layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
        }

        if ($route == 'product/product' && isset($this->request->get['product_id'])) {
            $this->load->model('catalog/product');

            $layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get['product_id']);
        }

        if ($route == 'information/information' && isset($this->request->get['information_id'])) {
            $this->load->model('catalog/information');

            $layout_id = $this->model_catalog_information->getInformationLayoutId($this->request->get['information_id']);
        }

        if (!$layout_id) {
            $layout_id = $this->model_design_layout->getLayout($route);
        }

        if (!$layout_id) {
            $layout_id = $this->config->get('config_layout_id');
        }

        $this->load->model('setting/module');

        $data['modules'] = array();

        $modules = $this->model_design_layout->getLayoutModules($layout_id, 'content_bottom');

        foreach ($modules as $module) {
            $part = explode('.', $module['code']);
			
            if (isset($part[1])) {
				$setting_info = $this->model_setting_module->getModule($part[1]);
				
                if ($setting_info && $setting_info['status']) {
					$output = $this->load->controller('web/extension/module/sub_' . $part[0], $setting_info);
					

                    if ($output) {
                        // $data['modules'][] = $output;
                        if ($part[0] == 'banner') {
                            $data['sub_banner'][] = $output;
                        }
                    }
                }
            }
        }

		

        return $this->load->view('web/common/content_sub_banner', $data);
    }
}
