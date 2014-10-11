Scalp
=====

Command line tool to Analyze and store your Media file's metadata 


Usage
=====

To use scalp, just require the buonzz/scalp in your composer project


    {
    "require":{
        "buonzz/scalp": "1.*"
      }
    }


#### Dump the folder and files structure as a JSON file


    bin/scalp dump:folder --folder-path=/var/www/html/pictures/  --output-file=/var/www/html/pictures/dump.json

The above command specifies the "folder-path" as the folder that contains the images and videos to read. The "output-file" will contain the output as a JSON file.  The dump:folder command retrieves the directory and file listing of the folder-path and output it as a tree of JSON objects.

#### Extract the ID3 Tags and Metadata of a file and dump it into a JSON file


    bin/scalp export:mongodb --folder-path=/my/videos --output-file=data/myfiles.js --database=mymedia --collection=myfiles

The above command specifies the "file-path" as the file that contains the image or video to analyze. The "output-file" will contain the output as a JSON file.
The "database" is the name of the MongoDB database you want to import the data into. While the "collection" is the table inside the database that wherein the things will gonna be inserted.

##### Exporting the output data into MongoDB

You can then use the resulting file output to load the data into your MongoDB server. For example, to load it in the local MongoDB server, just use:

    mongo localhost:27017/mymedia data/myfiles.js



