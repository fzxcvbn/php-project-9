CREATE TABLE IF NOT EXISTS urls
(
    id         BIGINT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(255) NOT NULL UNIQUE,
    created_at timestamp    NOT NULL
);