<?php
  $url = parse_url(getenv('DATABASE_URL'));

  $dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));

  $pdo = new PDO($dsn, $url['user'], $url['pass']);

$sql = "update poster set checked = ".htmlspecialchars($_POST["checked"])." , update_at = CURRENT_TIMESTAMP + interval '9 hour' where id = ".htmlspecialchars($_POST["id"]);
$stmt = $pdo->query($sql);

?>
