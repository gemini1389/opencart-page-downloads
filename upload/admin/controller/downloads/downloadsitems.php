<?php 
class ControllerDownloadsDownloadsitems extends Controller { 
	private $error = array();

	public function index() {
		$this->load->language('downloads/downloadsitems');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('downloads/downloadsitems');
		$this->getList();
	}

	public function insert() {
		$this->load->language('downloads/downloadsitems');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('downloads/downloadsitems');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$downloadsitem_id = $this->model_downloads_downloadsitems->addDownloadsitem($this->request->post);

			$this->session->data['success'] = $this->language->get('text_insert_success');
			if($this->request->post['act_mode']) {
				$this->redirect($this->url->link('downloads/downloadsitems', 'token=' . $this->session->data['token'], 'SSL'));
			} else {
				$this->redirect($this->url->link('downloads/downloadsitems/update', 'downloadsitem_id='.$downloadsitem_id.'&token=' . $this->session->data['token'], 'SSL'));
			}
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('downloads/downloadsitems');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('downloads/downloadsitems');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$downloadsitem_id = $this->request->get['downloadsitem_id'];
            $downloadsitem_old_file = $this->model_downloads_downloadsitems->getDownloadsitemsFile($downloadsitem_id);
			$this->model_downloads_downloadsitems->editDownloadsitem($downloadsitem_id, $this->request->post);

            if (file_exists(DIR_DOWNLOAD . $downloadsitem_old_file)) {
                if (md5_file(DIR_DOWNLOAD . $downloadsitem_old_file) != md5_file(DIR_DOWNLOAD . $this->request->post['filename'])) {
                    $this->deleteDownloadsitemsFile($downloadsitem_old_file);
                }
            }

			$this->session->data['success'] = $this->language->get('text_update_success');
			if($this->request->post['act_mode']) {
				$this->redirect($this->url->link('downloads/downloadsitems', 'token=' . $this->session->data['token'], 'SSL'));
			} else {
				$this->redirect($this->url->link('downloads/downloadsitems/update', 'downloadsitem_id='.$downloadsitem_id.'&token=' . $this->session->data['token'], 'SSL'));
			}
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('downloads/downloadsitems');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('downloads/downloadsitems');
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
            $downloadsitem_selected = $this->model_downloads_downloadsitems->getDownloadsitemsFiles(implode(',', $this->request->post['selected']));
            foreach ($downloadsitem_selected as $downloadsitem) {
                $this->model_downloads_downloadsitems->deleteDownloadsitem($downloadsitem['download_id']);
                $this->deleteDownloadsitemsFile($downloadsitem['filename']);
            }

			$this->session->data['success'] = $this->language->get('text_delete_success');

			$this->redirect($this->url->link('downloads/downloadsitems', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getList();
	}

	private function getList() {
		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('downloads/downloadsitems', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['insert'] = $this->url->link('downloads/downloadsitems/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('downloads/downloadsitems/delete', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['downloadsitems'] = array();

		$results = $this->model_downloads_downloadsitems->getDownloadsitems();

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('downloads/downloadsitems/update', 'token=' . $this->session->data['token'] . '&downloadsitem_id=' . $result['download_id'], 'SSL')
			);

			$this->data['downloadsitems'][] = array(
				'download_id' => $result['download_id'],
				'name'        => $result['name'],
				'sort_order'  => $result['sort_order'],
				'status'    => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'selected'    => isset($this->request->post['selected']) && in_array($result['download_id'], $this->request->post['selected']),
				'action'      => $action
			);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_status'] = $this->language->get('column_status');
		$this->data['column_sort_order'] = $this->language->get('column_sort_order');
		$this->data['column_action'] = $this->language->get('column_action');
		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->template = 'downloads/downloadsitem_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function getForm() {
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_none'] = $this->language->get('text_none');
        $this->data['text_browse'] = $this->language->get('text_browse');
        $this->data['text_clear'] = $this->language->get('text_clear');
		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
    	$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['entry_name'] = $this->language->get('entry_name');
		$this->data['entry_description'] = $this->language->get('entry_description');
		$this->data['entry_category'] = $this->language->get('entry_category');
		$this->data['entry_filename'] = $this->language->get('entry_filename');
        $this->data['entry_mask'] = $this->language->get('entry_mask');
		$this->data['entry_image'] = $this->language->get('entry_image');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['button_upload'] = $this->language->get('button_upload');
		$this->data['button_save_and_close'] = $this->language->get('button_save_and_close');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
    	$this->data['tab_general'] = $this->language->get('tab_general');
    	$this->data['tab_data'] = $this->language->get('tab_data');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = array();
		}

        if (isset($this->error['filename'])) {
            $this->data['error_filename'] = $this->error['filename'];
        } else {
            $this->data['error_filename'] = '';
        }

        if (isset($this->error['mask'])) {
            $this->data['error_mask'] = $this->error['mask'];
        } else {
            $this->data['error_mask'] = '';
        }

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('downloads/downloadsitems', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		if (!isset($this->request->get['downloadsitem_id'])) {
			$this->data['action'] = $this->url->link('downloads/downloadsitems/insert', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('downloads/downloadsitems/update', 'token=' . $this->session->data['token'] . '&downloadsitem_id=' . $this->request->get['downloadsitem_id'], 'SSL');
		}

		$this->data['cancel'] = $this->url->link('downloads/downloadsitems', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->get['downloadsitem_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$downloadsitem_info = $this->model_downloads_downloadsitems->getDownloadsitem($this->request->get['downloadsitem_id']);
    	}

		$this->data['token'] = $this->session->data['token'];

		$this->load->model('localisation/language');

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['description'])) {
			$this->data['downloadsitem_name'] = $this->request->post['description'];
		} elseif (isset($this->request->get['downloadsitem_id'])) {
			$this->data['description'] = $this->model_downloads_downloadsitems->getDownloadsDescriptions($this->request->get['downloadsitem_id']);
		} else {
			$this->data['description'] = array();
		}

		$downloadsitems = $this->model_downloads_downloadsitems->getDownloadsItems();

		// Remove own id from list
		if (!empty($downloadsitem_info)) {
			foreach ($downloadsitems as $key => $downloadsitem) {
				if ($downloadsitem['download_id'] == $downloadsitem_info['download_id']) {
					unset($downloadsitems[$key]);
				}
			}
		}

		$this->data['downloadsitems'] = $downloadsitems;

		if (isset($this->request->post['category_id'])) {
			$this->data['category_id'] = $this->request->post['category_id'];
		} elseif (!empty($downloadsitem_info)) {
			$this->data['category_id'] = $downloadsitem_info['category_id'];
		} else {
			$this->data['category_id'] = 0;
		}

        $results = $this->model_downloads_downloadsitems->getCategories();
        foreach ($results as $result) {
            $this->data['categories'][] = array(
                'category_id' => $result['category_id'],
                'name'        => $result['name'],
            );
        }

		if (isset($this->request->post['image'])) {
			$this->data['image'] = $this->request->post['image'];
		} elseif (!empty($downloadsitem_info)) {
			$this->data['image'] = $downloadsitem_info['image'];
		} else {
			$this->data['image'] = '';
		}

        $this->load->model('tool/image');

        if (isset($this->request->post['image']) && file_exists(DIR_IMAGE . $this->request->post['image'])) {
            $this->data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($downloadsitem_info) && $downloadsitem_info['image'] && file_exists(DIR_IMAGE . $downloadsitem_info['image'])) {
            $this->data['thumb'] = $this->model_tool_image->resize($downloadsitem_info['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
        }

        $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

        if (isset($this->request->post['filename'])) {
            $this->data['filename'] = $this->request->post['filename'];
        } elseif (!empty($downloadsitem_info)) {
            $this->data['filename'] = $downloadsitem_info['filename'];
        } else {
            $this->data['filename'] = '';
        }

        if (isset($this->request->post['filesize'])) {
            $this->data['filesize'] = $this->request->post['filesize'];
        } elseif (!empty($downloadsitem_info)) {
            $this->data['filesize'] = $downloadsitem_info['filesize'];
        } else {
            $this->data['filesize'] = '';
        }

        if (isset($this->request->post['mask'])) {
            $this->data['mask'] = $this->request->post['mask'];
        } elseif (!empty($downloadsitem_info)) {
            $this->data['mask'] = $downloadsitem_info['mask'];
        } else {
            $this->data['mask'] = '';
        }

		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($downloadsitem_info)) {
			$this->data['sort_order'] = $downloadsitem_info['sort_order'];
		} else {
			$this->data['sort_order'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($downloadsitem_info)) {
			$this->data['status'] = $downloadsitem_info['status'];
		} else {
			$this->data['status'] = 1;
		}

		$this->load->model('design/layout');

		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'downloads/downloadsitem_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'downloads/downloadsitems')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

        if ((utf8_strlen($this->request->post['filename']) < 3) || (utf8_strlen($this->request->post['filename']) > 128)) {
            $this->error['filename'] = $this->language->get('error_filename');
        }

        if (!file_exists(DIR_DOWNLOAD . $this->request->post['filename']) && !is_file(DIR_DOWNLOAD . $this->request->post['filename'])) {
            $this->error['filename'] = $this->language->get('error_exists');
        }

        if ((utf8_strlen($this->request->post['mask']) < 3) || (utf8_strlen($this->request->post['mask']) > 128)) {
            $this->error['mask'] = $this->language->get('error_mask');
        }

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'downloads/downloadsitems')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

    public function upload() {
        $this->load->language('downloads/downloadsitems');
        $json = array();

        if (!$this->user->hasPermission('modify', 'downloads/downloadsitems')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!isset($json['error'])) {
            if (!empty($this->request->files['file']['name'])) {
                $filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));

                if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 128)) {
                    $json['error'] = $this->language->get('error_filename');
                }

                // Allowed file extension types
                $allowed = array();

                $filetypes = explode("\n", $this->config->get('config_file_extension_allowed'));

                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

                if (!in_array(substr(strrchr($filename, '.'), 1), $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Allowed file mime types
                $allowed = array();

                $filetypes = explode("\n", $this->config->get('config_file_mime_allowed'));

                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

                if (!in_array($this->request->files['file']['type'], $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }

                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }
            } else {
                $json['error'] = $this->language->get('error_upload');
            }
        }

        if (!isset($json['error'])) {
            if (is_uploaded_file($this->request->files['file']['tmp_name']) && file_exists($this->request->files['file']['tmp_name'])) {
                $ext = md5(mt_rand());

                $json['filename'] = $filename . '.' . $ext;
                $json['mask'] = $filename;
                $json['filesize'] = $this->request->files['file']['size'];

                move_uploaded_file($this->request->files['file']['tmp_name'], DIR_DOWNLOAD . $filename . '.' . $ext);
            }

            $json['success'] = $this->language->get('text_upload');
        }

        $this->response->setOutput(json_encode($json));
    }

    private function deleteDownloadsitemsFile($downloadsitem_filename) {
        if (file_exists(DIR_DOWNLOAD . $downloadsitem_filename)) {
            unlink(DIR_DOWNLOAD . $downloadsitem_filename);
        }
    }
}
?>