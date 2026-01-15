<?php

require_once 'config/database.php';
require_once 'config/jwt.php';

class AuthController {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Login - Authentifie un utilisateur et retourne un token JWT
     */
    public function login() {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->username) || empty($data->password)) {
            http_response_code(400);
            echo json_encode(array("message" => "Username et password sont requis."));
            return;
        }

        $query = "SELECT id, username, email, password, role FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $data->username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($data->password, $row['password'])) {
                $payload = array(
                    "id" => $row['id'],
                    "username" => $row['username'],
                    "email" => $row['email'],
                    "role" => $row['role']
                );

                $jwt = JWT::encode($payload);

                http_response_code(200);
                echo json_encode(array(
                    "message" => "Connexion réussie.",
                    "token" => $jwt,
                    "user" => array(
                        "id" => $row['id'],
                        "username" => $row['username'],
                        "email" => $row['email'],
                        "role" => $row['role']
                    )
                ));
            } else {
                http_response_code(401);
                echo json_encode(array("message" => "Identifiants invalides."));
            }
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Identifiants invalides."));
        }
    }

    /**
     * Register - Enregistre un nouvel utilisateur
     */
    public function register() {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->username) || empty($data->email) || empty($data->password)) {
            http_response_code(400);
            echo json_encode(array("message" => "Username, email et password sont requis."));
            return;
        }

        // Vérifier si l'utilisateur existe déjà
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username OR email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $data->username);
        $stmt->bindParam(":email", $data->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            http_response_code(409);
            echo json_encode(array("message" => "Un utilisateur avec ce username ou email existe déjà."));
            return;
        }

        // Créer le nouvel utilisateur
        $query = "INSERT INTO " . $this->table_name . " (username, email, password, role) VALUES (:username, :email, :password, :role)";
        $stmt = $this->conn->prepare($query);

        $hashed_password = password_hash($data->password, PASSWORD_BCRYPT);
        $role = isset($data->role) ? $data->role : 'user';

        $stmt->bindParam(":username", $data->username);
        $stmt->bindParam(":email", $data->email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":role", $role);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(array("message" => "Utilisateur créé avec succès."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Impossible de créer l'utilisateur."));
        }
    }

    /**
     * Vérifie le token et retourne les informations de l'utilisateur
     */
    public function verify() {
        header('Content-Type: application/json');
        
        $decoded = JWT::authenticate();
        
        http_response_code(200);
        echo json_encode(array(
            "message" => "Token valide.",
            "user" => $decoded
        ));
    }
}
