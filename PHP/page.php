<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
	<style type="text/css">
		table, th, td{
			border:black solid 1px;
		}
	</style>
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

		//Savoir si c'est un prof ou élève
		$session = $_SESSION;
		$user = getUser($connexion, $session)[0];
		//var_dump($user);
		$currentUser = $user['lib_type'];
		$loginUser = $user['login'];
		/*$compte = getCompte($connexionBDD);
		foreach ($compte as $key => $value) {
			if($value['login']==$_SESSION['login']){
				$currentUser = $value['type'];
				$loginUser = $value['login'];
			}
		}*/


		//$listProf[] = getListeProf($connexionBDD);

		//Modifier le keyuser en fonction de la personne
		/*if($currentUser == 'professeur'){	
			$keyU = getUserKeyProf($connexionBDD,$_SESSION['login']);	
			$keyUser = $keyU[0]['nom_prof'];
			$class = getClass($keyUser,$listProf);
		}else{
			$keyU = getUserKeyEtud($connexionBDD,$_SESSION['login']);
			$keyUser = $keyU[0]['nom_etudiant'];
		}*/


		$post = $_POST;

		if(isset($_POST['ajoutNote'])){
			//Ajoute une nouvelle note
			addNote($post, $connexionBDD, $class);
		}

		

		//Set css
		if($currentUser == 'professeur'){
			echo'<link rel="stylesheet" type="text/css" href="../CSS/modelProf.css"> ';
		}else{
			echo'<link rel="stylesheet" type="text/css" href="../CSS/modelEtudiant.css"> ';
		}

		//$listEleve[] = getListeEleve($connexionBDD);

		//Fermer la base
		//$connexionBDD=null;

	?>

	<?php 
	var_dump($user);
		//$noteEleve[] = getNoteEleve($connexionBDD);
		//Affiche tableaux des notes en fonction de si c'est un professeur ou un élève
		echo'<table id="tableEtudiant" style="width: 300px;margin:0 auto">
				<thead>
					<tr>
						<th colspan="2">'./*getPrenomNomProf($keyUser,$listProf)*/$user['prenom'].' '.$user['nom'].'<br/>'.$loginUser.'<br/>';
		if(is_file('../image/'.$loginUser.'.png')){
			echo '<img src="../image/'.$loginUser.'.png" style="width:200px;height:200px">';
		}else{
			echo '<img src="../image/Default.png" style="width:200px;height:200px">';
		}
		

		echo			'</th>
					</tr>
					<tr>
						<th>';
		if($currentUser == 'professeur'){
			echo 'Elève';
		}else if($currentUser == 'etudiant'){
			echo 'Matière';
		}
		echo'			</th>
						<th>Note</th>
					</tr>
				</thead>
				<tbody>';


		if($currentUser == 'professeur'){
			//Afficher les notes des élèves
			//afficheNoteEleve($connexion);
			$note = getNoteGroupe($connexion, $user);
			showNoteGroupe();

			
			/*foreach ($noteEleve[0] as $key => $value) {
				if($value['matiere_test'] == $class){
					echo '<tr>';
					echo '<td>'.$value['prenom_etudiant'].' '.$value['nom_etudiant'].'</td>';
					echo '<td>'.$value['valeur'].'</td>';
					echo '</tr>';
				}
			}*/

			/*echo '	</tbody>
				</table>';*/
			echo '	</tbody>
			</table>';
		}else if($currentUser == 'etudiant'){
			//Afficher les notes de l'élève
			$note = getNoteEtud($connexion, $user['id_compte']);
			showNoteEtud($connexion, $note, $user);
			echo '	</tbody>
			</table>';

		}
		/**/
	?>
	

	<!--<?php
		if ($currentUser == "professeur") {
			//Formulaire pour ajouter une nouvelle note
			echo '<form action="#" method="post">
					<fieldset style="width:350px;margin:0 auto"><legend>Ajouter une note</legend>
						<select name="eleve">';

			foreach ($listEleve[0] as $key => $value) {
				echo'<option value="'.$value['id_etudiant'].'">'.$value['prenom_etudiant'].' '.$value['nom_etudiant'].'</option>';
			}
			
			echo	'</select>
						<input type="number" name="valeur" id="valeur" placeholder="valeur" required="required" min="0" max="20">
						<input type="submit" name="ajoutNote" value="Ok">
					</fieldset>
				</form>';

			//Afficher les notes avec des spécifications
			//Formulaire avec une entrée
			//Vérification si une demande a été faite
			if(isset($_POST["AfficheNote"])){
				//Vérification si cet élève existe
				$req = 'SELECT nom_etudiant as nb
						FROM listeetudiant
						WHERE nom_etudiant = "'.$_POST['std'].'"';

				$sql = $connexionBDD->query($req);
				if($sql->rowCount()!=0){
					//Si existe	
					echo '<table id="tableEtudiant" style="width: 300px">
							<thead>
								<tr>
									<th colspan="2">
										Etudiant '.$_POST['std'].'
									</th>
								</tr>
							</thead>
							<tbody>';
					//Vérification si l'élève possède des notes
					$req2 = 'SELECT matiere_test, valeur
							FROM listeetudiant, listenote
							WHERE listeetudiant.id_etudiant = listenote.id_etudiant and nom_etudiant = "'.$_POST['std'].'"';
					$sql2 = $connexionBDD->query($req2);
					if($sql2->rowCount()!=0){
						//Si vrai
						foreach ($sql2 as $key2 => $value2) {
							echo '<tr>';
							echo '<td>'.$value2['matiere_test'].'</td>';
							echo '<td>'.$value2['valeur'].'</td>';
							echo '</tr>';
						}
						echo '<tr>';
						echo '<td>Moyenne</td>';
						$req3 = 'SELECT avg(valeur) as moyenne
								FROM listeetudiant, listenote
								WHERE listeetudiant.id_etudiant = listenote.id_etudiant and nom_etudiant = "'.$_POST['std'].'"
								GROUP BY listenote.id_etudiant';
						foreach ($connexionBDD->query($req3) as $key3 => $value3) {
							echo '<td>'.round($value3['moyenne'],2).'</td>';
						}
						
						echo '</tr>';
					}else{
						//Si faux
						echo '<tr><td colspan="2">No Data Found</td></tr>';
					}
									
					echo	'</tbody>
							';
				}else{
					//Si n'existe pas
					echo 'No Data Found';
				}
					
				
			}/*else{*/
				//Si aucune demande, on affiche le form
				echo '<form method="post" action="#">
							<fieldset style="width:350px;margin:0 auto">
								<legend>Note etudiant</legend>
								<label>Etudiant: </label><input type="text" name="std" placeholder="nom">
								<input type="submit" name="AfficheNote" value="OK">
							</fieldset>
						</form>';
			/*}*/

			//Formulaire avec deux entrées
			//Vérification si une demande a été faite
			if(isset($_POST["AfficheNote2"])){
				//Vérification si cet élève existe
				$reqEtud = 'SELECT nom_etudiant as nb
						FROM listeetudiant
						WHERE nom_etudiant = "'.$_POST['std2'].'"';

				//Vérification si la matière existe
				$reqMat = 'SELECT matiere_cours
						FROM listeprof
						WHERE matiere_cours ="'.$_POST['mat'].'"';

				$sqlEtud = $connexionBDD->query($reqEtud);
				$sqlMat = $connexionBDD->query($reqMat);
				if($sqlEtud->rowCount()==0 && $sqlMat->rowCount()==0){
					/*//Si pas de précision pour étudiant et matiere	
					echo '<table id="tableEtudiant" style="width: 300px">
						<tbody>';
					$reqNoteFull = 'SELECT prenom_etudiant, nom_etudiant,matiere_test, valeur
									FROM listeetudiant, listenote
									WHERE listeetudiant.id_etudiant = listenote.id_etudiant';
					$sqlNoteFull = $connexionBDD->query($reqNoteFull);

					foreach ($sqlNoteFull as $key => $value) {
						echo '<tr><td>'.$value['nom_etudiant'].' '.$value['prenom_etudiant'].'</td><td>'.$value['matiere_test'].'</td><td>'.$value['valeur'].'</td></tr>';
					}

									
					echo	'</tbody></table>';*/
					echo 'No Data Found';
				}else if($sqlEtud->rowCount()==0){
					//Si seulement la matière est renseigné
					echo '<table id="tableEtudiant" style="width: 300px">
						<tbody>';
					$reqNoteMat = 'SELECT prenom_etudiant, nom_etudiant,matiere_test, valeur
									FROM listeetudiant, listenote
									WHERE listeetudiant.id_etudiant = listenote.id_etudiant and matiere_test = "'.$_POST['mat'].'"
									ORDER BY nom_etudiant';
					$sqlNoteMat = $connexionBDD->query($reqNoteMat);
					foreach ($sqlNoteMat as $key => $value) {
						echo '<tr><td>'.$value['nom_etudiant'].' '.$value['prenom_etudiant'].'</td><td>'.$value['matiere_test'].'</td><td>'.$value['valeur'].'</td></tr>';
					}
					echo	'</tbody></table>
							';
				}else if($sqlMat->rowCount()==0){
					//Si seulement l'étudiant est renseigné
					echo '<table id="tableEtudiant" style="width: 300px">
						<tbody>';
					$reqNoteEtud = 'SELECT prenom_etudiant, nom_etudiant,matiere_test, valeur
									FROM listeetudiant, listenote
									WHERE listeetudiant.id_etudiant = listenote.id_etudiant and nom_etudiant = "'.$_POST['std2'].'"
									ORDER BY matiere_test';
					$sqlNoteEtud = $connexionBDD->query($reqNoteEtud);
					foreach ($sqlNoteEtud as $key => $value) {
						echo '<tr><td>'.$value['nom_etudiant'].' '.$value['prenom_etudiant'].'</td><td>'.$value['matiere_test'].'</td><td>'.$value['valeur'].'</td></tr>';
					}
					echo	'</tbody></table>
							';
				}else{
					//Si les deux sont renseignés
					echo '<table id="tableEtudiant" style="width: 300px">
						<tbody>';
					$reqNoteEtudMat = 'SELECT prenom_etudiant, nom_etudiant,matiere_test, valeur
									FROM listeetudiant, listenote
									WHERE listeetudiant.id_etudiant = listenote.id_etudiant and nom_etudiant = "'.$_POST['std2'].'" and matiere_test = "'.$_POST['mat'].'"';
					$sqlNoteEtudMat = $connexionBDD->query($reqNoteEtudMat);
					foreach ($sqlNoteEtudMat as $key => $value) {
						echo '<tr><td>'.$value['nom_etudiant'].' '.$value['prenom_etudiant'].'</td><td>'.$value['matiere_test'].'</td><td>'.$value['valeur'].'</td></tr>';
					}
					echo	'</tbody></table>
							';
				}		
				
			}/*else{*/
				//Si aucune demande, on affiche le form
				echo '<form method="post" action="#">
							<fieldset style="width:350px;margin:0 auto">
								<legend>Note etudiants et matières</legend>
								<label>Etudiant: </label><input type="text" name="std2" placeholder="nom"><br/>
								<label>Matière: </label><input type="text" name="mat" placeholder="matière">
								<input type="submit" name="AfficheNote2" value="OK">
							</fieldset>
						</form>';
			/*}*/
		}
	?>-->
	
	<?php 
		echo '<a href="logout.php"><p style="text-align: center">Déconnexion</p></a>';
	?>

</body>
</html>