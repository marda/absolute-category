<?php

namespace Absolute\Module\Category\Presenter;

use Nette\Http\Response;
use Nette\Application\Responses\JsonResponse;
use Absolute\Core\Presenter\BaseRestPresenter;

class CategoryBasePresenter extends BaseRestPresenter
{

    public function startup()
    {
        parent::startup();
        if (!$this->user->isAllowed('backend'))
        {
            $this->jsonResponse->payload = (['message' => 'Unauthorized!']);
            $this->httpResponse->setCode(Response::S401_UNAUTHORIZED);
        }
    }

    // CONTROL
    // HANDLERS
    // SUBMITS
    // VALIDATION
    // COMPONENTS
}
