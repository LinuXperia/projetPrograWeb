<?php
include("config.php");
include("functions.php");

$createDb = "CREATE DATABASE IF NOT EXISTS {$GLOBALS['dbName']};";

$cUsers = "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` varchar(255) NOT NULL,
  `user` varchar(64) NOT NULL,
  `pwd` varchar(64) NOT NULL,
  `last_con` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT unicity_users UNIQUE (`mail`, `user`)
) ENGINE=InnoDB;";

$cComments = "CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `author` int(11) NOT NULL,
  `link` int(11) NOT NULL,
  `date` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT fk_author_comments FOREIGN KEY (`author`) REFERENCES `users`(`id`),
  CONSTRAINT fk_link_comments FOREIGN KEY (`link`) REFERENCES `links`(`id`)
) ENGINE=InnoDB;";

$cLinks = "CREATE TABLE IF NOT EXISTS `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(2000) NOT NULL,
  `url` varchar(2000) NOT NULL,
  `author` int(11) NOT NULL,
  `date` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`author`) REFERENCES `users`(`id`)
) ENGINE=InnoDB;";

$cLikes = "CREATE TABLE IF NOT EXISTS `likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` int(11),
  `link` int(11),
  `value` int(2) NOT NULL,
  `date` timestamp NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`comment`) REFERENCES `comments`(`id`),
  FOREIGN KEY (`link`) REFERENCES `links`(`id`),
  CONSTRAINT fk_user_likes FOREIGN KEY (`user`) REFERENCES `users`(`id`),
  CONSTRAINT chk_likes_objectUnicity CHECK ((`comment` = null AND `link` != null) OR
    (`link` = null AND `comment` != null)),
  CONSTRAINT chk_likes_value CHECK(`value` = -1 OR `value` = 1)
) ENGINE=InnoDB;";

echo "Connection to MySQL Database.";
echo "<br>";

//Create Database if it doesnt exist
$conn = new mysqli($GLOBALS['dbServ'],$GLOBALS['dbUser'],$GLOBALS['dbPass']);
mysqli_query($conn, $createDb);

$h = con();
query_feedback($h);

if (!$h) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Creating 'users' table.";
echo "<br>";
mysqli_query($h, $cUsers);
query_feedback($h);

echo "Creating 'links' table.";
echo "<br>";
mysqli_query($h, $cLinks);
query_feedback($h);

echo "Creating 'comments' table.";
echo "<br>";
mysqli_query($h, $cComments);
query_feedback($h);

echo "Creating 'likes' table.";
echo "<br>";
mysqli_query($h, $cLikes);
query_feedback($h);

mysqli_query($h, "INSERT INTO users (id, mail, user, pwd, last_con) VALUES (1,'max@gmail.com','max','max', CURRENT_TIMESTAMP)");
mysqli_query($h, "INSERT INTO users (id, mail, user, pwd, last_con) VALUES (2,'bob@gmail.com','bob','bob', CURRENT_TIMESTAMP)");
mysqli_query($h, "INSERT INTO `links` (`text`, `url`, `author`, `date`) VALUES ('Je conseille ce site', 'http://google.fr', '1', CURRENT_TIMESTAMP)");
mysqli_close($h);
addComment("Excellent site pour faire des recherches!", 2, 1);
vote(1,1,1,'comment');


?>
