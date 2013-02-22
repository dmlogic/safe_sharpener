<?php
/**
 * DM SafeSharpener for Expression Engine 2
 *
 * This software is copywright DM Logic Ltd
 * http://dmlogic.com
 *
 * You may use this software on commercial and
 * non commercial websites AT YOUR OWN RISK.
 * No warranty is provided nor liability accepted.
 *
 *
 */

$plugin_info = array(
  'pi_name' 		=> 'SafeSharpener',
  'pi_version' 		=> '1.0',
  'pi_author'	 	=> 'Darren Miller',
  'pi_author_url' 	=> 'http://dmlogic.com/',
  'pi_description' 	=> 'Adds field submission security to Safecracker',
  'pi_usage' 		=> Dm_safesharpener::usage()
);

class Dm_safesharpener {
	
	public $return_data = '';
	
	private $field_data = array();

	// -----------------------------------------------------------------

	function __construct() {
		
		// get EE
		$this->EE =& get_instance();
		
		// ensure we're not wasting our time here
		$key = $this->EE->config->item('encryption_key');
		if( empty($key) ) {
			exit('You must set an encryption key to use SafeSharpener');
		}
		
		// get the encryption library
		$this->EE->load->library('encrypt');
		
		// now create the hidden field
		$this->create_form_field();
	}
	
	// -----------------------------------------------------------------
	
	/**
	 * create_form_field
	 */
	private function create_form_field() {
		
		// find out common allowed fields
		$this->process_param('allowed_fields');
		
		// now fields for our member group
		$this->process_param('allowed_fields_'.$this->EE->session->userdata('group_id'));
		
		// allow us to have a form that doesn't use this
		$dis = strtolower($this->EE->TMPL->fetch_param('disable'));
		if( $dis == 'yes' || $dis == 'y') {
			$this->field_data['disable'] = 'yes';
		}
		
		if(empty($this->field_data)) {
			return;
		}
		
		$str = $this->EE->encrypt->encode( serialize($this->field_data) );
		
		$this->return_data = '<div class="hiddenFields"><input type="hidden" name="field_settings" value="'.$str.'" /></div>';
		
	}
	
	// -----------------------------------------------------------------
	
	/**
	 * process_param
	 * 
	 * Get data from template param and pass it to field data
	 * 
	 * @param string $name 
	 */
	private function process_param($name) {
		
		$data = $this->EE->TMPL->fetch_param($name);
		
		if(!empty($data)) {
			$this->field_data[$name] = explode('|',$data);
		}
	}
	 
	
	// -----------------------------------------------------------------
	
	/**
	 * usage
	 * 
	 * @return string 
	 */
	public function usage() {
		
		return 'See http://dmlogic.com/add-ons/safesharpener/';
	}
}