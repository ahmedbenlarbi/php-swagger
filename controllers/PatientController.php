<?php

require_once 'config/database.php';
require_once 'config/jwt.php';
require_once 'models/Patient.php';

class PatientController {
    private $db;
    private $patient;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->patient = new Patient($this->db);
    }

    public function getAll() {
        JWT::authenticate();
        $stmt = $this->patient->getAll();
        $num = $stmt->rowCount();

        if($num > 0) {
            $patients_arr = array();
            $patients_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $patient_item = array(
                    "id" => $id,
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "date_naissance" => $date_naissance,
                    "telephone" => $telephone,
                    "email" => $email,
                    "adresse" => $adresse,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
                array_push($patients_arr["records"], $patient_item);
            }

            http_response_code(200);
            echo json_encode($patients_arr);
        } else {
            http_response_code(200);
            echo json_encode(array("records" => array()));
        }
    }

    public function getOne($id) {
        JWT::authenticate();
        $this->patient->id = $id;

        if($this->patient->getOne()) {
            $patient_arr = array(
                "id" => $this->patient->id,
                "nom" => $this->patient->nom,
                "prenom" => $this->patient->prenom,
                "date_naissance" => $this->patient->date_naissance,
                "telephone" => $this->patient->telephone,
                "email" => $this->patient->email,
                "adresse" => $this->patient->adresse,
                "created_at" => $this->patient->created_at,
                "updated_at" => $this->patient->updated_at
            );

            http_response_code(200);
            echo json_encode($patient_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Patient non trouvé."));
        }
    }

    public function create() {
        JWT::authenticate();
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->nom) && !empty($data->prenom) && !empty($data->date_naissance)) {
            $this->patient->nom = $data->nom;
            $this->patient->prenom = $data->prenom;
            $this->patient->date_naissance = $data->date_naissance;
            $this->patient->telephone = $data->telephone ?? null;
            $this->patient->email = $data->email ?? null;
            $this->patient->adresse = $data->adresse ?? null;

            if($this->patient->create()) {
                http_response_code(201);
                echo json_encode(array(
                    "message" => "Patient créé avec succès.",
                    "id" => $this->patient->id
                ));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Impossible de créer le patient."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Données incomplètes. Nom, prénom et date de naissance sont requis."));
        }
    }

    public function update($id) {
        JWT::authenticate();
        $data = json_decode(file_get_contents("php://input"));

        $this->patient->id = $id;

        if(!empty($data->nom) && !empty($data->prenom) && !empty($data->date_naissance)) {
            $this->patient->nom = $data->nom;
            $this->patient->prenom = $data->prenom;
            $this->patient->date_naissance = $data->date_naissance;
            $this->patient->telephone = $data->telephone ?? null;
            $this->patient->email = $data->email ?? null;
            $this->patient->adresse = $data->adresse ?? null;

            if($this->patient->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Patient mis à jour avec succès."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Impossible de mettre à jour le patient."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Données incomplètes. Nom, prénom et date de naissance sont requis."));
        }
    }

    public function delete($id) {
        JWT::authenticate();
        $this->patient->id = $id;

        if($this->patient->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Patient supprimé avec succès."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Impossible de supprimer le patient."));
        }
    }
}
