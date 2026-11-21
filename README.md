# Nho Net - Personal Website (Full-Stack Docker Deployment)

A full-stack website created for **Nho Net** running on a Docker microservices architecture.

---

## Architecture Overview

```
                      +-----------------------------+
                      |       User's Browser        |
                      +--------------+--------------+
                                     |
               Port 3000 (UI & Proxy)| Port 8000 (Direct API)
                                     v
+------------------------------------+------------------------------------+
| Docker Network: nhonet-network                                          |
|                                                                         |
|   +-----------------------+               +--------------------------+  |
|   |    Frontend Container |               |     Backend Container    |  |
|   |    (nhonet-frontend)  | -- proxy /api/->    (nhonet-backend)     |  |
|   |      Nginx Alpine     |               |      Laravel 13 (PHP 8.4)|  |
|   +-----------------------+               +-------------+------------+  |
|                                                         |               |
|                                                         | TCP 3306      |
|                                                         v               |
|                                           +--------------------------+  |
|                                           |    Database Container    |  |
|                                           |       (nhonet-db)        |  |
|                                           |       MariaDB 11.8       |  |
|                                           +--------------------------+  |
+-------------------------------------------------------------------------+
```

---

## Containers & Setup Details

### 1. Docker Network
```powershell
docker network create nhonet-network
```

### 2. Database Container (MariaDB)
Created using `docker run` with username `nhonet`:
```powershell
docker run -d `
  --name nhonet-db `
  --network nhonet-network `
  -p 3306:3306 `
  -e MARIADB_ROOT_PASSWORD=rootpassword `
  -e MARIADB_DATABASE=nhonet_db `
  -e MARIADB_USER=nhonet `
  -e MARIADB_PASSWORD=nhonet_password `
  mariadb:11.8
```

### 3. Backend Container (Laravel)
Built with custom [backend/Dockerfile](file:///c:/Netz/docker%20learn/app%20test/backend/Dockerfile) and run using `docker run`:
```powershell
# Build Backend Image
docker build -t nhonet-laravel-backend:latest ./backend

# Run Backend Container
docker run -d `
  --name nhonet-backend `
  --network nhonet-network `
  -p 8000:8000 `
  nhonet-laravel-backend:latest
```

### 4. Frontend Container (Nginx / Web UI)
Built with custom [frontend/Dockerfile](file:///c:/Netz/docker%20learn/app%20test/frontend/Dockerfile) and run using `docker run`:
```powershell
# Build Frontend Image
docker build -t nhonet-frontend:latest ./frontend

# Run Frontend Container
docker run -d `
  --name nhonet-frontend `
  --network nhonet-network `
  -p 3000:80 `
  nhonet-frontend:latest
```

---

## Access URLs

| Component | URL | Description |
|-----------|-----|-------------|
| **Frontend Website** | [http://localhost:3000](http://localhost:3000) | Responsive Web Portal for Nho Net |
| **Backend API Health** | [http://localhost:8000/api/status](http://localhost:8000/api/status) | JSON API health status |
| **Backend Messages API** | [http://localhost:8000/api/messages](http://localhost:8000/api/messages) | Stored Guestbook messages |
| **MariaDB Database** | `localhost:3306` | User: `nhonet`, DB: `nhonet_db` |
