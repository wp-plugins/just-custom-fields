<div class="wrap">
	<div class="icon32 icon32-posts-page" id="icon-edit"><br></div>
	<h2><?php _e('Just Custom Fields', JCF_TEXTDOMAIN); ?></h2>
	<p><?php _e('You should choose Custom Post Type first to configure fields:', JCF_TEXTDOMAIN); ?></p>
	<ul class="dotted-list jcf-bold">
	<?php foreach($post_types as $key => $obj) : ?>
		<li style="width:300px;"><a href="?page=just_custom_fields&amp;pt=<?php echo $key; ?>"><?php echo $obj->label; ?></a>  <a style="font-weight:normal;float:right;" href="<?php echo home_url('/') . PLUGINDIR . '/just-custom-fields/export.php'; ?>?pt=<?php echo $key; ?>">export</a></li>
	<?php endforeach; ?>
	</ul>
	<div class="form">
		<br/>
		<hr/>
		<br/>
		<h3><?php _e('Import field settings', JCF_TEXTDOMAIN); ?></h3>
		<form action="options-general.php?page=just_custom_fields" method="post" enctype="multipart/form-data">
			<fieldset>
				<?php global $just_import_error, $just_import_message;
					if( !empty($just_import_error) ) : ?>
				<div class="error"><p class="message"><?php _e('Unable upload file.', JCF_TEXTDOMAIN); ?></p></div>
				<?php endif; ?>
				<?php if( !empty($just_import_message) ) : ?>
					<div class="updated"><p class="message"><?php
						if( $just_import_message == 'empty' ) _e('Nothing to import', JCF_TEXTDOMAIN);
						elseif( $just_import_message == 'done' ) _e('Fields imported successfully.', JCF_TEXTDOMAIN);
					?></p></div>
				<?php endif; ?>
				<p>
					<label><?php _e('Import custom fields settings:', JCF_TEXTDOMAIN); ?></label><br/>
					<input type="file" name="import_file" value="" size="40" />
				</p>
				<p>
					<label><?php _e('Custom Post Type to import to:', JCF_TEXTDOMAIN); ?></label><br/>
					<select name="import_pt">
						<option value=""></option>
						<?php foreach($post_types as $key => $obj) : ?>
						<option value="<?php echo $key; ?>"><?php echo $obj->label; ?></option>
						<?php endforeach; ?>
					</select>
					<br/><small><?php _e('* leave blank to import settings "as is" from the file.', JCF_TEXTDOMAIN); ?></small>
				</p>
				<p><input type="submit" class="button-primary" name="import_submitted" value="<?php _e('Run Import', JCF_TEXTDOMAIN); ?>" /></p>
			</fieldset>
		</form>
	</div>
</div>
