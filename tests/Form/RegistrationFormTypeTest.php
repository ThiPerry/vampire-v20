<?php
// tests/Form/RegistrationFormTypeTest.php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\RegistrationForm;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class RegistrationFormTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        // On instancie un validateur “light” pour prendre en charge l'option constraints
        $validator = Validation::createValidator();

        // On pré-charge notre form type et l’extension Validator
        return [
            new PreloadedExtension([new RegistrationForm()], []),
            new ValidatorExtension($validator),
        ];
    }

    public function testSubmitValidData(): void
    {
        // Données simulées valide
        $formData = [
            'pseudo'        => 'MonPseudo',
            'firstName'     => 'Alice',
            'email'         => 'alice@example.com',
            'plainPassword' => 'Secr3tP@ss',
            'agreeTerms'    => true,
        ];

        // On lie un nouvel User au form
        $user = new User();

        $form = $this->factory->create(RegistrationForm::class, $user);
        $form->submit($formData);

        // Pas d’erreur de mapping et le form est valide
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        // Vérifie que les setters ont été appelés
        $this->assertSame('MonPseudo',       $user->getPseudo());
        $this->assertSame('Alice',           $user->getFirstName());
        $this->assertSame('alice@example.com', $user->getEmail());

        // plainPassword et agreeTerms n’étant pas mappés, ils n’impactent pas l’entité
        $this->assertNull($user->getPassword());
    }

    public function testSubmitInvalidData(): void
    {
        // Soumission vide => générera des erreurs de NotBlank / IsTrue
        $form = $this->factory->create(RegistrationForm::class);
        $form->submit([]);

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());

        $messages = [];
        foreach ($form->getErrors(true) as $error) {
            $messages[] = $error->getMessage();
        }

        // Vérifie tes messages personnalisés
        $this->assertContains('Entrez votre mot de passe',   $messages);
        $this->assertContains("Vous devez agréér au termes.", $messages);

        // Vérifie aussi la contrainte de longueur : si le mdp est trop court
        $shortForm = $this->factory->create(RegistrationForm::class);
        $shortForm->submit([
            'plainPassword' => '123',
            'agreeTerms'    => true,
        ]);
        $shortMessages = [];
        foreach ($shortForm->getErrors(true) as $error) {
            $shortMessages[] = $error->getMessage();
        }
        $this->assertContains('Votre mot de passe dois faire au moins 6 charactères', $shortMessages);
    }
}
