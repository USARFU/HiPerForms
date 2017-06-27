<?php include ("dbaccess.php");
    
    //This is a bridge script that calls FileMaker::getContainerData with the provided url.
    
	if (isset($_GET['path'])){
		$url = $_GET['path'];
		$url = substr($url, 0, strpos($url, "?"));
		$url = substr($url, strrpos($url, ".") + 1);
		if($url == "jpg"){
			header('Content-type: image/jpeg');
		}
		else if($url == "png"){
			header('Content-type: image/png');
		} 
		else if($url == "pdf") {
			header('Content-type: application/pdf');
			if (isset($_GET['filename'])) {
				$filename = urlencode($_GET['filename']);
				header("Content-disposition: filename=$filename");
			}
		}
		else{
			header('Content-type: application/octet-stream');
		}
		echo $fm->getContainerData($_GET['path']);
	}
?>
