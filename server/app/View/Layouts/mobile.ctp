<html lang="">
    <head>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0" />
        <meta charset="utf-8" />
        <title>nÉTspresso</title>
        <link rel="stylesheet" type="text/css" href="css/mobile.css">
	    <script src="js/mobile.js"></script>
    </head>
	<body>

		<div id="container">
			<div id="header"></div>
			<div id="content">

				<?php echo $this->Session->flash(); ?>

				<?php echo $this->fetch('content'); ?>
			</div>
			<div id="footer"></div>
		</div>
		<?php echo $this->element('sql_dump'); ?>
	</body>
</html>