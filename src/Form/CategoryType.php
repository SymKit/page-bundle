<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Form;

use Symkit\FormBundle\Form\Type\FormSectionType;
use Symkit\FormBundle\Form\Type\SlugType;
use Symkit\MenuBundle\Entity\Menu;
use Symkit\PageBundle\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CategoryType extends AbstractType
{
    /**
     * @param class-string $categoryClass
     */
    public function __construct(
        private readonly string $categoryClass = Category::class,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('sec_general', FormSectionType::class, [
            'label' => 'form.category.sec_general',
            'section_icon' => 'heroicons:tag-20-solid',
            'section_description' => 'form.category.sec_general_desc',
            'mapped' => false,
        ]);
        $builder->add('name', TextType::class, [
            'label' => 'form.category.name',
            'attr' => ['placeholder' => 'form.category.name_placeholder'],
            'help' => 'form.category.name_help',
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'form.category.description',
            'required' => false,
            'attr' => ['rows' => 3, 'placeholder' => 'form.category.description_placeholder'],
            'help' => 'form.category.description_help',
        ]);
        $builder->add('defaultMenu', EntityType::class, [
            'class' => Menu::class,
            'choice_label' => 'name',
            'label' => 'form.category.default_menu',
            'required' => false,
            'placeholder' => 'form.category.default_menu_placeholder',
            'help' => 'form.category.default_menu_help',
        ]);
        $builder->add('slug', SlugType::class, [
            'label' => 'form.category.slug',
            'required' => false,
            'target' => 'name',
            'attr' => ['placeholder' => 'form.category.slug_placeholder'],
            'help' => 'form.category.slug_help',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->categoryClass,
            'translation_domain' => 'SymkitPageBundle',
        ]);
    }
}
