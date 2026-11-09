CREATE DATABASE IF NOT EXISTS db_perpustakaan;
USE db_perpustakaan;

CREATE TABLE authors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  country VARCHAR(100) NOT NULL
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  year INT NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  author_id INT NOT NULL,
  category_id INT NOT NULL,
  CONSTRAINT fk_books_author FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_books_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

INSERT INTO authors (name, country) VALUES ('Andrea Hirata','Indonesia'),('Tere Liye','Indonesia');
INSERT INTO categories (name) VALUES ('Novel'),('Pendidikan');
INSERT INTO books (title, year, stock, author_id, category_id) VALUES ('Laskar Pelangi',2005,5,1,1);
