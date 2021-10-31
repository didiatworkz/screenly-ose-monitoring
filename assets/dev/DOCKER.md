# Build production container
docker build -t somo .

# Build dev container
sudo docker build -t somo-dev -f Dockerfile.dev .

sudo docker run -d --name somo -v /local/folder/to/data:/var/www/html -p 80:80 -e "UID=$(id -u)" -e "GID=$(id -g)" -e "H_IP=192.168.178.2" -e "H_PORT=80" somo-dev