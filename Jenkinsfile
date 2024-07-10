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
        dependencyCheck(additionalArguments: '''--noupdate --nvdApiKey e38784dc-b37b-4d03-a011-ba432b095a74  -o './' -s './' -f 'ALL'  --prettyPrint''', odcInstallation: 'OWASP Dependency-Check Vulnerabilities')
      }
    }
   stage('Code Quality Check via SonarQube') {
	steps {
		script {
			def scannerHome = tool 'SonarQube';
				withSonarQubeEnv('SonarQube') {
				sh "${scannerHome}/bin/sonar-scanner \
				-Dsonar.projectKey=OWASP \
				-Dsonar.sources=. \
				-Dsonar.host.url=http://localhost:9000 \
				-Dsonar.token=sqp_a6d093e26302b42a7bfac2421d3d3e81526f5d8c"
				}
			}	
		}
	}
}
post {
	always {
		recordIssues enabledForFailure: true, tool: SonarQube()
	}    
 } 
}
