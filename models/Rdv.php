<?php

class Rdv {
    private $conn;
    private $table_name = "rdv";

    public $id;
    public $patient_id;
    public $medecin_id;
    public $date_rdv;
    public $motif;
    public $statut;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT r.*, 
                         p.nom as patient_nom, p.prenom as patient_prenom,
                         m.nom as medecin_nom, m.prenom as medecin_prenom, m.specialite
                  FROM " . $this->table_name . " r
                  LEFT JOIN patient p ON r.patient_id = p.id
                  LEFT JOIN medecin m ON r.medecin_id = m.id
                  ORDER BY r.date_rdv DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getOne() {
        $query = "SELECT r.*, 
                         p.nom as patient_nom, p.prenom as patient_prenom,
                         m.nom as medecin_nom, m.prenom as medecin_prenom, m.specialite
                  FROM " . $this->table_name . " r
                  LEFT JOIN patient p ON r.patient_id = p.id
                  LEFT JOIN medecin m ON r.medecin_id = m.id
                  WHERE r.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->patient_id = $row['patient_id'];
            $this->medecin_id = $row['medecin_id'];
            $this->date_rdv = $row['date_rdv'];
            $this->motif = $row['motif'];
            $this->statut = $row['statut'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET patient_id=:patient_id, medecin_id=:medecin_id, 
                      date_rdv=:date_rdv, motif=:motif, statut=:statut";
        
        $stmt = $this->conn->prepare($query);
        
        $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
        $this->medecin_id = htmlspecialchars(strip_tags($this->medecin_id));
        $this->date_rdv = htmlspecialchars(strip_tags($this->date_rdv));
        $this->motif = htmlspecialchars(strip_tags($this->motif));
        $this->statut = htmlspecialchars(strip_tags($this->statut));
        
        $stmt->bindParam(":patient_id", $this->patient_id);
        $stmt->bindParam(":medecin_id", $this->medecin_id);
        $stmt->bindParam(":date_rdv", $this->date_rdv);
        $stmt->bindParam(":motif", $this->motif);
        $stmt->bindParam(":statut", $this->statut);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET patient_id=:patient_id, medecin_id=:medecin_id, 
                      date_rdv=:date_rdv, motif=:motif, statut=:statut
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
        $this->medecin_id = htmlspecialchars(strip_tags($this->medecin_id));
        $this->date_rdv = htmlspecialchars(strip_tags($this->date_rdv));
        $this->motif = htmlspecialchars(strip_tags($this->motif));
        $this->statut = htmlspecialchars(strip_tags($this->statut));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(":patient_id", $this->patient_id);
        $stmt->bindParam(":medecin_id", $this->medecin_id);
        $stmt->bindParam(":date_rdv", $this->date_rdv);
        $stmt->bindParam(":motif", $this->motif);
        $stmt->bindParam(":statut", $this->statut);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
