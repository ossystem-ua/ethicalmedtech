<?php

namespace OSSystem\EMTBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class conferenceTerms extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('assesmentCountry', 'entity',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'class' => 'OSSystem\EMTBundle\Entity\Country',
                        'multiple' => false,
                        'attr' => array(
                    )
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

