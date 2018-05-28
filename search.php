<?php
if(!isset($_GET)){
	echo "please give me GET data";
	exit();
}
include("conn.php");

$GLOBALS["sql"] = "SELECT name, id FROM recipes WHERE ";
$GLOBALS["first"] = true;
$GLOBALS["params"] = array();

function appendToSql($query, $value){
	if(!$GLOBALS["first"]){
		$GLOBALS["sql"] .= " AND ";
	}else{
		$GLOBALS["first"] = false;
	}

	$GLOBALS["sql"] .= $query;
	$GLOBALS["params"][] = $value;
}

if(isset($_GET["difficulty"]) && $_GET["difficulty"] !== ""){
	appendToSql("difficulty < ?", $_GET["difficulty"]);
}
if(isset($_GET["time"]) && $_GET["time"] !== ""){
	appendToSql("time < ?", $_GET["time"]);
}

for($i = 0; $i < (int)$_GET["ingredientCount"]; $i++){
	if(isset($_GET["ingredient".strval($i)]) && $_GET["ingredient".strval($i)] !== ""){
		appendToSql("id in (SELECT recipe FROM uses WHERE ingredient=?)", $_GET["ingredient".strval($i)]);
	}
}

if(isset($_GET["vegan"])){
	if(!$GLOBALS["first"]){
		$GLOBALS["sql"] .= " AND ";
	}else{
		$GLOBALS["first"] = false;
	}

	$GLOBALS["sql"] .= "NOT EXISTS (SELECT u.recipe FROM uses u, ingredients i WHERE i.name = u.ingredient AND (i.class = 'v' COLLATE utf8mb4_bin OR i.class = 'f' OR i.class = 'm') AND u.recipe = id AND u.optional = 0)";
}else if(isset($_GET["vegetarian"])){
	if(!$GLOBALS["first"]){
		$GLOBALS["sql"] .= " AND ";
	}else{
		$GLOBALS["first"] = false;
	}

	$GLOBALS["sql"] .= "NOT EXISTS (SELECT u.recipe FROM uses u, ingredients i WHERE i.name = u.ingredient AND (i.class = 'm' OR i.class = 'f') AND id = u.recipe AND u.optional = 0)";
}

$GLOBALS["sql"] .= " LIMIT 50;";

if($debug){
	echo $GLOBALS["sql"];
	echo "<br/>";
	var_dump($GLOBALS["params"]);
}

$stmt = $conn->prepare($sql);
for($i = 0;$i < sizeof($GLOBALS["params"]); $i++){
	$stmt->bindParam($i+1, $GLOBALS["params"][$i]);
}
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$results = $stmt->fetchAll();
?>
<!DOCTYPE html>
<head>
	<title>Search results - recipes</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<meta charset="utf-8"/>
</head>
<body>
	<div id="main">
		<h1>Search results</h1>
		<ul>
			<?php
				foreach($results as $result){
					echo "<li><a href='view.php?id=".$result["id"]."'>".$result["name"]."</a></li>";
				}
			?>
		</ul>
	</div>
</body>
