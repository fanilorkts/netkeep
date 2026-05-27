<?php

require_once __DIR__ . '/../config/database.php';

class Ticket {

    private PDO $pdo;

    public ?int $id = null;
    public string $titre = '';
    public string $description = '';
    public string $urgence = 'Faible';
    public string $status = 'Ouvert';
    public ?bool $resolution = null;
    public ?string $date_creation = null;
    public int $id_materiel = 0;
    public int $id_auteur = 0;


    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    public function hydrate(array $data) {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM tickets");
        return $stmt->fetchAll();
    }

    public function findByAuteur(int $id_auteur): array {
        $stmt = $this->pdo->prepare("SELECT * FROM tickets WHERE id_auteur = :id_auteur");
        $stmt->execute(['id_auteur' => $id_auteur]);
        return $stmt->fetchAll();
    }

     public function findByStatus(string $status): array {
        $stmt = $this->pdo->prepare("SELECT * FROM tickets WHERE status = :status");
        $stmt->execute(['status' => $status]);
        return $stmt->fetchAll();
    }

     public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM tickets WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(string $titre, string $description, string $urgence, string $status, ?string $resolution, ?string $date_creation, int $id_materiel, int $id_auteur) {
        $this->date_creation = $date_creation ?? date('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare("INSERT INTO tickets (titre, description, urgence, status, resolution, date_creation, id_materiel, id_auteur) VALUES (:titre, :description, :urgence, :status, :resolution, :date_creation, :id_materiel, :id_auteur)");
        return $stmt->execute([
            'titre' => $titre,
            'description' => $description,
            'urgence' => $urgence,
            'status' => $status,
            'resolution' => $resolution,
            'date_creation' => $date_creation,
            'id_materiel' => $id_materiel,
            'id_auteur' => $id_auteur
        ]);
    }

    public function update(int $id, string $titre, string $description, string $urgence, string $status, ?string $resolution, ?string $date_creation, int $id_materiel, int $id_auteur) {
        $stmt = $this->pdo->prepare("UPDATE tickets SET titre = :titre, description = :description, urgence = :urgence, status = :status, resolution = :resolution, date_creation = :date_creation, id_materiel = :id_materiel, id_auteur = :id_auteur WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'titre' => $titre,
            'description' => $description,
            'urgence' => $urgence,
            'status' => $status,
            'resolution' => $resolution,
            'date_creation' => $date_creation,
            'id_materiel' => $id_materiel,
            'id_auteur' => $id_auteur
        ]);
    }

    public function resolution(int $id) {
        $stmt = $this->pdo->prepare("UPDATE tickets SET status = 'true' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    //Getters

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM tickets");
        return $stmt->fetchAll();
    }


    //Setters

    private function setTitre(string $titre) {
        $this->titre = $titre;
    }

    private function setDescription(string $description) {
        $this->description = $description;
    }

    private function setUrgence(string $urgence) {
        $this->urgence = $urgence;
        $allowedValues = ['Faible', 'Moyenne', 'Elevée'];
    }

    private function setStatus(string $status) {
        $this->status = $status;
        $allowedValues = ['Ouvert', 'En cours', 'Résolu', 'Fermé'];
    }

    private function setResolution(?string $resolution) {
        $this->resolution = $resolution;
    }

    private function setIdMateriel(int $id_materiel) {
        $this->id_materiel = $id_materiel;
    }

}

?>