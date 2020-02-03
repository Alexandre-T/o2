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

namespace App\Twig;

use App\Entity\Article;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Article twig extension.
 */
class ArticleExtension extends AbstractExtension
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * ArticleExtension constructor.
     *
     * @param TranslatorInterface $translator The translator provided by injection dependency
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * List of filters.
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('article', [$this, 'articleTranslate']),
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
        ];
    }

    /**
     * Return the translated name of article.
     *
     * @param mixed $value value to evaluate
     */
    public function articleTranslate($value): string
    {
        if (!$value instanceof Article) {
            return $value;
        }

        /** @var Article $value Value can only be an Article entity */

        return $this->translator->trans(sprintf(
            'form.field.article-%s-%d',
            $this->getNature($value),
            $value->getCredit()
            ));
    }

    /**
     * Return a code for translation.
     *
     * @param  Article $article the article to have nature
     * @return string
     */
    private function getNature(Article $article): string
    {
        switch(substr($article->getCode(), 0,4)) {
            case 'cmds':
                return 'cmd';
            case 'OLSX':
                return 'olsx';
            case 'CRED':
            default:
                return 'credit';
        }
    }
}
