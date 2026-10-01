# Client Project Tracker

A small Laravel application for tracking client projects. Project managers can list, search, filter, sort, create, view, edit, and delete projects. The same rules power a JSON API and the Vue interface.

## What you need before you start

Docker runs PHP, Composer, Node.js, and MySQL. You do not install those on Windows or Mac.

Install this on either system:

1. [Docker Desktop](https://www.docker.com/products/docker-desktop/) with Docker Compose v2. Open it and wait until it says the engine is running.
2. Git, only if you are cloning this project. A downloaded folder does not need Git.
3. About 4 GB of free memory for Docker, and an internet connection the first time so it can download the images.

These ports must be free on both systems:

| Port | Used for |
| --- | --- |
| 8080 | The website |
| 5173 | The Vue dev server |
| 3307 | MySQL on your computer |

MySQL inside Docker still uses port 3306. The published port on your computer is 3307, so this app can run beside another MySQL, including XAMPP on Windows.

### Windows

- Windows 10 or Windows 11, 64-bit.
- Docker Desktop using the WSL 2 backend. The installer prompts you to turn this on. Restart the computer if it asks you to.
- Virtualization enabled. If Docker says the virtual machine cannot start, turn on virtualization in the BIOS or UEFI settings.
- PowerShell or Windows Terminal. The commands below use PowerShell.
- If port 8080, 5173, or 3307 is already taken, quit the other program or change the ports in `.env` before the first start.

Check Docker from PowerShell:

```powershell
docker --version
docker compose version
```

Both commands should print a version. If either command is not found, Docker Desktop is not installed or its engine is still starting.

### Mac

- macOS 12 or newer. Docker Desktop supports both Apple silicon and Intel Macs. Download the installer that matches your chip.
- The Mac App Store is not required. Install Docker Desktop from the Docker website, then open it from Applications and wait until the whale icon says it is running.
- Terminal, which is already on the Mac. The commands below use `bash`-style syntax and work in zsh, the default Mac shell.
- Allow the Docker privileged helper if macOS asks. The first launch can take a few minutes.

Check Docker from Terminal:

```bash
docker --version
docker compose version
```

## This project does not use Laravel Sail

Laravel Sail is Laravel's own Docker wrapper. A Sail app is started with `./vendor/bin/sail up`.

This project does not include Sail. It has its own `docker-compose.yml` with four services:

| Service | What it runs |
| --- | --- |
| `app` | PHP 8.3 with the Laravel app |
| `nginx` | The website on port 8080 |
| `mysql` | MySQL 8.4 |
| `node` | Vite, which serves the Vue files |

Start and stop it with `docker compose`, on both Windows and Mac. There is no `sail` command to run.

## First-time setup

Open a terminal in the project folder. On Windows that is the folder in File Explorer: Shift+right-click the folder and choose "Open in Terminal", or `cd` to it in PowerShell. On Mac, drag the folder onto a Terminal window or `cd` to it.

### Windows (PowerShell)

```powershell
Copy-Item .env.example .env
docker compose up --build
```

### Mac (Terminal)

```bash
cp .env.example .env
docker compose up --build
```

The first start builds the PHP image, creates the MySQL database, runs migrations, and loads the 12 sample projects. The Node service installs the frontend packages and starts Vite. The first build often takes several minutes.

Wait until the logs show MySQL as healthy and Vite as ready. Then open:

http://localhost:8080

Leave that terminal open while you use the app. If `.env` has no `APP_KEY`, the app container generates one on startup.

The same `docker compose` commands in the rest of this file work on Windows PowerShell and on the Mac Terminal.

## Everyday commands

Start the app:

```bash
docker compose up
```

Stop it:

```bash
docker compose down
```

Stop it and delete the database volume:

```bash
docker compose down -v
```

`down -v` removes every project, including ones you created. The next `docker compose up` loads the sample projects again.

## Reset the sample data

This deletes every project and loads the original 12 rows:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

A normal restart does not reset data. Edits stay in MySQL until you run the command above or `docker compose down -v`.

## Database

You do not install MySQL, and you do not create the database by hand. The first `docker compose up --build` does that.

1. The `mysql` service starts MySQL 8.4 and creates the database and user from `.env`.
2. The `app` service waits until MySQL is healthy.
3. Migrations create the `projects` table, plus Laravel's default tables.
4. `ProjectSeeder` inserts the 12 sample projects when `projects` is empty.

These are the values in `.env.example`:

| Setting | Default | What it is |
| --- | --- | --- |
| `DB_DATABASE` | `task_management` | Database name |
| `DB_USERNAME` | `tracker` | User the Laravel app logs in as |
| `DB_PASSWORD` | `tracker_secret` | Password for that user |
| `DB_ROOT_PASSWORD` | `root_secret` | MySQL root password, used by Docker |
| `DB_HOST` | `mysql` | Database hostname inside Docker |
| `DB_PORT` | `3306` | Database port inside Docker |
| `FORWARD_DB_PORT` | `3307` | Port on your Windows or Mac machine |

`DB_HOST=mysql` is only for the containers. A program on your computer cannot use that hostname.

### Connect from Windows or Mac

Use MySQL Workbench, DBeaver, HeidiSQL, TablePlus, or another MySQL client with:

| Field | Value |
| --- | --- |
| Host | `127.0.0.1` |
| Port | `3307` |
| Database | `task_management` |
| Username | `tracker` |
| Password | `tracker_secret` |

The rows are in the `projects` table. Column names there are `client_name`, `project_name`, `start_date`, and `due_date`. The website and the API show those as `clientName`, `projectName`, `startDate`, and `dueDate`.

If `FORWARD_DB_PORT`, the database name, or the password in `.env` is different, use those values instead of the defaults above.

### MySQL prompt

From the project folder, while the stack is running. This command is the same in Windows PowerShell and the Mac Terminal:

```bash
docker compose exec mysql mysql -utracker -ptracker_secret task_management
```

```sql
SHOW TABLES;
SELECT id, client_name, project_name, status, priority FROM projects;
```

Type `exit` to leave the prompt.

Changing the database name or password in `.env` does not update a database that Docker already created. To apply those changes, stop the stack and delete its data, then start it again:

```bash
docker compose down -v
docker compose up --build
```

That removes every saved project and loads the 12 sample rows again.

## Change the ports

Edit `.env`, then start the stack again:

```
APP_PORT=8080
FORWARD_DB_PORT=3307
APP_URL=http://localhost:8080
```

`APP_URL` should use the same port as `APP_PORT`. `DB_HOST` stays `mysql`. That name is the database service inside Docker, not your computer.

## API

The interface and these endpoints share one service. Send `Content-Type: application/json`. Responses use only the public project fields.

| Action | Request |
| --- | --- |
| List | `GET /projects` |
| View | `GET /projects/1` |
| Create | `POST /projects` |
| Update | `PUT /projects/1` |
| Delete | `DELETE /projects/1` |

List, search, filter, and sort accept the same query on the page and on `GET /projects`:

```
GET /projects?search=acme&status=In%20Progress&priority=High&sort=dueDate&direction=asc
```

`sort` can be `clientName`, `projectName`, `status`, `priority`, `startDate`, or `dueDate`. `direction` is `asc` or `desc`. Priority sorts Low, Medium, High. Status sorts Planning, In Progress, On Hold, Completed.

Create and update body:

```json
{
  "clientName": "Acme Corporation",
  "projectName": "Corporate Website Redesign",
  "description": "Optional notes.",
  "status": "In Progress",
  "priority": "High",
  "startDate": "2026-06-01",
  "dueDate": "2026-07-15"
}
```

Status must be `Planning`, `In Progress`, `On Hold`, or `Completed`. Priority must be `Low`, `Medium`, or `High`. Client name, project name, start date, and due date are required. Description is optional. The due date cannot be earlier than the start date.

A list response looks like:

```json
{
  "data": [
    {
      "id": 1,
      "clientName": "Acme Corporation",
      "projectName": "Corporate Website Redesign",
      "description": "Redesign and modernize the company's corporate website.",
      "status": "In Progress",
      "priority": "High",
      "startDate": "2026-06-01",
      "dueDate": "2026-07-15"
    }
  ]
}
```

Invalid input returns `422`:

```json
{
  "message": "Client name is required.",
  "errors": {
    "clientName": ["Client name is required."]
  }
}
```

An unknown project id returns `404` with `"Project not found."`

Example from the project folder, while the stack is running:

```bash
curl http://localhost:8080/projects
curl http://localhost:8080/projects/1
```

## If something does not start

- Docker Desktop is running.
- Ports 8080, 5173, and 3307 are not already taken. Change `APP_PORT` or `FORWARD_DB_PORT` if they are.
- The page is blank on the first visit: Docker is still compiling the Vue files. Wait until `docker compose logs node` shows Vite is ready, then refresh. The first compile can take a minute. Vite is at http://localhost:5173.
- The project list is empty: `docker compose exec app php artisan migrate:fresh --seed`
- Read errors with `docker compose logs app` and `docker compose logs node`.

`APP_DEBUG` is `true` in `.env` so local errors are visible. Set it to `false` if this app is reachable by anyone other than you. That keeps stack traces out of responses.

## Deploy a test site on Render

Local Docker Compose stays the same. Render cannot start that Compose file, because Render runs one container. `docker/render/Dockerfile` is a second image for Render only. It puts Nginx and PHP in one container, builds the Vue files, and listens on the port Render assigns.

On the Render create screen:

| Field | Value |
| --- | --- |
| Language | Docker |
| Branch | `main` |
| Region | Singapore, or another region close to the database |
| Root Directory | leave blank |
| Dockerfile Path | `docker/render/Dockerfile` |
| Build Command | leave blank |
| Start Command | leave blank |

Render does not include MySQL. Create a MySQL database somewhere the service can reach, then add these environment variables on the Render service before the first deploy:

| Variable | Value |
| --- | --- |
| `APP_KEY` | A Laravel key. On your computer run `php artisan key:generate --show` and paste the result. |
| `APP_URL` | The Render URL, such as `https://taskmanagement.onrender.com` |
| `APP_DEBUG` | `false` |
| `DB_HOST` | The MySQL hostname |
| `DB_PORT` | `3306`, unless the database provider says otherwise |
| `DB_DATABASE` | The database name |
| `DB_USERNAME` | The database user |
| `DB_PASSWORD` | The database password |

The first boot creates the tables and loads the 12 sample projects. Later boots keep the rows already stored.

Push `docker/render/Dockerfile` to `main` before Render builds. Render can only see files that are on GitHub.

## Where the code lives

- `app/Http/Requests/Project` validates input.
- `app/Data` builds the payload, list query, and service result.
- `app/Services/ProjectService.php` creates, updates, lists, finds, and deletes projects.
- `app/Http/Resources/ProjectResource.php` is the only JSON shape sent to the browser.
- `app/Http/Controllers/Api/ProjectController.php` is the REST API.
- `resources/js/Pages/Projects/Index.vue` is the screen. Dialogs and the table are components under `resources/js/Components`.
- `database/migrations` creates the table. `database/seeders/ProjectSeeder.php` loads the sample projects.
