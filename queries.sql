INSERT INTO project (name, user_id)
VALUES  ('Входящие','1'),
        ('Учеба', '1'),
        ('Работа', '1'),
        ('Домашние дела', '2'),
        ('Авто', '2');
INSERT INTO user (date_regist, email, name, password)
VALUES ('2018.12.31', 'jobs@ya.ru', 'Стив', '0000'),
       ('2019.02.12', 'gates@ya.ru', 'Билл', '1234');
INSERT INTO task (date_create, date_done, state, name, file, deadline, user_id, project_id)
VALUES ('2018.12.01', NULL, 0, 'Собеседование в IT компании', '', '2019.02.09', '1', '3'),
       ('2019.01.01', NULL, 0, 'Выполнить тестовое задание', '', '2019.12.25', '1', '3'),
       ('2018.12.01', '2019.12.21', 1, 'Сделать задание первого раздела', '', '2019.12.31', '1', '2'),
       ('2019.02.12', NULL, 0, 'Встреча с другом', '', '2019.12.22', '2', '1'),
       ('2019.02.13', NULL, 0, 'Купить корм для кота', '', NULL, '2', '4'),
       ('2019.02.14', NULL, 0, 'Заказать пиццу', '', NULL, '2', '4');
-- Получить список из всех проектов одного пользователя
SELECT name FROM project WHERE user_id = 1;
-- Получить список из всех задач для одного проекта
SELECT name FROM task WHERE project_id = 3;
-- Получить список из всех задач для одного проекта и название этого проекта
SELECT p.name AS project, t.name AS task FROM task t JOIN project p WHERE p.id = t.project_id AND p.id = 3;
-- Пометить задачу как выполненную (Встреча с другом);
UPDATE task SET state = 1, date_done = '12.02.2019' WHERE id = 4;
-- Обновить название задачи
UPDATE task SET name = 'Заказать роллы' WHERE id = 6;
