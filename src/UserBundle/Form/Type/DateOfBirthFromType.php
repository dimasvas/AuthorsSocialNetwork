<?php
namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DateOfBirthFromType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'label' => 'common.date_birth',
                'format' => 'dd - MMMM - yyyy',
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y')-70))
        );
    }

    public function getParent()
    {
        return DateType::class;
    }

    public function getName()
    {
        return 'date_of_birth';
    }
}