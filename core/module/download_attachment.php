<?php
$path = "https://kaizen2.net/file/attach/" . $_POST['id'] . ".deploy";
$name = $_POST['name'];

header('Content-Type: application/octet-stream');
header('Content-Length: '.strlen(file_get_contents($path)));
header('Content-Disposition: attachment; filename*=UTF-8\'\'' . rawurlencode($name) );

ob_end_clean();
readfile($path,FILE_BINARY);
exit;
?>