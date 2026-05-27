<?php
class Database {
    private string $host   = 'localhost';
    private string $dbname = 'netkeep';
    private string $user   = 'root';
    private string $pass   = 'root';         // Vide sur XAMPP
    private ?PDO   $pdo    = null;       // null = pas encore connecté
 
    // Pattern Singleton : une seule connexion pour toute la requête
    public function getConnection(): PDO {
        if ($this->pdo === null) {
            $dsn = "mysql:host={$this->host}"
                 . ";dbname={$this->dbname}"
                 . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
            } catch (PDOException $e) {
                // NE PAS afficher $e->getMessage() en prod → révèle le mot de passe !
                die('Erreur de connexion à la base de données.');
            }
        }
        return $this->pdo;
    }
}
