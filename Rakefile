require "rubygems"
require "rake"

DIRDOCS 	= "docs"
DIRTESTS 	= "tests"
DIRLIB 		= "lib"

task :default => [:docs, :tests]

desc "Recreates the documentation using PHPDoc"
task :docs do
	system("rm -Rf #{DIRDOCS}")
  	system("phpdoc -t #{DIRDOCS} -o HTML:default:default -d #{DIRLIB}")
end

desc "Runs PHPUnit"
task :tests do
	system("rm -Rf #{DIRTESTS}/log/*")
	system("cd #{DIRTESTS}; phpunit --configuration phpunit.xml --verbose; cd ..;")
end

desc "Opens the documentation"
task :doc do
	system("open #{DIRDOCS}/index.html")
end

desc "Opens the reports log"
task :report do
	system("open #{DIRTESTS}/log/report/index.html")
end

desc "Creates a new release from the current tag"
task :release, :tag do |t, args|
	tag = args[:tag]
	system("git archive #{tag} --prefix=scaffold/ --format=zip > ../Releases/scaffold-#{tag}.zip")
end

desc "Creates a new tag"
task :tag, :version do |t,args|
	version = args[:version]
	system("git tag #{version}")
end