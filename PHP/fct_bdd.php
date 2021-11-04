<?php

function connect_bd(){
	$dsn="mysql:dbname=".BASE.";host=".SERVER;
	try{
		$connexion=new PDO($dsn,USER,PASSWD);
	}
	catch(PDOException $e){
		printf("Echec de la connexion : %s\n", $e->getMessage());
		exit();
	}
	$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $connexion;
}

//Obtenir les comptes utilisateur
function getCompte($connexionBDD){
	$compte=[];
	$req = 'SELECT *
			FROM compte';
	try{
		//Préparer et exécuter la requête
		$stmt = $connexionBDD->query($req);
		//On récupère les données sous forme d'un tableau
		while($donnees = $stmt->fetch(PDO::FETCH_ASSOC)){
			$compte[]=$donnees;
		}
		//On ferme la base
		$stmt->closeCursor();
		return $compte;
	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}
}



function addNewUser($connexion, $post){
	if(isset($_POST["NewAccount"])){
		//Check si il n'y a pas de caractère spéciaux
		if(ctype_alpha($_POST['loginInsc'])){
			//Check si le mdp est uniquement avec des chiffres
			if(is_numeric($_POST['passwdInsc'])){
				//Check si la confirmation est égale au mdp
				if($_POST['passwdInsc'] == $_POST['passwdConfInsc']){
					//$connexion = connect_bd();
					$comptePresent = getCompte($connexion);
					$freeLogin = true;
					//Check si le login est libre
					foreach ($comptePresent as $key => $value) {
						if($value['login'] == $_POST['loginInsc']){
							$freeLogin = false;
						}
					}
					if($freeLogin == true){
						//Ajout des éléments dans les tables
						setCompte($connexion, $post);
						echo '<script>alert ("Nouveau compte");</script>';

					}else{
						echo '<script>alert ("Login déjà existant!");</script>';
					}
				}else{
					echo '<script>alert ("Confirmation du mot de passe incorrect!");</script>';
				}
			}else{
				echo '<script>alert ("Mot de passe incorrect!");</script>';
			}
		}else{
			echo '<script>alert ("Login incorrect!");</script>';
		}
	}
}

function setCompte($connexion,$post){

	$req = 'INSERT INTO compte (login,passwd,id_type, nom, prenom) VALUES
				(:login,:passwd,:type,:nom,:prenom)';
	try{
		//Préparer et exécuter la requête
		$stmt = $connexion->prepare($req);
		
		$stmt->bindValue(':login',$post['loginInsc'], PDO::PARAM_STR);
		$stmt->bindValue(':passwd',$post['passwdInsc'], PDO::PARAM_STR);
		$stmt->bindValue(':type',$post['typeInsc'], PDO::PARAM_STR);
		$stmt->bindValue(':nom',$post['nomInsc'], PDO::PARAM_STR);
		$stmt->bindValue(':prenom',$post['prenomInsc'], PDO::PARAM_STR);

		//Exécuter la requête
		$stmt->execute();

		//On ferme la base
		$stmt->closeCursor();

	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}
}

function getUser($connexion, $session){
	$user = [];
	$req = 'SELECT *
			FROM compte, type
			WHERE type.id_type = compte.id_type and login = "'.$session['login'].'"';

	/*try{
		$stmt = $connexion->prepare($req);

		$stmt->bindValue(':login', $session['login'], PDO::PARAM_STR);

		$stmt->execute();

		$stmt->closeCursor();

		return $stmt;
	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}*/

	try{
		//Préparer et exécuter la requête
		$stmt = $connexion->query($req);
		//On récupère les données sous forme d'un tableau
		while($donnees = $stmt->fetch(PDO::FETCH_ASSOC)){
			$user[]=$donnees;
		}
		//On ferme la base
		$stmt->closeCursor();
		return $user;
	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}
}



?>