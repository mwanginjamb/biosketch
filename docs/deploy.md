# Deploying BioSketch

How to install BioSketch on a server for the first time, and how to update an existing installation.

BioSketch is a Yii 2 (advanced template) PHP application. The public web root is **`frontend/web`**. The `backend` app isn't used.

- [Requirements](#requirements)
- [First-time installation](#first-time-installation)
- [Updating an existing installation](#updating-an-existing-installation)
- [Configuration reference](#configuration-reference)
- [Security checklist](#security-checklist)
- [Troubleshooting](#troubleshooting)

---

## Requirements

- PHP **8.2+** with `pdo_mysql`, `mbstring`, `gd`, `curl`, `intl`, `openssl`, `fileinfo`
  (`gd` and `mbstring` are needed for PDF generation; run `php requirements.php` to check)
- MySQL / MariaDB
- Composer 2
- A web server (nginx or Apache) serving `frontend/web` over **HTTPS**. Outside dev mode the session cookie is marked `Secure`, so login only works over HTTPS.
- Outbound HTTPS from the server to the staff directory (`ASSIGNEE_ENDPOINT`) and to your SMTP server

Node.js is **not** needed on the server. The compiled CSS (`frontend/web/css/tailwind.css`) is committed to the repository.

---

## First-time installation

### 1. Get the code and dependencies

```bash
git clone <repository-url> biosketch
cd biosketch
git checkout <release-branch>          # e.g. master once develop is merged

composer install --no-dev --optimize-autoloader
```

### 2. Initialise the production environment

```bash
php init --env=Production --overwrite=n
```

This generates the entry scripts and local config files from `environments/prod`. It:

- sets `YII_DEBUG=false` and `YII_ENV=prod` in `frontend/web/index.php` (the debug toolbar and module are **not** loaded)
- generates the cookie validation keys
- makes `frontend/runtime`, `frontend/web/assets`, `console/runtime` (and backend equivalents) writable

Check the result:

```bash
grep -E "YII_DEBUG|YII_ENV" frontend/web/index.php
# defined('YII_DEBUG') or define('YII_DEBUG', false);
# defined('YII_ENV') or define('YII_ENV', 'prod');
```

> Never copy `frontend/web/index.php` or `common/config/*-local.php` from a development machine. They enable debug mode and may contain local credentials.

### 3. Create the `.env` file

Create `.env` in the project root (it is git-ignored). Restrict its permissions (e.g. `chmod 640`, owned by the web server user's group).

```dotenv
# Database
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=biosketch
DB_USER=biosketch
DB_PASSWORD=change-me

# Outgoing mail (verification and password-reset emails)
SMTP_HOST=smtp.example.org
SMTP_PORT=587
SMTP_USERNAME=biosketch@kemri.go.ke
SMTP_PASSWORD=change-me
SMTP_ENCRYPTION=tls

# Application
APP_NAME="Researcher Bio Sketch App"
APP_TIMEZONE=Africa/Nairobi
DEVELOPER="KEMRI ICT"

# Staff directory used at sign-up (employee number lookup)
ASSIGNEE_ENDPOINT=https://<staff-directory-host>/<path>
# ASSIGNEE_CA_BUNDLE=/etc/ssl/certs/kemri-internal-ca.pem   # only if the directory uses an internal CA
```

| Variable | Required | Notes |
|---|---|---|
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` | yes | Used by `common/config/main.php`. |
| `SMTP_HOST`, `SMTP_PORT`, `SMTP_USERNAME`, `SMTP_PASSWORD` | yes | Mail transport. `SMTP_USERNAME` is also the sender and admin address. |
| `SMTP_ENCRYPTION` | no | Informational; the transport negotiates TLS on port 587. |
| `APP_NAME` | no | Shown in titles and emails. |
| `APP_TIMEZONE` | no | Defaults to `Africa/Nairobi`. Used for dates on pages and PDFs. |
| `DEVELOPER` | no | Shown in the PDF footer and metadata. |
| `ASSIGNEE_ENDPOINT` | yes | Staff directory URL used to verify staff IDs at sign-up. |
| `ASSIGNEE_CA_BUNDLE` | no | Path to a PEM CA bundle when the directory's certificate is issued by an internal CA. |
| `ASSIGNEE_VERIFY_SSL` | **do not set** | Setting it to `false` disables certificate checks on the staff directory. It exists only for local development machines without a CA bundle. On servers, leave it unset (checks on) and use `ASSIGNEE_CA_BUNDLE` if needed. |

### 4. Create the database and run migrations

```sql
CREATE DATABASE biosketch CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'biosketch'@'localhost' IDENTIFIED BY 'change-me';
GRANT ALL PRIVILEGES ON biosketch.* TO 'biosketch'@'localhost';
```

```bash
php yii migrate --interactive=0
```

This creates all application tables, Yii's RBAC tables, the roles and permissions (`researcher`, `drd`, `admin`), the employee columns on `user`, and the API token table.

### 5. Create the first administrator

The RBAC screens are only available to admins, so the first admin is assigned from the command line:

1. Sign up through the web app (`/site/signup`) and verify the email address.
2. Assign the admin role to that account:

   ```bash
   php yii rbac/assign-admin <username>
   php yii rbac/list-admins        # check
   ```

After that, roles can be managed in the web app under **RBAC Mgt** in the sidebar of the BioSketch list. Every new account automatically gets the `researcher` role. Assign `drd` (read-only access to every biosketch) or `admin` as needed:

```bash
php yii rbac/assign drd <username>
php yii rbac/roles <username>
```

### 6. Configure the web server

Point the document root at `frontend/web` and route unknown paths to `index.php` (pretty URLs are enabled).

User-uploaded profile photos are stored in `frontend/web/uploads/`, which must be **writable** by the web server and must **never execute scripts**:

- **Apache:** `frontend/web/uploads/.htaccess` (committed) already blocks script execution. Make sure `AllowOverride` permits it.
- **nginx:** add this inside the `server` block (see `vagrant/nginx/app.conf`):

  ```nginx
  root /path/to/biosketch/frontend/web;

  location / {
      try_files $uri $uri/ /index.php$is_args$args;
  }

  # User uploads are static data: never execute scripts from there.
  location ^~ /uploads/ {
      location ~* \.(php\d?|phtml|phar)$ { deny all; }
      try_files $uri =404;
  }

  location ~ \.php$ {
      include fastcgi_params;
      fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
      fastcgi_pass unix:/run/php/php8.2-fpm.sock;   # adjust
  }

  location ~ /\.(ht|svn|git) {
      deny all;
  }
  ```

```bash
mkdir -p frontend/web/uploads/researchers/profile
chown -R www-data:www-data frontend/web/uploads frontend/runtime frontend/web/assets console/runtime   # adjust user
```

### 7. Smoke test

- [ ] `https://<host>/` loads the login page over HTTPS, with no debug toolbar in the bottom-right corner
- [ ] Sign-up: entering a staff ID fills in username and email; the verification email arrives
- [ ] Login works; the home page shows the notification bell and avatar menu
- [ ] Create a biosketch with a photo; the profile and **BioSketch PDF** pages load, and **Download PDF** saves a file
- [ ] As an admin: **BioSketch List** and **RBAC Mgt → Roles** load
- [ ] `https://<host>/apiv1` returns the API summary as JSON (see [API.md](API.md))

---

## Updating an existing installation

```bash
cd /path/to/biosketch
git pull                                         # or check out the new release tag
composer install --no-dev --optimize-autoloader
php yii migrate --interactive=0
rm -rf frontend/runtime/cache/* console/runtime/cache/*
```

- **Migrations:** always run them. Releases may add tables, columns, roles or permissions.
- **Clearing the caches:** RBAC roles and permissions are cached per application. Clear the runtime caches after every update so the web app picks up role and permission changes made by migrations. (`php yii cache/flush-all` only clears the console app's cache, not the web app's.)
- **CSS:** `frontend/web/css/tailwind.css` comes compiled in the repository, so there's nothing to build on the server. Developers rebuild it with `npm install && npm run build:css` before committing view changes.
- **Do not** re-run `php init` on an existing installation unless you intend to regenerate the local config. If you do, use `--overwrite=n`.

### Changes in the current release

If you're upgrading an installation from before November 2026, the migrations add:

| Migration | Adds |
|---|---|
| `m261117_120000_add_employee_columns_to_user_table` | `staff_id`, `department`, `department_code` on `user` |
| Yii RBAC migrations (`@yii/rbac/migrations`) | `auth_item`, `auth_item_child`, `auth_assignment`, `auth_rule` |
| `m261117_130000_create_admin_role` | the `admin` role |
| `m261117_140000_create_user_api_token_table` | API bearer tokens (hashed) |
| `m261117_150000_seed_rbac_roles_and_permissions` | `researcher` and `drd` roles and all permissions, plus `researcher` assigned to every existing user |

After migrating, assign the first admin as in [step 5](#5-create-the-first-administrator).

---

## Configuration reference

Optional application parameters go in `common/config/params-local.php`:

```php
return [
    // API token lifetime in seconds (default 30 days; 0 = never expires)
    'api.tokenTtl' => 30 * 24 * 3600,

    // After how many days a biosketch section counts as "not updated" in the notification bell (default 365)
    'biosketch.staleAfterDays' => 365,
];
```

---

## Security checklist

- [ ] `YII_DEBUG=false`, `YII_ENV=prod` in `frontend/web/index.php`, and no debug module in `frontend/config/main-local.php`
- [ ] `ASSIGNEE_VERIFY_SSL` is **not** set in `.env` (certificate checks on)
- [ ] Site served only over HTTPS
- [ ] `.env` is not readable by other users and is not inside the web root
- [ ] `frontend/web/uploads` is writable but cannot execute scripts
- [ ] At least one admin is assigned, and only the people who need it have `admin` or `drd`
- [ ] Database user has privileges only on the BioSketch database

---

## Troubleshooting

| Symptom | Likely cause |
|---|---|
| Login form reloads without logging in | Site served over plain HTTP; the session cookie is `Secure` outside dev. Use HTTPS. |
| Sign-up says "Employee not found" for a valid staff ID | Server can't reach `ASSIGNEE_ENDPOINT`, or certificate verification failed. Check outbound access and set `ASSIGNEE_CA_BUNDLE` for an internal CA. Look for "Staff directory" errors in `frontend/runtime/logs/app.log`. |
| Verification emails not arriving | SMTP settings in `.env` (host, port, credentials). Check `frontend/runtime/logs/app.log` and the SMTP server's logs. |
| Home page error mentioning `auth_assignment` / `auth_item` | Migrations not run: `php yii migrate`. |
| New role or permission not taking effect | Clear `frontend/runtime/cache/*`. |
| Pages look unstyled after a code change | `tailwind.css` wasn't rebuilt and committed by the developer (`npm run build:css`). |
| Database connection uses `yii2advanced`/`root` | `common/config/main-local.php` came from an old environment template and overrides `.env`. Remove its `db` block. |
