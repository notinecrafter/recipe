<?php
if(!isset($_GET['input']) || $_GET['input'] === ""){
	echo "please enter an input query";
	exit();
}

header('Content-Type: application/json');

include("conn.php");

$sql = "SELECT DISTINCT name FROM ingredients WHERE name like :term LIMIT 10";

$stmt = $conn->prepare($sql);
$term = $_GET['input'].'%';
$stmt->bindParam(":term", $term);
$stmt->execute();

$stmt->setFetchMode(PDO::FETCH_ASSOC);
$results = $stmt->fetchAll();

$output = array();

foreach($results as $result){
	array_push($output, $result["name"]);
}

echo json_encode($output);
?>