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

function getMatiere($connexion){
	$req = 'SELECT id_matiere, lib_matiere
			FROM matiere;';

	try{
		//Préparer et exécuter la requête
		$stmt = $connexion->query($req);
		//On récupère les données sous forme d'un tableau
		while($donnees = $stmt->fetch(PDO::FETCH_ASSOC)){
			$mat[]=$donnees;
		}
		//On ferme la base
		$stmt->closeCursor();
		return $mat;
	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}
}

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

function getEtudGroupProf($connexion, $user){
	$groupe = getGroupe($connexion);
	foreach ($groupe as $key => $value) {
		$req = 'SELECT compte.id_compte as id_compte, nom, prenom
				FROM compte, detailgroupe, groupe, enseignement
				WHERE compte.id_compte = detailgroupe.id_compte and groupe.id_groupe = detailgroupe.id_groupe and groupe.id_groupe = enseignement.id_groupe and enseignement.id_compte = :id_compte and groupe.lib_groupe = :lib_groupe;';
		try{
			$stmt = $connexion->prepare($req);

			$stmt->bindValue(':id_compte',$user['id_compte'],PDO::PARAM_STR);
			$stmt->bindValue(':lib_groupe',$value['lib_groupe'],PDO::PARAM_STR);

			$stmt->execute();

			while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$etudGroupe[$value['lib_groupe']][$data['id_compte']] = $data;
			}

			$stmt->closeCursor();
			
		}
		catch(PDOException $e){
			echo "Erreur : ".$e->getMessage();
		}
	}
	return $etudGroupe;
}

function getNoteGroupe($connexion, $user){
	$note = [];
	
	$etudGroupe = getEtudGroupProf($connexion, $user);

	foreach ($etudGroupe as $keyGroupe => $value) {
		foreach ($value as $keyId_compte => $value2) {
			$note[$keyGroupe][$keyId_compte] = getNoteEtud($connexion, $keyId_compte);
		}
	}
}

function showNoteGroupe(){

}

function getNoteEtud($connexion, $id_compte){
	$note = [];
	$matiere = getMatiere($connexion);
	foreach ($matiere as $key => $value) {
		$req = 'SELECT valeur
				FROM note, matiere 
				WHERE note.id_matiere = matiere.id_matiere and id_compte = :id_compte and matiere.id_matiere = :id_matiere;';
		try{
			$stmt = $connexion->prepare($req);

			$stmt->bindValue(':id_compte',$id_compte,PDO::PARAM_STR);
			$stmt->bindValue(':id_matiere',$value['id_matiere'],PDO::PARAM_STR);

			$stmt->execute();

			while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$note[$value['lib_matiere']][] = $data;
			}

			$stmt->closeCursor();
			
		}
		catch(PDOException $e){
			echo "Erreur : ".$e->getMessage();
		}
	}
	return $note;
}

function showNoteEtud($connexion, $note, $user){
	if(count($note) != 0){
		foreach ($note as $key => $value) {
			echo '<tr>';
			echo '<td rowspan="'.count($value).'">'.$key.'</td>';
			foreach ($value as $key2 => $value2) {				
				echo '<td>'.$value2['valeur'].'</td></tr><tr>';	
			}
			echo '</tr>';
		}

		echo '<td>Moyenne</td>';
		//Afficher la moyenne général
		$req = 'SELECT avg(valeur) as moyenne
				FROM note
				WHERE id_compte = :id_compte;';

		try{
			$stmt = $connexion->prepare($req);

			$stmt->bindValue(':id_compte',$user['id_compte'],PDO::PARAM_STR);

			$stmt->execute();

			while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$moyenne[] = $data;
			}

			$stmt->closeCursor();
		}
		catch(PDOException $e){
			echo "Erreur : ".$e->getMessage();
		}

		foreach ($moyenne as $key => $value) {
			echo '<td>'.round($value['moyenne'],2).'</td>';
		}
				
		echo '</tr>';
	}else{
		echo '<tr>';
		echo '<td colspan = "2">No Data Found</td>';
		echo '</tr>';
	}
}

?>