<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Google Maps Geocoder Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Justin Kimbrell
 * @link		https://www.objectivehtml.com
 */

require_once(PATH_THIRD . 'gmap_geocoder/libraries/InterfaceBuilder/InterfaceBuilder.php');

class Gmap_geocoder_mcp {
	
	public $return_data;
	
	private $_base_url;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		
		$this->EE->load->model('gmap_geocoder_model');
		
		$this->_base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=gmap_geocoder';
	}
	
	// ----------------------------------------------------------------

	/**
	 * Index Function
	 *
	 * @return 	void
	 */
	public function index()
	{
		$this->EE->cp->set_right_nav(array(
			'gmap_geocoder_new'	=> $this->_url('new_setting'),
			// Add more right nav items here.
		));
		
		$this->EE->cp->set_variable('cp_page_title', lang('gmap_geocoder_module_name'));
		
		$vars = array(
			'new_setting_url' => $this->_url('new_setting'),
			'settings' 		  => $this->EE->gmap_geocoder_model->get_settings(),
			'url'			  => $this->_url()
		);
		
		return $this->EE->load->view('index', $vars, TRUE);
	}
	
	public function new_setting()
	{
		$this->EE->cp->set_right_nav(array(
			'module_home' => $this->_url('index'),
			// Add more right nav items here.
		));
		
		$this->EE->cp->set_variable('cp_page_title', lang('gmap_geocoder_module_name'));
		
		$vars = array(
			'header'          => lang('gmap_geocoder_new_instance'),
			'action'          => $this->_url('new_setting_action'),
			'new_setting_url' => $this->_url('new_setting'),
			'settings'        => array(),
			'xid'             => $this->EE->security->generate_xid(),
			'table'           => $this->_build_settings_table(),
			'button'		   => lang('gmap_geocoder_save_setting')
		);
		
		return $this->EE->load->view('settings_form', $vars, TRUE);
	}
	
	public function edit_setting()
	{
		$this->EE->cp->set_right_nav(array(
			'module_home' => $this->_url('index'),
			// Add more right nav items here.
		));
		
		$this->EE->cp->set_variable('cp_page_title', lang('gmap_geocoder_module_name'));
		
		$id       = $this->EE->input->get_post('id');
		$settings = $this->EE->gmap_geocoder_model->get_setting($id);
		
		$vars = array(
			'header'           => lang('gmap_geocoder_edit_instance'),
			'action'           => $this->_url('edit_setting_action') . '&id='.$id,
			'edit_setting_url' => $this->_url('edit_setting'),
			'xid'              => $this->EE->security->generate_xid(),
			'table'            => $this->_build_settings_table($settings),
			'button'		   => lang('gmap_geocoder_update_setting')
		);
		
		return $this->EE->load->view('settings_form', $vars, TRUE);
	}
	
	private function _build_settings_table($settings = array())
	{
		if(!isset($this->EE->theme_loader))
		{
			$this->EE->load->library('theme_loader');
		}
		
		$this->EE->theme_loader->module_name = 'gmap_geocoder';
		$this->EE->theme_loader->javascript('InterfaceBuilder');
		$this->EE->theme_loader->output('$(document).ready(function() {
			var IB = new InterfaceBuilder();
		})');
		
		$fields = array(
			'geocode_fields' => array(
				'label' => lang('gmap_geocoder_fields'),
				'type'  => 'matrix',
				'settings' => array(
					'columns' => array(
						array(
							'name'  => 'field_name',
							'title' => 'Form Field Name'
						)
					),
					'attributes' => array(
						'class'       => 'mainTable padTable',
						'border'      => 0,
						'cellpadding' => 0,
						'cellspacing' => 0
					)
				)
			),
			'channel_names' => array(
				'label'       => lang('gmap_geocoder_channel_names'),
				'description' => lang('gmap_geocoder_channel_names_desc')
			),
			'latitude_field_name' => array(
				'label'       => lang('gmap_geocoder_latitude_field_name'),
				'description' => lang('gmap_geocoder_latitude_field_name_desc')
			),
			'longitude_field_name' => array(
				'label'       => lang('gmap_geocoder_longitude_field_name'),
				'description' => lang('gmap_geocoder_longitude_field_name_desc')
			),
			'gmap_field_name' => array(
				'label'       => lang('gmap_geocoder_gmap_field_name'),
				'description' => lang('gmap_geocoder_gmap_field_name_desc')
			)
		);
		
		$properties = array(
			'dataArray' => TRUE,
			'varName'   => 'settings'
		);
		
		return InterfaceBuilder::table($fields, $settings, $properties, array(
			'class'       => 'mainTable padTable',
			'border'      => 0,
			'cellspacing' => 0,
			'cellpadding' => 0
		));	
	}
	
	public function new_setting_action()
	{
		$settings = $this->EE->input->post('settings');
		
		$this->EE->gmap_geocoder_model->save_setting($settings);
		
		$this->EE->functions->redirect($this->_url('index'));
	}
	
	public function edit_setting_action()
	{
		$id       = $this->EE->input->get_post('id');		
		$settings = $this->EE->input->post('settings');
		
		$this->EE->gmap_geocoder_model->update_setting($id, $settings);
		
		$this->EE->functions->redirect($this->_url('index'));
	}
	
	public function delete_setting()
	{
		$id = $this->EE->input->get_post('id');
		
		$vars = array(
			'id'  => $id,
			'url' => $this->_url('delete_setting_action') . '&id='.$id
		);
		
		return $this->EE->load->view('delete_setting', $vars, TRUE);
	}
	
	public function delete_setting_action()
	{
		$id = $this->EE->input->get_post('id');
		
		$this->EE->gmap_geocoder_model->delete_setting($id);
		$this->EE->functions->redirect($this->_url('index'));
	}
	
	private function _url($method = FALSE)
	{
		return $this->_base_url . ($method ? '&method='.$method : NULL);
	}
	
	/**
	 * Start on your custom code here...
	 */
	
}
/* End of file mcp.gmap_geocoder.php */
/* Location: /system/expressionengine/third_party/gmap_geocoder/mcp.gmap_geocoder.php */