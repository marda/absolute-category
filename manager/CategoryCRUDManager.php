<?php

namespace Absolute\Module\Category\Manager;

use Absolute\Core\Manager\BaseManager;
use \Nette\Database\Context;
use Absolute\Module\File\Manager\FileCRUDManager;

class CategoryCRUDManager extends BaseManager
{

    private $fileCRUDManager;

    public function __construct(Context $database, FileCRUDManager $fileCRUDManager)
    {
        parent::__construct($database);
        $this->fileCRUDManager = $fileCRUDManager;
    }

    // OTHER METHODS
    // CONNECT METHODS

    public function connectEvents($events, $categoryId)
    {
        $events = array_unique(array_filter($events));
        // DELETE
        $this->database->table('event_category')->where('category_id', $categoryId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($events as $event)
        {
            $data[] = [
                "event_id" => $event,
                "category_id" => $categoryId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("event_category")->insert($data);
        }
        return true;
    }

    public function connectMenus($menus, $categoryId)
    {
        $menus = array_unique(array_filter($menus));
        // DELETE
        $this->database->table('menu_category')->where('category_id', $categoryId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($menus as $menu)
        {
            $data[] = [
                "menu_id" => $menu,
                "category_id" => $categoryId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("menu_category")->insert($data);
        }
        return true;
    }

    public function connectNotes($notes, $categoryId)
    {
        $notes = array_unique(array_filter($notes));
        // DELETE
        $this->database->table('note_category')->where('category_id', $categoryId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($notes as $note)
        {
            $data[] = [
                "note_id" => $note,
                "category_id" => $categoryId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("note_category")->insert($data);
        }
        return true;
    }

    public function connectPages($pages, $categoryId)
    {
        $pages = array_unique(array_filter($pages));
        // DELETE
        $this->database->table('page_category')->where('category_id', $categoryId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($pages as $page)
        {
            $data[] = [
                "page_id" => $page,
                "category_id" => $categoryId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("page_category")->insert($data);
        }
        return true;
    }

    public function connectProjects($projects, $categoryId)
    {
        $projects = array_unique(array_filter($projects));
        // DELETE
        $this->database->table('project_category')->where('category_id', $categoryId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($projects as $project)
        {
            $data[] = [
                "project_id" => $project,
                "category_id" => $categoryId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("project_category")->insert($data);
        }
        return true;
    }

    public function connectTodos($todos, $categoryId)
    {
        $todos = array_unique(array_filter($todos));
        // DELETE
        $this->database->table('todo_category')->where('category_id', $categoryId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($todos as $todo)
        {
            $data[] = [
                "todo_id" => $todo,
                "category_id" => $categoryId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("todo_category")->insert($data);
        }
        return true;
    }

    public function connectUsers($users, $categoryId)
    {
        $users = array_unique(array_filter($users));
        // DELETE
        $this->database->table('category_user')->where('category_id', $categoryId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($users as $user)
        {
            $data[] = [
                "user_id" => $user,
                "category_id" => $categoryId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("category_user")->insert($data);
        }
        return true;
    }
    // CUD METHODS

    public function create($userId, $name, $default, $image)
    {
        if (isset($image))
        {
            $fileId = $this->fileCRUDManager->createFromBase64($image, "", "/images/categories/");
            $fileId = (!$fileId) ? null : $fileId;
        }
        else
        {
            $fileId = null;
        }
        $result = $this->database->table('category')->insert(array(
            'name' => $name,
            'default' => $default,
            'created' => new \DateTime(),
            'file_id' => $fileId,
        ));
        return $result;
    }

    public function delete($id)
    {
        $db = $this->database->table('category')->get($id);
        if (!$db)
        {
            return false;
        }
        if ($db->file_id)
        {
            $this->fileCRUDManager->delete($db->file_id);
        }
        $this->database->table('event_category')->where('category_id', $id)->delete();
        $this->database->table('menu_category')->where('category_id', $id)->delete();
        $this->database->table('note_category')->where('category_id', $id)->delete();
        $this->database->table('page_category')->where('category_id', $id)->delete();
        $this->database->table('project_category')->where('category_id', $id)->delete();
        $this->database->table('category_user')->where('category_id', $id)->delete();
        $this->database->table('todo_category')->where('category_id', $id)->delete();
        return $this->database->table('category')->where('id', $id)->delete();
    }

    public function update($id, $post)
    {

        if (isset($post["image"]))
        {
            $fileId = $this->fileCRUDManager->createFromBase64($post["image"], "", "/images/categories/");
            $fileId = (!$fileId) ? null : $fileId;
        }
        else
        {
            $fileId = null;
        }
        if ($fileId != null)
            $post["file_id"] = $fileId;
        
        if(isset($post['events']))
            $this->connectEvents($post['events'], $id);
        if(isset($post['menus']))
            $this->connectMenus($post['menus'], $id);
        if(isset($post['notes']))
            $this->connectNotes($post['notes'], $id);
        if(isset($post['pages']))
            $this->connectPages($post['pages'], $id);
        if(isset($post['project']))
            $this->connectProjects($post['project'], $id);
        if(isset($post['users']))
            $this->connectUsers($post['users'], $id);
        if(isset($post['todos']))
            $this->connectTodos($post['todos'], $id);
        
        unset($post['id']);
        unset($post['image']);
        
        unset($post['users']);
        unset($post['events']);
        unset($post['menus']);
        unset($post['notes']);
        unset($post['pages']);
        unset($post['project']);
        unset($post['todos']);

        return $this->database->table('category')->where('id', $id)->update($post);
    }

}
