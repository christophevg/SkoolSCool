FILES=.htaccess ajax.php index.php lib
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
