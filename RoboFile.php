<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
use NGF\Robo\Tasks as NGFTasks;
/**
 * Class RoboFile.
 */
class RoboFile extends NGFTasks {

  /**
   * Build project.
   *
   * @command project:build
   * @aliases pb
   */
  public function build($branch) {

    // Generate environment file.
    $this->projectGenerateEnv();
    $this->projectSetupBehat();

    // Change Branch.
    $this
      ->taskGitStack()
      ->stopOnFail()
      ->checkout($branch)
      ->pull()
      ->run();

    // Run Composer update.
    $this
      ->taskComposerInstall()
      ->run();

    // Install website.
    $this->getInstallTask()
      ->arg('config_installer_sync_configure_form.sync_directory=' . $this->config('settings.config_directories.sync'))
      ->siteInstall('config_installer')
      ->run();

  }

}
