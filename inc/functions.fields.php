<?php
	
	/**
	 *	register field in global variable. contain info like id_base, title and class name
	 */
	function jcf_field_register( $class_name ){
		global $jcf_fields;
		
		// check class exists and try to create class object to get title
		if( !class_exists($class_name) ) return false;
		
		$field_obj = new $class_name();
		//pa($field_obj,1);

		$field = array(
			'id_base' => $field_obj->id_base,
			'class_name' => $class_name,
			'title' => $field_obj->title,
		);
		
		$jcf_fields[$field_obj->id_base] = $field;
	}
	
	/**
	 *	return array of registered fields (or concrete field by id_base)
	 */
	function jcf_get_registered_fields( $id_base = '' ){
		global $jcf_fields;
		
		if( !empty($id_base) ){
			return @$jcf_fields[$id_base];
		}
		
		return $jcf_fields;
	}
	
	/**
	 *	set fields in wp-options
	 */
	function jcf_field_settings_update( $key, $values = array() ){
		$option_name = jcf_fields_get_option_name();

		$field_settings = get_option($option_name, array());
		if( $values === NULL && isset($field_settings[$key]) ){
			unset($field_settings[$key]);
		}
		
		if( !empty($values) ){
			$field_settings[$key] = $values;
		}
		
		update_option($option_name, $field_settings);
	}
	
	/**
	 *	get fields from wp-options
	 */
	function jcf_field_settings_get( $id = '' ){
		$option_name = jcf_fields_get_option_name();
		
		$field_settings = get_option($option_name, array());
		
		if(!empty($id)){
			return @$field_settings[$id];
		}
		
		return $field_settings;
	}
	
	/**
	 *	init field object
	 */
	function jcf_init_field_object( $field_mixed, $fieldset_id = '' ){
		// $field_mixed can be real field id or only id_base
		$id_base = preg_replace('/\-([0-9]+)/', '', $field_mixed);
		$field = jcf_get_registered_fields( $id_base );
		
		$field_obj = new $field['class_name']();
		
		$field_obj->set_fieldset( $fieldset_id );
		$field_obj->set_id( $field_mixed );
		
		return $field_obj;
	}
	
	/**
	 * get next index for save new instance
	 */
	function jcf_get_fields_index( $id_base ){
		$option_name = 'jcf_fields_index';
		$indexes = get_option($option_name, array());
		
		// get index, increase on 1
		$index = (int)@$indexes[$id_base];
		$index ++;
		
		// update indexes
		$indexes[$id_base] = $index;
		update_option($option_name, $indexes);
		
		return $index;
	}
	
	// option name in wp-options table
	function jcf_fields_get_option_name(){
		$post_type = jcf_get_post_type();
		return 'jcf_fields-'.$post_type;
	}
	
	/**
	 *	parse "Settings" param for checkboxes/selects/multiple selects
	 */
	function jcf_parse_field_settings( $string ){
		$values = array();
		$v = explode("\n", $string);
		foreach($v as $val){
			$val = trim($val);
			if(strpos($val, '|') !== FALSE ){
				$a = explode('|', $val);
				$values[$a[0]] = $a[1];
			}
			elseif(!empty($val)){
				$values[$val] = $val;
			}
		}
		$values = array_flip($values);
		return $values;
	}
	
	/**
	 *	Import fields from xml data
	 */
	function jcf_fields_import( $xml_data ){
		if( !class_exists('just_xml') ){
			require_once( JCF_ROOT . '/inc/class.xml.php' );
		}
		
		$xml_parser = new just_xml();
		$xml = $xml_parser->xml2arr($xml_data);
		
		if( empty($xml['custom_post_type'][0]['#']['fieldsets'][0]['#']['fieldset']) ){
			return 'empty';
		}
		
		$xml_fieldsets = $xml['custom_post_type'][0]['#']['fieldsets'][0]['#']['fieldset'];
		$xml_field_settings = $xml['custom_post_type'][0]['#']['field_settings'][0]['#']['field_setting'];
		
		$post_type = $xml['custom_post_type'][0]['#']['name'][0]['#'];
		if( !empty($_POST['import_pt']) ){
			$post_type = $_POST['import_pt'];
		}
		
		jcf_set_post_type($post_type);
		
		$import_fieldsets = array();
		$import_field_settings = array();
		
		foreach($xml_fieldsets as $_fieldset){
			$_fieldset = $_fieldset['#'];
			$fieldset = array(
				'id' => $_fieldset['id'][0]['#'],
				'title' => $_fieldset['title'][0]['#'],
				'fields' => array(),
			);
			foreach($_fieldset['fields'][0]['#']['field'] as $_field){
				$fieldset['fields'][ $_field['@']['id'] ] = $_field['@']['enabled'];
			}
			
			$import_fieldsets[ $fieldset['id'] ] = $fieldset;
		}
		
		foreach($xml_field_settings as $_field_setting){
			$_field_setting = $_field_setting['#'];
			$field_setting = array();
			foreach($_field_setting as $var => $xml_value){
				$field_setting[$var] = $xml_value[0]['#'];
			}
			
			$import_field_settings[ $field_setting['id'] ] = $field_setting;
		}
		
		$db_fieldsets = jcf_fieldsets_get();
		$db_field_settings = jcf_field_settings_get();
		
		//pa( array($import_fieldsets, $import_field_settings) );
		//pa( array($db_fieldsets, $db_field_settings), 1 );
	
		// remove fields with same slug
		foreach($import_field_settings as $field){
			foreach($db_fieldsets as $db_fs_id => $db_fs){
				foreach($db_fs['fields'] as $f_id => $f_enabled){
					$f_params = $db_field_settings[$f_id];
					if( strcmp($f_params['slug'], $field['slug']) == 0 ){
						$field_obj = jcf_init_field_object($f_id, $db_fs_id);
						$field_obj->do_delete();
					}
				}
			}
		}
	
		$db_fieldsets = jcf_fieldsets_get();
		$db_field_settings = jcf_field_settings_get();
		
		//pa( array($db_fieldsets, $db_field_settings), 1 );
	
		// insert new fieldset / fields
		foreach($import_fieldsets as $fieldset){
			// create new fieldset if not exist
			if( !isset($db_fieldsets[ $fieldset['id'] ]) ){
				$db_fieldset = array(
					'id' => $fieldset['id'],
					'title' => $fieldset['title'],
					'fields' => array(),
				);
			}
			// if exist - take instance
			else{
				$db_fieldset = $db_fieldsets[ $fieldset['id'] ];
			}
			
			// go through fields
			// to import field we need: 1) create new instance with new number; 2) link with fieldset
			foreach($fieldset['fields'] as $f_id => $enabled){
				$import_field = $import_field_settings[$f_id];
				list( $id_base, $number ) = explode('-', $f_id, 2);
				// generate new number
				$number = jcf_get_fields_index($id_base);
				$db_f_id = $id_base.'-'.$number;
				
				$db_field = $import_field;
				$db_field['id'] = $db_f_id;
				
				// insert new field
				jcf_field_settings_update($db_f_id, $db_field);
				
				// add to fieldset
				$db_fieldset['fields'][$db_f_id] = $enabled;
			
			} // end foreach($fieldset['fields'])
	
			// update fieldset
			jcf_fieldsets_update($fieldset['id'], $db_fieldset);
		
		} // end foreach($import_fieldsets)
		
		return 'done';
	}

?>