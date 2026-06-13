<?php

/**
 * @file
 * Docker environment database and settings overrides for Drupal.
 */

// Database connection details from environment variables
$databases['default']['default'] = [
  'database' => getenv('DB_NAME') ?: 'drupal',
  'username' => getenv('DB_USER') ?: 'drupal_user',
  'password' => getenv('DB_PASSWORD') ?: 'drupal_secure_password',
  'host' => getenv('DB_HOST') ?: 'db',
  'port' => getenv('DB_PORT_INTERNAL') ?: '3306',
  'driver' => 'mysql',
  'prefix' => '',
  'collation' => 'utf8mb4_general_ci',
];

// Configure trusted hosts for local development
$settings['trusted_host_patterns'] = [
  '^localhost$',
  '^127\.0\.0\.1$',
  '^web$',
  '^php$',
  '^192\.168\.\d+\.\d+$', // Allow local network access
];

// Set private files and temp directories
$settings['file_private_path'] = '../private';
$settings['file_temp_path'] = '/tmp';

// Enable local development services (like disabling cache if needed)
$settings['container_yamls'][] = __DIR__ . '/services.yml';

// Optional: Enable verbose error logging in dev
$config['system.logging']['error_level'] = 'verbose';
