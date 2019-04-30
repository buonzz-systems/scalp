const {Command, flags} = require('@oclif/command')
const walk = require("walk")
const ignored_ones =  require('../IgnoredFiles')
const path = require('path')

class ExtractCommand extends Command {

  async run() {
    
    const {flags} = this.parse(ExtractCommand)
    const input_folder = flags.input_folder || './'
    const output_folder = flags.output_folder || './output'


    const walk_options = {
	    followLinks: false, 
	    filters: ignored_ones
	};

	const walker = walk.walk(input_folder, walk_options);

	walker.on("file", function (root, fileStats, next) {

	    let absPath =  path.join(root, fileStats.name);
	    let ext = path.extname(fileStats.name).toLowerCase();

	    console.log(absPath);

	    next();
	});

	walker.on("errors", function (root, nodeStatsArray, next) {
	    console.log("error")
	  next();
	});

	walker.on("end", function () {
		console.log("Done");
	});

  } // run
}

ExtractCommand.description = `Extract metadata information from media files`

ExtractCommand.flags = {
  input_folder: flags.string(),
  output_folder: flags.string()
}

module.exports = ExtractCommand
