<h3><img src="<?= $this->url->dir() ?>plugins/Mailgun/mailgun-icon.png"/>&nbsp;Incoming Task</h3>
<div class="panel">

    <?= $this->form->label(t('Protocol://Hostname:port to use for incoming task webhook (NO trailing slash)'), 'incomingtask_host_port') ?>
    <?= $this->form->text('incomingtask_host_port', $values) ?>

    <?= $this->form->label(t('Field to use for task subject (must exist in the incoming JSON request)'), 'incomingtask_subject') ?>
    <?= $this->form->text('incomingtask_subject', $values) ?>

    <p class="alert"><?= t("Warning: You need to click save every time you change a drop-down for the other ones to populate with valid date - sorry I am still working on this.") ?></p>

    <?= $this->form->label(t('Project ID for task creation'), 'incomingtask_project_id') ?>
    <?= $this->form->select('incomingtask_project_id', $this->app->projectModel->getList(),array('incomingtask_project_id'=>$values['incomingtask_project_id'])) ?>
    <!--<?= $this->form->text('incomingtask_project_id', $values) ?>-->
    <?php if (!in_array($values['incomingtask_project_id'], $this->app->projectModel->getAllIds())) { ?>
           <p class="alert-error"><?= t("ERROR: Project " . $values['incomingtask_project_id'] . " does not appear to exist - task insertion will FAIL") ?></p>
    <?php } ?>

    <?= $this->form->label(t('Column ID for task creation'), 'incomingtask_column_id') ?>
    <?= $this->form->select('incomingtask_column_id', $this->app->columnModel->getList($values['incomingtask_project_id'])) ?>
    <!--<?= $this->form->text('incomingtask_column_id', $values) ?>-->
    <?php if ($values['incomingtask_project_id'] != $this->app->columnModel->getProjectId($values['incomingtask_column_id'])) { ?>
           <p class="alert-error"><?= t("ERROR: Column " . $values['incomingtask_column_id'] . " is not in project " . $values['incomingtask_project_id'] . " - task insertion will FAIL") ?></p>
    <?php } ?>

    <?= $this->form->label(t('Swimlane ID for task creation'), 'incomingtask_swimlane_id') ?>
    <?= $this->form->select('incomingtask_swimlane_id', $this->app->swimlaneModel->getList($values['incomingtask_project_id'])) ?>
    <!--<?= $this->form->text('incomingtask_swimlane_id', $values) ?>-->
    <?php if (!array_key_exists($values['incomingtask_swimlane_id'], $this->app->swimlaneModel->getList($values['incomingtask_project_id']))) { ?>
           <p class="alert-error"><?= t("ERROR: Swimlane " . $values['incomingtask_swimlane_id'] . " does not appear to exist in project " . $values['incomingtask_project_id'] . " - task insertion will FAIL") ?></p>
    <?php } ?>

    <?= $this->form->label(t('URL to provide to remote service:'), 'incomingtask_column_id') ?>
    <div class="panel">
        <?php
            //if (strpos( $this->url->href('IncomingTaskController', 'receiver', array('plugin' => 'IncomingTask')) , "?") !== false)
            if (strpos( $this->url->href('IncomingTaskController', 'receiver', array('plugin' => 'IncomingTask')) , "?") !== false)
            {
               //There is already a ? in the URL - so append token with a &;
            ?>
               <?= $this->text->e($values['incomingtask_host_port'] . $this->url->href('IncomingTaskController', 'receiver', array('plugin' => 'IncomingTask')) . '&token=' . $values['webhook_token'] ) ?>
            <?php
            } else {
            ?>
               <?= $this->text->e($values['incomingtask_host_port'] . $this->url->href('IncomingTaskController', 'receiver', array('plugin' => 'IncomingTask')) . '?token=' . $values['webhook_token'] ) ?>
            <?php
            }
        ?>
    </div>

    <?= $this->form->label(t('Debug info:'), 'incomingtask_column_id') ?>
    <div class="panel">
        (Project ID=<?= $values['incomingtask_project_id']?>) (Column ID=<?= $values['incomingtask_column_id']?>) (Swimlane ID=<?= $values['incomingtask_swimlane_id']?>)
    </div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue">
    </div>

    <p class="alert"><?= t("NOTE: This plugin uses the Webhook token defined for Kanboard - if you reset that token this one will also change.") ?></p>
</div>

