node('JenkinsSlave') {
  stage('Clone') {
      // Clone the repo
      checkout([
          $class: 'GitSCM',
          branches: [
              [name: '${ghprbActualCommit}']
          ],
          doGenerateSubmoduleConfigurations: false,
          extensions: [],
          submoduleCfg: [],
          userRemoteConfigs: [
              [
                  credentialsId: 'CnectBotUP',
                  refspec: '+refs/pull/*:refs/remotes/origin/pr/*',
                  url: 'https://github.com/cnect-web/ngf'
              ]
          ]
      ])
  }
  stage('Build') {
      sh '''
          . /home/ubuntu/.jenkins_info &&
          cd ${WORKSPACE} &&
          composer install &&
          ./bin/robo project:install-config -o "project.root: ${WORKSPACE}" -o "project.url: ${HOST_NAME}" -o "database.password: ${MYSQL_PASSWORD}" &&
          ./bin/robo project:setup-behat -o "project.root: ${WORKSPACE}" -o "database.password: ${MYSQL_PASSWORD}"
          '''
  }
  stage('Install') {
      sh '''
          sudo rm -rf /var/www/html/web &&
          sudo ln -s ${WORKSPACE}/web /var/www/html/ &&
          sudo chmod 0777 ${WORKSPACE}/web/sites/default/files/
          '''
  }
  stage('Test') {
      sh '''cd ${WORKSPACE}/behat &&
      ./bnp.sh behat
      '''
  }
  stage('Ready') {
      sh '''
      . /home/ubuntu/.jenkins_info &&
      echo "${HOST_NAME}"
      '''
  }

}

