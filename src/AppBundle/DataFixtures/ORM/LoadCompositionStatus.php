<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\CompositionStatus;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadCompositionStatus extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $repository = $manager->getRepository('Gedmo\Translatable\Entity\Translation');
        $data =  $this->getInitData();

        foreach($data as $item) {

            $entity =  new CompositionStatus();

            $entity->setTranslatableLocale('ru_ru');
            $entity->setName($item['ru']);

            $repository->translate($entity, 'name', 'ua_ua', $item['ua']);

            $entity->setAlias($item['alias']);

            $manager->persist($entity);
            $manager->flush();

            $this->addReference('composition-status'.$item['alias'], $entity);
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
                'ru' => 'В процессе',
                'ua' => 'В процессе',
                'alias' => CompositionStatus::IN_PROCESS_STATUS_NAME
            ],
            [
                'ru' => 'Завершено',
                'ua' => 'Завершено',
                'alias' => CompositionStatus::FINISHED_STATUS_NAME
            ],
            [
                'ru' => 'Замороженно',
                'ua' => 'Замороженно',
                'alias' => CompositionStatus::FREEZED_STATUS_NAME
            ]
        ];
    }
}