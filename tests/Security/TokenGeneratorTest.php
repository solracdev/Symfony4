<?php

namespace App\Test\Security;

use App\Security\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase {
   
    public function testTokenGenerator(){
        
        $tokenGen = new TokenGenerator();
        
        $token = $tokenGen->getSecureToken(30);
        
        // Llamar a los assertion del phpUnit para probar los metodos
        // La instancia $this tiene multitud de assert pero casi siempre se usa el assertEquals
        
        // Comprobar que el token generado tiene el length 30
        $this->assertEquals(30 , strlen($token));
        
        // Comprobar que el token solo sea numeros y letras
        //$token[10] = "+"; // probar a cambiar la letra de la posicion 15 por un signo, asi fallara
        $this->assertTrue(ctype_alnum($token), "El Token generado no es alpha-numerico");
    }
}
