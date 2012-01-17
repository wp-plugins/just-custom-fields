<?php

	$root = dirname(__FILE__);
	while( basename($root) != 'wp-content' ){
		$root = dirname($root);
	}
	include_once( dirname($root).'/wp-config.php' );
	
	$post_types = jcf_get_post_types( 'object' );
	if( !empty($_GET['pt']) && isset($post_types[ $_GET['pt'] ]) ){
		$post_type = $post_types[ $_GET['pt'] ];
	}
	
	if( empty($post_type) ) wp_redirect( home_url('/').'wp-admin/options-general.php?page=just_custom_fields', 404 );
	
	jcf_set_post_type( $post_type->name );

	$fieldsets = jcf_fieldsets_get();
	$field_settings = jcf_field_settings_get();
	
	header('Content-Type: text/xml; charset=utf-8');
	header('Content-Disposition:attachment; filename="jcf_'.$post_type->name.'.xml"');
?>
<?php echo '<?xml'; ?> version="1.0"?>
<custom_post_type>
	<name><?php echo $post_type->name; ?></name>
	<label><?php echo $post_type->labels->name; ?></label>
	<fieldsets>
		<?php foreach($fieldsets as $fieldset): ?><fieldset>
			<id><?php echo $fieldset['id']; ?></id>
			<title><?php echo $fieldset['title']; ?></title>
			<fields>
				<?php foreach($fieldset['fields'] as $field => $enabled) : ?><field id="<?php echo $field; ?>" enabled="<?php echo (string)$enabled; ?>"></field>
				<?php endforeach; ?></fields>
		</fieldset>
	<?php endforeach; ?>
	
	</fieldsets>
	<field_settings>
		<?php foreach($field_settings as $field_id => $field) : ?><field_setting>
				<id><?php echo $field_id; ?></id>
				<?php foreach($field as $var => $value) : ?><<?php echo $var; ?>><![CDATA[<?php echo $value; ?>]]></<?php echo $var; ?>>
				<?php endforeach ?></field_setting>
		<?php endforeach; ?>
		
	</field_settings>
</custom_post_type>