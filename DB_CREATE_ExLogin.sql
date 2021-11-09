/*Créer bdd*/
drop database if exists ExLogin;

create database if not exists ExLogin;

/*Création table*/
/*Création type table*/
drop table if exists type;

create table if not exists type(
	id_type int not null AUTO_INCREMENT primary key,
	lib_type varchar(20) not null
)engine = InnoDB;

/*Création compte table*/
drop table if exists compte;

create table if not exists compte(
	id_compte int not null AUTO_INCREMENT primary key,
	login varchar(20) not null,
	passwd varchar(20) not null,
	nom varchar(20) not null,
	prenom varchar(20) not null,
	id_type int not null
)engine = InnoDB;

/*Création groupe table*/
drop table if exists groupe;

create table if not exists groupe(
	id_groupe int not null AUTO_INCREMENT primary key,
	lib_groupe varchar(20) not null
)engine = InnoDB;

/*Création matiere table*/
drop table if exists matiere;

create table if not exists matiere(
	id_matiere int not null AUTO_INCREMENT primary key,
	lib_matiere varchar(20) not null
)engine = InnoDB;

/*Création note table*/
drop table if exists note;

create table if not exists note(
	id_note int not null AUTO_INCREMENT primary key,
	valeur int not null,
	description varchar(100) null,
	id_matiere int not null,
	id_compte int not null
)engine = InnoDB;

/*Création detailgroupe table*/
drop table if exists detailgroupe;

create table if not exists detailgroupe(
	id_compte int not null,
	id_groupe int not null,
	primary key(id_compte, id_groupe)
)engine = InnoDB;

/*Création enseignement table*/
drop table if exists enseignement;

create table if not exists enseignement(
	id_compte int not null,
	id_matiere int not null,
	id_groupe int not null,
	primary key(id_compte, id_matiere, id_groupe)
)engine = InnoDB;

alter table enseignement add constraint FK_EnsMat foreign key (id_matiere) references matiere (id_matiere);
alter table enseignement add constraint FK_EnsCptCas foreign key (id_compte) references compte (id_compte) ON DELETE CASCADE;
alter table enseignement add constraint FK_EnsGrp foreign key (id_groupe) references groupe (id_groupe);

alter table detailgroupe add constraint FK_DGrpGrp foreign key (id_groupe) references groupe (id_groupe);
alter table detailgroupe add constraint FK_DGrpCptCas foreign key (id_compte) references compte (id_compte) ON DELETE CASCADE;

alter table compte add constraint FK_CptTyp foreign key (id_type) references type (id_type);

alter table note add constraint FK_NteMat foreign key (id_matiere) references matiere (id_matiere);
alter table note add constraint FK_NteCptCas foreign key (id_compte) references compte (id_compte) ON DELETE CASCADE;

alter table enseignement drop constraint FK_EnsCpt;
alter table detailgroupe drop constraint FK_DGrpCpt;
alter table note drop constraint FK_NteCpt;
