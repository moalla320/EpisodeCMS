<?php
/**
 * Event Component for CakePHP
 *
 * Plugin Spinoff - single component approach.
 *
 * @author Kjell Bublitz <kjell@growinthings.de>
 * @author Aleksey Razbakov <razbakov.aleksey@gmail.com>
 * @copyright 2010 (cc)
 * @link http://cakealot.com
 * @package eventful-component
 * @subpackage components
 * @version $Id$
 */
App::import('Core', 'String');

/**
 * EventComponent
 *
 *
 * @package eventful-component
 * @subpackage components
 */
class EventComponent extends Object {

	/**
	 * Controller Instance
	 *
	 * @var object
	 */
	private $Controller = null;

	/**
	 * Startup
	 *
	 * @param unknown_type $controller
	 */
	public function initialize(&$controller) {
		$this->Controller = $controller;
	}

	/**
	 * Trigger Event
	 *
	 * @param string|array $events Use dot notation if you want.
	 * @param array $data (optional) Any data to pass along
	 * @param array $config (optional)
	 * @return array
	 */
	public function triggerEvent($eventName, $data = array(), $config = array()) {
		if (is_array($eventName)) {
			$eventNames = Set::filter($eventName);
			foreach ($eventNames as $eventName) {
				extract($this->_parseEventName($eventName), EXTR_OVERWRITE);
				$return[$scope][$event] = $this->_dispatchEvent($scope, $event, $data);
			}
		} else {
			extract($this->_parseEventName($eventName), EXTR_OVERWRITE);
			$return[$scope][$event] = $this->_dispatchEvent($scope, $event, $data);
		}

		return $return;
	}

	/**
	 * Dispatch event
	 *
	 * @param string $scope
	 * @param string $eventName
	 * @param array $data (optional)
	 * @return
	 */
	private function _dispatchEvent($scope, $eventName, $data = array()) {
		$eventHandlerMethod = $this->_handlerMethodName($eventName);
		$validHandlersClasses = $this->_scanControllers($scope, $eventHandlerMethod);

		$return = array();
		foreach($validHandlersClasses as $class) {
			$controller = $this->Controller;
			if ($class != get_class($controller)) {
				$controller = new $class;
			}
			$event = new Event($eventName, $data);

			// call method on controller, pass event object and triggering controller object
			// TODO: Check if new way works better.
			// OLD: $return[$class] = call_user_method_array($eventHandlerMethod, $controller, array($event, &$this->Controller));
			$return[$class] = $controller->$eventHandlerMethod($event, &$this->Controller);		
		}
		return $return;
	}

	var $validHandlers = array();

	/**
	 * Check all controller classes in scope for the eventHandlerMethod
	 *
	 * @param unknown_type $scope
	 * @param unknown_type $eventHandlerMethod
	 * @return array List of controller class names
	 */
	private function _scanControllers($scope, $eventHandlerMethod) {
		$validHandlerClasses = array();
		$controllers = Configure::read('controllers');

		if(@$controllers)
		foreach ($controllers as $controller) {
			if (($scope == $controller || lowercase($scope) == 'global')) { // must be in scope, or global
				$controllerClass = $controller.'Controller';
				if (!class_exists($controllerClass)) { // app import doesn't work..
					App::import('Controller', $controller);
				}

				if (is_callable(array($controllerClass, $eventHandlerMethod))) {
					$validHandlerClasses[] = $controllerClass;
				}
			}
		}
		return $validHandlerClasses;
	}

	/**
	 * eventName to methodName
	 *
	 * @param string $eventName
	 * @return string
	 */
	private function _handlerMethodName($eventName) {
		return '_on'.Inflector::camelize($eventName);
	}

	/**
	 * Parse eventName and extract scope. Default scope is "Global"
	 *
	 * @param string $eventName
	 * @return array (scope, event)
	 */
	private function _parseEventName($eventName) {
		$eventTokens = String::tokenize($eventName, '.');
		$scope = 'Global';
		$event = $eventTokens[0];
		if (count($eventTokens) > 1) {
			list($scope, $event) = $eventTokens;
		}
		return compact('scope', 'event');
	}

}

/**
 * Event Object
 *
 * @package eventful-component
 */
class Event {

	/**
	 * Contains assigned values
	 *
	 * @var array
	 */
	protected $values = array();

	/**
	 * Constructor with EventName and EventData (optional)
	 *
	 * Event Data is automaticly assigned as properties by array key
	 *
	 * @param string $eventName Name of the Event
	 * @param array $data optional array with k/v data
	 */
	public function __construct($eventName, $data = array()) {
		$this->name = $eventName;

		if (!empty($data)) {
			foreach ($data as $name => $value) {
				$this->{$name} = $value;
			} // push data values to props
		}
	}

	/**
	 * Write to object
	 *
	 * @param string $name Key
	 * @param mixed $value Value
	 */
	public function __set($name, $value) {
		$this->values[$name] = $value;
	}

	/**
	 * Read from object
	 *
	 * @param string $name Key
	 */
	public function __get($name) {
		return $this->values[$name];
	}
}