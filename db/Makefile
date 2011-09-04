MYSQL=/usr/local/mysql/bin/mysql

all: create schema system passwords content

create:
	${MYSQL} -uroot mysql < create.sql

schema:
	${MYSQL} -uvbsg -pvbsg vbsg < schema.sql

system:
	${MYSQL} -uvbsg -pvbsg vbsg < system.sql

passwords:
	${MYSQL} -uvbsg -pvbsg vbsg < passwords.sql

content:
	${MYSQL} -uvbsg -pvbsg vbsg < content.sql
