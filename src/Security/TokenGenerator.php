<?php

namespace App\Security;

class TokenGenerator {

    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    public function getSecureToken(int $length): string {

        // Logintud de la constante ALPHABET
        $maxNumber = strlen(self::ALPHABET);

        // Variable donde se almacenara el token
        $token = "";

        // Generar un token con al longitud pasado por parametro
        for ($i = 0; $i < $length; $i++) {

            // Ir concatenando un
            $token .= self::ALPHABET[random_int(0, $maxNumber - 1)];
        }
        
        // Devolver el token generado
        return $token;
    }

}
