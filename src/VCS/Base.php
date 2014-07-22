<?php
namespace VCS;

use DateTime;

class Base
{
    /**
     * string Path to the local repository
     */
    public $repository_path;

    /**
     * string Type of repository (svn, git, hg)
     */
    public $repository_type;

    /**
     * Constructor
     * @param string $repository_path
     * @return void
     */
    public function __construct($repository_path)
    {
        $this->repository_path = realpath($repository_path);
    }

    /**
     * Parse the log provided as a string
     * @param  string $log VCS log
     * @return array structured data extracted from the log
     */
    public function parseLog($log)
    {
        for ($i = 0, $lines = count($log); $i < $lines; $i++) {
            $tmp = explode(': ', $log[$i]);
            $tmp = array_map('trim', $tmp);

            if ($tmp[0] == 'changeset') {
                $commit = $tmp[1];
            }

            if ($tmp[0] == 'user') {
                $email  = $this->extractEmail($tmp[1]);
                $author = $this->extractAuthor($tmp[1]);
            }

            if ($tmp[0] == 'date') {
                $date = trim($tmp[1]);
            }

            if ($tmp[0] == 'summary') {
                $commits[] = [
                    'commit'  => trim($commit),
                    'author'  => trim($author),
                    'email'   => trim($email),
                    'date'    => DateTime::createFromFormat('D M j H:i:s Y O', $date),
                    'summary' => trim($tmp[1]),
                    'vcs'     => trim($this->repository_type),
                ];
            }
        }

        return $commits;
    }

    /**
     * Extract the first email address found in the string
     *
     * @param  string $string
     * @return string Email address extracted or 'Unknown' if none found
     */
    public function extractEmail($string)
    {
        preg_match_all('/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i', $string, $matches);
        // We only care about the first email found

        return empty($matches[0][0]) ? 'Unknown' : $matches[0][0];
    }

    /**
     * Extract the Author name from the string, remove emails if they exist
     *
     * @param  string $string String to analyze
     * @return string Author name
     */
    public function extractAuthor($string)
    {
        preg_match_all('/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i', $string, $matches);
        $string = str_replace($matches[0], '', $string);
        $string = str_replace(['<', '>', '()'], '', $string);
        $string = trim($string);

        return empty($string) ? 'Unknown' : $string;
    }

    protected function execute($command)
    {
        $cwd = getcwd();
        chdir($this->repository_path);
        exec($command, $output, $return_code);
        chdir($cwd);

        if ($return_code !== 0) {
            error_log("Error with command {$command} launched in {$this->repository_path}");
        }

        return $output;
    }
}
