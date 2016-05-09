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



### Testing Indexed documents

To show all indexes
```
curl 'localhost:9200/_cat/indices?v'
```

To list all documents
```
curl -XGET 'localhost:9200/scalp_media_files/_search?pretty=true&q=*:*'
```
