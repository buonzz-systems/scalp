const {Command, flags} = require('@oclif/command')
const walk = require("walk")
const ignored_ones =  require('../IgnoredFiles')
const path = require('path')
const md5File = require('md5-file/promise')

class CrawlCommand extends Command {

  async run() {
    
    const {flags} = this.parse(CrawlCommand)
    const input_folder = flags.input_folder || './'
    const output_folder = flags.output_folder || './output'


    const walk_options = {
	    followLinks: false, 
	    filters: ignored_ones
	};

	const walker = walk.walk(input_folder, walk_options);

	walker.on("file", (root, fileStats, next) => {

	    let absPath =  path.join(root, fileStats.name);
	    let ext = path.extname(fileStats.name).toLowerCase();

        md5File(absPath).then(hash => {
            this.log(`The MD5 sum of ${absPath} is: ${hash}`)
        });

	    next();
	});

	walker.on("errors", (root, nodeStatsArray, next) => {
	    this.log("error")
	  next();
	});

	walker.on("end", () => {
		this.log("Done");
	});

  } // run
}

CrawlCommand.description = `Get Listing of files recursively from a given folder`

CrawlCommand.flags = {
  input_folder: flags.string(),
  output_folder: flags.string()
}

module.exports = CrawlCommand