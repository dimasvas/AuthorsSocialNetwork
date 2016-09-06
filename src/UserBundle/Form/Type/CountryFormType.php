<?php
/**
 * Created by PhpStorm.
 * User: dimas
 * Date: 30.07.15
 * Time: 17:36
 */

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class CountryFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'label' => 'common.choose_country')
        );
    }

    public function getParent()
    {
        return CountryType::class;
    }

    public function getName()
    {
        return 'country_field';
    }
}