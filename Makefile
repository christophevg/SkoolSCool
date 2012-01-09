SKIN=vbsg
FTP_USER=
FTP_PASS=
URL=ftp.vrijebasisschoolschriekgrootlo.be
ROOT=/www
FILES=.htaccess *.php lib

all: dist

upload: dist
	@ncftpput -R -u ${FTP_USER} -p ${FTP_PASS} ${URL} ${ROOT} webroot/*

dist: dist-clean
	@mkdir -p webroot/skins
	@cp -r ${FILES} webroot/
	@rm -f webroot/config.php
	@rm -rf webroot/lib/Zend
	@cp -r skins/${SKIN} webroot/skins/
	@cp -r skins/${SKIN}.php webroot/skins/
	@find webroot -type d -name .git | xargs rm -rf

dist-clean:
	@rm -rf webroot

init:
	@git submodule init && git submodule update
	@cd lib/LightOpenIDClient; git submodule init && git submodule update
	@echo "TODO"
	@echo "- Edit config.php to setup your site..."
	@echo "- Create the database: cd db; make"
