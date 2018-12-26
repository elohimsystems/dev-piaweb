<?PHP

$file = $_GET['file'];
$uuid = $_GET['uuid'];
if(file_exists ('files/' . $uuid . '/' . $file)){
	$data['id']=1;
	$data['name']=$file;
	$data['uuid']=$uuid;
}
else{
	$data['id']=null;
	$data['name']=null;
	$data['uuid']=null;
}
header('Content-type: application/json');	
echo json_encode( [$data] );

