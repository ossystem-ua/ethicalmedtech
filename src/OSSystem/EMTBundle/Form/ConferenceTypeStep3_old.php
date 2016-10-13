<?php

namespace OSSystem\EMTBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConferenceTypeStep3 extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('assesmentNameVenue', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'disabled' => in_array('assesmentNameVenue',$options['lockedFields']),
                        'label' => 'conference.edit.assesmentNameVenue',
                        'attr' => array('placeholder' => 'conference.edit.assesmentNameVenue_ph',
                            'maxlength' => 250,
                            )
                        ))
            ->add('assesmentCategoryVenue', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'disabled' => in_array('assesmentCategoryVenue',$options['lockedFields']),
                        'label' => 'conference.edit.assesmentCategoryVenue',
                        'attr' => array('placeholder' => 'conference.edit.assesmentCategoryVenue_ph',
                            'maxlength' => 150,
                            )
                        ))
            ->add('assesmentCity', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'disabled' => in_array('assesmentCity',$options['lockedFields']),
                        'label' => 'conference.edit.assesmentCity',
                        'attr' => array('placeholder' => 'conference.edit.assesmentCity_ph',
                            'maxlength' => 150,
                            )
                        ))
            ->add('assesmentCountry', 'entity',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'disabled' => in_array('assesmentCountry',$options['lockedFields']),
                        'label' => 'conference.edit.assesmentCountry',
                        'class' => 'OSSystem\EMTBundle\Entity\Country',
                        'multiple' => false,
                        'attr' => array(
                            )
                        ))
            ->add('assesmentLocalNA', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'disabled' => in_array('assesmentLocalNA',$options['lockedFields']),
                        'label' => 'conference.edit.assesmentLocalNA',
                        'attr' => array('placeholder' => 'conference.edit.assesmentLocalNA_ph',
                            'maxlength' => 150,
                            )
                        ))
            ->add('assesmentProposedAccomodation', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'disabled' => in_array('assesmentProposedAccomodation',$options['lockedFields']),
                        'label' => 'conference.edit.assesmentProposedAccomodation',
                        'attr' => array('placeholder' => 'conference.edit.assesmentProposedAccomodation_ph',
                            'maxlength' => 150,
                            )
                        ))    
                
            ->add('assesmentProposedAccomodationDocumentFile', 'file',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'disabled' => in_array('assesmentProposedAccomodationDocumentFile',$options['lockedFields']),
                        'label' => 'conference.edit.assesmentProposedAccomodationDocument',
                        'attr' => array('placeholder' => 'conference.edit.assesmentProposedAccomodationDocument_ph',
                            'maxlength' => 150,
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
