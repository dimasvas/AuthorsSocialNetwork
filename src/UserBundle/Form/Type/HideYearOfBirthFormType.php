<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class HideYearOfBirthFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'label'    => 'common.hide_year_of_birth',
            'required' => false)
        );
    }

    public function getParent()
    {
        return CheckboxType::class;
    }

    public function getName()
    {
        return 'hide_year';
    }
}