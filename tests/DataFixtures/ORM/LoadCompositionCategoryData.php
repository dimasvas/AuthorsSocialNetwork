<?php
namespace Tests\DataFixtures\ORM;

use AppBundle\Entity\CompositionCategory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadCompCatData
 * @package AppBundle\DataFixtures
 */
class LoadCompositionCategoryData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $repository = $manager->getRepository('Gedmo\Translatable\Entity\Translation');
        $data =  $this->getInitData();

        foreach($data as $item){
            $entity = new CompositionCategory();

            $entity->setTranslatableLocale('ru_ru');
            $entity->setName($item['ru']);

            $repository->translate($entity, 'name', 'ua_ua', $item['ua']);

            $entity->setAlias($item['alias']);

            $manager->persist($entity);
            $manager->flush();

            $this->addReference('composition-category-'.$item['alias'], $entity);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }

    private function getInitData()
    {
        return [
            [
                'ru' => 'Книга',
                'ua' => 'Книга',
                'alias' => 'book',
            ],
            [
                'ru' => 'Сценарий',
                'ua' => 'Сценарії',
                'alias' => 'scenario',
            ],
            [
                'ru' => 'Музыка',
                'ua' => 'Музика',
                'alias' => 'music',
            ],
            [
                'ru' => 'Анимация',
                'ua' => 'Анімація',
                'alias' => 'animation',
            ],
            [
                'ru' => 'Изображение',
                'ua' => 'Зображення',
                'alias' => 'image',
            ],
            [
                'ru' => 'Идея',
                'ua' => 'Идея',
                'alias' => 'idea',
            ],
            [
                'ru' => 'Игра',
                'ua' => 'Игра',
                'alias' => 'game',
            ],
            [
                'ru' => 'Cофт',
                'ua' => 'Софт',
                'alias' => 'soft',
            ],
            [
                'ru' => 'Видео',
                'ua' => 'Видео',
                'alias' => 'video',
            ]
            
        ];
    }
}