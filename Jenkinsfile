pipeline {
  agent any
  stages {
    stage('Checkout SCM') {
      steps {
        git(url: 'https://github.com/Cassandra-Fu/ICT2216_Group5.git', branch: 'main')
      }
    }

    stage('OWASP Dependency-Check Vulnerabilities') {
      steps {
        dependencyCheck(odcInstallation: 'OWASP Dependency-Check Vulnerabilities', dependencyCheckPublisher pattern: 'dependency-check-report.xml')
      }
    }
  }
}

