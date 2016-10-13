<?php

namespace OSSystem\EMTBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConferenceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
     
        $builder
            ->add('title', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.name',
                        'disabled' => in_array('title',$options['lockedFields']),
                        'attr' => array('placeholder' => 'conference.edit.name_ph',
                            'maxlength' => 150)
                        ))
            ->add('acronym', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.acronym',
                        'disabled' => in_array('acronym',$options['lockedFields']),
                        'attr' => array(
                            'maxlength' => 100)
                        ))
                
            ->add('therapeuticArea', 'entity',
                    array('translation_domain' => 'OSSystemEMTBundle', 
                        'required' => false,
                        'disabled' => in_array('therapeuticArea',$options['lockedFields']),
                        'class' => 'OSSystem\EMTBundle\Entity\TherapeuticArea',
                        'property' => 'title',
                        'multiple'  => false,
                        'label' => 'conference.edit.therapeuticArea',
                        'attr' => array('placeholder' => 'conference.edit.therapeuticArea_ph')
                        )) 
                
            ->add('therapeuticAreaOther', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.therapeuticAreaOther',
                        'required' => false,
                        'disabled' => in_array('therapeuticAreaOther',$options['lockedFields']),
                        'attr' => array(
                            'maxlength' => 100)
                        ))
                
                
            ->add('startConferenceDate','date',
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'disabled' => in_array('startConferenceDate',$options['lockedFields']),
                        'input'  => 'datetime',
                        'widget' => 'single_text',
                        'format' => 'dd.MM.yyyy',
                        'attr' => array('class' => 'date datepicker', 'placeholder' => '',
                            'maxlength' => 20)
                        ))

                
            ->add('endConferenceDate','date',
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'disabled' => in_array('endConferenceDate',$options['lockedFields']),
                        'widget' => 'single_text',
                        'format' => 'dd.MM.yyyy',
                        'attr' => array('class' => 'date datepicker', 'placeholder' => '',
                            'maxlength' => 20)
                        ))
                
            ->add('organizingCompaniesNames', 'textarea',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.organizingCompaniesNames',
                        'disabled' => in_array('organizingCompaniesNames',$options['lockedFields']),
                        'attr' => array('placeholder' => 'conference.edit.organizingCompaniesNames_ph',
                            'maxlength' => 500)
                        ))
            ->add('contactPersonsNames', 'textarea',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.contactPersonsNames',
                        'disabled' => in_array('contactPersonsNames',$options['lockedFields']),
                        'attr' => array(
                            'maxlength' => 255)
                        ))
            ->add('organizingEmails', 'email',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.organizingEmails',
                        'disabled' => in_array('organizingEmails',$options['lockedFields']),
                        'attr' => array('placeholder' => 'conference.edit.organizingEmails_ph',
                            'maxlength' => 200)
                        ))
            ->add('website', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.website',
                        'disabled' => in_array('website',$options['lockedFields']),
                        'attr' => array('placeholder' => 'conference.edit.website_ph',
                            'maxlength' => 150)
                        ))
            
            ->add('autopublishing', null,  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'disabled' => in_array('autopublishing',$options['lockedFields']),
                        'label' => 'conference.edit.autopublishing',
                        'attr' => array(
                            'maxlength' => 150)
                        ))
                
                
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
                        'disabled' => true, //in_array('assesmentCountry',$options['lockedFields']),
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
