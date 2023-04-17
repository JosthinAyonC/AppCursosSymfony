DROP TABLE IF EXISTS suscripcion;
DROP TABLE IF EXISTS curso;
DROP TABLE IF EXISTS usuario;

create table usuario(
id_usuario serial,
nombre varchar(100),
apellido varchar(100),
correo varchar(100) unique,
clave varchar(100),
roles json,
estado varchar(1),

constraint pk_usuario primary key (id_usuario)
);

create table curso(
id_curso serial,
nombre varchar(100),
fecha_publicacion date,
estado varchar(1),


constraint pk_curso primary key (id_curso),

constraint fk_curso_usuario foreign key(id_usuario)
		references usuario (id_usuario)
);

create table suscripcion(
	id_suscripcion serial,
    id_usuario int,
    id_curso int, 

	constraint id_suscripcion primary key(id_suscripcion),
	
    constraint fk_suscripcion_usuario foreign key(id_usuario)
		references usuario (id_usuario),
    constraint fk_suscripcion_curso foreign key(id_curso)
		references curso (id_curso)

);




INSERT INTO usuario(
nombre, apellido, correo, clave, roles, estado)
VALUES
(
'Josthin', 'Ayon', 'admin@admin.com',
'$2y$13$aJRvegWq0Qq8ApN5Sji48e3.oDyINOM2sMaDB7q0kfvEppG6JnQ2O',
'["ROLE_ADMIN"]', 'A' 
);
