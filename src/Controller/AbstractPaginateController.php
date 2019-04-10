<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractPaginateController extends AbstractController
{
    /**
     * Validate the ordered fields.
     * Redirect user to the default order when invalid fields are provided.
     *
     * @param Request $request        the request and fields
     * @param array   $acceptedFields array of acceptedFields
     * @param string  $defaultField   default order field
     */
    protected function validateSortedFields(Request $request, array $acceptedFields, string $defaultField): void
    {
        $field = $request->query->getAlpha('sort', $defaultField);

        if (in_array($field, $acceptedFields)) {
            return;
        }

        $url = '';
        dd($request, $url);

        //Field is not valid. Paginator will generate a 500 error. To avoid it, I redirect user
        $this->redirect($url, Response::HTTP_MOVED_PERMANENTLY);

    }
}