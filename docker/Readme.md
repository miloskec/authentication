## Microservice base 

### If base image was not build it should be build first 

docker build -f ./docker/Dockerfile --build-arg APP_TIMEZONE=CET --build-arg WWWGROUP=1000 --no-cache  --progress=plain -t base .  

## Authentication 

docker build -f ./docker/Dockerfile.authentication --build-arg APP_TIMEZONE=CET --build-arg WWWGROUP=1000 --no-cache  --progress=plain -t authentication . 
docker-compose -f docker-compose.prod.yml up --build 

## Authentication development

docker build -f ./docker/Dockerfile.authentication --build-arg APP_TIMEZONE=CET --build-arg WWWGROUP=1000 --no-cache -t authentication:dev . 
docker-compose -f docker-compose.dev.yml up --build 
