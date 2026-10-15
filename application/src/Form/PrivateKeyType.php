<?php

namespace App\Form;

use App\Entity\PrivateKey;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PrivateKeyType
 * @package App\Form
 */
class PrivateKeyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('path', FileType::class, ['help' => 'Требуется файл ЭЦП в формате .pfx']);
        $builder->add('password', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PrivateKey::class,
            'required' => true,
        ]);
    }
}