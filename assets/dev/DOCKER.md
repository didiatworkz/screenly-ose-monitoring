# Build production container
docker build -t somo .

# Build dev container
sudo docker build -t somo-dev -f Dockerfile.dev .