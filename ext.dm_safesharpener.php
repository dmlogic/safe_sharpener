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
class Dm_safesharpener_ext {

	public $settings        = array();
    public $name            = 'SafeSharpener';
    public $description     = 'Adds field submission security to Safecracker';
    public $settings_exist  = 'n';
    public $docs_url        = 'http://dmlogic.com';
	public $version			= '1.0';

	// -----------------------------------------------------------------
 
	// the fields we always let through
	private $fields = array(
		'ACT',
		'RET',
		'URI',
		'XID',
		'return_url',
		'author_id',
		'channel_id',
		'entry_id',
		'site_id',
		'return',
		'json',
		'dynamic_title',
		'error_handling',
		'preserve_checkboxes',
		'secure_return',
		'allow_comments',
		'rules'
	);
	
	// -----------------------------------------------------------------

	function __construct() {
		$this->EE =& get_instance();
	}
	
	// -----------------------------------------------------------------
	
	/**
	 * safecracker_submit_entry_start
	 * 
	 * Hook into the start of SafeCracker and remove any unwanted post vars
	 * 
	 */
	public function safecracker_submit_entry_start() {
		
		// allow us to bail from the whole procedure
		if(false === $this->process_settings()) {
			unset($_POST['field_settings']);
			return;
		}
		
		// store our current POST data
		$old_post = $_POST;
		
		// clear the POST array completely
		$_POST = array();
		
		// now re-instate with allowed data.
		foreach($this->fields as $f ) {
			if(isset($old_post[$f])) {
				$_POST[$f] = $old_post[$f];
			}
		}
	}
	
	// -----------------------------------------------------------------
	
	/**
	 * process_settings
	 * 
	 * Extract our allowed field settings
	 * 
	 * @return boolean 
	 */
	private function process_settings() {
		
		// get our allowed fields
		$settings = $this->EE->input->post('field_settings');
		
		if(empty($settings)) {
			return true;
		}
		
		// decrypt what we have
		$this->EE->load->library('encrypt');
		$settings = $this->EE->encrypt->decode($settings);
		$settings = unserialize($settings);
		
		if(empty($settings)) {
			return true;
		}
		
		// this means we don't want to do anything
		if(isset($settings['disable'])) {
			return false;
		}
		
		// the fields we always allow
		if(isset($settings['allowed_fields']) && !empty($settings['allowed_fields'])) {
			$this->fields = array_merge( $this->fields, $settings['allowed_fields'] );
		}
		
		// the fields for this member group
		$fname = 'allowed_fields_'.$this->EE->session->userdata('group_id');
		if(isset($settings[$fname]) && !empty($settings[$fname])) {
			$this->fields = array_merge( $this->fields, $settings[$fname] );
		}
		
		return true;
	}

	// -----------------------------------------------------------------

	/**
	 * Enable the extension
	 */
	public function activate_extension() {

		$this->EE->db->insert('extensions', array(
			'extension_id' => '',
			'class'	=> __CLASS__,
			'method' => "safecracker_submit_entry_start",
			'hook' => "safecracker_submit_entry_start",
			'settings' => '',
			'priority' => 10,
			'version'	=> $this->version,
			'enabled'	=> "y")
		);
	}

	// -----------------------------------------------------------------

	/**
	 * Disable the extension
	 */
	public function disable_extension() {

		$this->EE->db->where('class', __CLASS__);
    	$this->EE->db->delete('extensions');
	}

	// -----------------------------------------------------------------

	/**
	 * Update extension
	 */
	public function update_extension($current='') {

		if ($current == '' OR $current == $this->version) {
			return FALSE;
		}

		$data = array();
		$data['version'] = $this->version;
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->update('extensions', $data);
	}
}