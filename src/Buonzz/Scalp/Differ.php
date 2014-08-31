<?php namespace Buonzz\Scalp;

class Differ{
	
    public $revision;
   
    public $filesToIgnore = array(
        '.gitignore'        
    );

    public $repo;

    public function setRepo($repo){
    	if (!file_exists( $repo."/.git"))
         	throw new \Exception("'{$repo}' is not Git repository.");
     	else
     		$this->repo = $repo;
    }

    protected function runCommand($command)
    {        
        $command = escapeshellcmd($command);        

        exec($command, $output, $returnStatus);

        if ($returnStatus != 0)
            throw new Exception("Scalp attempted to run the following console command but it failed:\r\n$command");       

        return $output;
    }

    protected function gitCommand($command, $repoPath = null)
    {
        if (! $repoPath)
            $repoPath = $this->repo;
        $command = 'git --git-dir="' . $repoPath . '/.git" --work-tree="' . $repoPath . '" ' . $command;
        return $this->runCommand($command);
    }

    protected function compare($localRevision, $remoteRevision = null)
    {
        $tmpFile = tmpfile();
        $filesToUpload = array();
        $filesToDelete = array();
        $output = array();       

        // Use git to list the changed files between $remoteRevision and $localRevision
        if (empty($remoteRevision)) {
            $command = 'ls-files';
        } else if ($localRevision == 'HEAD') {
            $command = 'diff --name-status '.$remoteRevision.'...'.$localRevision;
        } else {
            // Question: should there really be a space after ... here?  Is that valid?
            $command = 'diff --name-status '.$remoteRevision.'... '.$localRevision;
        }

        try {
            $output = $this->gitCommand($command);            
        } catch (Exception $e) {        
            throw new Exception("The git diff command failed. This is probably because the git revision found on the server is not present in your repository.\r\nTry doing a 'git pull' before deploying.\r\n<reset>If performing a 'pull' doesn't resolve the problem, then it may be that another developer has deployed a commit without pushing to your repository. You need access to this commit to be able to correctly deploy changed files.\r\n\r\nA workaround is to reset the revision hash stored on the server using:\r\nphploy --sync=\"your-revision-hash-here\" HOWEVER this will leave your server files in an inconsistent state unless you manually update the appropriate files.");
        }

		if (! empty($remoteRevision)) {
	        foreach ($output as $line) {

	            // Added (A), Modified (C), Unmerged (M)
                if ($line[0] == 'A' or $line[0] == 'C' or $line[0] == 'M') {
	                $filesToUpload[] = trim(substr($line, 1));
                    // TODO: we could possibly calculate & sum the file sizes here to display the proper upload progress later on

                // Deleted (D)
	            } elseif ($line[0] == 'D') {
	                $filesToDelete[] = trim(substr($line, 1));

	            } else {
	                throw new Exception("Unknown git-diff status: {$line[0]}");
	            }
	        }
        } else {
		    $filesToUpload = $output;
		}

        // Skip any files in the $this->filesToIgnore array
        // (the array_values() call then ensures we have a numeric 0-based array with no gaps, 
        //  so that file numbers display correctly)
        $filesToUpload = array_values(array_diff($filesToUpload, $this->filesToIgnore));

        return array(
            'upload' => $filesToUpload,
            'delete' => $filesToDelete
        );
    }
}