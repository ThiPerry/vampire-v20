<?php
// src/Form/ProfileFormType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Mail',
            ])
            // On demande le mot de passe courant pour vérifier l'identité
            ->add('currentPassword', PasswordType::class, [
                'label'       => 'Mot de passe actuel',
                'mapped'      => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre mot de passe actuel']),
                    new UserPassword(['message' => 'Mot de passe incorrect']),
                ],
            ])
            // On propose ensuite un nouveau mot de passe (optionnel)
            ->add('newPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'mapped'          => false,
                'required'        => false,
                'first_options'   => ['label' => 'Nouveau mot de passe'],
                'second_options'  => ['label' => 'Confirmation'],
                'invalid_message' => 'Les deux mots de passe doivent correspondre',
                'constraints'     => [
                    // ne s'applique que si l'utilisateur remplit un nouveau mot de passe
                    new Length([
                        'min'        => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        'max'        => 4096,
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer les modifications',
                'attr'  => ['class' => 'btn-primary']
            ])
        ;
    }
}
