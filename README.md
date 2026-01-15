# API Médicale PHP Native avec Swagger

Projet PHP natif pour gérer les patients, médecins et rendez-vous avec documentation Swagger.

## Installation

1. **Configurer la base de données**

   ```bash
   mysql -u root -p < database.sql
   ```

2. **Configurer les paramètres de connexion**

   - Modifier `config/database.php` avec vos identifiants MySQL

3. **Démarrer le serveur PHP**

   ```bash
   php -S localhost:8000
   ```

4. **Accéder à Swagger UI**
   - Ouvrir: http://localhost:8000/swagger

## Endpoints API

### Patients

- `GET /api/patient` - Liste tous les patients
- `GET /api/patient/{id}` - Récupère un patient
- `POST /api/patient` - Crée un patient
- `PUT /api/patient/{id}` - Met à jour un patient
- `DELETE /api/patient/{id}` - Supprime un patient

### Médecins

- `GET /api/medecin` - Liste tous les médecins
- `GET /api/medecin/{id}` - Récupère un médecin
- `POST /api/medecin` - Crée un médecin
- `PUT /api/medecin/{id}` - Met à jour un médecin
- `DELETE /api/medecin/{id}` - Supprime un médecin

### Rendez-vous

- `GET /api/rdv` - Liste tous les rendez-vous
- `GET /api/rdv/{id}` - Récupère un rendez-vous
- `POST /api/rdv` - Crée un rendez-vous
- `PUT /api/rdv/{id}` - Met à jour un rendez-vous
- `DELETE /api/rdv/{id}` - Supprime un rendez-vous

## Structure du projet

```
php-openapi/
├── config/
│   ├── database.php
│   └── cors.php
├── models/
│   ├── Patient.php
│   ├── Medecin.php
│   └── Rdv.php
├── controllers/
│   ├── PatientController.php
│   ├── MedecinController.php
│   └── RdvController.php
├── swagger/
│   └── openapi.json
├── index.php
├── swagger.php
└── database.sql
```
