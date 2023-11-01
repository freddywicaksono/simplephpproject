
<?php
require_once __DIR__ . "/../vendor/autoload.php"; // add this library using: composer require vlucas/phpdotenv

class Database {
    private $pdo;

    public function __construct() {
        // Load environment variables from the .env file
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
        $dotenv->load();

        $host = $_ENV["DB_HOST"];
        $dbname = $_ENV["DB_DATABASE"];
        $username = $_ENV["DB_USERNAME"];
        $password = $_ENV["DB_PASSWORD"];
        $charset = $_ENV["DB_CHARSET"];
        
        $this->connect($host, $dbname, $username, $password, $charset);
    }

    private function connect($host, $dbname, $username, $password, $charset) {
        try {
            $this->pdo = new PDO("mysql:host={$host};dbname={$dbname};charset={$charset}", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);

        if ($stmt) {
            $stmt->execute($params);
            return $stmt;
        } else {
            die("Error in query: " . print_r($this->pdo->errorInfo(), true));
        }
    }

    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);

            if ($stmt) {
                $stmt->execute($params);
                return $stmt;
            } else {
                die("Error in query: " . print_r($this->pdo->errorInfo(), true));
            }
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }

    public function getLastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function createUsersTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `email` varchar(255) NOT NULL,
          `nama` varchar(50) NOT NULL,
          `password` varchar(255) NOT NULL,
          `level` enum('admin','dosen','mahasiswa') NOT NULL DEFAULT 'mahasiswa',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        
        try {
            $this->executeQuery($sql);
            echo "Table 'users' created successfully.";
        } catch (PDOException $e) {
            die("Error creating 'users' table: " . $e->getMessage());
        }
    }
}
