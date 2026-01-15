<?php

class JWT {
    private static $secret_key = "votre_cle_secrete_jwt_2026"; // Changez cette clé en production
    private static $algorithm = 'HS256';
    private static $issuer = 'http://localhost:8000';

    /**
     * Génère un token JWT
     */
    public static function encode($payload) {
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => self::$algorithm
        ]);

        $payload['iss'] = self::$issuer;
        $payload['iat'] = time();
        $payload['exp'] = time() + (60 * 60 * 24); // 24 heures

        $payload_json = json_encode($payload);

        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload_json);

        $signature = hash_hmac(
            'sha256',
            $base64UrlHeader . "." . $base64UrlPayload,
            self::$secret_key,
            true
        );
        $base64UrlSignature = self::base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Décode et valide un token JWT
     */
    public static function decode($jwt) {
        if (empty($jwt)) {
            return null;
        }

        $tokenParts = explode('.', $jwt);
        if (count($tokenParts) !== 3) {
            return null;
        }

        list($header, $payload, $signature) = $tokenParts;

        $signatureProvided = $signature;
        $base64UrlHeader = $header;
        $base64UrlPayload = $payload;

        $signatureCheck = self::base64UrlEncode(hash_hmac(
            'sha256',
            $base64UrlHeader . "." . $base64UrlPayload,
            self::$secret_key,
            true
        ));

        if ($signatureCheck !== $signatureProvided) {
            return null;
        }

        $payload = json_decode(self::base64UrlDecode($payload), true);

        if (!isset($payload['exp']) || $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    /**
     * Récupère le token depuis les headers
     */
    public static function getBearerToken() {
        $headers = self::getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * Récupère le header Authorization
     */
    private static function getAuthorizationHeader() {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(
                array_map('ucwords', array_keys($requestHeaders)), 
                array_values($requestHeaders)
            );
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
     * Encode en base64 URL-safe
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Décode depuis base64 URL-safe
     */
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Vérifie si l'utilisateur est authentifié
     */
    public static function authenticate() {
        $token = self::getBearerToken();
        if (!$token) {
            http_response_code(401);
            echo json_encode(array("message" => "Token manquant."));
            exit();
        }

        $decoded = self::decode($token);
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(array("message" => "Token invalide ou expiré."));
            exit();
        }

        return $decoded;
    }
}
