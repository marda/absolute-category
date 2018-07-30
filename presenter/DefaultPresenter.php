<?php

namespace Absolute\Module\Category\Presenter;

use Nette\Http\Response;
use Nette\Application\Responses\JsonResponse;
use Absolute\Module\Category\Presenter\CategoryBasePresenter;

class DefaultPresenter extends CategoryBasePresenter
{

    /** @var \Absolute\Module\Category\Manager\CategoryCRUDManager @inject */
    public $categoryCRUDManager;

    /** @var \Absolute\Module\Category\Manager\CategoryManager @inject */
    public $categoryManager;

    public function startup()
    {
        parent::startup();
    }

    public function renderDefault($resourceId)
    {
        switch ($this->httpRequest->getMethod())
        {
            case 'GET':
                if ($resourceId != null)
                    $this->_getRequest($resourceId);
                else
                    $this->_getListRequest($this->getParameter('offset'), $this->getParameter('limit'));
                break;
            case 'POST':
                $this->_postRequest($resourceId);
                break;
            case 'PUT':
                $this->_putRequest($resourceId);
                break;
            case 'DELETE':
                $this->_deleteRequest($resourceId);
            default:

                break;
        }
        $this->sendResponse(new JsonResponse(
                $this->jsonResponse->toJson(), "application/json;charset=utf-8"
        ));
    }

    private function _getRequest($id)
    {
        $category = $this->categoryManager->getById($id);
        if (!$category)
        {
            $this->httpResponse->setCode(Response::S404_NOT_FOUND);
            return;
        }
        $this->jsonResponse->payload = $category->toJson();
        $this->httpResponse->setCode(Response::S200_OK);
    }

    private function _getListRequest($offset, $limit)
    {
        $categorys = $this->categoryManager->getList($this->user->id, $offset, $limit);
        $this->httpResponse->setCode(Response::S200_OK);

        $this->jsonResponse->payload = array_map(function($n)
        {
            return $n->toJson();
        }, $categorys);
    }

    private function _putRequest($id)
    {
        $post = json_decode($this->httpRequest->getRawBody(),true);
        $this->jsonResponse->payload = [];
        $this->categoryCRUDManager->update($id, $post);
    }

    private function _postRequest($urlId)
    {
        $post = json_decode($this->httpRequest->getRawBody());
        $ret = $this->categoryCRUDManager->create($this->user->id, $post->name, $post->default, $post->image);
        if (!$ret)
        {
            $this->jsonResponse->payload = [];
            $this->httpResponse->setCode(Response::S500_INTERNAL_SERVER_ERROR);
        }
        else
        {
            $this->jsonResponse->payload = [];
            $this->httpResponse->setCode(Response::S201_CREATED);
        }
    }

    private function _deleteRequest($id)
    {
        $this->categoryCRUDManager->delete($id);
        $this->httpResponse->setCode(Response::S200_OK);
    }

}
