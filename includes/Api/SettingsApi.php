<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Api;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

class SettingsApi {

	public $admin_pages = array();
	public $admin_subpages = array();
	public $settings = array();
	public $sections = array();
	public $fields = array();

	public function register() {
		if ( !empty($this->admin_pages) ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		}
	}

	/**
	* Adds the main plugin pages
	*/
	public function add_pages( array $pages ) {
		$this->admin_pages = $pages;
		return $this;
	}

	/**
	* Adds all the plugin subpages
	*/
	public function add_sub_pages( array $pages ) {
		$this->admin_subpages = array_merge( $this->admin_subpages, $pages );
		return $this;
	}

	public function with_sub_page( $title = null ) {
		if ( empty($this->admin_pages) ) {
			return $this;
		}
		$admin_page = $this->admin_pages[0];
		$subpage = array(
			array(
				'parent_slug' => $admin_page['menu_slug'],
				'page_title'  => $admin_page['page_title'], 
				'menu_title'  => ($title) ? $title : $admin_page['menu_title'], 
				'capability'  => $admin_page['capability'], 
				'menu_slug'   => $admin_page['menu_slug'], 
				'callback'    => $admin_page['callback']
			)
		);
		$this->admin_subpages = $subpage;
		return $this;
	}

	/**
	* Adds the menu and submenu pages
	*/
	public function add_admin_menu() {
		foreach ( $this->admin_pages as $page ) {
			add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position'] );
		}
		foreach ( $this->admin_subpages as $page ) {
			add_submenu_page( $page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'] );
		}
	}
}