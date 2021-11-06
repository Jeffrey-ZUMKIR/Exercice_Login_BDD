/* Get note from student */
select lib_matiere, valeur
from note, matiere 
where note.id_matiere = matiere.id_matiere and id_compte = "5";