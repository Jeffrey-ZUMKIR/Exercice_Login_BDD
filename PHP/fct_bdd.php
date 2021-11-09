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

//Mutateur
//Vérification pour ajouter un nouveau compte
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

//Ajouter un nouveau compte
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

//Supprimer une note en fonction de son id
function deleteNote($connexion, $id_note){
	$req = 'DELETE FROM note WHERE id_note = :id_note';

	try{
		//requête préparée
		$stmt = $connexion->prepare($req);

		//Avec bindvalue
		$stmt->bindValue(':id_note',$id_note,PDO::PARAM_STR);

		//Exécuter la requête
		$stmt->execute();
	}
	catch(PDOException $e){
		echo 'Erreur : '.$e->getMessage();
	}
}

//Ajouter une note (valeur, id_matiere, id_compte et une description)
function addNote($connexion, $post, $mat){
	$req = 'INSERT INTO note (valeur, id_matiere, id_compte, description) VALUES 
			(:valeur,:id_matiere,:id_compte,:description)';

	try{
		//requête préparée
		$stmt = $connexion->prepare($req);

		//Avec bindvalue
		$stmt->bindValue(':valeur',$post['valeur'],PDO::PARAM_STR);
		$stmt->bindValue(':id_matiere',$mat['id_matiere'],PDO::PARAM_STR);
		$stmt->bindValue(':id_compte',$post['etudiant'],PDO::PARAM_STR);
		$stmt->bindValue(':description',$post['nomEval'],PDO::PARAM_STR);

		//Exécuter la requête
		$stmt->execute();

		//On indique que l'insertion s'est bien passée
		echo '<script>alert("Insertion de la nouvelle note effectuée");</script>';
	}
	catch(PDOException $e){
		echo 'Erreur : '.$e->getMessage();
	}
}

//Assesseur
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

//Obtenir toute les matieres de la bdd
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

//Obtenir une matiere en fonction de l'id de la matiere
function getMatiereFromId($connexion, $id){
	$req = 'SELECT id_matiere, lib_matiere
			FROM matiere
			WHERE id_matiere = "'.$id.'";';

	try{
		//Préparer et exécuter la requête
		$stmt = $connexion->query($req);
		//On récupère les données sous forme d'un tableau
		while($donnees = $stmt->fetch(PDO::FETCH_ASSOC)){
			$mat[]=$donnees;
		}
		//On ferme la base
		$stmt->closeCursor();
		return $mat[0];
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

//Obtenir les élèves du prof
function getEtudGroupProf($connexion, $user){
	$groupe = getGroupe($connexion);
	foreach ($groupe as $key => $value) {
		$req = 'SELECT compte.id_compte as id_compte, nom, prenom
				FROM compte, detailgroupe, groupe, enseignement
				WHERE compte.id_compte = detailgroupe.id_compte and groupe.id_groupe = detailgroupe.id_groupe and groupe.id_groupe = enseignement.id_groupe and enseignement.id_compte = :id_compte and groupe.lib_groupe = :lib_groupe and enseignement.id_groupe = groupe.id_groupe;';
		try{
			$stmt = $connexion->prepare($req);

			$stmt->bindValue(':id_compte',$user['id_compte'],PDO::PARAM_STR);
			$stmt->bindValue(':lib_groupe',$value['lib_groupe'],PDO::PARAM_STR);

			$stmt->execute();

			while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
				$etudGroupe[$value['id_groupe']][$data['id_compte']] = $data;
			}

			$stmt->closeCursor();
			
		}
		catch(PDOException $e){
			echo "Erreur : ".$e->getMessage();
		}
	}
	return $etudGroupe;
}

//Obtenir tout les enseignements
function getEnseignement($connexion, $user){
	$req = 'SELECT *
			FROM enseignement
			WHERE id_compte = "'.$user["id_compte"].'";';

	try{
		//Préparer et exécuter la requête
		$stmt = $connexion->query($req);
		//On récupère les données sous forme d'un tableau
		while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
			$enseign[]=$data;
		}
		//On ferme la base
		$stmt->closeCursor();
		return $enseign;
	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}
}

