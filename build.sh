cd /home/bugar-sehat
sudo git pull origin
sudo docker compose -f docker-compose.yml up --build -d
sudo docker rmi $(docker images --filter "dangling=true" -q --no-trunc)
