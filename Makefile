MAKEFLAGS = --no-print-directory --always-make
MAKE = make $(MAKEFLAGS)

DIRDOCS = docs
DIRTESTS = tests
DIRLIB = lib

PHPDOC = phpdoc -t $(DIRDOCS) -o HTML:default:default -d $(DIRLIB)
PHPUNIT = cd $(DIRTESTS); phpunit --configuration phpunit.xml --verbose; cd ..;

all:
	$(MAKE) tests
	$(MAKE) docs

docs:
	rm -Rf $(DIRDOCS)/*
	$(PHPDOC)

doc:
	open $(DIRDOCS)/index.html
	
tests:
	rm -Rf $(DIRTESTS)/log/*
	$(PHPUNIT)

report:
	open $(DIRTESTS)/log/report/index.html
