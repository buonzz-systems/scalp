## Re-compile the phar file

Put up the server dependencies
```
docker-compose up -d
```

ssh to the container
```
docker-compose exec cli bash
```

execute the build command

```
cd /code
./build.sh
```

The compiled phar file should now be available in  /code/dist/scalp.phar