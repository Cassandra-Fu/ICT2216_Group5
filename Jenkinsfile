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
    stage('Code Quality Check via SonarQube') {
	steps {
		script {
			def scannerHome = tool 'SonarQube';
			withSonarQubeEnv('SonarQube') {
				sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=OWASP -Dsonar.sources=."
			}
		}
	}
    }
  }
  post {
	always {
		recordIssues enabledForFailure: true, tool: sonarQube()
	}
 }
}

