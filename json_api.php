<?

$json = file_get_contents('url_here');
$obj = json_decode($json);
echo $obj->access_token;

?>