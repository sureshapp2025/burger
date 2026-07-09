# Drupal Multisite Setup with DDEV (Separate Database)

This document outlines the best practices for setting up a Drupal multisite named `subsite` (intended to be placed inside your `burger` project directory) using DDEV, ensuring the subsite has its own separate database.

## Prerequisites
- A working Drupal project running on DDEV.
- DDEV started (`ddev start`).

---

## Step 1: Create the Subsite Directory Structure
Drupal best practices dictate that multisites live within the `web/sites/` (or just `sites/` depending on your composer template) directory.

1. Navigate to your Drupal project's `sites` directory.
2. Create the folder for the subsite:
   ```bash
   mkdir -p web/sites/subsite
   ```
3. Copy the default settings file to your new subsite folder to act as a template:
   ```bash
   cp web/sites/default/default.settings.php web/sites/subsite/settings.php
   ```

## Step 2: Configure DDEV for a Separate Database
Instead of running a completely separate database container, the cleanest way to handle multisite databases in DDEV is to create an additional database inside the existing DDEV MariaDB/MySQL container.

1. Open your `.ddev/config.yaml` file.
2. Add a `post-start` hook to automatically create the separate database (`subsite_db`) when DDEV starts. Add this to the bottom of the file:

```yaml
hooks:
  post-start:
    - exec: mysql -uroot -proot -hdb -e "CREATE DATABASE IF NOT EXISTS subsite_db; GRANT ALL ON subsite_db.* TO 'db'@'%';"
```

3. Restart DDEV to trigger the hook and create the database:
   ```bash
   ddev restart
   ```

## Step 3: Map the Hostname in DDEV
You need DDEV to route traffic for your new subsite.

1. In `.ddev/config.yaml`, find the `additional_hostnames` array.
2. Add your subsite name to it (this will make it accessible at `https://subsite.ddev.site`):

```yaml
additional_hostnames:
  - subsite
```
3. Run `ddev restart` again to apply the routing changes.

## Step 4: Configure `sites.php`
Drupal needs to know how to map the incoming domain (`subsite.ddev.site`) to the specific directory (`sites/subsite`).

1. If it doesn't exist, copy the example `sites.php`:
   ```bash
   cp web/sites/example.sites.php web/sites/sites.php
   ```
2. Open `web/sites/sites.php` and append the following mapping at the bottom:
   ```php
   $sites['subsite.ddev.site'] = 'subsite';
   ```

## Step 5: Configure the Subsite's Database Credentials
Now, link your new subsite folder to the newly created database.

1. Open `web/sites/subsite/settings.php`.
2. Scroll to the bottom and add the database configuration for `subsite_db`:

```php
$databases['default']['default'] = array (
  'database' => 'subsite_db',
  'username' => 'db',
  'password' => 'db',
  'prefix' => '',
  'host' => 'db',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

// DDEV-specific settings hash salt (good practice)
$settings['hash_salt'] = 'some-random-hash-salt-for-subsite';
```

## Step 6: Install the Subsite
Now that the infrastructure, routing, and database are configured, you can install the Drupal subsite.

You can do this via the browser at `https://subsite.ddev.site` or using Drush inside DDEV:

```bash
ddev drush -l https://subsite.ddev.site site-install standard -y --site-name="Subsite Burger"
```

## Step 7: Shared vs. Separate Configuration (Best Practices)
* **Modules/Themes:** Place modules and themes shared across all sites in `web/modules/` and `web/themes/`. If a module/theme is *only* for the subsite, place it in `web/sites/subsite/modules/` and `web/sites/subsite/themes/`.
* **Files:** Drupal will automatically manage public files for the subsite in `web/sites/subsite/files/`. Ensure this folder is git-ignored in your `.gitignore` file.
