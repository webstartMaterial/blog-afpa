<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, array(
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'attr' => array(
                    'placeholder' => 'Entrer votre prénom'
                ),
                'label' => 'Votre prénom',
            ))
            ->add('lastName', TextType::class, array(
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'attr' => array(
                    'placeholder' => 'Entrer votre nom'
                ),
                'label' => 'Votre nom',

            ))
            ->add('dateOfBirth', DateType::class, array(
                'row_attr' => [
                    'class' => 'col-md-12'
                ],
                'label' => 'Votre date de naissance',
            ))
            ->add('email', EmailType::class, array(
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'attr' => array(
                    'placeholder' => 'Entrer votre email'
                ),
                'label' => 'Votre email',

            ))
            ->add('sexe', ChoiceType::class, [
                'attr' => [
                    'class' => 'd-flex justify-content-between'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'choices' => [
                    'Homme' => 'H',
                    'Femme' => 'F',
                    'Non binaire' => 'N',
                    'Non genré' => 'X',
                    'Xéno genre' => 'G',
                ],
                'data' => 'F',
                'expanded' => true,
                'label' => 'Votre sexe',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Veuillez accepter nos conditions générales',
                'mapped' => false,
                'row_attr' => [
                    'class' => 'col-md-6 mt-3'
                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Entrer votre mot de passe'],
                'row_attr' => [
                    'class' => 'col-md-12'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
