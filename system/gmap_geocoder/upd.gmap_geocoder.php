<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Photo Frame
 * 
 * @package		Google Maps Geocoder
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Justin Kimbrell
 * @link 		http://www.objectivehtml.com/photo-frame
 * @version		0.7.0
 * @build		20121031
 */
 
require_once PATH_THIRD . 'gmap_geocoder/config/gmap_geocoder_config.php';

class Gmap_geocoder_upd {

    public $version = GMAP_GEOCODER_VERSION;
	public $mod_name;
	public $ext_name;
	public $mcp_name;
	
	private $tables  = array(
		'gmap_geocoder_settings' => array(
			'id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			),
			'geocode_fields' => array(
				'type' => 'text'
			),
			'channel_names' => array(
				'type' => 'text'
			),
			'latitude_field_name' => array(
				'type' => 'longtext',
			),
			'longitude_field_name' => array(
				'type'		 => 'varchar',
				'constraint' => 200
			),
			'gmap_field_name' => array(
				'type'		 => 'longtext'
			),
			'preserve_lat_lng' => array(
				'type'		 => 'varchar',
				'constraint' => 200
			),
			'throw_error_no_geocode_fields' => array(
				'type'		 => 'varchar',
				'constraint' => 200
			)
		)
	);
	
	private $actions = array(
		array(
			'class'  => 'Gmap_geocoder_mcp',
			'method' => 'save_settings'
		),
		array(
			'class'  => 'Gmap_geocoder_mcp',
			'method' => 'update_settings'
		),
		array(
			'class'  => 'Gmap_geocoder_mcp',
			'method' => 'delete_settings'
		)
	);
		
	private $hooks = array(
		array('safecracker_entry_form_tagdata_start', 'safecracker_entry_form_tagdata_start', '', 100),
		array('entry_submission_start', 'entry_submission_start', '', 100),
		array('entry_submission_ready', 'entry_submission_ready', '', 100),
		array('entry_submission_stop', 'entry_submission_stop', '', 100)
	);
	
    public function __construct()
    {
        // Make a local reference to the ExpressionEngine super object
        $this->EE =& get_instance();
        
        $this->mod_name 	= str_replace('_upd', '', __CLASS__);
        $this->ext_name		= $this->mod_name . '_ext';
        $this->mcp_name		= $this->mod_name . '_mcp';
    }
	
	public function install()
	{	
		$this->EE->load->library('data_forge');
		
		$this->EE->data_forge->update_tables($this->tables);
				
		$data = array(
	        'module_name' 		 => $this->mod_name,
	        'module_version' 	 => $this->version,
	        'has_cp_backend' 	 => 'y',
	        'has_publish_fields' => 'n'
	    );
	    	
	    $this->EE->db->insert('modules', $data);
	    	    	    
		foreach ($this->hooks as $row)
		{
			$this->EE->db->insert(
				'extensions',
				array(
					'class' 	=> $this->ext_name,
					'method' 	=> $row[0],
					'hook' 		=> ( ! isset($row[1])) ? $row[0] : $row[1],
					'settings' 	=> ( ! isset($row[2])) ? '' : $row[2],
					'priority' 	=> ( ! isset($row[3])) ? 10 : $row[3],
					'version' 	=> $this->version,
					'enabled' 	=> 'y',
				)
			);
		}
		
		foreach($this->actions as $action)
			$this->EE->db->insert('actions', $action);
		
		$this->_set_defaults();
				
		return TRUE;
	}
	
	
	public function update($current = '')
	{
		require_once 'libraries/Data_forge.php';
	
		$this->EE->data_forge = new Data_forge();
		$this->EE->data_forge->update_tables($this->tables);

		foreach($this->actions as $action)
		{
			$this->EE->db->where(array(
				'class'  => $action['class'],
				'method' => $action['method']
			));
			
			$existing = $this->EE->db->get('actions');

			if($existing->num_rows() == 0)
			{
				$this->EE->db->insert('actions', $action);
			}
		}
		
		foreach($this->hooks as $row)
		{
			$this->EE->db->where(array(
				'class'  => $this->ext_name,
				'method'  => $row[0],
				'hook' => $row[1]
			));
			
			$existing = $this->EE->db->get('extensions');

			if($existing->num_rows() == 0)
			{
				$this->EE->db->insert(
					'extensions',
					array(
						'class' 	=> $this->ext_name,
						'method' 	=> $row[0],
						'hook' 		=> ( ! isset($row[1])) ? $row[0] : $row[1],
						'settings' 	=> ( ! isset($row[2])) ? '' : $row[2],
						'priority' 	=> ( ! isset($row[3])) ? 10 : $row[3],
						'version' 	=> $this->version,
						'enabled' 	=> 'y',
					)
				);
			}
		}
		
	    return TRUE;
	}
	
	public function uninstall()
	{
		$this->EE->load->dbforge();
		
		$this->EE->db->delete('modules', array('module_name' => $this->mod_name));
		$this->EE->db->delete('extensions', array('class' => $this->ext_name));		
		$this->EE->db->delete('actions', array('class' => $this->mod_name));
		
		$this->EE->db->delete('actions', array('class' => $this->mod_name));
		$this->EE->db->delete('actions', array('class' => $this->mcp_name));
		
		foreach(array_keys($this->tables) as $table)
		{
			$this->EE->dbforge->drop_table($table);
		}
			
		return TRUE;
	}
	
	private function _set_defaults()
	{ 

	}
}