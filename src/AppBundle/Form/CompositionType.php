<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Language;
//use Symfony\Component\Form\Extension\Core\Type\{TextType, TextareaType, CheckboxType, ChoiceType, FileType, SubmitType, HiddenType};
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CompositionType extends AbstractType
{
    /**
     * @var
     */
    private $category;
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->category = $options['composition_category'];
 
        $builder
            ->add('title', TextType::class, array('label' => 'common.title'))
            ->add('description',  TextareaType::class,  array('label' => 'common.description'))
            ->add('published', CheckboxType::class,  array('label' => 'common.published', 'required' => false))
            ->add('language', ChoiceType::class,  array(
                'label' => 'common.language',
                'choices'   => array_flip(Language::getCompositionLang()),
                'choices_as_values' => true,
            ))
             ->add('genres', EntityType::class, array(
                    'class' => 'AppBundle:Genre',
                    'label' => 'common.genre',
                    'expanded' => true,
                    'multiple' => true,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('g')
                                ->where('g.category = :category_id')
                                ->setParameter('category_id', $this->category->getId())
                                ->orderBy('g.name', 'ASC');
                    },
            ))->add('status', EntityType::class, array(
                    'class' => 'AppBundle:CompositionStatus',
                    'label' => 'common.status'
            ))->add('typeName', HiddenType::class, array('data' => ''))
              ->add('submit', SubmitType::class, array('label' => 'common.add_and_perceed', 'attr' => array(
                    'class' => 'btn btn-default btn-sm'
                )));                

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
     
            $event->getForm()->add('category_id', HiddenType::class,  array(
                'data' => $this->category->getId(),
                'mapped' => false
            ));
        });
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {        
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Composition',
            'composition_type' => '',
            'composition_category' =>  ''
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_composition';
    }
}
