<?php
class Produit {
    private $conn;
    private $table = "produits";

    public $id;
    public $nom;
    public $description;
    public $prix;
    public $stock;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ajouter un produit
    public function ajouterProduit() {
        $query = "INSERT INTO " . $this->table . " (nom, description, prix, stock) VALUES (:nom, :description, :prix, :stock)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':prix', $this->prix);
        $stmt->bindParam(':stock', $this->stock);
        return $stmt->execute();
    }

    // Modifier un produit existant
    public function modifierProduit() {
        $query = "UPDATE " . $this->table . " SET nom = :nom, description = :description, prix = :prix, stock = :stock WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':prix', $this->prix);
        $stmt->bindParam(':stock', $this->stock);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Supprimer un produit existant
    public function supprimerProduit() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Lister tous les produits
    public function listerProduits() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Récupérer un produit par ID
    public function voirProduit() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->nom = $row['nom'];
            $this->description = $row['description'];
            $this->prix = $row['prix'];
            $this->stock = $row['stock'];
        }
    }
}
