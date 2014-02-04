<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
    </div>
    <?php if ($error_warning) { ?>
        <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <div class="box">
		<div class="heading">
			<h1><img src="view/image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
			<div class="buttons">
				<a onclick="$('#act_mode').val('1');$('#form').submit();" class="button"><?php echo $button_save_and_close; ?></a>
				<a onclick="$('#act_mode').val('0');$('#form').submit();" class="button"><?php echo $button_save; ?></a>
				<a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<div id="tabs" class="htabs"><a href="#tab-general"><?php echo $tab_general; ?></a><a href="#tab-data"><?php echo $tab_data; ?></a></div>
			<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<div id="languages" class="htabs">
						<?php foreach ($languages as $language) { ?>
							<a href="#language<?php echo $language['language_id']; ?>"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
						<?php } ?>
					</div>
					<?php foreach ($languages as $language) { ?>
						<div id="language<?php echo $language['language_id']; ?>">
							<table class="form">
								<tr>
									<td><span class="required">*</span> <?php echo $entry_name; ?></td>
									<td>
										<input type="text" name="description[<?php echo $language['language_id']; ?>][name]" size="100" value="<?php echo isset($description[$language['language_id']]) ? $description[$language['language_id']]['name'] : ''; ?>" />
										<?php if (isset($error_name[$language['language_id']])) { ?>
											<span class="error"><?php echo $error_name[$language['language_id']]; ?></span>
										<?php } ?>
									</td>
								</tr>
								<tr>
									<td><?php echo $entry_description; ?></td>
									<td><input type="text" name="description[<?php echo $language['language_id']; ?>][description]" size="100" value="<?php echo isset($description[$language['language_id']]) ? $description[$language['language_id']]['description'] : ''; ?>" /></td>
								</tr>
							</table>
						</div>
					<?php } ?>
				</div>
				<div id="tab-data">
					<table class="form">
						<tr>
							<td><?php echo $entry_image; ?></td>
							<td valign="top">
								<div class="image">
									<img src="<?php echo $thumb; ?>" alt="" id="thumb" />
									<input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
									<br />
									<a onclick="image_upload('image', 'thumb');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $no_image; ?>'); $('#image').attr('value', '');"><?php echo $text_clear; ?></a>
								</div>
							</td>
						</tr>
                        <tr>
                            <td><?php echo $entry_filename; ?></td>
                            <td>
                                <input type="text" name="filename" value="<?php echo $filename; ?>" /> <a id="button-upload" class="button"><?php echo $button_upload; ?></a>
                                <?php if ($error_filename) { ?>
                                    <span class="error"><?php echo $error_filename; ?></span>
                                <?php } ?>
                                <input type="hidden" name="filesize" value="<?php echo $filesize; ?>" />
                            </td>
                        </tr>
						<tr>
							<td><?php echo $entry_category; ?></td>
							<td>
								<select name="category_id">
									<option value="0"><?php echo $text_none; ?></option>
									<?php foreach ($categories as $category) { ?>
										<?php if ($category['category_id'] == $category_id) { ?>
											<option value="<?php echo $category['category_id']; ?>" selected="selected"><?php echo $category['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</td>
						</tr>
                        <tr>
                            <td><?php echo $entry_mask; ?></td>
                            <td><input type="text" name="mask" value="<?php echo $mask; ?>" />
                                <?php if ($error_mask) { ?>
                                <span class="error"><?php echo $error_mask; ?></span>
                                <?php } ?></td>
                        </tr>
						<tr>
							<td><?php echo $entry_sort_order; ?></td>
							<td><input type="text" name="sort_order" value="<?php echo $sort_order; ?>" size="1" /></td>
						</tr>
						<tr>
							<td><?php echo $entry_status; ?></td>
							<td>
								<select name="status">
									<?php if ($status) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
					</table>
				</div>
				<input type="hidden" name="act_mode"  id ="act_mode" value="0"/>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script> 
<script type="text/javascript"><!--
<?php foreach ($languages as $language) { ?>
	CKEDITOR.replace('description<?php echo $language['language_id']; ?>', {
		filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
		filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
		filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
		filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
		filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
		filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
	});
<?php } ?>
//--></script>
<script type="text/javascript"><!--
function image_upload(field, thumb) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '<?php echo $text_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val()),
					dataType: 'text',
					success: function(data) {
						$('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},	
		bgiframe: false,
		width: 800,
		height: 400,
		resizable: false,
		modal: false
	});
};
//--></script>
<script type="text/javascript" src="view/javascript/jquery/ajaxupload.js"></script>
<script type="text/javascript"><!--
    new AjaxUpload('#button-upload', {
        action: 'index.php?route=downloads/downloadsitems/upload&token=<?php echo $token; ?>',
        name: 'file',
        autoSubmit: true,
        responseType: 'json',
        onSubmit: function(file, extension) {
            $('#button-upload').after('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');
            $('#button-upload').attr('disabled', true);
        },
        onComplete: function(file, json) {
            $('#button-upload').attr('disabled', false);

            if (json['success']) {
                alert(json['success']);

                $('input[name=\'filename\']').attr('value', json['filename']);
                $('input[name=\'mask\']').attr('value', json['mask']);
                $('input[name=\'filesize\']').attr('value', json['filesize']);
            }

            if (json['error']) {
                alert(json['error']);
            }

            $('.loading').remove();
        }
    });
    //--></script>
<script type="text/javascript"><!--
$('#tabs a').tabs(); 
$('#languages a').tabs();
//--></script> 
<?php echo $footer; ?>