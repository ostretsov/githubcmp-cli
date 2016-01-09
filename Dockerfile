FROM php:5.6-cli
COPY . /usr/src/githubcli-cmp
WORKDIR /usr/src/githubcli-cmp
CMD [ "php", "./github" ]