CREATE DATABASE IF NOT EXISTS mytheresa;
USE mytheresa;
CREATE DATABASE IF NOT EXISTS mytheresa_test;

-- Grant privileges
GRANT ALL PRIVILEGES ON mytheresa.* TO 'app'@'%';
GRANT ALL PRIVILEGES ON mytheresa_test.* TO 'app'@'%';
FLUSH PRIVILEGES;