Scalp
=====

Command line tool to Analyze and store your Media file's metadata 

Install
=======

It is very easy to install Scalp as a CLI utility:
<br/>
via wget
```
wget https://downloads.buonzz.com/scalp.phar
sudo mv scalp.phar  /usr/local/bin/scalp
chmod +x /usr/local/bin/scalp
```
via curl

```
curl -o scalp.phar 'https://downloads.buonzz.com/scalp.phar'
sudo mv scalp.phar  /usr/local/bin/scalp
chmod +x /usr/local/bin/scalp
```

After this, scalp command is available anywhere in your computer. To check if the scalp is installed properly, just execute

```
scalp -V
```

This should output

```
Scalp Metadata Extraction Tool by Darwin Biler version v2
```


#### via Composer - Globally

You can install scalp globally in your machine:

```
composer global require 'buonzz/scalp=dev-master'
```

Simply add this directory to your PATH in your ~/.bash_profile (or ~/.bashrc) like this:

```
export PATH=~/.composer/vendor/bin:$PATH
```

#### via Composer - per-project

just require the buonzz/scalp in your composer project

```
{
"require":{
    "buonzz/scalp": "1.*"
  }
}
```

Usage
=====

Scalp can extract the metadata information from your media files and export it to following:

* Static JSON files
* ElasticSearch Server


Running in the background

```
nohup scalp maver:generate > scalp.log &
```


First, you need to create a configuration file called .env This will be used by scalp to retrieve certain information:

| Setting       | Description                                                                                              | Default        |
|---------------|----------------------------------------------------------------------------------------------------------|----------------|
| DB_HOSTNAME   | database hostname for the ElasticSearch Server                                                           | localhost      |
| DB_NAME       | index name of the documents in ElasticSearch                                                             | metadata       |
| DOC_TYPE      | the document type in ES (table name for RDBMS)                                                           | files          |
| DB_PORT       | port number for ES                                                                                       | 9200           |
| DB_USER       | username for the database                                                                                | null           |
| DB_PASSWORD   | database password                                                                                        | null           |
| INPUT_FOLDER  | the folder on which to read the files to analyze                                                         | current folder |
| OUTPUT_FOLDER | where to dump the JSON files (when using the file:extract command)                                       | dist           |
| LOG_FOLDER    | where to write the log file, this is the log file generated by ElasticSearch client. Useful in debugging | logs           |
| THUMB_PERCENT_RESIZE    | Used when creating thumbnails, this should be between 10-100, the images will be resized with this percent value | 10           |


#### File Command

To generate static JSON files
```
scalp file:extract
```

Sample extracted metadata

```
{
   "last_modified":"2017-03-25T12:10:39+00:00",
   "last_accessed":"2017-04-09T12:07:11+00:00",
   "file_permissions":"0644",
   "date_indexed":"2017-04-10T03:04:23+00:00",
   "human_filesize":"974.83 kB",
   "filepath":"IMG_1123.JPG",
   "path_tags":[

   ],
   "filesize":"998225",
   "fileformat":"jpg",
   "filename":"IMG_1123.JPG",
   "mime_type":"image\/jpeg",
   "exif":{
      "DateTimeDigitized":"2016-05-17T16:19:26+00:00",
      "ExposureTime":0.016666666666667,
      "FNumber":4,
      "ISOSpeedRatings":200,
      "ShutterSpeedValue":6,
      "ApertureValue":4,
      "FocalLength":20
   },
   "date_tags":[
      "Tue",
      "17th",
      "Tuesday",
      "May",
      "May",
      "2016",
      "4pm",
      "UTC",
      "17"
   ],
   "file_contents_hash":"4c7e796bc250b14fe7964694c4db5852eca34ddee24991371f848c3e8097436d",
   "width":"1920",
   "height":"1280"
}
```

#### ES Command

Load it to ElasticSearch
```
scalp es:index
```

or just simply
```
scalp
```

#### Thumb Command

create thumbnails
```
scalp thumb:create
```

Save thumbnails to ElasticSearch
```
scalp thumb:save
```

##### Viewing the thumbnails

You can get a preview of those thumbnails by using the PHP's built in web server

```
php -S localhost:8080 /usr/local/bin/scalp
``` 
then in your localhost, just append the url given by search command.
