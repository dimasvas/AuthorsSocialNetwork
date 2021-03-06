<?php
namespace AppBundle\Model;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Months
 *
 * @author dimas
 */
class AppMonths {
    
    private static $months_ru = [
        '1' => 'Январь',
        '2' => 'Февраль',
        '3' => 'Март',
        '4' => 'Апрель',
        '5' => 'Май',
        '6' => 'Июнь',
        '7' => 'Июль',
        '8' => 'Август',
        '9' => 'Сентябрь',
        '10' => 'Октябрь',
        '11' => 'Ноябрь',
        '12' => 'Ноябрь'
    ];
    
    private static $months_ua = [
        
    ];
   
    
    public static function getMonths($locale = 'ru') {
        return array_flip($locale == 'ru' ? self::$months_ru : self::$months_ua);
    }
           
}
