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
        dependencyCheck additionalArguments: '--project WORKSPACE', odcInstallation: 'OWASP Dependency-Check Vulnerabilities'
        dependencyCheckPublisher pattern: 'dependency-check-report.xml'
      }
    }
    stage('Code Analysis') {
    	environment {
                scannerHome = tool 'Sonar'
       	}
       	steps {
                script {
                    withSonarQubeEnv('Sonar') {
                        sh "${scannerHome}/bin/sonar-scanner \
                            -Dsonar.projectKey=Deliverable3 \
                            -Dsonar.sources=. \
                            -Dsonar.host.url=http://localhost:9000 \
                            -Dsonar.token=squ_95fe82edb6fc5305a14703b17a79e1d598e2953c
                    }
                }
            }
    }
  }
}
