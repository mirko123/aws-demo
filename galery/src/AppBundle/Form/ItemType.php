<?php

namespace AppBundle\Form;

use AppBundle\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("name")->add("description");

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Item::class
//            'csrf_protection' => true,
//            'csrf_field_name' => '_token',
//            // a unique key to help generate the secret token
//            'csrf_token_id'   => 'task_item',
        ]);
    }

    public function getName()
    {
        return 'app_bundle_item_type';
    }
}
