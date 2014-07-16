<?php
namespace VCS;

use DateTime;

class Subversion extends Base
{
    public function getCommits()
    {
        $log = $this->execute('svn log');
        $this->repository_type = 'svn';

        return $this->parseLog($log);
    }

    public function parseLog($log)
    {
        $commits = [];

        // Remove empty lines
        $log = array_values(array_filter($log));

        for ($i = 0, $lines = count($log); $i < $lines; $i++) {

            // Skip separator
            if (! strncmp($log[$i], '----', strlen('----'))) {
                continue;
            }
            // Line starts with r and has pipes (|), it contains data
            if (! strncmp($log[$i], 'r', strlen('r')) && strstr($log[$i], '|')) {
                $tmp  = explode('|', $log[$i]);
                $tmp  = array_map('trim', $tmp);
                $date = trim(preg_replace('~\(([:alpha]*.+)\)~i', '', $tmp[2]));
                $commits[] = [
                    'commit'  => ltrim($tmp[0],'r'),
                    'author'  => $tmp[1],
                    'email'   => $tmp[1],
                    'date'    => DateTime::createFromFormat('Y-m-d H:i:s O', $date),
                    'summary' => $log[$i+1],
                    'vcs'     => $this->repository_type,
                ];
            }
        }

        return $commits;
    }

    public function update()
    {
        $this->execute('svn update');
    }
}
