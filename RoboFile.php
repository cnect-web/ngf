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
    // Change Branch.
    $this
      ->taskGitStack()
      ->stopOnFail()
      ->checkout($branch)
      ->pull()
      ->run();

    // Checkout any local changes on settings.php
    if ($this->taskExec("git checkout -- web/sites/default/settings.php")->run()->wasSuccessful()) {
      $this->say("Cleared up settings.php changes.");
    }

    // Install website.
    $this->projectInstallConfig();
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


  /**
   * Run QA tasks.
   *
   * @command tools:qa
   * @aliases qa
   */
  public function qa(array $options = ['paths' => ['web/modules/custom', 'web/themes/contrib/funkywave/'], 'ops' => ['cs']]) {
    if (in_array('cs', $options['ops'])) {
      $this->cs($options['paths']);
    }
  }

  /**
   * Run QA tasks.
   *
   * @command tools:code-sniff
   * @aliases cs
   */
  public function cs(array $paths) {
    if ($this
      ->taskExec("bin/phpcs --standard=phpcs-ruleset.xml " . implode(' ', $paths))
      ->run()
      ->wasSuccessful()
    ) {
      $this->say("Code sniffer finished.");
    };
  }

}
