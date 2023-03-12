FROM node:14-alpine

COPY ./package*.json ./
WORKDIR /var/www/${PROJECT_NAME}

RUN npm install

COPY . .

CMD ["/usr/local/bin/npm", "run", "dev-server"]

EXPOSE 8080