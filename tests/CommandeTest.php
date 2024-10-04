<?php
use PHPUnit\Framework\TestCase;
require_once '../src/Commande.php';

class CommandeTest extends TestCase {

    private $commande;

    protected function setUp(): void {
        // Créer une instance de connexion à la base de données
        $database = new Database();
        $db = $database->getConnection();

        // Passer la connexion à la classe Produit
        $this->commande = new Commande($db);
    }

    public function testAjouterCommandeValide() {
        $this->commande->client = "Client Test";
        $this->commande->produit_id = 1; // Assurez-vous d'avoir un produit avec cet ID dans la base
        $this->commande->quantite_commandee = 10;
        $this->commande->date_commande = date('Y-m-d H:i:s');
        $this->assertTrue($this->commande->ajouterCommande(), "L'ajout d'une commande valide devrait réussir.");
    }

    public function testAjouterCommandeSansNomClient() {
        $this->commande->client = "";
        $this->commande->produit_id = 1;
        $this->commande->quantite_commandee = 10;
        $this->commande->date_commande = date('Y-m-d H:i:s');
        $this->assertFalse($this->commande->ajouterCommande(), "L'ajout d'une commande sans nom de client devrait échouer.");
    }

    public function testAjouterCommandeSansStockSuffisant() {
        $this->commande->client = "Client Test";
        $this->commande->produit_id = 1; // Assurez-vous d'avoir un produit avec peu de stock
        $this->commande->quantite_commandee = 1000; // Quantité supérieure au stock
        $this->commande->date_commande = date('Y-m-d H:i:s');
        $this->assertFalse($this->commande->ajouterCommande(), "L'ajout d'une commande avec un stock insuffisant devrait échouer.");
    }

    public function testModifierCommandeValide() {
        $this->commande->id = 1; // Assurez-vous d'avoir une commande avec cet ID dans la base
        $this->commande->client = "Client Modifié";
        $this->commande->quantite_commandee = 5;
        $this->assertTrue($this->commande->modifierCommande(), "La modification d'une commande valide devrait réussir.");
    }

    public function testSupprimerCommande() {
        $this->commande->id = 1; // Assurez-vous d'avoir une commande avec cet ID dans la base
        $this->assertTrue($this->commande->supprimerCommande(), "La suppression d'une commande devrait réussir.");
    }
}
