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

}
