<?php

namespace Kanboard\Plugin\IncomingTask\Controller;

use Kanboard\Controller\BaseController;

/**
 * IncomingTask Controller
 *
 * @package  mailgun
 * @author   David Morlitz
 */
class IncomingTaskController extends BaseController
{
    /**
     * Handle IncomingTask webhooks
     *
     * @access public
     */
    public function receiver()
    {
        //Requests with an invalid webhook token will be rejected with an "Access denied" message
        $this->checkWebhookToken();

//Debug code
//$req_dump = print_r($_REQUEST, true);
//$fp = file_put_contents('/tmp/IncomingTask.log', $req_dump, FILE_APPEND);
//$req_dump = print_r($_POST, true);
//$fp = file_put_contents('/tmp/IncomingTask.log', $req_dump, FILE_APPEND);

        $incomingtask_subject = $this->configModel->get('incomingtask_subject');
        $incomingtask_project_id  = $this->configModel->get('incomingtask_project_id');
        $incomingtask_column_id   = $this->configModel->get('incomingtask_column_id');
        $incomingtask_swimlane_id = $this->configModel->get('incomingtask_swimlane_id');

        if ($this->configModel->get('incomingtask_subject') == "") {
           echo "ERROR: Subject field is not defined - please check your Kanboard configuration";
           exit(1);
        }

        if (intval($incomingtask_project_id) == 0) {
           echo "ERROR: Project to insert task into is not defined - please check your Kanboard configuration";
           exit(1);
        }

        if (!in_array($incomingtask_project_id, $this->projectModel->getAllIds())) {
          echo("ERROR: Project " . $incomingtask_project_id . " does not appear to exist - task insertion will FAIL");
          exit(1);
        }

        if (intval($incomingtask_column_id) == 0) {
           echo "ERROR: Column to insert task into is not defined - please check your Kanboard configuration";
           exit(1);
        }

        if ($incomingtask_project_id != $this->columnModel->getProjectId($incomingtask_column_id)) {
          echo("ERROR: Column " . $incomingtask_column_id . " is not in project " . $incomingtask_project_id . " - task insertion will FAIL");
          exit(1);
        }

        if (intval($incomingtask_swimlane_id) == 0) {
           echo "ERROR: Swimlane to insert task into is not defined - please check your Kanboard configuration";
           exit(1);
        }

        if (!array_key_exists($incomingtask_swimlane_id, $this->swimlaneModel->getList($incomingtask_project_id))) {
          echo("ERROR: Swimlane " . $incomingtask_swimlane_id . " does not appear to exist in project " . $incomingtask_project_id . " - task insertion will FAIL");
          exit(1);
        }

        if ($_REQUEST[$incomingtask_subject] == "") {
          echo("ERROR: No text was sent for the task name - ABORT");
          exit(1);
        }

        $result = $this->taskCreationModel->create(array(
                                                         'title' => $_REQUEST[$incomingtask_subject],
                                                         'project_id' => $incomingtask_project_id,
                                                         'column_id' => $incomingtask_column_id,
                                                         'swimlane_id' => $incomingtask_swimlane_id,
                                                        )
                                                  );

        if ($result > 0) {
           echo("Kanboard accepted a task titled '" . $_REQUEST[$incomingtask_subject]) . "' as task number " . $result;
        } else {
           echo("Something went wrong and Kanboard did not accept your task");
        }
    }
}
