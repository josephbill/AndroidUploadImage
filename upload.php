<?php

//importing dbdetails file

require_once 'dbdetails.php';

//this is the uploads folder

$upload_path = 'uploads/';

//getting the server ip 
$server_ip = gethostbyname(gethostname());

//creating the upload url 
$upload_url = 'http://'.$server_ip.'/AndroidImageUpload'.$upload_path;

//response array
$response = array();

if($_SERVER['REQUEST_METHOD']=='POST'){

	//checking the required parameters from the request 

if(isset($_POST['name'])) and isset($_FILES['image']['name'])){

//connecting to the database 
	$con = mysqli_connect(HOST,USER,PASS,DB) or die('unable to connect....');

	//getting name from the request 

	$name = $_POST['name'];

	//getting the file info. from the request

	$fileinfo = pathinfo($_FILES['images']['name']);

	//getting the file extension
	$extension = $fileinfo['extension'];

	//file url to store in the database

	$file_url = $upload_url. getFileName().'.'.$extension;

	//file path to upload in the server 

	$file_path = $upload_path. getFileName().'.'.$extension;

	//try saving file to directory

	try{
    //saving the file
		move_uploaded_file($_FILES['image']['tmp_name'], $file_path);
		$sql = "INSERT INTO `db_images`.`images` (`id` , `url` , `name`) VALUES (NULL, '$file_url', '$name' );";

	//adding the path and name to database 
		if (mysqli_query($con,$sql)) {
			# code...

			//filling response array with values 

			$response['error'] = false;
			$response['url'] = $file_url;
			$response['name'] = $name;

		}

		//if some error occurred 
     }catch(Exception $e){
        $response['error'] = true;
        $response['message'] = $e->getMessage(); 

     }

     //displaying the response 
     echo json_encode($response);

     //closing the connection 
     mysqli_close($con);
 }else{
$response['error']=true;
$response['message']='please choose a file';

 }

 //method to return file name for the image to be uploaded
 function getFileName(){
 $con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect...');
 $sql = "SELECT max(id) as id FROM images";
 $result = mysqli_fetch_array(mysqli_query($con,$sql));
 
 mysqli_close($con);
 if($result['id']==null)
 return 1; 
 else 
 return ++$result['id']; 
 }

