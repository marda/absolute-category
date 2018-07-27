<?php

namespace Absolute\Module\Category\Manager;

use Absolute\Core\Manager\BaseManager;
use Absolute\Module\Category\Entity\Category;
use Nette\Database\Context;
use Absolute\Module\File\Manager\FileManager;

class CategoryManager extends BaseManager
{
    private $fileManager;
    public function __construct(Context $database,FileManager $fileManager)
    {
        parent::__construct($database);
        $this->fileManager=$fileManager;
    }

    /* INTERNAL/EXTERNAL INTERFACE */
    public function getCategory($db){
        return $this->_getCategory($db);
    }

    protected function _getCategory($db)
    {
        if ($db == false)
        {
            return false;
        }
        $object = new Category($db->id, $db->name, $db->default, $db->created);
        if ($db->ref('file'))
        {
            $object->setImage($this->fileManager->_getFile($db->ref('file')));
        }
        return $object;
    }

    public function _getById($id)
    {
        $resultDb = $this->database->table('category')->get($id);
        return $this->_getCategory($resultDb);
    }

    private function _getList()
    {
        $ret = array();
        $resultDb = $this->database->table('category');
        foreach ($resultDb as $db)
        {
            $object = $this->_getCategory($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getListWithUsers()
    {
        $ret = array();
        $resultDb = $this->database->table('category');
        foreach ($resultDb as $db)
        {
            $object = $this->_getCategory($db);
            foreach ($db->related('category_user') as $userDb)
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

    private function _getListDefault()
    {
        $ret = array();
        $resultDb = $this->database->table('category')->where('default', true);
        foreach ($resultDb as $db)
        {
            $object = $this->_getCategory($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getSearch($search)
    {
        $ret = array();
        $resultDb = $this->database->table('category')->where('name REGEXP ?', $search);
        foreach ($resultDb as $db)
        {
            $object = $this->_getCategory($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getTodoList($todoId)
    {
        $ret = array();
        $resultDb = $this->database->table('category')->where(':todo_category.todo_id', $todoId);
        foreach ($resultDb as $db)
        {
            $object = $this->_getCategory($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getTodoItem($todoId, $categoryId)
    {
        return $this->_getCategory($this->database->table('category')->where(':todo_category.todo_id', $todoId)->where("category_id", $categoryId)->fetch());
    }

    public function _categoryTodoDelete($todoId, $categoryId)
    {
        return $this->database->table('todo_category')->where('todo_id', $todoId)->where('category_id', $categoryId)->delete();
    }

    public function _categoryTodoCreate($todoId, $categoryId)
    {
        return $this->database->table('todo_category')->insert(['todo_id' => $todoId, 'category_id' => $categoryId]);
    }

    private function _getEventList($eventId)
    {
        $ret = array();
        $resultDb = $this->database->table('category')->where(':event_category.event_id', $eventId);
        foreach ($resultDb as $db)
        {
            $object = $this->_getCategory($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getEventItem($eventId, $categoryId)
    {
        return $this->_getCategory($this->database->table('category')->where(':event_category.event_id', $eventId)->where("category_id", $categoryId)->fetch());
    }

    public function _categoryEventDelete($eventId, $categoryId)
    {
        return $this->database->table('event_category')->where('event_id', $eventId)->where('category_id', $categoryId)->delete();
    }

    public function _categoryEventCreate($eventId, $categoryId)
    {
        return $this->database->table('event_category')->insert(['event_id' => $eventId, 'category_id' => $categoryId]);
    }

    public function getEventList($eventId)
    {
        return $this->_getEventList($eventId);
    }

    public function getEventItem($eventId, $categoryId)
    {
        return $this->_getEventItem($eventId, $categoryId);
    }

    public function categoryEventDelete($eventId, $categoryId)
    {
        return $this->_categoryEventDelete($eventId, $categoryId);
    }

    public function categoryEventCreate($eventId, $categoryId)
    {
        return $this->_categoryEventCreate($eventId, $categoryId);
    }

    public function getTodoList($todoId)
    {
        return $this->_getTodoList($todoId);
    }

    public function getTodoItem($todoId, $categoryId)
    {
        return $this->_getTodoItem($todoId, $categoryId);
    }

    public function categoryTodoDelete($todoId, $categoryId)
    {
        return $this->_categoryTodoDelete($todoId, $categoryId);
    }

    public function categoryTodoCreate($todoId, $categoryId)
    {
        return $this->_categoryTodoCreate($todoId, $categoryId);
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

    public function getListDefault()
    {
        return $this->_getListDefault();
    }

    public function getSearch($search)
    {
        return $this->_getSearch($search);
    }

}
