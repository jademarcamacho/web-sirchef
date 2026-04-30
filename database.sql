CREATE DATABASE IF NOT EXISTS sirchef_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sirchef_db;

DROP TABLE IF EXISTS site_settings, user_activity_logs, messages, chat_group_members, chat_groups, follows, saved_recipes, favorites, likes, post_media, user_posts, recipe_comments, recipe_ratings, user_posted_recipes, recipe_ingredients, recipes, newsletter_subscribers, contact_messages, password_resets, email_verifications, users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(80) NOT NULL,
  last_name VARCHAR(80) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  bio TEXT NULL,
  profile_photo VARCHAR(255) NULL,
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  verified_at DATETIME NULL,
  failed_login_attempts INT NOT NULL DEFAULT 0,
  locked_until DATETIME NULL,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE email_verifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  email VARCHAR(160) NOT NULL,
  code VARCHAR(10) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  email VARCHAR(160) NOT NULL,
  code VARCHAR(10) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  duration_minutes INT NOT NULL,
  difficulty ENUM('Easy','Medium','Hard') NOT NULL,
  cuisine VARCHAR(80) NOT NULL,
  category VARCHAR(80) NOT NULL DEFAULT 'Dishes',
  image VARCHAR(255) NOT NULL,
  youtube_url VARCHAR(255) NULL,
  instructions TEXT NOT NULL,
  source_type ENUM('admin','user') NOT NULL DEFAULT 'admin',
  status ENUM('draft','published') NOT NULL DEFAULT 'published',
  search_count INT NOT NULL DEFAULT 0,
  views INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE recipe_ingredients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT NOT NULL,
  ingredient_name VARCHAR(140) NOT NULL,
  quantity VARCHAR(80) NULL,
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
  INDEX (ingredient_name)
);

CREATE TABLE user_posted_recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT NOT NULL,
  user_id INT NOT NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  duration_minutes INT NOT NULL,
  difficulty ENUM('Easy','Medium','Hard') NOT NULL,
  cuisine VARCHAR(80) NOT NULL,
  category VARCHAR(80) NOT NULL DEFAULT 'Dishes',
  image VARCHAR(255) NOT NULL,
  youtube_url VARCHAR(255) NULL,
  ingredients TEXT NOT NULL,
  instructions TEXT NOT NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'published',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_posted_recipe (recipe_id),
  INDEX idx_user_posted_user (user_id),
  INDEX idx_user_posted_status (status),
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE recipe_ratings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  recipe_id INT NOT NULL,
  rating TINYINT NOT NULL,
  feedback TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_rating (user_id, recipe_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

CREATE TABLE recipe_comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  recipe_id INT NOT NULL,
  comment TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

CREATE TABLE user_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  content TEXT NOT NULL,
  post_type ENUM('thought','recipe_share') NOT NULL DEFAULT 'thought',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE post_media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  media_type ENUM('image','video') NOT NULL,
  media_path VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES user_posts(id) ON DELETE CASCADE
);

CREATE TABLE likes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  recipe_id INT NULL,
  post_id INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_like_recipe (user_id, recipe_id),
  UNIQUE KEY uq_like_post (user_id, post_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
  FOREIGN KEY (post_id) REFERENCES user_posts(id) ON DELETE CASCADE
);

CREATE TABLE favorites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  recipe_id INT NULL,
  post_id INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_fav_recipe (user_id, recipe_id),
  UNIQUE KEY uq_fav_post (user_id, post_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
  FOREIGN KEY (post_id) REFERENCES user_posts(id) ON DELETE CASCADE
);

CREATE TABLE saved_recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  recipe_id INT NULL,
  post_id INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_save_recipe (user_id, recipe_id),
  UNIQUE KEY uq_save_post (user_id, post_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
  FOREIGN KEY (post_id) REFERENCES user_posts(id) ON DELETE CASCADE
);

