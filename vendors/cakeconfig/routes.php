<?php
	$config = Configure::read('config');
	$modules = Configure::read('modules');
	
	
	if(!$config) {
		Router::connect('/:action', array('controller'=>'install'));
	} else {
		Router::parseExtensions('json', 'rss', 'xml');		
		
		$routes = array();
		foreach($config['modules'] as $module=>$version) {
			if(sizeof(@$modules[$module]['routes'])>1) {
				foreach($modules[$module]['routes'] as $url=>$action) {
					if(strpos($url,':'))
						$routes = array_merge($routes, array($url=>$action));
					elseif(isset($routes[$url])) {
						$routes[$url] = $action;
					} else
						$routes = array_merge(array($url=>$action), $routes);
				}
			}
		}
		
		foreach($routes as $url=>$action) {
			$writeConfig = false;
			$configs = array();
			foreach($action as $key=>$param) {
				if(!is_int($key) && ($key == 'pass' || $writeConfig)) {
					$writeConfig = true;
					$configs[$key] = $param;
					unset($action[$key]);
				}
			}
			Router::connect($url, $action, $configs);
		}
	}