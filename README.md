# data-quality-api
A prototype API for obtaining data quality measures

## Docker

### Build
docker build -t data-quality-api .

### Run
docker run --name data-quality-api -d -p 80:80 --rm data-quality-api

### Log
docker logs data-quality-api -f
