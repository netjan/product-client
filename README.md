# Web application API client

## Getting started
If you want to try to use and tweak that example, you can follow these steps:

1. Run `git clone https://github.com/netjan/product-client` to clone the project
1. Generate the certificate `make certificate`
1. Create docker networks if not exist `docker network create backend` and `docker network create frontend`
1. Run `make install` to install the project
1. Run `make start` to up your containers
1. Visit https://localhost/ and play with your app!


## Web application API server
Web application API server is required to use this application. See details at page `https://github.com/netjan/product-server`.
