# Burger Migrations

This module provides an example CSV-based migration for `article` nodes.

Installation & usage (from project root):

1. Enable required modules (example):

```bash
composer require drupal/migrate_plus drupal/migrate_tools drupal/migrate_source_csv
drush en migrate_plus migrate_tools migrate_source_csv burger_migrations -y
```

2. Check migration status:

```bash
drush migrate:status
```

3. Run the article migration:

```bash
drush migrate:import burger_article
```

4. Rollback if needed:

```bash
drush migrate:rollback burger_article
```

Customize `config/install/migrate_plus.migration.burger_article.yml` and `data/articles.csv` for your real source.
