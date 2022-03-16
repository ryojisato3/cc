<?php
  $url = parse_url(getenv('DATABASE_URL'));

  $dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));

  $pdo = new PDO($dsn, $url['user'], $url['pass']);
  var_dump($pdo->getAttribute(PDO::ATTR_SERVER_VERSION));
try{
	echo "create table";
	$res = $pdo->query('drop table if exists poster');
	$res = $pdo->query('DROP SEQUENCE IF EXISTS users_id_seq;');
	$res = $pdo->query('CREATE SEQUENCE users_id_seq;');
	$sql = "CREATE TABLE poster (
		id int DEFAULT nextval('users_id_seq') PRIMARY KEY,
		kind int,
		name varchar(20),
		number varchar(20),
		longitude varchar(20),
		latitude varchar(20),
		checked boolean not null default false,
		update_at TIMESTAMP
	)";

	$res = $pdo->query($sql);
	
	$res = $pdo->query('drop table if exists relation_poster');
	$res = $pdo->query('DROP SEQUENCE IF EXISTS poster_id_seq;');
	$res = $pdo->query('CREATE SEQUENCE poster_id_seq;');
	$sql = "CREATE TABLE relation_poster (
		id int DEFAULT nextval('poster_id_seq') PRIMARY KEY,
		kind1 int,
		kind2 int
	)";

	$res = $pdo->query($sql);
} catch(PDOException $e) {
    echo $e->getMessage();//Remove or change message in production code
}
?>
