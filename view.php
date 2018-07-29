<?php
if(!isset($_GET["id"])){
	echo "please enter a recipe as a GET variable";
	exit();
}

include("Parsedown.php");
include("conn.php");

$sql = "SELECT * FROM recipes WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $_GET["id"]);
$stmt->execute();

$stmt->setFetchMode(PDO::FETCH_ASSOC);
$results = $stmt->fetchAll();

if(sizeof($results) === 0){
	echo "I'm sorry, we don't have that recipe";
	exit();
}

$result = $results[0];

$sql = "SELECT * FROM uses WHERE recipe=?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $_GET["id"]);
$stmt->execute();

$stmt->setFetchMode(PDO::FETCH_ASSOC);
$ingredients = $stmt->fetchAll();


$parsedown = new Parsedown();
$parsedown->setSafeMode(true);

$sql = "SELECT time,note FROM notes WHERE recipe=?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $_GET["id"]);
$stmt->execute();

$stmt->setFetchMode(PDO::FETCH_ASSOC);
$notes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Recipe: <?php echo $result["name"]?></title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<meta charset="utf-8"/>
</head>
<body>
	<div id='main'>
		<h1><?php echo $result["name"];?></h1>
		<p>
			<em>For <?php echo $result["people"];?> people</em><br/>
			<em>Difficulty: <?php echo $result["difficulty"];?>, time: <?php echo $result["time"];?> minutes</em>
		</p>
		<h4>Ingredients</h4>
		<ul>
			<?php
			foreach($ingredients as $ingredient){
				$amount = rtrim($ingredient["amount"], 0);
				if(substr($amount, -1) === '.'){
					$amount = substr($amount, 0, sizeof($amount)-2);
				}
				echo "<li>".$amount.$ingredient["unit"]." ".$ingredient["ingredient"]."</li>";
			}
			?>
		</ul>
		<h4>Instructions</h4>
		<p><?php echo $parsedown->text($result["instructions"]);?></p>
		<h4>Notes</h4>
		<p>
			<ul>
				<?php
				foreach($notes as $note){
					echo "<li><b>".$note["time"]."</b>: ".$note["note"];
				}
				?>
			</ul>
		</p>
		<form action="addnote.php" method="POST">
			<textarea name="note" rows="10" style="width:100%"></textarea>
			<input type='hidden' name='recipe' value='<?php echo $_GET["id"];?>'/>
			<input type='submit'/>
		</form>
	</div>
</body>