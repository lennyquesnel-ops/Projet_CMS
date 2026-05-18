<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('nom', TextType::class, [
				'label' => 'Nom',
				'required' => true,
				'attr' => [
					'class' => 'form-control form-control-lg',
					'placeholder' => 'Votre nom',
				],
			])
			->add('prenom', TextType::class, [
				'label' => 'Prénom',
				'required' => true,
				'attr' => [
					'class' => 'form-control form-control-lg',
					'placeholder' => 'Votre prénom',
				],
			])
			->add('email', EmailType::class, [
				'label' => 'Email',
				'required' => true,
				'attr' => [
					'class' => 'form-control form-control-lg',
					'placeholder' => 'votre.email@exemple.fr',
				],
			])
			->add('objet', TextType::class, [
				'label' => 'Objet',
				'required' => true,
				'attr' => [
					'class' => 'form-control form-control-lg',
					'placeholder' => 'Objet de votre message',
				],
			])
			->add('message', TextareaType::class, [
				'label' => 'Message',
				'required' => true,
				'attr' => [
					'class' => 'form-control form-control-lg',
					'placeholder' => 'Votre message',
					'rows' => 6,
				],
			])
			->add('envoyer', SubmitType::class, [
				'label' => 'Envoyer le message',
				'attr' => [
					'class' => 'btn btn-primary btn-modern btn-rounded px-5 py-3',
				],
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Contact::class,
		]);
	}
}