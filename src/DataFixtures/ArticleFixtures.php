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
use Doctrine\Persistence\ObjectManager;

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
        //Standard credit articles
        $manager->persist($this->create('article_10', 'CRED0010', 120, 10));
        $manager->persist($this->create('article_50', 'CRED0050', 500, 50));
        $manager->persist($this->create('article_100', 'CRED0100', 1000, 100));
        $manager->persist($this->create('article_500', 'CRED0500', 4500, 500));

        //CMD slave update
        $manager->persist($this->create('cmd_slave', 'cmdslave', 700, 0));

        //OLSX credit articles
        $manager->persist($this->create('olsx_10', 'OLSX0010', 120, 10));
        $manager->persist($this->create('olsx_50', 'OLSX0050', 500, 50));
        $manager->persist($this->create('olsx_100', 'OLSX0100', 1000, 100));
        $manager->persist($this->create('olsx_500', 'OLSX0500', 4500, 500));

        $manager->flush();
    }

    /**
     * Article factory.
     *
     * @param string $reference the name to reference
     * @param string $code      the code to store
     * @param int    $price     price of each article unit
     * @param int    $credit    credits allowed for each article ordered
     */
    private function create(string $reference, string $code, int $price, int $credit): Article
    {
        $article = new Article();
        $article->setCode($code);
        $article->setPrice($price);
        $article->setCredit($credit);
        $this->addReference($reference, $article);

        return $article;
    }
}
