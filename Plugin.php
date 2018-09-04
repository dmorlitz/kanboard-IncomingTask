<?php

namespace Kanboard\Plugin\IncomingTask;

use Kanboard\Core\Security\Role;
use Kanboard\Core\Translator;
use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Security\Authorization;
use Kanboard\Core\Security\AccessMap;
use Kanboard\Helper\Url;

/**
 * IncomingTask Plugin
 *
 * @package  IncomingTask
 * @author   David Morlitz
 */
class Plugin extends Base
{
    public function initialize()
    {
        //$this->emailClient->setTransport('mailgun', '\Kanboard\Plugin\Mailgun\EmailHandler');
        $this->template->hook->attach('template:config:integrations', 'IncomingTask:config/integration');
        $this->route->addRoute('/incomingtask/handler', 'IncomingTaskController', 'receiver', 'IncomingTask');
        $this->applicationAccessMap->add('IncomingTaskController', 'receiver', Role::APP_PUBLIC);
    }

    public function getPluginDescription()
    {
        return 'IncomingTask Web Integration';
    }

    public function getPluginAuthor()
    {
        return 'David Morlitz';
    }

    public function getPluginVersion()
    {
        return '0.0.1';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/dmorlitz/URL_NEEDED';
    }

    public function getCompatibleVersion()
    {
        return '>=1.2.5';
    }
}
