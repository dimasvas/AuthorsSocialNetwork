<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Service\AppLocale;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array('label' => 'common.name', 'attr' => ['novalidate' => 'novalidate']))
                ->add('surname',  TextType::class, array('label' => 'common.surname', 'attr' => ['novalidate' => 'novalidate']))
                ->add('middleName',  TextType::class, array('label' => 'common.middle_name', 'attr' => ['novalidate' => 'novalidate'], 'required' => false))
                ->add('gender', GenderFormType::class, array('placeholder' => 'common.choose_2', 'attr' => ['novalidate' => 'novalidate']))
                ->add('dateOfBirth', DateOfBirthFromType::class, array('attr' => ['novalidate' => 'novalidate']))
                ->add('hideYearOfBirth', HideYearOfBirthFormType::class)
                ->add('country', CountryFormType::class, array('placeholder' => 'common.choose_2', 'attr' => ['novalidate' => 'novalidate']))
                ->add('city', TextType::class, array('label' => 'common.city'))
                ->add('isAuthor', IsAuthorFormType::class, array('placeholder' => 'common.choose_2'))
                ->add('locale', HiddenType::class, array('data' => AppLocale::LANG_RUS));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }
    
    public function getName()
    {
        return $this->getBlockPrefix();
    }
    
    public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setDefaults(array(
        'attr'=>array('novalidate'=>'novalidate')
    ));
}
}