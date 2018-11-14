<?php

namespace App\Twig;

use App\Entity\LikeNotification;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig_SimpleTest;

class AppExtension extends AbstractExtension implements GlobalsInterface {

    /**
     * @var string
     */
    private $locale;

    public function __construct(string $locale) {

        $this->locale = $locale;
    }

    /**
     * Definir que filtros definimos para esta extension
     */
    public function getFilters() {

        // devolvemos un Array con instancias de twigFilter
        return [
            // Al crear las instancias en el constructor pasamos dos parametros:
            // El primero es el nombre que usaremos en twig para usar la function
            // El segundo un array con la instancia del $this y el nombre de la funcion que hemos definido en esta class
            new TwigFilter("price", [$this, "priceFilter"])
        ];
    }

    public function priceFilter($number) {

        return "$" . number_format($number, 2, ".", ",");
    }

    public function getGlobals() {
        
        return [
            "locale" => $this->locale
        ];
    }
    
    public function getTests() {
        
        // Para crear funciones en twig definimos una instancia del Twig SimpleTest, en el constructor el primer parametro es el nombre que usaremos en twig
        // I la funcion anonima que hara lo que queremos que haga en twig, por ejemplo en este caso, queremos comprobar si cuando en twig hagamos:
        // if param is like, ese param sea una instancia del tipo LikeNotification.
        return [
            new Twig_SimpleTest('like', function($objetc){
                
                return ($objetc instanceof LikeNotification);
            })
        ];
    }

}
