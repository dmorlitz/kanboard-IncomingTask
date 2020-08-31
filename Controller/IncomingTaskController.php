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
     * Handle IncomingTask webhooks - all requests start here
     *
     * @access public
     */
    public function receiver()
    {
        //Requests with an invalid webhook token will be rejected with an "Access denied" message
        $this->checkWebhookToken();

//Debug code
$req_dump = print_r($_REQUEST, true);
$fp = file_put_contents('/tmp/IncomingTask.log', $req_dump, FILE_APPEND);
$req_dump = print_r($_POST, true);
$fp = file_put_contents('/tmp/IncomingTask.log', $req_dump, FILE_APPEND);

        $incomingtask_subject = $this->configModel->get('incomingtask_subject');
        $incomingtask_project_id  = $this->configModel->get('incomingtask_project_id');
        $incomingtask_column_id   = $this->configModel->get('incomingtask_column_id');
        $incomingtask_swimlane_id = $this->configModel->get('incomingtask_swimlane_id');

        // Determine if we need to send HTTP status codes or descriptive text error messages
        $send_http_error_codes = true;
        //if ( (isset($_REQUEST['response_url'])) && (strpos($_REQUEST['response_url'], "slack.com") !== false) ) { $send_http_error_codes = false; }

        if ($this->configModel->get('incomingtask_subject') == "") {
           if ($send_http_error_codes) { http_response_code(500); }
           echo "ERROR: Subject field is not defined - please check your Kanboard configuration";
           exit(1);
        }

        if (intval($incomingtask_project_id) == 0) {
           if ($send_http_error_codes) { http_response_code(500); }
           echo "ERROR: Project to insert task into is not defined - please check your Kanboard configuration";
           exit(1);
        }

        if (!in_array($incomingtask_project_id, $this->projectModel->getAllIds())) {
           if ($send_http_error_codes) { http_response_code(500); }
           echo("ERROR: Project " . $incomingtask_project_id . " does not appear to exist - task insertion will FAIL");
           exit(1);
        }

        if (intval($incomingtask_column_id) == 0) {
           if ($send_http_error_codes) { http_response_code(500); }
           echo "ERROR: Column to insert task into is not defined - please check your Kanboard configuration";
           exit(1);
        }

        if ($incomingtask_project_id != $this->columnModel->getProjectId($incomingtask_column_id)) {
           if ($send_http_error_codes) { http_response_code(500); }
           echo("ERROR: Column " . $incomingtask_column_id . " is not in project " . $incomingtask_project_id . " - task insertion will FAIL");
           exit(1);
        }

        if (intval($incomingtask_swimlane_id) == 0) {
           if ($send_http_error_codes) { http_response_code(500); }
           echo "ERROR: Swimlane to insert task into is not defined - please check your Kanboard configuration";
           exit(1);
        }

        if (!array_key_exists($incomingtask_swimlane_id, $this->swimlaneModel->getList($incomingtask_project_id))) {
           if ($send_http_error_codes) { http_response_code(500); }
           echo("ERROR: Swimlane " . $incomingtask_swimlane_id . " does not appear to exist in project " . $incomingtask_project_id . " - task insertion will FAIL");
           exit(1);
        }

        //Try to find the first text field provided that has a value
        $subject_fields = explode(",", preg_replace('/\s+/','', $incomingtask_subject));
        $found = false;
        $subject = "";
        foreach ($subject_fields as $subject_sent) {
            if ( ($_REQUEST[$subject_sent] != "") && ($found == false) ) {
                $subject = $_REQUEST[$subject_sent];
                $found = true;
            }
        }

        if ($found == false) {
           if ($send_http_error_codes) { http_response_code(500); }
           echo "ERROR: You asked to look in the fields named " . implode(",", $subject_fields) . " but none of these were found in the data sent";
           exit(1);
        }

        if ($subject == "") {
           if ($send_http_error_codes) { http_response_code(500); }
           echo("ERROR: No text was sent for the task name - ABORT");
           exit(1);
        }

	$fullrequest = print_r($_REQUEST, true);

        if (isset($_REQUEST['body-plain'])) {
		$description = $_REQUEST['body-plain'] . "\n\n--------------\n\n" . $fullrequest;
	} else {
		$description = $fullrequest;
	}

        $result = $this->taskCreationModel->create(array(
                                                         'title' => $subject,
                                                         'project_id' => $incomingtask_project_id,
                                                         'column_id' => $incomingtask_column_id,
                                                         'swimlane_id' => $incomingtask_swimlane_id,
							 'description' => $description,
                                                        )
                                                  );

        if ($result > 0) {
           if ($send_http_error_codes) { http_response_code(200); }
           echo("Kanboard accepted a task titled '" . $subject . "' as task number " . $result);
        } else {
           if ($send_http_error_codes) { http_response_code(500); }
           echo("Something went wrong and Kanboard did not accept your task");
        }
    }
}
