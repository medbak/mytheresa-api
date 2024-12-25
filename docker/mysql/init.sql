CREATE DATABASE IF NOT EXISTS mytheresa;
USE mytheresa;

-- Grant privileges
GRANT ALL PRIVILEGES ON mytheresa.* TO 'app'@'%';
FLUSH PRIVILEGES;