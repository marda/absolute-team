<?php

namespace Absolute\Module\Team\Manager;

use Nette\Database\Context;
use Absolute\Core\Manager\BaseManager;
use Absolute\Module\Team\Entity\Team;
use Absolute\Module\File\Manager\FileManager;

class TeamManager extends BaseManager
{

    public function __construct(Context $database, FileManager $fileManager)
    {
        parent::__construct($database);
        $this->fileManager = $fileManager;
    }

    /* DB TO ENTITY */

    public function _getTeam($db)
    {
        if ($db == false)
        {
            return false;
        }
        $object = new Team($db->id, $db->name, $db->created);
        if ($db->ref('file'))
        {
            $object->setImage($this->fileManager->_getFile($db->ref('file')));
        }
        return $object;
    }

    public function getTeam($db)
    {
        return $this->_getTeam($db);
    }

    /* INTERNAL/EXTERNAL INTERFACE */

    public function _getById($id)
    {
        $resultDb = $this->database->table('team')->get($id);
        return $this->_getTeam($resultDb);
    }

    private function _getList()
    {
        $ret = array();
        $resultDb = $this->database->table('team');
        foreach ($resultDb as $db)
        {
            $object = $this->_getTeam($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getListWithUsers()
    {
        $ret = array();
        $resultDb = $this->database->table('team');
        foreach ($resultDb as $db)
        {
            $object = $this->_getTeam($db);
            foreach ($db->related('team_user') as $userDb)
            {
                $user = $this->_getUser($userDb->user);
                if ($user)
                {
                    $object->addUser($user);
                }
            }
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getSearch($search)
    {
        $ret = array();
        $resultDb = $this->database->table('team')->where('name REGEXP ?', $search);
        foreach ($resultDb as $db)
        {
            $object = $this->_getTeam($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getTodoList($todoId)
    {
        $ret = array();
        $resultDb = $this->database->table('team')->where(':todo_team.todo_id', $todoId);
        foreach ($resultDb as $db)
        {
            $object = $this->_getTeam($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getTodoItem($todoId, $teamId)
    {
        return $this->_getTeam($this->database->table('team')->where(':todo_team.todo_id', $todoId)->where("team_id", $teamId)->fetch());
    }

    public function _teamTodoDelete($todoId, $teamId)
    {
        return $this->database->table('todo_team')->where('todo_id', $todoId)->where('team_id', $teamId)->delete();
    }

    public function _teamTodoCreate($todoId, $teamId)
    {
        return $this->database->table('todo_team')->insert(['todo_id' => $todoId, 'team_id' => $teamId]);
    }

    

    private function _getEventList($eventId)
    {
        $ret = array();
        $resultDb = $this->database->table('team')->where(':event_team.event_id', $eventId);
        foreach ($resultDb as $db)
        {
            $object = $this->_getTeam($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getEventItem($eventId, $teamId)
    {
        return $this->_getTeam($this->database->table('team')->where(':event_team.event_id', $eventId)->where("team_id", $teamId)->fetch());
    }

    public function _teamEventDelete($eventId, $teamId)
    {
        return $this->database->table('event_team')->where('event_id', $eventId)->where('team_id', $teamId)->delete();
    }

    public function _teamEventCreate($eventId, $teamId)
    {
        return $this->database->table('event_team')->insert(['event_id' => $eventId, 'team_id' => $teamId]);
    }

    private function _getMenuList($menuId)
    {
        $ret = array();
        $resultDb = $this->database->table('team')->where(':menu_team.menu_id', $menuId);
        foreach ($resultDb as $db)
        {
            $object = $this->_getTeam($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getMenuItem($menuId, $teamId)
    {
        return $this->_getTeam($this->database->table('team')->where(':menu_team.menu_id', $menuId)->where("team_id", $teamId)->fetch());
    }

    public function _teamMenuDelete($menuId, $teamId)
    {
        return $this->database->table('menu_team')->where('menu_id', $menuId)->where('team_id', $teamId)->delete();
    }

    public function _teamMenuCreate($menuId, $teamId)
    {
        return $this->database->table('menu_team')->insert(['menu_id' => $menuId, 'team_id' => $teamId]);
    }

    private function _getPageList($pageId)
    {
        $ret = array();
        $resultDb = $this->database->table('team')->where(':page_team.page_id', $pageId);
        foreach ($resultDb as $db)
        {
            $object = $this->_getTeam($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getPageItem($pageId, $teamId)
    {
        return $this->_getTeam($this->database->table('team')->where(':page_team.page_id', $pageId)->where("team_id", $teamId)->fetch());
    }

    public function _teamPageDelete($pageId, $teamId)
    {
        return $this->database->table('page_team')->where('page_id', $pageId)->where('team_id', $teamId)->delete();
    }

    public function _teamPageCreate($pageId, $teamId)
    {
        return $this->database->table('page_team')->insert(['page_id' => $pageId, 'team_id' => $teamId]);
    }

    private function _getProjectList($projectId)
    {
        $ret = array();
        $resultDb = $this->database->table('team')->where(':project_team.project_id', $projectId);
        foreach ($resultDb as $db)
        {
            $object = $this->_getTeam($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getProjectItem($projectId, $teamId)
    {
        return $this->_getTeam($this->database->table('team')->where(':project_team.project_id', $projectId)->where("team_id", $teamId)->fetch());
    }

    public function _teamProjectDelete($projectId, $teamId)
    {
        return $this->database->table('project_team')->where('project_id', $projectId)->where('team_id', $teamId)->delete();
    }

    public function _teamProjectCreate($projectId, $teamId)
    {
        return $this->database->table('project_team')->insert(['project_id' => $projectId, 'team_id' => $teamId]);
    }

    public function getProjectList($projectId)
    {
        return $this->_getProjectList($projectId);
    }

    public function getProjectItem($projectId, $teamId)
    {
        return $this->_getProjectItem($projectId, $teamId);
    }

    public function teamProjectDelete($projectId, $teamId)
    {
        return $this->_teamProjectDelete($projectId, $teamId);
    }

    public function teamProjectCreate($projectId, $teamId)
    {
        return $this->_teamProjectCreate($projectId, $teamId);
    }

    /* EXTERNAL METHOD */

    public function getById($id)
    {
        return $this->_getById($id);
    }

    public function getList()
    {
        return $this->_getList();
    }

    public function getListWithUsers()
    {
        return $this->_getListWithUsers();
    }

    public function getSearch($search)
    {
        return $this->_getSearch($search);
    }

    public function getTodoList($todoId)
    {
        return $this->_getTodoList($todoId);
    }

    public function getTodoItem($todoId, $teamId)
    {
        return $this->_getTodoItem($todoId, $teamId);
    }

    public function teamTodoDelete($todoId, $teamId)
    {
        return $this->_teamTodoDelete($todoId, $teamId);
    }

    public function teamTodoCreate($todoId, $teamId)
    {
        return $this->_teamTodoCreate($todoId, $teamId);
    }

    public function getEventList($eventId)
    {
        return $this->_getEventList($eventId);
    }

    public function getEventItem($eventId, $teamId)
    {
        return $this->_getEventItem($eventId, $teamId);
    }

    public function teamEventDelete($eventId, $teamId)
    {
        return $this->_teamEventDelete($eventId, $teamId);
    }

    public function teamEventCreate($eventId, $teamId)
    {
        return $this->_teamEventCreate($eventId, $teamId);
    }

    public function getMenuList($menuId)
    {
        return $this->_getMenuList($menuId);
    }

    public function getMenuItem($menuId, $teamId)
    {
        return $this->_getMenuItem($menuId, $teamId);
    }

    public function teamMenuDelete($menuId, $teamId)
    {
        return $this->_teamMenuDelete($menuId, $teamId);
    }

    public function teamMenuCreate($menuId, $teamId)
    {
        return $this->_teamMenuCreate($menuId, $teamId);
    }

    public function getPageList($pageId)
    {
        return $this->_getPageList($pageId);
    }

    public function getPageItem($pageId, $teamId)
    {
        return $this->_getPageItem($pageId, $teamId);
    }

    public function teamPageDelete($pageId, $teamId)
    {
        return $this->_teamPageDelete($pageId, $teamId);
    }

    public function teamPageCreate($pageId, $teamId)
    {
        return $this->_teamPageCreate($pageId, $teamId);
    }

}
