FROM ubuntu:18.04

RUN apt-get update && apt-get install -y apache2 php composer

# COPY . /var/www/html/
