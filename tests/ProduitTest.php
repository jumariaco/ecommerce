<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Produit.php';


class ProduitTest extends TestCase {

    private $produit;

    protected function setUp(): void {
        // Créer une instance de connexion à la base de données
        $database = new Database();
        $db = $database->getConnection();

        // Initialiser l'objet Produit avec la connexion à la base de données
        $this->produit = new Produit($db);

        // Vérifiez que l'objet a bien été créé
        $this->assertNotNull($this->produit, "L'objet Produit n'a pas pu être initialisé.");
    }

    public function testAjouterProduitValide() {
        $this->produit->nom = "Produit Test";
        $this->produit->description = "Description Test";
        $this->produit->prix = 10.0;
        $this->produit->stock = 50;
        $this->assertTrue($this->produit->ajouterProduit(), "L'ajout d'un produit valide devrait réussir.");
    }

    public function testAjouterProduitNomVide() {
        $this->produit->nom = "";
        $this->produit->description = "Description Test";
        $this->produit->prix = 10.0;
        $this->produit->stock = 50;
        $this->assertFalse($this->produit->ajouterProduit(), "L'ajout d'un produit avec un nom vide devrait échouer.");
    }

    public function testAjouterProduitStockNegatif() {
        $this->produit->nom = "Produit Test";
        $this->produit->description = "Description Test";
        $this->produit->prix = 10.0;
        $this->produit->stock = -5; 
        $this->assertFalse($this->produit->ajouterProduit(), "L'ajout d'un produit avec un stock négatif devrait échouer.");
    }

    public function testAjouterProduitPrixNegatif() {
        $this->produit->nom = "Produit Test";
        $this->produit->description = "Description Test";
        $this->produit->prix = -10.0;
        $this->produit->stock = 50;
        $this->assertFalse($this->produit->ajouterProduit(), "L'ajout d'un produit avec un prix négatif devrait échouer.");
    }

    public function testModifierProduitValide() {
        $this->produit->id = 2; 
        $this->produit->nom = "Produit Modifié";
        $this->produit->description = "Description Modifiée";
        $this->produit->prix = 20.0;
        $this->produit->stock = 30;
        $this->assertTrue($this->produit->modifierProduit(), "La modification d'un produit valide devrait réussir.");
    }

    public function testSupprimerProduit() {
        $this->produit->id = 2; 
        $this->assertTrue($this->produit->supprimerProduit(), "La suppression d'un produit devrait réussir.");
    }

    public function testListerProduits() {
        $produits = $this->produit->listerProduits();
        $this->assertIsArray($produits, "Le résultat de listerProduits doit être un tableau.");
    }

    public function testVoirProduit() {
        $this->produit->id = 3; 
        $this->produit->voirProduit();
        $this->assertEquals("Produit Modifié", $this->produit->nom, "Le nom du produit doit correspondre après la lecture.");
    }
}
