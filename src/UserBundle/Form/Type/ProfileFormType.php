<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', array('disabled' => true))
            ->add('name', 'text', array('disabled' => true, 'label' => 'common.name'))
            ->add('surname', 'text', array('disabled' => true, 'label' => 'common.surname'))
            ->add('middleName', 'text', array('disabled' => true, 'label' => 'common.middle_name', 'required' => false))
            ->add('aliasName', 'text', array('label' => 'common.alias_name', 'required' => false))
            ->add('showAliasName', 'checkbox',  array('label' => 'common.show_alias_name', 'required' => false))
            ->add('dateOfBirth', 'birthday', array('disabled' => true))
            ->add('hideYearOfBirth', new HideYearOfBirthFormType())
            ->add('country', new CountryFormType())
            ->add('city', 'text', array('label' => 'common.city'))
            ->add('isAuthor', 'text', array('disabled' => true))
            ->add('locale', 'choice', array(
                'label' => 'common.locale',
                'choices'   => array(
                    'ru' => 'form.choice.locale.ru',
                    'ua' => 'form.choice.locale.ua',
                    'en' => 'form.choice.locale.en')
            ))
            ->add('aboutMe', 'textarea', array('label' => 'common.about_me', 'required' => false))
            ->add('imageFile', 'file', array('label' => 'common.profile_img', 'required' => false, 'data_class' => null));
    }

    public function getParent()
    {
        return 'fos_user_profile';
    }

    public function getName()
    {
        return 'app_user_profile';
    }
}