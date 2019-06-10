<?php

namespace apps\admin\forms\session;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Identical;

class LoginForm extends Form {
	public function initialize($entity = null, $options = null) {
		$filter_string = array (
				'trim',
				'striptags',
				'string' 
		);
		
		$username = new Text ( 'username', array (
				'required' => true,
				'class' => 'form-control',
				'placeholder' => 'Nome utente',
				'maxlength' => 50 
		) );
		$username->setLabel ( 'Nome utente' );
		$username->setFilters ( $filter_string );
		$username->addValidators ( array (
				new PresenceOf ( array (
						'message' => 'Il campo &egrave; obbligatorio' 
				) ) 
		) );
		$this->add ( $username );
		
		$password = new Password ( 'password', array (
				'required' => true,
				'class' => 'form-control',
				'placeholder' => 'Password',
				'maxlength' => 50 
		) );
		$password->setLabel ( 'Password' );
		$password->setFilters ( $filter_string );
		$password->addValidators ( array (
				new PresenceOf ( array (
						'message' => 'Il campo &egrave; obbligatorio' 
				) ),
				new StringLength ( array (
						'min' => 5,
						'messageMinimum' => 'La password &egrave; troppo corta' 
				) ) 
		) );
		$this->add ( $password );
		
		// CSRF
		$csrf = new Hidden ( 'csrf', array (
				'hidden' => true 
		) );
		/*$csrf->addValidator ( new Identical ( array (
				'value' => $this->security->getSessionToken (),
				'message' => 'CSRF validation failed' 
		) ) );
		$csrf->clear ();*/
		$this->add ( $csrf );
	}
}
