<?php

namespace Maclay\ServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClubBatchRecordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('activity', 'text')
            ->add('dateFrom', 'date', array("format" => "MMM/d/y"))
            ->add('dateTo', 'date', array("format" => "MMM/d/y"))
            ->add('organization', 'text')
            ->add('notes', 'textarea', array("required" => false))
            ->add('supervisor', 'text')
            ->add('studentHours', 'collection', array('type' => new StudentHoursType()))
            ->add('submit', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Maclay\ServiceBundle\Model\ClubBatchRecord'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'clubBatchRecord';
    }
}
