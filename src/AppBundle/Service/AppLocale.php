<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Service;

/**
 * Description of Locale
 *
 * @author dimas
 */
class AppLocale {
    
    const LANG_RUS = 'ru';
    const LANG_UA = 'ua';  
    
    public function isLocaleValid($locale)
    {
        switch ($locale) {
            case self::LANG_RUS :
                $isValid = true;
                break;
            case self::LANG_UA :
                $isValid = true;
                break;
            default:
                $isValid = false;
                break;
        }
        
        return $isValid;
    }
}
