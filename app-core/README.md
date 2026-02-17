# CodeIgniter 4 Framework

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds the distributable version of the framework.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the _public_ folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's _public_ folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter _public/..._, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Contributing

We welcome contributions from the community.

Please read the [_Contributing to CodeIgniter_](https://github.com/codeigniter4/CodeIgniter4/blob/develop/CONTRIBUTING.md) section in the development repository.

## Deployment to SiteGround (Shared Hosting)

To deploy this CodeIgniter 4 project to SiteGround or similar shared hosting:

### 1. File Structure

Upload all files (including `app`, `public`, `system`, `vendor`, `writable`, `.env`) to your `public_html` or a subdirectory.
**Important**: Do NOT move `index.php` out of `public/` unless you know exactly how to secure `app/` and `system/`.

### 2. Redirect to Public Folder

Create a `.htaccess` file in your root folder (outside `public`) to redirect all traffic to the `public/` folder securely:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### 3. Database Configuration

1. Create a MySQL Database & User in SiteGround Site Tools.
2. Edit `.env` (rename from `env` if needed):
   ```dotenv
   CI_ENVIRONMENT = production
   database.default.hostname = localhost
   database.default.database = your_db_name
   database.default.username = your_db_user
   database.default.password = your_db_pass
   database.default.DBDriver = MySQLi
   ```
3. Import your local database SQL dump or run migrations if you have SSH access: `php spark migrate`.

### 4. Permissions

Ensure `writable/` folder and its subfolders are writable (755 or 777 depending on server config).

### 5. Security Checklist

- Set `CI_ENVIRONMENT = production` in `.env`.
- Disable `display_errors` in php.ini or `.htaccess` if possible.
- Ensure `system` and `app` folders are denied access (CI4 handles this via `.htaccess` inside them).
