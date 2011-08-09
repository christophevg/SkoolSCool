SELECT SLEEP(1);

-- USERS
INSERT INTO allObjects ( type, id, name, pass, email, rights )
VALUES
( 'User', 'user',    'Simple User',     md5('user'),    'xtof', ''                  );
