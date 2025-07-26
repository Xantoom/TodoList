<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
			->add('password', RepeatedType::class, [
				'type' => PasswordType::class,
				'invalid_message' => 'Les deux mots de passe doivent correspondre.',
				'required' => true,
				'first_options'  => ['label' => 'Mot de passe'],
				'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
			])
			->add('email', EmailType::class, ['label' => 'Adresse email'])
			->add('roles', ChoiceType::class,
				[
					'label' => 'Rôles',
					'choices' => [
						'Utilisateur' => 'ROLE_USER',
						'Administrateur' => 'ROLE_ADMIN',
					],
					'expanded' => false,
					'multiple' => false,
				]
			)
		;

		// Transform array to single value and vice versa
		$builder->get('roles')->addModelTransformer(new CallbackTransformer(
			function ($rolesArray) {
				// Transform array to single value for the form
				return count($rolesArray) ? $rolesArray[0] : null;
			},
			function ($singleRole) {
				// Transform single value back to array for the entity
				return $singleRole ? [$singleRole] : [];
			}
		));
	}
}
