<?php

namespace OSSystem\EMTBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConferenceTypeStep2 extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delegatesMoreThan1Country', 'choice',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'disabled' => in_array('delegatesMoreThan1Country',$options['lockedFields']),
                        'label' => 'conference.edit.delegatesMoreThan1Country',
                        'choices' => array(1 => 'Yes', 0 => 'No', 2 => 'No information available at this time'),
                        )
                )
            ->add('delegatesCountries', 'entity',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'disabled' => in_array('delegatesCountries',$options['lockedFields']),
                        'label' => 'conference.edit.delegatesCountries',
                        'class' => 'OSSystem\EMTBundle\Entity\Country',
                        'multiple' => true,
                        'attr' => array(
                            )
                        ))
            
            ->add('delegatesAnticipate', 'choice',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.delegatesAnticipatename',
                        'required' => false,
                        'disabled' => in_array('delegatesMoreThan1Country',$options['lockedFields']),
                        'choices' => array(0 => "< 50 participants", 1 => '51 - 200', 2 => '201 - 500', 3 => '501 - 1000', 4 => '≥ 1000 participants'),
                        )
                )
            
            
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
