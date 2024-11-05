DROP TABLE IF EXISTS api_project;
 
CREATE DATABASE IF NOT EXISTS api_project;
 
CREATE TABLE IF NOT EXISTS api_project.users(
    cod_user int unsigned not null auto_increment primary key,
    name varchar(120) not null,
    email varchar(120) not null,
    password varchar(255) not null,
    status boolean default 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
);
 
CREATE TABLE IF NOT EXISTS api_project.address_user(
    cod_address_user int unsigned not null auto_increment primary key,
    cod_user int unsigned not null,
    name varchar(120) not null,
    address varchar(120) not null,
    number varchar(50),
    zip_code varchar(13),
    complement varchar(255),
    status boolean default 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_UserAddresUser FOREIGN KEY (cod_user) REFERENCES users (cod_user)
);
 
CREATE TABLE IF NOT EXISTS api_project.categories(
    cod_category int unsigned not null auto_increment primary key,
    name varchar(120) not null,
    status boolean default 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
);
 
CREATE TABLE IF NOT EXISTS api_project.products(
    cod_product int unsigned not null auto_increment primary key,
    cod_category int unsigned not null,
    name varchar(120) not null,
    description varchar(255) null,
    price decimal(10,2) not null,
    status boolean default 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_CatPro FOREIGN KEY (cod_category) REFERENCES categories (cod_category)
);