<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gmap_geocoder_model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->driver('channel_data');
	}
	
	public function update_field_ids($array)
	{
		$fields = $this->channel_data->get_channel_fields()->result_array();
		$fields = $this->channel_data->utility->reindex('field_name', $fields);
		
		foreach($array as $index => $value)
		{
			if(isset($fields[$value['field_name']]))
			{
				$field = $fields[$value['field_name']];
				
				$array[$index]['field_id'] = $field['field_id'];
			}
		}
		
		return $array;
	}
	
	public function get_settings($params = array())
	{
		return $this->channel_data->get('gmap_geocoder_settings', $params);
	}
	
	public function get_setting($id, $params = array())
	{
		$params['where']['id'] = $id;
		
		$setting = $this->get_settings($params);
		
		if($setting->num_rows() == 0)
		{
			return array();
		}
		
		$setting = $setting->row_array();
		
		if(isset($setting['geocode_fields']))
		{
			$setting['geocode_fields'] = json_decode($setting['geocode_fields']);	
		}
		
		return $setting;
	}
	
	public function save_setting($setting = array())
	{			
		$this->db->insert('gmap_geocoder_settings', $this->convert_array($setting));	
	}
	
	public function update_setting($id, $setting = array())
	{			
		$this->db->where('id', $id);
		$this->db->update('gmap_geocoder_settings', $this->convert_array($setting));
	}
		
	public function delete_setting($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('gmap_geocoder_settings');
	}	
	
	public function convert_array($array)
	{
		foreach($array as $index => $value)
		{
			if(!is_string($value))
			{
				$array[$index] = json_encode($value);
			}
		}
		
		return $array;
	}
}