<html>
	<head>
		<title>Upload</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<link rel="stylesheet" href="<?php echo $RESOURCE_URL ?>style.css">
		<script>
			function check() {
				var file = document.getElementById("file");
				if (!file || file.files.length == 0 || file.files[0].name.length == 0) return false;
				return true;
			}
		</script>
	</head>
	<body>
		<div id="title">Upload file to "<?php echo $FOLDER_NAME;?>":</div>
		<form name="upload" method="post" enctype="multipart/form-data" action="<?php echo $UPLOAD_URL;?>" onsubmit="return check();">
			<input id="file" name="uploader-http[]" type="file"></input>
			<input type="submit" value="Upload"></input>
		</form>
	</body>
</html>