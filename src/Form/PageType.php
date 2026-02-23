<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symkit\FaqBundle\Entity\Faq;
use Symkit\FormBundle\Form\Type\FormSectionType;
use Symkit\FormBundle\Form\Type\SlugType;
use Symkit\MediaBundle\Form\MediaType;
use Symkit\MenuBundle\Entity\Menu;
use Symkit\MenuBundle\Entity\MenuItem;
use Symkit\PageBundle\Entity\Page;
use Symkit\PageBundle\Service\PageLayoutRegistry;

final class PageType extends AbstractType
{
    /**
     * @param class-string<Page> $pageClass
     * @param class-string       $categoryClass
     */
    public function __construct(
        private readonly PageLayoutRegistry $layoutRegistry,
        private readonly string $pageClass,
        private readonly string $categoryClass,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('sec_general', FormSectionType::class, [
            'label' => 'form.page.sec_general',
            'section_icon' => 'heroicons:document-text-20-solid',
            'section_description' => 'form.page.sec_general_desc',
            'mapped' => false,
        ]);
        $builder->add('title', TextType::class, [
            'label' => 'form.page.title',
            'attr' => ['placeholder' => 'form.page.title_placeholder'],
        ]);
        $builder->add('slug', SlugType::class, [
            'label' => 'form.page.slug',
            'required' => false,
            'target' => 'title',
            'attr' => ['placeholder' => 'form.page.slug_placeholder'],
            'help' => 'form.page.slug_help',
        ]);
        $builder->add('category', EntityType::class, [
            'class' => $this->categoryClass,
            'required' => false,
            'choice_label' => 'name',
            'label' => 'form.page.category',
            'placeholder' => 'form.page.category_placeholder',
            'help' => 'form.page.category_help',
        ]);

        $builder->add('sec_content', FormSectionType::class, [
            'label' => 'form.page.sec_content',
            'section_icon' => 'heroicons:square-3-stack-3d-20-solid',
            'section_description' => 'form.page.sec_content_desc',
            'mapped' => false,
        ]);
        $builder->add('excerpt', TextareaType::class, [
            'label' => 'form.page.excerpt',
            'required' => false,
            'attr' => ['rows' => 2, 'placeholder' => 'form.page.excerpt_placeholder'],
        ]);
        $builder->add('content', TextareaType::class, [
            'label' => 'form.page.content',
            'attr' => ['rows' => 15, 'placeholder' => 'form.page.content_placeholder'],
        ]);
        $builder->add('status', ChoiceType::class, [
            'label' => 'form.page.status',
            'choices' => [
                'form.page.status_draft' => Page::STATUS_DRAFT,
                'form.page.status_published' => Page::STATUS_PUBLISHED,
            ],
        ]);
        $builder->add('template', ChoiceType::class, [
            'label' => 'form.page.template',
            'required' => false,
            'placeholder' => 'form.page.template_placeholder',
            'choices' => $this->layoutRegistry->getLayoutChoices(),
            'help' => 'form.page.template_help',
        ]);

        $builder->add('sec_menus', FormSectionType::class, [
            'label' => 'form.page.sec_menus',
            'section_icon' => 'heroicons:bars-3-20-solid',
            'section_description' => 'form.page.sec_menus_desc',
            'mapped' => false,
        ]);
        $builder->add('sidebarLeftMenu', EntityType::class, [
            'class' => Menu::class,
            'choice_label' => 'name',
            'label' => 'form.page.sidebar_menu',
            'required' => false,
            'placeholder' => 'form.page.sidebar_menu_placeholder',
            'help' => 'form.page.sidebar_menu_help',
        ]);
        $builder->add('activeMenu', EntityType::class, [
            'class' => Menu::class,
            'choice_label' => 'name',
            'label' => 'form.page.active_menu',
            'required' => false,
            'placeholder' => 'form.page.active_menu_placeholder',
            'help' => 'form.page.active_menu_help',
        ]);
        $builder->add('activeMenuItem', EntityType::class, [
            'class' => MenuItem::class,
            'choice_label' => 'label',
            'label' => 'form.page.active_menu_item',
            'required' => false,
            'placeholder' => 'form.page.active_menu_item_placeholder',
            'help' => 'form.page.active_menu_item_help',
            'choices' => [],
        ]);

        $builder->add('sec_media', FormSectionType::class, [
            'label' => 'form.page.sec_media',
            'section_icon' => 'heroicons:photo-20-solid',
            'section_description' => 'form.page.sec_media_desc',
            'mapped' => false,
        ]);
        $builder->add('ogImage', MediaType::class, [
            'label' => 'form.page.og_image',
            'required' => false,
            'help' => 'form.page.og_image_help',
        ]);
        $builder->add('metaTitle', TextType::class, [
            'label' => 'form.page.meta_title',
            'required' => false,
            'attr' => ['placeholder' => 'form.page.meta_title_placeholder'],
            'help' => 'form.page.meta_title_help',
        ]);
        $builder->add('metaDescription', TextareaType::class, [
            'label' => 'form.page.meta_description',
            'required' => false,
            'attr' => ['rows' => 2, 'placeholder' => 'form.page.meta_description_placeholder'],
            'help' => 'form.page.meta_description_help',
        ]);

        $builder->add('sec_faq', FormSectionType::class, [
            'label' => 'form.page.sec_faq',
            'section_icon' => 'heroicons:question-mark-circle-20-solid',
            'section_description' => 'form.page.sec_faq_desc',
            'mapped' => false,
        ]);
        $builder->add('faq', EntityType::class, [
            'class' => Faq::class,
            'choice_label' => 'title',
            'label' => 'form.page.faq',
            'required' => false,
            'placeholder' => 'form.page.faq_placeholder',
            'help' => 'form.page.faq_help',
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $page = $event->getData();
            if (null === $page || !$page instanceof $this->pageClass) {
                return;
            }

            $this->updateMenuItemChoices($event->getForm(), $page->getActiveMenu());
        });

        $builder->get('activeMenu')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $form = $event->getForm();
            $parent = $form->getParent();
            if ($parent instanceof FormInterface) {
                $data = $form->getData();
                $this->updateMenuItemChoices($parent, $data instanceof Menu ? $data : null);
            }
        });
    }

    private function updateMenuItemChoices(FormInterface $form, ?Menu $menu): void
    {
        $choices = $menu ? $menu->getItems()->toArray() : [];

        $form->add('activeMenuItem', EntityType::class, [
            'class' => MenuItem::class,
            'choice_label' => 'label',
            'label' => 'form.page.active_menu_item',
            'required' => false,
            'placeholder' => $menu ? 'form.page.active_menu_item_placeholder_select' : 'form.page.active_menu_item_placeholder',
            'help' => 'form.page.active_menu_item_help',
            'choices' => $choices,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->pageClass,
            'translation_domain' => 'SymkitPageBundle',
        ]);
    }
}
