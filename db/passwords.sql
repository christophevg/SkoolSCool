SELECT SLEEP(1);

-- USERS
INSERT INTO allObjects ( type, id, name, pass, email, rights )
VALUES
( 'User', 'user',        'Simple User',       md5('user'),        '', ''            ),
( 'User', 'contributor', 'Contributing User', md5('contributor'), '', 'contributor' ),
( 'User', 'admin',       'Admin User',        md5('admin'),       '', 'admin'       );