CREATE TABLE follows (
  id INT AUTO_INCREMENT PRIMARY KEY,
  follower_id INT NOT NULL,
  following_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_follow (follower_id, following_id),
  FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE chat_groups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(140) NOT NULL,
  created_by INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE chat_group_members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  group_id INT NOT NULL,
  user_id INT NOT NULL,
  role ENUM('admin','member') NOT NULL DEFAULT 'member',
  joined_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_group_user (group_id, user_id),
  FOREIGN KEY (group_id) REFERENCES chat_groups(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  group_id INT NULL,
  sender_id INT NOT NULL,
  receiver_id INT NULL,
  message TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (group_id) REFERENCES chat_groups(id) ON DELETE CASCADE,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  first_name VARCHAR(80) NOT NULL,
  last_name VARCHAR(80) NULL,
  email VARCHAR(160) NOT NULL,
  subject VARCHAR(180) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('new','read','replied') NOT NULL DEFAULT 'new',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE newsletter_subscribers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(160) NOT NULL UNIQUE,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE user_activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(100) NOT NULL,
  entity_type VARCHAR(80) NULL,
  entity_id INT NULL,
  details TEXT NULL,
  ip_address VARCHAR(64) NULL,
  user_agent VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE site_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(120) NOT NULL UNIQUE,
  setting_value TEXT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO site_settings (setting_key, setting_value) VALUES
('mail_host', 'smtp.gmail.com'),
('mail_port', '587'),
('mail_encryption', 'tls'),
('mail_from', 'yourgmail@gmail.com'),
('mail_name', 'SirChef'),
('mail_username', 'yourgmail@gmail.com'),
('mail_password', 'xxxx xxxx xxxx xxxx')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

INSERT INTO recipes (title, description, duration_minutes, difficulty, cuisine, category, image, youtube_url, instructions, source_type) VALUES
('Chicken Adobo','Filipino soy-vinegar chicken with garlic, bay leaves, and peppercorns.',45,'Easy','Filipino','Dishes','../Assets/chicken adobo.jpg','https://www.youtube.com/watch?v=wwapBsT0oc8','Marinate chicken in soy sauce, vinegar, garlic, bay leaves, and pepper. Simmer until tender. Reduce sauce until glossy. Serve with rice.','admin'),
('Sinigang na Baboy','Comforting sour pork soup with tamarind and vegetables.',60,'Medium','Filipino','Dishes','../Assets/sinigang.jpg','https://www.youtube.com/watch?v=22Lh3uR0J8A','Boil pork until tender. Add tamarind base and vegetables. Season with fish sauce. Serve hot.','admin'),
('Pancit Bihon','Rice noodles tossed with vegetables, chicken, and savory sauce.',35,'Medium','Filipino','Dishes','../Assets/pancit bihon.jpg','https://www.youtube.com/watch?v=grbK6Z6XJ1E','Saute garlic, onion, meat, and vegetables. Add broth and soy sauce. Toss noodles until cooked.','admin'),
('Garlic Butter Pasta','Quick pasta with garlic, butter, parsley, and parmesan.',20,'Easy','Italian','Dishes','../Assets/garlic.jpg','https://www.youtube.com/watch?v=bJUiWdM__Qw','Cook pasta. Saute garlic in butter. Toss pasta with parmesan, parsley, and pasta water.','admin'),
('Herbed Pizza','Homemade pizza with tomato sauce, mozzarella, and herbs.',50,'Medium','Italian','Dishes','../Assets/Herbed Garlic Butter Pizza Crust.jpg','https://www.youtube.com/watch?v=sv3TXMSv6Lw','Prepare dough. Spread sauce and toppings. Bake until crust is golden.','admin'),
('Kimchi Fried Rice','Spicy Korean rice with kimchi, egg, and scallions.',20,'Easy','Korean','Dishes','../Assets/fried rice.jpg','https://www.youtube.com/watch?v=eIo2BaE6LxI','Stir-fry kimchi. Add rice and gochujang. Top with fried egg and scallions.','admin'),
('Chicken Teriyaki Rice','Japanese-style glazed chicken served over rice.',30,'Easy','Japanese','Dishes','../Assets/chicken teriyaki rice.jpg','https://www.youtube.com/watch?v=XKN9y5i7ZP8','Sear chicken. Simmer with soy, mirin, sugar, and ginger. Serve over rice.','admin'),
('Miso Soup','Light Japanese soup with miso, tofu, and seaweed.',15,'Easy','Japanese','Appetizers','../Assets/miso soup.jpg','https://www.youtube.com/watch?v=2Nm48b3j9tw','Warm dashi. Dissolve miso. Add tofu and seaweed without boiling hard.','admin'),
('Chow Mein','Chinese stir-fried noodles with vegetables and savory sauce.',25,'Medium','Chinese','Dishes','../Assets/chow mein.jpg','https://www.youtube.com/watch?v=ikv3-VP6K44','Boil noodles. Stir-fry vegetables. Toss noodles with sauce until coated.','admin'),
('Egg Drop Soup','Silky Chinese soup with egg ribbons and scallions.',15,'Easy','Chinese','Appetizers','../Assets/egg drop soup.jpg','https://www.youtube.com/watch?v=Yk13C35Cxg4','Simmer broth. Stir in cornstarch slurry. Drizzle beaten egg while stirring.','admin'),
('Pad Thai','Thai rice noodles with tamarind sauce, egg, tofu, and peanuts.',35,'Medium','Thai','Dishes','../Assets/pad thai.jpg','https://www.youtube.com/watch?v=b7YnoRFuZ9o','Soak noodles. Stir-fry protein, egg, noodles, and sauce. Finish with peanuts and lime.','admin'),
('Green Curry','Thai coconut curry with vegetables and fragrant herbs.',40,'Medium','Thai','Dishes','../Assets/green curry.jpg','https://www.youtube.com/watch?v=LIbKVpBQKJI','Fry curry paste. Add coconut milk, protein, and vegetables. Simmer until tender.','admin'),
('Tom Yum Soup','Hot and sour Thai soup with lemongrass and shrimp.',30,'Medium','Thai','Appetizers','../Assets/tom yum soup.jpg','https://www.youtube.com/watch?v=hXaaZiMgvgI','Simmer aromatics. Add shrimp and mushrooms. Season with lime, fish sauce, and chili.','admin'),
('Beef Tacos','Mexican tacos with seasoned beef, salsa, and fresh toppings.',25,'Easy','Mexican','Dishes','../Assets/burger.png','https://www.youtube.com/watch?v=PGklx6OD_MM','Cook beef with spices. Warm tortillas. Fill with beef, salsa, lettuce, and cheese.','admin'),
('Chicken Curry','Indian-style chicken curry with warm spices and tomato gravy.',45,'Medium','Indian','Dishes','../Assets/caldereta.jpg','https://www.youtube.com/watch?v=WRYOVVexMhU','Saute aromatics and spices. Add chicken and tomatoes. Simmer until tender.','admin'),
('Classic Burger','American beef burger with lettuce, tomato, and sauce.',30,'Easy','American','Dishes','../Assets/burger.png','https://www.youtube.com/watch?v=foD42-73wdI','Season beef. Grill patties. Assemble with buns, vegetables, and sauce.','admin'),
('French Omelette','Soft French omelette with butter and herbs.',12,'Medium','French','Dishes','../Assets/egg drop soup.jpg','https://www.youtube.com/watch?v=s10etP1p2bU','Whisk eggs. Cook gently in butter while stirring. Fold while soft.','admin'),
('Spanish Tortilla','Potato and egg omelette cooked until tender and golden.',40,'Medium','Spanish','Dishes','../Assets/picadillo.jpg','https://www.youtube.com/watch?v=JceGMNG7rpU','Cook potatoes and onion. Mix with eggs. Cook slowly and flip carefully.','admin'),
('Vietnamese Spring Rolls','Fresh rolls with herbs, noodles, vegetables, and dipping sauce.',25,'Easy','Vietnamese','Appetizers','../Assets/spring rolls.jpg','https://www.youtube.com/watch?v=HvYhQwIBc7Y','Soften wrappers. Fill with noodles, herbs, vegetables, and protein. Roll tightly.','admin'),
('Laksa','Malaysian spicy coconut noodle soup.',50,'Hard','Malaysian','Dishes','../Assets/laksa.jpg','https://www.youtube.com/watch?v=0p2BKfgVCpE','Cook spice paste. Add coconut broth and noodles. Top with herbs and protein.','admin');

INSERT INTO recipe_ingredients (recipe_id, ingredient_name) VALUES
(1,'chicken'),(1,'soy sauce'),(1,'vinegar'),(1,'garlic'),(1,'bay leaf'),(1,'rice'),
(2,'pork'),(2,'tamarind'),(2,'tomato'),(2,'radish'),(2,'kangkong'),
(3,'rice noodles'),(3,'chicken'),(3,'carrot'),(3,'cabbage'),(3,'soy sauce'),
(4,'pasta'),(4,'garlic'),(4,'butter'),(4,'parmesan'),(4,'parsley'),
(5,'flour'),(5,'tomato sauce'),(5,'mozzarella'),(5,'basil'),
(6,'rice'),(6,'kimchi'),(6,'egg'),(6,'gochujang'),(6,'scallion'),
(7,'chicken'),(7,'soy sauce'),(7,'ginger'),(7,'rice'),(7,'sugar'),
(8,'miso'),(8,'tofu'),(8,'seaweed'),(8,'dashi'),
(9,'noodles'),(9,'cabbage'),(9,'carrot'),(9,'soy sauce'),
(10,'egg'),(10,'chicken broth'),(10,'scallion'),
(11,'rice noodles'),(11,'egg'),(11,'tamarind'),(11,'peanut'),(11,'tofu'),
(12,'coconut milk'),(12,'green curry paste'),(12,'chicken'),(12,'eggplant'),
(13,'shrimp'),(13,'lemongrass'),(13,'lime'),(13,'mushroom'),
(14,'beef'),(14,'tortilla'),(14,'lettuce'),(14,'tomato'),(14,'cheese'),
(15,'chicken'),(15,'curry powder'),(15,'tomato'),(15,'onion'),(15,'garlic'),
(16,'beef'),(16,'bun'),(16,'lettuce'),(16,'tomato'),(16,'cheese'),
(17,'egg'),(17,'butter'),(17,'herbs'),
(18,'egg'),(18,'potato'),(18,'onion'),(18,'olive oil'),
(19,'rice paper'),(19,'noodles'),(19,'lettuce'),(19,'herbs'),(19,'shrimp'),
(20,'noodles'),(20,'coconut milk'),(20,'shrimp'),(20,'laksa paste');
