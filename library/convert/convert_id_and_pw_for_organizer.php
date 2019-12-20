<?php
require_once "../../Class/StringEncrypt.php";
require_once "../../Class/StringEncryptOld.php";

$crypt = new StringEncrypt();
$cryptOld = new StringEncryptOld();

$config = array(
  'host'     => '127.0.0.1',
  'dbname'   => 'kaizen',
  'dbuser'   => 'kaizen_db',
  'password' => 'gvnO3yqCXQwmK96z'
);

try {
  $dsn ="mysql:host=$config[host];dbname=$config[dbname];charset=utf8";
  $db = new PDO($dsn, $config["dbuser"], $config["password"]);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  $sql = "SELECT * FROM tbl_organizer";
  $statement = $db->prepare($sql);
  $statement->execute();
  $result = $statement->fetchAll(PDO::FETCH_ASSOC);

  foreach ($result as $organizer) {
    $plainLoginID = $cryptOld->decrypt($organizer["id"]);
    $plainPassword = $cryptOld->decrypt($organizer["password"]);
    echo sprintf("%s %s %s<br>", $organizer["organizer_id"], $plainLoginID, $plainPassword);

    $newLoginID = $crypt->encrypt($plainLoginID);
    $newPassword = $crypt->encrypt($plainPassword);
    echo sprintf("%s %s %s<br>", $organizer["organizer_id"], $newLoginID, $newPassword);

    $plainLoginID = $crypt->decrypt($newLoginID);
    $plainPassword = $crypt->decrypt($newPassword);
    echo sprintf("%s %s %s<br>", $organizer["organizer_id"], $plainLoginID, $plainPassword);

    $sql = "UPDATE tbl_organizer
               SET password = :password,
                   id = :id
             WHERE organizer_id = :organizer_id
    ";
    $statement = $db->prepare($sql);

    $statement->bindValue(":password", $newPassword);
    $statement->bindValue(":id", $newLoginID);
    $statement->bindValue(":organizer_id", $organizer["organizer_id"]);

    $statement->execute();
  }

  $db = NULL;
} catch (Exception $e) {
  echo "Error!:" . h($e->getMessage());
  die();
}
?>
