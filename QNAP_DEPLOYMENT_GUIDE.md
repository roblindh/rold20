# QNAP Container Station Deployment Guide: RoL d20

This guide explains how to deploy, manage, and update **RoL d20** on your **QNAP TS-453Bmini** using **Container Station** (Docker & Docker Compose) with HTTPS encryption.

---

## 1. Architecture & Port Mapping

To prevent collisions with QNAP QTS built-in services (which use ports `80`, `443`, `8080`, `8081`, and `3306`), the stack uses dedicated non-conflicting ports:

| Service | Container Name | Internal Port | Host / NAS Port | Access URL |
|---|---|---|---|---|
| **RoL d20 Web App** | `rold20_web` | `80` (HTTP) | **`8090`** | `http://<NAS_IP>:8090/` |
| **phpMyAdmin** | `rold20_phpmyadmin` | `80` (HTTP) | **`8885`** | `http://<NAS_IP>:8885/` |
| **MySQL 8.0 Database** | `rold20_db` | `3306` (TCP) | **`3307`** | `localhost:3307` |
| **QTS HTTPS Reverse Proxy** | Built-in QTS | - | **`8443`** | `https://<NAS_IP>:8443/` |

---

## 2. Directory Setup on QNAP

1. In QTS **Container Station**, the shared `Container` folder is located at `/share/Container`.
2. Project target directory:
   - **NAS Linux Path**: `/share/Container/rold20`
   - **Windows Network Share**: `\\ROL-NAS-MINI\Container\rold20`

---

## 3. Deploy Project Files to the NAS

From PowerShell on your development machine in `d:\Projects\rold20`:

```powershell
.\deploy.ps1
```

*(This automatically mirrors all application files to `\\ROL-NAS-MINI\Container\rold20` while excluding `.git`, `tests/`, `dbdump/`, and IDE metadata.)*

---

## 4. Starting the Application (SSH Terminal)

1. SSH into the QNAP NAS:
   ```bash
   ssh admin@<NAS_IP>
   ```
2. Navigate to the project directory and start the stack:
   ```bash
   cd /share/Container/rold20
   docker compose up -d --build
   ```
   > [!NOTE]
   > Use `docker compose` (with a space). On modern QTS, Docker Compose v2 is integrated directly into the `docker` CLI.

3. Verify running containers:
   ```bash
   docker compose ps
   ```

On the initial launch, MySQL automatically imports the schema and data from `dbdump/Dump20200708.sql`.

---

## 5. Web UI Management in Container Station

Once started via SSH, the stack is automatically visible and manageable in the **Container Station Web UI**:
- Go to **Container Station > Applications** to see `rold20` and view live status, CPU/RAM usage, and restart controls.
- View individual container logs under **Containers** > `rold20_web` / `rold20_db`.

---

## 6. HTTPS & Public Domain Setup (DDNS)

### A. Free Let's Encrypt Certificate
1. In QTS, open **Control Panel > Security > Certificate & Private Key**.
2. Click **Replace Certificate** > **Get a certificate from Let's Encrypt**.
3. Domain: `rold20.ddns.net`.

### B. QTS Reverse Proxy Rule
1. Open **Control Panel > Network & File Services > Reverse Proxy** > **+ Add**.
2. **Rule Name**: `rold20`
3. **Source**: Protocol `HTTPS` / Host `rold20.ddns.net` / Port **`8443`**
4. **Destination**: Protocol `HTTP` / Host `127.0.0.1` / Port **`8090`**

### C. Router Port Forwarding
Because QTS system admin reserves internal port 443, your router translates external 443 to internal 8443:

| External (WAN) Port | Internal (LAN) IP | Internal (LAN) Port | Purpose |
|---|---|---|---|
| **`443`** (TCP) | NAS IP | **`8443`** | HTTPS Traffic |
| **`80`** (TCP) | NAS IP | **`80`** | Let's Encrypt Verification |

> [!TIP]
> If you have a cascaded / multi-router setup (e.g. ISP modem/router + secondary Wi-Fi router), ensure port forwarding is configured on **both** routers in sequence.

### D. Public Access URL
- **Public HTTPS**: 👉 **`https://rold20.ddns.net/`**

---

## 7. Updating the Application

### Updating PHP, HTML, or CSS Code
1. Make changes locally on your PC.
2. Ask the assistant to deploy, or run:
   ```powershell
   .\deploy.ps1
   ```
3. Refresh your browser (changes take effect instantly because `/share/Container/rold20` is mounted into the container).

### Updating Dockerfile, Apache, or PHP Configuration
1. Run `.\deploy.ps1`.
2. Rebuild the web container on the NAS:
   ```bash
   cd /share/Container/rold20
   docker compose up -d --build web
   ```

---

## 8. Technical & Security Reference

### QNAP Kernel Compatibility (Ubuntu 22.04 Base)
- Base image uses **Ubuntu 22.04 LTS** with precompiled binary packages (`php8.1`, `php8.1-mysql`, `php8.1-opcache`, `apache2`).
- Bypasses source compilation and GNU `tar` `fchmodat` syscall errors on the QTS Linux kernel.

### Case-Insensitive URL Matching (`mod_speling`)
- Linux filesystems are case-sensitive (`Styles/Site.css`), while web links may use `styles/site.css`.
- Apache **`mod_speling`** (`CheckSpelling On`, `CheckCaseOnly On`) is enabled to automatically resolve URL casing for stylesheets, icons, and creature images.

### Security Hardening (`.htaccess`)
- Blocks public web downloads of `.sql`, `.data`, `.ini`, `.xml`, `.log`, `.md`, `.json`, and `.sln` files.
- Blocks direct web access to `dbdump/`, `tests/`, `docker/`, `nbproject/`, and `.git/`.
- Disables directory browsing (`Options -Indexes`).
