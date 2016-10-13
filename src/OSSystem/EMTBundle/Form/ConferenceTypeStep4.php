<?php

namespace OSSystem\EMTBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use OSSystem\EMTBundle\Entity\Document;

class ConferenceTypeStep4 extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eventProgramme', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.eventProgramme',
                        'required' => false,
                        'disabled' => in_array('eventProgramme',$options['lockedFields']),
                        'attr' => array(
                            'placeholder' => 'conference.edit.eventProgramme_ph',)
                        )
                        
                )
                
            ->add('comments', 'textarea',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.comments',
                        'required' => false,
                        'disabled' => in_array('comments',$options['lockedFields']),
                        'attr' => array(
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
            'data_class' => 'OSSystem\EMTBundle\Entity\Conference',
            'lockedFields' => array()
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
