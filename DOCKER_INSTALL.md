# Docker Installation Guide

## Mac (Recommended)

### Option 1: Docker Desktop (Official)
1. Download [Docker Desktop for Mac](https://www.docker.com/products/docker-desktop)
2. Install and launch the application
3. Verify installation:
   ```bash
   docker --version
   docker compose version
   ```

### Option 2: Homebrew
```bash
brew install docker docker-compose
```

## Windows

1. Download [Docker Desktop for Windows](https://www.docker.com/products/docker-desktop)
2. Install and launch
3. Enable WSL 2 backend (recommended)
4. Verify:
   ```bash
   docker --version
   docker compose version
   ```

## Linux (Ubuntu/Debian)

```bash
sudo apt update
sudo apt install docker.io docker-compose

# Add your user to docker group (optional, avoids 'sudo')
sudo usermod -aG docker $USER
newgrp docker
```

## Verification Steps

Once Docker is installed, run these commands from the project directory:

```bash
# Navigate to project
cd /Users/robertlind/Projects/GitHub/rold20.worktrees/agents-project-summary-overview

# Start containers
docker compose up -d

# Check status
docker compose ps

# View logs
docker compose logs -f

# Test web service
curl -I http://localhost:8080

# Test database
docker compose exec db mysql -u rold20_user -prold20_pass rold20 -e "SHOW TABLES;"

# Stop containers
docker compose down
```

## What to Expect

**After `docker compose up -d`:**

```
NAME                  IMAGE              STATUS
rold20_web            (custom)           Up (healthy)
rold20_db             mysql:8.0          Up (healthy)
rold20_phpmyadmin     phpmyadmin         Up
```

**Services should be accessible at:**
- Web: http://localhost:8080 (shows RoL d20 homepage)
- phpMyAdmin: http://localhost:8081
- Database: localhost:3306

## Troubleshooting

**"docker: command not found"**
- Docker isn't installed. Follow installation steps above.

**Containers start but port is in use**
- Change ports in `docker-compose.yml`

**Database connection fails**
- Wait 15-20 seconds for MySQL to initialize
- Check logs: `docker compose logs db`

**Need to reset everything**
```bash
docker compose down -v
docker compose up -d
```
