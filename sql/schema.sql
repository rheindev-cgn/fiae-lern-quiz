CREATE DATABASE IF NOT EXISTS fiae_quiz;
USE fiae_quiz;

-- Rollen für das Berechtigungssystem
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL -- 'guest', 'user', 'admin'
);

-- Nutzerverwaltung
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Kategorien (z.B. Hardware, Netzwerktechnik, SQL)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL
);

-- Die Fragen
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    question_text TEXT NOT NULL,
    is_premium BOOLEAN DEFAULT TRUE, -- FALSE = Teil der kostenlosen Testversion
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Antwortmöglichkeiten (1:n zu Questions)
CREATE TABLE IF NOT EXISTS answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    answer_text TEXT NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (question_id) REFERENCES questions(id)
);