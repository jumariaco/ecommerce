<?php
class Commande {
    private $conn;
    private $table = "commandes";

    public $id;
    public $client;
    public $is_admin;
    public $date_commande;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ajouter une commande
    public function ajouterCommande() {
        $query = "INSERT INTO " . $this->table . " (client, is_admin, date_commande) VALUES (:client, :is_admin, :date_commande)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':client', $this->client);
        $stmt->bindParam(':is_admin', $this->is_admin);
        $stmt->bindParam(':date_commande', $this->date_commande);
        return $stmt->execute();
    }

    // Modifier une commande existante
    public function modifierCommande() {
        $query = "UPDATE " . $this->table . " SET client = :client, is_admin = :is_admin WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':client', $this->client);
        $stmt->bindParam(':is_admin', $this->is_admin);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Supprimer une commande existante
    public function supprimerCommande() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Lister toutes les commandes
    public function listerCommandes() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Récupérer une commande par ID
    public function voirCommande() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->client = $row['client'];
            $this->is_admin = $row['is_admin'];
            $this->date_commande = $row['date_commande'];
        }
    }
}
