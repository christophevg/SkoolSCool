MYSQL=/usr/local/mysql/bin/mysql
ADMIN=
USER=
PASS=
DATABASE=

all: create schema system passwords

create:
	@sed -e 's/%%USER%%/${USER}/g' \
			 -e 's/%%PASS%%/${PASS}/g' \
			 -e 's/%%DATABASE%%/${DATABASE}/g' \
			 -e 's/%%ADMIN%%/${ADMIN}/g' \
			 create.sql \
	| ${MYSQL} -u${ADMIN} -p mysql

schema:
	${MYSQL} -u${USER} -p${PASS} ${DATABASE}  < schema.sql

system:
	${MYSQL}  -u${USER} -p${PASS} ${DATABASE} < system.sql

passwords:
	${MYSQL}  -u${USER} -p${PASS} ${DATABASE} < passwords.sql
