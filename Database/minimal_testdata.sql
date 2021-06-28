DELETE
FROM assignment;
DELETE
FROM coding_standard;
DELETE
FROM course;
DELETE
FROM course_users;
DELETE
FROM output;
DELETE
FROM role;
DELETE
FROM submission;
DELETE
FROM task;
DELETE
FROM test;
DELETE
FROM user;

INSERT INTO coding_standard(id, name, description)
VALUES (1, 'Normal test', 'No coding style'),
       (2, 'Google Java Style', 'https://google.github.io/styleguide/javaguide.html'),
       (3, 'Sun Java Style', 'https://www.oracle.com/java/technologies/javase/codeconventions-contents.html');

INSERT INTO role(id, role)
VALUES (1, 'administrator'),
       (2, 'professor'),
       (3, 'student');
       
INSERT INTO test_type(id, type)
VALUES  (1, 'public'),
		(2, 'private');