<!DOCTYPE html>
<html>
<head>
	<title>Add an ingredient</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<meta charset="utf-8"/>
</head>
<body>
	<div id='main'>
		<?php
		if(isset($_POST['ingredient'])){
			include("conn.php");

			$sql = "INSERT INTO ingredients VALUES (?, ?);";
			$stmt = $conn->prepare($sql);
			$ingredient = strtolower($_POST['ingredient']);
			$stmt->bindParam(1, $ingredient);
			$stmt->bindParam(2, $_POST['class']);
			$succes = $stmt->execute();
			if($succes){
				echo "Ingredient added succesfully: ".$ingredient;
			}else{
				echo "Ingredient adding failed...";
			}
		}
		?>
		<form action='addingredient.php' method='POST'>
			<input type='text' name='ingredient' placeholder="ingredient">
			<select name='class'>
				<option value='V'>Vegan</option>
				<option value='v'>Vegetarian</option>
				<option value='f'>Fish</option>
				<option value='m'>Meat</option>
				<option value='o'>Other</option>
			</select>
			<input type='submit' value='add'/>
		</form>
	</div>
</body>