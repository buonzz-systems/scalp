Scalp
=====

Command line tool to Analyze and store your Media file's metadata 


Usage
=====

To use scalp, just require the buonzz/scalp in your composer project, then:


    bin/scalp dump:folder --folder-path=/var/www/html/pictures/  --output-file=/var/www/html/pictures/dump.json

The above command specifies the "folder-path" as the folder that contains the images and videos to analyze. The "output-file" will contain the output as a JSON file.  The dump:folder command retrieves the directory and file listing of the folder-path and output it as a tree of JSON objects.

