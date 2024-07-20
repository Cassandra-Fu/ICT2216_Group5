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
			sh '/var/jenkins_home/sonar-scanner-6.1.0.4477-linux-x64/bin/sonar-scanner -Dsonar.projectKey=OWASP -Dsonar.sources=. -Dsonar.host.url=http://localhost:9000 -Dsonar.token=sqp_a6d093e26302b42a7bfac2421d3d3e81526f5d8c'
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

