<?php

namespace Absolute\Module\Team\Presenter;

use Nette\Http\Response;
use Nette\Application\Responses\JsonResponse;

class DefaultPresenter extends TeamBasePresenter
{

    /** @var \Absolute\Module\Team\Manager\TeamManager @inject */
    public $teamManager;

    /** @var \Absolute\Module\Team\Manager\TeamCRUDManager @inject */
    public $teamCRUDManager;

    public function startup()
    {
        parent::startup();
    }

    public function renderDefault($resourceId)
    {
        switch ($this->httpRequest->getMethod())
        {
            case 'GET':
                if (isset($resourceId))
                {
                    $this->_getRequest($resourceId);
                }
                else
                {
                    $this->_getListRequest();
                }
                break;
            case 'POST':
                $this->_postRequest();
                break;
            case 'POST':
                $this->_postRequest();
                break;
            case 'PUT':
                $this->_putRequest($resourceId);
                break;
            case 'DELETE':
                $this->_deleteRequest($resourceId);
                break;
            default:
                break;
        }
        $this->sendResponse(new JsonResponse(
                $this->jsonResponse->toJson(), "application/json;charset=utf-8"
        ));
    }

    private function _getRequest($id)
    {
        $team = $this->teamManager->getById($id);
        if (!$team)
        {
            $this->httpResponse->setCode(Response::S404_NOT_FOUND);
            return;
        }
        $this->jsonResponse->payload = $team->toJson();
        $this->httpResponse->setCode(Response::S200_OK);
    }

    private function _getListRequest()
    {
        $team = $this->teamManager->getList($this->user->id);
        $this->jsonResponse->payload = array_map(function($n)
        {
            return $n->toJson();
        }, $team);
        $this->httpResponse->setCode(Response::S200_OK);
    }

    private function _postRequest()
    {
        $post = json_decode($this->httpRequest->getRawBody(), true);
        if (!isset($post["name"]) || !isset($post["image"]))
        {
            $this->httpResponse->setCode(Response::S400_BAD_REQUEST);
            return;
        }

        $id = $this->teamCRUDManager->create($post["name"], $post["image"]);
        if (!$id)
            $this->httpResponse->setCode(Response::S500_INTERNAL_SERVER_ERROR);
        else
        {

            if (isset($post['events']))
                $this->teamCRUDManager->connectEvents($post['events'], $id);
            if (isset($post['menus']))
                $this->teamCRUDManager->connectMenus($post['menus'], $id);
            if (isset($post['notes']))
                $this->teamCRUDManager->connectNotes($post['notes'], $id);
            if (isset($post['pages']))
                $this->teamCRUDManager->connectPages($post['pages'], $id);
            if (isset($post['project']))
                $this->teamCRUDManager->connectProjects($post['project'], $id);
            if (isset($post['users']))
                $this->teamCRUDManager->connectUsers($post['users'], $id);
            if (isset($post['todos']))
                $this->teamCRUDManager->connectTodos($post['todos'], $id);

            $this->httpResponse->setCode(Response::S201_CREATED);
        }
    }

    private function _putRequest($id)
    {
        if (!isset($id))
        {
            $this->httpResponse->setCode(Response::S400_BAD_REQUEST);
            return;
        }
        $post = json_decode($this->httpRequest->getRawBody(), true);

        $ret = $this->teamCRUDManager->update($id, $post);
        if (!$ret)
            $this->httpResponse->setCode(Response::S500_INTERNAL_SERVER_ERROR);
        else
            $this->httpResponse->setCode(Response::S200_OK);
    }

    private function _deleteRequest($id)
    {
        if (!isset($id))
        {
            $this->httpResponse->setCode(Response::S400_BAD_REQUEST);
            return;
        }

        $ret = $this->teamCRUDManager->delete($id);
        if (!$ret)
            $this->httpResponse->setCode(Response::S500_INTERNAL_SERVER_ERROR);
        else
            $this->httpResponse->setCode(Response::S200_OK);
    }

}
