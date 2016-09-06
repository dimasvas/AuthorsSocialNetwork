<?php

namespace AppBundle\Form\CompositionTypes;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LyricType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', CKEditorType::class, array(
                'input_sync' => true,
                'label' => false,
                'config' => array(
                    'language' => 'ru',
                    'FormatOutput' => false,
                    'EnterMode' => 'p',
                    'ShiftEnterMode' => 'br',
                    'lineBreakChars' => '<br>',
                    'allowedContent' => 'u em strong ul li',
                    'toolbar' => array(
                        array(
                            'name'  => 'document',
                            'items' => array('Source', '-', 'Save', 'DocProps', 'Preview', 'Print', '-', 'Templates'),
                        ),
                        array(
                            'name'  => 'clipboard',
                            'items' => array('Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo'),
                        ),
                        array(
                            'name'  => 'editing',
                            'items' => array('Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt'),
                        ),
                        '/',
                        array(
                            'name'  => 'basicstyles',
                            'items' => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'),
                        ),
                        array(
                            'name' => 'paragraph',
                            'items' => array('NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-')
                        ),
                        array(
                            'name' => 'insert',
                            'items' => array('Image','Table','HorizontalRule','SpecialChar','PageBreak' )
                        ),
                        array(
                            'name' => 'styles',
                            'items' => array('Styles','Format','Font','FontSize')
                        ),
                        array(
                            'name' => '',
                            'items' => array('TextColor','BGColor')
                        ),
                        array(
                            'name' => 'tools',
                            'items' => array('Maximize', 'ShowBlocks')
                        ),
                    ),
                    'uiColor' => '#ffffff'
            ),
        ));
        
        $builder->add('submit', SubmitType::class, array('label' => 'common.save'))
                ->setMethod('POST');
       
        /*There is a bug in SKEDITOR with /r/n. So i use regex to remove them */
        $builder->add('submit', SubmitType::class, array('label' => 'common.save'))
                 ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                    $data = $event->getData();
                    $form = $event->getForm();
                     
                    $sanitizedString = str_replace(array("\n\r", "\n", "\r"), '', $data);

                    $event->setData($sanitizedString);         
                });
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CompositionTypes\Lyric',
            'allow_extra_fields' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_lyric';
    }
}
