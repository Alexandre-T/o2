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

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article resource.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\Table(
 *     name="tr_article",
 *     schema="data",
 *     options={"comment": "Article resource table"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uk_article_code",  columns={"code"})
 *     }
 * )
 */
class Article
{
    /**
     * Article identifier.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer", name="id", options={"comment": "Article identifier"})
     */
    private $identifier;

    /**
     * Article unique code.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=8, options={"comment": "Article unique code"})
     */
    private $code;

    /**
     * Article cost.
     *
     * TODO find type.
     *
     * @var float
     *
     * @ORM\Column(type="decimal", precision=6, scale=2, options={"comment": "Article cost"})
     */
    private $cost;

    /**
     * Number of credit gained when buying this article.
     *
     * @ORM\Column(type="integer", options={"comment": "Credit gained when buying article"})
     */
    private $credit;

    /**
     * Identifier getter.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->identifier;
    }

    /**
     * Code getter.
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Cost getter.
     *
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Credit getter.
     *
     * @return int|null
     */
    public function getCredit(): ?int
    {
        return $this->credit;
    }

    /**
     * Code fluent setter.
     *
     * @param string $code unique code of article
     *
     * @return Article
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Cost fluent setter.
     *
     * @param mixed $cost new cost
     *
     * @return Article
     */
    public function setCost($cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Credit fluent setter.
     *
     * @param int $credit credit gained when buying article
     *
     * @return Article
     */
    public function setCredit(int $credit): self
    {
        $this->credit = $credit;

        return $this;
    }
}