//Obtenir les notes de tout les élèves d'un prof
function getNoteGroupe($connexion, $user){
	$note = [];
	
	$etudGroupe = getEtudGroupProf($connexion, $user);
	//var_dump($etudGroupe);

	$enseignement = getEnseignement($connexion, $user);
	//var_dump($enseignement);

	//$mat = getMatiereFromId($connexion, $value)

	foreach ($etudGroupe as $keyGroupe => $value) {
		foreach ($value as $keyId_compte => $value2) {
			foreach ($enseignement as $key => $value3) {
				//var_dump(getMatiereFromId($connexion, $value3["id_matiere"]));
				$note[$keyGroupe][$keyId_compte] = getNoteEtud($connexion, $keyId_compte, getMatiereFromId($connexion, $value3["id_matiere"]));
				
			}
			//$note[$keyGroupe][$keyId_compte] = getNoteEtud($connexion, $keyId_compte);
		}
	}
	return $note;
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

//Obtenir le nom et prenom d'un compte en fonction de son id
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

//Obtenir toute les notes de l'élève dans chaque matiere
function getAllNoteEtud($connexion, $id_compte){
	$note = [];
	$matiere = getMatiere($connexion);
	foreach ($matiere as $key => $value) {
		$note[$value['lib_matiere']]= getNoteEtud($connexion, $id_compte, $value);
	}
	return $note;
}

//Obtenir toute les notes d'un élève dans une matiere
function getNoteEtud($connexion, $id_compte, $theMat){
	//var_dump($theMat);
	$note = [];
	$req = 'SELECT valeur, id_note, description
			FROM note, matiere 
			WHERE note.id_matiere = matiere.id_matiere and id_compte = :id_compte and matiere.id_matiere = :id_matiere;';
	try{
		$stmt = $connexion->prepare($req);

		$stmt->bindValue(':id_compte',$id_compte,PDO::PARAM_STR);
		$stmt->bindValue(':id_matiere',$theMat['id_matiere'],PDO::PARAM_STR);

		$stmt->execute();

		while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
			$note[] = $data;
		}

		$stmt->closeCursor();
		
	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}



	return $note;
}

//Obtenir la matiere que le prof enseigne à un groupe
function getMatProf($connexion, $user, $idgroupe){
	$mat = [];
	$req = 'SELECT matiere.id_matiere
			FROM matiere, enseignement, groupe, compte
			WHERE compte.id_compte = enseignement.id_compte and groupe.id_groupe = enseignement.id_groupe and matiere.id_matiere = enseignement.id_matiere and compte.id_compte = :id_compte and groupe.id_groupe = :id_groupe;';
	try{
		$stmt = $connexion->prepare($req);

		$stmt->bindValue(':id_compte',$user["id_compte"],PDO::PARAM_STR);
		$stmt->bindValue(':id_groupe',$idgroupe,PDO::PARAM_STR);

		$stmt->execute();

		while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
			$mat[] = $data;
		}

		$stmt->closeCursor();
		
	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}



	return $mat[0];
}


function getMatEtud($connexion, $user, $id_compte){
	$mat = [];
	$req = 'SELECT matiere.id_matiere
			FROM enseignement, matiere, groupe, detailgroupe
			WHERE detailgroupe.id_compte = :id_etud and detailgroupe.id_groupe = groupe.id_groupe and groupe.id_groupe = enseignement.id_groupe and enseignement.id_matiere = matiere.id_matiere and enseignement.id_compte = :id_prof; ';

	try{
		$stmt = $connexion->prepare($req);

		$stmt->bindValue(':id_prof',$user["id_compte"],PDO::PARAM_STR);
		$stmt->bindValue(':id_etud',$id_compte,PDO::PARAM_STR);

		$stmt->execute();

		while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
			$mat[] = $data;
		}

		$stmt->closeCursor();
		
	}
	catch(PDOException $e){
		echo "Erreur : ".$e->getMessage();
	}



	return $mat[0];
}

//Affichage des notes des élèves du prof
function showNoteGroupe($connexion, $note){
	foreach ($note as $keyGroupe => $value) {
		echo '<table style="width: 90%; margin: 0 auto">
				<thead>
					<tr>
						<th colspan="2">'.getLibGroupe($connexion, $keyGroupe).'</th>
					</tr>
					<tr>
						<th style="width: 65%">Elève</th>
						<th>Note</th>
					</tr>
				</thead>
				<tbody>';
		foreach ($value as $keyEtud => $value2) {
			echo '<tr>';
			
			if(!empty($value2)){
				echo '<td rowspan = "'.sizeof($value2).'">'.getNomPrenomEtud($connexion,$keyEtud)['nom'].' '.getNomPrenomEtud($connexion,$keyEtud)['prenom'].'</td>';
				foreach ($value2 as $keyNote => $value3) {
					echo '<td>'.$value3['valeur'].'</td></tr><tr>';
				}
			}else{
				echo '<td>'.getNomPrenomEtud($connexion,$keyEtud)['nom'].' '.getNomPrenomEtud($connexion,$keyEtud)['prenom'].'</td>';
				echo '<td>No data</td></tr><tr>';
			}

			echo '</tr>';
		}




		echo '	</tbody>
			</table>';
	}
}

//Affichage des notes d'un élève
function showNoteEtud($connexion, $note, $user){
	echo '<table style="width: 90%;margin:0 auto">
			<thead>
				<tr>
					<th>Matière</th>
					<th>Note</th>
				</tr>
			</thead>';
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
	echo '	</tbody>
		</table>';
}

?>