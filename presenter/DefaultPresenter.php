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

  public function renderDefault($urlId) 
  {
    switch($this->httpRequest->getMethod())
    {
      case 'GET':
        if (isset($urlId)){
          $this->_getRequest($urlId);
        }
        else{
          $this->_getListRequest();
        }
        break;
      case 'POST':
        $this->httpResponse->setCode(Response::S201_CREATED);     
        break;
      case 'OPTIONS':
        $this->httpResponse->setCode(Response::S200_OK);  
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
    $this->jsonResponse->payload = array_map(function($n) { return $n->toJson(); }, $team);
    $this->httpResponse->setCode(Response::S200_OK);
  }
}
