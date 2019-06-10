<?php

namespace apps\admin\library;

use Phalcon\Mvc\User\Component;

class Menu extends Component {
	protected $current_route;
	public function render($menu) {

		$this->current_route = $this->getDi()->get('router')->getRewriteUri();

		// $params = $this->dispatcher->getParams();
		// if(isset($params[0])) $this->current_route.='/'.$params[0];
		
		$id_header = 0;
		$m = array ();
		
		$count = count ( $menu );
		for($i = 0; $i < $count; $i ++) {
			$active = '';
			$item = $menu [$i];
			
			if (! $item ['header']) {
				if (empty ( $item ['sub_menu'] )) {
					if (empty($active)) {
						$active = ('/admin/'.$item ['risorsa'] . '/' . $item ['azione'] == $this->current_route) ? 'active' : '';
					}
					$m [] = '<li class="menu_item ' . $active . '">' . $this->tag->linkTo ( array (
							'admin/'.$item ['risorsa'] . '/' . $item ['azione'],
							'<i class="fa ' . $item ['class'] . ' fa-fw"></i><span class="menu_desc">' . $item ['descrizione'] . '</span>' 
					) ) . '</li>';
				} else {
					$submenu = $this->render_submenu ( $item ['sub_menu'], '' );
					$active = $submenu ['active'];
					
					$m [] = '<li class="treeview menu_item ' . $active . '"><a href="#"><i class="fa ' . $item ['class'] . ' fa-fw"></i><span class="menu_desc">' . $item ['descrizione'] . '</span><i class="fa fa-angle-right fa-fw pull-right"></i></a>';
					$m [] = $submenu ['menu'];
					$m [] = '</li>';
				}
			} else {
				$m [] = '<li class="header ' . $id_header . '">' . strtoupper ( $item ['descrizione'] ) . '</li>';
				$id_header ++;
			}
		}

		return implode ( '', $m );
	}
	private function render_submenu($menu, $active) {
		$m = array ();
		$active_current = '';
		$isActive = false;
		
		$count = count ( $menu );
		for($i = 0; $i < $count; $i ++) {
			$item = $menu [$i];
			
			$fa_class = ! empty ( $item ['class'] ) ? $item ['class'] : 'fa-ellipsis-v';
			
			if (empty ( $item ['sub_menu'] )) {
				if ('/admin/'.$item ['risorsa'] . '/' . $item ['azione'] == $this->current_route) {
					$active_current = 'active';
					$isActive = true;
				} else {
					$active_current = '';
				}
				
				$m [] = '<li class="' . $active_current . '">' . $this->tag->linkTo ( array (
						'admin/'.$item ['risorsa'] . '/' . $item ['azione'],
						'<i class="fa ' . $fa_class . ' fa-fw"></i><span class="menu_desc">' . $item ['descrizione'] . '</span>' 
				) ) . '</li>';
			} else {
				$submenu = $this->render_submenu ( $item ['sub_menu'], $active );
				if ($submenu ['active'] != '') {
					$active = 'active';
					$isActive = true;
				} else {
					$active = '';
				}
				
				$m [] = '<li class="' . $active . '"><a href="#"><i class="fa ' . $fa_class . ' fa-fw"></i><span class="menu_desc">' . $item ['descrizione'] . '</span><i class="fa fa-angle-right fa-fw pull-right"></i></a>';
				$m [] = $submenu ['menu'];
				$m [] = '</li>';
			}
		}
		
		$m [] = '</ul>';
		
		$ul = $isActive ? '<ul class="treeview-menu menu-open" style="display:block;">' : '<ul class="treeview-menu">';
		array_unshift ( $m, $ul );
		
		return array (
				'active' => $isActive ? 'active' : '',
				'menu' => implode ( '', $m ) 
		);
	}
}