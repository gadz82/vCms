<?php
class ErrorsController extends ControllerBase {
	public function initialize() {
		$this->tag->setTitle ( 'Oops!');
		parent::initialize ();
	}

	public function show401Action() {

	}

	public function show404Action() {

	}

	public function show500Action() {
	    $params = $this->dispatcher->getParams();
        $this->view->error_code = $params['error_code'];
        $this->view->error_message = $params['error_messge'];
        $this->view->error_file = $params['error_file'];
        $this->view->error_trace = json_encode($params['error_trace'], JSON_PRETTY_PRINT);
	}
}