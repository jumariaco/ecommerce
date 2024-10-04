<?php
session_start(); // Démarrer la session pour gérer l'authentification

include_once 'database.php';
include_once 'Produit.php';
include_once 'Commande.php';
include_once 'User.php';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Vérifier si la connexion a réussi
if ($db === null) {
    echo json_encode(["error" => "Impossible de se connecter à la base de données"]);
    exit;
}

// Initialiser les classes
$produit = new Produit($db);
$commande = new Commande($db);
$user = new User($db);

// Identifier l'action via une requête GET ou POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Vérifier si l'utilisateur est connecté
function estConnecte() {
    return isset($_SESSION['user_id']);
}

// Vérifier si l'utilisateur est admin
function estAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

// Gestion de l'authentification
if ($action == 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    if ($user->verifierUser($email, $password)) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['is_admin'] = $user->is_admin;
        echo json_encode(["message" => "Connexion réussie", "user" => ["id" => $user->id, "nom" => $user->nom, "email" => $user->email, "is_admin" => $user->is_admin]]);
    } else {
        echo json_encode(["error" => "Email ou mot de passe incorrect"]);
    }
    exit;
} elseif ($action == 'logout') {
    session_destroy();
    echo json_encode(["message" => "Déconnexion réussie"]);
    exit;
}

// Vérifier les permissions avant d'exécuter d'autres actions
if (!estConnecte()) {
    echo json_encode(["error" => "Vous devez être connecté pour effectuer cette action."]);
    exit;
}

// Gestion des actions pour les produits (accessible uniquement aux admins)
if (estAdmin()) {
    if ($action == 'ajouterProduit') {
        if (validerChampsProduit($_POST)) {
            $produit->nom = $_POST['nom'];
            $produit->description = $_POST['description'];
            $produit->prix = $_POST['prix'];
            $produit->stock = $_POST['stock'];
            if ($produit->ajouterProduit()) {
                echo json_encode(["message" => "Produit ajouté avec succès"]);
            } else {
                echo json_encode(["error" => "Erreur lors de l'ajout du produit"]);
            }
        } else {
            echo json_encode(["error" => "Données du produit non valides"]);
        }
    } elseif ($action == 'modifierProduit') {
        if (validerChampsProduit($_POST) && isset($_POST['id'])) {
            $produit->id = $_POST['id'];
            $produit->nom = $_POST['nom'];
            $produit->description = $_POST['description'];
            $produit->prix = $_POST['prix'];
            $produit->stock = $_POST['stock'];
            if ($produit->modifierProduit()) {
                echo json_encode(["message" => "Produit modifié avec succès"]);
            } else {
                echo json_encode(["error" => "Erreur lors de la modification du produit"]);
            }
        } else {
            echo json_encode(["error" => "Données du produit non valides ou ID manquant"]);
        }
    } elseif ($action == 'supprimerProduit') {
        if (isset($_POST['id'])) {
            $produit->id = $_POST['id'];
            if ($produit->supprimerProduit()) {
                echo json_encode(["message" => "Produit supprimé avec succès"]);
            } else {
                echo json_encode(["error" => "Erreur lors de la suppression du produit"]);
            }
        } else {
            echo json_encode(["error" => "ID du produit manquant"]);
        }
    } elseif ($action == 'listerProduits') {
        $stmt = $produit->listerProduits();
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($produits);
    }
} elseif ($action == 'listerProduits') { // Les utilisateurs normaux peuvent lister les produits
    $stmt = $produit->listerProduits();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($produits);
}

// Gestion des actions pour les commandes (tous les utilisateurs peuvent créer et supprimer leurs propres commandes)
if ($action == 'ajouterCommande') {
    if (validerChampsCommande($_POST)) {
        $commande->client = $_SESSION['user_email']; // Utiliser l'email de l'utilisateur connecté comme client
        $commande->is_admin = $_SESSION['is_admin'];
        $commande->date_commande = date('Y-m-d H:i:s');
        if ($commande->ajouterCommande()) {
            echo json_encode(["message" => "Commande ajoutée avec succès"]);
        } else {
            echo json_encode(["error" => "Erreur lors de l'ajout de la commande"]);
        }
    } else {
        echo json_encode(["error" => "Données de la commande non valides"]);
    }
} elseif ($action == 'supprimerCommande') {
    if (isset($_POST['id'])) {
        $commande->id = $_POST['id'];
        // Vérifier que la commande appartient à l'utilisateur connecté ou que l'utilisateur est admin
        $commande->voirCommande();
        if ($commande->client == $_SESSION['user_email'] || estAdmin()) {
            if ($commande->supprimerCommande()) {
                echo json_encode(["message" => "Commande supprimée avec succès"]);
            } else {
                echo json_encode(["error" => "Erreur lors de la suppression de la commande"]);
            }
        } else {
            echo json_encode(["error" => "Vous n'avez pas les permissions pour supprimer cette commande"]);
        }
    } else {
        echo json_encode(["error" => "ID de la commande manquant"]);
    }
} elseif ($action == 'listerCommandes') {
    // Les utilisateurs normaux peuvent voir uniquement leurs commandes, les admins voient toutes les commandes
    if (estAdmin()) {
        $stmt = $commande->listerCommandes();
    } else {
        $stmt = $db->prepare("SELECT * FROM commandes WHERE client = :client");
        $stmt->bindParam(':client', $_SESSION['user_email']);
        $stmt->execute();
    }
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($commandes);
}

// Fonctions de validation pour les champs des produits et commandes
function validerChampsProduit($data) {
    return isset($data['nom']) && isset($data['description']) && isset($data['prix']) && isset($data['stock']) && is_numeric($data['prix']) && is_numeric($data['stock']) && $data['stock'] >= 0 && $data['prix'] >= 0;
}

function validerChampsCommande($data) {
    // Vérifier que le champ client est bien défini et que l'état admin est bien un booléen (0 ou 1)
    return isset($data['client']) && isset($data['is_admin']) && ($data['is_admin'] == 0 || $data['is_admin'] == 1);
}
