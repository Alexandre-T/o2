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

namespace App\Manager;

use App\Entity\Article;
use App\Exception\NoArticleException;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;

/**
 * Article Manager.
 */
class ArticleManager
{
    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ArticleManager constructor.
     *
     * @param EntityManagerInterface $entityManager provided by dependency injection
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Retrieve an article with its code.
     *
     * @param string $code code of article to retrieve
     *
     * @throws NoArticleException when article with this code does not exist in database
     *
     * @return Article
     */
    public function retrieveByCode(string $code): Article
    {
        $article = $this->getMainRepository()->findOneByCode($code);
        if ($article instanceof Article) {
            return $article;
        }

        throw new NoArticleException(sprintf('Article with code %s does not exist', $code));
    }

    /**
     * Return the main repository.
     *
     * @return EntityRepository|ArticleRepository|ObjectRepository
     */
    protected function getMainRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Article::class);
    }
}
