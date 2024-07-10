pipeline {
  agent any
  stages {
    stage('Checkout SCM') {
      steps {
        git(url: 'https://github.com/Cassandra-Fu/ICT2216_Group5.git', branch: 'main')
      }
    }

    stage('OWASP DependencyCheck') {
      steps {
        dependencyCheck(additionalArguments: '--noupdate --nvdApiKey e38784dc-b37b-4d03-a011-ba432b095a74 --format XML --format HTML', odcInstallation: 'OWASP Dependency-Check Vulnerabilities')
      }
    }

    stage('Composer Install and Test') {
      agent {
        docker {
          image 'composer:latest'
        }

      }
      steps {
      	sh 'composer install'
        sh 'mkdir -p logs'
        sh './vendor/bin/phpunit tests'
      }
    }

  }
  post {
    always {
      script {
        def resultFile = 'logs/unitreport.xml'
        if (fileExists(resultFile)) {
          junit resultFile
        } else {
          echo "Test results not found in ${resultFile}"
        }
      }

    }

    failure {
      sh 'ls -l logs'
      script {
        def resultFile = 'logs/unitreport.xml'
        if (fileExists(resultFile)) {
          sh "cat ${resultFile}"
        } else {
          echo "Test results not found in ${resultFile}"
        }
      }

    }

  }
}
