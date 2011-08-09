MYSQL=/usr/local/mysql/bin/mysql

all: create schema passwords

create:
	${MYSQL} -uroot mysql < create.sql

schema:
	${MYSQL} -uvbsg -pvbsg vbsg < schema.sql

passwords:
	${MYSQL} -uvbsg -pvbsg vbsg < passwords.sql	

dump:
	${MYSQL} -uvbsg -pvbsg vbsg < test.sql
