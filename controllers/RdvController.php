<?php

require_once 'config/database.php';
require_once 'config/jwt.php';
require_once 'models/Rdv.php';

class RdvController {
    private $db;
    private $rdv;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->rdv = new Rdv($this->db);
    }

    public function getAll() {
        JWT::authenticate();
        $stmt = $this->rdv->getAll();
        $num = $stmt->rowCount();

        if($num > 0) {
            $rdvs_arr = array();
            $rdvs_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $rdv_item = array(
                    "id" => $id,
                    "patient_id" => $patient_id,
                    "patient_nom" => $patient_nom ?? null,
                    "patient_prenom" => $patient_prenom ?? null,
                    "medecin_id" => $medecin_id,
                    "medecin_nom" => $medecin_nom ?? null,
                    "medecin_prenom" => $medecin_prenom ?? null,
                    "specialite" => $specialite ?? null,
                    "date_rdv" => $date_rdv,
                    "motif" => $motif,
                    "statut" => $statut,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
                array_push($rdvs_arr["records"], $rdv_item);
            }

            http_response_code(200);
            echo json_encode($rdvs_arr);
        } else {
            http_response_code(200);
            echo json_encode(array("records" => array()));
        }
    }

    public function getOne($id) {
        JWT::authenticate();
        $this->rdv->id = $id;

        if($this->rdv->getOne()) {
            $rdv_arr = array(
                "id" => $this->rdv->id,
                "patient_id" => $this->rdv->patient_id,
                "medecin_id" => $this->rdv->medecin_id,
                "date_rdv" => $this->rdv->date_rdv,
                "motif" => $this->rdv->motif,
                "statut" => $this->rdv->statut,
                "created_at" => $this->rdv->created_at,
                "updated_at" => $this->rdv->updated_at
            );

            http_response_code(200);
            echo json_encode($rdv_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Rendez-vous non trouvé."));
        }
    }

    public function create() {
        JWT::authenticate();
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->patient_id) && !empty($data->medecin_id) && !empty($data->date_rdv)) {
            $this->rdv->patient_id = $data->patient_id;
            $this->rdv->medecin_id = $data->medecin_id;
            $this->rdv->date_rdv = $data->date_rdv;
            $this->rdv->motif = $data->motif ?? null;
            $this->rdv->statut = $data->statut ?? 'planifie';

            if($this->rdv->create()) {
                http_response_code(201);
                echo json_encode(array(
                    "message" => "Rendez-vous créé avec succès.",
                    "id" => $this->rdv->id
                ));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Impossible de créer le rendez-vous."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Données incomplètes. Patient ID, Médecin ID et date du rendez-vous sont requis."));
        }
    }

    public function update($id) {
        JWT::authenticate();
        $data = json_decode(file_get_contents("php://input"));

        $this->rdv->id = $id;

        if(!empty($data->patient_id) && !empty($data->medecin_id) && !empty($data->date_rdv)) {
            $this->rdv->patient_id = $data->patient_id;
            $this->rdv->medecin_id = $data->medecin_id;
            $this->rdv->date_rdv = $data->date_rdv;
            $this->rdv->motif = $data->motif ?? null;
            $this->rdv->statut = $data->statut ?? 'planifie';

            if($this->rdv->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Rendez-vous mis à jour avec succès."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Impossible de mettre à jour le rendez-vous."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Données incomplètes. Patient ID, Médecin ID et date du rendez-vous sont requis."));
        }
    }

    public function delete($id) {
        JWT::authenticate();
        $this->rdv->id = $id;

        if($this->rdv->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Rendez-vous supprimé avec succès."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Impossible de supprimer le rendez-vous."));
        }
    }
}
