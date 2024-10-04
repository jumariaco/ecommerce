<?php
class Database {
    private $host = '127.0.0.1'; // Utilisez 127.0.0.1 pour éviter les problèmes de socket avec localhost
    private $port = '3306'; // Port par défaut de MySQL dans XAMPP
    private $db_name = 'ecommerce'; // Nom de la base de données que vous avez créée dans phpMyAdmin
    private $username = 'root'; // Utilisateur par défaut de XAMPP
    private $password = ''; // Laissez vide pour XAMPP par défaut
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Utiliser 127.0.0.1 et le port 3306 pour éviter le problème de socket avec localhost
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Erreur de connexion : " . $exception->getMessage();
        }

        return $this->conn;
    }
}
