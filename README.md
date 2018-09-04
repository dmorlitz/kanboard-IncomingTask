Incoming Task Creation
======================

This plugin enables a webhook for remote task insertion into Kanboard.  I use this plugin to enable Slack integration for task creation.

Once this plugin is installed, you will see a new configuration panel on the Integration tab of your system settings.  On this page you can set the following variables:
1) The base URL to send the remote webhook request to
2) The name of JSON field which contains the text for the subject of the new card

After hitting the Save button you wil see a computed URL that you can provide to the originating service as the destination URL to send their messages to.  Any requests sent will be added to the selected board.

NOTE: This plugin uses the webhook token defined on the Webhook settings panel.  If you reset this token you will also need to notify any services which send requests to this plugin

Author
------

- David Morlitz

Requirements
------------

- Kanboard >= 1.1.0

Installation
------------

You have the choice between 3 methods:

1. Install the plugin from the Kanboard plugin manager in one click
2. Download the zip file and decompress everything under the directory `plugins/IncomingTask`
3. Clone this repository into the folder `plugins/IncomingTask`

Note: Plugin folder is case-sensitive.
