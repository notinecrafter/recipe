<!DOCTYPE html>
<html>
<head>
	<title>Add a recipe</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" type="text/css" href="auto-complete.css">
	<meta charset="utf-8"/>
</head>
<body>
	<div id='main'>
		<?php
			if(isset($_POST['instructions'])){
				include("conn.php");
				if(!is_null($password)){
					if(hash('sha256', $_POST["password"]) !== $password){
						exit("wrong password");
					}
				}
				try{
					$conn->beginTransaction();

					$sql = "INSERT INTO recipes(name, people, difficulty, time, category, instructions) VALUES (:name, :people, :difficulty, :time, :category, :instructions);";

					$stmt = $conn->prepare($sql);
					$name = htmlspecialchars($_POST["name"]);
					$stmt->bindParam(":name", $name);
					$stmt->bindParam(":people", $_POST["people"]);
					$stmt->bindParam(":difficulty", $_POST["difficulty"]);
					$stmt->bindParam(":time", $_POST["time"]);
					$category = htmlspecialchars($_POST["category"]);
					$stmt->bindParam(":category", $category);
					$instructions = htmlspecialchars($_POST["instructions"]);
					$stmt->bindParam(":instructions", $instructions);

					if($stmt->execute()){
						echo "recipe inserted";
					}else{
						echo "failure in executing: ";
						exit();
					}

					//to insert the ingredients, we need the id of the thing we just inserted. This should always be the highest id; therefore,
					$sql = "SELECT id FROM recipes ORDER BY id DESC LIMIT 1";
					$stmt = $conn->prepare($sql);
					$stmt->execute();
					$stmt->setFetchMode(PDO::FETCH_ASSOC);
					$results = $stmt->fetchAll();
					$id = $results[0]["id"];

					//now, for the ingredients
					$ingredientCount = intval($_POST["ingredientCount"]);
					for($i = 0; $i < $ingredientCount; $i++){
						$sql = "INSERT INTO uses(recipe, ingredient, amount, unit, optional) VALUES(:recipe, :ingredient, :amount, :unit, :optional);";
						$stmt = $conn->prepare($sql);
						$stmt->bindParam(":recipe", $id);
						$stmt->bindParam(":ingredient", $_POST["ingredient".strval($i)]);
						$stmt->bindParam(":amount", $_POST["amount".strval($i)]);
						$stmt->bindParam(":unit", $_POST["unit".strval($i)]);
						if(isset($_POST['optional'.strval($i)])){
							$optional = 1;
						}else{
							$optional = 0;
						}
						$stmt->bindParam(":optional", $optional);

						$stmt->execute();
					}
					$conn->commit();
				}catch(Exception $e){
					var_dump($e->getMessage());
				}
			}
		?>
		<h1>Add a recipe</h1>
		<form action='add.php' method='POST' autocomplete="off">
			Name: <input type='text' name='name' onkeypress="return event.keyCode != 13;"><br/>
			People: <input type='number' name='people' onkeypress="return event.keyCode != 13;"><br/>
			Difficulty (1-10): <input type='number' name='difficulty' max='10' min='1' onkeypress="return event.keyCode != 13;">
			Time (minutes): <input type='number' name='time' onkeypress="return event.keyCode != 13;">
			Category: <input type='text' name='category' maxlength="30" onkeypress="return event.keyCode != 13;">
			<ul id='ingredients'>
				<li class='ingredient' id='ingredient0'>Ingredient: <input type='text' name='ingredient0' onkeypress="return event.keyCode != 13;"/> Amount: <input type='number' name='amount0' step='.001' onkeypress="return event.keyCode != 13;"/> Unit:<input type='text' maxlength="5" name='unit0' onkeypress="return event.keyCode != 13;"/> Optional:<input type='checkbox' name='optional0' onkeypress="return event.keyCode != 13;"/><input type='button' value='-' onclick='removeIngredient(0)'/></li>
			</ul>
			<input type='button' onclick='addIngredient()'value = "+"/><br/>
			<input type='hidden' id='ingredientCount' name='ingredientCount' value='1'>
			Instructions<br/>
			<textarea name='instructions' rows='20' style="width:100%"></textarea><br/>
			<input type='password' name='password' autocomplete="on"/>
			<input type='submit' value='add'/><br/>
			<p><a href='addingredient.php' target='_blank'>Ingrediënt not listed?</a></p>
	</div>
	<script type="text/javascript" src='auto-complete.min.js'></script>
	<script type="text/javascript">

		//stolen from https://stackoverflow.com/questions/12460378/how-to-get-json-from-url-in-javascript
		getJSON = function(url, callback) {
		    var xhr = new XMLHttpRequest();
		    xhr.open('GET', url, true);
		    xhr.responseType = 'json';
		    xhr.onload = function() {
		      var status = xhr.status;
		      if (status === 200) {
		        callback(null, xhr.response);
		      } else {
		        callback(status, xhr.response);
		      }
		    };
		    xhr.send();
		};

		autocomplete = new autoComplete({
			selector: 'input[name="ingredient0"]',
			source: function(term, response){
				getJSON("autocomplete.php?input="+term, function(status, data){response(data);});
		    }
		});

		function addAutoComplete(elementName){
			var auto = new autoComplete({
				selector: 'input[name="'+elementName+'"]',
				source: function(term, response){
					getJSON("autocomplete.php?input="+term, function(status, data){response(data);});
			    }
			});

		}

		last = 0;
		function addIngredient(){
			last++;
			var node = document.createElement("LI");
			node.className += 'ingredient';
			node.id = 'ingredient'+last;
			var inner = "Ingredient: <input type='text' name='ingredient"+last+"'/>Amount: <input type='number' name='amount"+last+"' step='.001' onkeypress='return event.keyCode != 13;'> Unit:<input type='text' maxlength='5' name='unit"+last+"' onkeypress='return event.keyCode != 13;'>Optional:<input type='checkbox' name='optional"+last+"'onkeypress='return event.keyCode != 13;'/><input type='button' value='-' onclick='removeIngredient("+last+")'/>";
			node.innerHTML = inner;
			list = document.getElementById("ingredients");
			list.appendChild(node);

			//give this element an autocomplete
			addAutoComplete("ingredient"+last);

			//update the amount counter
			var amount = document.getElementById("ingredientCount");
			amount.value = parseInt(amount.value)+1;
		}
		function removeIngredient(number){
			var node = document.getElementById("ingredient"+number);
			node.parentElement.removeChild(node);

			//make sure to decrement nodes above
			var toGo = last-number;
			while(toGo > 0){
				var toDo = document.getElementById("ingredient"+(last-toGo+1));
				toDo.id = "ingredient"+(last-toGo);

				var inputs = toDo.getElementsByTagName('input');
				//also make sure the "-" button has the correct number
				var button = inputs[4];
				button.onclick = function(){removeIngredient(last-toGo);};

				var text = inputs[0];
				text.name = 'ingredient'+(last-toGo);

				inputs[1].name = 'amount'+(last-toGo);
				inputs[2].name = 'unit'+(last-toGo);
				inputs[3].name = 'optional'+(last-toGo);

				toGo--;
			}

			last--;

			var amount = document.getElementById("ingredientCount");
			amount.value = parseInt(amount.value)-1;
		}

	</script>
</body>