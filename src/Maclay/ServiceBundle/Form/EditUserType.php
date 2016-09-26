<?php

namespace Maclay\ServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('lastName', 'text')
            ->add('firstName', 'text')
            ->add('middleName', 'text', array("required" => false))
            ->add('email', 'text')
            ->add('grade', 'integer' , array('mapped' => false))
            ->add('studentNumber', 'text' , array('mapped' => false))
            ->add('newPassword', 'text', array("required" => false, 'mapped' => false))
            ->add('submit', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Maclay\ServiceBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'editUser';
    }
}
