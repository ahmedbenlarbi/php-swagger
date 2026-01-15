<?php

require_once 'config/cors.php';

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

$base_path = '/api';
$uri_parts = explode('?', $request_uri);
$path = $uri_parts[0];

if (strpos($path, $base_path) === 0) {
    $path = substr($path, strlen($base_path));
}

$path_segments = explode('/', trim($path, '/'));
$resource = $path_segments[0] ?? '';
$id = $path_segments[1] ?? null;

switch($resource) {
    case 'auth':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        
        $action = $path_segments[1] ?? '';
        
        switch($action) {
            case 'login':
                if($request_method === 'POST') {
                    $controller->login();
                } else {
                    http_response_code(405);
                    echo json_encode(array("message" => "Méthode non autorisée."));
                }
                break;
            case 'register':
                if($request_method === 'POST') {
                    $controller->register();
                } else {
                    http_response_code(405);
                    echo json_encode(array("message" => "Méthode non autorisée."));
                }
                break;
            case 'verify':
                if($request_method === 'GET') {
                    $controller->verify();
                } else {
                    http_response_code(405);
                    echo json_encode(array("message" => "Méthode non autorisée."));
                }
                break;
            default:
                http_response_code(404);
                echo json_encode(array("message" => "Endpoint d'authentification non trouvé."));
                break;
        }
        break;

    case 'patient':
        require_once 'controllers/PatientController.php';
        $controller = new PatientController();
        
        switch($request_method) {
            case 'GET':
                if($id) {
                    $controller->getOne($id);
                } else {
                    $controller->getAll();
                }
                break;
            case 'POST':
                $controller->create();
                break;
            case 'PUT':
                if($id) {
                    $controller->update($id);
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "ID requis pour la mise à jour."));
                }
                break;
            case 'DELETE':
                if($id) {
                    $controller->delete($id);
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "ID requis pour la suppression."));
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(array("message" => "Méthode non autorisée."));
                break;
        }
        break;

    case 'medecin':
        require_once 'controllers/MedecinController.php';
        $controller = new MedecinController();
        
        switch($request_method) {
            case 'GET':
                if($id) {
                    $controller->getOne($id);
                } else {
                    $controller->getAll();
                }
                break;
            case 'POST':
                $controller->create();
                break;
            case 'PUT':
                if($id) {
                    $controller->update($id);
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "ID requis pour la mise à jour."));
                }
                break;
            case 'DELETE':
                if($id) {
                    $controller->delete($id);
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "ID requis pour la suppression."));
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(array("message" => "Méthode non autorisée."));
                break;
        }
        break;

    case 'rdv':
        require_once 'controllers/RdvController.php';
        $controller = new RdvController();
        
        switch($request_method) {
            case 'GET':
                if($id) {
                    $controller->getOne($id);
                } else {
                    $controller->getAll();
                }
                break;
            case 'POST':
                $controller->create();
                break;
            case 'PUT':
                if($id) {
                    $controller->update($id);
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "ID requis pour la mise à jour."));
                }
                break;
            case 'DELETE':
                if($id) {
                    $controller->delete($id);
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "ID requis pour la suppression."));
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(array("message" => "Méthode non autorisée."));
                break;
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(array("message" => "Endpoint non trouvé."));
        break;
}
