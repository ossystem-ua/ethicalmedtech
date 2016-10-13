<?php

namespace OSSystem\EMTBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConferenceStatusChange extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('conferenceStatus', 'entity',
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.status',
                        'required' => true,
                        'class' => 'OSSystem\EMTBundle\Entity\Status',
                        'attr' => array(
                            )
                        ))
            ->add('conferenceStatusText', 'textarea',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.statusText',
                        'attr' => array(
                            'maxlength' => 255,
                            'class' => 'tinymce',
                
                            )
                        ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OSSystem\EMTBundle\Entity\Conference'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ossystem_emtbundle_conference';
    }
}
