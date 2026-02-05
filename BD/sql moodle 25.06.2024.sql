CREATE SCHEMA senacbd;
USE senacbd;

CREATE TABLE curso (
    curso_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    curso_nome varchar(100),
    curso_situacao varchar(10),
    curso_descricao varchar(255),
    curso_quantidade_horas int(11)
);

CREATE TABLE disciplina (
    disciplina_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    disciplina_nome varchar(100),
    disciplina_carga_horaria int(11),
    disciplina_transversal varchar(10),
    disciplina_ementa varchar(255),
    disciplina_bibliografia varchar(100)
);

CREATE TABLE turma (
    turma_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    turma_nome varchar(100),
    turma_ano int(4),
    turma_semestre varchar(30),
    turma_estado varchar(10),
    turma_turno varchar(10),
    fk_curso_curso_codigo int(11),
    FOREIGN KEY (fk_curso_curso_codigo) REFERENCES curso(curso_codigo)
);

CREATE TABLE sala (
    sala_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    sala_nome varchar(100),
    sala_endereco varchar(150),
    sala_tipo varchar(100),
    sala_situacao varchar(15),
    sala_descricao varchar(150),
    sala_capacidade_alunos int(3),
    sala_disponibilidade_manha ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    sala_disponibilidade_tarde ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    sala_disponibilidade_noite ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel'
);

CREATE TABLE professor (
    professor_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    professor_nome varchar(100),
    professor_email varchar(100),
    professor_telefone varchar(11),
    professor_cpf varchar(14),
    professor_endereco varchar(255),
    professor_sexo varchar(10),
    professor_data_admissao date,
    professor_data_desligamento date,
    professor_titulacao varchar(60),
    professor_data_nascimento date,
    professor_url_curriculo_lattes varchar(60),
    professor_foto_nome varchar(100),
    professor_foto_data_upload datetime,
    professor_foto_path varchar(100),
    professor_disponibilidade_segunda_manha ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_segunda_tarde ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_segunda_noite ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_terca_manha ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_terca_tarde ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_terca_noite ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_quarta_manha ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_quarta_tarde ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_quarta_noite ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_quinta_manha ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_quinta_tarde ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_quinta_noite ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_sexta_manha ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_sexta_tarde ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_sexta_noite ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_sabado_manha ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_sabado_tarde ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel',
    professor_disponibilidade_sabado_noite ENUM('disponivel', 'indisponivel') DEFAULT 'disponivel'
);

CREATE TABLE agendamento_aula (
    agendamento_aula_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    agendamento_aula_data_da_aula date,
    agendamento_aula_hora_de_inicio time,
    agendamento_aula_hora_de_termino time,
    fk_professor_professor_codigo int(11),
    fk_sala_sala_codigo int(11),
    FOREIGN KEY (fk_professor_professor_codigo) REFERENCES professor(professor_codigo),
    FOREIGN KEY (fk_sala_sala_codigo) REFERENCES sala(sala_codigo)
);

CREATE TABLE aluno (
    aluno_nome varchar(100),
    aluno_sexo varchar(10),
    aluno_idade int(2),
    aluno_email varchar(100),
    aluno_telefone varchar(15),
    aluno_rg varchar(10),
    aluno_cpf varchar(11),
    aluno_dt_nascimento date,
    aluno_endereco varchar(100),
    aluno_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    aluno_foto_nome varchar(100),
    aluno_foto_path varchar(100),
    aluno_foto_data_upload datetime
);

CREATE TABLE professor_ministra_disciplina (
    ministra_dia_semana varchar(20),
    ministra_turno varchar(20),
    professor_ministra_disciplina_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    fk_professor_professor_codigo int(11),
    fk_disciplina_disciplina_codigo int(11),
    FOREIGN KEY (fk_professor_professor_codigo) REFERENCES professor(professor_codigo),
    FOREIGN KEY (fk_disciplina_disciplina_codigo) REFERENCES disciplina(disciplina_codigo)
);

CREATE TABLE avalicao_aluno_disciplina (
    disciplina_prova1 int(2),
    disciplina_prova2 int(2),
    disciplina_prova_final int(2),
    avaliacao_aluno_disciplina_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    fk_disciplina_disciplina_codigo int(11),
    fk_aluno_aluno_codigo int(11),
    FOREIGN KEY (fk_disciplina_disciplina_codigo) REFERENCES disciplina(disciplina_codigo),
    FOREIGN KEY (fk_aluno_aluno_codigo) REFERENCES aluno(aluno_codigo)
);

CREATE TABLE professor_capacidade (
    professor_capacidade_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    fk_disciplina_disciplina_codigo int(11),
    fk_professor_professor_codigo int(11),
    FOREIGN KEY (fk_professor_professor_codigo) REFERENCES professor(professor_codigo),
    FOREIGN KEY (fk_disciplina_disciplina_codigo) REFERENCES disciplina(disciplina_codigo)
);

CREATE TABLE curso_disciplina (
	curso_disciplina_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    fk_curso_curso_codigo int(11),
    fk_disciplina_disciplina_codigo int(11),
    curso_disciplina_semestre varchar(20),
    FOREIGN KEY (fk_curso_curso_codigo) REFERENCES curso(curso_codigo),
    FOREIGN KEY (fk_disciplina_disciplina_codigo) REFERENCES disciplina(disciplina_codigo)
);

