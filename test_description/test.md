# Teste Técnico – Desenvolvedor Jr (PHP + MySQL)
## Objetivo
1. Trabalhar com PHP (usando Composer)
2. Manipular MySQL
3. Estruturar projeto em MVC simples
4. Gerar relatórios (PDF e DOCX)
5. Utilizar bibliotecas externas

## Cenário
Desenvolver um sistema simples de gestão escolar, com foco em relatórios.

## Banco de Dados
```sql
CREATE TABLE turmas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    ano INT
);
CREATE TABLE alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    email VARCHAR(100),
    turma_id INT,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (turma_id) REFERENCES turmas(id)
);
CREATE TABLE notas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT,
    disciplina VARCHAR(100),
    nota DECIMAL(5,2),
    data_lancamento DATE,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id)
);
```

## Requisitos
### Cadastro de Turmas
1. Nome da turma
2. Ano

### Cadastro de Alunos
1. Nome
2. Email
3. Turma (select)

### Lançamento de Notas
1. Aluno
2. Disciplina
3. Nota
4. Data

### Listagem
Exibir: Aluno, Turma, Disciplina, Nota, Data
Filtros: Turma, Data início e fim
Diferencial: Média por aluno

## Relatórios
### PDF (Obrigatório)
1. Cabeçalho com nome da escola
2. Lista de notas
3. Média por aluno (se possível)
4. Biblioteca: knplabs/knp-snappy

### DOCX (Obrigatório)
1. Título
2. Tabela com dados
3. Biblioteca: phpoffice/phpword

### Excel (Diferencial)
1. Biblioteca: phpoffice/phpspreadsheet

## Requisitos Técnicos
### Composer (Obrigatório)
```bash
composer require knplabs/knp-snappy
composer require phpoffice/phpword
```

## Estrutura (MVC Simples)
```
/app
    /Controllers
    /Models
    /Views
/config
/public
/vendor
```

## Diferenciais
1. Docker configurado
2. MVC bem estruturado
3. Uso correto das bibliotecas
4. Layout organizado nos relatórios
5. Tratamento de erros
6. Média por aluno
7. Filtros eficientes

## Entrega
1. Repositório Git ou arquivo .zip
2. README.md com instruções
3. Script SQL
4. Comandos para rodar (ex: docker-compose up)