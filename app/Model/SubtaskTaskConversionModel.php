<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Class SubtaskTaskConversionModel
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class SubtaskTaskConversionModel extends Base
{
    /**
     * Convert a subtask to a task
     *
     * @access public
     * @param  integer $project_id
     * @param  integer $subtask_id
     * @return integer
     */
    public function convertToTask($project_id, $subtask_id, $is_link=false)
    {
        $subtask = $this->subtaskModel->getById($subtask_id);
        $parent_task = $this->taskFinderModel->getById($subtask['task_id']);

        $task_id = $this->taskCreationModel->create(array(
            'project_id' => $project_id,
            'title' => $subtask['title'],
            'time_estimated' => $subtask['time_estimated'],
            'time_spent' => $subtask['time_spent'],
            'owner_id' => $subtask['user_id'],
            'description' => $parent_task['description'],
            'color_id' => $parent_task['color_id'],
            'category_id' => $parent_task['category_id'],
            'reference' => $parent_task['reference'],
        ));

        if ($task_id !== false) {
			if ($is_link) {
				$this->taskLinkModel->create($task_id, $subtask_id, 1);
				$this->taskLinkModel->create($task_id, $subtask['task_id'], 4);
				$this->taskLinkModel->create($subtask['task_id'], $task_id, 5);
			} else {
				$this->subtaskModel->remove($subtask_id);
				$this->taskLinkModel->create($task_id, $subtask['task_id'], 6);
				$this->taskLinkModel->create($subtask['task_id'], $task_id, 7);
			}
        }

        return $task_id;
    }
}
