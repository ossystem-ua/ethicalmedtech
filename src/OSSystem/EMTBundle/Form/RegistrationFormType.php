<?php

namespace OSSystem\EMTBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\HttpFoundation\Request;

class RegistrationFormType extends BaseType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('fName', null,  
                    array('translation_domain' => 'OSSystemEMTBundle', 
                        'label' => 'emt_user.fName',
                        'attr' => array('placeholder' => 'emt_user.fName')
                        ))
            ->add('lName', null,  
                    array('translation_domain' => 'OSSystemEMTBundle', 
                        'label' => 'emt_user.lName',
                        'attr' => array('placeholder' => 'emt_user.lName')
                        ))
            ->add('email', null,  
                    array('translation_domain' => 'OSSystemEMTBundle', 
                        'label' => 'emt_user.email',
                        'attr' => array('placeholder' => 'emt_user.email')
                        ))
            ->add('username', null,  
                    array('translation_domain' => 'OSSystemEMTBundle', 
                        'label' => 'emt_user.username',
                        'attr' => array('placeholder' => 'emt_user.username')
                        ))

            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'first_options' => array(
                    'label' => 'emt_user.password',
                    'translation_domain'=>'OSSystemEMTBundle',
                ),
                'second_options' => array(
                    'label' => 'emt_user.plain_password',
                    'translation_domain'=>'OSSystemEMTBundle'
                ),
                'translation_domain'=>'OSSystemEMTBundle'
            ))
               
                
        ;
    }

    public function getName()
    {
        return 'user_registration';
    }
    
    public function getDefaultOptions(array $options)
    {
        return $options;
    }
}
