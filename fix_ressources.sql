USE skolea;

DELETE FROM ressources;
ALTER TABLE ressources AUTO_INCREMENT = 1;

INSERT INTO ressources (module_id, titre, type, contenu, description) VALUES
(1,  'Support de cours - Introduction a PHP 8', 'document', 'https://www.php.net/manual/fr/getting-started.php', 'Slides du module.'),
(1,  'Video - Installer son environnement PHP', 'video', 'https://www.youtube.com/watch?v=OK_JCtrrv-c', 'Guide officiel d''installation.'),
(2,  'Support de cours - PDO et requetes preparees', 'document', 'https://www.php.net/manual/fr/book.pdo.php', NULL),
(2,  'Quiz - Connexion a une base de donnees', 'quiz', 'https://www.w3schools.com/php/php_quiz.asp', '10 questions a choix multiple.'),
(3,  'Enonce du mini-projet CRUD', 'document', 'https://www.php.net/manual/fr/pdo.prepared-statements.php', NULL),
(4,  'Support de cours - ES6+', 'document', 'https://developer.mozilla.org/fr/docs/Web/JavaScript/Guide', NULL),
(5,  'Video - Comprendre async/await', 'video', 'https://www.youtube.com/watch?v=vn3tm0quoqE', NULL),
(6,  'Support de cours - API RESTful', 'document', 'https://developer.mozilla.org/fr/docs/Web/HTTP/Methods', NULL),
(7,  'Quiz - Authentification et securite', 'quiz', 'https://owasp.org/www-project-top-ten/', NULL),
(8,  'Support de cours - Bases de Python', 'document', 'https://docs.python.org/fr/3/tutorial/', NULL),
(9,  'Jeu de donnees d''exemple', 'document', 'https://raw.githubusercontent.com/mwaskom/seaborn-data/master/iris.csv', 'A utiliser pour les exercices pratiques.'),
(10, 'Support de cours - Introduction au ML', 'document', 'https://scikit-learn.org/stable/getting_started.html', NULL),
(11, 'Video - Entrainer un modele de regression', 'video', 'https://www.youtube.com/watch?v=7eh4d6sabA0', NULL),
(12, 'Support de cours - Prise en main de Figma', 'document', 'https://help.figma.com/hc/en-us/categories/360002051613-Getting-started', NULL),
(13, 'Quiz - Design system', 'quiz', 'https://www.designsystems.com/', NULL),
(14, 'Support de cours - Heuristiques de Nielsen', 'document', 'https://www.nngroup.com/articles/ten-usability-heuristics/', NULL),
(15, 'Support de cours - Failles web courantes', 'document', 'https://developer.mozilla.org/fr/docs/Learn/Server-side/First_steps/Website_security', NULL),
(16, 'Quiz - Bonnes pratiques de securisation', 'quiz', 'https://cheatsheetseries.owasp.org/', NULL),
(17, 'Support de cours - Strategie de contenu', 'document', 'https://developers.google.com/search/docs/fundamentals/creating-helpful-content', NULL),
(18, 'Video - Les bases du SEO', 'video', 'https://www.youtube.com/watch?v=MYE6T_gd7H0', NULL);
