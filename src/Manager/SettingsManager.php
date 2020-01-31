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

use App\Entity\EntityInterface;
use App\Entity\Settings;
use App\Exception\SettingsException;
use App\Repository\SettingsRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Settings manager class.
 *
 * Read all table when necessary and provide data.
 */
class SettingsManager extends AbstractRepositoryManager implements ManagerInterface
{
    /**
     * Cached data.
     *
     * @var Settings[]
     */
    private static $data;

    /**
     * Return default alias.
     */
    public function getDefaultAlias(): string
    {
        return 's';
    }

    /**
     * Get the default field for ordering data.
     */
    public function getDefaultSortField(): string
    {
        return 'code';
    }

    /**
     * Get a setting or throw an exception.
     *
     * @param string $code the code of setting
     *
     * @throws SettingsException when code does not exist
     */
    public function getSetting(string $code): Settings
    {
        /** @var ?Settings $setting */
        $setting = $this->repository->findOneByCode($code);

        if (!$setting instanceof Settings) {
            throw new SettingsException("{$code} is not a code set in settings repository.");
        }

        return $setting;
    }

    /**
     * Retrieve value for code provided.
     *
     * @param string $code settings code
     *
     * @throws SettingsException when settings code does not exists
     *
     * @return mixed|null
     */
    public function getValue(string $code)
    {
        if (null === self::$data) {
            $this->initialize();
        }

        if (array_key_exists($code, self::$data)) {
            return self::$data[$code]->getValue();
        }

        throw new SettingsException("{$code} is not a code set in settings repository.");
    }

    /**
     * Is this entity deletable?
     *
     * @param EntityInterface|Settings $entity entity to test
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(EntityInterface $entity): bool
    {
        return false;
    }

    /**
     * Paginate updatable settings.
     *
     * @param int    $page      number of page
     * @param int    $limit     limit of bills per page
     * @param string $sortField sort field
     * @param string $sortOrder sort order
     *
     * @throws QueryException when criteria is not valid
     */
    public function paginateUpdatable(int $page, int $limit, string $sortField, string $sortOrder): PaginationInterface
    {
        $criteria = Criteria::create();
        $expression = $criteria::expr()->eq('updatable', true);
        $criteria->where($expression);

        return $this->paginateWithCriteria($criteria, $page, $limit, $sortField, $sortOrder);
    }

    /**
     * Force manager to refresh data.
     */
    public function refresh(): void
    {
        self::$data = null;
    }

    /**
     * This method will add the HIDDEN field, the sortable field.
     *
     * @see https://github.com/KnpLabs/KnpPaginatorBundle/issues/196
     *
     * @param QueryBuilder $queryBuilder Query builder
     */
    protected function addHiddenField(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder->addSelect('s.code as HIDDEN code');
    }

    /**
     * Main repository getter.
     *
     * @return SettingsRepository|EntityRepository|ObjectRepository
     */
    protected function getMainRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Settings::class);
    }

    /**
     * Initialize data.
     */
    private function initialize(): void
    {
        self::$data = [];
        foreach ($this->repository->findAll() as $setting) {
            /* @var Settings $setting the repository is returning an array of settings */
            self::$data[$setting->getCode()] = $setting;
        }
    }
}
