<?php

//Connection à la bdd
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

//Obtenir les infos de l'utilisateur
function getUser($connexion, $session){
	$user = [];
	$req = 'SELECT *
			FROM compte, type
			WHERE type.id_type = compte.id_type and login = "'.$session['login'].'"';

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

//Obtenir tout les groupes
function getGroupe($connexion){
	$req = 'SELECT id_groupe, lib_groupe
			FROM groupe;';

	try{
		//Préparer et exécuter la requête
		$stmt = $connexion->query($req);
		//On récupère les données sous forme d'un tableau
		while($donnees = $stmt->fetch(PDO::FETCH_ASSOC)){
			$grp[]=$donnees;
		}
		//On ferme la base
		$stmt->closeCursor();
		return $grp;
	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}
}

//Obtenir le lib d'un groupe en fonction de son ID
function getLibGroupe($connexion, $id){
	$req = 'SELECT lib_groupe
			FROM groupe
			WHERE id_groupe = "'.$id.'";';

	try{
		//Préparer et exécuter la requête
		$stmt = $connexion->query($req);
		//On récupère les données sous forme d'un tableau
		while($donnees = $stmt->fetch(PDO::FETCH_ASSOC)){
			$grp=$donnees;
		}
		//On ferme la base
		$stmt->closeCursor();
		return $grp['lib_groupe'];
	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}
}

function getEtudNotInGroupe($connexion, $idgroupe){
	$etud = [];
	$req = 'SELECT DISTINCT id_compte, nom, prenom
			from compte
			where id_type = "3" and not id_compte in(
				select id_compte
				from detailgroupe
				where id_groupe = :id_groupe)
			order by nom';

	
	
	try{
		$stmt = $connexion->prepare($req);

		$stmt->bindValue(':id_groupe',$idgroupe,PDO::PARAM_STR);

		$stmt->execute();

		while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
			$etud[] = $data;
		}

		$stmt->closeCursor();
		
	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}



	return $etud;
}

function getEtudInGroupe($connexion, $idgroupe){
	$etud = [];
	$req = 'SELECT DISTINCT id_compte, nom, prenom
			from compte
			where id_type = "3" and id_compte in(
				select id_compte
				from detailgroupe
				where id_groupe = :id_groupe)
			order by nom';

	
	
	try{
		$stmt = $connexion->prepare($req);

		$stmt->bindValue(':id_groupe',$idgroupe,PDO::PARAM_STR);

		$stmt->execute();

		while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
			$etud[] = $data;
		}

		$stmt->closeCursor();
		
	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}



	return $etud;
}

function getNomPrenomEtud($connexion, $id){
	$req = 'SELECT nom, prenom
			FROM compte
			WHERE id_compte = "'.$id.'";';

	try{
		//Préparer et exécuter la requête
		$stmt = $connexion->query($req);
		//On récupère les données sous forme d'un tableau
		while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
			$nomP[]=$data;
		}
		//On ferme la base
		$stmt->closeCursor();
		return $nomP[0];
	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}
}

function addInGroupe($connexion, $id_compte, $id_groupe){
	$req = 'INSERT INTO detailgroupe (id_compte, id_groupe) VALUES
				(:id_compte, :id_groupe)
			ON DUPLICATE KEY UPDATE id_compte = :id_compte';

	try{
		//Préparer et exécuter la requête
		$stmt = $connexion->prepare($req);
		
		$stmt->bindValue(':id_compte',$id_compte, PDO::PARAM_STR);
		$stmt->bindValue(':id_groupe',$id_groupe, PDO::PARAM_STR);

		//Exécuter la requête
		$stmt->execute();

		//On ferme la base
		$stmt->closeCursor();

	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}
}

function removeFromGroupe($connexion, $id_compte, $id_groupe){
	$req = 'DELETE FROM detailgroupe
			WHERE id_compte = :id_compte and id_groupe = :id_groupe';

	try{
		//Préparer et exécuter la requête
		$stmt = $connexion->prepare($req);
		
		$stmt->bindValue(':id_compte',$id_compte, PDO::PARAM_STR);
		$stmt->bindValue(':id_groupe',$id_groupe, PDO::PARAM_STR);

		//Exécuter la requête
		$stmt->execute();

		//On ferme la base
		$stmt->closeCursor();

	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}
}

?>