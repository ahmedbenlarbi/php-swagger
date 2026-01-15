<?php

require_once 'config/database.php';
require_once 'config/jwt.php';
require_once 'models/Medecin.php';

class MedecinController {
    private $db;
    private $medecin;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->medecin = new Medecin($this->db);
    }

    public function getAll() {
        JWT::authenticate();
        $stmt = $this->medecin->getAll();
        $num = $stmt->rowCount();

        if($num > 0) {
            $medecins_arr = array();
            $medecins_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $medecin_item = array(
                    "id" => $id,
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "specialite" => $specialite,
                    "telephone" => $telephone,
                    "email" => $email,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
                array_push($medecins_arr["records"], $medecin_item);
            }

            http_response_code(200);
            echo json_encode($medecins_arr);
        } else {
            http_response_code(200);
            echo json_encode(array("records" => array()));
        }
    }

    public function getOne($id) {
        JWT::authenticate();
        $this->medecin->id = $id;

        if($this->medecin->getOne()) {
            $medecin_arr = array(
                "id" => $this->medecin->id,
                "nom" => $this->medecin->nom,
                "prenom" => $this->medecin->prenom,
                "specialite" => $this->medecin->specialite,
                "telephone" => $this->medecin->telephone,
                "email" => $this->medecin->email,
                "created_at" => $this->medecin->created_at,
                "updated_at" => $this->medecin->updated_at
            );

            http_response_code(200);
            echo json_encode($medecin_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Médecin non trouvé."));
        }
    }

    public function create() {
        JWT::authenticate();
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->nom) && !empty($data->prenom) && !empty($data->specialite)) {
            $this->medecin->nom = $data->nom;
            $this->medecin->prenom = $data->prenom;
            $this->medecin->specialite = $data->specialite;
            $this->medecin->telephone = $data->telephone ?? null;
            $this->medecin->email = $data->email ?? null;

            if($this->medecin->create()) {
                http_response_code(201);
                echo json_encode(array(
                    "message" => "Médecin créé avec succès.",
                    "id" => $this->medecin->id
                ));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Impossible de créer le médecin."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Données incomplètes. Nom, prénom et spécialité sont requis."));
        }
    }

    public function update($id) {
        JWT::authenticate();
        $data = json_decode(file_get_contents("php://input"));

        $this->medecin->id = $id;

        if(!empty($data->nom) && !empty($data->prenom) && !empty($data->specialite)) {
            $this->medecin->nom = $data->nom;
            $this->medecin->prenom = $data->prenom;
            $this->medecin->specialite = $data->specialite;
            $this->medecin->telephone = $data->telephone ?? null;
            $this->medecin->email = $data->email ?? null;

            if($this->medecin->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Médecin mis à jour avec succès."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Impossible de mettre à jour le médecin."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Données incomplètes. Nom, prénom et spécialité sont requis."));
        }
    }

    public function delete($id) {
        JWT::authenticate();
        $this->medecin->id = $id;

        if($this->medecin->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Médecin supprimé avec succès."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Impossible de supprimer le médecin."));
        }
    }
}
// CRUD FUNCTION