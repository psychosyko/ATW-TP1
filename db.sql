CREATE TABLE users (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  image_location TEXT NOT NULL,
  admin INT DEFAULT 0
);

CREATE TABLE types (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL
);

CREATE TABLE concerts (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  id_type INT NOT NULL,
  location VARCHAR(255) NOT NULL,
  organizer VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  date DATE NOT NULL,
  time TIME NOT NULL,
  price DECIMAL(10, 2),
  image_location TEXT NOT NULL,
  FOREIGN KEY (id_type) REFERENCES types(id)
);

CREATE TABLE orders (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL,
  id_concert INT NOT NULL,

  FOREIGN KEY (id_user) REFERENCES users(id),
  FOREIGN KEY (id_concert) REFERENCES concerts(id)
);

INSERT INTO types (name)
VALUES
  ('Rock'),
  ('Pop'),
  ('Jazz'),
  ('Classical'),
  ('Hip Hop'),
  ('Electronic'),
  ('Country'),
  ('Reggae'),
  ('Blues'),
  ('Folk');
