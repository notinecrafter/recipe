<?php
if(!isset($_POST["note"]) || !isset($_POST["recipe"])){
	echo "please enter the correct POST parameters";
	exit();
}
include("conn.php");

$sql = "INSERT INTO notes(recipe, note) VALUES (?,?);";
$stmt = $conn->prepare($sql);
$note = $_POST["note"];
$id = htmlspecialchars($_POST["recipe"]);
$stmt->bindParam(1,$id);
$stmt->bindParam(2,$note);
try{
	$stmt->execute();
	header('Location: view.php?id='.$_POST["recipe"]);
}catch(PDOException $e){
	echo "Something went wrong";
	if(debug){
		echo $e->getMessage();
	}
}
?>