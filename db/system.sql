-- USERS
INSERT INTO allObjects ( type, id, name, pass, email, rights )
VALUES
( 'User', 'system',  'system',          '', '', 'admin'             );

-- PAGECONTENT
INSERT INTO allObjects ( type, id, author, body )
VALUES
( 'PageContent', '404', 'system',
  '# Whoops, a 404...

This content object is not known.' ),
( 'PageContent', '401', 'system',
  '# Sorry...

You are not authorized to access this page.' ),

( 'PageContent', 'unknownContent', 'system',
  '# Unknown Content

To create this content, visit [{{id}}?create|this].' ),
( 'PageContent', 'newContent', 'system',
  '# New Content Wizard

Select the kind of content to create

## Basic Page

... allows you to create textual content using a simple mark-up language. It is
possible to link to this page from other pages or the site\'s nativation.

[{{id}}?create&type=PageContent&mode=edit|create a basic page]

## News Item

... is a page with an aditional date field. News items are also listed.

[{{id}}?create&type=NewsContent&mode=edit|create a news item...]'),

( 'PageContent', 'navigation', 'system', 
  '* [home]
** [category 1/subsection 1|Sub Section 1]
'),

( 'PageContent', 'footer', 'system',
  '&copy; 2011-2013 - SkoolsCool' ),

( 'PageContent', 'home', 'system',
	'This is the home page. It can be styled as you like.

It is also possible to include content from the site...

[include:footer?embed]' ),

( 'PageContent', 'news');

-- SPECIALS
INSERT INTO allObjects ( type, id, author )
VALUES
( 'NewsList', 'news', 'system' );
