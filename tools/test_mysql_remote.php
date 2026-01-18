<?php
// Simple remote MySQL connectivity tester for Cloud SQL and other hosts
// Usage: open this page and optionally pass query params:
//   ?host=34.138.246.29&user=YOUR_USER&pass=YOUR_PASS&db=YOUR_DB[&port=3306]

// Load project config to fall back on configured DB constants when params are missing
@include_once(dirname(__DIR__).'/config.php');

$host = isset($_GET['host']) ? $_GET['host'] : (defined('DB_SERVER') ? DB_SERVER : 'localhost');
$user = isset($_GET['user']) ? $_GET['user'] : (defined('DB_USERNAME') ? DB_USERNAME : 'root');
$pass = isset($_GET['pass']) ? $_GET['pass'] : (defined('DB_PASSWORD') ? DB_PASSWORD : '');
$db   = isset($_GET['db'])   ? $_GET['db']   : (defined('DB_NAME') ? DB_NAME : '');
$port = isset($_GET['port']) ? (int)$_GET['port'] : 3306;

$result = [
  'ok' => false,
  'error' => null,
  'server_version' => null,
  'host_info' => null,
  'client_info' => null,
  'db' => $db,
  'host' => $host,
  'port' => $port,
];

// Try connecting
$mysqli = @new mysqli($host, $user, $pass, $db, $port);
if ($mysqli && !$mysqli->connect_errno) {
  $result['ok'] = true;
  $result['host_info'] = $mysqli->host_info;
  $result['client_info'] = $mysqli->client_info;
  // Probe server version via query (works even without permissions to SHOW)
  if ($qry = $mysqli->query('SELECT VERSION() AS v')) {
    if ($row = $qry->fetch_assoc()) $result['server_version'] = $row['v'];
    $qry->close();
  }
  // Quick sanity check on permissions
  $mysqli->query('SELECT 1');
  $mysqli->close();
} else {
  $result['error'] = $mysqli ? $mysqli->connect_error : 'Unable to initialize mysqli';
}

// HTML output
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Remote MySQL Connectivity Test</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial; margin: 24px; }
    .card { border: 1px solid #ddd; border-radius: 8px; padding: 16px; max-width: 900px; }
    .ok { color: #0a7c2f; }
    .err { color: #b00020; }
    code { background: #f5f5f5; padding: 2px 4px; border-radius: 4px; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    label { font-weight: 600; }
    input { width: 100%; padding: 8px; }
    .actions { margin-top: 12px; }
    .note { font-size: 13px; color: #555; }
    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
  </style>
  </head>
<body>
  <h2>Remote MySQL Connectivity Test</h2>
  <div class="card">
    <form method="get" class="grid">
      <div>
        <label>Host/IP</label>
        <input type="text" name="host" value="<?php echo htmlspecialchars($host); ?>" placeholder="e.g., 34.138.246.29" />
      </div>
      <div>
        <label>Port</label>
        <input type="number" name="port" value="<?php echo (int)$port; ?>" />
      </div>
      <div>
        <label>Username</label>
        <input type="text" name="user" value="<?php echo htmlspecialchars($user); ?>" />
      </div>
      <div>
        <label>Password</label>
        <input type="password" name="pass" value="<?php echo htmlspecialchars($pass); ?>" />
      </div>
      <div>
        <label>Database</label>
        <input type="text" name="db" value="<?php echo htmlspecialchars($db); ?>" />
      </div>
      <div class="actions">
        <button type="submit">Test Connection</button>
      </div>
    </form>
  </div>

  <h3>Result</h3>
  <div class="card">
    <?php if ($result['ok']): ?>
      <p class="ok">Connection successful.</p>
      <ul>
        <li>Server: <span class="mono"><?php echo htmlspecialchars($result['host']); ?>:<?php echo (int)$result['port']; ?></span></li>
        <li>Database: <span class="mono"><?php echo htmlspecialchars($result['db']); ?></span></li>
        <li>Server Version: <span class="mono"><?php echo htmlspecialchars($result['server_version'] ?: 'unknown'); ?></span></li>
        <li>Host Info: <span class="mono"><?php echo htmlspecialchars($result['host_info']); ?></span></li>
        <li>Client Info: <span class="mono"><?php echo htmlspecialchars($result['client_info']); ?></span></li>
      </ul>
    <?php else: ?>
      <p class="err">Connection failed.</p>
      <p><strong>Error:</strong> <span class="mono"><?php echo htmlspecialchars($result['error']); ?></span></p>
      <p class="note">Common fixes for Cloud SQL (MySQL):</p>
      <ul>
        <li>Add your public IP in Cloud SQL <em>Authorized networks</em> when using Public IP.</li>
        <li>Ensure a MySQL user exists with the given username/password and has access to the database.</li>
        <li>Double-check the database name and that the instance is running.</li>
        <li>Prefer using the Cloud SQL Auth Proxy for secure access if public IP is restricted.</li>
      </ul>
    <?php endif; ?>
  </div>

  <h3>How to use with XAMPP/PHP</h3>
  <div class="card">
    <ul>
      <li>Set environment variables or update <code>initialize.php</code> to use your remote DB values:</li>
    </ul>
<pre class="mono">DB_SERVER=34.138.246.29
DB_USERNAME=&lt;your-mysql-username&gt;
DB_PASSWORD=&lt;your-mysql-password&gt;
DB_NAME=&lt;your-database-name&gt;
</pre>
    <p class="note">Port defaults to 3306. If your port differs, we can add an explicit port support in the connection class.</p>
    <p class="note">For Cloud SQL: you may also run the Cloud SQL Auth Proxy locally and point <code>DB_SERVER</code> to <code>127.0.0.1</code> with the proxy port.</p>
  </div>
</body>
</html>
<?php
// End of file
?>