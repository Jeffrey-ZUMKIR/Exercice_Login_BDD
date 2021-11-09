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
	<link rel="stylesheet" type="text/css" href="../CSS/modelEtudiant.css">
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


	?>
	<div class="row">
		<div class="column">
	<?php
		if(isset($_POST["delNote"])){
			$post = $_POST['noteDel'];
			var_dump($post);

			foreach ($post as $key => $value) {
				deleteNote($connexion, $value);
			}

			echo '<script>alert("Note supprimée!");</script>';

		}
	?>


	<?php 

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



		//Afficher les notes de l'élève
		$note = getAllNoteEtud($connexion, $user['id_compte']);
		showNoteEtud($connexion, $note, $user);

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
	
		</div>
		
	</div>

</body>
</html>