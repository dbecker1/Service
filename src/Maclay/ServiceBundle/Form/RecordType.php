<?php

namespace Maclay\ServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateFrom', 'date', array("format" => "MMM/d/y"))
            ->add('dateTo', 'date', array("format" => "MMM/d/y"))
            ->add('numHours', 'integer')
            ->add('activity', 'text')
            ->add('notes', 'textarea', array("required" => false))
            ->add('organization', 'text')
            ->add('supervisor', 'text')
            ->add('attachment', 'file', array("required" => false))
            ->add('submit', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Maclay\ServiceBundle\Entity\Record'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'record';
    }
}
