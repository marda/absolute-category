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

  // CUD METHODS

	public function create($name, $default, $image) 
	{
		if ($image instanceof \Nette\Http\FileUpload && $image->getName())
		{
			$fileId = $this->fileCRUDManager->createFromUpload($image, $image->getSanitizedName(), "/images/categories/");
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

	public function update($id, $name, $default, $image) 
	{
		$db = $this->database->table('category')->get($id);
		if (!$db)
		{
			return false;
		}
		if ($image instanceof \Nette\Http\FileUpload && $image->getName())
		{
			$fileId = $this->fileCRUDManager->createFromUpload($image, $image->getSanitizedName(), "/images/categories/");
			$fileId = (!$fileId) ? null : $fileId;
			if ($db->file_id)
			{
				$this->fileCRUDManager->delete($db->file_id);
			}
		}
		else
		{
			$fileId = $db->file_id;
		} 
 		return $this->database->table('category')->where('id', $id)->update(array(
			'name' => $name,
			'default' => $default,
			'file_id' => $fileId,
		));
	}
}

