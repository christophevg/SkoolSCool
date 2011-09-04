FILES=.htaccess ajax.php index.php bootstrap.php lib handle_file_upload.php
SKIN=vbsg

all: dist

dist: dist-clean
		@mkdir -p webroot/skins
		@cp -r ${FILES} webroot/
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
