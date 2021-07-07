CREATE USER 'poker-user'@'localhost' IDENTIFIED BY '<password>';
GRANT ALL PRIVILEGES ON `poker`.* TO 'poker-user'@'localhost';
FLUSH PRIVILEGES;
