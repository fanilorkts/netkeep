<?php

require_once __DIR__ . '/../config/database.php';

class User {

    private PDO $pdo;

    public int $id = 0;
    public string $nom = '';
    public string $prenom = '';
    public string $password = '';
    public string $email = '';
    public string $role = 'user';
  

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


    public function create(string $nom, string $prenom, string $email, string $password) {
        $stmt = $this->pdo->prepare("INSERT INTO users (nom, prenom, email, password) VALUES (:nom, :prenom, :email, :password)");
         $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        return $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => $hashed_password
        ]);
    }

    public function update(int $id, string $nom, string $prenom, string $email, string $password) {
        $stmt = $this->pdo->prepare("UPDATE users SET nom = :nom, prenom = :prenom, email = :email, password = :password WHERE id = :id");
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        return $stmt->execute([
            'id' => $id,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => $hashed_password
        ]);
    }

    public function delete(int $id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * @return array|false
     */

    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public function getFullName(): string {
        return $this->prenom . ' ' . $this->nom;
    }

    public function findByEmail(string $email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }


    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getPrenom(): string {
        return $this->prenom;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getRole(): string {
        return $this->role;
    }

        // Getters/Setters

   private function setRole(string $role) {
        $allowed = ['user', 'admin'];
        if (!in_array($role, $allowed)) {
            throw new InvalidArgumentException("Role must be 'user' or 'admin'");
        }
        $this->role = $role;
    }

    private function setpassword(string $password) {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }


}
?>
