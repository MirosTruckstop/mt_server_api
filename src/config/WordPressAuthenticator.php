<?php

class WordPressAuthenticator implements Slim\Middleware\HttpBasicAuthentication\AuthenticatorInterface {
	
	private $options;
	
	public function __construct() {
		$this->options = array(
			'wp_path' => '../wordpress',
			'table' => 'wp_users',
			'user' => 'user_login',
			'hash' => 'user_pass'
		);
	}

	/**
	 * 
	 * @param array $arguments Array that contains keys 'user' and 'password'
	 * @return boolean
	 */
	public function __invoke(array $arguments) {
		require_once($this->options['wp_path'].'/wp-includes/class-phpass.php');
		$user = ORM::for_table($this->options['table'])->select($this->options['hash'])->where($this->options['user'], $arguments['user'])->find_one();		
		$wp_hasher = new PasswordHash(8, TRUE);
		return $wp_hasher->CheckPassword($arguments['password'], $user[$this->options['hash']]);
	}
}