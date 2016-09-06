<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\Composition;

/**
 * Description of CompositionStatusType
 *
 * @author dimas
 */
class CompositionStatusType extends AbstractType 
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('language', ChoiceType::class, array(
                    'label' => 'common.composition_status',
                    'required' => false,
                    'placeholder' => 'common.select',
                    'choices' => array_flip([
                        Composition::STATUS_FINISHED => 'composition_status.' . Composition::STATUS_FINISHED,
                        Composition::STATUS_INPROCESS => 'composition_status.' . Composition::STATUS_INPROCESS,
                        Composition::STATUS_FREEZED => 'composition_status.' . Composition::STATUS_FREEZED,
                    ])
                )
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
    }
}
