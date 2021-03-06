<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Genre;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class LoadCompositionGenreData
 * @package AppBundle\DataFixtures
 */
class LoadCompositionGenreData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $data =  $this->getInitData();
        
        foreach ($data as $type => $genre) {
            foreach ($genre as $item) {
                $this->setData($type, $item, $manager);
            }
        }
    }
    
    private function setData($type, $item, $manager) {
        
        $repository = $manager->getRepository('Gedmo\Translatable\Entity\Translation');
        $entity = new Genre();

        $entity->setTranslatableLocale('ru_ru');
        $entity->setName($item['ru']);
        $entity->setAlias($item['alias']);
        
        $entity->setCategory($this->getReference("composition-category-{$type}"));
        
        $repository->translate($entity, 'name', 'ua', $item['ua']);
 
        $manager->persist($entity);
        $manager->flush();

        $this->addReference("composition-genre-{$type}-{$item['alias']}", $entity);
    }

    private function getInitData()
    {
        return  ['book' => [
                    ['ru' => 'Проза', 'ua' => 'Проза', 'alias' => 'prose'],
                    ['ru' => 'Поэзия', 'ua' => 'Поезія', 'alias' => 'poesy'],
                    ['ru' => 'Мемуары', 'ua' => 'Мемуары', 'alias' => 'memoirs'],
                    ['ru' => 'История', 'ua' => 'Історія', 'alias' => 'history'],
                    ['ru' => 'Детская', 'ua' => 'Дитяча', 'alias' => 'child'],
                    ['ru' => 'Приключения', 'ua' => 'Пригоди', 'alias' => 'adventure'],
                    ['ru' => 'Фантастика', 'ua' => 'Фантастика', 'alias' => 'fantastic'],
                    ['ru' => 'Фэнтези', 'ua' => 'Фентезі', 'alias' => 'fantasy'],
                    ['ru' => 'Философия', 'ua' => 'Філософія', 'alias' => 'philosophy'],
//                    ['ru' => 'Богословие', 'ua' => "Богослов'я", 'alias' => 'theology'],
                    ['ru' => 'Роман', 'ua' => 'Роман', 'alias' => 'novel'],
                    ['ru' => 'Естествознание', 'ua' => 'Природознавство', 'alias' => 'naturalHistory'],
                    ['ru' => 'Изобретательство', 'ua' => 'Винахідництво', 'alias' => 'invention'],
                    ['ru' => 'Юмор', 'ua' => 'Гумор', 'alias' => 'humor'],
                    ['ru' => 'Притчи', 'ua' => 'Притчі', 'alias' => 'proverbs'],
                    ['ru' => 'Сказки', 'ua' => 'Казки', 'alias' => 'fairyTales'],
                    ['ru' => 'Драматургия', 'ua' => 'Драматургія', 'alias' => 'dramaturgy'],
                    ['ru' => 'Дом и семья', 'ua' => 'Будинок і родина', 'alias' => 'family'],
                    ['ru' => 'Другое', 'ua' => 'Інші', 'alias' => 'other']
                ],
                'scenario' => [
                    ['ru' => 'Детские', 'ua' => 'Дитячі', 'alias' => 'children'],
                    ['ru' => 'На Пасху', 'ua' => 'На Великдень', 'alias' => 'easter'],
                    ['ru' => 'На Рождество', 'ua' => 'На Різдво', 'alias' => 'christmas'],
                    ['ru' => 'На Новый Год', 'ua' => 'На новий рік', 'alias' => 'newYear'],
                    ['ru' => 'На 8 марта', 'ua' => 'На 8 березня', 'alias' => 'eightMarch'],
                    ['ru' => 'День матери', 'ua' => 'День матері', 'alias' => 'mothersDay'],
                    ['ru' => 'День отца', 'ua' => 'День батька', 'alias' => 'fathersDay'],
                    ['ru' => 'На день рождения', 'ua' => 'На день народження', 'alias' => 'birthday'],
                    ['ru' => 'Лагерь', 'ua' => 'Табір', 'alias' => 'camp'],
                    ['ru' => 'На Baby Shower', 'ua' => 'На Baby Shower', 'alias' => 'babyShower'],
                    ['ru' => 'Притчи', 'ua' => 'Притчі', 'alias' => 'proverbs'],
                    ['ru' => 'На свадьбу', 'ua' => 'На весілля', 'alias' => 'wedding'],
                    ['ru' => 'Кукольные постановки', 'ua' => 'Лялькові постановки', 'alias' => 'puppetry'],
                    ['ru' => 'На Жатву', 'ua' => 'На Жнива', 'alias' => 'harvest'],
                    ['ru' => 'День Благодарения', 'ua' => 'День подяки', 'alias' => 'thanksgivingDay'],
                    ['ru' => 'Евангелизационные', 'ua' => 'Євангелізаційні', 'alias' => 'evangelism'],
                    ['ru' => 'Другое', 'ua' => 'Інші', 'alias' => 'other']
                ],
                'music' => [
                    ['ru' => 'Фонограмма', 'ua' => 'Фонограми ', 'alias' => 'phonogram'],
                    ['ru' => 'Классика', 'ua' => 'Класика', 'alias' => 'classic'],
                    ['ru' => 'Народов мира', 'ua' => 'Народів світу', 'alias' => 'ethnical'],
                    ['ru' => 'Джаз', 'ua' => 'Джаз', 'alias' => 'jazz'],
                    ['ru' => 'Кантри', 'ua' => 'Кантрі', 'alias' => 'сountry'],
                    ['ru' => 'Прославление', 'ua' => 'Прославлення', 'alias' => 'worship'],
                    ['ru' => 'Поп', 'ua' => 'Поп', 'alias' => 'pop'],
                    ['ru' => 'Рок', 'ua' => 'Рок', 'alias' => 'rock'],
                    ['ru' => 'Кавер', 'ua' => 'Кавер', 'alias' => 'cover'],
                    ['ru' => 'Детские', 'ua' => 'Дитячі', 'alias' => 'children'],
                    ['ru' => 'Колыбельная', 'ua' => 'Колискова', 'alias' => 'lullaby'],
                    ['ru' => 'Рэп', 'ua' => 'Рэп', 'alias' => 'rap'],
                    ['ru' => 'Другое', 'ua' => 'Інші', 'alias' => 'other']
                ],
                'image' => [
                    ['ru' => 'Арт', 'ua' => 'Арт', 'alias' => 'art'],
                    ['ru' => 'Обои', 'ua' => 'Шпалеры', 'alias' => 'walpaper'],
                    ['ru' => 'Раскраски', 'ua' => 'Розмальовки', 'alias' => 'сoloring'],
                    ['ru' => 'Иллюстрации', 'ua' => 'Ілюстрації', 'alias' => 'illustration'],
                    ['ru' => 'Детские', 'ua' => 'Дитячі', 'alias' => 'children'],
                    ['ru' => 'Открытки', 'ua' => 'Листівки', 'alias' => 'card'],
                    ['ru' => 'Пейзажи', 'ua' => 'Пейзажі', 'alias' => 'landscape'],
                    ['ru' => 'Портреты', 'ua' => 'Портрети', 'alias' => 'portrait'],
                    ['ru' => 'Другое', 'ua' => 'Інші', 'alias' => 'other'] 
                ],
                'idea' => [
                    ['ru' => 'Воскресная школа', 'ua' => 'Недільна школа', 'alias' => 'sundaySchool'],
                    ['ru' => 'Поделки', 'ua' => 'Вироби', 'alias' => 'craft'],
                    ['ru' => 'Мастер классы', 'ua' => 'Майстер класи', 'alias' => 'masterClass'],
                    ['ru' => 'Дизайн на праздники', 'ua' => 'Дизайн на свята', 'alias' => 'design'],
                    ['ru' => 'Сюрпризы', 'ua' => 'Сюрпризи', 'alias' => 'surprise'],
                    ['ru' => 'Другое', 'ua' => 'Інші', 'alias' => 'other'] 
                ],
                'lyric' => [
                    ['ru' => 'Песни', 'ua' => 'Пісні', 'alias' => 'songs'],
                    ['ru' => 'Стихи', 'ua' => 'Вірші', 'alias' => 'verses'],
                ],
                'game' => [
                    ['ru' => 'Игры в помещении', 'ua' => 'Ігри в приміщенні', 'alias' => 'indoorGame'],
                    ['ru' => 'Игры на природе', 'ua' => 'Ігри на природі', 'alias' => 'outdoorGame'],
                    ['ru' => 'Настольные игры', 'ua' => 'Настільні ігри', 'alias' => 'boardGame'],
                    ['ru' => 'Другое', 'ua' => 'Інші', 'alias' => 'other'] 
                ],
                'video' => [
                    ['ru' => 'Фильм', 'ua' => 'Фильм', 'alias' => 'film'],
                    ['ru' => 'Клип', 'ua' => 'Клип', 'alias' => 'clip'],
                    ['ru' => 'Другое', 'ua' => 'Інші', 'alias' => 'other'] 
                ],
                'animation' => [
                    ['ru' => 'Детские', 'ua' => 'Дитячі', 'alias' => 'children'],
                    ['ru' => 'Другое', 'ua' => 'Інші', 'alias' => 'other'] 
                ]
            ];
    }  
    
        /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
    
}