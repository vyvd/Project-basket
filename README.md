# Console

## Usage

Allows PHP scripts to be run from terminal

To run a script it has to be specified in the command by using the -c or --command option

The script will run the init function located in a "Command" class prefixed with the command.

E.g. --command ExampleCommand will point to /console/command/ExampleCommand.php

## Required options

| Command name    | Short name |          | Description                                                                |
|-----------------|------------|----------|----------------------------------------------------------------------------|
| --command       | -c         | Required | Relates to the name of the command class in /console/command/*Command.php  |
| --web_root_path | -w         | Required | The full path of the directory holding the web files                       |
| --env_file_path | -d         | Required | The full path to the environment variables file (.env) directory           |
| --env_file_name | -n         | Required | The name of the environment variable file .env                             |


## Examples
```
php console.php -c ExampleShortnameCommand
```
```
php console.php --command ExampleFullnameCommand
```
```
php console.php --command ExampleCommandWithOptions ---is_valid true -i false 
```

## Monthly Learning Report

### Options

| Command name     | Short name |          | Default          | nExample                                         | Description                                                                                                                                                                                                                         |
|------------------|------------|----------|------------------|--------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| --time_period    | -t         | Optional | -12 months       | `--time_period="12 months"`<br/>`-t="12 months"` | Set the time period from today for when a course was assigned to a user<br/> This uses the strtotime function so can accept any value that is valid for <br/> [PHP strtotime](https://www.php.net/manual/en/function.strtotime.php) |
| --limit          | -l         | Optional | false (No limit) | `--limit=1000`<br/>`-l=1000`                     | Set a limit for the amount of accounts to be sent an email                                                                                                                                                                          |
| --batch_limit    | -b         | Optional | 1000             | `--batch_limit=1000`<br/>`-b=100`                | Set a limit for the amount of accounts in a batch                                                                                                                                                                                   |
| --interval       | -i         | Optional | 0                | `--interval=10`<br/>`-i=10`                      | Set the time (in seconds) to wait before sending each email                                                                                                                                                                         |
| --batch_interval | -j         | Optional | 60               | `--batch_interval=60`<br/>`-j=60`                | Set the time (in seconds) to wait before sending the next batch of emails                                                                                                                                                           |
| --offset         | -o         | Optional | 0                | `--offset=0`<br/>`-o=0`                          | Specify an offset for the database fetch query                                                                                                                                                                                      |

### Example
```
php console.php --command MonthlyLearningReportEmail --batch_interval 60  -w="/var/www/html/new-skills-academy-rebuild" -d="/var/www/html" -n=".newskills-env" --email_override="truvoicer8@outlook.com"
```
# Code Structure

- /base - common template files, such as the header and footer, are stored here
- /classes - contains third party API functionality that isn't managed through Composer
- /controller - contains core functionality for the app, where each controller is named depending on what functionality it contains (for example, blumeController.php contains admin related functionality)
- /vendor - contains all Composer installed packages
- /view - contains all views for the app, and routes are configured to automatically load from the view (for example/view/courses contains the view for newskillsacademy.com/courses)
- /public - publically accessible index.php and assets
	- -htaccess - server config file for Apache based servers containing cache settings, and routes all traffic through index.php
	- index.php - loads configuration details such as database connections, and routes all traffic through the main /controller/Controller.php file
	- /assets - where static assets such as CSS and images are stored

Within Controller.php is a function called invoke(), which is where all traffic is routed through. Within here, there's a multidimensional array of routes that can be accessed on newskillsacademy.com, each with their own settings for whether sign in is required or not (but a normal user or admin user). Controller.php will then use this information to determine access to specific routes. For example, if a route is only for signed in users and someone who isn't signed in tried to access it, it will automatically redirect the user to the sign in page.

Functions within controllers can be accessed (for POST or GET requests) with newskillsacademy.com/ajax?c=controllerName&a=function-name. So for example, if we wanted to send POST data from the sign in form to the signIn() function within accountController.php - we would send the POST data to newskillsacademy.com/ajax?c=account&a=sign-in. Essentially, /ajax is the internal API endpoint for newskillsacademy.com. Within the frontend, we can utilise $this->renderFormAjax() functions to construct endpoints to /ajax.

There's also some constants which can be used anywhere in the codebase for different things, such as:

- CUR_ID_FRONT: The ID of the current signed in user. If this is blank, the user is not signed in.
- SITE_URL: The full domain of the website, useful for internal linking.
- SITE_NAME: The name of the website, i.e. New Skills Academy or Staff Skills Academy.
- REQUEST: The full slug of the current page the user is on, useful for adding active states within the website.
- MAINT_MODE: Set to "On" or "Off" depending if we are in a development environment.
- TO_PATH: The directory of which the public website sits on the server.
- TO_PATH_CDN: The directory of which the public CDN sits on the server.
- CUR_ID: Works the same as CUR_ID_FRONT, but for the admin portal users.
- 
## Example

So let's say you wanted to create a page called newskillsacademy.com/data and you only wanted it to be visible to those signed into their account. You'd first register a route in Controller.php, similar to the below:

    array(
        'url' => 'data',
        'controller' => '',
        'signInRequired' => "true",
        'permissions' => 'all'
    ),

You'd then create a view called /view/data/responsive.php, and in there you'd include base/header.base.php for example to include the header, followed by some other frontend code which you'd like to render on the page. That's it! Let's say you wanted to add some functionality though, you could then go onto create a controller called dataController.php, which contains functions relating to your new page. You can then use...

    $this->setControllers(array("data"));

...at the top of your new /view/data/responsive.php file, which now means functions from within dataController.php can be called via 

    $this->data->functionName();

in your view. Alternatively, you can send data to newskillsacademy.com/ajax?c=data&a=function-name from a form, for example.
