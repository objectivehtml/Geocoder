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
 * Google Maps Geocoder Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Justin Kimbrell
 * @link		https://www.objectivehtml.com
 */

class Gmap_geocoder_ext {
	
	public $settings 		= array();
	public $description		= 'A companion server-side geocoder for Google maps for ExpressioneEngine.';
	public $docs_url		= 'https://www.objectivehtml.com/google-maps';
	public $name			= 'Google Maps Geocoder';
	public $settings_exist	= 'n';
	public $version			= '1.0';
	public $required_by 	= array('module');
	
	private $EE;
	
	public $channel_ids;
	public $fields;
	public $response;
	public $location_string;
	
	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
	   	$this->EE =& get_instance();

        $this->settings = $settings;
    }
    
	public function settings()
	{
		return '';
	}
	
	public function safecracker_entry_form_tagdata_start($tagdata, $obj)
	{
		$geocode = strtolower($this->EE->TMPL->fetch_param('geocode'));

		if($geocode == 'false' || $geocode == 'no' || $geocode == '0')
		{
			$obj->form_hidden('safecracker_geocode', 'false');
		}

		return $tagdata;
	}
	
	public function entry_submission_start($channel_id, $autosave)
	{ 
		if(!$this->EE->input->post('safecracker_geocode'))
		{       
	        $this->_load();
	        
			$settings = $this->EE->gmap_geocoder_model->get_settings();
			
			foreach($settings->result() as $setting_index => $setting)
			{
				$channel_ids   = array();
				$channel_names = explode(',', $setting->channel_names);
				
				foreach($channel_names as $index => $channel_name)
				{
					$channel_names[$index] = 'or ' . $channel_name;
				}
				
				$channels = $this->EE->channel_data->get_channels(array(
					'where' => array(
						'site_id'      => config_item('site_id'),
						'channel_name' => $channel_names
					)
				));			
				
				$fields = $this->EE->channel_data->get_fields(array(
					'where' => array(
						'site_id'      => config_item('site_id')
					)
				));
				
				$fields = $this->EE->channel_data->utility->reindex($fields->result(), 'field_name');
				
				foreach($channels->result() as $channel)
				{
					$channel_ids[] = $channel->channel_id;
				}
				
				$location       = array();
				$geocode_fields = json_decode($setting->geocode_fields);
				
				if(in_array($channel_id, $channel_ids))
				{
					foreach($geocode_fields as $index => $field)
					{	
						$field_id = FALSE;
						
						if(isset($fields[$field->field_name]))
						{
							$field_id = $fields[$field->field_name]->field_id;
						}
						
						if($field = $this->EE->input->get_post($field->field_name))
						{
							$location[] = $field;	
						}
						else if($field_id && $field = $this->EE->input->get_post('field_id_'.$field_id))
						{
							$location[] = $field;						
						}
						
						$geocode_fields[$index] = (object) array(
							'field_name' => $field,
							'field_id'   => $field_id
						);
					}
					
					if(count($location) == 0)
					{				
						if(!isset($setting->throw_error_no_geocode_fields) || $setting->throw_error_no_geocode_fields == 'true')
						{
							if(isset($this->EE->api_sc_channel_entries))
							
							{
								$this->EE->api_sc_channel_entries->errors[] = lang('gmap_geocoder_no_valid_fields');					
							}
							else
							{
								$this->EE->api_channel_entries->_set_error(lang('gmap_geocoder_no_valid_fields'));
							}
						}
					}
					else
					{
						$location_string[$setting_index] = implode($location, ' ');
						$response = $this->EE->channel_data->gmap->geocode($location_string[$setting_index]);
						
						if($response[0]->status != 'OK')
						{					
							$error = str_replace(LD.'location'.RD, $location_string[$setting_index], lang('gmap_geocoder_no_valid_location'));
							
							if(isset($this->EE->api_sc_channel_entries))
							{
								$this->EE->api_sc_channel_entries->errors[] = $error;					
							}
							else
							{
								foreach($geocode_fields as $field)
								{
									$this->EE->api_channel_entries->_set_error($error, 'field_id_'.$field->field_id);	
								}
							}
						}
						else
						{
							$this->EE->session->set_cache('gmap_geocoder', 'response', $response);
							$this->EE->session->set_cache('gmap_geocoder', 'fields', $fields);
						}
					}
				}
			}
		}
	}

	public function entry_submission_ready($meta, $data, $autosave)
	{	 
		$entry = $this->EE->channel_data->get_channel_entry($data['entry_id'])->row();

		if(!$this->EE->input->post('safecracker_geocode'))
		{       
	        $this->_load();
	        
			$settings = $this->EE->gmap_geocoder_model->get_settings();
			
			if(!isset($this->EE->session->cache['gmap_geocoder']))
			{
				return $data;
			}
			
			foreach($settings->result() as $setting_index => $setting)
			{
				$response = $this->EE->session->cache['gmap_geocoder']['response'];			
				$fields   = $this->EE->session->cache['gmap_geocoder']['fields'];
				
				if($response[0]->status == 'OK' && isset($fields[$setting->latitude_field_name]) && isset($fields[$setting->longitude_field_name]))
				{
					$lat = $response[0]->results[0]->geometry->location->lat;
					$lng = $response[0]->results[0]->geometry->location->lng;
					
					if(
						isset($setting->preserve_lat_lng) && 
						$setting->preserve_lat_lng == 'true' &&
						!empty($entry->{$setting->latitude_field_name}) && 
						!empty($entry->{$setting->longitude_field_name})
					  )
					{
						return $data;
					}

					if(isset($fields[$setting->latitude_field_name]))
					{
						$lat_field  = $fields[$setting->latitude_field_name];
											
						$data[$setting->latitude_field_name]    = $lat;
						$data['field_id_'.$lat_field->field_id] = $lat;
					}
									
					if(isset($fields[$setting->longitude_field_name]))
					{
						$lng_field  = $fields[$setting->longitude_field_name];
											
						$data[$setting->longitude_field_name]   = $lng;
						$data['field_id_'.$lng_field->field_id] = $lng;
					}
					
					if(isset($fields[$setting->gmap_field_name]))
					{
						$gmap_field = $fields[$setting->gmap_field_name];								
						$response   = $this->EE->channel_data->gmap->build_response(array('markers' => array($response[0]->results[0])));
					
						$data[$setting->gmap_field_name]   		 = $response;
						$data['field_id_'.$gmap_field->field_id] = $response;
					}
								
					if(isset($this->EE->api_sc_channel_entries))
					{
						$this->EE->api_sc_channel_entries->data = $data;					
					}
					else
					{
						$this->EE->api_channel_entries->data = $data;
					}
				}
			}
		}

		return $data;
	}
	
    
    private function _load()
    {
        $this->EE->lang->loadfile('gmap_geocoder');
        $this->EE->load->model('gmap_geocoder_model');
        $this->EE->load->driver('channel_data');
        
		$this->EE->channel_data->api->load('gmap');
    }

	public function entry_submission_end($entry_id, $meta, $data)
	{	
		// var_dump($data);exit();
	}
			 
	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @return void
	 */
	function activate_extension()
	{	    
	    return TRUE;
	}
	
	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return  mixed   void on update / false if none
	 */
	function update_extension($current = '')
	{
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }
	
	    if ($current < '1.0')
	    {
	        // Update to version 1.0
	    }
	
	    $this->EE->db->where('class', __CLASS__);
	    $this->EE->db->update('extensions', array('version' => $this->version));
	}
	
	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
	    $this->EE->db->where('class', __CLASS__);
	    $this->EE->db->delete('extensions');
	}
	
}

/* End of file ext.gmap_geocoder.php */
/* Location: /system/expressionengine/third_party/gmap_geocoder/ext.gmap_geocoder.php */