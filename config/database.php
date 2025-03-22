<?php
class Database {
    private $host = "localhost";
    private $db_name = "employee_management";
    private $username = "root"; // Change if necessary
    private $password = "root"; // Change if necessary
    public $conn;


    private $testing = false;

    private $prod_host = "localhost";
    private $prod_db_name = "u653477705_intern_offer";
    private $prod_username = "u653477705_intern_offer"; // Change if necessary
    private $prod_password = "Avinash@9502b";

    public function getConnection() {
        $this->conn = null;

        try {
            if($this->testing){
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                    $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }else{
                $this->conn = new PDO("mysql:host=" . $this->prod_host . ";dbname=" . $this->prod_db_name,
                    $this->prod_username, $this->prod_password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        } catch (PDOException $exception) {
            echo "Connection failed: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
