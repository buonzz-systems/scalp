const {Command, flags} = require('@oclif/command')

class ExtractCommand extends Command {
  async run() {
    const {flags} = this.parse(ExtractCommand)
    const input_folder = flags.input_folder || './'
    const output_folder = flags.output_folder || './output'
  }
}

ExtractCommand.description = `Extract metadata information from media files`

ExtractCommand.flags = {
  input_folder: flags.string(),
  output_folder: flags.string()
}

module.exports = ExtractCommand
