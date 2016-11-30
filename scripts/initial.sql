

USE places;

DROP TABLE users;
DROP TABLE images;
DROP TABLE image_info;

CREATE TABLE users (id INT PRIMARY KEY AUTO_INCREMENT DEFAULT 1, username VARCHAR(30) NOT NULL);

CREATE TABLE images (id INT PRIMARY KEY AUTO_INCREMENT DEFAULT NULL
				  , user_id INT NOT NULL
				  , filepath VARCHAR(255) NOT NULL
				  , FOREIGN KEY (user_id) REFERENCES users(id));

CREATE TABLE image_info (id INT PRIMARY KEY AUTO_INCREMENT DEFAULT NULL    
							, user_id INT NOT NULL
							, filepath VARCHAR(255) NOT NULL    
							, latitude float(10,8)    
							, longitude float(10,8)        
							, address varchar(255)    
							, city varchar(255)    
							, state varchar(255)    
							, postal varchar(255)    
							, country varchar(255)    
							, blurb varchar(255)    
							, dt DATE NOT NULL    
							, FOREIGN KEY (user_id) REFERENCES users(id));

-- intial query after login,
SELECT info.*
FROM `users` user
INNER JOIN `images` img ON user.id = img.user_id
INNER JOIN `image_info` info ON img.id = info.image_id
WHERE user.username = 'aforloney'

/* After querying the images for a given user calculate the number of images per country, to generate JSON data */

SELECT img.filepath, info.blurb, info.dt
FROM `images` img
INNER JOIN `users` user ON img.user_id = user.id
INNER JOIN `image_info` info ON info.image_id = img.id
WHERE info.state = '' AND info.country = ''