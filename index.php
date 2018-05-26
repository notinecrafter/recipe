<!DOCTYPE html>
<html>
<head>
	<title>Search for recipes</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" type="text/css" href="auto-complete.css">
	<meta charset="utf-8"/>
</head>
<body>
	<div id='main'>
		<h1>Welcome to the SFB Technologies Recipe Book</h1>
		<div id='search'>
			<form action="search.php" method="GET" autocomplete="off">
				Maximum difficulty: <input type='number' min='1' max='10' name='difficulty'/><br/>
				Maximum time (minutes): <input type='number' name='time'/><br/>
				<ul id='ingredients'>
					<li class='ingredient' id='ingredient0'>Ingredient: <input type='text' name='ingredient0'/><input type='button' value='-' onclick='removeIngredient(0)'/></li>
				</ul>
				<input type='button' value='+' onclick="addIngredient()"/><br/>
				<input type='hidden' name='ingredientCount' value='1' id='ingredientCount'/>
				Vegan: <input type='checkbox' name='vegan'/> 
				Vegetarian: <input type='checkbox' name='vegetarian'/>
				<input type='submit' value='search'/>
			</form>
		</div>
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
			console.log(elementName)
			var auto = new autoComplete({
				selector: 'input[name="'+elementName+'"]',
				source: function(term, response){
					console.log("asdf");
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
			var inner = "Ingredient: <input type='text' name='ingredient"+last+"'/><input type='button' value='-' onclick='removeIngredient("+last+")'/>";
			node.innerHTML = inner;
			list = document.getElementById("ingredients");
			list.appendChild(node);

			addAutoComplete("ingredient"+last);

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
				//also make sure the "-" button has the correct number
				var button = toDo.getElementsByTagName('input')[1];
				button.onclick = function(){removeIngredient(last-toGo);};

				var text = toDo.getElementsByTagName('input')[0];
				text.name = 'ingredient'+(last-toGo);
				console.log(text.name);

				toGo--;
			}

			last--;

			var amount = document.getElementById("ingredientCount");
			amount.value = parseInt(amount.value)-1;
		}
	</script>
</body>