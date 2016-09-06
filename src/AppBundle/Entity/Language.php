<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Entity;


class Language {
    
    private static $compositionLanguages = [
        'ru' => 'language.ru',
        'ua' => 'language.ua'
    ];
    
    public static function getCompositionLang(){
            return self::$compositionLanguages;
    }
    
    public static function getCompositionLangKeys(){
            return array_keys(self::$compositionLanguages);
    }
}
