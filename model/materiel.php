<?php

require_once __DIR__ . '/../config/database.php';

class Materiel {

    private PDO $pdo;

    public ?int $id = null;
    public string $nom = '';
    public string $type = 'PC';
    public string $num_serie = '';
    public ?string $date_achat= null;
    public $estArchive = false;
    private ?int $id_user = null;

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

    public function create(string $nom, string $type, string $num_serie, ?string $date_achat, bool $estArchive, ?int $id_user) {
        $this->date_achat = $date_achat ?? date('Y-m-d');
        $stmt = $this->pdo->prepare("INSERT INTO materiels (nom, type, num_serie, date_achat, archive, id_user) VALUES (:nom, :type, :num_serie, :date_achat, :archive, :id_user)");
        return $stmt->execute([
            'nom' => $nom,
            'type' => $type,
            'num_serie' => $num_serie,
            'date_achat' => $date_achat,
            'estArchive' => $estArchive,
            'id_user' => $id_user
        ]);
    }

     public function update(int $id, string $nom, string $type, string $num_serie, ?string $date_achat, bool $estArchive, ?int $id_user) {
        $stmt = $this->pdo->prepare("UPDATE materiels SET nom = :nom, type = :type, num_serie = :num_serie, date_achat = :date_achat, archive = :archive, id_user = :id_user WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'nom' => $nom,
            'type' => $type,
            'num_serie' => $num_serie,
            'date_achat' => $date_achat,
            'estArchive' => $estArchive,
            'id_user' => $id_user
        ]);
    }

    public function archive(int $id) {
        $stmt = $this->pdo->prepare("UPDATE materiels SET estArchive = 'true' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Getters

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM materiels");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM materiels WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getByType(string $type): array {
        $stmt = $this->pdo->prepare("SELECT * FROM materiels WHERE type = :type");
        $stmt->execute(['type' => $type]);
        return $stmt->fetchAll();
    }

    public function affectation (int $id_materiel, int $id_user) {
        $stmt = $this->pdo->prepare("UPDATE materiels SET id_user = :id_user WHERE id = :id_materiel");
        return $stmt->execute([
            'id_user' => $id_user,
            'id_materiel' => $id_materiel
        ]);
    }

    //Setters 

    private function setNom(string $nom) {
        $this->nom = $nom;
    }

    private function setType(string $type) {
        $this->type = $type;
    }

    private function setNumSerie(string $num_serie) {
        $this->num_serie = $num_serie;
        $allowed = ['PC', 'Ecran'];
    }

    private function setDateAchat(?string $date_achat) {
        $this->date_achat = $date_achat;
    }

    private function setEstArchive(bool $estArchive) {
        $this->estArchive = $estArchive;
    }

    private function setIdUser(?int $id_user) {
        $this->id_user = $id_user;
    }


}



?>
