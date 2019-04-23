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
use Doctrine\ORM\EntityRepository;

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
     *
     * @return string
     */
    public function getDefaultSortField(): string
    {
        return 'code';
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
     * Force manager to refresh data.
     */
    public function refresh(): void
    {
        self::$data = null;
    }

    /**
     * Main repository getter.
     *
     * @return EntityRepository|SettingsRepository
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
        self::$data = $this->repository->findAll();
    }
}
