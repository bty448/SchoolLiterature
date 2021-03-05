<?php
	$pdo = new PDO('pgsql:host=ec2-54-247-96-169.eu-west-1.compute.amazonaws.com;port=5432;dbname=dags7aq80us7h1;user=yxbepyzyvugolg;password=9fb6a5e67b085cbf0526e455abc74dfeaec80f25cedf15bc22a363c5858679f9');
	
	$defaulNoteString = "g5=#g6=#g7=#g8=#g9=#g10=#g11=#";

	if(isset($_POST['username']) && isset($_POST['password'])) //return user data
	{
		$username = $_POST['username'];
		$password = saltPassword($_POST['password']);
		$query = $pdo->prepare("SELECT * FROM users WHERE username='".$username."' AND password='".$password."'");
		if(userIsset($query))
		{
			$query->execute();
			$resRow = $query->fetch(PDO::FETCH_ASSOC);
			echo json_encode($resRow);
		}
		else
		{
			$errorStr = "Wrong username or password!";
			echo $errorStr;
		}
	}

	if(isset($_POST['user_id']) && !isset($_POST['edit_name']) && !isset($_POST['new_note_str'])) //return user data
	{
		$user_id = $_POST['user_id'];
		$query = $pdo->prepare("SELECT * FROM users WHERE user_id='".$user_id."'");
		if(userIsset($query))
		{
			$query->execute();
			$resRow = $query->fetch(PDO::FETCH_ASSOC);
			echo json_encode($resRow);
		}
		else
		{
			$errorStr = "Wrong username or password!";
			echo $errorStr;
		}
	}
	
	if(isset($_POST['user_id']) && isset($_POST['edit_name']) && isset($_POST['edit_surname']) && isset($_POST['edit_grade'])) //update user data
	{
		$user_id = $_POST['user_id'];
		$name = $_POST['edit_name'];
		$surname = $_POST['edit_surname'];
		$grade = $_POST['edit_grade'];

		$queryName = $pdo->prepare("UPDATE users SET name='".$name."' WHERE user_id='".$user_id."'");
		$querySurname = $pdo->prepare("UPDATE users SET surname='".$surname."' WHERE user_id='".$user_id."'");
		$queryGrade = $pdo->prepare("UPDATE users SET grade='".$grade."' WHERE user_id='".$user_id."'");

		$queryName->execute();
		$querySurname->execute();
		$queryGrade->execute();
	}
	

	if(isset($_POST['new_username']) && isset($_POST['new_password']) && isset($_POST['name']) && isset($_POST['surname']) && isset($_POST['grade'])) // add new user in "users"
	{
		$new_username = $_POST['new_username'];
		$new_password = saltPassword($_POST['new_password']);
		$name = $_POST['name'];
		$surname = $_POST['surname'];
		$grade = $_POST['grade'];
		$query = $pdo->prepare("SELECT * FROM users WHERE username='".$new_username."'");
		if(!userIsset($query))
		{
			$qstr = "INSERT INTO users (username, password, name, surname, grade, notes) VALUES ('".$new_username."', '".$new_password."', '".$name."', '".$surname."', '".$grade."', '".$defaulNoteString."')";
			$addQuery = $pdo->prepare($qstr);
			$addQuery->execute();
			$returnQuery = $pdo->prepare("SELECT * FROM users WHERE username='".$new_username."'");
			$returnQuery->execute();
			$resRow = $returnQuery->fetch(PDO::FETCH_ASSOC);
			echo json_encode($resRow);
		}
		else
		{
			$errorStr = "User with this username already exists!";
			echo $errorStr;
		}
	}

	function userIsset($query)
	{
		$query->execute();
		$cnt = $query->rowCount();
		if($cnt > 0)
			return true;
		else
			return false;
	}
	
	function saltPassword($password)
	{
		$salt = "KDSfdfkkDfuf98d3Nch";
		$hash = md5($salt.$password);
		return $hash;
	}

	//////////////////////////////////////////BOOK NAMES SERVING////////////////////////////////////////////////


	if(isset($_GET['grade']) && isset($_GET['addNames']) && isset($_GET['addAuthors'])) // query structure: "name1;name2;name3;"
	{
		$grade = $_GET['grade'];
		$names = $_GET['addNames'];
		$authors = $_GET['addAuthors'];

		$checkQuery = $pdo->prepare("SELECT * FROM texts WHERE grade='".$grade."'");
		if(gradeIsset($checkQuery))
		{
			$queryNames = $pdo->prepare("UPDATE texts SET textNames='".$names."' WHERE grade='".$grade."'");
			$queryAuthors = $pdo->prepare("UPDATE texts SET textAuthors='".$authors."' WHERE grade='".$grade."'");

			$queryNames->execute();
			$queryAuthors->execute();
		}
		else
		{
			$query = $pdo->prepare("INSERT INTO texts VALUES ('".$grade."', '".$names."', '".$authors."')");
			$query->execute();
		}
	}

	if(isset($_POST['grade']) && !isset($_POST['new_username'])) //return array of text names and authors
	{
		$grade = $_POST['grade'];
		$query = $pdo->prepare("SELECT * FROM texts WHERE grade='".$grade."'");
		if(userIsset($query))
		{
			$query->execute();
			$resRow = $query->fetch(PDO::FETCH_ASSOC);
			echo json_encode($resRow);
		}
		else
		{
			$errorStr = "error";
			echo $errorStr;
		}
	}
	
	function gradeIsset($query)
	{
		$query->execute();
		$cnt = $query->rowCount();
		if($cnt > 0)
			return true;
		else
			return false;

	}

	//////////////////////////////////////////NOTES SERVING////////////////////////////////////////////////

	if(isset($_POST['user_id']) && isset($_POST['new_note_str'])) // query structure: "name1;name2;name3;"
	{
		$user_id = $_POST['user_id'];
		$new_note_str = $_POST['new_note_str'];

		$updNoteStr = $pdo->prepare("UPDATE users SET notes='".$new_note_str."' WHERE user_id='".$user_id."'");
		$updNoteStr->execute();
	}

?>