<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$lang = array(	
	'gmap_geocoder_module_name' => 
	'Geocoder',

	'gmap_geocoder_module_description'        => 
	'A configurable server-side geocoder that extends Google Maps for ExpressionEngine API',

	'module_home'                             => 'Back to Home',
	
	'gmap_geocoder_update_setting'            => 'Update Settings',
	'gmap_geocoder_save_setting'              => 'Save Settings',
	
	'gmap_geocoder_new'                       => 'New Geocoder Instance',
	'gmap_geocoder_new_instance'              => 'New Geocoder Instance',
	'gmap_geocoder_edit_instance'             => 'Edit Geocoder Instance',
	
	'gmap_geocoder_fields'                    => 'Geocode Fields',
	'gmap_geocoder_fields_desc'               => 'Enter the name of the form fields you wish to be used to build the address string which will be geocoded.',
	
	'gmap_geocoder_channel_names'             => 'Channel Names',
	'gmap_geocoder_channel_names_desc'        => 'Enter the names of the channels you wish be used for geocoding. You can enter multiple channels by delimiting them with a comma.',
	
	'gmap_geocoder_latitude_field_name'       => 'Latitude Field',
	'gmap_geocoder_latitude_field_name_desc'  => 'Enter the names of the field that will store the returned latitude.',
	
	'gmap_geocoder_longitude_field_name'      => 'Longitude Field',
	'gmap_geocoder_longitude_field_name_desc' => 'Enter the names of the field that will store the returned longitude.',
	
	'gmap_geocoder_gmap_field_name'      	  => 'Google Maps Field',
	'gmap_geocoder_gmap_field_name_desc' 	  => 'Enter the names of the Google Maps for ExpressionEngine fieldtype that will store the returned geocoder response.',
	
	'gmap_geocoder_delete_setting'            => 'Delete Setting',
	'gmap_geocoder_confirm_delete'            => 'Are you sure you want to delete this setting?',
	
	'gmap_geocoder_no_valid_fields'  		  => 'There are no valid geocoder fields.',
	'gmap_geocoder_no_valid_location'		  => '\'{location}\' is an invalid location\'',
	
	'gmap_geocoder_preserve_lat_lng'		  => 'Preserve Existing Coordinates?',
	'gmap_geocoder_preserve_lat_lng_desc'	  => 'If this setting is set to Yes, then Geocoder will only run for new entries and entries being editted without coordinates. If a coordinate exists, the geocoding process will be skipped.',

	'gmap_geocoder_throw_error_no_geocode_fields'      => 'Throw Errors',
	'gmap_geocoder_throw_error_no_geocode_fields_desc' => 'If this setting is set to Yes, then Geocoder will throw an error if no geocoder fields are present. If this is undesired behavior, set this setting to No.'
);

/* End of file lang.gmap_geocoder.php */
/* Location: /system/expressionengine/third_party/gmap_geocoder/language/english/lang.gmap_geocoder.php */
