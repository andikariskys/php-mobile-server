<?php
/**
 * SQLite Database Connection Helper
 * Stores system configuration persistently.
 */

$db_file = __DIR__ . '/settings.db';
$db = new SQLite3($db_file);

// Create settings table if it doesn't exist
$db->exec("CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT
)");

// Prepopulate/Ensure all keys exist using INSERT OR IGNORE
$defaults = [
    'username' => 'admin',
    'password' => 'admin',
    'ip_camera_port' => '4444',
    'ip_camera_url' => '',
    'terminal_port' => '3001',
    'terminal_url' => '',
    'dashboard_bg' => '',
    'use_prefix' => '0',
    'ip_address' => '192.168.1.100',
    'subnet_mask' => '255.255.255.0',
    'volume_call' => '50',
    'volume_system' => '50',
    'volume_ring' => '50',
    'volume_music' => '50',
    'volume_alarm' => '50',
    'volume_notification' => '50'
];

foreach ($defaults as $key => $val) {
    // INSERT OR IGNORE ensures existing custom configurations are not overwritten
    $stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (:key, :value)");
    $stmt->bindValue(':key', $key, SQLITE3_TEXT);
    $stmt->bindValue(':value', $val, SQLITE3_TEXT);
    $stmt->execute();
}

/**
 * Get setting value from SQLite
 */
function db_get($key, $default = null) {
    global $db;
    $stmt = $db->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->bindValue(':key', $key, SQLITE3_TEXT);
    $res = $stmt->execute();
    $row = $res->fetchArray(SQLITE3_ASSOC);
    return $row ? $row['value'] : $default;
}

/**
 * Set/Update setting value in SQLite
 */
function db_set($key, $value) {
    global $db;
    $stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (:key, :value)");
    $stmt->bindValue(':key', $key, SQLITE3_TEXT);
    $stmt->bindValue(':value', $value, SQLITE3_TEXT);
    return $stmt->execute();
}
