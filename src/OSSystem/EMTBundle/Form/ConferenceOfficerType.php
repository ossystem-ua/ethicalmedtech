<?php

namespace OSSystem\EMTBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConferenceOfficerType extends AbstractType
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
                        'label' => 'conference.edit.name',
                        'attr' => array('placeholder' => 'conference.edit.name_ph',
                            'maxlength' => 150)
                        ))
            ->add('acronym', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.acronym',
                        'attr' => array(
                            'maxlength' => 100)
                        ))
                
            ->add('therapeuticArea', 'entity',
                    array('translation_domain' => 'OSSystemEMTBundle', 
                        'required' => false,
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
                        'attr' => array(
                            'maxlength' => 100)
                        ))
                
            ->add('startConferenceDate','date',
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'input'  => 'datetime',
                        'widget' => 'single_text',
                        'format' => 'dd.MM.yyyy',
                        'attr' => array('class' => 'date datepicker', 'placeholder' => '',
                            'maxlength' => 20)
                        ))

                
            ->add('endConferenceDate','date',
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'widget' => 'single_text',
                        'format' => 'dd.MM.yyyy',
                        'attr' => array('class' => 'date datepicker', 'placeholder' => '',
                            'maxlength' => 20)
                        ))
                
            ->add('organizingCompaniesNames', 'textarea',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.organizingCompaniesNames',
                        'attr' => array('placeholder' => 'conference.edit.organizingCompaniesNames_ph',
                            'maxlength' => 500)
                        ))
            ->add('contactPersonsNames', 'textarea',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.contactPersonsNames',
                        'attr' => array(
                            'maxlength' => 255)
                        ))
            ->add('organizingEmails', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.organizingEmails',
                        'attr' => array('placeholder' => 'conference.edit.organizingEmails_ph',
                            'maxlength' => 200)
                        ))
            ->add('website', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.website',
                        'attr' => array('placeholder' => 'conference.edit.website_ph',
                            'maxlength' => 150)
                        ))
            
            ->add('autopublishing', null,  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.autopublishing',
                        'attr' => array(
                            'maxlength' => 150)
                        ))
            ->add('delegatesMoreThan1Country', 'choice',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.delegatesMoreThan1Country',
                        'choices' => array(1 => 'Yes', 0 => 'No', 2 => 'No information available at this time'),
                        )
                )
            ->add('delegatesCountries', 'entity',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.delegatesCountries',
                        'class' => 'OSSystem\EMTBundle\Entity\Country',
                        'multiple' => true,
                        'attr' => array(
                            )
                        ))
            ->add('delegatesAnticipate', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.delegatesAnticipatename',
                        'required' => false,
                        'attr' => array(
                            'maxlength' => 11)
                        ))
            ->add('assesmentNameVenue', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.assesmentNameVenue',
                        'attr' => array('placeholder' => 'conference.edit.assesmentNameVenue_ph',
                            'maxlength' => 250,
                            )
                        ))
            ->add('assesmentCategoryVenue', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.assesmentCategoryVenue',
                        'attr' => array('placeholder' => 'conference.edit.assesmentCategoryVenue_ph',
                            'maxlength' => 150,
                            )
                        ))
            ->add('assesmentCity', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.assesmentCity',
                        'attr' => array('placeholder' => 'conference.edit.assesmentCity_ph',
                            'maxlength' => 150,
                            )
                        ))
            ->add('assesmentCountry', 'entity',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.assesmentCountry',
                        'class' => 'OSSystem\EMTBundle\Entity\Country',
                        'multiple' => false,
                        'attr' => array(
                            )
                        ))
            ->add('assesmentLocalNA', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.assesmentLocalNA',
                        'attr' => array('placeholder' => 'conference.edit.assesmentLocalNA_ph',
                            'maxlength' => 150,
                            )
                        ))
            ->add('assesmentProposedAccomodation', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'required' => false,
                        'label' => 'conference.edit.assesmentProposedAccomodation',
                        'attr' => array('placeholder' => 'conference.edit.assesmentProposedAccomodation_ph',
                            'maxlength' => 150,
                            )
                        ))
                
            ->add('eventProgramme', 'text',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.eventProgramme',
                        'required' => false,
                        'attr' => array(
                            'placeholder' => 'conference.edit.eventProgramme_ph',)
                        )
                        
                )
                
            ->add('comments', 'textarea',  
                    array('translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'conference.edit.comments',
                        'required' => false,
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
