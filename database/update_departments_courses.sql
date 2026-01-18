-- SQL script to update departments and courses

-- First, clear existing departments and courses
TRUNCATE TABLE department_list;
TRUNCATE TABLE curriculum_list;

-- Insert new departments
INSERT INTO department_list (name, description, status, date_created) VALUES
('College of Psychology', 'Department focused on psychological studies and research.', 1, NOW()),
('College of Education', 'Department focused on education and teaching methodologies.', 1, NOW()),
('College of Information Technology', 'Department focused on information technology and computer science.', 1, NOW()),
('College of Engineering', 'Department focused on various engineering disciplines.', 1, NOW()),
('College of Business Administration', 'Department focused on business management and administration.', 1, NOW());

-- Insert new courses/curriculums
INSERT INTO curriculum_list (department_id, name, description, status, date_created) VALUES
(1, 'BS Psychology', 'Bachelor of Science in Psychology', 1, NOW()),
(2, 'BSEd English', 'Bachelor of Secondary Education Major in English', 1, NOW()),
(2, 'BSEd Social Studies', 'Bachelor of Secondary Education Major in Social Studies', 1, NOW()),
(2, 'BEEd', 'Bachelor of Elementary Education', 1, NOW()),
(3, 'BSIT', 'Bachelor of Science in Information Technology', 1, NOW()),
(4, 'BS Computer Engineering', 'Bachelor of Science in Computer Engineering', 1, NOW()),
(4, 'BS Industrial Engineering', 'Bachelor of Science in Industrial Engineering', 1, NOW()),
(5, 'BSBA-HRM', 'Bachelor of Science in Business Administration Major in Human Resource Management', 1, NOW()),
(3, 'Diploma in IT', 'Diploma in Information Technology', 1, NOW()),
(4, 'Diploma in CET', 'Diploma in Computer Engineering Technology', 1, NOW());