<?php
	class UsersController extends AppController {
		function index() {

		}

		function logout() {
			$this->Auth->logout();
			$this->redirect($this->Auth->logoutRedirect);
		}

		function login() {
			$menu = array();
			$this->set('title', 'Authorization');
			$this->set('layout_title', 'Authorization');
			$this->set('layout_redirect', array('controller'=>'users', 'action'=>'login'));
			$this->set(compact('menu'));
			$this->theme = "_classic";
			if(Configure::read('config.backend.theme'))
				$this->theme = Configure::read('config.backend.theme');

			if (!empty($this->data)) {
				$this->data['User']['password'] = Security::hash(@$_POST['data']['User']['password']);

				if($this->Auth->login(@$this->data['User'])) {
					$this->Session->delete('Message.auth');

					if (!empty($this->data['User']['remember_me'])) {
						$cookie = array();
						$cookie['username'] = $this->data['User']['username'];
						$cookie['password'] = $_POST['data']['User']['password'];
						unset($this->data['User']['remember_me']);
						$this->Cookie->write('Auth.User', $cookie, true, '+2 weeks');
					} else {
						$this->Cookie->delete('Auth.User');
					}
					$this->redirect($this->Auth->redirect());
				} else {
					$this->data['User']['password'] = '';
				}

			} else {
				$this->Auth->login($this->Cookie->read('Auth.User'));
			}
		}
	}