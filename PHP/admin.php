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
	<link rel="stylesheet" type="text/css" href="../CSS/modelAdmin.css">
	<style type="text/css">
		table, th, td{
			border:black solid 1px;
		}
	</style>
</head>
<body>
	<?php
		include 'fct_bdd_admin.php';	

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


		if(isset($_POST["addInGroupe"])){
			$post = $_POST;
			foreach ($post['etudiant'] as $key => $value) {
				addInGroupe($connexion, $value, $post['groupe']);
			}
			echo '<script>alert("Ajouter au groupe!")</script>';
		}

		if(isset($_POST["removeFromGroupe"])){
			$post = $_POST;
			if(isset($post['etudiant'])){
				foreach ($post['etudiant'] as $key => $value) {
					removeFromGroupe($connexion, $value, $post['groupe']);
				}
				echo '<script>alert("Retirer du groupe!")</script>';
			}
			
		}


	?>
	<div class="row">
		<div class="column">


		<?php 




			//Affiche tableaux des notes en fonction de si c'est un professeur ou un élève
			echo'<table id="tableEtudiant" style="width: 90%; margin: 0 auto">
					<thead>
						<tr>
							<th colspan="2">'./*getPrenomNomProf($keyUser,$listProf)*/$user['prenom'].' '.$user['nom'].'<br/>'.$user['login'].'<br/>';
			if(is_file('../image/'.$user['login'].'.png')){
				echo '<img src="../image/'.$user['login'].'.png" style="width:200px;height:200px">';
			}else{
				echo '<img src="../image/Default.png" style="width:200px;height:200px">';
			}
			

			echo			'</th>
						</tr>
					</thead>
				</table>';

			echo '<a href="logout.php"><p style="text-align: center">Déconnexion</p></a>';
		?>
	
		</div>
		<div class="column">
		<?php
			echo '<fieldset style="width: 90%;margin: 0 auto"><legend>Ajouter un(des) élève(s) dans un groupe</legend>';
			echo '	<form action="#" method="post">
						<fieldset style="width: 90%;margin: 0 auto"><legend>Choisir un groupe</legend>';

			$groupes = getGroupe($connexion);

			echo '			<select name="groupe">';

			foreach ($groupes as $key => $value) {
				echo '<option value="'.$value["id_groupe"].'">'.$value["lib_groupe"].'</option>';
			}


			echo '			</select>';
			echo '			<input type="submit" name="whichGroupe1">';
			echo '		</fieldset>
					</form>';

			if(isset($_POST["whichGroupe1"])){
				$etud = getEtudNotInGroupe($connexion, $_POST["groupe"]);
				echo '<form action="#" method="post">
						<fieldset style="width: 90%;margin: 0 auto"><legend>Choisir le(s) élève(s) a ajouter au groupe '.getLibGroupe($connexion, $_POST["groupe"]).'</legend>';

				echo '			<select name="etudiant[]" multiple>';
				foreach ($etud as $key => $value) {
					echo '<option value="'.$value['id_compte'].'">'.$value['nom'].' '.$value['prenom'].'</option>';
				}
				echo '			</select>';

				echo '		<input type="hidden" name=groupe value="'.$_POST["groupe"].'">';
				echo '		<input type="submit" name="addInGroupe">';
				echo '	</fieldset>
					</form>';
			}


			echo '</fieldset>';

		?>
		<?php
			echo '<fieldset style="width: 90%;margin: 0 auto"><legend>Retirer un(des) élève(s) d\'un groupe</legend>';
			echo '	<form action="#" method="post">
						<fieldset style="width: 90%;margin: 0 auto"><legend>Choisir un groupe</legend>';

			$groupes = getGroupe($connexion);

			echo '			<select name="groupe">';

			foreach ($groupes as $key => $value) {
				echo '<option value="'.$value["id_groupe"].'">'.$value["lib_groupe"].'</option>';
			}


			echo '			</select>';
			echo '			<input type="submit" name="whichGroupe2">';
			echo '		</fieldset>
					</form>';

			if(isset($_POST["whichGroupe2"])){
				$etud = getEtudInGroupe($connexion, $_POST["groupe"]);
				echo '<form action="#" method="post">
						<fieldset style="width: 90%;margin: 0 auto"><legend>Choisir le(s) élève(s) a retirer du groupe '.getLibGroupe($connexion, $_POST["groupe"]).'</legend>';

				echo '			<select name="etudiant[]" multiple>';
				foreach ($etud as $key => $value) {
					echo '<option value="'.$value['id_compte'].'">'.$value['nom'].' '.$value['prenom'].'</option>';
				}
				echo '			</select>';

				echo '		<input type="hidden" name=groupe value="'.$_POST["groupe"].'">';
				echo '		<input type="submit" name="removeFromGroupe">';
				echo '	</fieldset>
					</form>';
			}

			echo '		</fieldset>
					</form>';

		?>
		</div>
		<div class="column">
			
		</div>
	</div>

</body>
</html>