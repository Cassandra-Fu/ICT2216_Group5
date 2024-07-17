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
  }
}

