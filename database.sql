CREATE TABLE IF NOT EXISTS urls
(
    id          INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name        VARCHAR(255) NOT NULL UNIQUE,
    created_at  TIMESTAMP NOT NULL
);

CREATE TABLE IF NOT EXISTS url_checks
(
    id          INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    url_id      BIGINT REFERENCES urls(id) NOT NULL,
    status_code INT,
    h1          VARCHAR(225),
    title       VARCHAR(225),
    description VARCHAR(225),
    created_at  TIMESTAMP NOT NULL
)