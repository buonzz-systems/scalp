Scalp
=====

Command line tool to Analyze and store your Media file's metadata 

Install
=======


#### Global

You can install scalp globally in your machine:

```
composer global require 'buonzz/scalp=dev-master'
```

Simply add this directory to your PATH in your ~/.bash_profile (or ~/.bashrc) like this:

```
export PATH=~/.composer/vendor/bin:$PATH
```

#### Per-project

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


A file called **.env.example** contains a sample configuration. You can just copy it and rename it to .env and tweak its contents.


#### File Command

To generate static JSON files
```
bin/scalp file:extract
```

#### ES Command

Load it to ElasticSearch
```
bin/scalp es:index
```


### Testing Indexed documents

To show all indexes
```
curl 'localhost:9200/_cat/indices?v'
```

To list all documents
```
curl -XGET 'localhost:9200/[your index name]/_search?pretty=true&q=*:*'
```
