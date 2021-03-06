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

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * This controller improved pagination.
 */
abstract class AbstractPaginateController extends AbstractController
{
    /**
     * Get sort order from request.
     *
     * @param Request $request      the request containing order
     * @param string  $defaultOrder the default order if request have no one
     */
    protected function getOrder(Request $request, string $defaultOrder = 'desc'): string
    {
        $order = $request->query->getAlpha('direction', $defaultOrder);

        return 'desc' === strtolower($order) ? 'desc' : 'asc';
    }

    /**
     * Get the sorter field from request.
     *
     * @param Request $request      the request containing sorter field
     * @param string  $defaultField the default field if request have no one
     */
    protected function getSortedField(Request $request, string $defaultField): string
    {
        return $request->query->getAlpha('sort', $defaultField);
    }

    /**
     * Validate the ordered fields.
     * Redirect user to the default order when invalid fields are provided.
     *
     * @param Request $request        the request with sorting fields
     * @param array   $acceptedFields array of acceptedFields
     */
    protected function validateSortedField(Request $request, array $acceptedFields): bool
    {
        $field = $request->query->getAlpha('sort');

        return empty($field) || in_array($field, $acceptedFields, true);
    }
}