CREATE TABLE sala_turma (
    sala_turma_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    fk_sala_sala_codigo int(11),
    fk_turma_turma_codigo int(11),
    FOREIGN KEY (fk_sala_sala_codigo) REFERENCES sala(sala_codigo),
    FOREIGN KEY (fk_turma_turma_codigo) REFERENCES turma(turma_codigo)
);

CREATE TABLE matricula (
	matricula_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    fk_curso_curso_codigo int(11),
    fk_aluno_aluno_codigo int(11),
    FOREIGN KEY (fk_curso_curso_codigo) REFERENCES curso(curso_codigo),
    FOREIGN KEY (fk_aluno_aluno_codigo) REFERENCES aluno(aluno_codigo)
);

CREATE TABLE aluno_turma (
    aluno_turma_codigo int(11) AUTO_INCREMENT PRIMARY KEY,
    fk_aluno_aluno_codigo int(11),
    fk_turma_turma_codigo int(11),
    FOREIGN KEY (fk_aluno_aluno_codigo) REFERENCES aluno(aluno_codigo),
    FOREIGN KEY (fk_turma_turma_codigo) REFERENCES turma(turma_codigo)
);
 
ALTER TABLE turma ADD CONSTRAINT FK_turma_2
    FOREIGN KEY (fk_curso_curso_codigo)
    REFERENCES curso (curso_codigo)
    ON DELETE RESTRICT;
 
ALTER TABLE agendamento_aula ADD CONSTRAINT FK_agendamento_aula_2
    FOREIGN KEY (fk_professor_professor_codigo)
    REFERENCES professor (professor_codigo);
 
ALTER TABLE agendamento_aula ADD CONSTRAINT FK_agendamento_aula_3
    FOREIGN KEY (fk_sala_sala_codigo)
    REFERENCES sala (sala_codigo);
 
ALTER TABLE professor_ministra_disciplina ADD CONSTRAINT FK_professor_ministra_disciplina_2
    FOREIGN KEY (fk_professor_professor_codigo)
    REFERENCES professor (professor_codigo);
 
ALTER TABLE professor_ministra_disciplina ADD CONSTRAINT FK_professor_ministra_disciplina_3
    FOREIGN KEY (fk_disciplina_disciplina_codigo)
    REFERENCES disciplina (disciplina_codigo);
 
ALTER TABLE avalicao_aluno_disciplina ADD CONSTRAINT FK_avalicao_aluno_disciplina_2
    FOREIGN KEY (fk_disciplina_disciplina_codigo)
    REFERENCES disciplina (disciplina_codigo);
 
ALTER TABLE avalicao_aluno_disciplina ADD CONSTRAINT FK_avalicao_aluno_disciplina_3
    FOREIGN KEY (fk_aluno_aluno_codigo)
    REFERENCES aluno (aluno_codigo);
 
ALTER TABLE professor_capacidade ADD CONSTRAINT FK_professor_capacidade_2
    FOREIGN KEY (fk_disciplina_disciplina_codigo)
    REFERENCES disciplina (disciplina_codigo);
 
ALTER TABLE professor_capacidade ADD CONSTRAINT FK_professor_capacidade_3
    FOREIGN KEY (fk_professor_professor_codigo)
    REFERENCES professor (professor_codigo);
 
ALTER TABLE curso_disciplina ADD CONSTRAINT FK_curso_disciplina_2
    FOREIGN KEY (fk_disciplina_disciplina_codigo)
    REFERENCES disciplina (disciplina_codigo)
    ON DELETE RESTRICT;
 
ALTER TABLE curso_disciplina ADD CONSTRAINT FK_curso_disciplina_3
    FOREIGN KEY (fk_curso_curso_codigo)
    REFERENCES curso (curso_codigo)
    ON DELETE SET NULL;
 
ALTER TABLE sala_turma ADD CONSTRAINT FK_sala_turma_2
    FOREIGN KEY (fk_turma_turma_codigo)
    REFERENCES turma (turma_codigo)
    ON DELETE RESTRICT;
 
ALTER TABLE sala_turma ADD CONSTRAINT FK_sala_turma_3
    FOREIGN KEY (fk_sala_sala_codigo)
    REFERENCES sala (sala_codigo)
    ON DELETE RESTRICT;
 
ALTER TABLE matricula ADD CONSTRAINT FK_matricula_2
    FOREIGN KEY (fk_curso_curso_codigo)
    REFERENCES curso (curso_codigo)
    ON DELETE RESTRICT;
 
ALTER TABLE matricula ADD CONSTRAINT FK_matricula_3
    FOREIGN KEY (fk_aluno_aluno_codigo)
    REFERENCES aluno (aluno_codigo)
    ON DELETE SET NULL;
 
ALTER TABLE aluno_turma ADD CONSTRAINT FK_aluno_turma_2
    FOREIGN KEY (fk_aluno_aluno_codigo)
    REFERENCES aluno (aluno_codigo)
    ON DELETE RESTRICT;
 
ALTER TABLE aluno_turma ADD CONSTRAINT FK_aluno_turma_3
    FOREIGN KEY (fk_turma_turma_codigo)
    REFERENCES turma (turma_codigo)
    ON DELETE RESTRICT;