# Teste Técnico PHP - School

## Executando com Docker

### Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/) e [Docker Compose](https://docs.docker.com/compose/install/) instalados.

### Configuração

1. Copie o arquivo de variáveis de ambiente:

   ```bash
   cp .env.example .env
   ```

2. Suba os containers:

   ```bash
   docker compose up -d --build
   ```

   Isso inicia três serviços:
   - `school_app` — Aplicação PHP 8.2-FPM
   - `school_nginx` — Servidor Nginx (disponível na porta `APP_PORT`, padrão `8080`)
   - `school_db` — MySQL 8.0 (disponível na porta `DB_PORT_EXTERNAL`, padrão `3307`)

   O schema do banco é inicializado automaticamente na primeira execução via `sql/01_schema.sql`.

3. A API estará disponível em `http://localhost:8080`.

### Popular o banco de dados (Seed)

Para popular o banco com dados de exemplo:

```bash
docker compose exec app php database/seed.php
```

Para limpar as tabelas antes de popular (fresh seed):

```bash
docker compose exec app php database/seed.php --fresh
```

### Parar os containers

```bash
docker compose down
```

Para remover também o volume do banco de dados:

```bash
docker compose down -v