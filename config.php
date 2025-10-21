<?php
class Database {
    private $host = "localhost";
    private $db_name = "perpustakaan_assalafiyyah";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// Upload configuration
define('UPLOAD_COVER_DIR', 'uploads/covers/');
define('UPLOAD_PDF_DIR', 'uploads/pdf/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_COVER_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_PDF_TYPES', ['pdf']);

// Create upload directories if not exists
if (!file_exists(UPLOAD_COVER_DIR)) {
    mkdir(UPLOAD_COVER_DIR, 0777, true);
}
if (!file_exists(UPLOAD_PDF_DIR)) {
    mkdir(UPLOAD_PDF_DIR, 0777, true);
}
?>