<?php
class ControllerModuleDownloads extends Controller {
    public function index() {
        $this->language->load('module/downloads');
        $this->load->model('module/downloads');
        $this->load->model('tool/image');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('module/downloads'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['button_download'] = $this->language->get('button_download');

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $this->data['downloads'] = array();

        $categories_info = $this->model_module_downloads->getCategories();

        $category_ids = array();
        foreach ($categories_info as $category_info) {
            $category_ids[] = $category_info['category_id'];
        }

        $downloads_info = $this->model_module_downloads->getDownloads($category_ids);

        $downloads = $categories = array();
        foreach ($downloads_info as $download_info) {
            if ($download_info['image']) {
                $image = $this->model_tool_image->resize($download_info['image'], 100, 100);
            } else {
                $image = $this->model_tool_image->resize('no_image.jpg', 100, 100);
            }

            $i = 0;
            $size = $download_info['filesize'];
            while (($size / 1024) > 1) {
                $size = $size / 1024;
                $i++;
            }
            $suffix = explode(', ', $this->language->get('text_suffix_size'));
            $filesize = round(substr($size, 0, strpos($size, '.') + 4), 2) . ' ' . $suffix[$i];

            $downloads[$download_info['category_id']][] = array(
                'download_id'   => $download_info['download_id'],
                'image'         => $image,
                'filesize'      => $filesize,
                'name'          => $download_info['name'],
                'description'   => strip_tags(html_entity_decode($download_info['description'], ENT_QUOTES, 'UTF-8')),
                'href'          => $this->url->link('module/downloads/download', 'file_id=' . $download_info['download_id'], 'SSL')
            );
        }

        foreach ($categories_info as $category_info) {
            $this->data['downloads_cats'][$category_info['category_id']] = array(
                'category_id'       => $category_info['category_id'],
                'cat_name'          => $category_info['name'],
                'cat_description'   => strip_tags(html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8')),
                'cat_files'         => $downloads[$category_info['category_id']],
            );
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/downloads.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/downloads.tpl';
        } else {
            $this->template = 'default/template/module/downloads.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    public function download() {
        $this->load->model('module/downloads');

        if (isset($this->request->get['file_id'])) {
            $download_id = $this->request->get['file_id'];
        } else {
            $download_id = 0;
        }

        $download_info = $this->model_module_downloads->getDownload($download_id);

        if ($download_info) {
            $file = DIR_DOWNLOAD . $download_info['filename'];
            $mask = basename($download_info['mask']);

            if (!headers_sent()) {
                if (file_exists($file)) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file));

                    if (ob_get_level()) ob_end_clean();

                    readfile($file, 'rb');

                    exit;
                } else {
                    exit('Error: Could not find file ' . $file . '!');
                }
            } else {
                exit('Error: Headers already sent out!');
            }
        } else {
            $this->redirect($this->url->link('module/downloads', '', 'SSL'));
        }
    }
}
?>