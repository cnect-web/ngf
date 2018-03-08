<?php

namespace NGF\Robo;

use Robo\Robo;
use Robo\Tasks as RoboTasks;

/**
 * Class Tasks.
 *
 * @package NGF\Robo\Task\Build
 */
class Tasks extends RoboTasks {
  use \Boedah\Robo\Task\Drush\loadTasks;
  use \NuvoleWeb\Robo\Task\Config\loadTasks;

  /**
   * Output hostname.
   */
  public function ec2Hostname() {
    $result = $this
      ->taskExec('curl http://169.254.169.254/latest/meta-data/public-hostname')
      ->printOutput(false)
      ->run();
    if($result->wasSuccessful()) {
      $hostname = $result->getMessage();
      $this->say($hostname);
    }
  }

  /**
   * Output ip.
   */
  public function ec2Ip() {
    $result = $this
      ->taskExec('curl http://169.254.169.254/latest/meta-data/local-ipv4')
      ->printOutput(false)
      ->run();

    if($result->wasSuccessful()) {
      $ip = $result->getMessage();
      $this->say($ip);
    }
  }

  /**
   * Setup Behat.
   *
   * @command project:setup-behat
   * @aliases psb
   */
  public function projectSetupBehat() {
    $behat_tokens = $this->config('behat.tokens');

    $this->collectionBuilder()->addTaskList([
      $this->taskFilesystemStack()
        ->copy($this->config('behat.source'), $this->config('behat.destination'), TRUE),
      $this->taskReplaceInFile($this->config('behat.destination'))
        ->from(array_keys($behat_tokens))
        ->to($behat_tokens),
      $this->taskReplaceInFile($this->config('behat.destination'))
        ->from("{drupal_root}")
        ->to($this->config('project.root') . '/web'),
      $this->taskReplaceInFile($this->config('behat.destination'))
        ->from("{base_url}")
        ->to($this->config('project.url')),
    ])->run();
  }

  /**
   * Install site.
   *
   * @command project:install
   * @aliases pi
   */
  public function projectInstall() {
    $this->projectGenerateEnv();
    $this->getInstallTask()
      ->siteInstall($this->config('site.profile'))
      ->run();
  }

  /**
   * Install site from given configuration.
   *
   * @command project:install-config
   * @aliases pic
   */
  public function projectInstallConfig() {
    $this->projectGenerateEnv();
    $this->getInstallTask()
      ->arg('config_installer_sync_configure_form.sync_directory=' . $this->config('settings.config_directories.sync'))
      ->siteInstall('config_installer')
      ->run();
  }

  /**
   * Setup .env file.
   *
   * @command project:setup-env
   * @aliases pse
   */
  public function projectGenerateEnv() {
    $content = '';
    $settings = [
      'ENVIRONMENT' => 'project.environment',
      'DATABASE' => 'database.name',
      'DATABASE_HOST' => 'database.host',
      'DATABASE_PORT' => 'database.port',
      'DATABASE_USER' => 'database.user',
      'DATABASE_PASSWORD' => 'database.password',
      'DATABASE_PREFIX' => 'database.prefix',
    ];
    foreach ($settings as $key => $setting) {
      $content .= "$key=" . $this->config($setting) ."\n";
    }
    if (!empty($content)) {
      $this->taskWriteToFile($this->root() . '/.env')->text($content)->run();
    }
  }

  /**
   * Get installation task.
   *
   * @return \Boedah\Robo\Task\Drush\DrushStack
   *   Drush installation task.
   */
  protected function getInstallConfigTask() {
    return $this->taskDrushStack($this->config('bin.drush'))
      ->arg("--root={$this->root()}/web")
      ->accountMail($this->config('account.mail'))
      ->accountName($this->config('account.name'))
      ->accountPass($this->config('account.password'))
      ->dbPrefix($this->config('database.prefix'))
      ->dbUrl(sprintf("mysql://%s:%s@%s:%s/%s",
        $this->config('database.user'),
        $this->config('database.password'),
        $this->config('database.host'),
        $this->config('database.port'),
        $this->config('database.name')));
  }

  /**
   * Get installation task.
   *
   * @return \Boedah\Robo\Task\Drush\DrushStack
   *   Drush installation task.
   */
  protected function getInstallTask() {
    return $this->taskDrushStack($this->config('bin.drush'))
      ->arg("--root={$this->root()}/web")
      ->siteName($this->config('site.name'))
      ->siteMail($this->config('site.mail'))
      ->locale($this->config('site.locale'))
      ->accountMail($this->config('account.mail'))
      ->accountName($this->config('account.name'))
      ->accountPass($this->config('account.password'))
      ->dbPrefix($this->config('database.prefix'))
      ->dbUrl(sprintf("mysql://%s:%s@%s:%s/%s",
        $this->config('database.user'),
        $this->config('database.password'),
        $this->config('database.host'),
        $this->config('database.port'),
        $this->config('database.name')));
  }

  /**
   * Get root directory.
   *
   * @return string
   *   Root directory.
   */
  protected function root() {
    return getcwd();
  }

}
