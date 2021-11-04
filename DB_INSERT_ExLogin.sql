insert into type(lib_type) values
	("admin"),
	("professeur"),
	("etudiant");

insert into compte(login, passwd, nom, prenom, id_type) values
	("GraMad","1234","Madembo","Grace",(SELECT id_type FROM type WHERE lib_type = "professeur")),
	("NicVal","1234","Valentin","Nicolas",(SELECT id_type FROM type WHERE lib_type = "professeur")),
	("NicLeh","1234","Lehmann","Nicolas",(SELECT id_type FROM type WHERE lib_type = "professeur")),
	("MarSen","1234","Sensei","Marine",(SELECT id_type FROM type WHERE lib_type = "professeur")),
	("JefZum","1234","Zumkir","Jeffrey",(SELECT id_type FROM type WHERE lib_type = "etudiant")),
	("BapRif","1234","Riff","Baptiste",(SELECT id_type FROM type WHERE lib_type = "etudiant")),
	("MarSch","1234","Schwartz","Marine",(SELECT id_type FROM type WHERE lib_type = "etudiant")),
	("HugEit","1234","Eitel","Hugo",(SELECT id_type FROM type WHERE lib_type = "etudiant")),
	("GaePio","1234","Piou","Gaetan",(SELECT id_type FROM type WHERE lib_type = "etudiant")),
	("LucCor","1234","Cornu","Luc",(SELECT id_type FROM type WHERE lib_type = "etudiant"));

insert into groupe(lib_groupe) values
	("F1"),
	("F2"),
	("Bachelore"),
	("Master"),
	("Jap1");

insert into matiere(lib_matiere) values
	("Web"),
	("Algo"),
	("GD"),
	("Japonais");

insert into note(valeur, id_matiere, id_compte) values
	("15",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Web"), (SELECT id_compte FROM compte WHERE login = "JefZum")),
	("13",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Web"), (SELECT id_compte FROM compte WHERE login = "JefZum")),
	("17",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Web"), (SELECT id_compte FROM compte WHERE login = "MarSch")),
	("15",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Web"), (SELECT id_compte FROM compte WHERE login = "MarSch")),
	("14",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Web"), (SELECT id_compte FROM compte WHERE login = "BapRif")),
	("2",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Web"), (SELECT id_compte FROM compte WHERE login = "BapRif")),
	("18",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Web"), (SELECT id_compte FROM compte WHERE login = "HugEit")),
	("2",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Web"), (SELECT id_compte FROM compte WHERE login = "HugEit")),
	("15",(SELECT id_matiere FROM matiere WHERE lib_matiere = "GD"), (SELECT id_compte FROM compte WHERE login = "JefZum")),
	("16",(SELECT id_matiere FROM matiere WHERE lib_matiere = "GD"), (SELECT id_compte FROM compte WHERE login = "MarSch")),
	("17",(SELECT id_matiere FROM matiere WHERE lib_matiere = "GD"), (SELECT id_compte FROM compte WHERE login = "BapRif")),
	("16",(SELECT id_matiere FROM matiere WHERE lib_matiere = "GD"), (SELECT id_compte FROM compte WHERE login = "HugEit")),
	("17",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Algo"), (SELECT id_compte FROM compte WHERE login = "JefZum")),
	("16",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Algo"), (SELECT id_compte FROM compte WHERE login = "MarSch")),
	("15",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Algo"), (SELECT id_compte FROM compte WHERE login = "BapRif")),
	("16",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Algo"), (SELECT id_compte FROM compte WHERE login = "HugEit")),
	("15",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Japonais"), (SELECT id_compte FROM compte WHERE login = "JefZum")),
	("18",(SELECT id_matiere FROM matiere WHERE lib_matiere = "Japonais"), (SELECT id_compte FROM compte WHERE login = "MarSch"));

insert into detailgroupe(id_compte, id_groupe) values
	((SELECT id_compte FROM compte WHERE login = "JefZum"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Bachelore")),
	((SELECT id_compte FROM compte WHERE login = "MarSch"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Bachelore")),
	((SELECT id_compte FROM compte WHERE login = "BapRif"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Bachelore")),
	((SELECT id_compte FROM compte WHERE login = "HugEit"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Bachelore")),
	((SELECT id_compte FROM compte WHERE login = "JefZum"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Jap1")),
	((SELECT id_compte FROM compte WHERE login = "MarSch"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Jap1")),
	((SELECT id_compte FROM compte WHERE login = "GaePio"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Master")),
	((SELECT id_compte FROM compte WHERE login = "LucCor"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Master"));

insert into enseignement(id_compte, id_matiere, id_groupe) values
	((SELECT id_compte FROM compte WHERE login = "GraMad"), (SELECT id_matiere FROM matiere WHERE lib_matiere = "Web"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Bachelore")),
	((SELECT id_compte FROM compte WHERE login = "NicVal"), (SELECT id_matiere FROM matiere WHERE lib_matiere = "GD"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Bachelore")),
	((SELECT id_compte FROM compte WHERE login = "NicLeh"), (SELECT id_matiere FROM matiere WHERE lib_matiere = "Algo"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Bachelore")),
	((SELECT id_compte FROM compte WHERE login = "MarSen"), (SELECT id_matiere FROM matiere WHERE lib_matiere = "Japonais"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Bachelore")),
	((SELECT id_compte FROM compte WHERE login = "GraMad"), (SELECT id_matiere FROM matiere WHERE lib_matiere = "Web"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Master")),
	((SELECT id_compte FROM compte WHERE login = "NicVal"), (SELECT id_matiere FROM matiere WHERE lib_matiere = "GD"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Master")),
	((SELECT id_compte FROM compte WHERE login = "NicLeh"), (SELECT id_matiere FROM matiere WHERE lib_matiere = "Algo"), (SELECT id_groupe FROM groupe WHERE lib_groupe = "Master"));