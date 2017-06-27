<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Rugby Camp Info Sheet</title>
</head>

<body>
<!-- Error Codes 281-282 -->
<?php
include_once "header.php";

$DocumentPath = urlencode( $documentRecord->getField('Document') );
$Topic = $documentRecord->getField('Topic');
$filename = $Topic . ".pdf";

header("location: ../include/ContainerBridge.php?path=" . $DocumentPath . "&filename=" . $filename);
exit();
?>

</body>
</html>