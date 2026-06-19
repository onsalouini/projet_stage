<?php
// src/Form/ProfileType.php
namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
{
$builder
->add('adresse', TextType::class, ['required' => false])
->add('telephone', TextType::class, ['required' => false])
->add('bio', TextType::class, ['required' => false])
->add('dateNaissance', DateType::class, [
'widget' => 'single_text',
'required' => false
])
->add('photoProfil', TextType::class, ['required' => false]);
}

public function configureOptions(OptionsResolver $resolver)
{
$resolver->setDefaults([
'data_class' => Profile::class,
]);
}
}