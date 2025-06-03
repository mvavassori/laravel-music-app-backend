#!/bin/bash

podman pod create --name laravel-music-app-backend-pod \
    --restart unless-stopped \
    --replace \
    -p 3306:3306 \
    -p 8080:8000

podman create --pod laravel-music-app-backend-pod \
    --name mysql-laravel-music-app-db \
    -e MYSQL_ROOT_PASSWORD=rootpassword \
    -e MYSQL_DATABASE=musicappdb \
    -e MYSQL_USER=appuser \
    -e MYSQL_PASSWORD=apppassword \
    --replace \
    docker.io/mysql:lts
    
podman create --pod laravel-music-app-backend-pod \
  --name laravel-music-app-backend \
  -v ./laravel-music-app-backend:/app \
  --workdir /app \
  --replace \
  -e WEB_DOCUMENT_ROOT=/app/public \
  docker.io/webdevops/php-apache:8.1-alpine

podman pod start laravel-music-app-backend-pod