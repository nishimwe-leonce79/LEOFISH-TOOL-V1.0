FROM php:8.2-cli
WORKDIR /app
COPY . .
RUN chmod +x leofish.sh && touch creds.txt && chmod 666 creds.txt
CMD ["php", "-S", "0.0.0.0:10000"
