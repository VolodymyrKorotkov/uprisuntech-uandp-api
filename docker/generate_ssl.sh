# shellcheck disable=SC2046
# shellcheck disable=SC2164
cd $(dirname "$0")/../
# shellcheck disable=SC2196
# shellcheck disable=SC2046
export $(egrep -v '^#' .env | xargs)

snap install --classic certbot
ln -s /snap/bin/certbot /usr/bin/certbot

certbot certonly --standalone -d "$APP_HOSTNAME"

# chmod certs
chmod -R -o+r -g+r /etc/letsencrypt/archive

cp /etc/letsencrypt/live/"$APP_HOSTNAME"/fullchain.pem ./secret/ssl/nginx.crt
cp /etc/letsencrypt/live/"$APP_HOSTNAME"/privkey.pem ./secret/ssl/nginx.key