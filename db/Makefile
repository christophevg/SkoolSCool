MYSQL_CLIENT=/usr/local/mysql/bin/mysql
ADMIN=
USER=
PASS=
DATABASE=
MYSQL=${MYSQL_CLIENT} -u${USER} -p${PASS} ${DATABASE}

all: create schema system passwords

create:
	@sed -e 's/%%USER%%/${USER}/g' \
			 -e 's/%%PASS%%/${PASS}/g' \
			 -e 's/%%DATABASE%%/${DATABASE}/g' \
			 -e 's/%%ADMIN%%/${ADMIN}/g' \
			 create.sql \
	| ${MYSQL_CLIENT} -u${ADMIN} -p mysql

schema:
	${MYSQL} < schema.sql

system:
	${MYSQL} < system.sql

passwords:
	${MYSQL} < passwords.sql

shell:
	${MYSQL}
