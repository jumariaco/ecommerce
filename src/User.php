<?php
class User {
    private $conn;
    private $table = "users";

    public $id;
    public $nom;
    public $email;
    public $password;
    public $is_admin;
    public $date_creation;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Enregistrer un utilisateur
    public function enregistrerUser() {
        $query = "INSERT INTO " . $this->table . " (nom, email, password, is_admin) VALUES (:nom, :email, :password, :is_admin)";
        $stmt = $this->conn->prepare($query);

        // Sécuriser les données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT); // Hacher le mot de passe
        $this->is_admin = $this->is_admin ? 1 : 0;

        // Lier les valeurs
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':is_admin', $this->is_admin);

        return $stmt->execute();
    }

    // Vérifier l'authentification de l'utilisateur
    public function verifierUser($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->nom = $user['nom'];
            $this->email = $user['email'];
            $this->is_admin = $user['is_admin'];
            return true;
        }
        return false;
    }
}
