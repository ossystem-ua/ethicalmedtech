<?php

namespace OSSystem\EMTBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\HttpFoundation\Request;

class ProfileFormType extends BaseType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //parent::buildForm($builder, $options);
        ///var_dump($options);
        $builder
            ->add('organization', 'text', 
                    array(
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.organization',
                        'attr' => array('placeholder' => 'emt_user.default.organization')
                     ))
            ->add('category', 'entity', 
                    array(
                        'class' => 'OSSystemEMTBundle:UserCategory',
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.category',
                        'property' => 'category',
                        ))
            ->add('address', 'text',
                    array(
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.address',
                        'attr' => array('placeholder' => 'emt_user.default.address')
                    ))
            ->add('address2', 'text',
                    array(
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.address2',
                        'required' => false,
                        'attr' => array('placeholder' => 'emt_user.address2')
                    ))
            ->add('postalCode', 'text',
                    array(
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.postalCode',
                        'attr' => array('placeholder' => 'emt_user.default.postalCode')
                    ))
            ->add('city', 'text',
                    array(
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.city',
                        'attr' => array('placeholder' => 'emt_user.default.city')
                    ))
            ->add('country', 'entity', 
                    array(
                        'class' => 'OSSystemEMTBundle:Country',
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.country',
                        'property' => 'title',
                    ))
            
            ->add('title', 'text',
                    array(
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.title',
                        'attr' => array('placeholder' => 'emt_user.default.title')
                    ))
            ->add('fName', 'text',
                    array(
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.fName',
                        'attr' => array('placeholder' => 'emt_user.default.fName')
                    ))
            ->add('lName', 'text',
                    array(
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.lName',
                        'attr' => array('placeholder' => 'emt_user.default.lName')
                    ))
            ->add('jobTitle', 'text',
                    array(
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.jobTitle',
                        'attr' => array('placeholder' => 'emt_user.default.jobTitle')
                    ))
            ->add('phone', 'text',
                    array(
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.phone',
                        'attr' => array('placeholder' => 'emt_user.default.phone')
                    ))
            ->add('email', 'email',
                    array(
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.email',
                        'attr' => array('placeholder' => 'emt_user.default.email')
                    ))
             ->add('optIn', 'checkbox',
                    array(
                        'translation_domain' => 'OSSystemEMTBundle',
                        'label' => 'emt_user.optIn',
                        'required'  => false,
                        'attr' => array('placeholder' => 'emt_user.optIn')
                    ))
        ;
    }

    public function getName()
    {
        return 'user_profile';
    }
    
    public function getDefaultOptions(array $options)
    {
        return $options;
    }
}
