<?php
/**
 * This file is part of the O2 Application.
 *
 * PHP version 7.1|7.2|7.3|7.4
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Article fixtures.
 */
class ArticleFixtures extends Fixture
{
    /**
     * Load articles.
     *
     * @param ObjectManager $manager manager to save data
     */
    public function load(ObjectManager $manager): void
    {
        //Ten by ten.
        $ten = new Article();
        $ten->setCode('CRED0010');
        $ten->setPrice(100);
        $ten->setCredit(10);

        //Hundred by hundred.
        $hundred = new Article();
        $hundred->setCode('CRED0100');
        $hundred->setPrice(1000);
        $hundred->setCredit(100);

        //fiveHundred by fiveHundred.
        $fiveHundred = new Article();
        $fiveHundred->setCode('CRED0500');
        $fiveHundred->setPrice(4500);
        $fiveHundred->setCredit(500);

        //These references are used.
        $this->addReference('article_10', $ten);
        $this->addReference('article_100', $hundred);
        $this->addReference('article_500', $fiveHundred);

        //Persist prod data
        $manager->persist($ten);
        $manager->persist($hundred);
        $manager->persist($fiveHundred);

        $manager->flush();
    }
}
