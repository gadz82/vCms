<?php
define ( 'BASE_DIR', dirname ( __DIR__ ) );
define ( 'APP_DIR', BASE_DIR . '/apps/admin' );
define('ABSOLUTE_DIR', str_replace('public', '', __DIR__));

/*if ($_SERVER ['SERVER_ADDR'] != '127.0.0.1' || $_SERVER ['REMOTE_ADDR'] != '127.0.0.1') {
	header ( 'Location: https://marchiauto.it' );
}*/

if (! empty ( $_POST )) {
	
	require_once (BASE_DIR . '/apps/admin/library/builder/Builder.php');
	require_once (BASE_DIR . '/apps/admin/library/builder/Mysql.php');
	
	$controller_data = array (
			'CONTROLLER_NAME' => isset ( $_POST ['controller_name'] ) ? $_POST ['controller_name'] : '',
			'CONTROLLER_NAME_SINGOLARE' => isset ( $_POST ['controller_name_singolare'] ) ? $_POST ['controller_name_singolare'] : '',
			'TITOLO_SINGOLARE' => isset ( $_POST ['titolo_singolare'] ) ? $_POST ['titolo_singolare'] : '',
			'TITOLO_PLURALE' => isset ( $_POST ['titolo_plurale'] ) ? $_POST ['titolo_plurale'] : '',
			'JOIN_RICERCA_TABELLA' => isset ( $_POST ['join_ricerca_tabella'] ) ? $_POST ['join_ricerca_tabella'] : '',
			'JOIN_RICERCA_ALIAS' => isset ( $_POST ['join_ricerca_alias'] ) ? $_POST ['join_ricerca_alias'] : '',
			'JOIN_RICERCA_CAMPO' => isset ( $_POST ['join_ricerca_campo'] ) ? $_POST ['join_ricerca_campo'] : '',
			'RENDER_PAGING_OBJ' => isset ( $_POST ['render_pagina_object'] ) ? $_POST ['render_pagina_object'] : '' 
	);
	
	$options = array (
			'permessi' => isset ( $_POST ['permessi'] ),
			'menu' => isset ( $_POST ['menu'] ),
			'override' => isset ( $_POST ['override'] ) 
	);
	
	$flags = array (
			'history' => isset ( $_POST ['history'] ),
			'email_segnalazioni' => isset ( $_POST ['email_segnalazioni'] ) 
	);
	
	$menu = array (
			'descrizione_menu_padre' => isset ( $_POST ['descrizione_menu_padre'] ) ? $_POST ['descrizione_menu_padre'] : '',
			'descrizione_menu' => isset ( $_POST ['descrizione_menu'] ) ? $_POST ['descrizione_menu'] : '',
			'icona_menu' => isset ( $_POST ['icona_menu'] ) ? $_POST ['icona_menu'] : '' 
	);
	error_reporting(E_ALL);
    ini_set('display_errors', 1);

	$b = new builder ( $controller_data, $flags, $options, $menu );
	$b->run ();
}
	
echo file_get_contents ( 'builder/theme/index.html' );

?>