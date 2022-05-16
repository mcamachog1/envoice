<?php
$data = json_decode(file_get_contents('php://input'), false);

print_r($data);
print_r($data->title);


?>