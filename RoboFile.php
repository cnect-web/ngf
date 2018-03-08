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
    // Load .env file from project root.
    $dotenv = new Dotenv\Dotenv($this->root() . "/../");
    $dotenv->load();
    var_dump(getenv("ENVIRONMENT"));
    if (getenv("ENVIRONMENT") == 'development') {
      $this->projectSetupBehat();
    }

    // Change Branch.
    $this
      ->taskGitStack()
      ->stopOnFail()
      ->checkout($branch)
      ->pull()
      ->run();

    $this->say(getcwd());

    // Install website.
    /*
    $this->getInstallConfigTask()
      ->arg('config_installer_sync_configure_form.sync_directory=' . $this->config('settings.config_directories.sync'))
      ->siteInstall('config_installer')
      ->run();

    $this->taskDrushStack($this->config('bin.drush'))
      ->arg('-r', 'web/')
      ->exec("php-eval 'node_access_rebuild();'")
      ->run();
    */
  }

  /**
   * Update project dependencies.
   *
   * @command project:update-dep
   * @aliases pud
   */
  public function updateSiteDependencies() {
    // Run Composer update.
    $this
      ->taskComposerUpdate()
      ->run();
  }

  /**
   * Update project.
   *
   * @command project:update
   * @aliases pu
   */
  public function updateSite() {
    $this->taskDrushStack($this->config('bin.drush'))
      ->arg('-r', 'web/')
      ->exec('cache-clear drush')
      ->exec('updb')
      ->exec('csim -y')
      ->exec('cr')
      ->run();
  }

}
