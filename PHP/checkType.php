<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<?php
		include 'fct_bdd.php';

		define('USER',"root");
		define('PASSWD',"");
		define('SERVER',"localhost");
		define('BASE',"exlogin");

		//Connexion à la base
		$connexion = connect_bd();

		$session = $_SESSION;
		$user = getUser($connexion, $session)[0];
		var_dump($user);
		if($user['lib_type'] == "professeur"){
			header("Location: ./prof.php");
		}else if($user['lib_type'] == "etudiant"){
			header("Location: ./etudiant.php");
		}else if($user['lib_type'] == "admin"){
			header("Location: ./admin.php");
		}
		
	?>
</body>
</html>