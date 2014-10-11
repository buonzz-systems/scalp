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


    bin/scalp extract:video --file-path=/your/movies/filename_dvd.avi  --output-file=data/output.json

The above command specifies the "file-path" as the file that contains the image or video to analyze. The "output-file" will contain the output as a JSON file.


#### Extract the ID3 Tags and Metadata of a file and dump it into a MongoDB script file


    bin/scalp export:mongodb --folder-path=/my/videos --output-file=data/mongodata.js

You can then use the resulting file to import the data ito your MongoDB database.


