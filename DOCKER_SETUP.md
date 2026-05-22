# Docker Setup for RoL d20

## Quick Start

```bash
# Start the containers
docker-compose up -d

# Verify services are running
docker-compose ps
```

## Access Points

| Service | URL | Credentials |
|---------|-----|-------------|
| **Web App** | http://localhost:8080 | - |
| **phpMyAdmin** | http://localhost:8081 | User: `rold20_user` / Pass: `rold20_pass` |
| **Database** | localhost:3306 | User: `rold20_user` / Pass: `rold20_pass` |

## Common Commands

```bash
# View logs
docker-compose logs -f web        # PHP/Apache logs
docker-compose logs -f db         # MySQL logs

# Stop all services
docker-compose down

# Stop and remove volumes (clean slate)
docker-compose down -v

# Rebuild containers
docker-compose up -d --build

# Execute commands in containers
docker-compose exec web bash      # Shell in PHP container
docker-compose exec db mysql -u rold20_user -p rold20  # MySQL CLI
```

## Environment Variables

Configured in `docker-compose.yml`:
- `DB_HOST=db`
- `DB_USER=rold20_user`
- `DB_PASSWORD=rold20_pass`
- `DB_NAME=rold20`

Update these values in `docker-compose.yml` if needed.

## Database

The latest dump (`Dump20200708.sql`) is automatically loaded on first startup.

To load a different dump:
1. Update the volume path in `docker-compose.yml` under `db.volumes`
2. Run `docker-compose down -v && docker-compose up -d`

## Troubleshooting

**Container won't start:**
```bash
docker-compose logs db
docker-compose logs web
```

**Port already in use:**
Change ports in `docker-compose.yml`:
- `8080:80` → `8888:80` (web)
- `3306:3306` → `3307:3306` (db)

**Database connection errors:**
Ensure the `db` container is healthy:
```bash
docker-compose ps
```
Wait 10-15 seconds for MySQL to fully initialize.

## Development Tips

- Edit files locally; changes are reflected in containers via volume mounting
- Database persists in the `db_data` volume
- To reset database: `docker-compose down -v && docker-compose up -d`
