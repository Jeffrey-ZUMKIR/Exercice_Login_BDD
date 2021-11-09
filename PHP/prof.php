<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
	<script src="https://code.jquery.com/jquery-3.5.0.js"></script>
	<link rel="stylesheet" type="text/css" href="../CSS/style.css">
	<link rel="stylesheet" type="text/css" href="../CSS/modelProf.css">
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


		$post = $_POST;

		if(isset($_POST['ajoutNote'])){
			//Ajoute une nouvelle note
			addNote($post, $connexionBDD, $class);
		}

		

		//Set css
		/*if($currentUser == 'professeur'){
			echo'<link rel="stylesheet" type="text/css" href="../CSS/modelProf.css"> ';
		}else{
			echo'<link rel="stylesheet" type="text/css" href="../CSS/modelEtudiant.css"> ';
		}*/


	?>
	<div class="row">
		<div class="column">
	<?php
		if(isset($_POST["delNote"])){
			$post = $_POST['noteDel'];

			foreach ($post as $key => $value) {
				deleteNote($connexion, $value);
			}

			echo '<script>alert("Note supprimée!");</script>';

		}
	?>


	<?php 
		if(isset($_POST["AddNote"])){
			addNote($connexion, $post, getMatProf($connexion, $user, $_POST["groupe2"]));
		}



		//Affiche tableaux des notes en fonction de si c'est un professeur ou un élève
		echo'<table id="tableEtudiant" style="width: 90%; margin: 0 auto">
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
				</thead>
			</table>';


		if($currentUser == 'professeur'){
			//Afficher les notes des élèves
			$note = getNoteGroupe($connexion, $user);
			showNoteGroupe($connexion, $note);
		}

		echo '<a href="logout.php"><p style="text-align: center">Déconnexion</p></a>';
	?>
	
	<script type="text/javascript">
		/*function changeSelect(){
			$.ajax({
				type: "POST",
				url: 'page.php', // Here is your link which will give you desired values.
				success: function(data){
				    var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				    	if (this.readyState == 4 && this.status == 200) {
				        	document.getElementById("txtHint").innerHTML = this.responseText;
				        }
				    };
				    xmlhttp.open("GET", "gethint.php?q=" + str, true);
				    xmlhttp.send();
				},
				error: function(){
					alert(data);
				}
			});
		}*/
	</script>
	</div>
	<div class="column">
	<?php
		
		echo '<fieldset style="width:90%;margin: 0 auto"><legend>Ajouter une note</legend>';
		echo '<form action="#" method="post">
				<fieldset style="width:90%;margin:0 auto"><legend>Choix de groupe</legend>
					<select name="groupe" onchange="changeSelect()">';

		foreach ($note as $key => $value) {
			echo'<option value="'.$key.'"> '.getLibGroupe($connexion, $key).' </option>';
		}
		echo '		</select>
					<input type="submit" name="WhichGroupe">
				</fieldset>
			</form>';

		if(isset($_POST["WhichGroupe"])){
			//$groupe = $_POST["groupe"];
			echo '<form action="#" method="post">
					<fieldset style="width:90%;margin:0 auto"><legend>Choix d\'élève</legend>
						<input type="hidden" name="groupe2" value = "'.$_POST["groupe"].'">
						<select name="etudiant" onchange="changeSelect()">';

			foreach ($note[$_POST["groupe"]] as $key => $value) {
				echo'<option value="'.$key.'"> '.getNomPrenomEtud($connexion, $key)['nom'].' '.getNomPrenomEtud($connexion,$key)['prenom'].' </option>';
			}

			echo '		</select>
						<input type="text" name="nomEval" placeholder="Nom eval" required="required">
						<input type="number" name="valeur" id="valeur" placeholder="valeur" required="required" min="0" max="20">
						<input type="submit" name="AddNote">
					</fieldset>
				</form>';
		}
		echo '</fieldset>';

	?>
	<?php

		echo '<form action="#" method="post">
				<fieldset style="width:90%;margin:0 auto"><legend>Afficher note élève (work in progress)</legend>
					<select name = "etudiants[]" multiple>';

		foreach ($note as $key => $value) {
			echo '<optgroup label="'.getLibGroupe($connexion, $key).'">';
			foreach ($value as $key2 => $value2) {
				echo '<option value = "'.$key.' '.$key2.'">'.getNomPrenomEtud($connexion, $key2)['nom'].' '.getNomPrenomEtud($connexion, $key2)['prenom'].'</option>';
			}
			echo '</optgroup>';
		}


		echo '		</select>
					<input type="submit" name="ShowNote">
				</fieldset>
			</form>';

		if(isset($_POST["ShowNote"])){
			$notebis = [];
			foreach ($_POST["etudiants"] as $key => $value) {
				$groupNote[] = explode(' ',$value);
			}
		}
	?>
		</div>
		<div class="column">
			<?php
				echo '<fieldset style="width:90%;margin:0 auto"><legend>Supprimer une note</legend>
						<form action = "#" method = "post">
							<fieldset style="width:90%;margin:0 auto"><legend>Choisir l\'élève</legend>
								<select name = "myStudent">';

				foreach ($note as $key => $value) {
					foreach ($value as $key2 => $value2) {
						echo '<option value = "'.$key2.'">'.getNomPrenomEtud($connexion, $key2)['nom'].' '.getNomPrenomEtud($connexion, $key2)['prenom'].'</option>';
					}
					
				}


				echo '			</select>
								<input type = "submit" name = "etudDel">
							</fieldset>
						</form>';


				if(isset($_POST['etudDel'])){
					$noteEtud = getNoteEtud($connexion, $_POST['myStudent'], getMatEtud($connexion, $user, $_POST['myStudent']));

					echo '	<form action = "#" method = "post">
								<fieldset style="width:90%;margin:0 auto"><legend>Supprimer une(des) note(s)</legend>';
					if(sizeof($noteEtud)!=0){
						echo '<select name = "noteDel[]" multiple>';
						foreach ($noteEtud as $key => $value) {
							echo '<option value = "'.$value["id_note"].'" selected>'.$value["description"].': '.$value["valeur"].'/20</option>';
						}


						echo '		</select>';
			?>
			<a href="#"onclick="return(confirm('Etes-vous sûr de vouloir supprimer cette entrée?'));"><input type = "submit" name = "delNote"></a>

			<?php			
					}else{
						echo 'No data';
					}
					echo '		</fieldset>
							</form>
						</fieldset>';
								
				}
			?>
		</div>
	</div>

</body>
</html>