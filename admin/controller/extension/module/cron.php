<?php
/**
 * Cron logic copied from Opencart 3.1
 * Converted to extension by opencart-templates.co.uk
 */
class ControllerExtensionModuleCron extends Controller {
	private $error = array();
	
	public function index() {
		$this->load->language('extension/module/cron');

		if (!$this->config->get('module_cron_status')) {
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/cron');

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/module/cron');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/cron');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_extension_module_cron->addCron($this->request->post['code'], $this->request->post['cycle'], $this->request->post['action'], $this->request->post['status']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/cron', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/module/cron');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/cron');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_extension_module_cron->editCron($this->request->get['cron_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/cron', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/module/cron');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/cron');

		if (isset($this->request->post['selected']) && $this->validate()) {
			foreach ($this->request->post['selected'] as $cron_id) {
				$this->model_extension_module_cron->deleteCron($cron_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/cron', 'user_token=' . $this->session->data['user_token'] . $url));
		}

		$this->getList();
	}

	public function run() {
		$this->load->language('extension/module/cron');

		$json = array();

		if (isset($this->request->get['cron_id'])) {
			$cron_id = $this->request->get['cron_id'];
		} else {
			$cron_id = 0;
		}

		if (!$this->user->hasPermission('modify', 'extension/module/cron')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('extension/module/cron');

			$cron_info = $this->model_extension_module_cron->getCron($cron_id);

			if ($cron_info) {
				$this->load->controller($cron_info['action'], $cron_id, $cron_info['code'], $cron_info['cycle'], $cron_info['date_added'], $cron_info['date_modified']);

				$this->model_extension_module_cron->runCron($cron_info['cron_id']);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function enable() {
		$this->load->language('extension/module/cron');

		$json = array();

		if (isset($this->request->get['cron_id'])) {
			$cron_id = $this->request->get['cron_id'];
		} else {
			$cron_id = 0;
		}

		if (!$this->user->hasPermission('modify', 'extension/module/cron')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('extension/module/cron');

			$this->model_extension_module_cron->editStatus($cron_id, 1);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function disable() {
		$this->load->language('extension/module/cron');

		$json = array();

		if (isset($this->request->get['cron_id'])) {
			$cron_id = $this->request->get['cron_id'];
		} else {
			$cron_id = 0;
		}

		if (!$this->user->hasPermission('modify', 'extension/module/cron')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('extension/module/cron');

			$this->model_extension_module_cron->editStatus($cron_id, 0);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function install() {
		$this->load->model('setting/setting');

		$settings['module_cron_status'] = 1;

		$this->model_setting_setting->editSetting('module_cron', $settings);

		$this->load->model('extension/module/cron');

		$this->model_extension_module_cron->install();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'code';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/cron', 'user_token=' . $this->session->data['user_token'] . $url)
		);

		$data['add'] = $this->url->link('extension/module/cron/add', 'user_token=' . $this->session->data['user_token']);
		$data['delete'] = $this->url->link('extension/module/cron/delete', 'user_token=' . $this->session->data['user_token'] . $url);
		
		$data['cron'] = $this->url->link('extension/cron');

		$data['crons'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$cron_total = $this->model_extension_module_cron->getTotalCrons();

		$results = $this->model_extension_module_cron->getCrons($filter_data);

		foreach ($results as $result) {
			$data['crons'][] = array(
				'cron_id'       => $result['cron_id'],
				'code'          => $result['code'],
				'cycle'         => $this->language->get('text_' . $result['cycle']),
				'action'        => $result['action'],
				'status'        => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'date_added'    => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('datetime_format'), strtotime($result['date_modified'])),
				'enabled'       => $result['status'],
				'edit'          => $this->url->link('extension/module/cron/edit', 'user_token=' . $this->session->data['user_token'] . '&cron_id=' . $result['cron_id'], true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_code'] = $this->url->link('extension/module/cron', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url);
		$data['sort_cycle'] = $this->url->link('extension/module/cron', 'user_token=' . $this->session->data['user_token'] . '&sort=cycle' . $url);
		$data['sort_action'] = $this->url->link('extension/module/cron', 'user_token=' . $this->session->data['user_token'] . '&sort=action' . $url);
		$data['sort_status'] = $this->url->link('extension/module/cron', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url);
		$data['sort_date_added'] = $this->url->link('extension/module/cron', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url);
		$data['sort_date_modified'] = $this->url->link('extension/module/cron', 'user_token=' . $this->session->data['user_token'] . '&sort=date_modified' . $url);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', array(
			'total' => $cron_total,
			'page'  => $page,
			'limit' => $this->config->get('config_limit_admin'),
			'url'   => $this->url->link('extension/module/cron', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		));

		$data['results'] = sprintf($this->language->get('text_pagination'), ($cron_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($cron_total - $this->config->get('config_limit_admin'))) ? $cron_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $cron_total, ceil($cron_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/cron/list', $data));
	}

	protected function getForm() {
		if (isset($this->error)) {
			foreach($this->error as $key => $error) {
				$data['error_' . $key] = $error;
			}
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_name'),
			'href' => $this->url->link('extension/module/cron', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['cron_id'])) {
			$data['form_action'] = $this->url->link('extension/module/cron/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['form_action'] = $this->url->link('extension/module/cron/edit', 'user_token=' . $this->session->data['user_token'] . '&cron_id=' . $this->request->get['cron_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/module/cron', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['cron_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$cron_info = $this->model_extension_module_cron->getCron($this->request->get['cron_id']);
		}

		if (!empty($cron_info)) {
			$data['cron_id'] = $cron_info['cron_id'];
		} else {
			$data['cron_id'] = '';
		}

		if (isset($this->request->post['code'])) {
			$data['code'] = $this->request->post['code'];
		} elseif (!empty($cron_info)) {
			$data['code'] = $cron_info['code'];
		} else {
			$data['code'] = '';
		}

		if (isset($this->request->post['action'])) {
			$data['action'] = $this->request->post['action'];
		} elseif (!empty($cron_info)) {
			$data['action'] = $cron_info['action'];
		} else {
			$data['action'] = '';
		}

		if (isset($this->request->post['cycle'])) {
			$data['cycle'] = $this->request->post['cycle'];
		} elseif (!empty($cron_info)) {
			$data['cycle'] = $cron_info['cycle'];
		} else {
			$data['cycle'] = 'day';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($cron_info)) {
			$data['status'] = $cron_info['status'];
		} else {
			$data['status'] = 1;
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/cron/form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/cron')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->error['warning'])) {
			if (empty($this->request->post['code'])) {
				$this->error['warning'] = $this->language->get('error_code');
			} elseif (empty($this->request->post['action'])) {
				$this->error['warning'] = $this->language->get('error_action');
			} elseif (empty($this->request->post['cycle'])) {
				$this->error['warning'] = $this->language->get('error_cycle');
			}
		}

		return !$this->error;
	}
}