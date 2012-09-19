<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Convert from EPUB to KOBO EPUB</title>
	<?php echo Asset::css('bootstrap.css'); ?>
	<style>
	</style>
</head>
<body>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
			<h1>Convert from EPUB to KOBO EPUB</h1>
			
			<p>Upload EPUB file (max 10MB)</p>
			
			<?php if (isset($error)): ?>
			<p class="alert alert-error"><?php echo $error; ?></p>
			<?php endif; ?>
			
			<?php
			echo Form::open(
				array(
					'name' => 'upload',
					'enctype' => 'multipart/form-data',
				)
			);
			?>
			<?php echo Form::file('file'); ?>
			<?php echo Form::submit('submit', 'Upload', array('class' => 'btn')); ?>
			<?php echo Form::close(); ?>
			</div>
		</div>
	</div>
</body>
</html>
